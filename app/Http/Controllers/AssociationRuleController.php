<?php

namespace App\Http\Controllers;

use App\Services\Recommendation\RecommendationService;
use Illuminate\Http\Request;

/**
 * Storefront "for you" page. Admin association-rule browsing lives under
 * Admin\RecommendationController.
 */
class AssociationRuleController extends Controller
{
    public function recommendations(Request $request, RecommendationService $reco)
    {
        $productId = (int) $request->input('product_id');
        $cartIds = collect(session('cart', []))->pluck('id')->map(fn ($id) => (int) $id)->all();

        if ($productId) {
            $recommendations = $reco->crossSell($productId, 12);
            $title = 'Frequently bought together';
        } elseif ($cartIds) {
            $recommendations = $reco->forCart($cartIds, 12);
            $title = 'Complete your look';
        } else {
            $recommendations = $reco->personalized(auth()->id(), session()->getId(), [], 12);
            if ($recommendations->isEmpty()) {
                $recommendations = $reco->trending(12);
            }
            $title = 'Recommended for you';
        }

        return view('website.recommendations', compact('recommendations', 'title', 'productId'));
    }
}
