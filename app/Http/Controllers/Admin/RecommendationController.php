<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssociationRule;
use App\Models\Product;
use App\Models\ProductRecommendation;
use App\Models\RecommendationRun;
use App\Services\Recommendation\RecommendationTrainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    public function index(Request $request)
    {
        $latestTrain = RecommendationRun::where('kind', 'train')->latest()->first();
        $latestEval = RecommendationRun::where('kind', 'evaluate')->latest()->first();
        $defaults = RecommendationTrainer::DEFAULTS;

        // Form values: old input > forced defaults > last train params > code defaults
        if ($request->session()->pull('use_default_thresholds')) {
            $thresholds = $defaults;
        } else {
            $thresholds = array_merge(
                $defaults,
                is_array($latestTrain?->params) ? $latestTrain->params : [],
                array_filter($request->old(), fn ($v) => $v !== null && $v !== '')
            );
            // Keep only known keys
            $thresholds = array_intersect_key($thresholds, $defaults) + $defaults;
        }

        $distinct = fn (string $type) => (int) ProductRecommendation::where('type', $type)
            ->selectRaw('COUNT(DISTINCT product_id) as c')->value('c');
        $coverage = [
            'cross_sell' => $distinct(ProductRecommendation::CROSS_SELL),
            'upsell' => $distinct(ProductRecommendation::UPSELL),
            'similar' => $distinct(ProductRecommendation::SIMILAR),
            'cf' => $distinct(ProductRecommendation::CF),
        ];
        $productCount = max(1, Product::count());
        $coveragePct = collect($coverage)->map(fn ($n) => round(100 * $n / $productCount, 1));

        $rulesCount = [
            'product' => AssociationRule::where('level', 'product')->count(),
            'type' => AssociationRule::where('level', 'type')->count(),
        ];

        $events = DB::table('recommendation_events')
            ->select('source', 'event', DB::raw('COUNT(*) as n'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('source', 'event')
            ->get()
            ->groupBy('source')
            ->map(function ($rows) {
                $counts = $rows->pluck('n', 'event');
                $imp = (int) ($counts['impression'] ?? 0);
                $click = (int) ($counts['click'] ?? 0);
                $atc = (int) ($counts['add_to_cart'] ?? 0);
                $purchase = (int) ($counts['purchase'] ?? 0);

                return [
                    'impressions' => $imp,
                    'clicks' => $click,
                    'add_to_cart' => $atc,
                    'purchases' => $purchase,
                    'ctr' => $imp ? round(100 * $click / $imp, 2) : 0,
                    'conversion' => $click ? round(100 * $atc / $click, 2) : 0,
                ];
            });

        return view('dashboard.recommendations.index', compact(
            'latestTrain', 'latestEval', 'coverage', 'coveragePct', 'rulesCount',
            'events', 'productCount', 'defaults', 'thresholds'
        ));
    }

    public function train(Request $request)
    {
        $data = $request->validate([
            'min_support' => ['nullable', 'numeric', 'min:0.0001', 'max:1'],
            'min_confidence' => ['nullable', 'numeric', 'min:0.01', 'max:1'],
            'type_min_support' => ['nullable', 'numeric', 'min:0.0001', 'max:1'],
            'type_min_confidence' => ['nullable', 'numeric', 'min:0.01', 'max:1'],
            'max_itemset_size' => ['nullable', 'integer', 'min:2', 'max:5'],
            'top_k' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $args = array_filter([
            '--min-support' => $data['min_support'] ?? null,
            '--min-confidence' => $data['min_confidence'] ?? null,
            '--type-min-support' => $data['type_min_support'] ?? null,
            '--type-min-confidence' => $data['type_min_confidence'] ?? null,
            '--max-itemset-size' => $data['max_itemset_size'] ?? null,
            '--top-k' => $data['top_k'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        $exit = Artisan::call('recommendations:train', $args);
        $message = trim(Artisan::output()) ?: ($exit === 0 ? 'Training completed.' : 'Training failed.');

        return redirect()->route('admin.recommendations.index')
            ->with($exit === 0 ? 'success' : 'error', $message);
    }

    public function resetDefaults()
    {
        return redirect()->route('admin.recommendations.index')
            ->with('use_default_thresholds', true)
            ->with('success', 'تم استعادة القيم الافتراضية في النموذج. اضغطي Train لتطبيقها.');
    }

    public function trainDefaults()
    {
        $d = RecommendationTrainer::DEFAULTS;
        $exit = Artisan::call('recommendations:train', [
            '--min-support' => $d['min_support'],
            '--min-confidence' => $d['min_confidence'],
            '--type-min-support' => $d['type_min_support'],
            '--type-min-confidence' => $d['type_min_confidence'],
            '--max-itemset-size' => $d['max_itemset_size'],
            '--top-k' => $d['top_k'],
        ]);
        $message = trim(Artisan::output()) ?: ($exit === 0 ? 'Training with defaults completed.' : 'Training failed.');

        return redirect()->route('admin.recommendations.index')
            ->with($exit === 0 ? 'success' : 'error', $message)
            ->with('use_default_thresholds', true);
    }

    public function evaluate(Request $request)
    {
        $k = (int) $request->input('k', 5);
        $exit = Artisan::call('recommendations:evaluate', ['--k' => $k]);
        $message = trim(Artisan::output()) ?: ($exit === 0 ? 'Evaluation completed.' : 'Evaluation failed.');

        return redirect()->route('admin.recommendations.index')
            ->with($exit === 0 ? 'success' : 'error', $message);
    }

    public function rules(Request $request)
    {
        $level = $request->input('level', 'product');
        $q = AssociationRule::query()->where('level', $level)->positiveCorrelation()
            ->orderByDesc('lift')->orderByDesc('confidence');

        if ($request->filled('min_confidence')) {
            $q->where('confidence', '>=', (float) $request->input('min_confidence'));
        }
        if ($request->filled('min_lift')) {
            $q->where('lift', '>=', (float) $request->input('min_lift'));
        }

        $rules = $q->paginate(50)->appends($request->query());

        $ids = [];
        if ($level === 'product') {
            foreach ($rules as $rule) {
                foreach (array_merge($rule->antecedent ?? [], $rule->consequent ?? []) as $id) {
                    $ids[] = (int) $id;
                }
            }
        }
        $names = $ids
            ? Product::whereIn('id', array_unique($ids))->pluck('eng_name', 'id')
            : collect();

        return view('dashboard.recommendations.rules', compact('rules', 'level', 'names'));
    }
}
