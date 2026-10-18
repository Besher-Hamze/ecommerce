<?php

namespace App\Console\Commands;

use App\Services\Recommendation\RecommendationTrainer;
use Illuminate\Console\Command;

class TrainRecommendations extends Command
{
    protected $signature = 'recommendations:train
        {--min-support= : Apriori minimum support for product-level rules (fraction, e.g. 0.003)}
        {--min-confidence= : Apriori minimum confidence (fraction, e.g. 0.1)}';

    protected $description = 'Mine association rules (cross-sell), rank upsells, build item-based CF and content neighbourhoods';

    public function handle(RecommendationTrainer $trainer)
    {
        $this->info('Training recommendation models...');
        $run = $trainer->train([
            'min_support' => $this->option('min-support') !== null ? (float) $this->option('min-support') : null,
            'min_confidence' => $this->option('min-confidence') !== null ? (float) $this->option('min-confidence') : null,
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
        $this->line('Step timings (ms): ' . json_encode($s['timings_ms']));

        return self::SUCCESS;
    }
}
