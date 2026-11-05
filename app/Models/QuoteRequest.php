<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    public const NEW = 'new';
    public const IN_PROGRESS = 'in_progress';
    public const CLOSED = 'closed';

    protected $fillable = [
        'product_id', 'name', 'email', 'phone', 'company', 'quantity',
        'message', 'consented_at', 'status', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['consented_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(QuoteRequestEvent::class)->orderBy('created_at')->orderBy('id');
    }
}
