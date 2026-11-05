<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderTrackingController
{
    public function show(Order $order): View
    {
        // La route est protégée par une signature temporaire (middleware signed).
        // Ne jamais afficher les coordonnées personnelles ou les notes internes.
        return view('store.order-tracking', ['order' => $order->load('items')]);
    }
}
