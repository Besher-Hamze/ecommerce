<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductView extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'session_id', 'product_id', 'source', 'created_at'];
}
