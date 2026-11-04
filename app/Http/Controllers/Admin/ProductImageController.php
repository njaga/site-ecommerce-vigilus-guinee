<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductImageController
{
    public function update(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        $this->assertOwned($product, $image);
        $data = $request->validate([
            'alt' => ['required', 'string', 'min:10', 'max:180'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);
        $image->update($data);

        return back()->with('success', 'Photo mise à jour : description alternative et ordre.');
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        $this->assertOwned($product, $image);
        if ($product->is_active && $product->images()->count() <= 1) {
            throw ValidationException::withMessages([
                'image' => 'Masquez le produit avant de supprimer sa dernière photo.',
            ]);
        }

        // Access restriction prevents deletion of another product image.
        $path = $image->path;
        $image->delete();
        Storage::disk('public')->delete($path);

        return back()->with('success', 'Photo supprimée du catalogue.');
    }

    private function assertOwned(Product $product, ProductImage $image): void
    {
        abort_unless($image->product_id === $product->id, 404);
    }
}
