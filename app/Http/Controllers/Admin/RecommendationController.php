<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssociationRule;
use App\Models\Product;
use App\Models\ProductRecommendation;
use App\Models\RecommendationRun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    public function index()
    {
        $latestTrain = RecommendationRun::where('kind', 'train')->latest()->first();
        $latestEval = RecommendationRun::where('kind', 'evaluate')->latest()->first();

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
            'latestTrain', 'latestEval', 'coverage', 'coveragePct', 'rulesCount', 'events', 'productCount'
        ));
    }

    public function train(Request $request)
    {
        $params = array_filter([
            'min_support' => $request->input('min_support'),
            'min_confidence' => $request->input('min_confidence'),
        ], fn ($v) => $v !== null && $v !== '');

        $exit = Artisan::call('recommendations:train', array_filter([
            '--min-support' => $params['min_support'] ?? null,
            '--min-confidence' => $params['min_confidence'] ?? null,
        ]));

        $message = trim(Artisan::output()) ?: ($exit === 0 ? 'Training completed.' : 'Training failed.');

        return redirect()->route('admin.recommendations.index')
            ->with($exit === 0 ? 'success' : 'error', $message);
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
