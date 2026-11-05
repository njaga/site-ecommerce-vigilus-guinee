<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController
{
    public function index(CartService $cart): View
    {
        return view('store.cart', ['lines' => $cart->lines(), 'subtotal' => $cart->subtotal()]);
    }

    public function add(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $product = Product::published()->findOrFail($data['product_id']);
        if (! $product->can_order) {
            return back()->withErrors(['cart' => 'Ce produit n’est pas disponible à la commande actuellement.']);
        }

        $quantities = $cart->quantities();
        $next = ($quantities[$product->id] ?? 0) + $data['quantity'];
        if ($next > $product->stock_qty || $next > 100) {
            return back()->withErrors(['cart' => 'Quantité supérieure au stock disponible ou à la limite autorisée.']);
        }

        $quantities[$product->id] = $next;
        session()->put('cart', $quantities);

        return redirect()->route('cart.index')->with('success', 'Produit ajouté au panier.');
    }

    public function update(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);
        $quantities = $cart->quantities();
        if (! array_key_exists($data['product_id'], $quantities)) {
            return redirect()->route('cart.index');
        }

        if ($data['quantity'] === 0) {
            unset($quantities[$data['product_id']]);
        } else {
            $product = Product::published()->find($data['product_id']);
            if (! $product || $product->price_gnf === null || $product->stock_qty < $data['quantity']) {
                return back()->withErrors(['cart' => 'Quantité ou disponibilité non valide.']);
            }
            $quantities[$data['product_id']] = $data['quantity'];
        }

        session()->put('cart', $quantities);

        return redirect()->route('cart.index')->with('success', 'Panier mis à jour.');
    }
}
