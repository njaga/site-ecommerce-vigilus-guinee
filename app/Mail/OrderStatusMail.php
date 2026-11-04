<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $trackingUrl,
        public bool $forTeam = false,
    ) {}

    public function build(): self
    {
        $subject = $this->forTeam
            ? 'Nouvelle demande de commande — '.$this->order->reference
            : 'Suivi de votre commande Vigilus — '.$this->order->reference;

        return $this->subject($subject)->view('emails.order-status');
    }
}
