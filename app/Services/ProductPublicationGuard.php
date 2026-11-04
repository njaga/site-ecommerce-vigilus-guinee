<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ProductPublicationGuard
{
    /**
     * Preview a draft's missing publication requirements without making it
     * public and without requiring an attempted publish action.
     */
    public function issuesFor(Product $product): array
    {
        $data = [
            'is_active' => true,
            'category_id' => $product->category_id,
            'short_description' => $product->short_description,
            'description' => $product->description,
            'specifications' => $product->specifications ?? [],
            'seo_title' => $product->seo_title,
            'seo_description' => $product->seo_description,
            'price_gnf' => $product->price_gnf,
            'price_on_request' => $product->price_on_request,
            'technical_verified' => $product->technical_verified_at !== null,
            'commercial_verified' => $product->commercial_verified_at !== null,
            'media_rights_verified' => $product->media_rights_verified_at !== null,
        ];

        try {
            $this->check($data, $product);
            return [];
        } catch (ValidationException $exception) {
            return collect($exception->errors())->flatten()->all();
        }
    }

    /**
     * Refuse a public listing without reviewed technical and commercial facts.
     * This does not itself authenticate legal rights; the admin must attest them.
     */
    public function check(array $data, ?Product $current = null, bool $hasNewImage = false): void
    {
        if (! ($data['is_active'] ?? false)) {
            return;
        }

        $errors = [];
        $category = Category::find($data['category_id'] ?? null);

        if (! $category || ! $category->is_active) {
            $errors['category_id'] = 'Choisissez une catégorie publique active.';
        }
        if (mb_strlen(trim((string) ($data['short_description'] ?? ''))) < 30) {
            $errors['short_description'] = 'Ajoutez une description courte en français (30 caractères minimum).';
        }
        if (mb_strlen(trim((string) ($data['description'] ?? ''))) < 100) {
            $errors['description'] = 'Renseignez une description commerciale vérifiée (100 caractères minimum).';
        }
        if (count($data['specifications'] ?? []) < 2) {
            $errors['specifications_text'] = 'Ajoutez au moins deux caractéristiques techniques validées.';
        }
        if (! $hasNewImage && (! $current || ! $current->images()->exists())) {
            $errors['image'] = 'Ajoutez une photo constructeur autorisée avant de publier.';
        }
        if (mb_strlen(trim((string) ($data['seo_title'] ?? ''))) < 20
            || mb_strlen(trim((string) ($data['seo_title'] ?? ''))) > 75) {
            $errors['seo_title'] = 'Le titre SEO doit contenir entre 20 et 75 caractères.';
        }
        if (mb_strlen(trim((string) ($data['seo_description'] ?? ''))) < 70
            || mb_strlen(trim((string) ($data['seo_description'] ?? ''))) > 170) {
            $errors['seo_description'] = 'La description SEO doit contenir entre 70 et 170 caractères.';
        }

        $price = $data['price_gnf'] ?? null;
        $quoteOnly = (bool) ($data['price_on_request'] ?? false);
        if ($quoteOnly && $price !== null) {
            $errors['price_gnf'] = 'Choisissez soit un prix public, soit « sur devis », pas les deux.';
        } elseif (! $quoteOnly && ($price === null || $price <= 0)) {
            $errors['price_gnf'] = 'Renseignez un prix de vente GNF validé ou cochez explicitement « sur devis ».';
        }

        foreach ([
            'technical_verified' => 'Confirmez la vérification des informations techniques.',
            'commercial_verified' => 'Confirmez la validation commerciale du prix et du stock.',
            'media_rights_verified' => 'Confirmez les droits de publication des images et contenus.',
        ] as $flag => $message) {
            if (! ($data[$flag] ?? false)) {
                $errors[$flag] = $message;
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
