<?php

namespace App\Services;

use App\Models\CatalogImportRow;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CatalogDraftApprover
{
    /**
     * A reviewed BOQ row becomes an UNPUBLISHED draft with NO retail price,
     * NO inventory and NO vendor costs. Publication is a separate action.
     */
    public function approve(CatalogImportRow $row, Category $category): Product
    {
        return DB::transaction(function () use ($row, $category): Product {
            $locked = CatalogImportRow::whereKey($row->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== CatalogImportRow::PENDING || ! $locked->external_model) {
                throw ValidationException::withMessages([
                    'row' => 'Cette ligne est invalide, dupliquée ou a déjà été traitée.',
                ]);
            }

            if (Product::where('sku', $locked->external_model)->exists()) {
                throw ValidationException::withMessages([
                    'row' => 'La référence existe déjà. Aucun produit existant ne sera écrasé.',
                ]);
            }

            $name = $locked->description ?: $locked->external_model;
            $slug = Str::slug($locked->external_model);
            if ($slug === '' || Product::where('slug', $slug)->exists()) {
                $slug = Str::slug($locked->external_model).'-boq-'.$locked->id;
            }

            $product = Product::create([
                'category_id' => $category->id,
                'brand' => 'Dahua',
                'sku' => $locked->external_model,
                'slug' => $slug,
                'name' => $name,
                'short_description' => $name,
                'description' => $locked->features,
                'specifications' => [],
                'price_gnf' => null,
                'stock_qty' => 0,
                'is_active' => false,
                'is_featured' => false,
            ]);

            $locked->update([
                'status' => CatalogImportRow::APPROVED,
                'suggested_category_id' => $category->id,
                'product_id' => $product->id,
                'issue' => null,
            ]);

            return $product;
        });
    }
}
