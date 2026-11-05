<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogImportBatch extends Model
{
    protected $fillable = ['created_by', 'total_rows', 'status'];

    public function rows(): HasMany
    {
        return $this->hasMany(CatalogImportRow::class, 'batch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
