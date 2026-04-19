{{-- Ask a Question --}}
<div class="offcanvas offcanvas-end w-lg-50 w-100" tabindex="-1" id="askQuestion"
    aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header w-100">
        <h3 class="border-bottom h5 ms-1 mb-4 float-start border-theme border-thick d-flex justify-content-between align-items-center w-100">
            Ask a question
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                class="close closeBtn text-large btn btn-link">&times;</button>
        </h3>
    </div>

    <div class="px-4 h-100">
        <div class="slideout-padding-container h-100">
            <div class="px-0 px-sm-3">

                {{-- ── FORM WRAPPER ── --}}
                <div id="aqFormWrapper">
                    <div class="p-3 rounded border bg-light mb-4" id="aq-vehicle-context">
                        Ask a question about the
                        <strong class="text-primary" id="aq-vehicle-title"></strong>
                        and we'll respond promptly. If you need immediate assistance, please call us directly at
                        <strong><a href="tel:+16152670590">(615) 267-0590</a></strong>.
                    </div>

                    <form
                        id="ask-question-form"
                        data-action="{{ route('frontend.forms.ask-question') }}"
                        novalidate
                    >
                        {{-- Hidden vehicle_id --}}
                        <input type="hidden" name="vehicle_id" id="aq-vehicle-id">

                        <div class="row">

                            {{-- First Name --}}
                            <div class="col-sm-6">
                                <div class="mb-3 mb-md-4">
                                    <label for="aq_first_name" class="form-label mb-1">
                                        First Name <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <input
                                        id="aq_first_name"
                                        name="first_name"
                                        type="text"
                                        class="form-control"
                                        placeholder="First"
                                        minlength="2"
                                        maxlength="100"
                                        tabindex="1"
                                        required
                                    >
                                </div>
                            </div>

                            {{-- Last Name --}}
                            <div class="col-sm-6">
                                <div class="mb-3 mb-md-4">
                                    <label for="aq_last_name" class="form-label mb-1">
                                        Last Name <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <input
                                        id="aq_last_name"
                                        name="last_name"
                                        type="text"
                                        class="form-control"
                                        placeholder="Last"
                                        minlength="2"
                                        maxlength="100"
                                        tabindex="2"
                                        required
                                    >
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-sm-6">
                                <div class="mb-3 mb-md-4">
                                    <label for="aq_email" class="form-label mb-1">
                                        Email Address <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <input
                                        id="aq_email"
                                        name="email"
                                        type="email"
                                        class="form-control"
                                        placeholder="you@email.com"
                                        autocomplete="off"
                                        maxlength="255"
                                        tabindex="3"
                                        required
                                    >
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="col-sm-6">
                                <div class="mb-3 mb-md-4">
                                    <label for="aq_phone" class="form-label mb-1">
                                        Phone Number <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <input
                                        id="aq_phone"
                                        name="phone"
                                        type="tel"
                                        class="form-control"
                                        placeholder="(###) ###-####"
                                        inputmode="numeric"
                                        maxlength="20"
                                        tabindex="4"
                                        required
                                    >
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

                            {{-- Comment / Question --}}
                            <div class="col-12">
                                <div class="mb-3 mb-md-4">
                                    <label for="aq_comment" class="form-label mb-1">
                                        What questions can we answer for you?
                                        <strong class="text-danger ps-1">*</strong>
                                    </label>
                                    <textarea
                                        id="aq_comment"
                                        name="comment"
                                        class="form-control h-100"
                                        placeholder="Add a question or comment"
                                        tabindex="6"
                                        maxlength="2000"
                                        required
                                    ></textarea>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="pt-4 border-top"></div>
                            </div>
                            <div class="col-sm-6 pb-3 col-12">
                                <button
                                    type="submit"
                                    id="aq-submit-btn"
                                    class="text-start w-100 btn btn-primary btn-md"
                                >
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
                {{-- ── END FORM WRAPPER ── --}}

                {{-- ── SUCCESS STATE ── --}}
                <div id="aqSuccessBox" class="hidden">
                    <div data-cy="confirmation" class="py-4 px-0 text-center px-sm-4 border-bottom">

                        <div class="my-1">
                            <span class="d-inline-block h1 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="55" height="55" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.08 0l3.992-3.992a.75.75 0 1 0-1.06-1.06L7.5 9.44 5.98 7.922a.75.75 0 1 0-1.06 1.06l2.05 2.048z" />
                                </svg>
                            </span>
                        </div>

                        <div class="h2 text-primary mt-2">Question Received!</div>
                        <p class="mt-2">
                            We've received your inquiry about the
                            <span class="aq-vehicle-title-success"></span>
                            and will get back to you as soon as possible.
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
                                    <button
                                        type="button"
                                        data-cy="btn-confirmation"
                                        class="w-100 btn btn-primary btn-lg aq-get-approved-btn"
                                        data-bs-toggle="offcanvas" data-bs-target="#getApproved"
                                    >
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
                                <div class="mb-4 cursor-pointer" id="aq-star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-value="{{ $i }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        </span>
                                    @endfor
                                </div>
                                <div class="mb-4 option-content hidden" id="aqNpsOptions">
                                    <div class="mb-4 text-start">
                                        <span class="badge bg-secondary p-1 me-2">OPTIONAL</span>
                                        <label class="form-label opacity-7 mb-3">
                                            How could we improve your experience?
                                        </label>
                                        <textarea class="text-xx form-control"
                                            placeholder="Any feedback or comments are greatly appreciated"
                                            rows="2"></textarea>
                                    </div>
                                    <button type="button" class="btn btn-primary aq-nps-submit-btn">
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
                {{-- ── END SUCCESS STATE ── --}}

            </div>
        </div>
    </div>
</div>
