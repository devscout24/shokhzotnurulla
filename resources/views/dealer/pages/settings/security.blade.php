@extends('layouts.dealer.app')

@section('title', __('Security') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/settings.css',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content" data-view="security">
            <div class="page-body">
                <div class="settings-card">
                  <div class="section-title">{{ __('Account Security') }}</div>
                  <form method="POST" action="{{ route('dealer.settings.security.update') }}" id="security-form">
                        @csrf
                        @method('PATCH')
                        <div class="form-row">
                            <div class="form-input-col">
                                <label for="is_2fa_required" class="form-label">{{ __('Require Two-factor Authentication (2FA)') }}</label>
                                <div class="select-wrapper">
                                    <select name="is_2fa_required" id="is_2fa_required" class="form-control @error('is_2fa_required') is-invalid @enderror" aria-describedby="2faHelp">
                                        <option value="" selected disabled>{{ __('Select option') }}</option>
                                        <option value="1" {{ $user->is_2fa_required == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ $user->is_2fa_required == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                                <div id="2faHelp" class="form-text">{{ __('Add an additional layer of security to your account by requiring more than just a password') }}</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-input-col">
                                <label for="password_complexity" class="form-label">{{ __('Enforce Password Complexity') }}</label>
                                <div class="select-wrapper">
                                    <select name="password_complexity" id="password_complexity" class="form-control @error('password_complexity') is-invalid @enderror" aria-describedby="passwordComplexityHelp">
                                        <option value="" selected disabled>{{ __('Select option') }}</option>
                                        <option value="1" {{ $user->password_complexity == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ $user->password_complexity == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                                <div id="passwordComplexityHelp" class="form-text">{{ __('Password complexity includes a minimum of 8 characters, 1 lower-case letter, 1 upper-case letter, 1 number, and 1 special character.') }}</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-input-col">
                                <label for="password_reuse_policy" class="form-label">{{ __('Password Reuse Policy') }}</label>
                                <div class="select-wrapper">
                                    <select name="password_reuse_policy" id="password_reuse_policy" class="form-control @error('password_reuse_policy') is-invalid @enderror" aria-describedby="passwordReuseHelp">
                                        <option value="" selected disabled>{{ __('Select option') }}</option>
                                        <option value="1" {{ $user->password_reuse_policy == 1 ? 'selected' : '' }}>{{ __('1') }}</option>
                                        <option value="2" {{ $user->password_reuse_policy == 2 ? 'selected' : '' }}>{{ __('2') }}</option>
                                        <option value="3" {{ $user->password_reuse_policy == 3 ? 'selected' : '' }}>{{ __('3') }}</option>
                                        <option value="4" {{ $user->password_reuse_policy == 4 ? 'selected' : '' }}>{{ __('4') }}</option>
                                        <option value="5" {{ $user->password_reuse_policy == 5 ? 'selected' : '' }}>{{ __('5') }}</option>
                                    </select>
                                </div>
                                <div id="passwordReuseHelp" class="form-text">{{ __('When updating a password, prevent user from using their last X passwords') }}</div>
                            </div>
                        </div>

                        <button type="sumbit" class="btn-save">
                            <i class="bi bi-check2"></i> {{ __('Save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection