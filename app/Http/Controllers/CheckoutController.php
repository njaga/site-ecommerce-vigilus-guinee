<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
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

        return view('store.checkout', ['lines' => $lines, 'subtotal' => $cart->subtotal()]);
    }

    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:150'],
            'customer_phone' => ['required', 'string', 'min:7', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ]);

        $quantities = $cart->quantities();
        if ($quantities === []) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Le panier est vide.']);
        }

        $order = DB::transaction(function () use ($quantities, $data): Order {
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
                    throw ValidationException::withMessages(['cart' => 'Prix ou stock modifié : veuillez vérifier votre panier.']);
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

            // Demande uniquement, sans paiement ni déduction immédiate de stock.
            $order = Order::create([
                'reference' => 'VG-'.Str::upper(Str::random(12)),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'city' => $data['city'],
                'district' => $data['district'] ?? null,
                'address' => $data['address'],
                'notes' => $data['notes'] ?? null,
                'subtotal_gnf' => $subtotal,
                'shipping_gnf' => null,
                'total_gnf' => null, // Frais de livraison à confirmer manuellement.
                'currency' => 'GNF',
                'status' => 'pending_review',
                'payment_status' => 'unpaid',
            ]);
            $order->items()->createMany($lines);

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('checkout.success')->with('order_reference', $order->reference);
    }

    public function success(Request $request): View|RedirectResponse
    {
        $reference = $request->session()->get('order_reference');
        if (! $reference) {
            return redirect()->route('home');
        }

        return view('store.order-success', compact('reference'));
    }
}
