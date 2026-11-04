<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'reference', 'customer_name', 'customer_phone', 'customer_email',
        'city', 'district', 'address', 'notes',
        'subtotal_gnf', 'shipping_gnf', 'total_gnf', 'currency',
        'status', 'payment_status', 'reserved_at', 'cancelled_at'
    ];

    protected function casts(): array
    {
        return [
            'subtotal_gnf' => 'integer',
            'shipping_gnf' => 'integer',
            'total_gnf' => 'integer',
            'reserved_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
