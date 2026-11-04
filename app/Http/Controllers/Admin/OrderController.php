<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController
{
    public function index(): View
    {
        return view('admin.orders.index', ['orders' => Order::withCount('items')->latest()->paginate(30)]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('items')]);
    }

    public function reserve(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'pending_review') {
                throw ValidationException::withMessages(['order' => 'Cette demande ne peut plus être réservée.']);
            }

            $items = $locked->items()->orderBy('product_id')->get();
            foreach ($items as $item) {
                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                if (! $product || ! $product->is_active || $product->stock_qty < $item->quantity) {
                    throw ValidationException::withMessages(['order' => 'Stock insuffisant pour la référence '.$item->sku_snapshot]);
                }

                $product->decrement('stock_qty', $item->quantity);
            }

            $locked->update(['status' => 'reserved', 'reserved_at' => now()]);
        });

        return back()->with('success', 'Stock réservé. Aucun paiement n’a été enregistré.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (! in_array($locked->status, ['pending_review', 'reserved'], true)) {
                throw ValidationException::withMessages(['order' => 'Demande déjà clôturée.']);
            }

            if ($locked->status === 'reserved') {
                foreach ($locked->items()->orderBy('product_id')->get() as $item) {
                    Product::whereKey($item->product_id)->lockForUpdate()->first()?->increment('stock_qty', $item->quantity);
                }
            }

            $locked->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        });

        return back()->with('success', 'Demande annulée ; le stock réservé a été restitué, le cas échéant.');
    }
}
