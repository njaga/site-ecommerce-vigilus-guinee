<?php

namespace App\Services;

use App\Mail\OrderStatusMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Throwable;

class OrderNotifier
{
    public function trackingUrl(Order $order): string
    {
        return URL::temporarySignedRoute(
            'order.track',
            now()->addDays((int) config('commerce.tracking_link_days', 30)),
            ['order' => $order->id],
        );
    }

    public function sendStatus(Order $order, bool $newOrder = false): void
    {
        // Ne bloque jamais l'enregistrement d'une commande si la messagerie est indisponible.
        $url = $this->trackingUrl($order);

        if ($order->customer_email && filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($order->customer_email)->send(new OrderStatusMail($order, $url));
            } catch (Throwable $e) {
                Log::warning('Échec notification email client', ['order_id' => $order->id, 'error_type' => $e::class]);
            }
        }

        $team = trim((string) config('commerce.order_alert_email'));
        if ($newOrder && filter_var($team, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($team)->send(new OrderStatusMail($order, route('admin.orders.show', $order), true));
            } catch (Throwable $e) {
                Log::warning('Échec notification email équipe', ['order_id' => $order->id, 'error_type' => $e::class]);
            }
        }
    }
}
