<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogImportRow extends Model
{
    public const PENDING = 'pending';
    public const DUPLICATE = 'duplicate';
    public const INVALID = 'invalid';
    public const APPROVED = 'approved';
    public const SKIPPED = 'skipped';

    protected $fillable = [
        'source_row', 'source_category', 'suggested_category_id', 'external_model',
        'internal_model', 'part_number', 'description', 'features', 'warranty_months',
        'status', 'issue', 'product_id',
    ];

    protected function casts(): array
    {
        return ['source_row' => 'integer', 'warranty_months' => 'integer'];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CatalogImportBatch::class, 'batch_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'suggested_category_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
