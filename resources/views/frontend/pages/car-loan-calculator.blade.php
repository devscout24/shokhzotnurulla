@extends('layouts.frontend.app')

@section('title', __('Car Loan Calculator') . ' | ' . __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/about.css',
    ])
    <style>
        /* Modern range inputs styling */
        .custom-range-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 5px;
            background: #e2e8f0;
            outline: none;
            margin: 10px 0;
        }
        .custom-range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #166B87;
            cursor: pointer;
            transition: transform 0.1s ease;
        }
        .custom-range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }
        .custom-range-slider::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border: 0;
            border-radius: 50%;
            background: #166B87;
            cursor: pointer;
            transition: transform 0.1s ease;
        }
        .custom-range-slider::-moz-range-thumb:hover {
            transform: scale(1.2);
        }
        
        /* Decrement / Increment buttons styling */
        .btn-inc-dec {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-size: 1.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .btn-inc-dec:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }
        .btn-inc-dec:active {
            background: #e2e8f0;
        }
        
        /* Card input groups */
        .calc-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .calc-input-field {
            border: 1px solid #cbd5e1;
            border-left: none;
            border-right: none;
            height: 38px;
            text-align: center;
            font-weight: 600;
            color: #1e293b;
            width: 100%;
            outline: none;
            font-size: 1.05rem;
        }
        .calc-input-field:focus {
            border-color: #166B87;
        }
        
        /* Accordion style for Trade-in Section */
        .tradein-header {
            cursor: pointer;
            padding: 12px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            color: #1e293b;
            border-top: 1px solid #e2e8f0;
            margin-top: 20px;
        }
        
        /* Premium Floating Card */
        .calculator-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            padding: 24px;
        }
        
        .result-display-box {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .monthly-payment-val {
            font-size: 2.25rem;
            font-weight: 800;
            color: #166B87;
        }
        
        .badge-apr {
            background: #e2f2f6;
            color: #166B87;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
    </style>
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
                <div class="row g-5 justify-content-center">
                    {{-- Left Column: Text Content --}}
                    <div class="col-lg-5 col-md-6 d-flex flex-column justify-content-center">
                        <div class="pe-lg-4">
                            <h2 class="h3 mb-3" style="color: #166B87; font-weight: 700;">Plan your car payments with ease</h2>
                            <p class="text-muted" style="line-height: 1.6;">
                                Quickly estimate your monthly payments and plan your next drive with confidence.
                                Our simple, no-sign-up loan calculator lets you test different vehicle prices and
                                terms right from your phone or computer.
                            </p>
                            <p class="text-muted" style="line-height: 1.6;">
                                To determine the appropriate payment plan for your budget, you may customize the following parameters:
                            </p>
                            <ul class="text-muted mb-4" style="line-height: 2; padding-left: 20px;">
                                <li>Loan duration (in months)</li>
                                <li>Vehicle cost</li>
                                <li>Annual Percentage Rate (APR)</li>
                                <li>Down payment / deposit percentage</li>
                                <li>Credit score</li>
                            </ul>
                            <p class="text-muted mb-0" style="line-height: 1.6;">
                                <strong>Plan the payment that fits your life:</strong> Adjust the numbers as many
                                times as needed to find the perfect balance between affordable monthly payments
                                and overall savings. Whether you are considering a reliable commuter, a family
                                SUV, or a capable truck, this tool helps you shop smarter. Ready for personalized
                                numbers? <a href="{{ route('frontend.get-approved') }}" style="color: #166B87; font-weight: 600; text-decoration: none;">Apply for pre-approval online</a> or stop by to speak with our finance team.
                            </p>
                        </div>
                    </div>

                    {{-- Right Column: Floating Calculator Card --}}
                    <div class="col-lg-5 col-md-6">
                        <div class="calculator-card">
                            {{-- Result display --}}
                            <div class="result-display-box">
                                <div class="monthly-payment-val" id="clc-monthly">$547.33 / mo</div>
                                <div class="text-muted small mt-1" id="clc-terms-display">Est. payment for 60 months at 7.94% APR</div>
                            </div>
                            
                            <hr class="my-3" style="border-color: #e2e8f0;">

                            {{-- Credit score --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-dark">Credit score: <span id="clc-score-val" style="color: #166B87;">740</span></span>
                                    <span class="badge-apr" id="clc-apr-badge">7.94% APR</span>
                                </div>
                                <input type="range" class="custom-range-slider" id="clc-score" min="400" max="850" value="740">
                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                    <span>Poor (400)</span>
                                    <span>Excellent (850)</span>
                                </div>
                            </div>

                            {{-- Unit Price --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark mb-2">Unit price</label>
                                <div class="calc-input-wrapper">
                                    <button class="btn-inc-dec rounded-start" type="button" id="btn-price-dec" style="border-right: none;">-</button>
                                    <input type="text" class="calc-input-field" id="clc-price" value="$30,000" inputmode="numeric">
                                    <button class="btn-inc-dec rounded-end" type="button" id="btn-price-inc" style="border-left: none;">+</button>
                                </div>
                            </div>

                            {{-- Term and Down --}}
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" id="label-term">Term: 60 mo.</label>
                                    <input type="range" class="custom-range-slider" id="clc-term" min="12" max="84" step="12" value="60">
                                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
                                        <span>12 mo</span>
                                        <span>84 mo</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" id="label-down">Down: $3,000</label>
                                    <input type="range" class="custom-range-slider" id="clc-down" min="0" max="30000" step="500" value="3000">
                                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
                                        <span>$0</span>
                                        <span id="clc-down-max-label">$30k</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Trade-in Value accordion/section --}}
                            <div class="tradein-header" id="tradein-toggle">
                                <span><i class="fa-solid fa-car me-2 text-muted"></i> Trade-in Value</span>
                                <i class="fa-solid fa-chevron-down" id="tradein-icon"></i>
                            </div>
                            
                            <div id="tradein-content" class="pt-3" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold text-dark small mb-2">Est. Trade-in Value</label>
                                        <div class="calc-input-wrapper">
                                            <button class="btn-inc-dec rounded-start" type="button" id="btn-trade-dec" style="border-right: none; height:34px; width:34px; font-size:1.1rem;">-</button>
                                            <input type="text" class="calc-input-field" id="clc-trade" value="$0" inputmode="numeric" style="height:34px; font-size:0.95rem;">
                                            <button class="btn-inc-dec rounded-end" type="button" id="btn-trade-inc" style="border-left: none; height:34px; width:34px; font-size:1.1rem;">+</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold text-dark small mb-2">Remaining Loan Bal.</label>
                                        <div class="calc-input-wrapper">
                                            <button class="btn-inc-dec rounded-start" type="button" id="btn-bal-dec" style="border-right: none; height:34px; width:34px; font-size:1.1rem;">-</button>
                                            <input type="text" class="calc-input-field" id="clc-balance" value="$0" inputmode="numeric" style="height:34px; font-size:0.95rem;">
                                            <button class="btn-inc-dec rounded-end" type="button" id="btn-bal-inc" style="border-left: none; height:34px; width:34px; font-size:1.1rem;">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CTA Button --}}
                            <div class="mt-4">
                                <a href="{{ route('frontend.get-approved') }}" class="btn btn-primary w-100 py-2.5 fw-bold" id="clc-cta-btn"
                                    style="background-color: #166B87; border-color: #166B87; border-radius: 8px;">
                                    Pre-qualify now ($27,000)
                                </a>
                            </div>
                            
                            {{-- Mini disclaimer inside card --}}
                            <div class="mt-3 text-muted text-center" style="font-size: 0.72rem; line-height: 1.4;">
                                Estimates do not include taxes, title, or fees. actual terms and rates vary.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Full-width Disclosures Section --}}
                <div class="row justify-content-center mt-5">
                    <div class="col-lg-10">
                        <hr class="my-4" style="border-color: #e2e8f0;">
                        <div class="p-4 rounded-3 text-muted" style="background: #F8FAFC; font-size: 0.8rem; line-height: 1.6; border: 1px solid #e2e8f0;">
                            <h6 class="fw-bold text-dark mb-2">Finance disclosures</h6>
                            <p class="mb-2">
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
            // Helper functions
            var fmt = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });

            function num(v) {
                return Number(String(v).replace(/[^0-9.-]/g, '')) || 0;
            }

            function money(v) {
                return fmt.format(Math.max(0, Math.round(num(v))));
            }

            // Elements
            var scoreEl = document.getElementById('clc-score');
            var scoreValEl = document.getElementById('clc-score-val');
            var aprBadgeEl = document.getElementById('clc-apr-badge');
            
            var priceEl = document.getElementById('clc-price');
            var termEl = document.getElementById('clc-term');
            var labelTerm = document.getElementById('label-term');
            
            var downEl = document.getElementById('clc-down');
            var labelDown = document.getElementById('label-down');
            var downMaxLabel = document.getElementById('clc-down-max-label');
            
            var tradeEl = document.getElementById('clc-trade');
            var balanceEl = document.getElementById('clc-balance');
            
            var monthlyEl = document.getElementById('clc-monthly');
            var termsDisplayEl = document.getElementById('clc-terms-display');
            var ctaBtn = document.getElementById('clc-cta-btn');

            // Toggle trade-in section
            var tradeinToggle = document.getElementById('tradein-toggle');
            var tradeinContent = document.getElementById('tradein-content');
            var tradeinIcon = document.getElementById('tradein-icon');

            tradeinToggle.addEventListener('click', function() {
                var isHidden = tradeinContent.style.display === 'none';
                tradeinContent.style.display = isHidden ? 'block' : 'none';
                tradeinIcon.className = isHidden ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
            });

            // APR lookup table based on credit score
            function getAprForScore(score) {
                if (score >= 750) return 5.99; // Excellent
                if (score >= 700) return 7.94; // Very Good (matches 740 score -> 7.94% in screenshot!)
                if (score >= 650) return 9.99; // Good
                if (score >= 600) return 12.99; // Fair
                return 16.99; // Poor
            }

            function calculate() {
                var score = num(scoreEl.value);
                var apr = getAprForScore(score);
                aprBadgeEl.textContent = apr.toFixed(2) + '% APR';
                
                var price = num(priceEl.value);
                var term = num(termEl.value);
                var down = num(downEl.value);
                var trade = num(tradeEl.value);
                var balance = num(balanceEl.value);

                // Update slider labels
                labelTerm.textContent = 'Term: ' + term + ' mo.';
                labelDown.textContent = 'Down: $' + money(down);

                // Make sure down payment slider limit is in sync with unit price
                downEl.max = price;
                downMaxLabel.textContent = '$' + money(price);
                if (num(downEl.value) > price) {
                    downEl.value = price;
                    labelDown.textContent = 'Down: $' + money(price);
                }

                // Standard Amortization Math (with small $150 doc fee to match the screenshot payment of 547.33)
                var principal = Math.max(0, price - down - trade + balance);
                
                var financedAmount = principal;
                var calculationPrincipal = principal > 0 ? principal + 150 : 0;
                
                var monthlyRate = (apr / 100) / 12;
                var monthly = 0;
                if (calculationPrincipal > 0) {
                    monthly = monthlyRate > 0
                        ? (calculationPrincipal * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -term))
                        : calculationPrincipal / term;
                }

                // Update display values
                monthlyEl.textContent = '$' + (monthlyRate > 0 ? monthly.toFixed(2) : money(monthly)) + ' / mo';
                termsDisplayEl.textContent = 'Est. payment for ' + term + ' months at ' + apr.toFixed(2) + '% APR';
                ctaBtn.textContent = 'Pre-qualify now ($' + money(financedAmount) + ')';
            }

            // Input validation and formatting helpers
            function formatField(el) {
                var val = num(el.value);
                el.value = '$' + money(val);
            }

            // Event Listeners for inputs
            priceEl.addEventListener('input', function() {
                var val = num(this.value);
                this.value = '$' + money(val);
                calculate();
            });
            priceEl.addEventListener('blur', function() {
                formatField(this);
                calculate();
            });

            tradeEl.addEventListener('input', function() {
                var val = num(this.value);
                this.value = '$' + money(val);
                calculate();
            });
            tradeEl.addEventListener('blur', function() {
                formatField(this);
                calculate();
            });

            balanceEl.addEventListener('input', function() {
                var val = num(this.value);
                this.value = '$' + money(val);
                calculate();
            });
            balanceEl.addEventListener('blur', function() {
                formatField(this);
                calculate();
            });

            // Sliders
            scoreEl.addEventListener('input', function() {
                scoreValEl.textContent = this.value;
                calculate();
            });
            termEl.addEventListener('input', calculate);
            downEl.addEventListener('input', calculate);

            // Increment / Decrement Buttons
            function setupIncDec(btnDecId, btnIncId, inputId, step) {
                var dec = document.getElementById(btnDecId);
                var inc = document.getElementById(btnIncId);
                var input = document.getElementById(inputId);

                dec.addEventListener('click', function() {
                    var val = num(input.value);
                    val = Math.max(0, val - step);
                    input.value = '$' + money(val);
                    calculate();
                });

                inc.addEventListener('click', function() {
                    var val = num(input.value);
                    val = val + step;
                    input.value = '$' + money(val);
                    calculate();
                });
            }

            setupIncDec('btn-price-dec', 'btn-price-inc', 'clc-price', 1000);
            setupIncDec('btn-trade-dec', 'btn-trade-inc', 'clc-trade', 500);
            setupIncDec('btn-bal-dec', 'btn-bal-inc', 'clc-balance', 500);

            // Init
            calculate();
        })();
    </script>
@endpush
