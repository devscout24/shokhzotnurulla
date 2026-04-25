@extends('layouts.dealer.app')
@section('title', __('Digital Retail Settings') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/website-settings-retail.css',
        // 'resources/js/dealer/pages/website-settings-retail.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Website Settings</h2>
        </div>

        <div class="view-content" data-view="digital-retail">
            <div class="ws-layout">

                <aside class="ws-sidebar">
                    <a href="{{ route('dealer.website.settings.general') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-info-circle"></i></span>
                        <span>General</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.locations') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-geo-alt"></i></span>
                        <span>Locations &amp; Hours</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.banners') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-card-image"></i></span>
                        <span>Banners /<br>Announcements</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.finance') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-percent"></i></span>
                        <span>Interest Rates</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.retail') }}" class="menu-item active">
                        <span class="ws-icon"><i class="bi bi-grid"></i></span>
                        <span>Digital Retail</span>
                    </a>
                    {{-- <a href="{{ route('dealer.website.settings.domains') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-globe"></i></span>
                        <span>Domains</span>
                    </a> --}}
                    <a href="{{ route('dealer.website.settings.redirects') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-arrow-left-right"></i></span>
                        <span>Redirects</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.ips.index') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-hdd-network"></i></span>
                        <span>Dealer IP Addresses</span>
                    </a>
                </aside>

                <div class="dr-content">
                    <div class="dr-card">
                        <div class="dr-card-title">Digital Retail</div>

                        <div class="dr-row">
                            <div class="dr-label">Free shipping miles</div>
                            <div class="dr-input-col">
                                <input type="number"
                                       name="free_shipping_miles"
                                       class="dr-control"
                                       placeholder="250"
                                       value="{{ old('free_shipping_miles', $settings->shipping_free_miles ?? 250) }}">
                            </div>
                        </div>

                        <div class="dr-row">
                            <div class="dr-label">Shipping discount outside of free radius</div>
                            <div class="dr-input-col">
                                <span class="dr-prefix">$</span>
                                <input type="number"
                                       name="shipping_discount"
                                       class="dr-control has-prefix"
                                       placeholder="199"
                                       value="{{ old('shipping_discount', $settings->shipping_discount_dollars ?? 199.00) }}">
                            </div>
                        </div>

                        <div class="dr-row">
                            <div class="dr-label">Deposit amount to reserve vehicle</div>
                            <div class="dr-input-col">
                                <span class="dr-prefix">$</span>
                                <input type="number"
                                       name="deposit_amount"
                                       class="dr-control has-prefix"
                                       placeholder="500"
                                       value="{{ old('deposit_amount', $settings->deposit_minimum ?? 500.00) }}">
                            </div>
                        </div>

                        <div class="dr-row">
                            <div class="dr-label">Deposit hold hours</div>
                            <div class="dr-input-col">
                                <input type="number"
                                       name="deposit_hold_hours"
                                       class="dr-control"
                                       placeholder="48"
                                       value="{{ old('deposit_hold_hours', $settings->deposit_hold_hours ?? 48) }}">
                            </div>
                        </div>

                        <div class="dr-row">
                            <div class="dr-label">Digital Retail hold hours</div>
                            <div class="dr-input-col">
                                <input type="number"
                                       name="retail_hold_hours"
                                       class="dr-control"
                                       placeholder="72"
                                       value="{{ old('retail_hold_hours', $settings->digital_retail_hold_hours ?? 72) }}">
                            </div>
                        </div>

                        <div class="dr-row">
                            <div class="dr-label">Number of days a trade offer is valid</div>
                            <div class="dr-input-col">
                                <input type="number"
                                       name="trade_offer_days"
                                       class="dr-control"
                                       placeholder="14"
                                       value="{{ old('trade_offer_days', $settings->trade_days_valid ?? 14) }}">
                            </div>
                        </div>

                        <div class="dr-save-wrap">
                            <button type="button" class="dr-btn-save" id="btnSaveDigitalRetail">
                                &#10003; Save
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('page-scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showToast(type, message) {
            if (type === 'success') toastr.success(message);
            else toastr.error(message);
        }

        document.getElementById('btnSaveDigitalRetail').addEventListener('click', function () {
            const formData = new FormData();
            formData.append('free_shipping_miles', document.querySelector('[name="free_shipping_miles"]').value);
            formData.append('shipping_discount', document.querySelector('[name="shipping_discount"]').value);
            formData.append('deposit_amount', document.querySelector('[name="deposit_amount"]').value);
            formData.append('deposit_hold_hours', document.querySelector('[name="deposit_hold_hours"]').value);
            formData.append('retail_hold_hours', document.querySelector('[name="retail_hold_hours"]').value);
            formData.append('trade_offer_days', document.querySelector('[name="trade_offer_days"]').value);
            formData.append('_token', csrfToken);
            formData.append('_method', 'PATCH');

            fetch('{{ route("dealer.website.settings.retail.update") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json', // Ensure JSON response
                },
                body: formData,
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    // Throw an error with the response data
                    throw { response, data };
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                } else {
                    showToast('error', 'Failed to save.');
                }
            })
            .catch(error => {
                if (error.data && error.data.errors) {
                    for (const [field, messages] of Object.entries(error.data.errors)) {
                        messages.forEach(msg => showToast('error', msg));
                    }
                } else {
                    showToast('error', 'An error occurred. Please try again.');
                }
            });
        });
    </script>
@endpush
