@extends('layouts.auth')

@section('title', __('Login') . ' | ' . __(config('app.name')))

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
                            <h6 class="mt-3">{{ __('Sign In') }}</h6>
                        </div>

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('Email address') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('Email') }}">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                @if (Route::has('password.request'))
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label">{{ __('Password') }}</label>
                                        <a href="{{ route('password.request') }}" class="forgot-link" tabindex="-1">{{ __('Forgot password?') }}</a>
                                    </div>
                                @endif
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-login w-100">
                                {{ __('Sign In') }}
                            </button>

                        </form>

                    </div>
                    <p class="privacy-text text-center mt-5"><a href="#" class="privacy-link">{{ __('Privacy Policy') }}</a></p>
                </div>
            </div>

        </div>
    </div>
@endsection