<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       $categories = Cache::rememberForever('categories', function () {
            return Category::where('status','=',1)->get();
        });

        $settings = Cache::rememberForever('settings', function () {
            return Setting::all();
        });
		
    }
}
