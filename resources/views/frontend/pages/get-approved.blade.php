@extends('layouts.frontend.app')

@section('title', __('Get Approved') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/get-approved.css',
        'resources/js/frontend/pages/get-approved.js'
    ])
@endpush

@section('page-content')
    <div class="d-block h-63 d-xl-none" id="mobile-nav-spacer"></div>

    <div class="page-template-schedule-service" role="main">
        <header class="sc-5a5d3415-0 jHTnHg" id="interior-page-header" title="Auto financing in Smyrna, TN">
            <div class="position-relative container">
                <div>
                    <h1 class="m-0 text-white py-3 text-center" id="page_h1">Auto financing in Smyrna, TN</h1>
                </div>
            </div>
        </header>

        <div class="bg-white pt-3 pt-lg-5">
            <div class="sc-1a7ba87f-0 cElement cContainer container">
                <h2 class="text-center">Simple financing that puts you in the driver's seat at {{ $dealerName ?? config('app.name') }}</h2>
                <div class="sc-1a7ba87f-0 eIywFy cElement cContainer container">
                    <p class="text-center">At {{ $dealerName ?? config('app.name') }}, we make financing your next vehicle straightforward,
                        fast, and tailored to your life in Middle Tennessee.</p>
                    <div class="sc-1a7ba87f-0 d-none d-sm-block cElement cContainer container">
                        <hr class="cElement cDivider my-5">
                    </div>
                </div>

                <div class="sc-1a7ba87f-0 eIywFy cElement cContainer container">
                    <div class="px-4 pt-0 pb-4 border rounded bg-white">

                        {{-- ── HIDDEN: borrower type ── --}}
                        <input type="hidden" id="ga-borrower-type" name="borrower_type" value="">

                        {{-- ════════════════════════════════════════════════════════
                             ERROR SUMMARY (shown on submit attempt with errors)
                        ════════════════════════════════════════════════════════ --}}
                        <div id="ga-error-summary" class="hidden alert alert-danger mt-3 mb-0">
                            <strong><i class="fa-solid fa-circle-exclamation me-2"></i>Please fix the following errors:</strong>
                            <ul id="ga-error-list" class="mb-0 mt-2"></ul>
                        </div>

                        {{-- ════════════════════════════════════════════════════════
                             STEP HEADERS — dynamically updated by JS
                        ════════════════════════════════════════════════════════ --}}

                        {{-- Step 1 Header --}}
                        <div id="ga-header-1" class="wizardstep clearfix py-3 wizardstep-active border-bottom">
                            <b class="text-primary">Contact Information</b>
                            <span class="text-muted float-end wizardstep-indicator ga-indicator" id="ga-ind-1">1 / 4</span>
                        </div>

                        {{-- ════════════════════════════════════════════════════════
                             STEP 1 — Contact Information
                        ════════════════════════════════════════════════════════ --}}
                        <div id="ga-step-1" class="px-sm-3">

                            {{-- Choice section --}}
                            <div id="ga-choice" class="py-4 text-center">
                                <p>Are you applying for financing for yourself, or jointly with a co-borrower?</p>
                                <div class="mt-4 row">
                                    <div class="col-sm-6 col-12 mb-3">
                                        <button type="button" id="ga-btn-myself"
                                            class="w-100 text-start btn btn-primary btn-lg">
                                            <span class="d-inline-block me-3">
                                                <i class="fa-solid fa-check fa-lg"></i>
                                            </span>
                                            For myself
                                        </button>
                                    </div>
                                    <div class="col-sm-6 col-12 mb-3">
                                        <button type="button" id="ga-btn-coborrower"
                                            class="w-100 text-start btn btn-primary btn-lg">
                                            <span class="d-inline-block me-3">
                                                <i class="fa-solid fa-plus fa-lg"></i>
                                            </span>
                                            With a co-borrower
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Borrower contact form (shared for single + joint) --}}
                            <div id="ga-contact-form" class="hidden py-2">

                                <button type="button" id="ga-btn-change-type" class="mt-2 mb-4 btn btn-light btn-default">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    <span id="ga-change-type-label">Change to joint application</span>
                                </button>

                                <div class="row">

                                    {{-- First Name --}}
                                    <div class="col-sm-4">
                                        <div class="mb-3 mb-md-4">
                                            <label class="form-label" for="ga-first-name">
                                                First Name <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            <input type="text" id="ga-first-name" name="first_name"
                                                class="form-control" placeholder="First"
                                                minlength="2" maxlength="100" tabindex="1">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    {{-- Last Name --}}
                                    <div class="col-sm-4">
                                        <div class="mb-3 mb-md-4">
                                            <label class="form-label" for="ga-last-name">
                                                Last Name <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            <input type="text" id="ga-last-name" name="last_name"
                                                class="form-control" placeholder="Last"
                                                minlength="2" maxlength="100" tabindex="2">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    {{-- Suffix --}}
                                    <div class="col-sm-4">
                                        <div class="mb-3 mb-md-4">
                                            <label class="form-label" for="ga-suffix">Suffix</label>
                                            <select id="ga-suffix" name="suffix" class="form-select" tabindex="3">
                                                <option value="">Select...</option>
                                                @foreach(['Sr','Jr','III','IV','V','VI','VII','VIII','IX','X'] as $s)
                                                    <option value="{{ $s }}">{{ $s }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-sm-6">
                                        <div class="mb-3 mb-md-4">
                                            <label class="form-label" for="ga-email">
                                                Email Address <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            <input type="email" id="ga-email" name="email"
                                                class="form-control" placeholder="you@email.com"
                                                autocomplete="off" maxlength="255" tabindex="4">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    {{-- Phone --}}
                                    <div class="col-sm-6">
                                        <div class="mb-3 mb-md-4">
                                            <label class="form-label" for="ga-phone">
                                                Phone Number <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            <input type="tel" id="ga-phone" name="phone"
                                                class="form-control" placeholder="(###) ###-####"
                                                inputmode="numeric" maxlength="20" tabindex="5">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    {{-- Communication Preference --}}
                                    <div class="col-sm-12">
                                        <div class="mb-3 mb-md-4">
                                            <label class="form-label">
                                                What is the best way to contact you?
                                                <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            @foreach(['email' => 'Email', 'text' => 'Text Message', 'phone' => 'Phone Call'] as $val => $label)
                                                <label class="custom-control custom-radio p-0">
                                                    <input class="form-check-input ga-commpref" type="radio"
                                                        name="commpref" value="{{ $val }}">
                                                    <span class="custom-control-label">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                            <div class="invalid-feedback d-block" id="ga-commpref-error"></div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12"><hr></div>
                                    <div class="col-sm-6 col-12">
                                        <button type="button" id="ga-continue-1"
                                            class="btn btn-primary d-flex justify-content-between w-100">
                                            Continue
                                            <i class="fa-solid fa-angle-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 Header --}}
                        <div id="ga-header-2" class="wizardstep clearfix py-3 border-bottom">
                            <b class="text-muted">Current Address</b>
                            <span class="text-muted float-end wizardstep-indicator ga-indicator" id="ga-ind-2">2 / 4</span>
                        </div>

                        {{-- ════════════════════════════════════════════════════════
                             STEP 2 — Current Address
                        ════════════════════════════════════════════════════════ --}}
                        <div id="ga-step-2" class="hidden px-sm-3 py-4">
                            <div class="row">

                                {{-- Street Address --}}
                                <div class="col-sm-12">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-address">
                                            Street Address <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-address" name="address"
                                            class="form-control" placeholder="Include apt. or suite #"
                                            autocomplete="new-password" tabindex="1">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- City --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-city">
                                            City <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-city" name="city"
                                            class="form-control" placeholder="City" maxlength="100" tabindex="2">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- State --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-state">
                                            State <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <select id="ga-state" name="state" class="form-select" tabindex="3">
                                            <option value="">Select...</option>
                                            @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $abbr => $name)
                                                <option value="{{ $abbr }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Zip Code --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-postalcode">
                                            Zip Code <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-postalcode" name="postalcode"
                                            class="form-control" placeholder="_ _ _ _ _"
                                            maxlength="5" minlength="5" inputmode="numeric" tabindex="4">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Type of Residence --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-housing">
                                            Type of Residence <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <select id="ga-housing" name="housing" class="form-select" tabindex="5">
                                            <option value="">Select...</option>
                                            <option value="Rent">Rented</option>
                                            <option value="Own">Mortgaged</option>
                                            <option value="Own_Freeandclear">Owned (free &amp; clear)</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Years at Residence --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-currentaddressperiod">
                                            How many years have you lived here?
                                            <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="number" id="ga-currentaddressperiod" name="currentaddressperiod"
                                            class="form-control" min="0" tabindex="6">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Monthly Payment --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-housingpay">
                                            Monthly rent/mortgage payment
                                            <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-lighter">$</span>
                                            <input type="text" id="ga-housingpay" name="housingpay"
                                                class="form-control" placeholder="0.00"
                                                inputmode="numeric" tabindex="7">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12"><div class="pt-4 border-top"></div></div>
                                <div class="col-sm-6 col-12">
                                    <button type="button" id="ga-continue-2"
                                        class="text-start w-100 btn btn-primary btn-md d-flex justify-content-between">
                                        Continue
                                        <i class="fa-solid fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 Header --}}
                        <div id="ga-header-3" class="wizardstep clearfix py-3 border-bottom">
                            <b class="text-muted">Employment Information</b>
                            <span class="text-muted float-end wizardstep-indicator ga-indicator" id="ga-ind-3">3 / 4</span>
                        </div>

                        {{-- ════════════════════════════════════════════════════════
                             STEP 3 — Employment Information
                        ════════════════════════════════════════════════════════ --}}
                        <div id="ga-step-3" class="hidden px-sm-3 py-4">
                            <div class="row">

                                {{-- Employer --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-employer">
                                            Employer Name <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-employer" name="employer"
                                            class="form-control" tabindex="1">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Job Title --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-position">
                                            Job Title <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-position" name="position"
                                            class="form-control" tabindex="2">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Employer Phone --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-wphone">
                                            Employer Phone Number <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="tel" id="ga-wphone" name="wphone"
                                            class="form-control" placeholder="(###) ###-####"
                                            inputmode="numeric" tabindex="3">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Monthly Income --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-mincome">
                                            Monthly Income <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-lighter">$</span>
                                            <input type="text" id="ga-mincome" name="mincome"
                                                class="form-control" placeholder="0.00"
                                                inputmode="numeric" tabindex="4">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Years Worked --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-years">
                                            Years worked here <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="number" id="ga-years" name="years"
                                            class="form-control" placeholder="0" min="0" tabindex="5">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Months Worked --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-months">
                                            Months worked here <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="number" id="ga-months" name="months"
                                            class="form-control" placeholder="0" min="0" max="11" tabindex="6">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Other Income --}}
                                <div class="col-sm-12">
                                    <div class="mb-3 mb-md-4 border-top pt-3">
                                        <label class="form-label">
                                            Do you have another source of income?
                                            <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <div>
                                            <label class="custom-control custom-radio p-0">
                                                <input class="form-check-input" type="radio" name="other" value="Y" id="ga-other-yes">
                                                <span class="custom-control-label">Yes</span>
                                            </label>
                                            <label class="custom-control custom-radio p-0">
                                                <input class="form-check-input" type="radio" name="other" value="N" id="ga-other-no">
                                                <span class="custom-control-label">No</span>
                                            </label>
                                        </div>
                                        <div class="invalid-feedback d-block" id="ga-other-error"></div>
                                    </div>
                                </div>

                                {{-- Other Income Fields (conditional) --}}
                                <div id="ga-other-income-fields" class="hidden col-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-3 mb-md-4">
                                                <label class="form-label" for="ga-otherincomeexpln">Type of Income</label>
                                                <select id="ga-otherincomeexpln" name="otherincomeexpln" class="form-select">
                                                    <option value="">Select...</option>
                                                    @foreach([
                                                        'Alimony, child support or separate maintenance',
                                                        'Insurance payments',
                                                        'Investments',
                                                        'Public assistance',
                                                        'Regular allowance',
                                                        'Rental properties',
                                                        'Retirement',
                                                        'Self-employment',
                                                        'Social security',
                                                        'Workers compensation',
                                                        'Military allowances',
                                                        'Other household income (partner, spouse, etc.)',
                                                        'Other (royalty payments, trust payouts)',
                                                    ] as $incomeType)
                                                        <option value="{{ $incomeType }}">{{ $incomeType }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3 mb-md-4">
                                                <label class="form-label" for="ga-otherincome">Gross Income</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-lighter">$</span>
                                                    <input type="text" id="ga-otherincome" name="otherincome"
                                                        class="form-control" placeholder="0.00" inputmode="numeric">
                                                </div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12"><div class="pt-4 border-top"></div></div>
                                <div class="col-sm-6 col-12">
                                    <button type="button" id="ga-continue-3"
                                        class="text-start w-100 btn btn-primary btn-md d-flex justify-content-between">
                                        Continue
                                        <i class="fa-solid fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ════════════════════════════════════════════════════════
                             CO-BORROWER STEPS (joint only — steps 4 & 5)
                        ════════════════════════════════════════════════════════ --}}

                        {{-- Step 4 Header (joint only) --}}
                        <div id="ga-header-4-joint" class="hidden wizardstep clearfix py-3 border-bottom">
                            <b class="text-muted">Co-borrower Information</b>
                            <span class="text-muted float-end wizardstep-indicator ga-indicator" id="ga-ind-4-joint">4 / 6</span>
                        </div>

                        {{-- Step 4 — Co-borrower Information (joint only) --}}
                        <div id="ga-step-4-joint" class="hidden px-sm-3 py-4">
                            <div class="row">

                                {{-- Co First Name --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spousefirstname">
                                            First Name <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-spousefirstname" name="spousefirstname"
                                            class="form-control" placeholder="First" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Last Name --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spouselastname">
                                            Last Name <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-spouselastname" name="spouselastname"
                                            class="form-control" placeholder="Last" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Suffix --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spousesuffix">Suffix</label>
                                        <select id="ga-spousesuffix" name="spousesuffix" class="form-select">
                                            <option value="">Select...</option>
                                            @foreach(['Sr','Jr','III','IV','V','VI','VII','VIII','IX','X'] as $s)
                                                <option value="{{ $s }}">{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Co Phone --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spousephone">
                                            Phone Number <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="tel" id="ga-spousephone" name="spousephone"
                                            class="form-control" placeholder="(###) ###-####" inputmode="numeric">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Address --}}
                                <div class="col-sm-12">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spouseaddress">
                                            Street Address <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-spouseaddress" name="spouseaddress"
                                            class="form-control" placeholder="Include apt. or suite #" autocomplete="new-password">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co City --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spousecity">
                                            City <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-spousecity" name="spousecity"
                                            class="form-control" placeholder="City" maxlength="100">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co State --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spousestate">
                                            State <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <select id="ga-spousestate" name="spousestate" class="form-select">
                                            <option value="">Select...</option>
                                            @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $abbr => $name)
                                                <option value="{{ $abbr }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Zip --}}
                                <div class="col-sm-4">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spousepostalcode">
                                            Zip Code <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-spousepostalcode" name="spousepostalcode"
                                            class="form-control" placeholder="_ _ _ _ _" maxlength="5" inputmode="numeric">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Housing --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spousehousing">
                                            Type of Residence <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <select id="ga-spousehousing" name="spousehousing" class="form-select">
                                            <option value="">Select...</option>
                                            <option value="Rent">Rented</option>
                                            <option value="Own">Mortgaged</option>
                                            <option value="Own_Freeandclear">Owned (free &amp; clear)</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Years at Address --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spouseaddressperiod">
                                            How many years have you lived here?
                                            <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="number" id="ga-spouseaddressperiod" name="spouseaddressperiod"
                                            class="form-control" min="0">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Housing Pay --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spousehousingpay">
                                            Monthly rent/mortgage payment
                                            <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-lighter">$</span>
                                            <input type="text" id="ga-spousehousingpay" name="spousehousingpay"
                                                class="form-control" placeholder="0.00" inputmode="numeric">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12"><div class="pt-4 border-top"></div></div>
                                <div class="col-sm-6 col-12">
                                    <button type="button" id="ga-continue-4-joint"
                                        class="text-start w-100 btn btn-primary btn-md d-flex justify-content-between">
                                        Continue
                                        <i class="fa-solid fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Step 5 Header (joint only) --}}
                        <div id="ga-header-5-joint" class="hidden wizardstep clearfix py-3 border-bottom">
                            <b class="text-muted">Co-borrower Employment</b>
                            <span class="text-muted float-end wizardstep-indicator ga-indicator" id="ga-ind-5-joint">5 / 6</span>
                        </div>

                        {{-- Step 5 — Co-borrower Employment (joint only) --}}
                        <div id="ga-step-5-joint" class="hidden px-sm-3 py-4">
                            <div class="row">

                                {{-- Co Employer --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spouseemployer">
                                            Employer Name <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-spouseemployer" name="spouseemployer"
                                            class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Job Title --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spouseposition">
                                            Job Title <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="text" id="ga-spouseposition" name="spouseposition"
                                            class="form-control">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Employer Phone --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spouseworkphone">
                                            Employer Phone Number <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="tel" id="ga-spouseworkphone" name="spouseworkphone"
                                            class="form-control" placeholder="(###) ###-####" inputmode="numeric">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Monthly Income --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spouseincome">
                                            Monthly Income <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-lighter">$</span>
                                            <input type="text" id="ga-spouseincome" name="spouseincome"
                                                class="form-control" placeholder="0.00" inputmode="numeric">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Years Worked --}}
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-md-4">
                                        <label class="form-label" for="ga-spouseyears">
                                            Years worked here <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <input type="number" id="ga-spouseyears" name="spouseyears"
                                            class="form-control" placeholder="0" min="0">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                {{-- Co Other Income --}}
                                <div class="col-sm-12">
                                    <div class="mb-3 mb-md-4 border-top pt-3">
                                        <label class="form-label">
                                            Does co-borrower have another source of income?
                                            <strong class="text-danger ps-1">*</strong>
                                        </label>
                                        <div>
                                            <label class="custom-control custom-radio p-0">
                                                <input class="form-check-input" type="radio" name="spouseother" value="Y" id="ga-spouseother-yes">
                                                <span class="custom-control-label">Yes</span>
                                            </label>
                                            <label class="custom-control custom-radio p-0">
                                                <input class="form-check-input" type="radio" name="spouseother" value="N" id="ga-spouseother-no">
                                                <span class="custom-control-label">No</span>
                                            </label>
                                        </div>
                                        <div class="invalid-feedback d-block" id="ga-spouseother-error"></div>
                                    </div>
                                </div>

                                {{-- Co Other Income Fields (conditional) --}}
                                <div id="ga-spouse-other-income-fields" class="hidden col-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-3 mb-md-4">
                                                <label class="form-label">Type of Income</label>
                                                <select name="spouseotherincomeexpln" class="form-select">
                                                    <option value="">Select...</option>
                                                    @foreach([
                                                        'Alimony, child support or separate maintenance',
                                                        'Insurance payments',
                                                        'Investments',
                                                        'Public assistance',
                                                        'Regular allowance',
                                                        'Rental properties',
                                                        'Retirement',
                                                        'Self-employment',
                                                        'Social security',
                                                        'Workers compensation',
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
                                            <div class="mb-3 mb-md-4">
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
                                <div class="col-12"><div class="pt-4 border-top"></div></div>
                                <div class="col-sm-6 col-12">
                                    <button type="button" id="ga-continue-5-joint"
                                        class="text-start w-100 btn btn-primary btn-md d-flex justify-content-between">
                                        Continue
                                        <i class="fa-solid fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ════════════════════════════════════════════════════════
                             LAST STEP HEADER — Consent & Verification
                             (4/4 for single, 6/6 for joint)
                        ════════════════════════════════════════════════════════ --}}
                        <div id="ga-header-last" class="wizardstep clearfix py-3 border-bottom">
                            <b class="text-muted">Consent &amp; Verification</b>
                            <span class="text-muted float-end wizardstep-indicator ga-indicator" id="ga-ind-last">4 / 4</span>
                        </div>

                        {{-- ════════════════════════════════════════════════════════
                             LAST STEP — Consent & Verification
                        ════════════════════════════════════════════════════════ --}}
                        <div id="ga-step-last" class="hidden px-sm-3 py-4">
                            <p><strong class="text-large">Almost there...</strong></p>
                            <p class="pb-4 border-bottom">
                                To submit your application, we need to verify your social security number (SSN) and
                                date of birth (DOB).
                            </p>

                            {{-- ── PRIMARY BORROWER ── --}}
                            <div class="mb-4">
                                <h5 id="ga-consent-borrower-title" class="mb-3 text-primary">Your Information</h5>

                                <div class="row">
                                    {{-- SSN --}}
                                    <div class="col-sm-5">
                                        <div class="mb-4">
                                            <label class="form-label" for="ga-ssn">
                                                Social Security Number <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            <input type="text" id="ga-ssn" name="ssn"
                                                class="form-control" placeholder="___-__-____">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    {{-- DOB --}}
                                    <div class="col-sm-7">
                                        <div class="mb-4">
                                            <label class="form-label">
                                                Date of Birth <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            <div class="row">
                                                <div class="col-4">
                                                    <select name="month" id="ga-month" class="form-select">
                                                        <option value="">Month</option>
                                                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mi => $mn)
                                                            <option value="{{ $mi + 1 }}">{{ $mn }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-4">
                                                    <select name="day" id="ga-day" class="form-select">
                                                        <option value="">Day</option>
                                                        @for($d = 1; $d <= 31; $d++)
                                                            <option value="{{ $d }}">{{ $d }}</option>
                                                        @endfor
                                                    </select>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-4">
                                                    <select name="year" id="ga-year" class="form-select">
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

                                {{-- Authorization --}}
                                <p class="pt-4 border-top text-large">
                                    <strong>Authorization</strong>
                                </p>
                                <p class="border-bottom pb-4 mb-4">
                                    By clicking on the I Agree checkbox and typing in your name, you are confirming
                                    that you have read and understand the
                                    <a href="/privacy" target="_blank">Privacy Policy</a> and
                                    <a href="/electronic-signature-disclosure" target="_blank">Electronic Signature Disclosure</a>,
                                    and you are authorizing <strong>{{ $dealerName ?? config('app.name') }}</strong> under all applicable
                                    federal and state laws, including the Fair Credit Reporting Act, to obtain
                                    information from your personal credit profile.
                                </p>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <label class="form-label">Full name for electronic signature</label>
                                            <input type="text" id="ga-singlesignature" name="singlesignature"
                                                class="form-control" placeholder="First and last name">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="p-3 bg-lighter border rounded mb-2">
                                            <label class="mb-0">
                                                <input type="checkbox" id="ga-singleconsent" name="singleconsent"
                                                    class="me-2 mb-0">
                                                <strong>I agree</strong>
                                            </label>
                                        </div>
                                        <div class="invalid-feedback d-block" id="ga-singleconsent-error"></div>
                                    </div>
                                </div>

                                {{-- Regulation B (shown for joint) --}}
                                <div id="ga-regulationb-primary-row" class="hidden mt-3">
                                    <div class="p-3 bg-lighter border rounded">
                                        <label class="mb-0">
                                            <input type="checkbox" id="ga-regulationbprimary" name="regulationbprimary"
                                                class="me-2 mb-0">
                                            <strong>I acknowledge the joint application (Regulation B)</strong>
                                        </label>
                                    </div>
                                    <div class="invalid-feedback d-block" id="ga-regulationbprimary-error"></div>
                                </div>
                            </div>

                            {{-- ── CO-BORROWER CONSENT (joint only) ── --}}
                            <div id="ga-coborrower-consent-section" class="hidden mt-4 pt-4 border-top">
                                <h5 class="mb-3 text-primary">Co-borrower Information</h5>

                                <div class="row">
                                    {{-- Co SSN --}}
                                    <div class="col-sm-5">
                                        <div class="mb-4">
                                            <label class="form-label" for="ga-spousessn">
                                                Social Security Number <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            <input type="text" id="ga-spousessn" name="spousessn"
                                                class="form-control" placeholder="___-__-____">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    {{-- Co DOB --}}
                                    <div class="col-sm-7">
                                        <div class="mb-4">
                                            <label class="form-label">
                                                Date of Birth <strong class="text-danger ps-1">*</strong>
                                            </label>
                                            <div class="row">
                                                <div class="col-4">
                                                    <select name="spousemonth" id="ga-spousemonth" class="form-select">
                                                        <option value="">Month</option>
                                                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $mi => $mn)
                                                            <option value="{{ $mi + 1 }}">{{ $mn }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-4">
                                                    <select name="spouseday" id="ga-spouseday" class="form-select">
                                                        <option value="">Day</option>
                                                        @for($d = 1; $d <= 31; $d++)
                                                            <option value="{{ $d }}">{{ $d }}</option>
                                                        @endfor
                                                    </select>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-4">
                                                    <select name="spouseyear" id="ga-spouseyear" class="form-select">
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

                                {{-- Co Authorization --}}
                                <p class="pt-4 border-top text-large">
                                    <strong>Co-borrower Authorization</strong>
                                </p>
                                <p class="border-bottom pb-4 mb-4">
                                    By clicking on the I Agree checkbox and typing in your name, the co-borrower is
                                    confirming that they have read and understand the
                                    <a href="/privacy" target="_blank">Privacy Policy</a> and
                                    <a href="/electronic-signature-disclosure" target="_blank">Electronic Signature Disclosure</a>,
                                    and is authorizing <strong>{{ $dealerName ?? config('app.name') }}</strong> under all applicable
                                    federal and state laws, including the Fair Credit Reporting Act, to obtain
                                    information from their personal credit profile.
                                </p>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <label class="form-label">Full name for electronic signature</label>
                                            <input type="text" id="ga-jointsignature" name="jointsignature"
                                                class="form-control" placeholder="Co-borrower first and last name">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="p-3 bg-lighter border rounded mb-2">
                                            <label class="mb-0">
                                                <input type="checkbox" id="ga-jointconsent" name="jointconsent"
                                                    class="me-2 mb-0">
                                                <strong>I agree</strong>
                                            </label>
                                        </div>
                                        <div class="invalid-feedback d-block" id="ga-jointconsent-error"></div>

                                        <div class="p-3 bg-lighter border rounded mt-2">
                                            <label class="mb-0">
                                                <input type="checkbox" id="ga-regulationbjoint" name="regulationbjoint"
                                                    class="me-2 mb-0">
                                                <strong>I acknowledge the joint application (Regulation B)</strong>
                                            </label>
                                        </div>
                                        <div class="invalid-feedback d-block" id="ga-regulationbjoint-error"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="row mt-4">
                                <div class="col-12"><div class="pt-4 border-top"></div></div>
                                <div class="col-sm-6 col-12">
                                    <button type="button" id="ga-submit-btn"
                                        class="w-100 text-start btn btn-primary btn-md d-flex justify-content-between">
                                        <span class="btn-label">Finish Application</span>
                                        <i class="fa-solid fa-check btn-icon"></i>
                                    </button>
                                </div>
                                <div class="col-12"><div class="mt-4 border-top"></div></div>
                            </div>
                        </div>

                        {{-- ════════════════════════════════════════════════════════
                             SUCCESS STATE
                        ════════════════════════════════════════════════════════ --}}
                        <div id="ga-success" class="hidden px-sm-3">
                            <div class="py-4 px-0 px-sm-4 text-center">

                                <div class="my-2">
                                    <span class="d-inline-block h1 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="55" height="55"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.08 0l3.992-3.992a.75.75 0 1 0-1.06-1.06L7.5 9.44 5.98 7.922a.75.75 0 1 0-1.06 1.06l2.05 2.048z" />
                                        </svg>
                                    </span>
                                </div>

                                <div class="h2 text-primary">Credit Application Received</div>
                                <div class="h4">Thank you for trusting {{ $dealerName ?? config('app.name') }} with your financing needs.</div>
                                <p>We will review your application and reach out shortly with next steps.</p>

                            </div>
                        </div>

                    </div>
                </div>

                {{-- Page content below form --}}
                <div class="sc-24764b04-0 kduiyY"></div>
            </div>

            <div class="sc-1a7ba87f-0 fkPBgo cElement cContainer w-100">
                <div class="sc-1a7ba87f-0 cElement cContainer container">
                    <div class="sc-1a7ba87f-0 cElement cContainer container">
                        <h3 class="text-center">Access secure and fast car financing without hassle</h3>
                    </div>
                    <hr class="cElement cDivider my-5">
                    <div class="cElement cColumnLayout row">
                        @foreach([
                            ['Clear terms from the start', 'We walk you through every detail, from interest rate, loan length, down payment, to monthly payment, so you understand exactly what you are signing before anything is finalized.'],
                            ['Fast pre-approval online', 'Apply in minutes right from your phone or computer and get pre-approved before you visit us in Smyrna.'],
                            ['Solutions for every credit situation', 'We partner with a broad network of lenders to offer competitive rates and flexible terms.'],
                        ] as $item)
                            <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                                <div class="cElement cIcon text-center">
                                    <span class="d-inline-block h1 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="55" fill="#166c87" viewBox="0 0 24 24">
                                            <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm-1 14l-4-4 1.41-1.41L11 13.17l5.59-5.59L18 9z" />
                                        </svg>
                                    </span>
                                </div>
                                <h4 class="text-center">{{ $item[0] }}</h4>
                                <p class="text-center">{{ $item[1] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-scripts')
    <script>
        window.gaRoutes = {
            submit: '{{ route('frontend.forms.get-approved') }}',
        };
        window.npsRouteTemplate = window.npsRouteTemplate || '{{ route('frontend.forms.nps', ['formEntry' => '__id__']) }}';
    </script>
@endpush