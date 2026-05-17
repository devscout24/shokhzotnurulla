@extends('layouts.dealer.app')

@section('title', __('Authentication') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/settings.css',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content" data-view="authentication">
            <div class="page-body">
                <div class="settings-card">
                    <div class="section-title">{{ __('Two-Factor Authentication (2FA)') }}</div>

                    @if(!$user->google2fa_secret)
                        <p class="mb-4">{{ __('Set up two-factor authentication by scanning the barcode below. Alternatively, you can use the code') }} <strong>{{ $secret }}</strong></p>
                        
                        <div class="mb-4">
                            <div>
                                <img src="data:image/svg+xml;base64,{{ $QR_Image }}" alt="QR Code">
                            </div>
                        </div>

                        <form method="POST" action="{{ route('dealer.2fa.enable') }}">
                            @csrf
                            <div class="form-row">
                                <div class="form-input-col">
                                    <label for="one_time_password" class="form-label">{{ __('Enter Google Authenticator Code') }}</label>
                                    <input type="text" name="one_time_password" id="one_time_password" class="form-control @error('one_time_password') is-invalid @enderror" required>
                                    @error('one_time_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn-save mt-3">
                                <i class="bi bi-shield-lock"></i> {{ __('Verify & Enable 2FA') }}
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>
                                {{ __('Two-factor authentication is currently enabled on your account.') }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('dealer.2fa.disable') }}" onsubmit="return confirm('Are you sure you want to disable Two-Factor Authentication?');">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-shield-x"></i> {{ __('Disable 2FA') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection