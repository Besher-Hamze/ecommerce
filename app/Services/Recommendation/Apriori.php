<?php

namespace App\Services\Recommendation;

/**
 * Apriori frequent-itemset mining and association-rule generation
 * (Agrawal & Srikant, 1994) - the cross-selling engine.
 *
 * Candidate k-itemsets are counted per transaction: every k-combination of the
 * transaction's frequent items is generated and kept only if all of its
 * (k-1)-subsets are frequent (the Apriori / downward-closure property). With
 * small baskets this is far cheaper than testing every candidate against every
 * transaction.
 *
 * Rule metrics for X => Y:
 *   support    = P(X ∪ Y)
 *   confidence = P(Y | X) = support(X ∪ Y) / support(X)
 *   lift       = confidence / support(Y)              (> 1 means positive association)
 *   leverage   = support(X ∪ Y) - support(X)·support(Y)
 *   conviction = (1 - support(Y)) / (1 - confidence) (null when confidence = 1)
 */
class Apriori
{
    /** @var array<int, array<int, int|string>> */
    private array $transactions;
    private int $n;
    /** @var array<string, int> itemset key => transaction count */
    private array $counts = [];

    public function __construct(array $transactions)
    {
        $clean = [];
        foreach ($transactions as $transaction) {
            $items = array_values(array_unique(array_filter($transaction, fn ($i) => $i !== null && $i !== '')));
            if ($items) {
                sort($items);
                $clean[] = $items;
            }
        }
        $this->transactions = $clean;
        $this->n = count($clean);
    }

    public function transactionCount(): int
    {
        return $this->n;
    }

    /**
     * @return array<string, array{items: array, count: int, support: float}>
     */
    public function frequentItemsets(float $minSupport, int $maxSize = 3): array
    {
        $this->counts = [];
        if ($this->n === 0) {
            return [];
        }
        $minCount = max(2, (int) ceil($minSupport * $this->n));

        $single = [];
        foreach ($this->transactions as $t) {
            foreach ($t as $item) {
                $single[$item] = ($single[$item] ?? 0) + 1;
            }
        }
        $frequent = [];
        foreach ($single as $item => $count) {
            if ($count >= $minCount) {
                $frequent[(string) $item] = $count;
            }
        }
        $this->counts = $frequent;
        $previous = $frequent;

        for ($k = 2; $k <= $maxSize && count($previous) >= $k; $k++) {
            $candidates = [];
            foreach ($this->transactions as $t) {
                $items = array_values(array_filter($t, fn ($i) => isset($frequent[(string) $i])));
                if (count($items) < $k) {
                    continue;
                }
                foreach ($this->combinations($items, $k) as $combo) {
                    if ($k > 2 && !$this->allSubsetsFrequent($combo, $previous)) {
                        continue;
                    }
                    $key = implode('|', $combo);
                    $candidates[$key] = ($candidates[$key] ?? 0) + 1;
                }
            }
            $previous = array_filter($candidates, fn ($c) => $c >= $minCount);
            $this->counts += $previous;
        }

        $result = [];
        foreach ($this->counts as $key => $count) {
            $result[$key] = [
                'items' => $this->parseKey($key),
                'count' => $count,
                'support' => $count / $this->n,
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array> rules sorted by score (desc)
     */
    public function rules(float $minSupport, float $minConfidence, int $maxSize = 3, float $minLift = 1.0): array
    {
        $itemsets = $this->frequentItemsets($minSupport, $maxSize);
        $rules = [];

        foreach ($itemsets as $data) {
            $items = $data['items'];
            if (count($items) < 2) {
                continue;
            }
            foreach ($this->properSubsets($items) as $antecedent) {
                $consequent = array_values(array_diff($items, $antecedent));
                $supX = $this->counts[implode('|', $antecedent)] / $this->n;
                $supY = $this->counts[implode('|', $consequent)] / $this->n;
                $supXY = $data['support'];
                $confidence = $supXY / $supX;
                $lift = $confidence / $supY;
                if ($confidence < $minConfidence || $lift < $minLift) {
                    continue;
                }
                $rules[] = [
                    'antecedent' => $antecedent,
                    'consequent' => $consequent,
                    'support' => round($supXY, 6),
                    'confidence' => round($confidence, 6),
                    'lift' => round($lift, 6),
                    'leverage' => round($supXY - $supX * $supY, 6),
                    'conviction' => $confidence >= 1 ? null : round((1 - $supY) / (1 - $confidence), 6),
                    'antecedent_count' => (int) round($supX * $this->n),
                    'consequent_count' => (int) round($supY * $this->n),
                    'both_count' => $data['count'],
                    'total_transactions' => $this->n,
                    'score' => self::score($confidence, $lift),
                ];
            }
        }

        usort($rules, fn ($a, $b) => $b['score'] <=> $a['score'] ?: $b['support'] <=> $a['support']);

        return $rules;
    }

    /**
     * Ranking score in [0, 1]: blends how reliable the rule is (confidence)
     * with how much stronger than chance it is (lift mapped to 1 - 1/lift).
     */
    public static function score(float $confidence, float $lift): float
    {
        return round(0.6 * $confidence + 0.4 * max(0, 1 - 1 / max($lift, 1e-9)), 6);
    }

    private function allSubsetsFrequent(array $combo, array $previous): bool
    {
        $k = count($combo);
        for ($skip = 0; $skip < $k; $skip++) {
            $subset = $combo;
            unset($subset[$skip]);
            if (!isset($previous[implode('|', $subset)])) {
                return false;
            }
        }

        return true;
    }

    private function combinations(array $items, int $k): \Generator
    {
        $n = count($items);
        $idx = range(0, $k - 1);
        while (true) {
            yield array_map(fn ($i) => $items[$i], $idx);
            $i = $k - 1;
            while ($i >= 0 && $idx[$i] === $n - $k + $i) {
                $i--;
            }
            if ($i < 0) {
                return;
            }
            $idx[$i]++;
            for ($j = $i + 1; $j < $k; $j++) {
                $idx[$j] = $idx[$j - 1] + 1;
            }
        }
    }

    private function properSubsets(array $items): array
    {
        $n = count($items);
        $subsets = [];
        for ($mask = 1; $mask < (1 << $n) - 1; $mask++) {
            $subset = [];
            for ($i = 0; $i < $n; $i++) {
                if ($mask & (1 << $i)) {
                    $subset[] = $items[$i];
                }
            }
            $subsets[] = $subset;
        }

        return $subsets;
    }

    private function parseKey(string $key): array
    {
        return array_map(fn ($v) => is_numeric($v) ? (int) $v : $v, explode('|', $key));
    }
}
