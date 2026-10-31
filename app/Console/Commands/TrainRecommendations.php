<?php

namespace App\Console\Commands;

use App\Services\Recommendation\RecommendationTrainer;
use Illuminate\Console\Command;

class TrainRecommendations extends Command
{
    protected $signature = 'recommendations:train
        {--min-support= : Apriori minimum support for product-level rules (fraction, e.g. 0.003)}
        {--min-confidence= : Apriori minimum confidence (fraction, e.g. 0.1)}
        {--type-min-support= : Apriori minimum support for type-level rules}
        {--type-min-confidence= : Apriori minimum confidence for type-level rules}
        {--max-itemset-size= : Maximum frequent itemset size}
        {--top-k= : Recommendations kept per product}';

    protected $description = 'Mine association rules (cross-sell), rank upsells, build item-based CF and content neighbourhoods';

    public function handle(RecommendationTrainer $trainer)
    {
        $this->info('Training recommendation models...');

        $opt = function (string $name) {
            $v = $this->option($name);

            return ($v === null || $v === '') ? null : (str_contains($name, 'size') || $name === 'top-k'
                ? (int) $v
                : (float) $v);
        };

        $run = $trainer->train([
            'min_support' => $opt('min-support'),
            'min_confidence' => $opt('min-confidence'),
            'type_min_support' => $opt('type-min-support'),
            'type_min_confidence' => $opt('type-min-confidence'),
            'max_itemset_size' => $opt('max-itemset-size'),
            'top_k' => $opt('top-k'),
        ]);

        $s = $run->stats;
        $this->table(['Metric', 'Value'], [
            ['Transactions (completed carts)', $s['transactions']],
            ['Product-level rules', $s['product_rules']],
            ['Type-level rules', $s['type_rules']],
            ['Cross-sell coverage (any level)', $s['cross_sell_coverage'] . ' %'],
            ['Cross-sell coverage (product rules only)', $s['cross_sell_product_level_coverage'] . ' %'],
            ['Upsell coverage', $s['upsell_coverage'] . ' %'],
            ['Item-CF coverage', $s['cf_coverage'] . ' %'],
            ['Content-similarity coverage', $s['similar_coverage'] . ' %'],
            ['Duration', $run->duration_ms . ' ms'],
        ]);
        $this->line('Params: ' . json_encode($run->params));
        $this->line('Step timings (ms): ' . json_encode($s['timings_ms']));

        return self::SUCCESS;
    }
}
