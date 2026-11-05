<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public QuoteRequest $quote,
        public bool $forTeam = false,
    ) {}

    public function build(): self
    {
        return $this
            ->subject($this->forTeam
                ? 'Nouvelle demande de devis Vigilus #'.$this->quote->id
                : 'Vigilus Guinée — votre demande de devis a été reçue')
            ->view('emails.quote-received');
    }
}
