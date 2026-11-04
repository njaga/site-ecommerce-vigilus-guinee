<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    protected $fillable = ['product_id', 'name', 'email', 'phone', 'company', 'quantity', 'message', 'consented_at', 'status'];

    protected function casts(): array
    {
        return ['consented_at' => 'datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
