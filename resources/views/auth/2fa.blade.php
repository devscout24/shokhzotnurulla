@extends('layouts.auth')

@section('title', __('Two-Factor Authentication') . ' | ' . __(config('app.name')))

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0 align-items-center min-vh-100">

            <!-- LEFT PANEL -->
            <div class="col-xl-7 col-lg-6 d-none d-lg-flex justify-content-center align-items-center">
                <div class="left-panel">
                    <div class="content-wrapper">
                        <p class="top-label">{{ __('SECURE YOUR ACCOUNT') }}</p>

                        <h1 class="main-heading">
                            <img src="{{ asset('assets/panels/common/images/logos/AI-small.png') }}" class="heading-icon" alt="Icon">
                            {{ __('Two-Factor Authentication') }}
                        </h1>

                        <h5 class="sub-heading">
                            <strong>{{ __('Extra layer of security:') }}</strong> {{ __('Protect your dealer account from unauthorized access.') }}
                        </h5>

                        <p class="description">
                            {{ __('Your account is protected by Two-Factor Authentication. Please open your Google Authenticator app and enter the 6-digit code to continue.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10 mx-auto d-flex justify-content-center align-items-center">
                <div class="login-wrapper d-flex flex-column">
                    <div class="login-card">

                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/panels/common/images/logos/AI.jpeg') }}" class="login-logo" alt="Logo">
                            <h6 class="mt-3">{{ __('Two-Factor Authentication') }}</h6>
                        </div>

                        <form method="POST" action="{{ route('dealer.2fa.verify.post') }}" id="loginForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">{{ __('Authenticator Code') }}</label>
                                <input id="one_time_password" type="text" class="form-control @error('one_time_password') is-invalid @enderror" name="one_time_password" required autofocus placeholder="{{ __('Enter 6-digit code') }}">

                                @error('one_time_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-login w-100">
                                {{ __('Verify') }}
                            </button>

                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-muted">
                                {{ __('Cancel and Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>

                    </div>
                    <p class="privacy-text text-center mt-5"><a href="#" class="privacy-link">{{ __('Privacy Policy') }}</a></p>
                </div>
            </div>

        </div>
    </div>
@endsection
