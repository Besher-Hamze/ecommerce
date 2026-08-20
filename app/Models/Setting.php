<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
class Setting extends Model
{
    use HasFactory;
    protected $table="settings";
    protected $fillable=['key','value'];
	public static function boot() {

        parent::boot();
        static::saving(function () {
			
            Cache::flush();
        } );
        static::updating(function () {
			
            Cache::flush();
        } );
        static::deleting(function(){Cache::flush();});
    }
}
