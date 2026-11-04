<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const PENDING = 'pending_review';
    public const RESERVED = 'reserved';
    public const CONFIRMED = 'confirmed';
    public const CANCELLED = 'cancelled';

    protected $fillable = [
        'reference', 'submission_key', 'customer_name', 'customer_phone', 'customer_email',
        'city', 'district', 'address', 'notes', 'consented_at',
        'subtotal_gnf', 'shipping_gnf', 'total_gnf', 'currency',
        'status', 'payment_status', 'reserved_at', 'confirmed_at', 'cancelled_at'
    ];

    protected function casts(): array
    {
        return [
            'subtotal_gnf' => 'integer',
            'shipping_gnf' => 'integer',
            'total_gnf' => 'integer',
            'reserved_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'consented_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('created_at')->orderBy('id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::PENDING => 'En attente de validation par Vigilus',
            self::RESERVED => 'Stock réservé — confirmation commerciale en cours',
            self::CONFIRMED => 'Commande confirmée par Vigilus — règlement hors site',
            self::CANCELLED => 'Demande annulée',
            default => 'Statut en cours de vérification',
        };
    }
}
