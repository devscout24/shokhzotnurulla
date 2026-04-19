{{-- Unlock e-Price --}}
<div class="offcanvas offcanvas-end w-lg-50 w-100" tabindex="-1" id="unlockEPrice"
     aria-labelledby="unlockEPriceLabel">

    <div class="offcanvas-header w-100">
        <h3 class="border-bottom h5 ms-1 mb-4 float-start border-theme border-thick d-flex justify-content-between align-items-center w-100">
            Unlock Your e-Price
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                    class="close closeBtn text-large btn btn-link">&times;</button>
        </h3>
    </div>

    <div class="px-4 h-100">
        <div class="slideout-padding-container h-100">
            <div class="px-0 px-sm-3">

                {{-- ── FORM WRAPPER ── --}}
                <div id="upFormWrapper">

                    <div class="p-3 rounded border bg-light mb-4">
                        <i class="fa-solid fa-lock-open text-primary me-2"></i>
                        Unlock the price for the
                        <strong class="text-primary" id="up-vehicle-title"></strong>.
                        Fill in your details below and we'll instantly reveal the lowest available price.
                    </div>

                    <form id="unlock-price-form"
                          data-action="{{ route('frontend.forms.unlock-price') }}"
                          novalidate>

                        <input type="hidden" name="vehicle_id" id="up-vehicle-id">

                        <div class="row">

                            {{-- First Name --}}
                            <div class="col-sm-6">
                                <div class="mb-3 mb-md-4">
                                    <label for="up_first_name" class="form-label mb-1">
                                        First Name <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <input id="up_first_name"
                                           name="first_name"
                                           type="text"
                                           class="form-control"
                                           placeholder="First"
                                           minlength="2"
                                           maxlength="100"
                                           tabindex="1"
                                           required>
                                    <div class="invalid-feedback" id="up-err-first-name"></div>
                                </div>
                            </div>

                            {{-- Last Name --}}
                            <div class="col-sm-6">
                                <div class="mb-3 mb-md-4">
                                    <label for="up_last_name" class="form-label mb-1">
                                        Last Name <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <input id="up_last_name"
                                           name="last_name"
                                           type="text"
                                           class="form-control"
                                           placeholder="Last"
                                           minlength="2"
                                           maxlength="100"
                                           tabindex="2"
                                           required>
                                    <div class="invalid-feedback" id="up-err-last-name"></div>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-sm-6">
                                <div class="mb-3 mb-md-4">
                                    <label for="up_email" class="form-label mb-1">
                                        Email Address <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <input id="up_email"
                                           name="email"
                                           type="email"
                                           class="form-control"
                                           placeholder="you@email.com"
                                           autocomplete="off"
                                           maxlength="255"
                                           tabindex="3"
                                           required>
                                    <div class="invalid-feedback" id="up-err-email"></div>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="col-sm-6">
                                <div class="mb-3 mb-md-4">
                                    <label for="up_phone" class="form-label mb-1">
                                        Phone Number <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <input id="up_phone"
                                           name="phone"
                                           type="tel"
                                           class="form-control"
                                           placeholder="(###) ###-####"
                                           inputmode="numeric"
                                           maxlength="20"
                                           tabindex="4"
                                           required>
                                    <div class="invalid-feedback" id="up-err-phone"></div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="pt-4 border-top"></div>
                            </div>
                            <div class="col-sm-6 pb-3 col-12">
                                <button type="submit"
                                        id="up-submit-btn"
                                        class="text-start w-100 btn btn-primary btn-md">
                                    <span class="btn-label">Continue</span>
                                    <span class="btn-icon float-end ms-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                  d="M1 8a.5.5 0 0 1 .5-.5h11.793L9.146 3.354a.5.5 0 1 1 .708-.708l5 5a.5.5 0 0 1 0 .708l-5 5a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
                {{-- ── END FORM WRAPPER ── --}}

                {{-- ── SUCCESS STATE ── --}}
                <div id="upSuccessBox" class="hidden">
                    <div data-cy="up-confirmation" class="py-4 px-0 text-center px-sm-4 border-bottom">

                        <div class="my-1">
                            <span class="d-inline-block h1 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="55" height="55"
                                     fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.08 0l3.992-3.992a.75.75 0 1 0-1.06-1.06L7.5 9.44 5.98 7.922a.75.75 0 1 0-1.06 1.06l2.05 2.048z"/>
                                </svg>
                            </span>
                        </div>

                        <div class="h2 text-primary mt-2">Special pricing unlocked!</div>
                        <p class="mt-2">Your savings are on the way.</p>
                        <p class="text-muted" style="font-size:13px;">
                            Please check your inbox for an email confirmation in the next few minutes.
                        </p>

                    </div>
                    {{-- NPS Survey --}}
                    <div class="pt-4 pb-3 px-0 text-center">
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <div class="mb-2 opacity-75">How would you rate your experience on our website?</div>
                                <div class="mb-4 cursor-pointer" id="up-star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-value="{{ $i }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        </span>
                                    @endfor
                                </div>
                                <div class="mb-4 option-content hidden" id="upNpsOptions">
                                    <div class="mb-4 text-start">
                                        <span class="badge bg-secondary p-1 me-2">OPTIONAL</span>
                                        <label class="form-label opacity-7 mb-3">
                                            How could we improve your experience?
                                        </label>
                                        <textarea class="text-xx form-control"
                                            placeholder="Any feedback or comments are greatly appreciated"
                                            rows="2"></textarea>
                                    </div>
                                    <button type="button" class="btn btn-primary up-nps-submit-btn">
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

                    <div class="row mt-3">
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
                {{-- ── END SUCCESS STATE ── --}}

            </div>
        </div>
    </div>
</div>