@extends('layouts.auth')

@section('title', __('Forgot Password') . ' | ' . __(config('app.name')))

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0 align-items-center min-vh-100">

            {{-- CENTERED CARD --}}
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 col-10 mx-auto d-flex justify-content-center align-items-center">
                <div class="forgot-wrapper d-flex flex-column align-items-center w-100">

                    <div class="login-card w-100">

                        {{-- Logo --}}
                        <div class="text-center mb-3">
                            <img src="{{ asset('assets/panels/common/images/logos/AI.jpeg') }}" class="forgot-logo" alt="Logo">
                        </div>

                        {{-- Title --}}
                        <h6 class="forgot-title text-center">{{ __('Forgot Password') }}</h6>

                        {{-- Description --}}
                        <p class="forgot-description">
                            {{ __('Enter your email address and we will send you an email with instructions to reset your password.') }}
                        </p>

                        {{-- Form --}}
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">{{ __('Email address') }}</label>
                                <input
                                    id="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                    autofocus
                                    placeholder="{{ __('Email') }}"
                                >
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-login w-100">
                                {{ __('Send Instructions') }}
                            </button>

                        </form>

                    </div>

                    {{-- Back to Login --}}
                    <a href="{{ route('login') }}" class="back-to-login mt-4">
                        <span>&larr;</span> {{ __('Back to Login') }}
                    </a>

                </div>
            </div>

        </div>
    </div>
@endsection