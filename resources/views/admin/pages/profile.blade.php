@extends('layouts.admin.app')

@section('title', __('Admin Profile') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    .profile-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 24px;
        max-width: 600px;
        margin-top: 20px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .form-control:focus {
        border-color: purple;
        outline: none;
        box-shadow: 0 0 0 3px rgba(128, 0, 128, 0.1);
    }
    .text-danger { color: #d93025; font-size: 12px; margin-top: 4px; }
    
    .btn-save {
        background: purple;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover { background: #6b21a8; }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header">
        <h2 class="view-title">Admin Profile</h2>
    </div>

    <hr>

    <div class="view-content">
        @if(session('success'))
            <div class="alert alert-success mb-4" style="padding: 12px 16px; border-radius: 6px; background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; font-size: 13px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-card">
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
                
                <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 15px;">Change Password</h4>
                <p style="font-size: 12px; color: #888; margin-bottom: 20px;">Leave blank if you don't want to change it.</p>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control">
                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn-save">Save Profile</button>
                </div>
            </form>
        </div>

        {{-- Two-Factor Authentication Card --}}
        <div class="profile-card" id="2fa-section" style="margin-top: 30px;">
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 20px; color: #333;">
                <i class="bi bi-shield-lock" style="margin-right: 6px;"></i>Two-Factor Authentication (2FA)
            </h4>

            @if(!$user->google2fa_secret)
                <p style="font-size: 13px; color: #555; margin-bottom: 16px; line-height: 1.6;">
                    Set up two-factor authentication by scanning the QR code below with your Google Authenticator app.
                    Alternatively, you can manually enter the secret key: <strong style="color: #333; letter-spacing: 1px;">{{ $secret }}</strong>
                </p>

                <div style="margin-bottom: 20px; background: #f8f8f8; display: inline-block; padding: 16px; border-radius: 8px; border: 1px solid #eee;">
                    <img src="data:image/svg+xml;base64,{{ $QR_Image }}" alt="QR Code" style="display: block;">
                </div>

                <form method="POST" action="{{ route('admin.2fa.enable') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="one_time_password">Enter Google Authenticator Code</label>
                        <input type="text" name="one_time_password" id="one_time_password"
                               class="form-control" placeholder="Enter 6-digit code" required
                               style="max-width: 280px; letter-spacing: 4px; font-size: 16px; text-align: center;">
                        @error('one_time_password') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-save">
                            <i class="bi bi-shield-check" style="margin-right: 4px;"></i> Verify & Enable 2FA
                        </button>
                    </div>
                </form>
            @else
                <div style="padding: 14px 18px; border-radius: 6px; background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; font-size: 13px; display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                    <i class="bi bi-check-circle-fill"></i>
                    Two-Factor Authentication is currently <strong>enabled</strong> on your account.
                </div>

                <form method="POST" action="{{ route('admin.2fa.disable') }}" data-swal-confirm="Are you sure you want to disable Two-Factor Authentication? This will reduce your account security." data-swal-title="Are you sure?">
                    @csrf
                    <button type="submit" style="background: transparent; color: #d93025; border: 1px solid #d93025; border-radius: 6px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 13px;"
                            onmouseover="this.style.background='#d93025'; this.style.color='#fff';"
                            onmouseout="this.style.background='transparent'; this.style.color='#d93025';">
                        <i class="bi bi-shield-x" style="margin-right: 4px;"></i> Disable 2FA
                    </button>
                </form>
            @endif
        </div>
    </div>
</main>
@endsection
