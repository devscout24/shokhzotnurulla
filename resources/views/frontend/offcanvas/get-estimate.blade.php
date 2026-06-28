{{-- ═══════════════════════════════════════════════════════════════════════
Payment Calculator — Offcanvas
ID prefix: gep- (get estimate payment)
═══════════════════════════════════════════════════════════════════════ --}}
@php
    $estimateMonthly = $monthly ?? 0;
    $estimatePrice = $pricing['final_price'] ?? 0;
    $estimateTitle = $vehicleTitle ?? 'Selected vehicle';
    $matchingDefaultRate = ($interestRates ?? collect())->first(function ($rate) {
        $termMatches = ($rate->min_term === null || 60 >= $rate->min_term)
            && ($rate->max_term === null || 60 <= $rate->max_term);
        $creditMatches = ($rate->min_credit_score === null || 740 >= $rate->min_credit_score)
            && ($rate->max_credit_score === null || 740 <= $rate->max_credit_score);
        return $termMatches && $creditMatches;
    });

    $estimateRate = ($pricing['applied_special'] ?? null)
        && $pricing['applied_special']?->discount_type === 'special'
        && $pricing['applied_special']?->finance_rate
        ? (float) $pricing['applied_special']->finance_rate
        : ($matchingDefaultRate ? (float) $matchingDefaultRate->rate : 6.79);
    $estimateTerm = ($pricing['applied_special'] ?? null)
        && $pricing['applied_special']?->discount_type === 'special'
        && $pricing['applied_special']?->finance_term
        ? (int) $pricing['applied_special']->finance_term
        : 60;

    $interestRatesJson = ($interestRates ?? collect())->map(fn($r) => [
        'id' => $r->id,
        'make' => $r->make,
        'min_model_year' => $r->min_model_year,
        'max_model_year' => $r->max_model_year,
        'min_term' => $r->min_term,
        'max_term' => $r->max_term,
        'min_credit_score' => $r->min_credit_score,
        'max_credit_score' => $r->max_credit_score,
        'condition' => $r->condition,
        'rate' => (float) $r->rate,
        'sort_order' => $r->sort_order,
    ])->toJson();
@endphp
@once
    <style>
        /* ── Stepper (unit price & trade-in) ── */
        #getEstimate .gep-stepper .input-group-text,
        #getEstimate .gep-stepper .form-control {
            border-radius: 0 !important;
        }

        #getEstimate .gep-stepper-btn {
            width: 42px;
            justify-content: center;
            cursor: pointer;
            user-select: none;
        }

        #getEstimate .gep-stepper-btn:first-child {
            border-radius: 12px 0 0 12px !important;
        }

        #getEstimate .gep-stepper-btn:last-child {
            border-radius: 0 12px 12px 0 !important;
        }

        #getEstimate .gep-stepper-symbol {
            min-width: 38px;
            justify-content: center;
        }

        /* ── Custom range slider ── */
        #getEstimate .gep-range {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 4px;
            border-radius: 2px;
            outline: none;
            cursor: pointer;
            /* background set via JS */
        }

        #getEstimate .gep-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background-color: #ffffff;
            cursor: grab;
            border: 1px solid #eeeeee;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            /* grip bar via SVG background */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='5' height='12'%3E%3Crect width='5' height='12' rx='2' fill='%23cccccc'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center center;
        }


        #getEstimate .gep-range::-webkit-slider-thumb:active {
            cursor: grabbing;
        }

        #getEstimate .gep-range::-moz-range-thumb {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background-color: #ffffff;
            cursor: grab;
            border: 1px solid #eeeeee;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='5' height='12'%3E%3Crect width='5' height='12' rx='2' fill='%23cccccc'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center center;
        }

        #getEstimate .gep-range::-moz-range-thumb:active {
            cursor: grabbing;
        }

        #getEstimate .gep-slider-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #6c757d;
            margin-top: 2px;
        }

        #getEstimate .gep-slider-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 6px;
        }
    </style>
@endonce

