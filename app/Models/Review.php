<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    public function user()
{
    return $this->belongsTo(User::class);
}
public function shop_product()
{
    return $this->belongsTo(Shop_product::class);
}
}
