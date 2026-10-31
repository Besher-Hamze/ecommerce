@extends('dashboard.master')

@section('content')
@php
    $labels = [
        'min_support' => ['Support للمنتجات', 'أقل نسبة ظهور للمنتجات معاً (مثال 0.003 = 0.3%)'],
        'min_confidence' => ['Confidence للمنتجات', 'أقل ثقة للقاعدة على مستوى المنتج'],
        'type_min_support' => ['Support للأنواع', 'أقل دعم لقواعد النوع (lipstick → lip_liner)'],
        'type_min_confidence' => ['Confidence للأنواع', 'أقل ثقة لقواعد النوع'],
        'max_itemset_size' => ['حجم المجموعة', 'أكبر عدد منتجات بمجموعة متكررة (2–5)'],
        'top_k' => ['Top-K', 'عدد الاقتراحات المحفوظة لكل منتج'],
    ];
    $steps = [
        'min_support' => '0.001',
        'min_confidence' => '0.01',
        'type_min_support' => '0.001',
        'type_min_confidence' => '0.01',
        'max_itemset_size' => '1',
        'top_k' => '1',
    ];
@endphp
<section id="main-content" dir="rtl">
    <section class="wrapper site-min-height">
        <section class="panel">
            <header class="panel-heading">
                محرك التوصيات — Recommendation Engine
            </header>
            <div class="panel-body">
                @if(session('success'))
                    <div class="alert alert-success" style="white-space:pre-wrap">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="white-space:pre-wrap">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-info">
                            <div class="panel-body text-center">
                                <h5>Cross-sell coverage</h5>
                                <h2>{{ $coveragePct['cross_sell'] }}%</h2>
                                <small>{{ $coverage['cross_sell'] }} / {{ $productCount }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-success">
                            <div class="panel-body text-center">
                                <h5>Upsell coverage</h5>
                                <h2>{{ $coveragePct['upsell'] }}%</h2>
                                <small>{{ $coverage['upsell'] }} / {{ $productCount }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-warning">
                            <div class="panel-body text-center">
                                <h5>CF coverage</h5>
                                <h2>{{ $coveragePct['cf'] }}%</h2>
                                <small>{{ $coverage['cf'] }} / {{ $productCount }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h5>Association rules</h5>
                                <h2>{{ $rulesCount['product'] + $rulesCount['type'] }}</h2>
                                <small>{{ $rulesCount['product'] }} product · {{ $rulesCount['type'] }} type</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h4>عتبات التدريب (Thresholds)</h4>
                <p class="text-muted">
                    القيم الحالية معبّأة من
                    @if($latestTrain && is_array($latestTrain->params))
                        <strong>آخر تدريب</strong> ({{ $latestTrain->created_at }})
                    @else
                        <strong>الافتراضي بالكود</strong>
                    @endif
                    — غيّريها ثم اضغطي Train.
                </p>

                <form method="POST" action="{{ route('admin.recommendations.train') }}" id="trainForm">
                    @csrf
                    <div class="row">
                        @foreach($labels as $key => [$title, $hint])
                            <div class="col-md-4" style="margin-bottom:15px">
                                <label><strong>{{ $title }}</strong></label>
                                <input type="number"
                                       name="{{ $key }}"
                                       id="th_{{ $key }}"
                                       class="form-control threshold-input"
                                       step="{{ $steps[$key] }}"
                                       value="{{ old($key, $thresholds[$key] ?? $defaults[$key]) }}"
                                       data-default="{{ $defaults[$key] }}"
                                       required>
                                <small class="text-muted">{{ $hint }} · افتراضي: <code>{{ $defaults[$key] }}</code></small>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top:10px">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="fa fa-play"></i> Train الآن بهذه القيم
                        </button>
                        <button class="btn btn-default btn-lg" type="button" id="fillDefaultsBtn">
                            <i class="fa fa-undo"></i> املأ القيم الافتراضية
                        </button>
                    </div>
                </form>

                <div style="margin-top:12px">
                    <form method="POST" action="{{ route('admin.recommendations.reset') }}" style="display:inline-block;margin-left:8px">
                        @csrf
                        <button class="btn btn-warning" type="submit" title="يعيد تعبئة النموذج فقط بدون تدريب">
                            استعادة الافتراضي بالنموذج
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.recommendations.train-defaults') }}" style="display:inline-block"
                          onsubmit="return confirm('تدريب بالقيم الافتراضية الآن؟');">
                        @csrf
                        <button class="btn btn-success" type="submit">
                            Train بالقيم الافتراضية مباشرة
                        </button>
                    </form>
                </div>

                @if($latestTrain && is_array($latestTrain->params))
                    <div class="alert alert-info" style="margin-top:15px;direction:ltr;text-align:left">
                        <strong>Last trained params:</strong>
                        <code>{{ json_encode($latestTrain->params) }}</code>
                    </div>
                @endif

                <hr>
                <h4>التقييم (Evaluate)</h4>
                <form method="POST" action="{{ route('admin.recommendations.evaluate') }}" class="form-inline">
                    @csrf
                    <div class="form-group" style="margin-left:10px">
                        <label>K</label>
                        <input type="number" name="k" value="5" class="form-control" style="width:80px" min="1" max="20">
                    </div>
                    <button class="btn btn-info" type="submit" style="margin-right:10px">Evaluate</button>
                </form>

                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h4>آخر تدريب</h4>
                        @if($latestTrain)
                            <p><strong>Status:</strong> {{ $latestTrain->status }} · {{ $latestTrain->duration_ms }} ms</p>
                            <p><strong>At:</strong> {{ $latestTrain->created_at }}</p>
                            <pre style="max-height:220px;overflow:auto;background:#f7f7f7;padding:10px;direction:ltr;text-align:left">{{ json_encode($latestTrain->stats, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="text-muted">لا يوجد تدريب بعد. شغّل Train.</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h4>آخر تقييم (offline)</h4>
                        @if($latestEval)
                            <p><strong>At:</strong> {{ $latestEval->created_at }} · {{ $latestEval->duration_ms }} ms</p>
                            <pre style="max-height:220px;overflow:auto;background:#f7f7f7;padding:10px;direction:ltr;text-align:left">{{ json_encode($latestEval->stats, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="text-muted">لا يوجد تقييم بعد.</p>
                        @endif
                    </div>
                </div>

                <hr>
                <h4>أداء الأونلاين (آخر 30 يوم)</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="direction:ltr;text-align:left">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Impressions</th>
                                <th>Clicks</th>
                                <th>CTR %</th>
                                <th>Add to cart</th>
                                <th>Conv %</th>
                                <th>Purchases</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $source => $row)
                                <tr>
                                    <td>{{ $source }}</td>
                                    <td>{{ $row['impressions'] }}</td>
                                    <td>{{ $row['clicks'] }}</td>
                                    <td>{{ $row['ctr'] }}</td>
                                    <td>{{ $row['add_to_cart'] }}</td>
                                    <td>{{ $row['conversion'] }}</td>
                                    <td>{{ $row['purchases'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7">No events yet — browse the storefront to generate impressions.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p>
                    <a href="{{ route('admin.association-rules.index') }}" class="btn btn-default">استكشاف قواعد Apriori</a>
                </p>
            </div>
        </section>
    </section>
</section>
<script>
document.getElementById('fillDefaultsBtn')?.addEventListener('click', function () {
    document.querySelectorAll('.threshold-input').forEach(function (el) {
        el.value = el.getAttribute('data-default');
    });
});
</script>
@endsection
