@extends('dashboard.master')

@section('content')
<section id="main-content" dir="rtl">
    <section class="wrapper site-min-height">
        <section class="panel">
            <header class="panel-heading">
                Apriori Association Rules Analysis
            </header>

            <div class="panel-body">

                {{-- Statistics row --}}
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h6>Total Carts</h6>
                                <h3 id="totalCarts">{{ $totalCarts }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h6>Total Items</h6>
                                <h3 id="totalItems">{{ $totalItems }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h6>Avg Items/Cart</h6>
                                <h3 id="avgItems">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h6>Unique Products</h6>
                                <h3 id="uniqueProducts">0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Generate Association Rules form --}}
                <h4 style="margin-bottom: 15px;">Generate Association Rules</h4>

                <form id="aprioriForm" class="row" style="margin-bottom: 20px;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="support">Minimum Support (%)</label>
                            <input type="number" id="support" name="support" class="form-control"
                                value="20" min="1" max="100" step="1">
                            <small class="help-block">Items must appear in at least this % of carts</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confidence">Minimum Confidence (%)</label>
                            <input type="number" id="confidence" name="confidence" class="form-control"
                                value="50" min="1" max="100" step="1">
                            <small class="help-block">If A is bought, probability of buying B</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" onclick="generateRules()">
                            Generate Rules
                        </button>
                    </div>
                </form>

                <hr>

                {{-- Results section --}}
                <h4 style="margin-bottom: 15px;">Association Rules Results</h4>

                <div id="loadingSpinner" class="text-center" style="display:none; padding: 20px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p style="margin-top: 10px;">Generating rules...</p>
                </div>

                <div id="resultsContainer" style="display:none;">
                    <div class="alert alert-info">
                        Total Transactions: <strong id="transactionCount">0</strong> |
                        Total Rules: <strong id="rulesCount">0</strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>If Customer Buys</th>
                                    <th>Then Buys</th>
                                    <th>Confidence</th>
                                    <th>Support</th>
                                    <th>Lift</th>
                                </tr>
                            </thead>
                            <tbody id="rulesTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div id="errorContainer" class="alert alert-danger" style="display:none;"></div>
                <div id="noDataContainer" class="alert alert-warning" style="display:none;"></div>

            </div>
        </section>
    </section>
</section>

<script>
    function generateRules() {
        const support = document.getElementById('support').value / 100;
        const confidence = document.getElementById('confidence').value / 100;

        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('resultsContainer').style.display = 'none';
        document.getElementById('errorContainer').style.display = 'none';
        document.getElementById('noDataContainer').style.display = 'none';

        fetch("{{ route('apriori.rules') }}?support=" + support + "&confidence=" + confidence)
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingSpinner').style.display = 'none';

                if (data.success) {
                    displayRules(data);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                document.getElementById('loadingSpinner').style.display = 'none';
                showError('Error: ' + error.message);
            });
    }

    function displayRules(data) {
        if (data.total_rules === 0) {
            document.getElementById('noDataContainer').style.display = 'block';
            document.getElementById('noDataContainer').textContent =
                'No association rules found with the selected parameters. Try lowering support or confidence values.';
            return;
        }

        document.getElementById('transactionCount').textContent = data.total_transactions;
        document.getElementById('rulesCount').textContent = data.total_rules;

        const tableBody = document.getElementById('rulesTableBody');
        tableBody.innerHTML = '';

        data.rules.forEach(rule => {
            const ifBuys = rule.If_buys.map(p => `<strong>${p.name}</strong><br/><small>${p.price}</small>`).join('<br/>');
            const thenBuys = rule.Then_buys.map(p => `<strong>${p.name}</strong><br/><small>${p.price}</small>`).join('<br/>');

            const row = `
            <tr>
                <td>${ifBuys}</td>
                <td>${thenBuys}</td>
                <td><span class="label label-success">${rule.Confidence}</span></td>
                <td><span class="label label-info">${rule.Support}</span></td>
                <td>${rule.Lift}</td>
            </tr>
        `;
            tableBody.innerHTML += row;
        });

        document.getElementById('resultsContainer').style.display = 'block';
    }

    function showError(message) {
        document.getElementById('errorContainer').textContent = message;
        document.getElementById('errorContainer').style.display = 'block';
    }

    window.addEventListener('load', function() {
        fetch("{{ route('apriori.statistics') }}")
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalCarts').textContent = data.total_carts;
                document.getElementById('totalItems').textContent = data.total_cart_items;
                document.getElementById('avgItems').textContent = data.average_items_per_cart.toFixed(2);
                document.getElementById('uniqueProducts').textContent = data.unique_products;
            });
    });
</script>
@endsection
