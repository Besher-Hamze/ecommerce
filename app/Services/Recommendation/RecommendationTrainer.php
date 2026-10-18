<?php

namespace App\Services\Recommendation;

use App\Models\ProductRecommendation;
use App\Models\RecommendationRun;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Offline training ("batch layer"): mines rules and neighbourhoods from the
 * behavioural data and materialises them into association_rules and
 * product_recommendations, which the storefront reads with indexed queries.
 */
class RecommendationTrainer
{
    public const DEFAULTS = [
        'min_support' => 0.003,
        'min_confidence' => 0.10,
        'max_itemset_size' => 3,
        'type_min_support' => 0.01,
        'type_min_confidence' => 0.10,
        'top_k' => 8,
    ];

    public const CACHE_VERSION_KEY = 'reco.model_version';

    public function __construct(private RecommendationData $data)
    {
    }

    public function train(array $params = []): RecommendationRun
    {
        $params = array_merge(self::DEFAULTS, array_filter($params, fn ($v) => $v !== null));
        $started = microtime(true);
        $timings = [];
        $last = $started;
        $tick = function (string $step) use (&$timings, &$last) {
            $now = microtime(true);
            $timings[$step] = (int) round(($now - $last) * 1000);
            $last = $now;
        };

        $baskets = $this->data->baskets();
        $products = $this->data->products();
        $feedback = $this->data->feedback();
        $tick('load_data');

        $transactions = array_column($baskets, 'items');
        $apriori = new Apriori($transactions);
        $productRules = $apriori->rules($params['min_support'], $params['min_confidence'], (int) $params['max_itemset_size']);
        $tick('apriori_products');

        $typeTransactions = array_map(
            fn ($items) => array_values(array_unique(array_map(fn ($id) => $products[$id]['type'] ?? null, $items))),
            $transactions
        );
        $typeRules = (new Apriori($typeTransactions))->rules($params['type_min_support'], $params['type_min_confidence'], 2);
        $tick('apriori_types');

        $tfidf = (new TfIdf())->fit($products);
        $similar = $tfidf->neighbours(12, 0.08);
        $tick('tfidf');

        $upsells = (new UpsellRanker($tfidf))->rank($products, 4);
        $tick('upsell');

        $cf = (new ItemBasedCF())->fit(RecommendationData::userItemMatrix($baskets, $feedback), 30);
        $cfNeighbours = $cf->neighbours();
        $tick('item_cf');

        $crossSells = (new CrossSellBuilder())->build($productRules, $typeRules, $products, $cfNeighbours, (int) $params['top_k'], true);
        $tick('cross_sell');

        DB::transaction(function () use ($productRules, $typeRules, $params, $crossSells, $upsells, $similar, $cfNeighbours) {
            DB::table('association_rules')->delete();
            DB::table('product_recommendations')->delete();

            $now = now();
            $rows = [];
            foreach ([['product', $productRules, $params['min_support'], $params['min_confidence']],
                      ['type', $typeRules, $params['type_min_support'], $params['type_min_confidence']]] as [$level, $rules, $minSup, $minConf]) {
                foreach ($rules as $rule) {
                    $rows[] = [
                        'level' => $level,
                        'antecedent' => json_encode($rule['antecedent']),
                        'consequent' => json_encode($rule['consequent']),
                        'antecedent_size' => count($rule['antecedent']),
                        'support' => $rule['support'], 'confidence' => $rule['confidence'], 'lift' => $rule['lift'],
                        'leverage' => $rule['leverage'], 'conviction' => $rule['conviction'],
                        'antecedent_count' => $rule['antecedent_count'], 'consequent_count' => $rule['consequent_count'],
                        'both_count' => $rule['both_count'], 'total_transactions' => $rule['total_transactions'],
                        'min_support' => $minSup, 'min_confidence' => $minConf,
                        'created_at' => $now, 'updated_at' => $now,
                    ];
                }
            }
            $this->bulkInsert('association_rules', $rows);

            $rows = [];
            $this->collect($rows, ProductRecommendation::CROSS_SELL, $crossSells, fn ($m) => $m['score'], fn ($m) => $m);
            $this->collect($rows, ProductRecommendation::UPSELL, $upsells, fn ($m) => $m['score'], fn ($m) => $m);
            $this->collect($rows, ProductRecommendation::SIMILAR, $similar, fn ($s) => $s, fn () => null);
            $this->collect($rows, ProductRecommendation::CF, array_map(fn ($n) => array_slice($n, 0, 20, true), $cfNeighbours), fn ($s) => $s, fn () => null);
            $this->bulkInsert('product_recommendations', $rows);
        });
        $tick('persist');

        $stats = [
            'transactions' => $apriori->transactionCount(),
            'products' => count($products),
            'product_rules' => count($productRules),
            'type_rules' => count($typeRules),
            'cross_sell_coverage' => $this->coverage($crossSells, count($products)),
            'cross_sell_product_level_coverage' => $this->coverage(array_map(
                fn ($list) => array_filter($list, fn ($m) => $m['level'] === 'product'), $crossSells), count($products)),
            'upsell_coverage' => $this->coverage($upsells, count($products)),
            'similar_coverage' => $this->coverage($similar, count($products)),
            'cf_coverage' => $this->coverage($cfNeighbours, count($products)),
            'feedback_events' => count($feedback),
            'timings_ms' => $timings,
        ];

        $run = RecommendationRun::create([
            'kind' => 'train',
            'status' => 'completed',
            'params' => $params,
            'stats' => $stats,
            'duration_ms' => (int) round((microtime(true) - $started) * 1000),
        ]);
        Cache::forever(self::CACHE_VERSION_KEY, $run->id);

        return $run;
    }

    private function collect(array &$rows, string $type, array $lists, callable $score, callable $meta): void
    {
        foreach ($lists as $productId => $list) {
            $rank = 0;
            foreach ($list as $recommendedId => $value) {
                $m = $meta($value);
                $rows[] = [
                    'type' => $type,
                    'product_id' => $productId,
                    'recommended_product_id' => $recommendedId,
                    'score' => round($score($value), 6),
                    'rank' => ++$rank,
                    'meta' => $m === null ? null : json_encode($m),
                ];
            }
        }
    }

    private function coverage(array $lists, int $total): float
    {
        $covered = count(array_filter($lists, fn ($l) => !empty($l)));

        return $total ? round($covered / $total * 100, 1) : 0.0;
    }

    private function bulkInsert(string $table, array $rows): void
    {
        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }
}
