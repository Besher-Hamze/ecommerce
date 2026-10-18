<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationEvent extends Model
{
    public const UPDATED_AT = null;

    public const SOURCES = ['cross_sell', 'bundle', 'upsell', 'cf', 'similar', 'trending'];

    protected $fillable = ['user_id', 'session_id', 'event', 'source', 'product_id', 'context_product_id', 'value', 'created_at'];
}
