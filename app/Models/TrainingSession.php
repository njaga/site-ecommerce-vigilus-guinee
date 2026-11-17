<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    protected $fillable = ['title', 'description', 'starts_at', 'location', 'audience', 'is_published'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'is_published' => 'boolean'];
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('starts_at', '>', now())->orderBy('starts_at');
    }
}
