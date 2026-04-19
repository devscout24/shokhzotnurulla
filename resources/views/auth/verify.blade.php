@extends('layouts.auth')

@section('title', __('Verify Your Email Address') . ' | ' . __(config('app.name')))

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0 align-items-center min-vh-100">

            <!-- LEFT PANEL -->
            <div class="col-xl-7 col-lg-6 d-none d-lg-flex justify-content-center align-items-center">
                <div class="left-panel">
                    <div class="content-wrapper">
                        <p class="top-label">{{ __('INTRODUCING THE NEW AND IMPROVED') }}</p>

                        <h1 class="main-heading">
                            <img src="{{ asset('assets/panels/common/images/logos/AI-small.png') }}" class="heading-icon" alt="Icon">
                            {{ __('FuelGuage AI Descriptions') }}
                        </h1>

                        <h5 class="sub-heading">
                            <strong>{{ __('Stop Writing, Start Ranking:') }}</strong> {{ __('Your inventory just became your dealership\'s most powerful SEO engine') }}
                        </h5>

                        <p class="description">
                            {{ __('Every vehicle that comes and goes from your lot can now leave behind something more valuable than a sale: rich, engaging content that compounds over time. Think of FuelBot AI Descriptions as turning every car listing into a blog post—except it writes itself, automatically, on your behalf. Unlike plain text descriptions used across most dealer sites, FuelBot generates structured, SEO-ready content with headings, sections, and internal links.') }}
                        </p>

                        <div class="btn-group-custom mt-4">
                            <a href="#" class="btn btn-primary-custom">{{ __('Enroll in FuelBot') }}</a>
                            <a href="#" class="btn btn-outline-custom">{{ __('Learn More') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10 mx-auto d-flex justify-content-center align-items-center">
                <div class="login-wrapper d-flex flex-column">
                    <div class="login-card">

                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/panels/common/images/logos/AI.jpeg') }}" class="login-logo" alt="Logo">
                            <h6 class="mt-3">{{ __('Verify Your Email') }}</h6>
                        </div>

                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        <p>
                            {{ __('Before proceeding, please check your email for a verification link.') }}<br>
                            {{ __('If you did not receive the email, click below button to request another') }}
                        </p>
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-login w-100">{{ __('Resend Email Verification Link') }}</button>.
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection