<?php

namespace App\Services\Recommendation;

/**
 * Upselling: recommend a *better, slightly more expensive substitute* of the
 * product the customer is looking at.
 *
 * Hard constraints (a candidate must be a genuine upgrade):
 *   - same product type (a substitute, not a complement),
 *   - price between (1 + minUplift) and maxRatio times the current price,
 *   - Bayesian-average rating not lower than the current product's.
 *
 * Multi-criteria utility (weighted sum, each criterion normalised to [0, 1]):
 *   score = 0.40·similarity + 0.25·qualityGain + 0.20·priceFit + 0.15·popularity
 *     similarity  - TF-IDF cosine (keeps the upgrade relevant to the customer's intent)
 *     qualityGain - (bayes(C) - bayes(P)) / 1 star, capped at 1
 *     priceFit    - Gaussian around the ideal uplift ratio (default +35%): larger jumps
 *                   are accepted less often by shoppers
 *     popularity  - log(1 + sales) normalised by the best seller (social proof)
 *
 * Bayesian average (IMDb formula): (v·R + m·C) / (v + m) with v = number of reviews,
 * R = mean stars, C = catalogue mean, m = prior weight. It prevents a product with a
 * single 5-star review from outranking one with 80 reviews averaging 4.6.
 */
class UpsellRanker
{
    public function __construct(
        private TfIdf $content,
        private float $minUplift = 0.05,
        private float $maxRatio = 2.0,
        private float $idealRatio = 1.35,
        private float $priorWeight = 5.0,
        private array $weights = ['similarity' => 0.40, 'quality' => 0.25, 'price' => 0.20, 'popularity' => 0.15],
    ) {
    }

    /**
     * @param array<int, array{type:string, price:float, rating_avg:float, rating_count:int, sales:int}> $products
     * @return array<int, array<int, array>> product id => ranked upsell candidates
     */
    public function rank(array $products, int $k = 4): array
    {
        $rated = array_filter($products, fn ($p) => $p['rating_count'] > 0);
        $globalMean = $rated
            ? array_sum(array_map(fn ($p) => $p['rating_avg'] * $p['rating_count'], $rated)) / array_sum(array_column($rated, 'rating_count'))
            : 4.0;
        $maxSales = max(1, max(array_column($products, 'sales')));

        $byType = [];
        $bayes = [];
        foreach ($products as $id => $p) {
            $byType[$p['type']][] = $id;
            $bayes[$id] = ($p['rating_count'] * $p['rating_avg'] + $this->priorWeight * $globalMean) / ($p['rating_count'] + $this->priorWeight);
        }

        $result = [];
        foreach ($products as $id => $p) {
            $candidates = [];
            foreach ($byType[$p['type']] as $cid) {
                if ($cid === $id) {
                    continue;
                }
                $c = $products[$cid];
                $ratio = $c['price'] / max($p['price'], 0.01);
                if ($ratio < 1 + $this->minUplift || $ratio > $this->maxRatio || $bayes[$cid] < $bayes[$id]) {
                    continue;
                }

                $similarity = $this->content->similarity($id, $cid);
                $quality = min(1.0, ($bayes[$cid] - $bayes[$id]) / 1.0);
                $priceFit = exp(-(($ratio - $this->idealRatio) ** 2) / (2 * 0.3 ** 2));
                $popularity = log(1 + $c['sales']) / log(1 + $maxSales);

                $score = $this->weights['similarity'] * $similarity
                    + $this->weights['quality'] * $quality
                    + $this->weights['price'] * $priceFit
                    + $this->weights['popularity'] * $popularity;

                $candidates[$cid] = [
                    'score' => round($score, 6),
                    'similarity' => round($similarity, 4),
                    'rating_from' => round($bayes[$id], 2),
                    'rating_to' => round($bayes[$cid], 2),
                    'price_from' => $p['price'],
                    'price_to' => $c['price'],
                    'uplift_pct' => round(($ratio - 1) * 100, 1),
                    'components' => [
                        'similarity' => round($similarity, 3), 'quality' => round($quality, 3),
                        'price_fit' => round($priceFit, 3), 'popularity' => round($popularity, 3),
                    ],
                ];
            }
            uasort($candidates, fn ($a, $b) => $b['score'] <=> $a['score']);
            $result[$id] = array_slice($candidates, 0, $k, true);
        }

        return $result;
    }
}
