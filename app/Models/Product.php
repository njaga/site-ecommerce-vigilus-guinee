<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Support\CatalogPreview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'brand', 'sku', 'slug', 'name', 'short_description',
        'description', 'specifications', 'price_gnf', 'stock_qty', 'is_active',
        'is_featured', 'datasheet_url', 'seo_title', 'seo_description',
        'price_on_request', 'technical_verified_at', 'commercial_verified_at',
        'media_rights_verified_at', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'price_gnf' => 'integer',
            'stock_qty' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'price_on_request' => 'boolean',
            'technical_verified_at' => 'datetime',
            'commercial_verified_at' => 'datetime',
            'media_rights_verified_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        if (CatalogPreview::enabled()) {
            // All existing products, including drafts, remain DB drafts.
            return $query;
        }

        return $query->where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('is_active', true));
    }

    public function getDisplayPriceGnfAttribute(): ?int
    {
        return CatalogPreview::displayPriceGnf($this->price_gnf, (bool) $this->price_on_request);
    }

    public function getCanOrderAttribute(): bool
    {
        if (CatalogPreview::enabled()) {
            // Allows cart/checkout layout inspection with zero test stock.
            // Sending a real order is separately forbidden in checkout.
            return $this->category !== null;
        }

        return $this->is_active
            && $this->category?->is_active
            && ! $this->price_on_request
            && $this->price_gnf !== null
            && $this->stock_qty > 0;
    }
}
