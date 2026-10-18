<?php

namespace App\Services\Recommendation;

/**
 * Turns Apriori rules into a ranked "frequently bought together" list per product.
 *
 * Multi-level association rules (Han & Fu, 1995):
 *   1. product-level rules {P} => {Q} are used first (specific, high precision);
 *   2. (hybrid mode) the list is then filled with the item-based CF neighbours of P,
 *      which capture co-purchase *and* co-browsing behaviour of similar customers;
 *   3. when a product still has too few candidates (new or rarely bought items - the
 *      cold-start problem) the list is completed with product-type rules, e.g.
 *      {lipstick} => {lip_liner}; the concrete lip liner is chosen by
 *      co-purchase similarity with P, brand match and popularity.
 */
class CrossSellBuilder
{
    /**
     * @param array $productRules  rules on product ids
     * @param array $typeRules     rules on product types
     * @param array $products      product id => features (type, brand_id, sales)
     * @param array $cfNeighbours  product id => [product id => similarity]
     * @return array<int, array<int, array>> product id => [recommended id => meta]
     */
    public function build(array $productRules, array $typeRules, array $products, array $cfNeighbours, int $k = 8, bool $hybrid = false): array
    {
        $direct = [];
        foreach ($productRules as $rule) {
            if (count($rule['antecedent']) !== 1 || count($rule['consequent']) !== 1) {
                continue;
            }
            [$p] = $rule['antecedent'];
            [$q] = $rule['consequent'];
            if (!isset($direct[$p][$q]) || $direct[$p][$q]['score'] < $rule['score']) {
                $direct[$p][$q] = [
                    'score' => $rule['score'], 'level' => 'product', 'confidence' => $rule['confidence'],
                    'lift' => $rule['lift'], 'support' => $rule['support'],
                ];
            }
        }

        $typeConsequents = [];
        foreach ($typeRules as $rule) {
            if (count($rule['antecedent']) === 1 && count($rule['consequent']) === 1) {
                $typeConsequents[$rule['antecedent'][0]][$rule['consequent'][0]] = $rule;
            }
        }
        $byType = [];
        $maxSales = 1;
        foreach ($products as $id => $p) {
            $byType[$p['type']][] = $id;
            $maxSales = max($maxSales, $p['sales']);
        }

        $result = [];
        foreach ($products as $id => $p) {
            $list = $direct[$id] ?? [];
            uasort($list, fn ($a, $b) => $b['score'] <=> $a['score']);
            $list = array_slice($list, 0, $k, true);

            if ($hybrid && count($list) < $k) {
                foreach ($cfNeighbours[$id] ?? [] as $cid => $sim) {
                    if (count($list) >= $k) {
                        break;
                    }
                    if (!isset($list[$cid])) {
                        $list[$cid] = ['score' => round(0.5 * $sim, 6), 'level' => 'cf', 'similarity' => round($sim, 4)];
                    }
                }
            }

            if (count($list) < $k) {
                $fallback = [];
                foreach ($typeConsequents[$p['type']] ?? [] as $type => $rule) {
                    $candidates = [];
                    foreach ($byType[$type] ?? [] as $cid) {
                        if ($cid === $id || isset($list[$cid])) {
                            continue;
                        }
                        $c = $products[$cid];
                        $candidates[$cid] = 0.5 * min(1, ($cfNeighbours[$id][$cid] ?? 0) * 2)
                            + 0.3 * ($c['brand_id'] === $p['brand_id'] ? 1 : 0)
                            + 0.2 * log(1 + $c['sales']) / log(1 + $maxSales);
                    }
                    arsort($candidates);
                    foreach (array_slice($candidates, 0, 3, true) as $cid => $fit) {
                        $fallback[$cid] = [
                            'score' => round($rule['score'] * (0.4 + 0.4 * $fit), 6), 'level' => 'type',
                            'confidence' => $rule['confidence'], 'lift' => $rule['lift'], 'support' => $rule['support'],
                            'rule' => $p['type'] . ' => ' . $type,
                        ];
                    }
                }
                uasort($fallback, fn ($a, $b) => $b['score'] <=> $a['score']);
                $list += array_slice($fallback, 0, $k - count($list), true);
            }

            $result[$id] = $list;
        }

        return $result;
    }
}