<div class="offcanvas offcanvas-end w-lg-50 w-100" tabindex="-1" id="getEstimate" aria-labelledby="getEstimateLabel"
    data-interest-rates='{{ $interestRatesJson }}'>

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="offcanvas-header w-100">
        <h3 class="h5 ms-1 mb-4 float-start d-flex justify-content-between align-items-center w-100"
            style="border-bottom: 2px solid #166B87;">
            Payment Calculator
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                class="close closeBtn text-large btn btn-link">×</button>
        </h3>
    </div>

    {{-- ── Body ────────────────────────────────────────────────────────────── --}}
    <div class="offcanvas-body px-4 pt-0">

        {{-- Monthly payment display --}}
        <div class="text-center">
            <small class="text-muted" data-gep-vehicle-title>{{ $estimateTitle }}</small>
            <div class="text-xlarge my-1" style="color: #166B87;">
                <b data-cy="paymentcalc-amount">${{ number_format($estimateMonthly) }}</b><span class="text-muted"> /
                    mo</span>
            </div>
            <small data-gep-terms>Est. payment for {{ $estimateTerm }} months at {{ number_format($estimateRate, 2) }}%
                APR</small>
        </div>

        <div class="pt-3 border-top"></div>

        {{-- ── Credit score slider ── --}}
        <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <b>Credit score: <span data-gep-credit-score>740</span></b>
                <a href="#" target="_blank" rel="noopener noreferrer" style="color: #166B87;"
                    data-cy="paymentcalc-print" title="Print payment details">
                    <span class="d-inline-block me-1">
                        <svg height="12" width="12" viewBox="0 0 24 24" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6 7V3h12v4H6zm12 2h1a3 3 0 0 1 3 3v5h-4v4H6v-4H2v-5a3 3 0 0 1 3-3h1v2H5a1 1 0 0 0-1 1v3h16v-3a1 1 0 0 0-1-1h-1V9zM8 19h8v-4H8v4z" />
                        </svg>
                    </span>
                    Print
                </a>
            </div>
            <input type="range" class="gep-range" min="400" max="850" value="740" aria-label="Credit score"
                data-gep-credit-slider>
            <div class="gep-slider-labels">
                <span>400</span>
                <span>850</span>
            </div>
        </div>

        <div class="border-top pt-3"></div>

        {{-- ── Unit price ── --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Unit price</label>
                <div class="input-group gep-stepper">
                    <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="-100">
                        <b class="mx-auto">-</b>
                    </button>
                    <span class="bg-lighter input-group-text gep-stepper-symbol"><b class="mx-auto">$</b></span>
                    <input class="form-control border-radius-0" placeholder="10,000" disabled min="1000" max="1000000"
                        required type="text" value="{{ $estimatePrice ? number_format($estimatePrice) : '' }}"
                        name="amount" inputmode="numeric" style="font-size: inherit;">
                    <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="100">
                        <b class="mx-auto">+</b>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Term slider + Down slider (side by side) ── --}}
        <div class="row mb-3">
            {{-- Term --}}
            <div class="col-6">
                <div class="gep-slider-title">Term: <span data-gep-term-display>60</span> mo.</div>
                <input type="range" class="gep-range" min="36" max="84" value="60" step="1"
                    aria-label="Loan term in months" data-gep-term-slider data-gep-term-steps="36,48,60,72,75,84">
                <div class="gep-slider-labels">
                    <span>36 mo</span>
                    <span>84 mo</span>
                </div>
            </div>
            {{-- Down --}}
            <div class="col-6">
                <div class="gep-slider-title">Down: <span data-gep-down-display>$0</span></div>
                <input type="range" class="gep-range" min="0" max="50" value="0" step="1"
                    aria-label="Down payment percentage" data-gep-down-slider>
                <div class="gep-slider-labels">
                    <span>0%</span>
                    <span>50%</span>
                </div>
            </div>
        </div>

        {{-- Hidden inputs still used by calculateMonthly() --}}
        <input type="hidden" name="down_pct" value="0">
        {{-- term is read from offcanvas.dataset.term, set by the term slider --}}

        {{-- ── Trade-in Value ── --}}
        <div class="calc-tradein col-12 px-0">
            <div class="border-top">
                <div class="py-3 cursor-pointer d-flex align-items-center" role="button" data-gep-collapse-toggle
                    data-gep-target="#gep-tradein" aria-expanded="true" aria-controls="gep-tradein">
                    <span class="d-inline-block me-2 mt-n1" style="color: #166B87;">
                        <svg height="16" width="16" fill="#166B87">
                            <use data-gep-collapse-icon xlink:href="/regular.svg#square-minus"></use>
                        </svg>
                    </span>
                    Trade-in Value
                </div>

                <div id="gep-tradein" class="collapse show">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="form-label">Est. Trade Value</label>
                                <div class="input-group gep-stepper">
                                    <button type="button" class="bg-lighter input-group-text gep-stepper-btn"
                                        data-gep-step="-100">
                                        <b class="mx-auto">-</b>
                                    </button>
                                    <span class="bg-lighter input-group-text gep-stepper-symbol"><b
                                            class="mx-auto">$</b></span>
                                    <input class="form-control border-radius-0" placeholder="10,000" max="1000000"
                                        required type="text" value="0" name="tradeinamount" inputmode="numeric">
                                    <button type="button" class="bg-lighter input-group-text gep-stepper-btn"
                                        data-gep-step="100">
                                        <b class="mx-auto">+</b>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-3">
                                <label class="form-label">Remaining Loan Balance</label>
                                <div class="input-group gep-stepper">
                                    <button type="button" class="bg-lighter input-group-text gep-stepper-btn"
                                        data-gep-step="-100">
                                        <b class="mx-auto">-</b>
                                    </button>
                                    <span class="bg-lighter input-group-text gep-stepper-symbol"><b
                                            class="mx-auto">$</b></span>
                                    <input class="form-control border-radius-0" placeholder="5,000" max="1000000"
                                        required type="text" value="0" name="tradeinremainingbalance"
                                        inputmode="numeric">
                                    <button type="button" class="bg-lighter input-group-text gep-stepper-btn"
                                        data-gep-step="100">
                                        <b class="mx-auto">+</b>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Footer CTA ── --}}
        <div class="py-4 border-top text-center mt-1">
            <strong class="d-block mb-2">Save an hour at the dealership</strong>
            <p>
                With our lender relationships, we can often beat your bank or credit union's rate. Get your new car
                faster with an online approval. Estimated monthly payment does not include title and license fees.
                Monthly payment will be higher.
            </p>
            <button type="button" data-cy="btn-confirmation"
                class="cursor-pointer d-block btn btn-primary mx-auto btn-lg"
                style="background-color: #166B87; border-color: #166B87;" data-bs-toggle="offcanvas"
                data-bs-target="#getApproved" aria-controls="getApproved">
                Get approved &rsaquo;
            </button>
        </div>
    </div>
</div>

@once
    @push('page-scripts')
        <script>
            (function () {
                var offcanvas = document.getElementById('getEstimate');
                if (!offcanvas) return;

                // ── Parse interest rates ───────────────────────────────────────────
                var interestRates = [];
                try {
                    var raw = offcanvas.dataset.interestRates;
                    if (raw) interestRates = JSON.parse(raw);
                } catch (e) { }

                // ── Helpers ────────────────────────────────────────────────────────
                var moneyFormatter = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });

                function numberFrom(value) {
                    if (value === null || value === undefined) return 0;
                    return Number(String(value).replace(/[^0-9.-]/g, '')) || 0;
                }

                function money(value) {
                    return moneyFormatter.format(Math.max(0, Math.round(numberFrom(value))));
                }

                function setValue(selector, value) {
                    var input = offcanvas.querySelector(selector);
                    if (input) input.value = money(value);
                }

                function setInputValue(input, value) {
                    if (!input) return;
                    var min = numberFrom(input.getAttribute('min'));
                    var max = numberFrom(input.getAttribute('max'));
                    var next = numberFrom(value);
                    if (input.hasAttribute('min')) next = Math.max(min, next);
                    if (input.hasAttribute('max')) next = Math.min(max, next);
                    input.value = money(next);
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                function getValue(selector) {
                    return numberFrom(offcanvas.querySelector(selector)?.value);
                }

                // ── Term: snap to allowed steps ────────────────────────────────────
                var TERM_STEPS = [36, 48, 60, 72, 75, 84];

                function snapTerm(raw) {
                    return TERM_STEPS.reduce(function (prev, curr) {
                        return Math.abs(curr - raw) < Math.abs(prev - raw) ? curr : prev;
                    });
                }

                function currentTerm() {
                    return Number(offcanvas.dataset.term || 60);
                }

                // ── Slider fill helper ─────────────────────────────────────────────
                function updateSliderFill(slider) {
                    var min = Number(slider.min);
                    var max = Number(slider.max);
                    var val = Number(slider.value);
                    var pct = ((val - min) / (max - min)) * 100;
                    slider.style.background =
                        'linear-gradient(to right, #dee2e6 ' + pct + '%, #166B87 ' + pct + '%)';
                }

                // ── Rate matching ──────────────────────────────────────────────────
                function conditionMatches(rateCondition, vehicleCondition) {
                    if (vehicleCondition === 'New') return rateCondition === 'new' || rateCondition === 'any';
                    if (vehicleCondition === 'Certified Pre-Owned') return rateCondition === 'cpo' || rateCondition === 'used' || rateCondition === 'any';
                    return rateCondition === 'used' || rateCondition === 'any';
                }

                function findMatchingRate(vehicleYear, vehicleMake, vehicleCondition, creditScore, term) {
                    var matched = interestRates.filter(function (rate) {
                        if (rate.min_model_year > vehicleYear || rate.max_model_year < vehicleYear) return false;
                        if (rate.make && rate.make !== '' && rate.make !== vehicleMake) return false;
                        if (!conditionMatches(rate.condition, vehicleCondition)) return false;
                        if (rate.min_credit_score !== null && creditScore < rate.min_credit_score) return false;
                        if (rate.max_credit_score !== null && creditScore > rate.max_credit_score) return false;
                        if (rate.min_term !== null && term < rate.min_term) return false;
                        if (rate.max_term !== null && term > rate.max_term) return false;
                        return true;
                    });
                    if (matched.length === 0) return null;
                    matched.sort(function (a, b) {
                        var aMakeScore = a.make ? 0 : 1;
                        var bMakeScore = b.make ? 0 : 1;
                        if (aMakeScore !== bMakeScore) return aMakeScore - bMakeScore;
                        var aCondScore = a.condition === 'any' ? 1 : 0;
                        var bCondScore = b.condition === 'any' ? 1 : 0;
                        if (aCondScore !== bCondScore) return aCondScore - bCondScore;
                        if (a.sort_order !== b.sort_order) return a.sort_order - b.sort_order;
                        return a.id - b.id;
                    });
                    return matched[0];
                }

                // ── Calculate monthly payment ──────────────────────────────────────
                function calculateMonthly() {
                    var price = getValue('[name="amount"]');
                    var downPct = numberFrom(offcanvas.querySelector('[name="down_pct"]')?.value);
                    var downAmount = price * downPct / 100;
                    var tradeValue = getValue('[name="tradeinamount"]');
                    var tradeBalance = getValue('[name="tradeinremainingbalance"]');
                    var principal = Math.max(0, price - downAmount - tradeValue + tradeBalance);
                    var rate = numberFrom(offcanvas.dataset.rate || 6.79);
                    var term = currentTerm();
                    var monthlyRate = (rate / 100) / 12;
                    var monthly = monthlyRate > 0
                        ? (principal * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -term))
                        : principal / term;

                    var amountEl = offcanvas.querySelector('[data-cy="paymentcalc-amount"]');
                    var termsEl = offcanvas.querySelector('[data-gep-terms]');
                    if (amountEl) amountEl.textContent = '$' + money(monthly);
                    if (termsEl) termsEl.textContent = 'Est. payment for ' + term + ' months at ' + rate.toFixed(2) + '% APR';
                }

                // ── Update rate then recalculate ───────────────────────────────────
                function updateRateAndRecalculate() {
                    var vehicleYear = Number(offcanvas.dataset.vehicleYear || 0);
                    var vehicleMake = offcanvas.dataset.vehicleMake || '';
                    var vehicleCondition = offcanvas.dataset.vehicleCondition || '';
                    var creditScore = Number(offcanvas.querySelector('[data-gep-credit-slider]')?.value || 740);
                    var term = currentTerm();

                    // update credit score display
                    var creditDisplay = offcanvas.querySelector('[data-gep-credit-score]');
                    if (creditDisplay) creditDisplay.textContent = creditScore;

                    var matched = findMatchingRate(vehicleYear, vehicleMake, vehicleCondition, creditScore, term);
                    offcanvas.dataset.rate = matched ? matched.rate : numberFrom(offcanvas.dataset.fallbackRate || 6.79);

                    calculateMonthly();
                }

                // ── Credit score slider ────────────────────────────────────────────
                var creditSlider = offcanvas.querySelector('[data-gep-credit-slider]');
                if (creditSlider) {
                    creditSlider.addEventListener('input', function () {
                        updateSliderFill(creditSlider);
                        updateRateAndRecalculate();
                    });
                    updateSliderFill(creditSlider);
                }

                // ── Term slider ────────────────────────────────────────────────────
                var termSlider = offcanvas.querySelector('[data-gep-term-slider]');
                var termDisplay = offcanvas.querySelector('[data-gep-term-display]');

                function applyTermSlider() {
                    var snapped = snapTerm(Number(termSlider.value));
                    termSlider.value = snapped;
                    offcanvas.dataset.term = snapped;
                    if (termDisplay) termDisplay.textContent = snapped;
                    updateSliderFill(termSlider);
                    updateRateAndRecalculate();
                }

                if (termSlider) {
                    termSlider.addEventListener('input', applyTermSlider);
                    applyTermSlider();
                }

                // ── Down payment slider ────────────────────────────────────────────
                var downSlider = offcanvas.querySelector('[data-gep-down-slider]');
                var downDisplay = offcanvas.querySelector('[data-gep-down-display]');
                var downHidden = offcanvas.querySelector('[name="down_pct"]');

                function applyDownSlider() {
                    var pct = Number(downSlider.value);
                    var price = getValue('[name="amount"]');
                    var amt = Math.round(price * pct / 100);
                    if (downDisplay) downDisplay.textContent = '$' + money(amt);
                    if (downHidden) downHidden.value = pct;
                    updateSliderFill(downSlider);
                    calculateMonthly();
                }

                if (downSlider) {
                    downSlider.addEventListener('input', applyDownSlider);
                    updateSliderFill(downSlider);
                }

                // ── Trade-in inputs ────────────────────────────────────────────────
                offcanvas.querySelectorAll('input[name="tradeinamount"], input[name="tradeinremainingbalance"]').forEach(function (field) {
                    field.addEventListener('input', calculateMonthly);
                    field.addEventListener('change', calculateMonthly);
                });

                // ── Stepper buttons (unit price & trade-in only) ───────────────────
                offcanvas.querySelectorAll('.gep-stepper-btn').forEach(function (button) {
                    button.addEventListener('click', function () {
                        var group = button.closest('.gep-stepper');
                        var input = group ? group.querySelector('input') : null;
                        if (!input) return;
                        var delta = numberFrom(button.dataset.gepStep);
                        setInputValue(input, getValue('[name="' + input.name + '"]') + delta);
                        // if price changed, refresh down display too
                        if (input.name === 'amount') applyDownSlider();
                        calculateMonthly();
                    });
                });

                // ── Collapse toggles ───────────────────────────────────────────────
                function toggleCollapse(header) {
                    var target = offcanvas.querySelector(header.dataset.gepTarget || '');
                    if (!target) return;
                    var isOpen = target.classList.contains('show') && target.style.display !== 'none';
                    isOpen = !isOpen;
                    var icon = header.querySelector('[data-gep-collapse-icon]');
                    target.classList.toggle('show', isOpen);
                    target.style.display = isOpen ? 'block' : 'none';
                    header.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    if (icon) {
                        icon.setAttribute('href', isOpen ? '/regular.svg#square-minus' : '/regular.svg#square-plus');
                        icon.setAttribute('xlink:href', isOpen ? '/regular.svg#square-minus' : '/regular.svg#square-plus');
                    }
                }

                offcanvas.querySelectorAll('[data-gep-collapse-toggle]').forEach(function (header) {
                    var target = offcanvas.querySelector(header.dataset.gepTarget || '');
                    if (target) target.style.display = target.classList.contains('show') ? 'block' : 'none';
                    header.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleCollapse(header);
                    });
                });

                // ── Offcanvas show handler ─────────────────────────────────────────
                offcanvas.addEventListener('show.bs.offcanvas', function (event) {
                    var trigger = event.relatedTarget;
                    if (!trigger) return;

                    var title = trigger.dataset.vehicleTitle || 'Selected vehicle';
                    var price = numberFrom(trigger.dataset.vehiclePrice);
                    var monthly = numberFrom(trigger.dataset.vehicleMonthly);
                    var rate = numberFrom(trigger.dataset.vehicleRate || 6.79);
                    var term = Number(trigger.dataset.vehicleTerm || 60);

                    offcanvas.dataset.fallbackRate = rate;
                    offcanvas.dataset.rate = rate;
                    offcanvas.dataset.term = term;
                    offcanvas.dataset.vehicleYear = trigger.dataset.vehicleYear || '';
                    offcanvas.dataset.vehicleMake = trigger.dataset.vehicleMake || '';
                    offcanvas.dataset.vehicleCondition = trigger.dataset.vehicleCondition || '';

                    // reset credit slider
                    if (creditSlider) {
                        creditSlider.value = '740';
                        updateSliderFill(creditSlider);
                    }

                    // reset term slider
                    if (termSlider) {
                        termSlider.value = String(term);
                        if (termDisplay) termDisplay.textContent = term;
                        updateSliderFill(termSlider);
                    }

                    // reset down slider
                    if (downSlider) {
                        downSlider.value = '0';
                        if (downDisplay) downDisplay.textContent = '$0';
                        if (downHidden) downHidden.value = '0';
                        updateSliderFill(downSlider);
                    }

                    var titleEl = offcanvas.querySelector('[data-gep-vehicle-title]');
                    var amountEl = offcanvas.querySelector('[data-cy="paymentcalc-amount"]');
                    if (titleEl) titleEl.textContent = title;
                    if (amountEl) amountEl.textContent = '$' + money(monthly);

                    setValue('[name="amount"]', price);
                    setValue('[name="tradeinamount"]', 0);
                    setValue('[name="tradeinremainingbalance"]', 0);

                    updateRateAndRecalculate();
                });
            })();
        </script>
    @endpush
@endonce