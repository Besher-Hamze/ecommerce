<?php

namespace App\Services;

class AprioriService
{
    protected array $transactions = [];
    protected int $transactionCount = 0;

    public function __construct(array $transactions)
    {
        // Normalize and deduplicate items inside each transaction
        $this->transactions = array_map(function ($transaction) {
            $clean = array_values(array_unique(array_filter(array_map('trim', $transaction))));
            sort($clean);
            return $clean;
        }, $transactions);

        // Remove empty transactions
        $this->transactions = array_values(array_filter($this->transactions, fn($t) => count($t) > 0));
        $this->transactionCount = count($this->transactions);
    }

    /**
     * Run Apriori and return association rules
     */
    public function generateRules(float $minSupport = 0.02, float $minConfidence = 0.3, int $maxItemsetSize = 3): array
    {
        if ($this->transactionCount === 0) {
            return [];
        }

        $frequentItemsets = $this->findFrequentItemsets($minSupport, $maxItemsetSize);
        $rules = $this->buildAssociationRules($frequentItemsets, $minConfidence);

        // Advanced filtering strategy (added value)
        // 1) Keep lift > 1 (positive association)
        // 2) Prefer non-trivial rules with confidence and support balance
        $rules = array_values(array_filter($rules, function ($rule) {
            return $rule['lift'] > 1.0
                && $rule['support'] >= 0.01
                && $rule['confidence'] >= 0.25;
        }));

        // Sort by practical impact score: confidence * lift * support
        usort($rules, function ($a, $b) {
            $scoreA = $a['confidence'] * $a['lift'] * $a['support'];
            $scoreB = $b['confidence'] * $b['lift'] * $b['support'];
            return $scoreB <=> $scoreA;
        });

        return $rules;
    }

    /**
     * Find frequent itemsets with Apriori
     */
    protected function findFrequentItemsets(float $minSupport, int $maxItemsetSize): array
    {
        $allFrequent = [];

        // L1: single-item candidates
        $itemCounts = [];
        foreach ($this->transactions as $transaction) {
            foreach ($transaction as $item) {
                $key = $this->itemsetKey([$item]);
                $itemCounts[$key] = ($itemCounts[$key] ?? 0) + 1;
            }
        }

        $Lk = $this->filterBySupport($itemCounts, $minSupport);
        $k = 1;

        while (!empty($Lk) && $k <= $maxItemsetSize) {
            $allFrequent = array_merge($allFrequent, $Lk);
            $k++;

            if ($k > $maxItemsetSize) {
                break;
            }

            $candidates = $this->generateCandidates(array_keys($Lk), $k);
            if (empty($candidates)) {
                break;
            }

            $candidateCounts = [];
            foreach ($this->transactions as $transaction) {
                foreach ($candidates as $candidateKey => $candidateItems) {
                    if ($this->isSubset($candidateItems, $transaction)) {
                        $candidateCounts[$candidateKey] = ($candidateCounts[$candidateKey] ?? 0) + 1;
                    }
                }
            }

            $Lk = $this->filterBySupport($candidateCounts, $minSupport);
        }

        return $allFrequent;
    }

    protected function generateCandidates(array $prevFrequentKeys, int $k): array
    {
        $candidates = [];
        $prevSets = array_map(fn($key) => explode('|', $key), $prevFrequentKeys);

        $count = count($prevSets);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $merged = array_values(array_unique(array_merge($prevSets[$i], $prevSets[$j])));
                sort($merged);

                if (count($merged) === $k) {
                    $key = $this->itemsetKey($merged);
                    $candidates[$key] = $merged;
                }
            }
        }

        return $candidates;
    }

    protected function filterBySupport(array $counts, float $minSupport): array
    {
        $result = [];
        foreach ($counts as $itemsetKey => $count) {
            $support = $count / $this->transactionCount;
            if ($support >= $minSupport) {
                $result[$itemsetKey] = [
                    'items' => explode('|', $itemsetKey),
                    'count' => $count,
                    'support' => $support,
                ];
            }
        }
        return $result;
    }

    protected function buildAssociationRules(array $frequentItemsets, float $minConfidence): array
    {
        $rules = [];

        // Build fast lookup for support/count
        $lookup = [];
        foreach ($frequentItemsets as $itemsetKey => $data) {
            $lookup[$itemsetKey] = $data;
        }

        foreach ($frequentItemsets as $itemsetKey => $data) {
            $items = $data['items'];
            $itemCount = count($items);

            if ($itemCount < 2) {
                continue;
            }

            $subsets = $this->nonEmptyProperSubsets($items);

            foreach ($subsets as $antecedent) {
                $consequent = array_values(array_diff($items, $antecedent));
                sort($antecedent);
                sort($consequent);

                if (empty($consequent)) {
                    continue;
                }

                $anteKey = $this->itemsetKey($antecedent);
                $consKey = $this->itemsetKey($consequent);

                if (!isset($lookup[$anteKey]) || !isset($lookup[$consKey])) {
                    continue;
                }

                $supportXY = $data['support'];
                $supportX  = $lookup[$anteKey]['support'];
                $supportY  = $lookup[$consKey]['support'];

                if ($supportX <= 0 || $supportY <= 0) {
                    continue;
                }

                $confidence = $supportXY / $supportX;
                if ($confidence < $minConfidence) {
                    continue;
                }

                $lift = $confidence / $supportY;

                $rules[] = [
                    'antecedent' => $antecedent,
                    'consequent' => $consequent,
                    'support' => round($supportXY, 6),
                    'confidence' => round($confidence, 6),
                    'lift' => round($lift, 6),
                    'antecedent_count' => $lookup[$anteKey]['count'],
                    'consequent_count' => $lookup[$consKey]['count'],
                    'both_count' => $data['count'],
                    'total_transactions' => $this->transactionCount,
                ];
            }
        }

        // De-duplicate rules by key
        $unique = [];
        foreach ($rules as $rule) {
            $key = $this->itemsetKey($rule['antecedent']) . '->' . $this->itemsetKey($rule['consequent']);
            $unique[$key] = $rule;
        }

        return array_values($unique);
    }

    protected function nonEmptyProperSubsets(array $items): array
    {
        $n = count($items);
        $subsets = [];

        // bitmask from 1 to 2^n - 2 (exclude empty and full set)
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

    protected function isSubset(array $subset, array $set): bool
    {
        return count(array_diff($subset, $set)) === 0;
    }

    protected function itemsetKey(array $items): string
    {
        sort($items);
        return implode('|', $items);
    }
}
