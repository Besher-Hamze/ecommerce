<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociationRule extends Model
{
    use HasFactory;

    protected $table = 'association_rules';

    protected $fillable = [
        'antecedent',
        'consequent',
        'support',
        'confidence',
        'lift',
        'antecedent_count',
        'consequent_count',
        'both_count',
        'total_transactions',
        'min_support',
        'min_confidence',
    ];

    protected $casts = [
        'antecedent'  => 'array',
        'consequent'  => 'array',
        'support'     => 'double',
        'confidence'  => 'double',
        'lift'        => 'double',
    ];

    /**
     * Scope: filter rules with lift > 1 (positive correlation)
     */
    public function scopePositiveCorrelation($query)
    {
        return $query->where('lift', '>', 1.0);
    }

    /**
     * Scope: filter strong rules (high confidence)
     */
    public function scopeStrongRules($query, $minConfidence = 0.5)
    {
        return $query->where('confidence', '>=', $minConfidence);
    }

    /**
     * Get recommendations for a given product name
     */
    public static function getRecommendationsFor(string $productName, int $limit = 5)
    {
        return static::where('antecedent', 'LIKE', '%"' . $productName . '"%')
            ->where('lift', '>', 1.0)
            ->orderByDesc('lift')
            ->limit($limit)
            ->get();
    }
}
