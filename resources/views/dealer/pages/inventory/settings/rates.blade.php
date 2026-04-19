@extends('layouts.dealer.app')
@section('title', __('Finance Settings') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/inventory-settings-rates.css',
        'resources/js/dealer/pages/inventory-settings-rates.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">
            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="rates">

                <div class="fs-wrapper">

                    {{-- ── LEFT SIDEBAR ── --}}
                    <aside class="fs-sidebar">
                        <div class="menu-label">Menu</div>
                        <a href="{{ route('dealer.inventory.settings.rates.index') }}" class="menu-item active">
                            <i class="bi bi-percent icon"></i> Interest Rates
                        </a>
                        <a href="{{ route('dealer.inventory.settings.fees.index') }}" class="menu-item">
                            <i class="bi bi-currency-dollar icon"></i> Inventory Fees
                            <span class="badge-new ms-1">NEW</span>
                        </a>
                        <a href="{{ route('dealer.inventory.settings.syndication') }}" class="menu-item">
                            <i class="bi bi-arrow-left-right icon"></i> Syndication
                        </a>
                    </aside>

                    {{-- ── RIGHT CONTENT ── --}}
                    <div class="fs-content"
                         data-sync-url="{{ route('dealer.inventory.settings.rates.sync') }}">
                        {{-- ── Unsaved Changes Banner ── --}}
                        <div class="fs-unsaved-banner mb-3" id="unsavedBanner" style="display:none;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            You have <strong id="unsavedCount">0</strong> unsaved
                            <span id="unsavedWord">change</span> to interest rates.
                            Please be sure to click "Save" below to save your changes.
                        </div>
                        <p class="fs-note">
                            <strong>Note:</strong> We've added the ability to separate interest rates by
                            Certified Pre-owned (factory certified) vs. Verified Pre-owned
                            (Acura Precision, HondaTrue, OEM-certified, etc).
                        </p>

                        {{-- Toolbar --}}
                        <div class="fs-toolbar">
                            <span class="toolbar-label">Manage Interest Rates</span>

                            <div class="fs-btn-group">
                                <button class="active" data-condition="any">Any</button>
                                <button data-condition="new">New</button>
                                <button data-condition="used">Pre-owned</button>
                                <button data-condition="cpo">CPO</button>
                                <button data-condition="vpo">VPO</button>
                            </div>

                            <div class="fs-term-wrap">
                                <span>Min. Term</span>
                                <input type="number" id="filterMinTerm" min="0" max="200" placeholder="0">
                            </div>
                            <div class="fs-term-wrap">
                                <span>Max. Term</span>
                                <input type="number" id="filterMaxTerm" min="0" max="200" placeholder="120">
                            </div>

                            <button class="fs-btn-add-rate" id="btnAddRate" type="button">
                                <i class="bi bi-plus"></i> Add Rate
                            </button>
                        </div>

                        {{-- Rate Table --}}
                        <div class="fs-table-wrap">
                            <table class="fs-table" id="ratesTable">
                                <thead>
                                    <tr>
                                        <th>Make</th>
                                        <th>Min. Model<span class="th-sub">Year</span></th>
                                        <th>Max. Model<span class="th-sub">Year</span></th>
                                        <th>Default Min.<span class="th-sub">Term (mo.)</span></th>
                                        <th>Default Max.<span class="th-sub">Term (mo.)</span></th>
                                        <th>Min. Credit<span class="th-sub">Score</span></th>
                                        <th>Max. Credit<span class="th-sub">Score</span></th>
                                        <th>Vehicle<span class="th-sub">Condition</span></th>
                                        <th>Interest<span class="th-sub">Rate</span></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="ratesBody">

                                @forelse($grouped as $yearKey => $rates)
                                    @php $firstRate = $rates->first(); @endphp

                                    <tr class="fs-group-row"
                                        data-year-key="{{ $yearKey }}"
                                        data-min-year="{{ $firstRate->min_model_year }}"
                                        data-max-year="{{ $firstRate->max_model_year }}">
                                        <td colspan="10">
                                            {{ $firstRate->year_range_label }}
                                            <button class="btn-add-group" type="button"
                                                    title="Add empty row to this group"
                                                    data-min-year="{{ $firstRate->min_model_year }}"
                                                    data-max-year="{{ $firstRate->max_model_year }}">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    @foreach($rates as $rate)
                                        @include('dealer.components.inventory.settings.interest-rate-row', ['rate' => $rate, 'makes' => $makes])
                                    @endforeach

                                @empty
                                    <tr class="fs-empty-row" id="emptyRow">
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            No interest rates configured yet. Click <strong>+ Add Rate</strong> to get started.
                                        </td>
                                    </tr>
                                @endforelse

                                </tbody>
                            </table>
                        </div>

                        <button class="fs-btn-save" id="btnSaveRates" type="button">
                            <i class="bi bi-check-lg"></i> Save
                        </button>

                    </div>{{-- end fs-content --}}
                </div>{{-- end fs-wrapper --}}
            </div>{{-- end subview --}}
        </div>
    </main>
@endsection

@push('page-modals')
    @include('dealer.modals.inventory-add-rate', ['makes' => $makes])
@endpush

{{-- Pass makes list to JS --}}
@push('page-scripts')
<script>
    window.ratesMakes = @json($makes->pluck('name'));
</script>
@endpush