<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociationRule extends Model
{
    use HasFactory;

    protected $table = 'association_rules';

    protected $fillable = [
        'level',
        'antecedent',
        'consequent',
        'antecedent_size',
        'support',
        'confidence',
        'lift',
        'leverage',
        'conviction',
        'antecedent_count',
        'consequent_count',
        'both_count',
        'total_transactions',
        'min_support',
        'min_confidence',
    ];

    protected $casts = [
        'antecedent' => 'array',
        'consequent' => 'array',
        'support' => 'double',
        'confidence' => 'double',
        'lift' => 'double',
        'leverage' => 'double',
        'conviction' => 'double',
    ];

    public function scopePositiveCorrelation($query)
    {
        return $query->where('lift', '>', 1.0);
    }

    public function scopeStrongRules($query, $minConfidence = 0.5)
    {
        return $query->where('confidence', '>=', $minConfidence);
    }

    public function scopeProductLevel($query)
    {
        return $query->where('level', 'product');
    }
}
