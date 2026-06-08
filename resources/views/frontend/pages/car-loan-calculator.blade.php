@extends('layouts.frontend.app')

@section('title', __('Car Loan Calculator') . ' | ' . __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/about.css',
    ])
@endpush

@section('page-content')
    <div class="d-block h-63 d-xl-none" id="mobile-nav-spacer"></div>

    <div class="page-template" role="main">
        <header class="sc-5a5d3415-0 jHTnHg" id="interior-page-header"
            title="Car loan calculator">
            <div class="position-relative container">
                <div>
                    <h1 class="m-0 text-white py-3 text-center" id="page_h1">Car, truck, &amp; SUV loan calculator</h1>
                </div>
            </div>
        </header>

        <div class="bg-white py-4 py-lg-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">

                        {{-- Introduction --}}
                        <div class="mb-4">
                            <h2 class="h3" style="color: #166B87;">Plan your car payments with ease</h2>
                            <p class="text-muted">
                                Quickly estimate your monthly payments and plan your next drive with confidence.
                                Our simple, no-sign-up loan calculator lets you test different vehicle prices and
                                terms right from your phone or computer.
                            </p>
                        </div>

                        {{-- Calculator --}}
                        <div class="row g-4">
                            {{-- Left: Inputs --}}
                            <div class="col-md-7">
                                <div class="border rounded-3 p-4 h-100" style="border-color: #166B87 !important;">
                                    <h4 class="mb-3" style="color: #166B87;">Loan details</h4>

                                    {{-- Credit score --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Credit score: <span id="clc-score-val">740</span></label>
                                        <input type="range" class="form-range" id="clc-score" min="400" max="850" value="740">
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>400</span>
                                            <span>850</span>
                                        </div>
                                    </div>

                                    {{-- Unit price --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Unit price</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">$</span>
                                            <input type="text" class="form-control clc-input" id="clc-price"
                                                value="30,000" inputmode="numeric">
                                        </div>
                                    </div>

                                    {{-- Term --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Term (months)</label>
                                        <select class="form-select" id="clc-term">
                                            <option value="36">36 months</option>
                                            <option value="48">48 months</option>
                                            <option value="60" selected>60 months</option>
                                            <option value="72">72 months</option>
                                            <option value="84">84 months</option>
                                        </select>
                                    </div>

                                    {{-- APR --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Annual Percentage Rate (APR)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control clc-input" id="clc-apr"
                                                value="6.79" inputmode="decimal">
                                            <span class="input-group-text bg-light">%</span>
                                        </div>
                                    </div>

                                    {{-- Down payment --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Down payment</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">$</span>
                                            <input type="text" class="form-control clc-input" id="clc-down"
                                                value="3,000" inputmode="numeric">
                                        </div>
                                    </div>

                                    {{-- Trade-in value --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Trade-in value</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">$</span>
                                            <input type="text" class="form-control clc-input" id="clc-trade"
                                                value="0" inputmode="numeric">
                                        </div>
                                    </div>

                                    {{-- Remaining loan balance --}}
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">Remaining loan balance</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">$</span>
                                            <input type="text" class="form-control clc-input" id="clc-balance"
                                                value="0" inputmode="numeric">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right: Results --}}
                            <div class="col-md-5">
                                <div class="border rounded-3 p-4 text-center h-100 d-flex flex-column justify-content-center"
                                    style="border-color: #166B87 !important; background: #f8fafc;">
                                    <p class="text-muted mb-1">Estimated monthly payment</p>
                                    <div class="display-4 fw-bold mb-1" style="color: #166B87;" id="clc-monthly">
                                        $0
                                    </div>
                                    <small class="text-muted" id="clc-terms-display">Est. payment for 60 months at 6.79% APR</small>

                                    <hr class="my-3">

                                    <div class="text-start small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Vehicle price</span>
                                            <span id="clc-summary-price">$30,000</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Down payment</span>
                                            <span id="clc-summary-down">-$3,000</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Trade-in</span>
                                            <span id="clc-summary-trade">-$0</span>
                                        </div>
                                        <div class="d-flex justify-content-between fw-bold border-top pt-1">
                                            <span>Amount financed</span>
                                            <span id="clc-summary-financed">$27,000</span>
                                        </div>
                                    </div>

                                    <a href="{{ route('frontend.get-approved') }}" class="btn btn-primary mt-2 w-100"
                                        style="background-color: #166B87; border-color: #166B87;">
                                        Get approved &rsaquo;
                                    </a>

                                    <a href="{{ route('frontend.inventory') }}" id="clc-browse-btn"
                                        class="btn btn-outline-primary mt-2 w-100"
                                        style="border-color: #166B87; color: #166B87;">
                                        Browse vehicles under $0
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Info text --}}
                        <div class="mt-4">
                            <p class="text-muted">
                                To determine the appropriate payment plan for your budget, you may customize the
                                following parameters: loan duration (in months), vehicle cost, Annual Percentage
                                Rate (APR), down payment / deposit percentage, and credit score.
                            </p>
                            <p class="text-muted">
                                <strong>Plan the payment that fits your life:</strong> Adjust the numbers as many
                                times as needed to find the perfect balance between affordable monthly payments
                                and overall savings. Whether you are considering a reliable commuter, a family
                                SUV, or a capable truck, this tool helps you shop smarter. Ready for personalized
                                numbers?
                                <a href="{{ route('frontend.get-approved') }}" style="color: #166B87;">Apply for pre-approval online</a>
                                or stop by to speak with our finance team.
                            </p>
                        </div>

                        {{-- Disclosures --}}
                        <div class="mt-4 p-3 bg-light rounded-3 small text-muted">
                            <h6 class="fw-bold">Finance disclosures</h6>
                            <p>
                                The payment estimator is not an advertisement or offer for specific terms of
                                credit and actual terms may vary. Payment amounts presented are for illustrative
                                purposes only and may not be available. Not all models are available in all
                                states. Actual vehicle price may vary by Dealer.
                            </p>
                            <p class="mb-0">
                                The Estimated Monthly Payment amount calculated is based on the variables
                                entered, the price of the vehicle you entered, the term you select, the down
                                payment you enter, the Annual Percentage Rate (APR) you select, and any net
                                trade-in amount. The payment estimate displayed does not include taxes, title,
                                license and/or registration fees. Payment amount is for illustrative purposes
                                only. Actual prices may vary by Dealer. Payment amounts may be different due to
                                various factors such as fees, specials, rebates, term, down payment, APR, net
                                trade-in, and applicable tax rate. Actual APR is based on available finance
                                programs and the creditworthiness of the customer. Not all customers will qualify
                                for credit or for the lowest rate. Please contact an authorized dealer for actual
                                rates, program details and actual terms.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.partials.dealership-info')
@endsection

@push('page-scripts')
    <script>
        (function () {
            var fmt = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });

            function num(v) {
                return Number(String(v).replace(/[^0-9.-]/g, '')) || 0;
            }

            function money(v) {
                return fmt.format(Math.max(0, Math.round(num(v))));
            }

            var priceEl = document.getElementById('clc-price');
            var downEl = document.getElementById('clc-down');
            var tradeEl = document.getElementById('clc-trade');
            var balanceEl = document.getElementById('clc-balance');
            var termEl = document.getElementById('clc-term');
            var aprEl = document.getElementById('clc-apr');
            var scoreEl = document.getElementById('clc-score');
            var scoreValEl = document.getElementById('clc-score-val');
            var monthlyEl = document.getElementById('clc-monthly');
            var termsDisplayEl = document.getElementById('clc-terms-display');
            var summaryPrice = document.getElementById('clc-summary-price');
            var summaryDown = document.getElementById('clc-summary-down');
            var summaryTrade = document.getElementById('clc-summary-trade');
            var summaryFinanced = document.getElementById('clc-summary-financed');
            var browseBtn = document.getElementById('clc-browse-btn');
            var baseUrl = '{{ route('frontend.inventory') }}';

            function calculate() {
                var price = num(priceEl.value);
                var down = num(downEl.value);
                var trade = num(tradeEl.value);
                var balance = num(balanceEl.value);
                var term = Number(termEl.value);
                var apr = num(aprEl.value);

                var principal = Math.max(0, price - down - trade + balance);
                var monthlyRate = (apr / 100) / 12;
                var monthly = monthlyRate > 0
                    ? (principal * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -term))
                    : principal / term;

                monthlyEl.textContent = '$' + money(monthly);
                termsDisplayEl.textContent = 'Est. payment for ' + term + ' months at ' + apr.toFixed(2) + '% APR';
                summaryPrice.textContent = '$' + money(price);
                summaryDown.textContent = '-$' + money(down);
                summaryTrade.textContent = '-$' + money(trade);
                summaryFinanced.textContent = '$' + money(principal);

                if (browseBtn) {
                    var maxPrice = Math.round(price);
                    browseBtn.href = baseUrl + '?price[gt]=0&price[lt]=' + maxPrice;
                    browseBtn.textContent = 'Browse vehicles under $' + money(maxPrice);
                }
            }

            function formatAndCalculate(el) {
                var raw = num(el.value);
                el.value = money(raw);
                calculate();
            }

            priceEl.addEventListener('input', function () { formatAndCalculate(this); });
            downEl.addEventListener('input', function () { formatAndCalculate(this); });
            tradeEl.addEventListener('input', function () { formatAndCalculate(this); });
            balanceEl.addEventListener('input', function () { formatAndCalculate(this); });
            termEl.addEventListener('change', calculate);
            aprEl.addEventListener('input', function () { formatAndCalculate(this); });
            scoreEl.addEventListener('input', function () {
                scoreValEl.textContent = this.value;
            });

            priceEl.addEventListener('blur', function () { formatAndCalculate(this); });
            downEl.addEventListener('blur', function () { formatAndCalculate(this); });
            tradeEl.addEventListener('blur', function () { formatAndCalculate(this); });
            balanceEl.addEventListener('blur', function () { formatAndCalculate(this); });
            aprEl.addEventListener('blur', function () { formatAndCalculate(this); });

            calculate();
        })();
    </script>
@endpush
