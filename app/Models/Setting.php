<?php

namespace App\Models;

use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = "settings";

    protected $fillable = ['key', 'value'];

    protected static function booted()
    {
        $forget = fn () => Cache::forget(AppServiceProvider::SETTINGS_CACHE_KEY);
        static::saved($forget);
        static::deleted($forget);
    }
}
