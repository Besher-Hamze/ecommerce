<?php

namespace App\Services\Recommendation;

/**
 * Accumulates top-K ranking metrics over many recommendation queries.
 */
class MetricAccumulator
{
    private int $queries = 0;
    private float $hits = 0;
    private float $precision = 0;
    private float $recall = 0;
    private float $mrr = 0;
    private array $recommended = [];

    public function __construct(private int $k)
    {
    }

    public function add(array $list, array $targets): void
    {
        $this->queries++;
        $targets = array_flip($targets);
        $relevant = 0;
        $firstRank = null;
        foreach ($list as $rank => $item) {
            $this->recommended[$item] = true;
            if (isset($targets[$item])) {
                $relevant++;
                $firstRank ??= $rank + 1;
            }
        }
        $this->hits += $relevant > 0 ? 1 : 0;
        $this->precision += $relevant / $this->k;
        $this->recall += $relevant / max(1, count($targets));
        $this->mrr += $firstRank ? 1 / $firstRank : 0;
    }

    public function result(int $catalogueSize): array
    {
        $q = max(1, $this->queries);

        return [
            'queries' => $this->queries,
            'hit_rate' => round($this->hits / $q, 4),
            'precision' => round($this->precision / $q, 4),
            'recall' => round($this->recall / $q, 4),
            'mrr' => round($this->mrr / $q, 4),
            'coverage' => round(count($this->recommended) / max(1, $catalogueSize) * 100, 1),
        ];
    }
}
