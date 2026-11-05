<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['product_id', 'sku_snapshot', 'name_snapshot', 'unit_price_gnf', 'quantity', 'subtotal_gnf'];

    protected function casts(): array
    {
        return ['unit_price_gnf' => 'integer', 'quantity' => 'integer', 'subtotal_gnf' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
