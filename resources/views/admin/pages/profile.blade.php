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
        border-color: #ce4f4b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.1);
    }
    .text-danger { color: #d93025; font-size: 12px; margin-top: 4px; }
    
    .btn-save {
        background: #ce4f4b;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover { background: #a93226; }
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
    </div>
</main>
@endsection
