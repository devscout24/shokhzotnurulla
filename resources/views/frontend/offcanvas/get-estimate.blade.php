{{-- ═══════════════════════════════════════════════════════════════════════
     Payment Calculator — Offcanvas
     ID prefix: gep- (get estimate payment)
═══════════════════════════════════════════════════════════════════════ --}}
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
            <small class="text-muted">2017 Lincoln Navigator L Reserve</small>
            <div class="text-xlarge my-1" style="color: #166B87;">
                <b data-cy="paymentcalc-amount">$489.34</b><span class="text-muted"> / mo</span>
            </div>
            <small>Est. payment for 60 months at 7.99% APR</small>
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
                    <div class="input-group">
                        <span class="bg-lighter prepend input-group-text"><b class="mx-auto">$</b></span>
                        <input class="form-control border-radius-0" placeholder="10,000" disabled min="1000"
                            max="1000000" required type="text" value="25,999" name="amount"
                            inputmode="numeric" style="font-size: inherit;">
                        <span class="bg-lighter append input-group-text" role="button"><b
                                class="mx-auto">-</b></span>
                        <span class="bg-lighter append input-group-text" role="button"><b
                                class="mx-auto">+</b></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="mb-3 mb-md-4">
                    <label class="form-label">Loan months</label>
                    <select data-cy="paymentcalc-state" name="state" class="custom-select form-select">
                        <option value="36">36 months</option>
                        <option value="48">48 months</option>
                        <option value="60" selected>60 months</option>
                        <option value="72">72 months</option>
                        <option value="75">75 months</option>
                        <option value="84">84 months</option>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <div class="border-top">
                    <div class="py-3 cursor-pointer d-flex align-items-center" role="button"
                        data-bs-toggle="collapse" data-bs-target="#gep-amount-down"
                        aria-expanded="true" aria-controls="gep-amount-down">
                        <span class="d-inline-block me-2 mt-n1" style="color: #166B87;">
                            <svg height="16" width="16" fill="#166B87">
                                <use xlink:href="/regular.svg#square-minus"></use>
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
                                    <div class="mb-3 mb-md-4 input-group">
                                        <span class="bg-lighter prepend input-group-text"><b class="mx-auto">%</b></span>
                                        <input class="form-control border-radius-0" data-cy="paymentcalc-down" step="1"
                                            min="0.0" max="99.9" placeholder="10" required type="text" value="11"
                                            name="down_pct" inputmode="numeric">
                                        <span class="bg-lighter append input-group-text" role="button"><b
                                                class="mx-auto">-</b></span>
                                        <span class="bg-lighter append input-group-text" role="button"><b
                                                class="mx-auto">+</b></span>
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
                        data-bs-toggle="collapse" data-bs-target="#gep-tradein"
                        aria-expanded="true" aria-controls="gep-tradein">
                        <span class="d-inline-block me-2 mt-n1" style="color: #166B87;">
                            <svg height="16" width="16" fill="#166B87">
                                <use xlink:href="/regular.svg#square-minus"></use>
                            </svg>
                        </span>
                        Trade-in Value
                    </div>

                    <div id="gep-tradein" class="collapse show">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="mb-0">
                                    <label class="form-label">Est. Trade Value</label>
                                    <div class="mb-3 mb-md-4 input-group">
                                        <span class="bg-lighter prepend input-group-text"><b class="mx-auto">$</b></span>
                                        <input class="form-control border-radius-0" placeholder="10,000" max="1000000"
                                            required type="text" value="1,000" name="tradeinamount"
                                            inputmode="numeric">
                                        <span class="bg-lighter append input-group-text" role="button"><b
                                                class="mx-auto">-</b></span>
                                        <span class="bg-lighter append input-group-text" role="button"><b
                                                class="mx-auto">+</b></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="mb-0">
                                    <label class="form-label">Remaining Loan Balance</label>
                                    <div class="mb-3 mb-md-4 input-group">
                                        <span class="bg-lighter prepend input-group-text"><b class="mx-auto">$</b></span>
                                        <input class="form-control border-radius-0" placeholder="5,000" max="1000000"
                                            required type="text" value="2,000" name="tradeinremainingbalance"
                                            inputmode="numeric">
                                        <span class="bg-lighter append input-group-text" role="button"><b
                                                class="mx-auto">-</b></span>
                                        <span class="bg-lighter append input-group-text" role="button"><b
                                                class="mx-auto">+</b></span>
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
                data-bs-toggle="offcanvas" data-bs-target="#get-approved" aria-controls="get-approved">
                Get approved &rsaquo;
            </button>
        </div>
    </div>
</div>
