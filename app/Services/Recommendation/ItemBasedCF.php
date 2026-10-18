<?php

namespace App\Services\Recommendation;

/**
 * Item-based collaborative filtering on implicit feedback
 * (Sarwar et al., 2001; top-N variant of Deshpande & Karypis, 2004).
 *
 * Each user-item pair gets an implicit preference r(u,i):
 *   purchase = 3, wishlist = 2, view = 1   (the strongest signal per pair is kept)
 *
 * Item-item similarity is the cosine between item columns of the user-item
 * matrix, damped by significance weighting (Koren, 2008) so that pairs supported
 * by very few common users do not dominate:
 *   sim(i,j) = cos(i,j) · n_ij / (n_ij + λ)
 *
 * Personalised top-N for user u:
 *   score(j) = Σ_{i ∈ history(u)} w(u,i) · sim(i,j)     for j ∉ purchased(u)
 * where w(u,i) also decays with the age of the interaction (see RecommendationService).
 */
class ItemBasedCF
{
    public const WEIGHTS = ['purchase' => 3.0, 'wishlist' => 2.0, 'view' => 1.0];

    /** @var array<int, array<int, float>> */
    private array $neighbours = [];

    public function __construct(private float $shrinkage = 5.0)
    {
    }

    /**
     * @param array<int, array<int, float>> $userItems user id => [item id => preference]
     */
    public function fit(array $userItems, int $k = 30, float $minSimilarity = 0.02): self
    {
        $norms = [];
        $dots = [];
        $common = [];

        foreach ($userItems as $items) {
            $ids = array_keys($items);
            $count = count($ids);
            foreach ($items as $i => $r) {
                $norms[$i] = ($norms[$i] ?? 0) + $r * $r;
            }
            for ($a = 0; $a < $count; $a++) {
                $i = $ids[$a];
                $ri = $items[$i];
                for ($b = $a + 1; $b < $count; $b++) {
                    $j = $ids[$b];
                    [$x, $y] = $i < $j ? [$i, $j] : [$j, $i];
                    $dots[$x][$y] = ($dots[$x][$y] ?? 0) + $ri * $items[$j];
                    $common[$x][$y] = ($common[$x][$y] ?? 0) + 1;
                }
            }
        }

        $sims = [];
        foreach ($dots as $i => $row) {
            foreach ($row as $j => $dot) {
                $n = $common[$i][$j];
                $sim = $dot / sqrt($norms[$i] * $norms[$j]) * ($n / ($n + $this->shrinkage));
                if ($sim >= $minSimilarity) {
                    $sims[$i][$j] = $sim;
                    $sims[$j][$i] = $sim;
                }
            }
        }

        foreach ($sims as $i => $row) {
            arsort($row);
            $this->neighbours[$i] = array_slice($row, 0, $k, true);
        }

        return $this;
    }

    /** @return array<int, array<int, float>> */
    public function neighbours(): array
    {
        return $this->neighbours;
    }

    /**
     * @param array<int, float> $history item id => weight
     * @param array<int, bool> $exclude
     * @return array<int, float> item id => score, best first
     */
    public function recommend(array $history, array $exclude = [], int $k = 10): array
    {
        $scores = [];
        foreach ($history as $i => $w) {
            foreach ($this->neighbours[$i] ?? [] as $j => $sim) {
                if (!isset($exclude[$j])) {
                    $scores[$j] = ($scores[$j] ?? 0) + $w * $sim;
                }
            }
        }
        arsort($scores);

        return array_slice($scores, 0, $k, true);
    }
}
