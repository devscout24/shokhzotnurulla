@extends('layouts.admin.app')

@section('title', __('Integrations') . ' | ' . $dealer->company_name)

@push('page-styles')
<style>
    .integration-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .integration-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .integration-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .integration-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .integration-logo {
        height: 32px;
        width: auto;
    }
    .status-badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
    }
    .status-configured { background: #e6f4ea; color: #1e8e3e; }
    .status-not-configured { background: #f1f3f4; color: #5f6368; }
    
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: #666; margin-bottom: 5px; }
    .form-control { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; }
    
    .btn-test { background: #f8f9fa; border: 1px solid #ddd; color: #444; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    .btn-save { background: #c0392b; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-deactivate { background: #fff; border: 1px solid #dc3545; color: #dc3545; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; margin-right: 5px; }
    .btn-deactivate:hover { background: #dc3545; color: #fff; }
</style>
@endpush

@section('page-content')
<main class="main-content">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 class="view-title">API Integrations</h2>
            <p style="color: #666; font-size: 14px;">Managing services for <strong>{{ $dealer->company_name }}</strong></p>
        </div>
        <a href="{{ route('admin.dealers.index', $dealer) }}" class="btn-test" style="text-decoration: none;">Back to Dealer</a>
    </div>

    <div class="integration-grid">
        <!-- Carfax -->
        <div class="integration-card" id="card-carfax">
            <div class="integration-header">
                <h3 style="font-size: 16px; margin: 0;">Carfax</h3>
                <span class="status-badge {{ $dealer->integrations->where('provider', 'carfax')->where('is_active', true)->first() ? 'status-configured' : 'status-not-configured' }}">
                    {{ $dealer->integrations->where('provider', 'carfax')->where('is_active', true)->first() ? 'Configured' : 'Not Configured' }}
                </span>
            </div>
            <form class="integration-form" data-provider="carfax">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="settings[username]" class="form-control" value="{{ $dealer->integrations->where('provider', 'carfax')->where('is_active', true)->first()?->settings['username'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="settings[password]" class="form-control" placeholder="••••••••">
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                    <div>
                        @if($dealer->integrations->where('provider', 'carfax')->where('is_active', true)->first())
                            <button type="button" class="btn-deactivate" data-provider="carfax">Unconfigure</button>
                        @endif
                        <button type="button" class="btn-test test-connection" data-provider="carfax">Test Connection</button>
                    </div>
                    <button type="submit" class="btn-save">Save Settings</button>
                </div>
            </form>
        </div>

        <!-- 700Credit -->
        <div class="integration-card" id="card-700credit">
            <div class="integration-header">
                <h3 style="font-size: 16px; margin: 0;">700Credit</h3>
                <span class="status-badge {{ $dealer->integrations->where('provider', '700credit')->where('is_active', true)->first() ? 'status-configured' : 'status-not-configured' }}">
                    {{ $dealer->integrations->where('provider', '700credit')->where('is_active', true)->first() ? 'Configured' : 'Not Configured' }}
                </span>
            </div>
            <form class="integration-form" data-provider="700credit">
                <div class="form-group">
                    <label class="form-label">API Key</label>
                    <input type="text" name="settings[api_key]" class="form-control" value="{{ $dealer->integrations->where('provider', '700credit')->where('is_active', true)->first()?->settings['api_key'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Dealer Code</label>
                    <input type="text" name="settings[dealer_code]" class="form-control" value="{{ $dealer->integrations->where('provider', '700credit')->where('is_active', true)->first()?->settings['dealer_code'] ?? '' }}">
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                    <div>
                        @if($dealer->integrations->where('provider', '700credit')->where('is_active', true)->first())
                            <button type="button" class="btn-deactivate" data-provider="700credit">Unconfigure</button>
                        @endif
                        <button type="button" class="btn-test test-connection" data-provider="700credit">Test Connection</button>
                    </div>
                    <button type="submit" class="btn-save">Save Settings</button>
                </div>
            </form>
        </div>

        <!-- Google Analytics 4 -->
        <div class="integration-card" id="card-ga4">
            <div class="integration-header">
                <h3 style="font-size: 16px; margin: 0;">Google Analytics 4</h3>
                <span class="status-badge {{ $dealer->integrations->where('provider', 'ga4')->where('is_active', true)->first() ? 'status-configured' : 'status-not-configured' }}">
                    {{ $dealer->integrations->where('provider', 'ga4')->where('is_active', true)->first() ? 'Configured' : 'Not Configured' }}
                </span>
            </div>
            <form class="integration-form" data-provider="ga4">
                <div class="form-group">
                    <label class="form-label">Measurement ID</label>
                    <input type="text" name="settings[measurement_id]" class="form-control" value="{{ $dealer->integrations->where('provider', 'ga4')->where('is_active', true)->first()?->settings['measurement_id'] ?? '' }}" placeholder="G-XXXXXXXXXX">
                    <small class="text-muted mt-1 d-block" style="font-size: 11px; color: #888;">Found in GA4 Admin > Data Streams</small>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                    <div>
                        @if($dealer->integrations->where('provider', 'ga4')->where('is_active', true)->first())
                            <button type="button" class="btn-deactivate" data-provider="ga4">Unconfigure</button>
                        @endif
                    </div>
                    <button type="submit" class="btn-save">Save Settings</button>
                </div>
            </form>
        </div>

        <!-- Google Tag Manager -->
        <div class="integration-card" id="card-gtm">
            <div class="integration-header">
                <h3 style="font-size: 16px; margin: 0;">Google Tag Manager</h3>
                <span class="status-badge {{ $dealer->integrations->where('provider', 'gtm')->where('is_active', true)->first() ? 'status-configured' : 'status-not-configured' }}">
                    {{ $dealer->integrations->where('provider', 'gtm')->where('is_active', true)->first() ? 'Configured' : 'Not Configured' }}
                </span>
            </div>
            <form class="integration-form" data-provider="gtm">
                <div class="form-group">
                    <label class="form-label">Container ID</label>
                    <input type="text" name="settings[container_id]" class="form-control" value="{{ $dealer->integrations->where('provider', 'gtm')->where('is_active', true)->first()?->settings['container_id'] ?? '' }}" placeholder="GTM-XXXXXXX">
                    <small class="text-muted mt-1 d-block" style="font-size: 11px; color: #888;">Format: GTM- followed by alphanumeric characters</small>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                    <div>
                        @if($dealer->integrations->where('provider', 'gtm')->where('is_active', true)->first())
                            <button type="button" class="btn-deactivate" data-provider="gtm">Unconfigure</button>
                        @endif
                    </div>
                    <button type="submit" class="btn-save">Save Settings</button>
                </div>
            </form>
        </div>

        <!-- Stripe -->
        <div class="integration-card" id="card-stripe">
            <div class="integration-header">
                <h3 style="font-size: 16px; margin: 0;">Stripe Gateway</h3>
                <span class="status-badge {{ $dealer->integrations->where('provider', 'stripe')->where('is_active', true)->first() ? 'status-configured' : 'status-not-configured' }}">
                    {{ $dealer->integrations->where('provider', 'stripe')->where('is_active', true)->first() ? 'Configured' : 'Not Configured' }}
                </span>
            </div>
            <form class="integration-form" data-provider="stripe">
                <div class="form-group">
                    <label class="form-label">Publishable Key</label>
                    <input type="text" name="settings[public_key]" class="form-control" value="{{ $dealer->integrations->where('provider', 'stripe')->where('is_active', true)->first()?->settings['public_key'] ?? '' }}" placeholder="pk_live_...">
                </div>
                <div class="form-group">
                    <label class="form-label">Secret Key</label>
                    <input type="password" name="settings[secret_key]" class="form-control" placeholder="sk_live_... (Hidden for security)">
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                    <div>
                        @if($dealer->integrations->where('provider', 'stripe')->where('is_active', true)->first())
                            <button type="button" class="btn-deactivate" data-provider="stripe">Unconfigure</button>
                        @endif
                        <button type="button" class="btn-test test-connection" data-provider="stripe">Test Connection</button>
                    </div>
                    <button type="submit" class="btn-save">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

@push('page-scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Handle Form Submission
        $('.integration-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const provider = $form.data('provider');
            const formData = $form.serializeArray();
            
            // Construct the data object
            const data = {
                provider: provider,
                is_active: true,
                settings: {},
                _token: '{{ csrf_token() }}'
            };
            
            formData.forEach(item => {
                if (item.name.startsWith('settings[')) {
                    const key = item.name.match(/\[(.*?)\]/)[1];
                    data.settings[key] = item.value;
                }
            });

            $.ajax({
                url: '{{ route("admin.dealers.integrations.save", $dealer) }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error saving settings.');
                }
            });
        });

        // Handle Unconfigure
        $('.btn-deactivate').on('click', function() {
            const provider = $(this).data('provider');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to make this integration inactive?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, deactivate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/dealers/{{ $dealer->id }}/integrations/${provider}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Error unconfiguring integration.');
                        }
                    });
                }
            });
        });

        // Handle Connection Testing
        $('.test-connection').on('click', function() {
            const provider = $(this).data('provider');
            const $btn = $(this);
            $btn.text('Testing...').prop('disabled', true);

            // First save current values before testing
            const $form = $btn.closest('form');
            const formData = $form.serializeArray();
            const data = {
                provider: provider,
                settings: {},
                test_connection: true,
                _token: '{{ csrf_token() }}'
            };
            
            formData.forEach(item => {
                if (item.name.startsWith('settings[')) {
                    const key = item.name.match(/\[(.*?)\]/)[1];
                    data.settings[key] = item.value;
                }
            });

            $.ajax({
                url: '{{ route("admin.dealers.integrations.save", $dealer) }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Connection failed.');
                },
                complete: function() {
                    $btn.text('Test Connection').prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush

