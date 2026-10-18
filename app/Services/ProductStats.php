<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * Keeps the denormalised counters on `products` (sales, ratings, views) in sync.
 * They let listing pages sort by popularity / rating without aggregate queries.
 */
class ProductStats
{
    public static function refresh(): void
    {
        DB::statement('UPDATE products SET sales_count = 0, rating_avg = 0, rating_count = 0, view_count = 0');

        DB::statement('UPDATE products p SET sales_count = s.qty FROM (
                SELECT oi.product_id, SUM(oi.quantity) AS qty
                FROM order_items oi JOIN orders o ON o.id = oi.order_id
                WHERE o.state <> ?
                GROUP BY oi.product_id
            ) s WHERE s.product_id = p.id', [Order::STATE_CANCELLED]);

        DB::statement('UPDATE products p SET rating_avg = s.avg_star, rating_count = s.n FROM (
                SELECT product_id, ROUND(AVG(star)::numeric, 2) AS avg_star, COUNT(*) AS n
                FROM reviews WHERE star IS NOT NULL GROUP BY product_id
            ) s WHERE s.product_id = p.id');

        DB::statement('UPDATE products p SET view_count = s.n FROM (
                SELECT product_id, COUNT(*) AS n FROM product_views GROUP BY product_id
            ) s WHERE s.product_id = p.id');
    }

    public static function refreshRating(int $productId): void
    {
        DB::statement('UPDATE products SET
                rating_avg = COALESCE((SELECT ROUND(AVG(star)::numeric, 2) FROM reviews WHERE product_id = ? AND star IS NOT NULL), 0),
                rating_count = (SELECT COUNT(*) FROM reviews WHERE product_id = ? AND star IS NOT NULL)
            WHERE id = ?', [$productId, $productId, $productId]);
    }
}
