<?php

namespace App\Services;

use App\Mail\QuoteReceivedMail;
use App\Models\QuoteRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class QuoteNotifier
{
    public function sendReceived(QuoteRequest $quote): void
    {
        // Une panne SMTP ne doit pas effacer une demande commerciale déjà persistée.
        $recipient = trim((string) config('commerce.quote_alert_email'));
        $quote->loadMissing('product');

        if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($recipient)->send(new QuoteReceivedMail($quote, true));
            } catch (Throwable $exception) {
                Log::warning('Notification devis équipe non envoyée', [
                    'quote_id' => $quote->id, 'exception_type' => $exception::class,
                ]);
            }
        }

        if ($quote->email && filter_var($quote->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($quote->email)->send(new QuoteReceivedMail($quote));
            } catch (Throwable $exception) {
                Log::warning('Confirmation devis client non envoyée', [
                    'quote_id' => $quote->id, 'exception_type' => $exception::class,
                ]);
            }
        }
    }
}
