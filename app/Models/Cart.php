<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'order_id', 'status', 'total'];

    // Relationship: A cart has many items
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Relationship: A cart belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
