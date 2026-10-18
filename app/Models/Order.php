<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATE_PENDING = 0;
    public const STATE_APPROVED = 1;
    public const STATE_CANCELLED = 2;

    protected $table = 'orders';

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'email', 'mobile', 'address', 'company', 'city', 'country',
        'zipcode', 'order_notes', 'total_quantity', 'subtotal', 'shipping', 'total_price', 'state',
    ];

    protected $casts = [
        'id' => 'integer',
        'total_quantity' => 'integer',
        'total_price' => 'float',
        'subtotal' => 'float',
        'shipping' => 'float',
        'state' => 'integer',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')->withPivot('quantity');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStateLabelAttribute(): string
    {
        return match ($this->state) {
            self::STATE_APPROVED => 'Approved',
            self::STATE_CANCELLED => 'Cancelled',
            default => 'Pending',
        };
    }
}
