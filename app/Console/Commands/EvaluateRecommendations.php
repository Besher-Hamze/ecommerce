<?php

namespace App\Console\Commands;

use App\Services\Recommendation\RecommendationEvaluator;
use Illuminate\Console\Command;

class EvaluateRecommendations extends Command
{
    protected $signature = 'recommendations:evaluate {--k=5 : Size of the recommendation list}';

    protected $description = 'Offline evaluation (temporal hold-out) of cross-sell, personalisation and upsell models against baselines';

    public function handle(RecommendationEvaluator $evaluator)
    {
        $k = max(1, (int) $this->option('k'));
        $this->info("Evaluating with K = {$k} (temporal hold-out: last order of each customer)...");
        $run = $evaluator->evaluate($k);
        $s = $run->stats;

        $this->line(sprintf('Train baskets: %d, test baskets: %d', $s['split']['train_baskets'], $s['split']['test_baskets']));
        foreach (['basket_completion' => 'Task A - basket completion (cross-sell)', 'personalisation' => 'Task B - personalised next basket'] as $key => $title) {
            $this->newLine();
            $this->info($title);
            $rows = [];
            foreach ($s[$key] as $method => $m) {
                $rows[] = [$method, $m['queries'], $m['hit_rate'], $m['precision'], $m['recall'], $m['mrr'], $m['coverage'] . ' %'];
            }
            $this->table(['Method', 'Queries', "HitRate@{$k}", "Precision@{$k}", "Recall@{$k}", "MRR@{$k}", 'Coverage'], $rows);
        }

        $this->newLine();
        $this->info('Upselling quality');
        $this->table(['Metric', 'Value'], collect($s['upsell'])->map(fn ($v, $key) => [$key, $v])->values()->all());
        $this->line("Duration: {$run->duration_ms} ms");

        return self::SUCCESS;
    }
}
