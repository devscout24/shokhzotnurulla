@extends('layouts.dealer.app')

@section('title', __('Settings') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/settings.css',
        'resources/js/dealer/pages/settings.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content" data-view="profile">
            <div class="page-body">

                {{-- ── MY PROFILE ── --}}
                <div class="settings-card">
                    <div class="section-title">{{ __('My profile') }}</div>
                    <form method="POST" action="{{ route('dealer.settings.profile.update') }}" id="profile-form">
                        @csrf
                        @method('PATCH')

                        <div class="form-row">
                            <div class="form-label-col">{{ __('First name') }}</div>
                            <div class="form-input-col">
                                <input type="text" name="first_name"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       minlength="3" maxlength="50"
                                       value="{{ $user->first_name }}"
                                       placeholder="{{ __('First name') }}" required>
                                @error('first_name')
                                    <div class="mt-1 text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-label-col">{{ __('Last name') }}</div>
                            <div class="form-input-col">
                                <input type="text" name="last_name"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       minlength="3" maxlength="50"
                                       value="{{ $user->last_name }}"
                                       placeholder="{{ __('Last name') }}" required>
                                @error('last_name')
                                    <div class="mt-1 text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-label-col">{{ __('Email address') }}</div>
                            <div class="form-input-col">
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ $user->email }}"
                                       placeholder="{{ __('Email address') }}" required>
                                @error('email')
                                    <div class="mt-1 text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-label-col">{{ __('Time zone') }}</div>
                            <div class="form-input-col">
                                <div class="select-wrapper">
                                    <select name="timezone"
                                            class="form-control @error('timezone') is-invalid @enderror">
                                        <option value="" disabled>{{ __('Select Time zone') }}</option>
                                        @foreach($timezones as $tz)
                                            <option value="{{ $tz }}" {{ $user->timezone == $tz ? 'selected' : '' }}>
                                                {{ $tz }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('timezone')
                                    <div class="mt-1 text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn-save">
                            <i class="bi bi-check2"></i> {{ __('Save') }}
                        </button>
                    </form>
                </div>

                {{-- ── CHANGE PASSWORD ── --}}
                <div class="settings-card section-gap">
                    <div class="section-title">{{ __('Change password') }}</div>

                    <form id="password-form"
                          data-url="{{ route('dealer.settings.password.update') }}"
                          autocomplete="off">
                        @csrf

                        <div class="form-row">
                            <div class="form-label-col">{{ __('Old password') }}</div>
                            <div class="form-input-col">
                                <input type="password" name="old_password" id="oldPassword"
                                       class="form-control"
                                       placeholder="{{ __('Old password') }}" required>
                                <div class="mt-1 text-danger d-none" id="errOldPassword"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-label-col">{{ __('New password') }}</div>
                            <div class="form-input-col">
                                <input type="password" name="password" id="newPassword"
                                       class="form-control"
                                       placeholder="{{ __('New password') }}"
                                       autocomplete="new-password" required>
                                <div class="mt-1 text-danger d-none" id="errPassword"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-label-col">{{ __('Confirm new password') }}</div>
                            <div class="form-input-col">
                                <input type="password" name="password_confirmation" id="confirmPassword"
                                       class="form-control"
                                       placeholder="{{ __('Confirm new password') }}"
                                       autocomplete="new-password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn-save" id="btnSavePassword">
                            <i class="bi bi-check2"></i> {{ __('Save') }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>
@endsection