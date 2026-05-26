{{-- ═══════════════════════════════════════════════════════════════════════
     Payment Calculator — Offcanvas
     ID prefix: gep- (get estimate payment)
═══════════════════════════════════════════════════════════════════════ --}}
@php
    $estimateMonthly = $monthly ?? 0;
    $estimatePrice = $pricing['final_price'] ?? 0;
    $estimateTitle = $vehicleTitle ?? 'Selected vehicle';
    $estimateRate = ($pricing['applied_special'] ?? null)
        && $pricing['applied_special']?->discount_type === 'special'
        && $pricing['applied_special']?->finance_rate
            ? (float) $pricing['applied_special']->finance_rate
            : 6.79;
    $estimateTerm = ($pricing['applied_special'] ?? null)
        && $pricing['applied_special']?->discount_type === 'special'
        && $pricing['applied_special']?->finance_term
            ? (int) $pricing['applied_special']->finance_term
            : 60;
@endphp
@once
    <style>
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
    </style>
@endonce
<div class="offcanvas offcanvas-end w-lg-50 w-100" tabindex="-1" id="getEstimate"
    aria-labelledby="getEstimateLabel">

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
        <div class="text-center">
            <small class="text-muted" data-gep-vehicle-title>{{ $estimateTitle }}</small>
            <div class="text-xlarge my-1" style="color: #166B87;">
                <b data-cy="paymentcalc-amount">${{ number_format($estimateMonthly) }}</b><span class="text-muted"> / mo</span>
            </div>
            <small data-gep-terms>Est. payment for {{ $estimateTerm }} months at {{ number_format($estimateRate, 2) }}% APR</small>
        </div>

        <div class="pt-3 border-top"></div>
        <div class="d-flex mb-2 align-items-center">
            <b>Credit score: 740</b>
            <a href="#" target="_blank" rel="noopener noreferrer" class="ms-auto"
                style="color: #166B87;" data-cy="paymentcalc-print" title="Print payment details">
                <span class="d-inline-block me-1">
                    <svg height="12" width="12" viewBox="0 0 24 24" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 7V3h12v4H6zm12 2h1a3 3 0 0 1 3 3v5h-4v4H6v-4H2v-5a3 3 0 0 1 3-3h1v2H5a1 1 0 0 0-1 1v3h16v-3a1 1 0 0 0-1-1h-1V9zM8 19h8v-4H8v4z" />
                    </svg>
                </span>
                Print
            </a>
        </div>

        <div class="mb-3">
            <input type="range" class="form-range" min="400" max="850" value="740"
                aria-label="Credit score">
        </div>

        <div class="row">
            <div class="col-md-4 col-12">
                <div class="mb-3 mb-md-4">
                    <label class="form-label">Unit price</label>
                    <div class="input-group gep-stepper">
                        <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="-100">
                            <b class="mx-auto">-</b>
                        </button>
                        <span class="bg-lighter input-group-text gep-stepper-symbol"><b class="mx-auto">$</b></span>
                        <input class="form-control border-radius-0" placeholder="10,000" disabled min="1000"
                            max="1000000" required type="text" value="{{ $estimatePrice ? number_format($estimatePrice) : '' }}" name="amount"
                            inputmode="numeric" style="font-size: inherit;">
                        <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="100">
                            <b class="mx-auto">+</b>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="mb-3 mb-md-4">
                    <label class="form-label">Loan months</label>
                    <select data-cy="paymentcalc-state" name="state" class="custom-select form-select">
                        <option value="36">36 months</option>
                        <option value="48">48 months</option>
                        <option value="60" {{ $estimateTerm === 60 ? 'selected' : '' }}>60 months</option>
                        <option value="72">72 months</option>
                        <option value="75">75 months</option>
                        <option value="84">84 months</option>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <div class="border-top">
                    <div class="py-3 cursor-pointer d-flex align-items-center" role="button"
                        data-gep-collapse-toggle data-gep-target="#gep-amount-down"
                        aria-expanded="true" aria-controls="gep-amount-down">
                        <span class="d-inline-block me-2 mt-n1" style="color: #166B87;">
                            <svg height="16" width="16" fill="#166B87">
                                <use data-gep-collapse-icon xlink:href="/regular.svg#square-minus"></use>
                            </svg>
                        </span>
                        Amount Down
                    </div>

                    <div id="gep-amount-down" class="collapse show">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-3 mb-md-4">
                                    <label class="form-label">Down payment</label>
                                    <div role="group" class="d-flex btn-group">
                                        <button type="button" data-cy="paymentcalc-downPref"
                                            class="w-50 py-2 btn btn-default">Cash</button>
                                        <button type="button" data-cy="paymentcalc-downPref"
                                            class="w-50 py-2 btn btn-secondary active">Percentage</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-0">
                                    <label class="form-label">Amount Down</label>
                                    <div class="mb-3 mb-md-4 input-group gep-stepper">
                                        <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="-1">
                                            <b class="mx-auto">-</b>
                                        </button>
                                        <span class="bg-lighter input-group-text gep-stepper-symbol"><b class="mx-auto">%</b></span>
                                        <input class="form-control border-radius-0" data-cy="paymentcalc-down" step="1"
                                            min="0.0" max="99.9" placeholder="10" required type="text" value="0"
                                            name="down_pct" inputmode="numeric">
                                        <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="1">
                                            <b class="mx-auto">+</b>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="calc-tradein col-12">
                <div class="border-top">
                    <div class="py-3 cursor-pointer d-flex align-items-center" role="button"
                        data-gep-collapse-toggle data-gep-target="#gep-tradein"
                        aria-expanded="true" aria-controls="gep-tradein">
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
                                <div class="mb-0">
                                    <label class="form-label">Est. Trade Value</label>
                                    <div class="mb-3 mb-md-4 input-group gep-stepper">
                                        <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="-100">
                                            <b class="mx-auto">-</b>
                                        </button>
                                        <span class="bg-lighter input-group-text gep-stepper-symbol"><b class="mx-auto">$</b></span>
                                        <input class="form-control border-radius-0" placeholder="10,000" max="1000000"
                                            required type="text" value="0" name="tradeinamount"
                                            inputmode="numeric">
                                        <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="100">
                                            <b class="mx-auto">+</b>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-0">
                                    <label class="form-label">Remaining Loan Balance</label>
                                    <div class="mb-3 mb-md-4 input-group gep-stepper">
                                        <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="-100">
                                            <b class="mx-auto">-</b>
                                        </button>
                                        <span class="bg-lighter input-group-text gep-stepper-symbol"><b class="mx-auto">$</b></span>
                                        <input class="form-control border-radius-0" placeholder="5,000" max="1000000"
                                            required type="text" value="0" name="tradeinremainingbalance"
                                            inputmode="numeric">
                                        <button type="button" class="bg-lighter input-group-text gep-stepper-btn" data-gep-step="100">
                                            <b class="mx-auto">+</b>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-4 border-top text-center mt-1">
            <strong class="d-block mb-2">Save an hour at the dealership</strong>
            <p>
                With our lender relationships, we can often beat your bank or credit union's rate. Get your new car
                faster with an online approval. Estimated monthly payment does not include title and license fees.
                Monthly payment will be higher.
            </p>
            <button type="button" data-cy="btn-confirmation"
                class="cursor-pointer d-block btn btn-primary mx-auto btn-lg"
                style="background-color: #166B87; border-color: #166B87;"
                data-bs-toggle="offcanvas" data-bs-target="#getApproved" aria-controls="getApproved">
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

                var moneyFormatter = new Intl.NumberFormat('en-US', {
                    maximumFractionDigits: 0
                });

                function numberFrom(value) {
                    if (value === null || value === undefined) return 0;
                    return Number(String(value).replace(/[^0-9.-]/g, '')) || 0;
                }

                function money(value) {
                    return moneyFormatter.format(Math.max(0, Math.round(numberFrom(value))));
                }

                function formatInputValue(input, value) {
                    var next = numberFrom(value);

                    if (input && input.name === 'down_pct' && !Number.isInteger(next)) {
                        return next.toFixed(1);
                    }

                    return money(next);
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

                    input.value = formatInputValue(input, next);
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                function getValue(selector) {
                    return numberFrom(offcanvas.querySelector(selector)?.value);
                }

                function currentRate() {
                    return numberFrom(offcanvas.dataset.rate || '6.79');
                }

                function currentTerm() {
                    return Number(offcanvas.querySelector('select[name="state"]')?.value || offcanvas.dataset.term || 60);
                }

                function calculateMonthly() {
                    var price = getValue('[name="amount"]');
                    var downPct = numberFrom(offcanvas.querySelector('[name="down_pct"]')?.value);
                    var tradeValue = getValue('[name="tradeinamount"]');
                    var tradeBalance = getValue('[name="tradeinremainingbalance"]');
                    var principal = Math.max(0, price - (price * downPct / 100) - tradeValue + tradeBalance);
                    var rate = currentRate();
                    var term = currentTerm();
                    var monthlyRate = (rate / 100) / 12;
                    var monthly = monthlyRate > 0
                        ? (principal * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -term))
                        : principal / term;

                    var amount = offcanvas.querySelector('[data-cy="paymentcalc-amount"]');
                    var terms = offcanvas.querySelector('[data-gep-terms]');

                    if (amount) amount.textContent = '$' + money(monthly);
                    if (terms) terms.textContent = 'Est. payment for ' + term + ' months at ' + rate.toFixed(2) + '% APR';
                }

                function toggleCollapse(header) {
                    var target = offcanvas.querySelector(header.dataset.gepTarget || '');
                    if (!target) return;

                    var isOpen = target.classList.toggle('show');
                    var icon = header.querySelector('[data-gep-collapse-icon]');
                    header.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                    if (icon) {
                        icon.setAttribute('href', isOpen ? '/regular.svg#square-minus' : '/regular.svg#square-plus');
                        icon.setAttribute('xlink:href', isOpen ? '/regular.svg#square-minus' : '/regular.svg#square-plus');
                    }
                }

                offcanvas.addEventListener('show.bs.offcanvas', function (event) {
                    var trigger = event.relatedTarget;
                    if (!trigger) return;

                    var title = trigger.dataset.vehicleTitle || 'Selected vehicle';
                    var price = numberFrom(trigger.dataset.vehiclePrice);
                    var monthly = numberFrom(trigger.dataset.vehicleMonthly);
                    var rate = numberFrom(trigger.dataset.vehicleRate || 6.79);
                    var term = Number(trigger.dataset.vehicleTerm || 60);

                    offcanvas.dataset.rate = rate;
                    offcanvas.dataset.term = term;

                    var titleEl = offcanvas.querySelector('[data-gep-vehicle-title]');
                    var amount = offcanvas.querySelector('[data-cy="paymentcalc-amount"]');
                    var termSelect = offcanvas.querySelector('select[name="state"]');

                    if (titleEl) titleEl.textContent = title;
                    if (amount) amount.textContent = '$' + money(monthly);
                    if (termSelect) termSelect.value = String(term);
                    setValue('[name="amount"]', price);
                    setValue('[name="tradeinamount"]', 0);
                    setValue('[name="tradeinremainingbalance"]', 0);
                    var downPct = offcanvas.querySelector('[name="down_pct"]');
                    if (downPct) downPct.value = '0';
                    calculateMonthly();
                });

                offcanvas.querySelectorAll('input[name="down_pct"], input[name="tradeinamount"], input[name="tradeinremainingbalance"], select[name="state"]').forEach(function (field) {
                    field.addEventListener('input', calculateMonthly);
                    field.addEventListener('change', calculateMonthly);
                });

                offcanvas.querySelectorAll('.gep-stepper-btn').forEach(function (button) {
                    button.addEventListener('click', function () {
                        var group = button.closest('.gep-stepper');
                        var input = group ? group.querySelector('input') : null;
                        if (!input) return;

                        var delta = numberFrom(button.dataset.gepStep);
                        setInputValue(input, getValue('[name="' + input.name + '"]') + delta);
                        calculateMonthly();
                    });
                });

                offcanvas.querySelectorAll('[data-gep-collapse-toggle]').forEach(function (header) {
                    header.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        toggleCollapse(header);
                    });
                });
            })();
        </script>
    @endpush
@endonce
