<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuoteRequest;
use App\Services\QuoteNotifier;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuoteController
{
    public function create(Request $request): View
    {
        $product = Product::published()->find($request->query('produit'));

        return view('store.quote', compact('product'));
    }

    public function store(Request $request, QuoteNotifier $notifier): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['nullable', 'email', 'max:255', 'required_without:phone'],
            'phone' => ['required', 'string', 'min:7', 'max:40'],
            'company' => ['nullable', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'message' => ['nullable', 'string', 'max:3000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'size:0'], // Champ anti-spam masqué.
        ]);

        if (isset($data['product_id']) && ! Product::published()->whereKey($data['product_id'])->exists()) {
            throw ValidationException::withMessages([
                'product_id' => 'Cette référence ne peut plus faire l’objet d’une demande.',
            ]);
        }

        unset($data['consent'], $data['website']);
        $data['consented_at'] = now();
        $data['status'] = 'new';
        $quote = QuoteRequest::create($data);
        $notifier->sendReceived($quote);

        return redirect()->route('quote.create')->with('success', 'Votre demande a été enregistrée. Notre équipe vous recontactera.');
    }
}
