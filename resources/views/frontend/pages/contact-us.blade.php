@extends('layouts.frontend.app')

@section('title', __('Contact Us') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/contact.css',
        'resources/js/frontend/pages/contact.js',
    ])
@endpush

@php
    $loc        = $locationMenuData[0] ?? null;
    $phones     = $loc['phones'] ?? [];

    $mainPhone = collect($phones)->firstWhere('type', 'sales')
        ?? collect($phones)->firstWhere('type', 'main')
        ?? collect($phones)->first();

    $mainPhoneNumber = $mainPhone ? $mainPhone['number'] : '(615) 267-0590';
    $mainPhoneRaw    = preg_replace('/\D/', '', $mainPhoneNumber);

    $street1    = $loc['street1']    ?? '1339 South Lowry Street';
    $city       = $loc['city']       ?? 'Smyrna';
    $state      = $loc['state']      ?? 'TN';
    $postalcode = $loc['postalcode'] ?? '37167';

    $dealerName = $dealerName ?: config('app.name', 'Angel Motors Inc');

    $socials = $dealerSocialLinks ?? [];

    $socialPlatforms = [
        'facebook'  => ['label' => 'Facebook',  'icon' => 'facebook.png'],
        'youtube'   => ['label' => 'Youtube',   'icon' => 'youtube.png'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'instagram.png'],
        'tiktok'    => ['label' => 'Tiktok',    'icon' => 'tiktok.png'],
        'twitter'   => ['label' => 'Twitter',   'icon' => 'twitter.png'],
        'pinterest' => ['label' => 'Pinterest', 'icon' => 'pinterest.png'],
        'linkedin'  => ['label' => 'LinkedIn',  'icon' => 'linkedin.png'],
    ];
@endphp

@section('page-content')
    <div class="d-block h-63 d-xl-none" id="mobile-nav-spacer"></div>

    <div class="page-template-schedule-service" role="main">
        <header class="sc-5a5d3415-0 jHTnHg" id="interior-page-header"
            title="Contact Angel Motors Inc in Smyrna, TN">
            <div class="position-relative container">
                <div>
                    <h1 class="m-0 text-white py-3 text-center" id="page_h1">Contact Angel Motors Inc in Smyrna, TN
                    </h1>
                </div>
            </div>
        </header>

        <div class="bg-white pt-3 pt-lg-5">
            <div class="">
                <div class="sc-24764b04-0 kdtJAz"></div>
                <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                    <div class="cElement cColumnLayout  row">
                        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-2">
                            <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer container contact-block">

                                <h2 id="GnVo3EkgXg" class="text-start">Connect with Angel Motors Inc</h2>

                                <p>
                                    We are here to help whenever you are ready to explore vehicles, ask questions,
                                    <a href="{{ route('frontend.service') }}">schedule a car service</a> or
                                    <a href="{{ route('frontend.get-approved') }}">discuss financing</a>. Our team enjoys hearing from
                                    drivers in Smyrna and across Middle Tennessee, and we make it simple and
                                    welcoming to reach us.
                                </p>

                                <p>
                                    Angel Motors Inc is conveniently located right in Smyrna, TN, easy to access for
                                    customers from Murfreesboro, La Vergne, Nashville, and nearby communities. Our
                                    showroom provides a relaxed setting where you can browse at your own pace, sit
                                    down for honest conversations, and get clear answers without any rush.
                                </p>

                                <p>
                                    No matter how you prefer to connect, we are ready to assist. Reach out today and
                                    let Angel Motors Inc help you find the right vehicle or keep your current one
                                    running smoothly. We look forward to serving you in Smyrna, TN.
                                </p>

                                <div class="sc-24764b04-0 kdvwXJ"></div>

                                <!-- Address -->
                                <div class="position-relative d-flex align-items-start p-2 craftElement craftCheck">
                                    <div class="pe-3">
                                        <span class="d-inline-block faIcon fa-fw">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="#166c87" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 0C5.243 0 3 2.243 3 5c0 3.5 5 11 5 11s5-7.5 5-11c0-2.757-2.243-5-5-5zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="w-100">
                                        <div>
                                            <h3 class="text-small text-start">Address</h3>
                                            <div class="cElement cText contact-link">
                                                <p>{{ $street1 }}, {{ $city }}, {{ $state }} {{ $postalcode }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="cElement cDivider hr-2">

                                <!-- Phone -->
                                <div class="position-relative d-flex align-items-start p-2 craftElement craftCheck">
                                    <div class="pe-3">
                                        <span class="d-inline-block faIcon fa-fw">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="#166c87" viewBox="0 0 24 24">
                                                <path
                                                    d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24 11.72 11.72 0 003.64.58 1 1 0 011 1v3.5a1 1 0 01-1 1A16 16 0 014 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.28.2 2.53.58 3.64a1 1 0 01-.25 1.01l-2.21 2.2z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="w-100">
                                        <div>
                                            <h3 class="text-small text-start">Phone</h3>
                                            <div class="cElement cText contact-link">
                                                <p>
                                                    <a href="tel:{{ $mainPhoneRaw }}">{{ $mainPhoneNumber }}</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="cElement cDivider hr-2">

                                <!-- Social Media -->
                                <div class="sc-24764b04-0 kdvwXJ"></div>
                                <p><strong>Social Media</strong></p>
                                <div class="cElement cColumnLayout row">
                                    <div class="cElement cColumn col-12 order-sm-0 order-1">

                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            @foreach ($socialPlatforms as $platform => $meta)
                                                @if (!empty($socials[$platform]))
                                                    <a title="{{ $meta['label'] }}" target="_blank" href="{{ $socials[$platform] }}">
                                                        <img
                                                            width="30"
                                                            height="30"
                                                            src="{{ asset('assets/frontend/img/' . $meta['icon']) }}"
                                                            alt="{{ $meta['label'] }} icon"
                                                            loading="lazy"
                                                            class="cElement cImage img-fluid"
                                                        >
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>

                                    </div>

                                    <div class="cElement cColumn col-sm-4 col-12"></div>
                                    <div class="cElement cColumn col-sm-4 col-12"></div>
                                </div>

                                <div class="sc-24764b04-0 kdvwXJ"></div>

                            </div>
                        </div>

                        {{-- ── RIGHT COLUMN — Form + Success Box ── --}}
                        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                            <div class="sc-1a7ba87f-0 cElement cContainer container">

                                {{-- ── FORM WRAPPER (hidden on success) ── --}}
                                <div id="contact-form-wrapper">
                                    <form
                                        id="contact-us-form"
                                        data-action="{{ route('frontend.forms.contact-us') }}"
                                        novalidate
                                    >
                                        <div class="cElement cForm p-4 border rounded bg-white">
                                            <div class="row">

                                                {{-- First Name --}}
                                                <div class="col-sm-6">
                                                    <div class="mb-3 mb-md-4">
                                                        <label class="m-0 form-label" for="first_name">
                                                            First Name <strong class="text-danger ps-1">*</strong>
                                                        </label>
                                                        <input
                                                            data-cy="formcontrol-text-first_name"
                                                            tabindex="1"
                                                            placeholder="First"
                                                            minlength="2"
                                                            maxlength="100"
                                                            id="first_name"
                                                            class="form-control"
                                                            type="text"
                                                            name="first_name"
                                                            required
                                                        >
                                                    </div>
                                                </div>

                                                {{-- Last Name --}}
                                                <div class="col-sm-6">
                                                    <div class="mb-3 mb-md-4">
                                                        <label class="m-0 form-label" for="last_name">
                                                            Last Name <strong class="text-danger ps-1">*</strong>
                                                        </label>
                                                        <input
                                                            data-cy="formcontrol-text-last_name"
                                                            tabindex="2"
                                                            placeholder="Last"
                                                            minlength="2"
                                                            maxlength="100"
                                                            id="last_name"
                                                            class="form-control"
                                                            type="text"
                                                            name="last_name"
                                                            required
                                                        >
                                                    </div>
                                                </div>

                                                {{-- Email --}}
                                                <div class="col-sm-6">
                                                    <div class="mb-3 mb-md-4">
                                                        <label class="m-0 form-label" for="email">
                                                            Email Address <strong class="text-danger ps-1">*</strong>
                                                        </label>
                                                        <input
                                                            data-cy="formcontrol-email"
                                                            placeholder="you@email.com"
                                                            tabindex="3"
                                                            aria-label="Your email"
                                                            autocomplete="off"
                                                            id="email"
                                                            class="form-control"
                                                            type="email"
                                                            name="email"
                                                            maxlength="255"
                                                            required
                                                        >
                                                    </div>
                                                </div>

                                                {{-- Phone --}}
                                                <div class="col-sm-6">
                                                    <div class="mb-3 mb-md-4">
                                                        <label class="m-0 form-label" for="phone">
                                                            Phone Number <strong class="text-danger ps-1">*</strong>
                                                        </label>
                                                        <input
                                                            inputmode="numeric"
                                                            data-cy="formcontrol-phone"
                                                            class="form-control"
                                                            id="phone"
                                                            tabindex="4"
                                                            placeholder="(###) ###-####"
                                                            type="tel"
                                                            name="phone"
                                                            maxlength="20"
                                                            required
                                                        >
                                                    </div>
                                                </div>

                                                {{-- Communication Preference --}}
                                                <div class="col-sm-12">
                                                    <div class="mb-3 mb-md-4">
                                                        <label class="m-0 form-label">
                                                            What is the best way to contact you?
                                                            <strong class="text-danger ps-1">*</strong>
                                                        </label>
                                                        <div>
                                                            <label class="custom-control custom-radio p-0">
                                                                <input
                                                                    data-cy="formcontrol-radio-commpref"
                                                                    class="form-check-input"
                                                                    checked
                                                                    type="radio"
                                                                    value="email"
                                                                    name="commpref"
                                                                >
                                                                <span class="custom-control-label">Email</span>
                                                            </label>
                                                            <label class="custom-control custom-radio p-0">
                                                                <input
                                                                    data-cy="formcontrol-radio-commpref"
                                                                    class="form-check-input"
                                                                    type="radio"
                                                                    value="text"
                                                                    name="commpref"
                                                                >
                                                                <span class="custom-control-label">Text Message</span>
                                                            </label>
                                                            <label class="custom-control custom-radio p-0">
                                                                <input
                                                                    data-cy="formcontrol-radio-commpref"
                                                                    class="form-check-input"
                                                                    type="radio"
                                                                    value="phone"
                                                                    name="commpref"
                                                                >
                                                                <span class="custom-control-label">Phone Call</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Comment --}}
                                                <div class="col-sm-12">
                                                    <div class="mb-3 mb-md-4">
                                                        <label class="m-0 form-label" for="comment">
                                                            What questions can we answer for you?
                                                            <strong class="text-danger ps-1">*</strong>
                                                        </label>
                                                        <textarea
                                                            data-cy="formcontrol-textarea"
                                                            name="comment"
                                                            placeholder="Add a question or comment"
                                                            tabindex="6"
                                                            id="comment"
                                                            class="form-control h-100"
                                                            maxlength="2000"
                                                            required
                                                        ></textarea>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">

                                                {{-- Divider --}}
                                                <div class="col-12">
                                                    <div class="pt-4 border-top"></div>
                                                </div>

                                                {{-- Submit Button --}}
                                                <div class="col-sm-6 col-12">
                                                    <button
                                                        type="submit"
                                                        id="contact-submit-btn"
                                                        data-cy="btn-submit-form-wrapper"
                                                        class="text-start w-100 btn btn-primary btn-md"
                                                    >
                                                        <span class="btn-label">Continue</span>
                                                        <span class="btn-icon d-inline-block faIcon ofa-regular ofa-angle-right float-end text-white">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" fill="white" viewBox="0 0 24 24">
                                                                <path d="M10 17l5-5-5-5v10z" />
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                                {{-- ── END FORM WRAPPER ── --}}

                                {{-- ── SUCCESS BOX (shown on successful submission) ── --}}
                                <div id="contact-success-box" class="d-none">
                                    <div class="cElement cForm p-4 border rounded bg-white h-500 d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="mb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="11" stroke="#166c87" stroke-width="1.5"/>
                                                <path d="M7 12.5l3.5 3.5 6.5-7" stroke="#166c87" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <h2 class="fw-bold mb-2" style="color: #166c87;">Question Received!</h2>
                                        <p class="text-muted mb-0">
                                            Thank you for contacting {{ $dealerName }}!<br>
                                            We will get back to you as soon as possible.
                                        </p>
                                    </div>
                                </div>
                                {{-- ── END SUCCESS BOX ── --}}

                            </div>
                        </div>
                    </div>
                    <div class="sc-24764b04-0 kduvPm"></div>
                </div>
            </div>

            <!-- Dealership Info -->
            @include('frontend.partials.dealership-info')
        </div>
    </div>
@endsection