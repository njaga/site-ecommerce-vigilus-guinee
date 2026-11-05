<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuoteRequest;
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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['nullable', 'email', 'max:255', 'required_without:phone'],
            'phone' => ['required', 'string', 'min:7', 'max:40'],
            'company' => ['nullable', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'message' => ['nullable', 'string', 'max:3000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'size:0'], // Champ anti-spam masqué.
        ]);

        unset($data['consent'], $data['website']);
        $data['consented_at'] = now();
        $data['status'] = 'new';
        QuoteRequest::create($data);

        return redirect()->route('quote.create')->with('success', 'Votre demande a été enregistrée. Notre équipe vous recontactera.');
    }
}
