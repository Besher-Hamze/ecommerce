<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRecommendation extends Model
{
    public const CROSS_SELL = 'cross_sell';
    public const UPSELL = 'upsell';
    public const SIMILAR = 'similar';
    public const CF = 'cf';

    public $timestamps = false;

    protected $fillable = ['type', 'product_id', 'recommended_product_id', 'score', 'rank', 'meta'];

    protected $casts = [
        'score' => 'double',
        'meta' => 'array',
    ];
}
