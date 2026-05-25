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
                            <svg style="height:60px;width:auto;" viewBox="0 0 164.24 163.51" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="lg1" gradientUnits="userSpaceOnUse" x1="26.335" y1="8.3599" x2="160.376" y2="99.8003">
                                        <stop offset="0" style="stop-color:#B379FD"/>
                                        <stop offset="1" style="stop-color:#0088EA"/>
                                    </linearGradient>
                                    <linearGradient id="lg2" gradientUnits="userSpaceOnUse" x1="29.9617" y1="78.4065" x2="107.2338" y2="167.6826">
                                        <stop offset="0" style="stop-color:#A6A3F8"/>
                                        <stop offset="1" style="stop-color:#0F92FA"/>
                                    </linearGradient>
                                    <linearGradient id="lg3" gradientUnits="userSpaceOnUse" x1="127.5493" y1="142.9003" x2="89.232" y2="104.5831">
                                        <stop offset="0.1869" style="stop-color:#51BFF5"/>
                                        <stop offset="1" style="stop-color:#ABCFFC"/>
                                    </linearGradient>
                                </defs>
                                <path fill="url(#lg1)" d="M163.3,81.76c0,16.59-4.95,32.03-13.47,44.9L118.1,73.61l-0.8-2.08c-1.54-4.13-3.68-8.04-6.41-11.5c-7.35-9.28-19.43-15.69-31.45-14.01c-2.32,0.31-4.61,0.91-6.81,1.69C57,53.23,49.18,68.18,40.95,81.45c-0.39-0.68-0.78-1.35-1.17-2.03c-1.06-1.83-2.11-3.66-3.17-5.49c-1.55-2.69-3.11-5.38-4.66-8.07c-1.88-3.26-3.76-6.51-5.64-9.77c-2.04-3.53-4.08-7.06-6.11-10.59c-4.09-7.08-8.54-14.26-12.81-21.35c-3.19-5.3-9.53-13.66-4.42-19.75C4.57,2.47,6.8,1.1,9.24,0.53C9.62,0.44,10,0.38,10.39,0.33c0.49-0.07,0.99-0.1,1.5-0.1h69.88C126.8,0.23,163.3,36.74,163.3,81.76z"/>
                                <path fill="url(#lg2)" d="M61.77,118.35l26.1,44.71c-2.01,0.15-4.05,0.23-6.1,0.23c0,0-69.86,0-69.88,0c-3,0-5.93-1.18-8.09-3.26c-2.1-2.02-3.62-4.81-3.11-7.85c0.55-3.29,3.18-6.49,4.82-9.33c2.59-4.49,5.18-8.98,7.78-13.47c3.94-6.83,7.88-13.66,11.83-20.49c0.8-1.38,1.6-2.76,2.39-4.15c0,0,13.41-23.24,13.42-23.25c0.01-0.02,0.02-0.03,0.03-0.05C49.18,68.18,57,53.23,72.63,47.71c2.2-0.78,4.49-1.38,6.81-1.69c12.02-1.68,24.1,4.73,31.45,14.01c2.73,3.46,4.87,7.37,6.41,11.5l0.8,2.08C125.25,125.81,61.77,118.35,61.77,118.35z"/>
                                <path fill="url(#lg3)" d="M149.83,126.66c-2.28,3.45-4.81,6.72-7.59,9.78c-0.91,1.01-1.85,2-2.82,2.97c-13.4,13.4-31.47,22.17-51.55,23.65l-26.1-44.71c0,0,63.48,7.46,56.33-44.74L149.83,126.66z"/>
                            </svg>
                            <h6 class="mt-3">{{ __('Two-Factor Authentication') }}</h6>
                        </div>

                        <form method="POST" action="{{ $verifyRoute ?? route('dealer.2fa.verify.post') }}" id="loginForm">
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
