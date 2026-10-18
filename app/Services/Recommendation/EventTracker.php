<?php

namespace App\Services\Recommendation;

use App\Models\RecommendationEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Online evaluation log. CTR = click / impression, conversion = add_to_cart / click,
 * and purchases are attributed to the widget the item was added from.
 */
class EventTracker
{
    public static function impressions(string $source, Collection|array $products, ?int $contextProductId = null): void
    {
        $ids = collect($products)->pluck('id')->filter()->unique();
        if ($ids->isEmpty() || !in_array($source, RecommendationEvent::SOURCES, true)) {
            return;
        }
        $base = self::base() + ['event' => 'impression', 'source' => $source, 'context_product_id' => $contextProductId];
        DB::table('recommendation_events')->insert($ids->map(fn ($id) => $base + ['product_id' => $id])->values()->all());
    }

    public static function record(string $event, ?string $source, int $productId, ?int $contextProductId = null, ?float $value = null): void
    {
        if (!$source || !in_array($source, RecommendationEvent::SOURCES, true)) {
            return;
        }
        DB::table('recommendation_events')->insert(self::base() + [
            'event' => $event,
            'source' => $source,
            'product_id' => $productId,
            'context_product_id' => $contextProductId,
            'value' => $value,
        ]);
    }

    private static function base(): array
    {
        return [
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'created_at' => now(),
        ];
    }
}
