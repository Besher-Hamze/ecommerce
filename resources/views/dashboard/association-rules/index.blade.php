@extends('dashboard.master')

@section('content')
<section id="main-content" dir="rtl">
    <section class="wrapper site-min-height">
        <section class="panel">
            <header class="panel-heading">
                تحليل سلة المشتريات باستخدام Apriori - منتجات التجميل
            </header>

            <div class="panel-body">
                <form method="GET" action="{{ route('admin.association-rules.index') }}" class="row" style="margin-bottom: 20px;">
                    <input type="hidden" name="run" value="1">

                    <div class="col-md-3">
                        <label>Min Support</label>
                        <input type="number" class="form-control" step="0.001" min="0.001" max="1" name="min_support" value="{{ $minSupport }}">
                    </div>
                    <div class="col-md-3">
                        <label>Min Confidence</label>
                        <input type="number" class="form-control" step="0.001" min="0.001" max="1" name="min_confidence" value="{{ $minConfidence }}">
                    </div>
                    <div class="col-md-3">
                        <label>Max Itemset Size</label>
                        <input type="number" class="form-control" min="2" max="5" name="max_itemset_size" value="{{ $maxItemsetSize }}">
                    </div>
                    <div class="col-md-3" style="margin-top:25px;">
                        <button class="btn btn-primary" type="submit">تشغيل التحليل</button>
                    </div>
                </form>

                <hr>

                <div class="row" style="margin: 20px 0;">
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h4>Transactions</h4>
                                <h2>{{ $summary['transactions_count'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h4>Unique Products</h4>
                                <h2>{{ $summary['unique_products_count'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h4>Discovered Rules</h4>
                                <h2>{{ $summary['rules_count'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <h4>Top Frequent Cosmetic Products</h4>
                <div class="table-responsive" style="margin-bottom:20px;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Frequency</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary['top_products'] as $product => $freq)
                            <tr>
                                <td>{{ $product }}</td>
                                <td>{{ $freq }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h4>Association Rules</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped" id="editable-sample">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Rule</th>
                                <th>Support</th>
                                <th>Confidence</th>
                                <th>Lift</th>
                                <th>Insight</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rules as $index => $rule)
                            @php
                            $antecedent = is_array($rule['antecedent']) ? $rule['antecedent'] : (array) $rule['antecedent'];
                            $consequent = is_array($rule['consequent']) ? $rule['consequent'] : (array) $rule['consequent'];
                            $support = $rule['support'];
                            $confidence = $rule['confidence'];
                            $lift = $rule['lift'];
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ implode(', ', $antecedent) }}</strong>
                                    &nbsp;⇒&nbsp;
                                    <strong>{{ implode(', ', $consequent) }}</strong>
                                </td>
                                <td>{{ number_format($support, 4) }}</td>
                                <td>{{ number_format($confidence, 4) }}</td>
                                <td>
                                    <span class="label {{ $lift > 1 ? 'label-success' : 'label-default' }}">
                                        {{ number_format($lift, 4) }}
                                    </span>
                                </td>
                                <td>
                                    @if($lift > 1.2 && $confidence > 0.5)
                                    Strong bundle recommendation.
                                    @elseif($lift > 1)
                                    Positive cross-sell relation.
                                    @else
                                    Weak relation.
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="alert alert-success">
                    <h4>Business Insights for Cosmetic E-commerce</h4>
                    <ul>
                        <li>Use high-lift rules in "Frequently Bought Together" widgets.</li>
                        <li>Create bundle discounts for high-confidence pairs (e.g., lipstick + lip liner).</li>
                        <li>Use category-aware recommendations (foundation + primer, cleanser + toner).</li>
                        <li>Apply rule filtering by season/campaign to increase conversion.</li>
                    </ul>
                </div>
            </div>
        </section>
    </section>
</section>
@endsection