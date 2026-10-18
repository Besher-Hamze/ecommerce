<?php

namespace App\Services\Recommendation;

use App\Models\RecommendationRun;

/**
 * Offline evaluation with a temporal hold-out split.
 *
 * For every customer with at least two orders, the most recent order is the
 * test set; everything before it (orders, views, wishlists) is training data.
 * Models are re-trained in memory on the training part only, so no
 * information from the future leaks into the recommendations.
 *
 * Task A - basket completion (cross-selling):
 *   for each test basket with >= 2 items, every item is used once as the seed and
 *   the other items of the basket are the targets.
 * Task B - next-basket personalisation:
 *   the customer's training history is the input, the items of the test order are
 *   the targets (items bought before are excluded).
 * Upselling has no ground truth in purchase logs (a shopper never buys both the
 * product and its upgrade), so it is evaluated with descriptive quality metrics.
 *
 * Metrics: HitRate@K, Precision@K, Recall@K, MRR@K and catalogue coverage.
 */
class RecommendationEvaluator
{
    public function __construct(private RecommendationData $data)
    {
    }

    public function evaluate(int $k = 5, array $params = []): RecommendationRun
    {
        $params = array_merge(RecommendationTrainer::DEFAULTS, $params);
        $started = microtime(true);
        mt_srand(7);

        $baskets = $this->data->baskets();
        $products = $this->data->products();
        $feedback = $this->data->feedback();

        [$train, $test] = $this->temporalSplit($baskets);
        // Browsing that belongs to the test purchase session (the days right before it) is
        // excluded too, otherwise "viewed then bought" leaks the answer into the history.
        $cutoff = [];
        foreach ($test as $basket) {
            $cutoff[$basket['user']] = date('Y-m-d H:i:s', strtotime($basket['date']) - 3 * 86400);
        }
        $trainFeedback = array_values(array_filter($feedback, fn ($e) => !isset($cutoff[$e['user']]) || $e['date'] < $cutoff[$e['user']]));

        // --- Models trained on the training split only -----------------------------------
        $transactions = array_column($train, 'items');
        $productRules = (new Apriori($transactions))->rules($params['min_support'], $params['min_confidence'], (int) $params['max_itemset_size']);
        $typeTransactions = array_map(fn ($items) => array_values(array_unique(array_map(fn ($id) => $products[$id]['type'], $items))), $transactions);
        $typeRules = (new Apriori($typeTransactions))->rules($params['type_min_support'], $params['type_min_confidence'], 2);

        $matrix = RecommendationData::userItemMatrix($train, $trainFeedback);
        $cf = (new ItemBasedCF())->fit($matrix, 30);
        $cfNeighbours = $cf->neighbours();
        $multilevel = (new CrossSellBuilder())->build($productRules, $typeRules, $products, $cfNeighbours, max(10, $k));
        $crossSell = (new CrossSellBuilder())->build($productRules, $typeRules, $products, $cfNeighbours, max(10, $k), true);
        $productOnly = array_map(fn ($list) => array_filter($list, fn ($m) => $m['level'] === 'product'), $crossSell);

        $tfidf = (new TfIdf())->fit($products);
        $content = $tfidf->neighbours(max(10, $k), 0.0);

        $popularity = [];
        foreach ($transactions as $items) {
            foreach ($items as $item) {
                $popularity[$item] = ($popularity[$item] ?? 0) + 1;
            }
        }
        arsort($popularity);
        $popular = array_keys($popularity);
        $allIds = array_keys($products);

        // --- Task A: basket completion ---------------------------------------------------
        $seedMethods = [
            'hybrid_apriori_cf' => fn ($seed) => array_keys($crossSell[$seed] ?? []),
            'apriori_multilevel' => fn ($seed) => array_keys($multilevel[$seed] ?? []),
            'apriori_product_only' => fn ($seed) => array_keys($productOnly[$seed] ?? []),
            'item_cf' => fn ($seed) => array_keys($cfNeighbours[$seed] ?? []),
            'content_tfidf' => fn ($seed) => array_keys($content[$seed] ?? []),
            'popularity' => fn ($seed) => $popular,
            'random' => fn ($seed) => $this->randomSample($allIds, $k + 1),
        ];
        $taskA = [];
        foreach ($seedMethods as $name => $recommend) {
            $acc = new MetricAccumulator($k);
            foreach ($test as $basket) {
                if (count($basket['items']) < 2) {
                    continue;
                }
                foreach ($basket['items'] as $seed) {
                    $targets = array_values(array_diff($basket['items'], [$seed]));
                    $list = array_values(array_diff($recommend($seed), [$seed]));
                    $acc->add(array_slice($list, 0, $k), $targets);
                }
            }
            $taskA[$name] = $acc->result(count($products));
        }

        // --- Task B: personalised next basket ----------------------------------------------
        $userMethods = [
            'item_cf' => fn ($history, $exclude) => array_keys($cf->recommend($history, $exclude, $k)),
            'content_profile' => fn ($history, $exclude) => array_keys(array_diff_key($tfidf->scoreProfile($history), $exclude)),
            'popularity' => fn ($history, $exclude) => array_values(array_filter($popular, fn ($id) => !isset($exclude[$id]))),
            'random' => fn ($history, $exclude) => $this->randomSample($allIds, $k),
        ];
        $purchasedBefore = [];
        foreach ($train as $basket) {
            foreach ($basket['items'] as $item) {
                $purchasedBefore[$basket['user']][$item] = true;
            }
        }
        $taskB = [];
        foreach ($userMethods as $name => $recommend) {
            $acc = new MetricAccumulator($k);
            foreach ($test as $basket) {
                $history = $matrix[$basket['user']] ?? [];
                $exclude = $purchasedBefore[$basket['user']] ?? [];
                $targets = array_values(array_filter($basket['items'], fn ($i) => !isset($exclude[$i])));
                if (!$history || !$targets) {
                    continue;
                }
                $acc->add(array_slice($recommend($history, $exclude), 0, $k), $targets);
            }
            $taskB[$name] = $acc->result(count($products));
        }

        // --- Upselling quality --------------------------------------------------------------
        $upsells = (new UpsellRanker($tfidf))->rank($products, 4);
        $firsts = array_filter(array_map(fn ($list) => reset($list) ?: null, $upsells));
        $upsell = [
            'coverage' => round(count($firsts) / max(1, count($products)) * 100, 1),
            'avg_rating_gain' => $this->avg(array_map(fn ($m) => $m['rating_to'] - $m['rating_from'], $firsts), 3),
            'avg_price_uplift_pct' => $this->avg(array_column($firsts, 'uplift_pct'), 1),
            'avg_similarity' => $this->avg(array_column($firsts, 'similarity'), 3),
            'same_brand_pct' => round(count(array_filter(array_keys($firsts), fn ($id) => isset($upsells[$id])
                && $products[array_key_first($upsells[$id])]['brand_id'] === $products[$id]['brand_id'])) / max(1, count($firsts)) * 100, 1),
        ];

        return RecommendationRun::create([
            'kind' => 'evaluate',
            'status' => 'completed',
            'params' => array_merge($params, ['k' => $k]),
            'stats' => [
                'split' => ['train_baskets' => count($train), 'test_baskets' => count($test), 'products' => count($products)],
                'basket_completion' => $taskA,
                'personalisation' => $taskB,
                'upsell' => $upsell,
            ],
            'duration_ms' => (int) round((microtime(true) - $started) * 1000),
        ]);
    }

    private function temporalSplit(array $baskets): array
    {
        $byUser = [];
        foreach ($baskets as $id => $basket) {
            $byUser[$basket['user']][$id] = $basket;
        }
        $train = $test = [];
        foreach ($byUser as $user => $userBaskets) {
            uasort($userBaskets, fn ($a, $b) => strcmp($a['date'], $b['date']));
            if ($user && count($userBaskets) >= 2) {
                $lastId = array_key_last($userBaskets);
                $test[$lastId] = $userBaskets[$lastId];
                unset($userBaskets[$lastId]);
            }
            $train += $userBaskets;
        }

        return [$train, $test];
    }

    private function randomSample(array $ids, int $n): array
    {
        $picked = [];
        while (count($picked) < $n) {
            $picked[$ids[mt_rand(0, count($ids) - 1)]] = true;
        }

        return array_keys($picked);
    }

    private function avg(array $values, int $precision): float
    {
        return $values ? round(array_sum($values) / count($values), $precision) : 0.0;
    }
}
