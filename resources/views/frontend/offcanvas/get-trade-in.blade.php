{{-- Get Trade-in Value Offcanvas --}}
<div class="offcanvas offcanvas-end w-lg-50 w-100" tabindex="-1" id="getTrade"
    aria-labelledby="offcanvasRightLabel" data-url-vin-decode="{{ route('frontend.data.vin-decode') }}">
    <div class="offcanvas-header w-100">
        <h3 class="border-bottom h5 ms-1 mb-4 float-start border-theme border-thick d-flex justify-content-between align-items-center w-100">
            Get your trade-in value
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                class="close closeBtn text-large btn btn-link">&times;</button>
        </h3>
    </div>

    <div class="px-4">
        <div class="slideout-padding-container">

            {{-- ── START OVER BUTTON ── --}}
            <div class="pb-3 border-bottom mt-n2 hidden" id="ti-start-over-bar">
                <button type="button" class="btn btn-primary" id="ti-start-over-btn">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Start Over
                </button>
            </div>

            {{-- Header 1 --}}
            <div class="ti-wizard-header wizardstep clearfix py-3 wizardstep-active border-bottom" id="ti-header-1">
                <b class="text-primary">Vehicle Information</b>
                <span class="text-muted float-end wizardstep-indicator notranslate">1 / 4</span>
            </div>

            {{-- ════════════════════════════════════════════════════════════
                 STEP 1 — Vehicle Info
            ════════════════════════════════════════════════════════════ --}}
            <div id="ti-step-1" class="px-0">
                <div class="py-4">
                    <p class="tradeIntroText">Get started by entering your Vehicle Identification Number (VIN) or make and model.</p>

                    {{-- Tab buttons --}}
                    <div class="mb-4 d-inline-flex btn-group" role="group" id="ti-tab-group">
                        <button type="button" class="btn btn-default active" id="ti-tab-ymm">Make &amp; Model</button>
                        <button type="button" class="btn btn-default" id="ti-tab-vin">VIN<span class="d-none d-sm-inline"> Number</span></button>
                    </div>

                    {{-- YMM Panel --}}
                    <div id="ti-panel-ymm" class="px-4 pt-4 border rounded">
                        <div class="row">
                            <div class="mb-4 col-sm-4 col-12">
                                <label class="form-label">Year</label>
                                <select name="year" id="ti-year" class="custom-select form-select">
                                    @for($y = 2027; $y >= 2000; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                    @for($y = 1999; $y >= 1990; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-4 col-sm-4 col-12">
                                <label class="form-label">Make</label>
                                <select name="make" id="ti-make" class="custom-select form-select">
                                    <option value="">[Select Make]</option>
                                    @foreach($globalMakes as $make)
                                        <option value="{{ $make->name }}">{{ $make->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4 col-sm-4 col-12">
                                <label class="form-label">Model</label>
                                <select name="model" id="ti-model" class="custom-select form-select" disabled>
                                    <option value="">[Select Model]</option>
                                </select>
                            </div>
                            <div class="mb-4 col-sm-12 col-12 hidden" id="ti-trim-row">
                                <label class="form-label">Trim</label>
                                <input type="text" name="trim" id="ti-trim" class="form-control" placeholder="e.g. LT, EX, Sport">
                            </div>
                            <div class="mb-4 col-sm-6 col-12 hidden" id="ti-body-row">
                                <label class="form-label">Body Style</label>
                                <select name="body" id="ti-body" class="custom-select form-select">
                                    <option value="">[Select Body Style]</option>
                                    @foreach($globalBodyStyles as $style)
                                        <option value="{{ $style->name }}">{{ $style->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- VIN Panel --}}
                    <div id="ti-panel-vin" class="hidden px-4 pt-4 border rounded">
                        <div class="mb-4">
                            <label class="form-label">Vehicle Identification Number (VIN)</label>
                            <input type="text" name="vin" id="ti-vin" class="form-control"
                                placeholder="VIN Number" minlength="17" maxlength="17">
                                <div class="invalid-feedback d-block small text-danger mt-1" id="ti-vin-error" style="display:none !important;"></div>
                            <small class="d-block my-4 form-text">
                                A Vehicle Identification Number (VIN) is a 17-character identifier of your vehicle.
                                You can find your VIN on your car registration or driver's side door frame.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Step 1a Continue --}}
                <div class="row mb-4">
                    <div class="col-sm-6 col-12">
                        <button type="button" id="ti-continue-1a"
                            class="w-100 text-start btn btn-primary btn-md d-flex align-items-center justify-content-between" disabled>
                            Continue
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <div class="col-12"><div class="border-top mt-4"></div></div>
                </div>

                {{-- ── Step 1b — Condition ── --}}
                <div id="ti-step-1b" class="hidden">
                    <div class="border-top pt-3 mb-4">
                        <p class="h4 fw-bold" id="ti-vehicle-title-display"></p>
                        <button type="button" class="btn btn-link ps-0 text-muted small" id="ti-back-to-1a">
                            <i class="bi bi-pencil me-1"></i>Edit vehicle selection
                        </button>
                    </div>

                    <div class="row">

                        {{-- Mileage --}}
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Exact Mileage <strong class="text-danger ps-1">*</strong></label>
                                <input type="text" name="mileage" id="ti-mileage" class="form-control"
                                    placeholder="Exact Mileage" inputmode="numeric" required>
                                <div class="invalid-feedback" id="ti-mileage-error"></div>
                            </div>
                        </div>

                        {{-- Postal Code --}}
                        <div class="col-sm-6">
                            <div class="mb-3 mb-md-4">
                                <label class="form-label" for="ti-postal-code">
                                    Postal Code <strong class="text-danger ps-1">*</strong>
                                </label>
                                <input type="text" name="postal_code" id="ti-postal-code" class="form-control"
                                    placeholder="_ _ _ _ _" maxlength="5" minlength="5" inputmode="numeric" required>
                                <div class="invalid-feedback" id="ti-postal-code-error"></div>
                            </div>
                        </div>

                        {{-- Exterior Color --}}
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Exterior Color <strong class="text-danger ps-1">*</strong></label>
                                <select name="exterior_color" id="ti-exterior-color" class="custom-select form-select" required>
                                    <option value="">[Select]</option>
                                    @foreach($globalColors as $color)
                                        <option value="{{ $color->name }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="ti-exterior-color-error"></div>
                            </div>
                        </div>

                        {{-- Interior Color --}}
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Interior Color <strong class="text-danger ps-1">*</strong></label>
                                <select name="interior_color" id="ti-interior-color" class="custom-select form-select" required>
                                    <option value="">[Select]</option>
                                    @foreach($globalColors as $color)
                                        <option value="{{ $color->name }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="ti-interior-color-error"></div>
                            </div>
                        </div>

                        {{-- Engine --}}
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Engine</label>
                                <input type="text" name="engine" id="ti-engine" class="form-control"
                                    placeholder="e.g. 3.5L V6, 2.0L Turbo">
                            </div>
                        </div>

                        {{-- Drivetrain --}}
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Drivetrain</label>
                                <select name="drivetrain" id="ti-drivetrain" class="custom-select form-select">
                                    <option value="">[Select]</option>
                                    @foreach($globalDrivetrainTypes as $dt)
                                        <option value="{{ $dt->name }}">{{ $dt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Keys --}}
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">How many keys do you have? <strong class="text-danger ps-1">*</strong></label>
                                <div class="d-flex btn-group" role="group">
                                    <button type="button" class="btn btn-default ti-keys-btn" data-value="1">1</button>
                                    <button type="button" class="btn btn-default ti-keys-btn" data-value="2+">2+</button>
                                </div>
                                <input type="hidden" name="keys" id="ti-keys-hidden">
                                <div class="text-danger small mt-1" id="ti-keys-error"></div>
                            </div>
                        </div>

                        {{-- Ownership --}}
                        <div class="col-sm-9">
                            <div class="mb-3">
                                <label class="form-label">Do you have a loan or lease on the vehicle? <strong class="text-danger ps-1">*</strong></label>
                                <div class="d-flex btn-group" role="group">
                                    <button type="button" class="btn btn-default ti-ownership-btn" data-value="Loan">Loan</button>
                                    <button type="button" class="btn btn-default ti-ownership-btn" data-value="Lease">Lease</button>
                                    <button type="button" class="btn btn-default ti-ownership-btn" data-value="I own it">No, I own it</button>
                                </div>
                                <input type="hidden" name="ownership" id="ti-ownership-hidden">
                                <div class="text-danger small mt-1" id="ti-ownership-error"></div>
                            </div>
                        </div>

                        {{-- Lienholder --}}
                        <div class="col-sm-9 hidden" id="ti-lienholder-row">
                            <div class="mb-3">
                                <label class="form-label">Who holds the lien on the loan/lease?</label>
                                <input type="text" name="lienholder" id="ti-lienholder" class="form-control"
                                    placeholder="Name of bank or dealership">
                            </div>
                        </div>

                        {{-- Remaining Balance --}}
                        <div class="col-sm-9 hidden" id="ti-balance-row">
                            <div class="mb-3">
                                <label class="form-label">What remaining balance do you owe?</label>
                                <div class="input-group">
                                    <span class="bg-lighter prepend input-group-text">$</span>
                                    <input type="text" name="remaining_balance" id="ti-balance" class="form-control"
                                        placeholder="Loan Amount" inputmode="numeric">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Step 1b Continue --}}
                    <div class="row mb-4">
                        <div class="col-sm-6 col-12">
                            <button type="button" id="ti-continue-1b"
                                class="w-100 text-start btn btn-primary btn-md d-flex align-items-center justify-content-between">
                                Continue
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div class="col-12"><div class="border-top mt-4"></div></div>
                    </div>
                </div>
            </div>

            {{-- Header 2 --}}
            <div class="ti-wizard-header wizardstep clearfix py-3 border-bottom" id="ti-header-2">
                <b class="text-muted">History &amp; Condition</b>
                <span class="text-muted float-end wizardstep-indicator notranslate">2 / 4</span>
            </div>

            {{-- ════════════════════════════════════════════════════════════
                 STEP 2 — History & Condition
            ════════════════════════════════════════════════════════════ --}}
            <div id="ti-step-2" class="hidden py-4 px-3">

                {{-- Overall Condition --}}
                <div class="mb-3 mb-md-4">
                    <label class="m-0 form-label">
                        How would you rate your vehicle overall?
                        <strong class="text-danger ps-1">*</strong>
                    </label>
                    <label class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input form-check-input" name="condition" value="new">
                        <span class="custom-control-label">Like-new condition with little signs of wear or tear</span>
                    </label>
                    <label class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input form-check-input" name="condition" value="average">
                        <span class="custom-control-label">Average condition with normal wear and tear (dents, dings, scratches)</span>
                    </label>
                    <label class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input form-check-input" name="condition" value="poor">
                        <span class="custom-control-label">Visible rust, cosmetic damage, or signs of vehicle repainting</span>
                    </label>
                    <div class="text-danger small mt-1" id="ti-condition-error"></div>
                </div>

                {{-- Clean Title --}}
                <div class="mb-3 mb-md-4">
                    <label class="m-0 form-label">Does the vehicle have a clean title? <strong class="text-danger ps-1">*</strong></label>
                    <div class="d-flex w-50 pe-3 btn-group" role="group">
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="clean_title" data-value="yes">Yes</button>
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="clean_title" data-value="no">No</button>
                    </div>
                    <input type="hidden" name="clean_title" id="ti-clean-title">
                    <div class="text-danger small mt-1" id="ti-clean-title-error"></div>
                </div>

                {{-- Run & Drive --}}
                <div class="mb-3 mb-md-4">
                    <label class="m-0 form-label">Does your vehicle run and drive? <strong class="text-danger ps-1">*</strong></label>
                    <div class="d-flex w-50 pe-3 btn-group" role="group">
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="run_drive" data-value="yes">Yes</button>
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="run_drive" data-value="no">No</button>
                    </div>
                    <input type="hidden" name="run_drive" id="ti-run-drive">
                    <div class="text-danger small mt-1" id="ti-run-drive-error"></div>
                </div>

                {{-- Accident --}}
                <div class="mb-3 mb-md-4">
                    <label class="m-0 form-label">Has your vehicle been in an accident? <strong class="text-danger ps-1">*</strong></label>
                    <div class="d-flex w-50 pe-3 btn-group" role="group">
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="accident" data-value="yes">Yes</button>
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="accident" data-value="no">No</button>
                    </div>
                    <input type="hidden" name="accident" id="ti-accident">
                    <div class="text-danger small mt-1" id="ti-accident-error"></div>
                </div>

                {{-- Warning Lights --}}
                <div class="mb-3 mb-md-4">
                    <label class="m-0 form-label">Are there any active warning or service lights? <strong class="text-danger ps-1">*</strong></label>
                    <div class="d-flex w-50 pe-3 btn-group" role="group">
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="warning_lights" data-value="yes">Yes</button>
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="warning_lights" data-value="no">No</button>
                    </div>
                    <input type="hidden" name="warning_lights" id="ti-warning-lights">
                    <div class="text-danger small mt-1" id="ti-warning-lights-error"></div>
                </div>

                {{-- Smoked In --}}
                <div class="mb-3 mb-md-4">
                    <label class="m-0 form-label">Has the vehicle been smoked in? <strong class="text-danger ps-1">*</strong></label>
                    <div class="d-flex w-50 pe-3 btn-group" role="group">
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="smoked_in" data-value="yes">Yes</button>
                        <button type="button" class="btn btn-default ti-yesno-btn" data-field="smoked_in" data-value="no">No</button>
                    </div>
                    <input type="hidden" name="smoked_in" id="ti-smoked-in">
                    <div class="text-danger small mt-1" id="ti-smoked-in-error"></div>
                </div>

                {{-- Damage --}}
                <div class="mb-3 mb-md-4">
                    <label class="m-0 form-label">
                        Does your vehicle have any of the following?
                        <strong class="text-danger ps-1">*</strong>
                    </label>
                    @foreach(['Hail damage', 'Dents', 'Scratched paint', 'Fading or chipping paint', 'Interior wear and tear, stains', 'None of the above'] as $damage)
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input form-check-input ti-damage-cb"
                                name="damage[]" value="{{ $damage }}">
                            <span class="custom-control-label">{{ $damage }}</span>
                        </label>
                    @endforeach
                    <div class="text-danger small mt-1" id="ti-damage-error"></div>
                </div>

                {{-- Tire Condition --}}
                <div class="mb-3 mb-md-4">
                    <label class="form-label">What condition are your tires?</label>
                    <input type="text" name="tire_condition" id="ti-tire-condition" class="form-control"
                        placeholder="e.g. Good, Fair, Needs replacement">
                </div>

                {{-- Step 2 Continue --}}
                <div class="row">
                    <div class="col-12"><div class="pt-4 border-top"></div></div>
                    <div class="col-sm-6 col-12">
                        <button type="button" id="ti-continue-2"
                            class="text-start w-100 btn btn-primary btn-md d-flex align-items-center justify-content-between">
                            Continue
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Header 3 --}}
            <div class="ti-wizard-header wizardstep clearfix py-3 border-bottom" id="ti-header-3">
                <b class="text-muted">Photos (Optional)</b>
                <span class="text-muted float-end wizardstep-indicator notranslate">3 / 4</span>
            </div>

            {{-- ════════════════════════════════════════════════════════════
                 STEP 3 — Photos (Optional)
            ════════════════════════════════════════════════════════════ --}}
            <div id="ti-step-3" class="hidden py-4 px-3">

                <div class="pb-2 mb-3 border-bottom">
                    <strong class="h4">Recommended Photos</strong>
                </div>
                <ul class="ms-2">
                    <li>Exterior: front left, front right, back left, back right</li>
                    <li>Wheels and tires: front left, front right, back left, back right</li>
                    <li>Dashboard, odometer, service lights</li>
                    <li>Front seats, back seats</li>
                    <li>Engine bay, including front bumper</li>
                </ul>

                <div class="bg-lighter p-5 border rounded text-center cursor-pointer" id="ti-dropzone">
                    <input type="file" id="ti-photo-input" accept="image/*" multiple class="d-none">
                    <i class="bi bi-cloud-arrow-up h1 m-0 text-muted d-block"></i>
                    <p class="mb-0 mt-3">Drag and drop images or click here to select files. Non-image files will be discarded.</p>
                </div>

                <div id="ti-photo-preview" class="hidden px-3 pt-3 bg-lighter mt-4 border rounded">
                    <div class="row" id="ti-photo-grid"></div>
                </div>

                <div id="ti-upload-status" class="hidden mt-3">
                    <div class="d-flex align-items-center text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <span>Uploading photos...</span>
                    </div>
                </div>

                <div class="mt-4 row">
                    <div class="col-sm-6 col-12">
                        <button type="button" id="ti-continue-3"
                            class="text-start w-100 mb-4 mb-md-0 btn btn-primary btn-md d-flex align-items-center justify-content-between">
                            Continue
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <div class="col-sm-6 col-12">
                        <button type="button" id="ti-skip-photos"
                            class="text-start w-100 text-nowrap btn btn-default">
                            <i class="bi bi-slash-circle me-2"></i>Skip Photos
                        </button>
                    </div>
                </div>
            </div>

            {{-- Header 4 --}}
            <div class="ti-wizard-header wizardstep clearfix py-3 border-bottom" id="ti-header-4">
                <b class="text-muted">Contact Information</b>
                <span class="text-muted float-end wizardstep-indicator notranslate">4 / 4</span>
            </div>

            {{-- ════════════════════════════════════════════════════════════
                 STEP 4 — Contact Information
            ════════════════════════════════════════════════════════════ --}}
            <div id="ti-step-4" class="hidden py-4 px-3">
                <div class="row">

                    {{-- First Name --}}
                    <div class="col-sm-6">
                        <div class="mb-3 mb-md-4">
                            <label class="form-label" for="ti-first-name">
                                First Name <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="text" name="first_name" id="ti-first-name" class="form-control"
                                placeholder="First" minlength="2" maxlength="100" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- Last Name --}}
                    <div class="col-sm-6">
                        <div class="mb-3 mb-md-4">
                            <label class="form-label" for="ti-last-name">
                                Last Name <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="text" name="last_name" id="ti-last-name" class="form-control"
                                placeholder="Last" minlength="2" maxlength="100" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-sm-6">
                        <div class="mb-3 mb-md-4">
                            <label class="form-label" for="ti-email">
                                Email Address <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="email" name="email" id="ti-email" class="form-control"
                                placeholder="you@email.com" maxlength="255" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="col-sm-6">
                        <div class="mb-3 mb-md-4">
                            <label class="form-label" for="ti-phone">
                                Phone Number <strong class="text-danger ps-1">*</strong>
                            </label>
                            <input type="tel" name="phone" id="ti-phone" class="form-control"
                                placeholder="(###) ###-####" inputmode="numeric" maxlength="20" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    {{-- Communication Preference --}}
                    <div class="col-12">
                        <div class="mb-3 mb-md-4">
                            <label class="form-label">
                                What is the best way to contact you?
                                <strong class="text-danger ps-1">*</strong>
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="commpref" value="email" checked>
                                <label class="form-check-label">Email</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="commpref" value="text">
                                <label class="form-check-label">Text Message</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="commpref" value="phone">
                                <label class="form-check-label">Phone Call</label>
                            </div>
                        </div>
                    </div>

                    {{-- Comment --}}
                    <div class="col-12">
                        <div class="mb-3 mb-md-4">
                            <label class="form-label" for="ti-comment">What questions can we answer for you?</label>
                            <textarea name="comment" id="ti-comment" class="form-control"
                                placeholder="Add a question or comment" maxlength="2000" rows="3"></textarea>
                        </div>
                    </div>

                </div>

                {{-- Submit --}}
                <div class="row">
                    <div class="col-12"><div class="pt-4 border-top"></div></div>
                    <div class="col-sm-6 col-12">
                        <button type="button" id="ti-submit-btn"
                            class="text-start w-100 btn btn-primary btn-md d-flex align-items-center justify-content-between">
                            <span class="btn-label">Continue</span>
                            <i class="btn-icon bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                {{-- Server-side validation errors --}}
                <div id="ti-form-errors" class="hidden mt-3 alert alert-danger">
                    <div id="ti-error-list"></div>
                </div>

                <p class="text-small text-muted mt-4">
                    * All offers are subject to final review by dealer. Although every attempt is made to provide an accurate value,
                    there may be extenuating circumstances such as unique options, high or low mileage, vehicle condition, etc.,
                    that may require dealer to revise the estimate.
                </p>
            </div>

            {{-- ════════════════════════════════════════════════════════════
                 SUCCESS STATE
            ════════════════════════════════════════════════════════════ --}}
            <div id="ti-success" class="hidden py-4 px-3">
                <div class="py-4 px-0 text-center px-sm-4 border-bottom">

                    <div class="my-1">
                        <span class="d-inline-block h1 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="55" height="55" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.08 0l3.992-3.992a.75.75 0 1 0-1.06-1.06L7.5 9.44 5.98 7.922a.75.75 0 1 0-1.06 1.06l2.05 2.048z" />
                            </svg>
                        </span>
                    </div>

                    <div class="h2 text-primary mt-2">Trade-in Received!</div>
                    <p class="mt-2">
                        Thank you! We've received your trade-in information and will be in touch shortly
                        to discuss your vehicle's value.
                    </p>

                    {{-- Get Approved CTA --}}
                    <div class="pt-4 mt-5 border-top text-center">
                        <h4>Save an hour at the dealership</h4>
                        <p>With our lender relationships, we can often beat your bank or credit union's rate.
                            Get your new vehicle faster with an online approval.</p>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-4 text-center">
                                <button type="button" class="w-100 btn btn-primary btn-lg ti-get-approved-btn" data-bs-toggle="offcanvas" data-bs-target="#getApproved">
                                    Get approved
                                    <i class="bi bi-chevron-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- NPS Survey --}}
                <div class="pt-4 pb-3 px-0 text-center">
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <div class="mb-2 opacity-75">How would you rate your experience on our website?</div>
                            <div class="mb-4 cursor-pointer" id="ti-star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star" data-value="{{ $i }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                        </svg>
                                    </span>
                                @endfor
                            </div>
                            <div class="mb-4 option-content hidden" id="tiNpsOptions">
                                <div class="mb-4 text-start">
                                    <span class="badge bg-secondary p-1 me-2">OPTIONAL</span>
                                    <label class="form-label opacity-7 mb-3">How could we improve your experience?</label>
                                    <textarea class="form-control" placeholder="Any feedback or comments are greatly appreciated" rows="2"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary ti-nps-submit-btn">
                                    <i class="bi bi-check2 me-2"></i>Share Your Feedback
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="text-center col-md-4 col-12 offset-md-4">
                        <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                            class="w-100 btn btn-primary btn-lg border-dark mb-5 cursor-pointer">
                            <i class="bi bi-x-lg me-2"></i>Close Window
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
