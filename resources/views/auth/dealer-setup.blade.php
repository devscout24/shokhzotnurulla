@extends('layouts.auth')

@section('title', __('Setup Account') . ' | ' . __(config('app.name')))

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0 align-items-center min-vh-100">

            {{-- CENTERED CARD --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 col-10 mx-auto d-flex justify-content-center align-items-center">
                <div class="forgot-wrapper d-flex flex-column align-items-center w-100">

                    <div class="login-card w-100">

                        {{-- Logo --}}
                        <div class="text-center mb-3">
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
                        </div>

                        {{-- Title --}}
                        <h6 class="forgot-title text-center">{{ __('Setup Your Account') }}</h6>

                        {{-- Success Status --}}
                        @if (session('status'))
                            <div class="alert alert-success mt-3 mb-0" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{-- Form --}}
                        <form method="POST" action="{{ route('dealer.setup.submit') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label class="form-label">{{ __('Email address') }}</label>
                                <input
                                    id="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email"
                                    value="{{ $email ?? old('email') }}"
                                    required
                                    autocomplete="email"
                                    autofocus
                                    placeholder="{{ __('Email') }}"
                                    readonly
                                >
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Password') }}</label>
                                 <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <button type="submit" class="btn btn-login w-100">
                                {{ __('Set Password & Verify') }}
                            </button>

                        </form>

                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
