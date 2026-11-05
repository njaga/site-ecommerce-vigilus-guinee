<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\OrderNotifier;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController
{
    public function create(CartService $cart): View|RedirectResponse
    {
        $lines = $cart->lines();
        if ($lines->isEmpty() || $lines->contains(fn ($line) => ! $line['valid'])) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Veuillez vérifier la disponibilité de tous les produits du panier.']);
        }

        if (! session()->has('checkout_token')) {
            session()->put('checkout_token', (string) Str::uuid());
        }

        return view('store.checkout', [
            'lines' => $lines,
            'subtotal' => $cart->subtotal(),
            'checkoutToken' => session('checkout_token'),
        ]);
    }

    public function store(Request $request, CartService $cart, OrderNotifier $notifier): RedirectResponse
    {
        $data = $request->validate([
            'checkout_token' => ['required', 'uuid'],
            'customer_name' => ['required', 'string', 'min:2', 'max:150'],
            'customer_phone' => ['required', 'string', 'min:7', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ]);

        $token = (string) $request->session()->get('checkout_token', '');
        if ($token === '' || ! hash_equals($token, $data['checkout_token'])) {
            throw ValidationException::withMessages([
                'checkout_token' => 'Ce formulaire a expiré. Revenez au panier pour réessayer.',
            ]);
        }

        // En cas de double clic, toujours renvoyer la même demande (clé unique BDD).
        $existing = Order::where('submission_key', $token)->first();
        if ($existing) {
            session()->forget(['cart', 'checkout_token']);
            return redirect()->route('checkout.success')->with('order_reference', $existing->reference);
        }

        $quantities = $cart->quantities();
        if ($quantities === []) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Le panier est vide.']);
        }

        try {
            $order = DB::transaction(function () use ($quantities, $data, $token): Order {
                $products = Product::published()->whereIn('id', array_keys($quantities))
                    ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

                if ($products->count() !== count($quantities)) {
                    throw ValidationException::withMessages(['cart' => 'Un produit a été retiré du catalogue.']);
                }

                $lines = [];
                $subtotal = 0;
                foreach ($quantities as $id => $qty) {
                    $product = $products->get((int) $id);
                    if (! $product || $product->price_gnf === null || $product->stock_qty < $qty || $qty > 100) {
                        throw ValidationException::withMessages([
                            'cart' => 'Prix ou stock modifié : veuillez vérifier votre panier.',
                        ]);
                    }

                    $lineTotal = $product->price_gnf * $qty;
                    $subtotal += $lineTotal;
                    $lines[] = [
                        'product_id' => $product->id,
                        'sku_snapshot' => $product->sku,
                        'name_snapshot' => $product->name,
                        'unit_price_gnf' => $product->price_gnf,
                        'quantity' => $qty,
                        'subtotal_gnf' => $lineTotal,
                    ];
                }

                // Pas de paiement : demande à valider manuellement par Vigilus.
                $order = Order::create([
                    'reference' => 'VG-'.Str::upper(Str::random(12)),
                    'submission_key' => $token,
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'customer_email' => $data['customer_email'] ?? null,
                    'city' => $data['city'],
                    'district' => $data['district'] ?? null,
                    'address' => $data['address'],
                    'notes' => $data['notes'] ?? null,
                    'consented_at' => now(),
                    'subtotal_gnf' => $subtotal,
                    'shipping_gnf' => null,
                    'total_gnf' => null,
                    'currency' => 'GNF',
                    'status' => Order::PENDING,
                    'payment_status' => 'unpaid',
                ]);
                $order->items()->createMany($lines);
                $order->events()->create(['to_status' => Order::PENDING]);

                return $order;
            });
        } catch (UniqueConstraintViolationException $e) {
            // Concurrence : une soumission parallèle a déjà créé la même demande.
            $order = Order::where('submission_key', $token)->first();
            if (! $order) {
                throw $e;
            }
        }

        session()->forget(['cart', 'checkout_token']);
        $notifier->sendStatus($order->load('items'), newOrder: true);

        return redirect()->route('checkout.success')->with('order_reference', $order->reference);
    }

    public function success(Request $request): View|RedirectResponse
    {
        $reference = $request->session()->get('order_reference');
        if (! $reference) {
            return redirect()->route('home');
        }

        $order = Order::where('reference', $reference)->firstOrFail();
        $trackingUrl = app(OrderNotifier::class)->trackingUrl($order);

        return view('store.order-success', compact('reference', 'trackingUrl'));
    }
}
