@extends('layouts.dealer.app')
@section('title', __('Fees') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/inventory-settings-fees.css',
        'resources/js/dealer/pages/inventory-settings-fees.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">
            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="fees">
                <div class="fs-wrapper">

                    {{-- ── LEFT SIDEBAR ── --}}
                    <aside class="fs-sidebar">
                        <div class="menu-label">Menu</div>
                        <a href="{{ route('dealer.inventory.settings.rates.index') }}" class="menu-item">
                            <i class="bi bi-percent icon"></i> Interest Rates
                        </a>
                        <a href="{{ route('dealer.inventory.settings.fees.index') }}" class="menu-item active">
                            <i class="bi bi-currency-dollar icon"></i> Inventory Fees
                            <span class="badge-new ms-1">NEW</span>
                        </a>
                        <a href="{{ route('dealer.inventory.settings.syndication') }}" class="menu-item">
                            <i class="bi bi-arrow-left-right icon"></i> Syndication
                        </a>
                    </aside>

                    {{-- ── RIGHT CONTENT ── --}}
                    <div class="if-page">

                        <p class="if-description">
                            <strong>Inventory Fees</strong> add the ability for custom price stacking on vehicle
                            listings. Fees (or in some cases, incentives / discounts) can be applied to all vehicles,
                            or only to vehicles that meet certain conditions (i.e. only new or used). Fees can be
                            either a flat amount or a percentage of the vehicle price, and can be designated as
                            pre-tax or post-tax, as well as optional or guaranteed.
                        </p>

                        <p style="font-size:12.5px;color:#555;margin:0 0 2px;">Examples:</p>
                        <ul class="if-examples">
                            <li>$99.95 documentation fee (guaranteed, post-tax, all vehicles)</li>
                            <li>$349 3M Paint Protectant (optional, pre-tax, all vehicles)</li>
                            <li>$799 Theft Protection</li>
                            <li>-$1,500 Trade-in assistance (optional, pre-tax, used vehicles)</li>
                            <li>-$500 down payment assistance</li>
                        </ul>

                        <p class="if-coming-soon">
                            <strong>Coming Soon:</strong> We'll be adding the ability to assign fees to specific vehicles.
                        </p>

                        {{-- Fees Card --}}
                        <div class="if-card">
                            <div class="if-card-header">
                                <span class="if-card-title">Inventory Fees</span>
                                <button class="if-btn-add-fee" id="btnAddFee" type="button">
                                    <i class="bi bi-plus"></i> Add Fee
                                </button>
                            </div>

                            <table class="if-table" id="feesTable">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Dealer</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Tax</th>
                                        <th>Optional</th>
                                        <th>Condition</th>
                                    </tr>
                                </thead>
                                <tbody id="feesBody">
                                @forelse($fees as $fee)
                                    @include('dealer.components.inventory.settings.fee-row', [
                                        'fee'    => $fee,
                                        'dealer' => auth()->user()->currentDealer,
                                    ])
                                @empty
                                    <tr id="emptyRow">
                                        <td colspan="8" class="no-results">No fees found.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>{{-- end if-page --}}
                </div>{{-- end fs-wrapper --}}
            </div>{{-- end subview --}}
        </div>
    </main>
@endsection

@push('page-modals')
    @include('dealer.modals.inventory-add-fee')
@endpush

@push('page-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

    <script>
        window.feesConfig = {
            storeUrl:   '{{ route('dealer.inventory.settings.fees.store') }}',
            reorderUrl: '{{ route('dealer.inventory.settings.fees.reorder') }}',
            dealerName: '{{ auth()->user()->currentDealer->name }}',
        };
    </script>
@endpush