<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequestEvent extends Model
{
    protected $fillable = ['user_id', 'from_status', 'to_status', 'note'];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class, 'quote_request_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
