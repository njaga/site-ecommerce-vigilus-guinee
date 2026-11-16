<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingRequest extends Model
{
    protected $fillable = ['training_session_id', 'session_title', 'request_type', 'name', 'company', 'email', 'phone', 'participants', 'preferred_period', 'message', 'consented_at', 'status'];

    protected function casts(): array
    {
        return ['consented_at' => 'datetime', 'participants' => 'integer'];
    }
}
