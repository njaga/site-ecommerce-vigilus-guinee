<?php

namespace App\Http\Controllers\Admin;

use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuoteController
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('statut', '');
        $quotes = QuoteRequest::query()->with(['product', 'reviewer'])->latest();
        if (in_array($status, [QuoteRequest::NEW, QuoteRequest::IN_PROGRESS, QuoteRequest::CLOSED], true)) {
            $quotes->where('status', $status);
        }

        return view('admin.quotes', [
            'quotes' => $quotes->paginate(30)->withQueryString(),
            'selectedStatus' => $status,
        ]);
    }

    public function update(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([
                QuoteRequest::NEW, QuoteRequest::IN_PROGRESS, QuoteRequest::CLOSED,
            ])],
            'internal_note' => ['nullable', 'string', 'max:1500'],
        ]);

        DB::transaction(function () use ($quote, $data, $request): void {
            $locked = QuoteRequest::whereKey($quote->id)->lockForUpdate()->firstOrFail();
            $from = $locked->status;
            $to = $data['status'];

            if ($from !== $to && ! in_array($to, match ($from) {
                QuoteRequest::NEW => [QuoteRequest::IN_PROGRESS, QuoteRequest::CLOSED],
                QuoteRequest::IN_PROGRESS => [QuoteRequest::CLOSED],
                default => [],
            }, true)) {
                throw ValidationException::withMessages([
                    'status' => 'Transition non autorisée pour cette demande de devis.',
                ]);
            }

            $note = trim((string) ($data['internal_note'] ?? ''));
            if ($from === $to && $note === '') {
                throw ValidationException::withMessages([
                    'internal_note' => 'Indiquez une nouvelle note ou modifiez le statut.',
                ]);
            }

            $locked->update([
                'status' => $to,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
            $locked->events()->create([
                'user_id' => $request->user()->id,
                'from_status' => $from,
                'to_status' => $to,
                'note' => $note !== '' ? $note : null,
            ]);
        });

        return back()->with('success', 'Devis mis à jour et mouvement conservé dans l’historique.');
    }
}
