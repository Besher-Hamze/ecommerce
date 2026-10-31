<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const SETTINGS_CACHE_KEY = 'store.settings';
    public const CATEGORIES_CACHE_KEY = 'store.nav_categories';

    public function register()
    {
        //
    }

    public function boot()
    {
        Paginator::useBootstrapFive();

        View::composer(['website.*'], function ($view) {
            $settings = Cache::remember(self::SETTINGS_CACHE_KEY, 3600, function () {
                return Setting::pluck('value', 'key')->all();
            });

            $navCategories = Cache::remember(self::CATEGORIES_CACHE_KEY, 3600, function () {
                return Category::whereNull('parent_id')
                    ->where('status', 1)
                    ->with(['children' => fn ($q) => $q->where('status', 1)->orderBy('sort_order')])
                    ->orderBy('sort_order')
                    ->get();
            });

            $wishlistIds = Auth::check()
                ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->all()
                : [];

            $view->with('storeSettings', $settings)
                ->with('navCategories', $navCategories)
                ->with('cartCount', CartService::totalQuantity(session('cart', [])))
                ->with('wishlistCount', count($wishlistIds))
                ->with('wishlistIds', $wishlistIds);
        });
    }
}
