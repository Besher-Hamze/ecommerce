<?php

namespace App\Services\Recommendation;

/**
 * Content representation of products: TF-IDF vectors (sublinear tf, smoothed idf,
 * L2-normalised) over name, brand, type, texture, tags and description.
 * Cosine similarity is computed through an inverted index, so only products that
 * share at least one term are compared.
 */
class TfIdf
{
    private const STOPWORDS = [
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from', 'has', 'have', 'in', 'is', 'it', 'its',
        'of', 'on', 'or', 'that', 'the', 'this', 'to', 'with', 'your', 'you', 'our', 'we', 'will', 'can', 'all',
        'any', 'more', 'most', 'into', 'up', 'out', 'so', 'than', 'too', 'very', 'just', 'also', 'not', 'no',
        'use', 'used', 'using', 'apply', 'each', 'every', 'which', 'while', 'without', 'after', 'over', 'new',
        'one', 'two', 'make', 'makes', 'give', 'gives', 'look', 'skin', 'product', 'products', 'formula', 'color',
        'colour', 'colors', 'colours', 'shade', 'shades', 'its', 'them', 'their', 'they', 'what', 'how', 'when',
    ];

    /** @var array<int, array<string, float>> product id => term => weight */
    private array $vectors = [];
    /** @var array<string, array<int, float>> term => product id => weight */
    private array $index = [];

    /**
     * @param array<int, array{name:string, brand:string, type:string, form:?string, tags:array, description:string}> $documents
     */
    public function fit(array $documents): self
    {
        $tf = [];
        $df = [];
        foreach ($documents as $id => $doc) {
            $terms = $this->terms($doc);
            $counts = array_count_values($terms);
            $tf[$id] = $counts;
            foreach (array_keys($counts) as $term) {
                $df[$term] = ($df[$term] ?? 0) + 1;
            }
        }

        $n = count($documents);
        foreach ($tf as $id => $counts) {
            $vector = [];
            $norm = 0.0;
            foreach ($counts as $term => $count) {
                if ($df[$term] < 2 && !str_contains($term, ':')) {
                    continue; // hapax legomena add noise, structured tokens are kept
                }
                $w = (1 + log($count)) * (log((1 + $n) / (1 + $df[$term])) + 1);
                $vector[$term] = $w;
                $norm += $w * $w;
            }
            $norm = sqrt($norm) ?: 1.0;
            foreach ($vector as $term => $w) {
                $vector[$term] = $w / $norm;
                $this->index[$term][$id] = $vector[$term];
            }
            $this->vectors[$id] = $vector;
        }

        return $this;
    }

    public function similarity(int $a, int $b): float
    {
        $va = $this->vectors[$a] ?? [];
        $vb = $this->vectors[$b] ?? [];
        if (count($va) > count($vb)) {
            [$va, $vb] = [$vb, $va];
        }
        $dot = 0.0;
        foreach ($va as $term => $w) {
            if (isset($vb[$term])) {
                $dot += $w * $vb[$term];
            }
        }

        return $dot;
    }

    /**
     * Top-k most similar documents for every document.
     *
     * @return array<int, array<int, float>> id => [neighbour id => cosine]
     */
    public function neighbours(int $k = 20, float $minSimilarity = 0.05): array
    {
        $result = [];
        foreach ($this->vectors as $id => $vector) {
            $scores = [];
            foreach ($vector as $term => $w) {
                foreach ($this->index[$term] as $other => $ow) {
                    if ($other !== $id) {
                        $scores[$other] = ($scores[$other] ?? 0) + $w * $ow;
                    }
                }
            }
            arsort($scores);
            $result[$id] = array_filter(array_slice($scores, 0, $k, true), fn ($s) => $s >= $minSimilarity);
        }

        return $result;
    }

    /**
     * Scores for a weighted profile of documents (content-based user profile).
     *
     * @param array<int, float> $profile id => weight
     * @return array<int, float>
     */
    public function scoreProfile(array $profile): array
    {
        $centroid = [];
        foreach ($profile as $id => $weight) {
            foreach ($this->vectors[$id] ?? [] as $term => $w) {
                $centroid[$term] = ($centroid[$term] ?? 0) + $weight * $w;
            }
        }
        $scores = [];
        foreach ($centroid as $term => $w) {
            foreach ($this->index[$term] as $other => $ow) {
                $scores[$other] = ($scores[$other] ?? 0) + $w * $ow;
            }
        }
        arsort($scores);

        return $scores;
    }

    /** @return string[] */
    private function terms(array $doc): array
    {
        $text = strtolower(($doc['name'] . ' ') . $doc['name'] . ' ' . ($doc['description'] ?? ''));
        $text = preg_replace('/[^a-z0-9\s]+/', ' ', $text);
        $terms = [];
        foreach (preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY) as $token) {
            if (strlen($token) < 3 || ctype_digit($token) || in_array($token, self::STOPWORDS, true)) {
                continue;
            }
            $terms[] = $this->stem($token);
        }

        // Structured attributes become dedicated tokens (repeated to raise their weight).
        $terms[] = 'brand:' . strtolower($doc['brand']);
        foreach ([1, 2] as $_) {
            $terms[] = 'type:' . $doc['type'];
        }
        if (!empty($doc['form'])) {
            $terms[] = 'form:' . $doc['form'];
        }
        foreach ($doc['tags'] ?? [] as $tag) {
            $terms[] = 'tag:' . str_replace(' ', '_', $tag);
        }

        return $terms;
    }

    private function stem(string $token): string
    {
        foreach (['ingly' => 4, 'ing' => 5, 'ies' => 4, 'es' => 5, 's' => 4] as $suffix => $minLength) {
            if (strlen($token) > $minLength && str_ends_with($token, $suffix)) {
                return $suffix === 'ies' ? substr($token, 0, -3) . 'y' : substr($token, 0, -strlen($suffix));
            }
        }

        return $token;
    }
}
