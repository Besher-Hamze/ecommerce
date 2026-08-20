<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = "coupons";
    protected $fillable = [
        'title',
        'coupon_type',
        'code',
        'start_date',
        'expire_date',
        'min_purchase',
        'discount',
        'discount_type'
    ];
}
