@extends('layouts.admin.app')

@section('title', __('Restricted Credits') . ' | ' . __(config('app.name')))

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

    .key-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 20px;
        margin-bottom: 16px;
    }
    .key-status.configured {
        background: #e6f4ea;
        color: #1e8e3e;
        border: 1px solid #ceead6;
    }
    .key-status.not-configured {
        background: #fef7e0;
        color: #b06000;
        border: 1px solid #fde293;
    }
    .key-hint {
        font-size: 12px;
        color: #666;
        margin-top: 6px;
        line-height: 1.5;
    }
    .input-toggle-wrapper {
        position: relative;
    }
    .input-toggle-wrapper .form-control {
        padding-right: 42px;
    }
    .toggle-password {
        position: absolute;
        right: 1px;
        top: 1px;
        bottom: 1px;
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        cursor: pointer;
        color: #888;
        transition: color 0.2s;
    }
    .toggle-password:hover {
        color: #333;
    }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header">
        <h2 class="view-title">Restricted Credits</h2>
    </div>

    <hr>

    <div class="view-content">
        @if(session('success'))
            <div class="alert alert-success mb-4" style="padding: 12px 16px; border-radius: 6px; background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; font-size: 13px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4" style="padding: 12px 16px; border-radius: 6px; background: #fdecea; color: #d93025; border: 1px solid #f5c6cb; font-size: 13px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="settings-card" style="max-width: 640px;">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px; color: #333;">Vehicle Databases API</h3>

            @if($isConfigured)
                <div class="key-status configured">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Configured
                </div>
            @else
                <div class="key-status not-configured">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Not configured
                </div>
            @endif

            <form action="{{ route('admin.restricted-credits.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label class="form-label" for="vehicle_databases_api_key">VEHICLE_DATABASES_API_KEY</label>
                    <div class="input-toggle-wrapper">
                        <input
                            type="password"
                            id="vehicle_databases_api_key"
                            name="vehicle_databases_api_key"
                            class="form-control"
                            placeholder="{{ $isConfigured ? '••••••••••••••••' : 'Paste your API key here' }}"
                            value="{{ $apiKey }}"
                            autocomplete="off"
                        >
                        <button type="button" class="toggle-password" onclick="toggleApiKeyVisibility()" tabindex="-1">
                            <svg id="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    @error('vehicle_databases_api_key')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <p class="key-hint">
                        Used by VIN Decode V2 to look up vehicle specs from <a href="https://www.vehicledatabases.com" target="_blank" rel="noopener">vehicledatabases.com</a>.
                    </p>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn-save">Save API Key</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

@push('page-scripts')
<script>
    function toggleApiKeyVisibility() {
        const input = document.getElementById('vehicle_databases_api_key');
        const open = document.getElementById('eye-open');
        const closed = document.getElementById('eye-closed');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        open.style.display = isPassword ? 'none' : '';
        closed.style.display = isPassword ? '' : 'none';
    }
</script>
@endpush
