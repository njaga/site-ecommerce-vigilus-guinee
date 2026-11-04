<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController
{
    public function index(Request $request): View
    {
        $status = $request->query('statut');
        $allowed = [Order::PENDING, Order::RESERVED, Order::CONFIRMED, Order::CANCELLED];
        $orders = Order::query()->withCount('items')->latest();

        if (in_array($status, $allowed, true)) {
            $orders->where('status', $status);
        }

        return view('admin.orders.index', [
            'orders' => $orders->paginate(30)->withQueryString(),
            'selectedStatus' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(['items', 'events.actor']),
        ]);
    }

    public function reserve(Order $order, OrderNotifier $notifier): RedirectResponse
    {
        $updated = DB::transaction(function () use ($order): Order {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== Order::PENDING) {
                throw ValidationException::withMessages(['order' => 'Seules les demandes en attente peuvent être réservées.']);
            }

            foreach ($locked->items()->orderBy('product_id')->get() as $item) {
                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                if (! $product || ! $product->is_active || $product->stock_qty < $item->quantity) {
                    throw ValidationException::withMessages([
                        'order' => 'Stock insuffisant pour la référence '.$item->sku_snapshot,
                    ]);
                }
                $product->decrement('stock_qty', $item->quantity);
            }

            $locked->update(['status' => Order::RESERVED, 'reserved_at' => now()]);
            $locked->events()->create([
                'user_id' => auth()->id(),
                'from_status' => Order::PENDING,
                'to_status' => Order::RESERVED,
            ]);

            return $locked;
        });

        $notifier->sendStatus($updated->load('items'));
        return back()->with('success', 'Stock réservé. Les frais restent à confirmer. Aucun paiement en ligne.');
    }

    public function confirm(Request $request, Order $order, OrderNotifier $notifier): RedirectResponse
    {
        $data = $request->validate([
            'shipping_gnf' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'internal_note' => ['nullable', 'string', 'max:1500'],
        ]);

        $updated = DB::transaction(function () use ($order, $data): Order {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== Order::RESERVED) {
                throw ValidationException::withMessages(['order' => 'Réservez le stock avant de confirmer la demande.']);
            }

            $shipping = (int) $data['shipping_gnf'];
            $locked->update([
                'status' => Order::CONFIRMED,
                'shipping_gnf' => $shipping,
                'total_gnf' => $locked->subtotal_gnf + $shipping,
                'confirmed_at' => now(),
            ]);
            $locked->events()->create([
                'user_id' => auth()->id(),
                'from_status' => Order::RESERVED,
                'to_status' => Order::CONFIRMED,
                'note' => $data['internal_note'] ?? null,
            ]);

            return $locked;
        });

        $notifier->sendStatus($updated->load('items'));
        return back()->with('success', 'Commande confirmée commercialement. Le règlement reste à organiser hors du site.');
    }

    public function cancel(Order $order, OrderNotifier $notifier): RedirectResponse
    {
        $updated = DB::transaction(function () use ($order): Order {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (! in_array($locked->status, [Order::PENDING, Order::RESERVED], true)) {
                throw ValidationException::withMessages([
                    'order' => 'Cette demande ne peut pas être annulée automatiquement. Contactez le responsable commercial.',
                ]);
            }

            $previous = $locked->status;
            if ($previous === Order::RESERVED) {
                foreach ($locked->items()->orderBy('product_id')->get() as $item) {
                    Product::whereKey($item->product_id)->lockForUpdate()->first()?->increment('stock_qty', $item->quantity);
                }
            }

            $locked->update(['status' => Order::CANCELLED, 'cancelled_at' => now()]);
            $locked->events()->create([
                'user_id' => auth()->id(),
                'from_status' => $previous,
                'to_status' => Order::CANCELLED,
            ]);

            return $locked;
        });

        $notifier->sendStatus($updated->load('items'));
        return back()->with('success', 'Demande annulée. Le stock a été restitué si une réservation existait.');
    }
}
