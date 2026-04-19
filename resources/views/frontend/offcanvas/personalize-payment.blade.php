{{-- Personal Payment --}}
<div class="offcanvas offcanvas-end  w-lg-50 w-100" tabindex="-1" id="offcanvasRight"
        aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header w-100">
        <h3
            class="border-bottom h5  ms-1 mb-4 float-start border-theme border-thick d-flex justify-content-between align-items-center w-100 ">
            Personalize payment
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                class="close closeBtn text-large btn btn-link" fdprocessedid="9uo7t">×</button>
        </h3>

    </div>
    <div class="  px-4 h-100">
        <div class="slideout-padding-container h-100">
            <div class="sc-802f3b9e-2 cbIQov">
                <div class="text-center"><small class="font-weight-bold opacity-75">2020 DODGE
                        CHARGER SXT</small>
                    <div class="text-xlarge my-1 text-primary"><b data-cy="paymentcalc-amount">$294</b><span
                            class="text-muted"> / mo</span></div>Est.
                    payment for 75 months at 6.79% APR
                </div>
                <div class="position-relative pt-3">
                    <div class="sc-802f3b9e-1 gNhWIU noBlur">
                        <div class="pt-3 border-top">
                        </div>
                        <div class="d-flex mb-2 align-items-center">
                            <b>Credit score: <span id="credit-score">750</span></b>
                            <a href="#" target="_blank" rel="noopener noreferrer" class="ms-auto text-primary"
                                data-cy="paymentcalc-print" title="Print payment details">
                                <span class="d-inline-block me-1">
                                    <svg height="12" width="12" viewBox="0 0 16 16" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10.8L3.2 8l-1.4 1.4L6 13.6l8-8-1.4-1.4L6 10.8z" />
                                    </svg>
                                </span>
                                Print
                            </a>
                        </div>
                        <div class="mb-3">

                            <div class="slider-wrapper">
                                <div class="slider-track price-financing-slider">

                                    <div class="slider-handle" id="handle-min" tabindex="0" aria-valuemax="32000"
                                        aria-valuemin="5000" aria-valuenow="5000" draggable="false" role="slider">
                                        <div class="slider-handle-bar"></div>
                                    </div>
                                    <div class="slider-handle slider-handle-top" id="handle-max" tabindex="0"
                                        aria-valuemax="32000" aria-valuemin="5000" aria-valuenow="32000"
                                        draggable="false" role="slider">
                                        <div class="slider-handle-bar"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted row">
                                <div class="text-start col-6">590</div>
                                <div class="text-end col-6">890</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="mb-3 mb-md-4">
                                    <label class="form-label">Unit price</label>
                                    <div class="input-group equal-width-group">
                                        <span class="bg-lighter prepend input-group-text"><b
                                                class="mx-auto">$</b></span>
                                        <input class="form-control border-radius-0" placeholder="10,000" min="1000"
                                            max="1000000" required type="text" value="19,900" name="amount"
                                            inputmode="numeric" id="unitPrice">
                                        <span class="bg-lighter append input-group-text" role="button"
                                            id="decrease"><b class="mx-auto">-</b></span>
                                        <span class="bg-lighter append input-group-text" role="button"
                                            id="increase"><b class="mx-auto">+</b></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="mb-3 mb-md-4"><label class="form-label">Loan
                                        months</label><select data-cy="paymentcalc-state" name="state"
                                        class="custom-select form-select" fdprocessedid="k1oysf">
                                        <option value="">Select...</option>
                                        <option value="36">36 months</option>
                                        <option value="48">48 months</option>
                                        <option value="60">60 months</option>
                                        <option value="72">72 months</option>
                                        <option value="75">75 months</option>
                                        <option value="84">84 months</option>
                                    </select></div>
                            </div>
                            <div class="d-none col-md-4 col-6">
                                <div class="mb-3 mb-md-4"><label class="form-label">Zip code
                                        for tax</label><input data-cy="formcontrol-salestaxpostalcode"
                                        class="form-control " maxlength="5" minlength="5" placeholder="_ _ _ _ _"
                                        type="text" value="37167" name="salestaxpostalcode" inputmode="numeric">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="border-top">
                                    <div id="toggleAmountDown"
                                        class="py-3 cursor-pointer d-flex align-items-center">
                                        <span id="amountIcon"
                                            class="d-inline-block faIcon ofa-regular ofa-square-minus me-2 mt-n1 text-primary float-left">
                                            <i class="fa-solid fa-sm fa-square-plus"></i>
                                        </span>Amount Down
                                    </div>
                                    <div id="amountDownContent">

                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="mb-3 mb-md-4"><label class="form-label">Down
                                                        payment</label>
                                                    <div role="group" class="d-flex btn-group">
                                                        <button type="button" data-cy="paymentcalc-downPref"
                                                            class="w-50 py-2 active btn btn-default"
                                                            fdprocessedid="zswem5">Cash</button><button
                                                            type="button" data-cy="paymentcalc-downPref"
                                                            class="w-50 py-2  btn btn-default"
                                                            fdprocessedid="hrqt4l">Percentage</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="mb-0"><label class="form-label">Amount
                                                        Down</label>
                                                    <div class="mb-3 mb-md-4 input-group"><span
                                                            class="bg-lighter prepend input-group-text"><b
                                                                class="mx-auto">$</b></span><input
                                                            class="form-control border-radius-0"
                                                            placeholder="10,000" max="1000000" required=""
                                                            type="text" value="1,990" name="down_amount"
                                                            inputmode="numeric" fdprocessedid="85h68"><span
                                                            class="bg-lighter append   input-group-text"
                                                            role="button"><b class="mx-auto">-</b></span><span
                                                            class="bg-lighter append  input-group-text"
                                                            role="button"><b class="mx-auto">+</b></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="calc-tradein col-12">
                                <div class="border-top">
                                    <div id="toggleTrade" class="py-3 cursor-pointer d-flex align-items-center">
                                        <span id="tradeIcon"
                                            class="d-inline-block faIcon ofa-regular ofa-square-minus me-2 mt-n1 text-primary float-left">
                                            <i class="fa-solid fa-sm fa-square-plus"></i>
                                        </span>Trade-in Value
                                    </div>
                                    <div id="tradeContent">

                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="mb-0">
                                                    <label class="form-label">Est. Trade Value</label>
                                                    <div class="mb-3 mb-md-4 input-group">
                                                        <span class="bg-lighter prepend input-group-text"><b
                                                                class="mx-auto">$</b></span>
                                                        <input class="form-control border-radius-0"
                                                            placeholder="10,000" max="1000000" min="0" required
                                                            type="text" value="3,000" name="tradeinamount"
                                                            inputmode="numeric" id="tradeValueInput">
                                                        <span class="bg-lighter append input-group-text"
                                                            role="button" id="decrement"><b
                                                                class="mx-auto">-</b></span>
                                                        <span class="bg-lighter append input-group-text"
                                                            role="button" id="increment"><b
                                                                class="mx-auto">+</b></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="mb-0">
                                                    <label class="form-label">Remaining Loan Balance</label>
                                                    <div class="mb-3 mb-md-4 input-group">
                                                        <span class="bg-lighter prepend input-group-text"><b
                                                                class="mx-auto">$</b></span>
                                                        <input class="form-control border-radius-0"
                                                            placeholder="5,000" max="1000000" min="0" required
                                                            type="text" value="5,000" name="tradeinremainingbalance"
                                                            inputmode="numeric" id="loanBalanceInput">
                                                        <span class="bg-lighter append input-group-text"
                                                            role="button" id="loanDecrement"><b
                                                                class="mx-auto">-</b></span>
                                                        <span class="bg-lighter append input-group-text"
                                                            role="button" id="loanIncrement"><b
                                                                class="mx-auto">+</b></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="py-4 border-top text-center mt-1">
                    <h4>Save an hour at the dealership</h4>
                    <p>With our lender relationships, we can often beat your bank or credit union's rate. Get your
                        new car faster with an online approval. Estimated monthly payment does not include title and
                        license fees. Monthly payment will be higher.</p>
                    <button type="button" data-cy="btn-confirmation"
                        class="cursor-pointer d-block btn btn-primary mx-auto btn-lg" fdprocessedid="zvhgdl">Get
                        approved
                        <span class="ms-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 1 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" />
                            </svg>
                        </span>
                    </button>
                </div>
                <hr class="my-4">
                <div class="text-small text-muted">
                    <p>The payment estimator is not an advertisement or offer for specific terms of credit and
                        actual terms may vary. Payment amounts presented are for illustrative purposes only and may
                        not be available. Not all models are available in all states. Actual vehicle price may vary
                        by Dealer. The Estimated Monthly Payment amount calculated is based on the variables
                        entered, the price of the vehicle you entered, the term you select, the down payment you
                        enter, the Annual Percentage Rate (APR) you select, and any net trade-in amount. The payment
                        estimate displayed does not include taxes, title, license and/or registration fees. Payment
                        amount is for illustrative purposes only. Actual prices may vary by Dealer. Payment amounts
                        may be different due to various factors such as fees, specials, rebates, term, down payment,
                        APR, net trade-in, and applicable tax rate. Actual APR is based on available finance
                        programs and the creditworthiness of the customer. Not all customers will qualify for credit
                        or for the lowest rate. Please contact an authorized dealer for actual rates, program
                        details and actual terms.</p>
                </div>
            </div>
        </div>
    </div>
</div>