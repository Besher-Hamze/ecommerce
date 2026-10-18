<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Slider;
use App\Services\CartService;
use App\Services\Recommendation\EventTracker;
use App\Services\Recommendation\RecommendationService;
use Database\Seeders\CatalogSeeder;

class HomeController extends Controller
{
    public function __construct(private RecommendationService $reco)
    {
    }

    public function index()
    {
        $cartIds = CartService::productIds(session('cart', []));
        $sessionId = session()->getId();
        $userId = auth()->id();

        $forYou = $this->reco->personalized($userId, $sessionId, $cartIds, 8);
        if ($forYou->isEmpty()) {
            $forYou = $this->reco->trending(8);
            EventTracker::impressions('trending', $forYou);
        } else {
            EventTracker::impressions('cf', $forYou);
        }

        $trending = $this->reco->trending(8, $forYou->pluck('id')->all());
        EventTracker::impressions('trending', $trending);

        $newArrivals = Product::catalog()
            ->orderByDesc('products.created_at')
            ->take(8)
            ->get();

        $bestSellers = Product::catalog()
            ->orderByDesc('products.sales_count')
            ->take(8)
            ->get();

        $slider_images = Slider::all();
        $reviews = Review::with('user')->latest()->take(5)->get();
        $taxonomy = CatalogSeeder::TAXONOMY;

        return view('website.index', compact(
            'forYou',
            'trending',
            'newArrivals',
            'bestSellers',
            'slider_images',
            'reviews',
            'taxonomy'
        ));
    }
}
