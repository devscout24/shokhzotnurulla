@extends('layouts.admin.app')

@section('title', __('Admin Restricted Sites') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    .settings-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 24px;
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

    .table-container {
        overflow-x: auto;
        margin-top: 15px;
    }
    .sites-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .sites-table th {
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        background-color: #f9f9f9;
        border-bottom: 2px solid #eee;
    }
    .sites-table td {
        padding: 12px 16px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #eee;
    }
    .sites-table tr:hover {
        background-color: #fcfcfc;
    }
    .btn-delete {
        background: transparent;
        color: #d93025;
        border: 1px solid #d93025;
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete:hover {
        background-color: #d93025;
        color: #fff;
    }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header">
        <h2 class="view-title">Admin Restricted Sites</h2>
    </div>

    <hr>

    <div class="view-content">
        @if(session('success'))
            <div class="alert alert-success mb-4" style="padding: 12px 16px; border-radius: 6px; background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; font-size: 13px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: flex; gap: 24px; flex-wrap: wrap;">
            <!-- Section A: Toggle System Settings -->
            <div class="settings-card" style="flex: 1; min-width: 320px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px; color: #333;">Login Domain Restriction Toggle</h3>
                
                <form action="{{ route('admin.restricted-sites.setting') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="hidden" name="restricted_login_enabled" value="0">
                            <input type="checkbox" id="restricted_login_enabled" name="restricted_login_enabled" value="1" {{ old('restricted_login_enabled', $setting->value) == '1' ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer; accent-color: purple;">
                            <label for="restricted_login_enabled" class="form-label" style="margin-bottom: 0; font-weight: 500; cursor: pointer;">Enable Domain Restriction for Admins</label>
                        </div>
                        <p style="font-size: 12px; color: #666; margin-top: 8px; margin-left: 28px; line-height: 1.5;">
                            When enabled, admin/system staff users can only log in to the application if the request host matches one of the authorized domains. This applies to all system roles.
                        </p>
                    </div>
                    <div style="margin-top: 24px;">
                        <button type="submit" class="btn-save">Save Toggle Setting</button>
                    </div>
                </form>
            </div>

            <!-- Section B: Add Allowed Host -->
            <div class="settings-card" style="flex: 1; min-width: 320px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px; color: #333;">Authorize New Domain</h3>

                <form action="{{ route('admin.restricted-sites.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="domain">Allowed Domain (Hostname)</label>
                        <input type="text" id="domain" name="domain" class="form-control" placeholder="e.g. admin.yourdomain.com" value="{{ old('domain') }}" required>
                        @error('domain')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="margin-top: 24px;">
                        <button type="submit" class="btn-save">Add Authorized Domain</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section B: Listing Table -->
        <div class="settings-card" style="margin-top: 30px;">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px; color: #333;">Authorized Domains List</h3>
            
            <div class="table-container">
                <table class="sites-table">
                    <thead>
                        <tr>
                            <th>Domain (Hostname)</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sites as $site)
                            <tr>
                                <td><strong>{{ $site->domain }}</strong></td>
                                <td style="text-align: right;">
                                    <form action="{{ route('admin.restricted-sites.destroy', $site->id) }}" method="POST" data-swal-confirm="Are you sure you want to remove this domain?" data-swal-title="Are you sure?" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="padding: 24px; text-align: center; color: #888; font-size: 14px;">
                                    No authorized domains have been added yet. Admin accounts will not be able to login if restriction is toggled on.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection
