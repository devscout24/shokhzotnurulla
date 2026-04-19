{{-- Schedule a Test Drive --}}
<div class="offcanvas offcanvas-end w-lg-50 w-100" tabindex="-1" id="scheduleTest"
    aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header w-100">
        <h3 class="border-bottom h5 ms-1 mb-4 float-start border-theme border-thick d-flex justify-content-between align-items-center w-100">
            Schedule a test drive
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                class="close closeBtn text-large btn btn-link">&times;</button>
        </h3>
    </div>

    <div class="px-4 h-100">
        <div class="slideout-padding-container h-100">

            {{-- ── STEP HEADERS ── --}}
            <div class="std-wizard-step clearfix py-3 wizardstep-active border-bottom" id="std-header-1">
                <b class="text-primary">Select a Day &amp; Time</b>
                <span class="text-muted float-end wizardstep-indicator">1 / 2</span>
            </div>

            {{-- ── STEP 1: Calendar ── --}}
            <div id="std-step-1" class="px-0 px-sm-3 py-4 border-bottom">

                {{-- Date selection --}}
                <div id="std-select-date">
                    <p>Select a day:</p>
                    <div class="row row-bordered border cursor-pointer g-0" id="std-days-container">
                        {{-- Days rendered by JS --}}
                    </div>
                    <div class="row mt-3">
                        <div class="col ps-0">
                            <button type="button" class="btn btn-link underline" id="std-prev-week" disabled>
                                <span class="me-2 text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                                    </svg>
                                </span>
                                Previous Week
                            </button>
                        </div>
                        <div class="col text-end pe-0">
                            <button type="button" class="btn btn-link underline" id="std-next-week">
                                Next Week
                                <span class="ms-2 text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Time selection (shown after day selected) --}}
                <div id="std-select-time" class="hidden">
                    <button type="button" class="mb-3 w-100 text-start btn btn-default" id="std-back-to-date">
                        <span class="d-inline-block me-2 float-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                        </span>
                        Select a different day
                    </button>

                    <div class="font-weight-bold mb-3">
                        <span class="d-inline-block me-2 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="#166B87" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <polyline points="3 10 12 19 21 6"></polyline>
                            </svg>
                        </span>
                        Selected: <span id="std-selected-day-label"></span>
                    </div>

                    <div class="bg-lighter border rounded p-4 mt-3 mb-4">
                        <p>Select a time:</p>
                        <select id="std-time-select" class="custom-select form-select">
                            <option value="">Select...</option>
                            <option value="9:00 AM">9:00 AM</option>
                            <option value="9:30 AM">9:30 AM</option>
                            <option value="10:00 AM">10:00 AM</option>
                            <option value="10:30 AM">10:30 AM</option>
                            <option value="11:00 AM">11:00 AM</option>
                            <option value="11:30 AM">11:30 AM</option>
                            <option value="12:00 PM">12:00 PM</option>
                            <option value="12:30 PM">12:30 PM</option>
                            <option value="1:00 PM">1:00 PM</option>
                            <option value="1:30 PM">1:30 PM</option>
                            <option value="2:00 PM">2:00 PM</option>
                            <option value="2:30 PM">2:30 PM</option>
                            <option value="3:00 PM">3:00 PM</option>
                            <option value="3:30 PM">3:30 PM</option>
                            <option value="4:00 PM">4:00 PM</option>
                            <option value="4:30 PM">4:30 PM</option>
                            <option value="5:00 PM">5:00 PM</option>
                            <option value="5:30 PM">5:30 PM</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="pt-4 border-top mx-3 col-12"></div>
                        <div class="col-sm-4 col-12">
                            <button type="button" id="std-continue-step1"
                                class="w-100 text-start btn btn-primary btn-md">
                                Continue
                                <span class="d-inline-block float-end ms-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── STEP 2 HEADER ── --}}
            <div class="std-wizard-step clearfix py-3 border-bottom" id="std-header-2">
                <b class="text-muted">Let us know who's coming</b>
                <span class="text-muted float-end wizardstep-indicator notranslate">2 / 2</span>
            </div>

            {{-- ── STEP 2: Contact Form ── --}}
            <div id="std-step-2" class="hidden px-0 px-sm-3 py-4 border-bottom">
                <form
                    id="std-form"
                    data-action="{{ route('frontend.forms.schedule-test-drive') }}"
                    novalidate
                >
                    {{-- Hidden date fields --}}
                    <input type="hidden" name="preferred_date"      id="std-hidden-date">
                    <input type="hidden" name="preferred_day_label" id="std-hidden-day-label">
                    <input type="hidden" name="preferred_time"      id="std-hidden-time">
                    <input type="hidden" name="vehicle_id"          id="std-hidden-vehicle-id">

                    <div class="row">

                        {{-- First Name --}}
                        <div class="col-sm-6">
                            <div class="mb-3 mb-md-4">
                                <label for="std_first_name" class="form-label mb-1">
                                    First Name <strong class="text-danger ps-1">*</strong>
                                </label>
                                <input id="std_first_name" name="first_name" type="text"
                                    class="form-control" placeholder="First"
                                    minlength="2" maxlength="100" tabindex="1" required>
                            </div>
                        </div>

                        {{-- Last Name --}}
                        <div class="col-sm-6">
                            <div class="mb-3 mb-md-4">
                                <label for="std_last_name" class="form-label mb-1">
                                    Last Name <strong class="text-danger ps-1">*</strong>
                                </label>
                                <input id="std_last_name" name="last_name" type="text"
                                    class="form-control" placeholder="Last"
                                    minlength="2" maxlength="100" tabindex="2" required>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-sm-6">
                            <div class="mb-3 mb-md-4">
                                <label for="std_email" class="form-label mb-1">
                                    Email Address <strong class="text-danger ps-1">*</strong>
                                </label>
                                <input id="std_email" name="email" type="email"
                                    class="form-control" placeholder="you@email.com"
                                    autocomplete="off" maxlength="255" tabindex="3" required>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-sm-6">
                            <div class="mb-3 mb-md-4">
                                <label for="std_phone" class="form-label mb-1">
                                    Phone Number <strong class="text-danger ps-1">*</strong>
                                </label>
                                <input id="std_phone" name="phone" type="tel"
                                    class="form-control" placeholder="(###) ###-####"
                                    inputmode="numeric" maxlength="20" tabindex="4" required>
                            </div>
                        </div>

                        {{-- Communication Preference --}}
                        <div class="col-12">
                            <div class="mb-3 mb-md-4">
                                <label class="form-label mb-2">
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

                        {{-- Comment (optional) --}}
                        <div class="col-12">
                            <div class="mb-3 mb-md-4">
                                <label for="std_comment" class="form-label mb-1">
                                    What questions can we answer for you?
                                </label>
                                <textarea id="std_comment" name="comment"
                                    class="form-control h-100"
                                    placeholder="Add a question or comment"
                                    tabindex="6" maxlength="2000"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="pt-4 border-top"></div>
                        </div>
                        <div class="col-sm-6 pb-3 col-12">
                            <button type="submit" id="std-submit-btn"
                                class="text-start w-100 btn btn-primary btn-md">
                                <span class="btn-label">Continue</span>
                                <span class="btn-icon float-end ms-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M1 8a.5.5 0 0 1 .5-.5h11.793L9.146 3.354a.5.5 0 1 1 .708-.708l5 5a.5.5 0 0 1 0 .708l-5 5a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ── SUCCESS STATE ── --}}
            <div id="std-success" class="hidden px-0 px-sm-3 py-4 border-bottom">
                <div data-cy="confirmation" class="py-4 px-0 text-center px-sm-4 border-bottom">

                    <div class="my-1">
                        <span class="d-inline-block h1 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="55" height="55" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.08 0l3.992-3.992a.75.75 0 1 0-1.06-1.06L7.5 9.44 5.98 7.922a.75.75 0 1 0-1.06 1.06l2.05 2.048z" />
                            </svg>
                        </span>
                    </div>

                    <div class="h2 text-primary mt-2">Your test drive is scheduled</div>
                    <div class="h4">We've sent you an email confirmation.</div>
                    <p class="mt-2">
                        If you need to cancel or reschedule, instructions can be found in your inbox.
                    </p>

                    {{-- Credit App CTA --}}
                    <div class="pt-4 mt-5 border-top text-center">
                        <h4>Save an hour at the dealership</h4>
                        <p>
                            With our lender relationships, we can often beat your bank or credit union's rate.
                            Get your new vehicle faster with an online approval. Estimated monthly payment does
                            not include title and license fees. Monthly payment will be higher.
                        </p>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-4 text-center">
                                <button type="button" data-cy="btn-confirmation"
                                    class="w-100 btn btn-primary btn-lg std-get-approved-btn" data-bs-toggle="offcanvas" data-bs-target="#getApproved">
                                    Get approved
                                    <span class="ms-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 1 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" />
                                        </svg>
                                    </span>
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
                            <div class="mb-4 cursor-pointer" id="std-star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star" data-value="{{ $i }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                        </svg>
                                    </span>
                                @endfor
                            </div>
                            <div class="mb-4 option-content hidden" id="stdNpsOptions">
                                <div class="mb-4 text-start">
                                    <span class="badge bg-secondary p-1 me-2">OPTIONAL</span>
                                    <label class="form-label opacity-7 mb-3">
                                        How could we improve your experience?
                                    </label>
                                    <textarea class="text-xx form-control"
                                        placeholder="Any feedback or comments are greatly appreciated"
                                        rows="2"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary std-nps-submit-btn">
                                    <span class="d-inline-block me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="white" viewBox="0 0 16 16">
                                            <path d="M13.485 1.929a.75.75 0 0 1 1.06 1.06L6.53 10.004l-3.03-3.03a.75.75 0 0 1 1.06-1.06l1.97 1.97L13.485 1.93z" />
                                        </svg>
                                    </span>
                                    Share Your Feedback
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="text-center col-md-4 col-12 offset-md-4">
                        <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                            class="w-100 btn btn-primary btn-lg border-dark mb-5 cursor-pointer">
                            <span class="d-inline-block me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                </svg>
                            </span>
                            Close Window
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
