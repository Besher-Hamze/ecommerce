<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationRun extends Model
{
    protected $fillable = ['kind', 'status', 'params', 'stats', 'duration_ms'];

    protected $casts = [
        'params' => 'array',
        'stats' => 'array',
    ];
}
