{{-- ═══════════════════════════════════════════════════════════════════════
     Get Approved — Offcanvas
     ID prefix: gao- (get approved offcanvas) — no conflict with page version
     JS: resources/js/frontend/pages/get-approved-offcanvas.js
═══════════════════════════════════════════════════════════════════════ --}}
<div class="offcanvas offcanvas-end w-lg-50 w-100" tabindex="-1" id="getApproved"
    aria-labelledby="gaoOffcanvasLabel">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="offcanvas-header w-100">
        <h3 class="border-bottom h5 ms-1 mb-4 float-start border-theme border-thick d-flex justify-content-between align-items-center w-100">
            Get Approved
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                class="close closeBtn text-large btn btn-link">×</button>
        </h3>
    </div>

    {{-- ── Body ────────────────────────────────────────────────────────────── --}}
    <div class="offcanvas-body px-4 pt-0">

        {{-- Hidden: borrower type --}}
        <input type="hidden" id="gao-borrower-type" name="borrower_type" value="">

        {{-- Error Summary --}}
        <div id="gao-error-summary" class="hidden alert alert-danger mb-3">
            <strong><i class="fa-solid fa-circle-exclamation me-2"></i>Please fix the following errors:</strong>
            <ul id="gao-error-list" class="mb-0 mt-2"></ul>
        </div>

        {{-- ══ STEP HEADERS ══════════════════════════════════════════════════ --}}

        <div id="gao-header-1" class="wizardstep clearfix py-3 wizardstep-active border-bottom">
            <b class="text-primary">Contact Information</b>
            <span class="text-muted float-end wizardstep-indicator gao-indicator" id="gao-ind-1">1 / 4</span>
        </div>

        {{-- ══ STEP 1 — Contact Information ═════════════════════════════════ --}}
        <div id="gao-step-1">

            {{-- Choice --}}
            <div id="gao-choice" class="py-4 text-center">
                <p>Are you applying for financing for yourself, or jointly with a co-borrower?</p>
                <div class="mt-4 row">
                    <div class="col-sm-6 col-12 mb-3">
                        <button type="button" id="gao-btn-myself"
                            class="w-100 text-start btn btn-primary btn-lg">
                            <span class="d-inline-block me-3"><i class="fa-solid fa-check fa-lg"></i></span>
                            For myself
                        </button>
                    </div>
                    <div class="col-sm-6 col-12 mb-3">
                        <button type="button" id="gao-btn-coborrower"
                            class="w-100 text-start btn btn-primary btn-lg">
                            <span class="d-inline-block me-3"><i class="fa-solid fa-plus fa-lg"></i></span>
                            With a co-borrower
                        </button>
                    </div>
                </div>
            </div>

            {{-- Contact form (shared single + joint) --}}
            <div id="gao-contact-form" class="hidden py-2">

                <button type="button" id="gao-btn-change-type" class="mt-2 mb-4 btn btn-light btn-default">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    <span id="gao-change-type-label">Change to joint application</span>
                </button>

                <div class="row">

                    {{-- First Name --}}
                    <div class="col-sm-4">
                        <div class="mb-3">
                            <label class="form-label" for="gao-first-name">
                                First Name <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="text" id="gao-first-name" name="first_name"
                                class="form-control" placeholder="First" minlength="2" maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- Last Name --}}
                    <div class="col-sm-4">
                        <div class="mb-3">
                            <label class="form-label" for="gao-last-name">
                                Last Name <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="text" id="gao-last-name" name="last_name"
                                class="form-control" placeholder="Last" minlength="2" maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- Suffix --}}
                    <div class="col-sm-4">
                        <div class="mb-3">
                            <label class="form-label" for="gao-suffix">Suffix</label>
                            <select id="gao-suffix" name="suffix" class="form-select">
                                <option value="">Select...</option>
                                @foreach(['Sr','Jr','III','IV','V','VI','VII','VIII','IX','X'] as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label" for="gao-email">
                                Email Address <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="email" id="gao-email" name="email"
                                class="form-control" placeholder="you@email.com"
                                autocomplete="off" maxlength="255">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label" for="gao-phone">
                                Phone Number <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="tel" id="gao-phone" name="phone"
                                class="form-control" placeholder="(___) ___-____" inputmode="numeric">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- Communication Preference --}}
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">
                                What is the best way to contact you?
                                <strong class="text-danger ps-1">*</strong>
                            </label>
                            @foreach(['email' => 'Email', 'text' => 'Text Message', 'phone' => 'Phone Call'] as $val => $label)
                                <label class="custom-control custom-radio p-0">
                                    <input class="form-check-input gao-commpref" type="radio"
                                        name="gao_commpref" value="{{ $val }}">
                                    <span class="custom-control-label">{{ $label }}</span>
                                </label>
                            @endforeach
                            <div class="invalid-feedback d-block" id="gao-commpref-error"></div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-12"><hr></div>
                    <div class="col-sm-6 col-12">
                        <button type="button" id="gao-continue-1"
                            class="btn btn-primary d-flex justify-content-between w-100">
                            Continue <i class="fa-solid fa-angle-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2 Header --}}
        <div id="gao-header-2" class="wizardstep clearfix py-3 border-bottom">
            <b class="text-muted">Current Address</b>
            <span class="text-muted float-end wizardstep-indicator gao-indicator" id="gao-ind-2">2 / 4</span>
        </div>

        {{-- ══ STEP 2 — Current Address ══════════════════════════════════════ --}}
        <div id="gao-step-2" class="hidden py-4">
            <div class="row">

                <div class="col-sm-12">
                    <div class="mb-3">
                        <label class="form-label" for="gao-address">
                            Street Address <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-address" name="address"
                            class="form-control" placeholder="Include apt. or suite #" autocomplete="new-password">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-city">
                            City <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-city" name="city"
                            class="form-control" placeholder="City" maxlength="100">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-state">
                            State <strong class="text-danger ps-1">*</strong>
                        </label>
                        <select id="gao-state" name="state" class="form-select">
                            <option value="">Select...</option>
                            @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $abbr => $name)
                                <option value="{{ $abbr }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-postalcode">
                            Zip Code <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-postalcode" name="postalcode"
                            class="form-control" placeholder="_ _ _ _ _" maxlength="5" inputmode="numeric">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-housing">
                            Type of Residence <strong class="text-danger ps-1">*</strong>
                        </label>
                        <select id="gao-housing" name="housing" class="form-select">
                            <option value="">Select...</option>
                            <option value="Rent">Rented</option>
                            <option value="Own">Mortgaged</option>
                            <option value="Own_Freeandclear">Owned (free &amp; clear)</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-currentaddressperiod">
                            How many years have you lived here?
                            <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="number" id="gao-currentaddressperiod" name="currentaddressperiod"
                            class="form-control" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-housingpay">
                            Monthly rent/mortgage payment <strong class="text-danger ps-1">*</strong>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-lighter">$</span>
                            <input type="text" id="gao-housingpay" name="housingpay"
                                class="form-control" placeholder="0.00" inputmode="numeric">
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-12"><div class="pt-3 border-top"></div></div>
                <div class="col-sm-6 col-12">
                    <button type="button" id="gao-continue-2"
                        class="text-start w-100 btn btn-primary d-flex justify-content-between">
                        Continue <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Step 3 Header --}}
        <div id="gao-header-3" class="wizardstep clearfix py-3 border-bottom">
            <b class="text-muted">Employment Information</b>
            <span class="text-muted float-end wizardstep-indicator gao-indicator" id="gao-ind-3">3 / 4</span>
        </div>

        {{-- ══ STEP 3 — Employment Information ══════════════════════════════ --}}
        <div id="gao-step-3" class="hidden py-4">
            <div class="row">

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-employer">
                            Employer Name <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-employer" name="employer" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-position">
                            Job Title <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-position" name="position" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-wphone">
                            Employer Phone Number <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="tel" id="gao-wphone" name="wphone"
                            class="form-control" placeholder="(___) ___-____" inputmode="numeric">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-mincome">
                            Monthly Income <strong class="text-danger ps-1">*</strong>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-lighter">$</span>
                            <input type="text" id="gao-mincome" name="mincome"
                                class="form-control" placeholder="0.00" inputmode="numeric">
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-years">
                            Years worked here <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="number" id="gao-years" name="years"
                            class="form-control" placeholder="0" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-months">
                            Months worked here <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="number" id="gao-months" name="months"
                            class="form-control" placeholder="0" min="0" max="11">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Other Income --}}
                <div class="col-sm-12">
                    <div class="mb-3 border-top pt-3">
                        <label class="form-label">
                            Do you have another source of income?
                            <strong class="text-danger ps-1">*</strong>
                        </label>
                        <div>
                            <label class="custom-control custom-radio p-0">
                                <input class="form-check-input" type="radio" name="gao_other" value="Y" id="gao-other-yes">
                                <span class="custom-control-label">Yes</span>
                            </label>
                            <label class="custom-control custom-radio p-0">
                                <input class="form-check-input" type="radio" name="gao_other" value="N" id="gao-other-no">
                                <span class="custom-control-label">No</span>
                            </label>
                        </div>
                        <div class="invalid-feedback d-block" id="gao-other-error"></div>
                    </div>
                </div>

                {{-- Other Income Fields --}}
                <div id="gao-other-income-fields" class="hidden col-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label" for="gao-otherincomeexpln">Type of Income</label>
                                <select id="gao-otherincomeexpln" name="otherincomeexpln" class="form-select">
                                    <option value="">Select...</option>
                                    @foreach([
                                        'Alimony, child support or separate maintenance',
                                        'Insurance payments','Investments','Public assistance',
                                        'Regular allowance','Rental properties','Retirement',
                                        'Self-employment','Social security','Workers compensation',
                                        'Military allowances',
                                        'Other household income (partner, spouse, etc.)',
                                        'Other (royalty payments, trust payouts)',
                                    ] as $incomeType)
                                        <option value="{{ $incomeType }}">{{ $incomeType }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label" for="gao-otherincome">Gross Income</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-lighter">$</span>
                                    <input type="text" id="gao-otherincome" name="otherincome"
                                        class="form-control" placeholder="0.00" inputmode="numeric">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-12"><div class="pt-3 border-top"></div></div>
                <div class="col-sm-6 col-12">
                    <button type="button" id="gao-continue-3"
                        class="text-start w-100 btn btn-primary d-flex justify-content-between">
                        Continue <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ══ CO-BORROWER HEADERS (joint only) ══════════════════════════════ --}}

        <div id="gao-header-4-joint" class="hidden wizardstep clearfix py-3 border-bottom">
            <b class="text-muted">Co-borrower Information</b>
            <span class="text-muted float-end wizardstep-indicator gao-indicator" id="gao-ind-4-joint">4 / 6</span>
        </div>

        {{-- ══ STEP 4j — Co-borrower Information ════════════════════════════ --}}
        <div id="gao-step-4-joint" class="hidden py-4">
            <div class="row">

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spousefirstname">
                            First Name <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-spousefirstname" name="spousefirstname"
                            class="form-control" placeholder="First" maxlength="100">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spouselastname">
                            Last Name <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-spouselastname" name="spouselastname"
                            class="form-control" placeholder="Last" maxlength="100">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spousesuffix">Suffix</label>
                        <select id="gao-spousesuffix" name="spousesuffix" class="form-select">
                            <option value="">Select...</option>
                            @foreach(['Sr','Jr','III','IV','V','VI','VII','VIII','IX','X'] as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spousephone">
                            Phone Number <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="tel" id="gao-spousephone" name="spousephone"
                            class="form-control" placeholder="(___) ___-____" inputmode="numeric">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spouseaddress">
                            Street Address <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-spouseaddress" name="spouseaddress"
                            class="form-control" placeholder="Include apt. or suite #" autocomplete="new-password">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spousecity">
                            City <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-spousecity" name="spousecity"
                            class="form-control" placeholder="City" maxlength="100">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spousestate">
                            State <strong class="text-danger ps-1">*</strong>
                        </label>
                        <select id="gao-spousestate" name="spousestate" class="form-select">
                            <option value="">Select...</option>
                            @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $abbr => $name)
                                <option value="{{ $abbr }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spousepostalcode">
                            Zip Code <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-spousepostalcode" name="spousepostalcode"
                            class="form-control" placeholder="_ _ _ _ _" maxlength="5" inputmode="numeric">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spousehousing">
                            Type of Residence <strong class="text-danger ps-1">*</strong>
                        </label>
                        <select id="gao-spousehousing" name="spousehousing" class="form-select">
                            <option value="">Select...</option>
                            <option value="Rent">Rented</option>
                            <option value="Own">Mortgaged</option>
                            <option value="Own_Freeandclear">Owned (free &amp; clear)</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spouseaddressperiod">
                            How many years have you lived here?
                            <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="number" id="gao-spouseaddressperiod" name="spouseaddressperiod"
                            class="form-control" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spousehousingpay">
                            Monthly rent/mortgage payment <strong class="text-danger ps-1">*</strong>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-lighter">$</span>
                            <input type="text" id="gao-spousehousingpay" name="spousehousingpay"
                                class="form-control" placeholder="0.00" inputmode="numeric">
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-12"><div class="pt-3 border-top"></div></div>
                <div class="col-sm-6 col-12">
                    <button type="button" id="gao-continue-4-joint"
                        class="text-start w-100 btn btn-primary d-flex justify-content-between">
                        Continue <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Step 5j Header --}}
        <div id="gao-header-5-joint" class="hidden wizardstep clearfix py-3 border-bottom">
            <b class="text-muted">Co-borrower Employment</b>
            <span class="text-muted float-end wizardstep-indicator gao-indicator" id="gao-ind-5-joint">5 / 6</span>
        </div>

        {{-- ══ STEP 5j — Co-borrower Employment ═════════════════════════════ --}}
        <div id="gao-step-5-joint" class="hidden py-4">
            <div class="row">

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spouseemployer">
                            Employer Name <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-spouseemployer" name="spouseemployer" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spouseposition">
                            Job Title <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="text" id="gao-spouseposition" name="spouseposition" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spouseworkphone">
                            Employer Phone Number <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="tel" id="gao-spouseworkphone" name="spouseworkphone"
                            class="form-control" placeholder="(___) ___-____" inputmode="numeric">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spouseincome">
                            Monthly Income <strong class="text-danger ps-1">*</strong>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-lighter">$</span>
                            <input type="text" id="gao-spouseincome" name="spouseincome"
                                class="form-control" placeholder="0.00" inputmode="numeric">
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label" for="gao-spouseyears">
                            Years worked here <strong class="text-danger ps-1">*</strong>
                        </label>
                        <input type="number" id="gao-spouseyears" name="spouseyears"
                            class="form-control" placeholder="0" min="0">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="mb-3 border-top pt-3">
                        <label class="form-label">
                            Does co-borrower have another source of income?
                            <strong class="text-danger ps-1">*</strong>
                        </label>
                        <div>
                            <label class="custom-control custom-radio p-0">
                                <input class="form-check-input" type="radio" name="gao_spouseother" value="Y" id="gao-spouseother-yes">
                                <span class="custom-control-label">Yes</span>
                            </label>
                            <label class="custom-control custom-radio p-0">
                                <input class="form-check-input" type="radio" name="gao_spouseother" value="N" id="gao-spouseother-no">
                                <span class="custom-control-label">No</span>
                            </label>
                        </div>
                        <div class="invalid-feedback d-block" id="gao-spouseother-error"></div>
                    </div>
                </div>

                {{-- Co-borrower Other Income Fields --}}
                <div id="gao-spouse-other-income-fields" class="hidden col-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Type of Income</label>
                                <select name="spouseotherincomeexpln" class="form-select">
                                    <option value="">Select...</option>
                                    @foreach([
                                        'Alimony, child support or separate maintenance',
                                        'Insurance payments','Investments','Public assistance',
                                        'Regular allowance','Rental properties','Retirement',
                                        'Self-employment','Social security','Workers compensation',
                                        'Military allowances',
                                        'Other household income (partner, spouse, etc.)',
                                        'Other (royalty payments, trust payouts)',
                                    ] as $incomeType)
                                        <option value="{{ $incomeType }}">{{ $incomeType }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Gross Income</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-lighter">$</span>
                                    <input type="text" name="spouseotherincome"
                                        class="form-control" placeholder="0.00" inputmode="numeric">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-12"><div class="pt-3 border-top"></div></div>
                <div class="col-sm-6 col-12">
                    <button type="button" id="gao-continue-5-joint"
                        class="text-start w-100 btn btn-primary d-flex justify-content-between">
                        Continue <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ══ LAST STEP HEADER — Consent & Verification ═════════════════════ --}}
        <div id="gao-header-last" class="wizardstep clearfix py-3 border-bottom">
            <b class="text-muted">Consent &amp; Verification</b>
            <span class="text-muted float-end wizardstep-indicator gao-indicator" id="gao-ind-last">4 / 4</span>
        </div>

        {{-- ══ LAST STEP — Consent & Verification ════════════════════════════ --}}
        <div id="gao-step-last" class="hidden py-4">
            <p><strong>Almost there...</strong></p>
            <p class="pb-3 border-bottom">
                To submit your application, we need to verify your social security number (SSN) and date of birth (DOB).
            </p>

            {{-- Primary Borrower --}}
            <div class="mb-4">
                <h6 id="gao-consent-borrower-title" class="mb-3 text-primary">Your Information</h6>

                <div class="row">
                    <div class="col-sm-5">
                        <div class="mb-3">
                            <label class="form-label" for="gao-ssn">
                                Social Security Number <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="text" id="gao-ssn" name="ssn" class="form-control" placeholder="___-__-____">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="mb-3">
                            <label class="form-label">
                                Date of Birth <strong class="text-danger ps-1">*</strong>
                            </label>
                            <div class="row">
                                <div class="col-4">
                                    <select name="month" id="gao-month" class="form-select">
                                        <option value="">Month</option>
                                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mi => $mn)
                                            <option value="{{ $mi + 1 }}">{{ $mn }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-4">
                                    <select name="day" id="gao-day" class="form-select">
                                        <option value="">Day</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endfor
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-4">
                                    <select name="year" id="gao-year" class="form-select">
                                        <option value="">Year</option>
                                        @for($y = 2010; $y >= 1930; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="pt-3 border-top"><strong>Authorization</strong></p>
                <p class="border-bottom pb-3 mb-3">
                    By clicking I Agree and typing your name, you confirm you have read the
                    <a href="/privacy" target="_blank">Privacy Policy</a> and
                    <a href="/electronic-signature-disclosure" target="_blank">Electronic Signature Disclosure</a>,
                    and authorize <strong>{{ $dealerName ?? config('app.name') }}</strong> under the Fair Credit Reporting Act
                    to obtain your personal credit profile.
                </p>

                <div class="row">
                    <div class="col-sm-8">
                        <div class="mb-2">
                            <label class="form-label">Full name for electronic signature</label>
                            <input type="text" id="gao-singlesignature" name="singlesignature"
                                class="form-control" placeholder="First and last name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="p-3 bg-lighter border rounded mb-2">
                            <label class="mb-0">
                                <input type="checkbox" id="gao-singleconsent" name="singleconsent" class="me-2">
                                <strong>I agree</strong>
                            </label>
                        </div>
                        <div class="invalid-feedback d-block" id="gao-singleconsent-error"></div>
                    </div>
                </div>

                {{-- Regulation B (always shown) --}}
                <div id="gao-regulationb-primary-row" class="mt-3">
                    <div class="p-3 bg-lighter border rounded">
                        <label class="mb-0">
                            <input type="checkbox" id="gao-regulationbprimary" name="regulationbprimary" class="me-2">
                            <strong>I acknowledge the joint application (Regulation B)</strong>
                        </label>
                    </div>
                    <div class="invalid-feedback d-block" id="gao-regulationbprimary-error"></div>
                </div>
            </div>

            {{-- Co-borrower Consent (joint only) --}}
            <div id="gao-coborrower-consent-section" class="hidden mt-4 pt-4 border-top">
                <h6 class="mb-3 text-primary">Co-borrower Information</h6>

                <div class="row">
                    <div class="col-sm-5">
                        <div class="mb-3">
                            <label class="form-label" for="gao-spousessn">
                                Social Security Number <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="text" id="gao-spousessn" name="spousessn"
                                class="form-control" placeholder="___-__-____">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="mb-3">
                            <label class="form-label">
                                Date of Birth <strong class="text-danger ps-1">*</strong>
                            </label>
                            <div class="row">
                                <div class="col-4">
                                    <select name="spousemonth" id="gao-spousemonth" class="form-select">
                                        <option value="">Month</option>
                                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mi => $mn)
                                            <option value="{{ $mi + 1 }}">{{ $mn }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select name="spouseday" id="gao-spouseday" class="form-select">
                                        <option value="">Day</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select name="spouseyear" id="gao-spouseyear" class="form-select">
                                        <option value="">Year</option>
                                        @for($y = 2010; $y >= 1930; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="pt-3 border-top"><strong>Co-borrower Authorization</strong></p>
                <p class="border-bottom pb-3 mb-3">
                    The co-borrower confirms they have read the
                    <a href="/privacy" target="_blank">Privacy Policy</a> and
                    <a href="/electronic-signature-disclosure" target="_blank">Electronic Signature Disclosure</a>,
                    and authorizes <strong>{{ $dealerName ?? config('app.name') }}</strong> to obtain their credit profile.
                </p>

                <div class="row">
                    <div class="col-sm-8">
                        <div class="mb-2">
                            <label class="form-label">Full name for electronic signature</label>
                            <input type="text" id="gao-jointsignature" name="jointsignature"
                                class="form-control" placeholder="Co-borrower first and last name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="p-3 bg-lighter border rounded mb-2">
                            <label class="mb-0">
                                <input type="checkbox" id="gao-jointconsent" name="jointconsent" class="me-2">
                                <strong>I agree</strong>
                            </label>
                        </div>
                        <div class="invalid-feedback d-block" id="gao-jointconsent-error"></div>
                        <div class="p-3 bg-lighter border rounded mt-2">
                            <label class="mb-0">
                                <input type="checkbox" id="gao-regulationbjoint" name="regulationbjoint" class="me-2">
                                <strong>I acknowledge the joint application (Regulation B)</strong>
                            </label>
                        </div>
                        <div class="invalid-feedback d-block" id="gao-regulationbjoint-error"></div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="row mt-4">
                <div class="col-12"><div class="pt-3 border-top"></div></div>
                <div class="col-sm-8 col-12">
                    <button type="button" id="gao-submit-btn"
                        class="w-100 text-start btn btn-primary d-flex justify-content-between">
                        <span class="gao-btn-label">Finish Application</span>
                        <i class="fa-solid fa-check gao-btn-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ══ SUCCESS STATE ══════════════════════════════════════════════════ --}}
        <div id="gao-success" class="hidden">
            <div class="py-4 px-0 px-sm-4 text-center border-bottom">

                <div class="my-1">
                    <span class="d-inline-block h1 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="55" height="55"
                            fill="currentColor" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.08 0l3.992-3.992a.75.75 0 1 0-1.06-1.06L7.5 9.44 5.98 7.922a.75.75 0 1 0-1.06 1.06l2.05 2.048z"/>
                        </svg>
                    </span>
                </div>

                <div class="h2 text-primary mt-2">Credit Application Received</div>
                <p class="mt-2">
                    Thank you for trusting {{ $dealerName ?? config('app.name') }} with your financing needs.
                    We will review your application and reach out shortly with next steps.
                </p>

            </div>

            {{-- NPS Survey --}}
            <div class="pt-4 pb-3 px-0 text-center" id="gao-nps-section">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="mb-2 opacity-75">How would you rate your experience on our website?</div>
                        <div class="mb-4 cursor-pointer" id="gao-star-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star" data-value="{{ $i }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                    </svg>
                                </span>
                            @endfor
                        </div>
                        <div class="hidden" id="gaoNpsOptions">
                            <div class="mb-4 text-start">
                                <span class="badge bg-secondary p-1 me-2">OPTIONAL</span>
                                <label class="form-label opacity-75 mb-3">How could we improve your experience?</label>
                                <textarea class="form-control"
                                    placeholder="Any feedback or comments are greatly appreciated"
                                    rows="2"></textarea>
                            </div>
                            <button type="button" class="btn btn-primary gao-nps-submit-btn">
                                <i class="fa-solid fa-check me-2"></i>Share Your Feedback
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Close Button --}}
            <div class="row">
                <div class="text-center col-md-4 col-12 offset-md-4">
                    <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                        class="w-100 btn btn-primary btn-lg border-dark mb-5 cursor-pointer">
                        <span class="d-inline-block me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" viewBox="0 0 16 16">
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </span>
                        Close Window
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
