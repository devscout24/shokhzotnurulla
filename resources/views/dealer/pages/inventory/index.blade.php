@extends('layouts.dealer.app')
@section('title', __('Inventory') . ' | '. __(config('app.name')))

@push('page-assets')
    {{-- Quill --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    @vite([
        'resources/css/dealer/pages/inventory.css',
        'resources/js/dealer/pages/inventory.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent" style="padding:0;">

        <div class="view-content inventory-view" data-view="inventory">

            {{-- Inventory Top Bar --}}
            @include('dealer.partials.inventory-topbar')

            {{-- ══════════════════════════════
                 MAIN SPLIT
            ══════════════════════════════ --}}
            <div class="inv-main">

                {{-- ════════════════════════
                     LEFT SIDEBAR
                ════════════════════════ --}}
                <aside class="inv-sidebar">

                    {{-- Add Unit --}}
                    <button type="button" class="inv-btn-add" id="btnAddUnit">
                        <i class="bi bi-plus-lg"></i> Add Unit
                    </button>

                    {{-- Total units (always open, no chevron) --}}
                    <div class="inv-filter-group open" data-group="units">
                        <div class="inv-filter-head" style="cursor:default;">
                            <span class="inv-filter-head-label" style="font-weight:700;">
                                {{ $totalCount }} {{ Str::plural('unit', $totalCount) }}
                            </span>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="inv-filter-group open" data-group="location">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Location</span>
                        </div>
                        <div class="inv-filter-body">
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="location"
                                       value="{{ $dealer->id }}"
                                       {{ request('location') == $dealer->id ? 'checked' : '' }}>
                                {{ $dealer->name }}
                                <span class="inv-filter-count">({{ $totalCount }})</span>
                            </label>
                        </div>
                    </div>

                    {{-- Condition --}}
                    <div class="inv-filter-group open" data-group="condition">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Condition</span>
                        </div>
                        <div class="inv-filter-body">
                            @foreach($conditionCounts as $cond => $count)
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="condition"
                                       value="{{ $cond }}"
                                       {{ request('condition') === $cond ? 'checked' : '' }}>
                                {{ $cond }}
                                <span class="inv-filter-count">({{ $count }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Make --}}
                    <div class="inv-filter-group open" data-group="make">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Make</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            <div class="inv-filter-scroll">
                                @foreach($makeCounts as $make)
                                <label class="inv-filter-item">
                                    <input type="checkbox"
                                           class="inv-filter-cb"
                                           data-filter="make_id"
                                           value="{{ $make->id }}"
                                           {{ request('make_id') == $make->id ? 'checked' : '' }}>
                                    {{ strtoupper($make->name) }}
                                    <span class="inv-filter-count">({{ $make->vehicles_count }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Years --}}
                    <div class="inv-filter-group" data-group="years">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Years</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            <div class="inv-year-range">
                                <span>Min Year</span>
                                <select class="inv-filter-cb" data-filter="year_min">
                                    <option value="">Older</option>
                                    @foreach($years->sort() as $yr)
                                        <option value="{{ $yr }}" {{ request('year_min') == $yr ? 'selected' : '' }}>
                                            {{ $yr }}
                                        </option>
                                    @endforeach
                                </select>
                                <span>to</span>
                                <span>Max Year</span>
                                <select class="inv-filter-cb" data-filter="year_max">
                                    <option value="">New</option>
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ request('year_max') == $yr ? 'selected' : '' }}>
                                            {{ $yr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Body Style --}}
                    <div class="inv-filter-group" data-group="body">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Body Style</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            @foreach($bodyTypeCounts as $bodyName => $count)
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="body_type_id"
                                       value="{{ $bodyName }}">
                                {{ $bodyName }}
                                <span class="inv-filter-count">({{ $count }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Exterior Color --}}
                    <div class="inv-filter-group" data-group="extcolor">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Exterior Color</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            @foreach($exteriorColorCounts as $color)
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="exterior_color_id"
                                       value="{{ $color->id }}"
                                       {{ request('exterior_color_id') == $color->id ? 'checked' : '' }}>
                                @if($color->hex)
                                    <span class="inv-color-dot" style="background:{{ $color->hex }};"></span>
                                @endif
                                {{ $color->name }}
                                <span class="inv-filter-count">({{ $color->vehicles_exterior_count  }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Interior Color --}}
                    <div class="inv-filter-group" data-group="intcolor">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Interior Color</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            @foreach($interiorColorCounts as $color)
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="interior_color_id"
                                       value="{{ $color->id }}"
                                       {{ request('interior_color_id') == $color->id ? 'checked' : '' }}>
                                @if($color->hex)
                                    <span class="inv-color-dot" style="background:{{ $color->hex }};"></span>
                                @endif
                                {{ $color->name }}
                                <span class="inv-filter-count">({{ $color->vehicles_interior_count  }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Fuel Type --}}
                    <div class="inv-filter-group" data-group="fuel">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Fuel Type</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            @foreach($fuelTypeCounts as $fuel)
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="fuel_type_id"
                                       value="{{ $fuel->id }}"
                                       {{ request('fuel_type_id') == $fuel->id ? 'checked' : '' }}>
                                {{ $fuel->name }}
                                <span class="inv-filter-count">({{ $fuel->vehicles_count }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Transmission --}}
                    <div class="inv-filter-group" data-group="trans">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Transmission</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            @foreach($transmissionCounts as $trans)
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="transmission_type_id"
                                       value="{{ $trans->id }}"
                                       {{ request('transmission_type_id') == $trans->id ? 'checked' : '' }}>
                                {{ $trans->name }}
                                <span class="inv-filter-count">({{ $trans->vehicles_count }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Drivetrain --}}
                    <div class="inv-filter-group" data-group="drive">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Drivetrain</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            @foreach($drivetrainCounts as $drive)
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="drivetrain_type_id"
                                       value="{{ $drive->id }}"
                                       {{ request('drivetrain_type_id') == $drive->id ? 'checked' : '' }}>
                                {{ $drive->name }}
                                <span class="inv-filter-count">({{ $drive->vehicles_count }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Seating Capacity --}}
                    <div class="inv-filter-group" data-group="seats">
                        <div class="inv-filter-head">
                            <span class="inv-filter-head-label">Seating Capacity</span>
                            <i class="bi bi-chevron-down inv-filter-chevron"></i>
                        </div>
                        <div class="inv-filter-body">
                            @foreach($seatingCounts as $seats => $count)
                            <label class="inv-filter-item">
                                <input type="checkbox"
                                       class="inv-filter-cb"
                                       data-filter="seating_capacity"
                                       value="{{ $seats }}"
                                       {{ request('seating_capacity') == $seats ? 'checked' : '' }}>
                                {{ $seats }}
                                <span class="inv-filter-count">({{ $count }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                </aside>
                {{-- end sidebar --}}

                {{-- ════════════════════════
                     RIGHT CONTENT
                ════════════════════════ --}}
                <div class="inv-content">

                    {{-- ── Top action bar ── --}}
                    <div class="inv-topbar">

                        {{-- Status tabs --}}
                        <a href="{{ route('dealer.inventory.index', request()->except('on_hold', 'status')) }}"
                           class="inv-btn-tab {{ !request('on_hold') && !request('status') ? 'active' : '' }}"
                           id="tabAllUnits">
                            All Units
                        </a>
                        <a href="{{ route('dealer.inventory.index', array_merge(request()->except('on_hold', 'status'), ['on_hold' => 1])) }}"
                           class="inv-btn-tab {{ request('on_hold') ? 'active' : '' }}"
                           id="tabOnHold">
                            On-Hold
                        </a>
                        <a href="{{ route('dealer.inventory.index', array_merge(request()->except('on_hold', 'status'), ['status' => 'sold'])) }}"
                           class="inv-btn-tab {{ request('status') === 'sold' ? 'active' : '' }}"
                           id="tabTempSold">
                            Temp. Sold
                        </a>

                        <div class="inv-topbar-sep"></div>

                        {{-- Display toggle --}}
                        <div class="inv-select-wrap">
                            <span class="inv-label">Display</span>
                            <select class="inv-select" id="invDisplay">
                                <option value="list">List</option>
                                <option value="grid">Grid</option>
                            </select>
                        </div>

                        {{-- Per page limit --}}
                        <div class="inv-select-wrap">
                            <span class="inv-label">Limit</span>
                            <select class="inv-select" id="invLimit">
                                <option value="12"  {{ request('per_page', 25) == 12  ? 'selected' : '' }}>12</option>
                                <option value="25"  {{ request('per_page', 25) == 25  ? 'selected' : '' }}>25</option>
                                <option value="48"  {{ request('per_page', 25) == 48  ? 'selected' : '' }}>48</option>
                                <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        <div class="inv-topbar-right">

                            <button type="button" class="inv-btn-export">
                                <i class="bi bi-upload"></i> Export
                            </button>

                            {{-- Columns Displayed --}}
                            <div class="inv-cols-wrap">
                                <button type="button" class="inv-btn-columns" id="btnColumns">
                                    <i class="bi bi-layout-three-columns"></i>
                                    <span id="colsLabel">7 Columns Displayed</span>
                                    <i class="bi bi-chevron-down inv-cols-chevron"></i>
                                </button>
                                <div class="inv-cols-dropdown" id="colsDropdown">
                                    <label class="inv-col-item locked">
                                        <input type="checkbox" checked disabled> Vehicle
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="stock" checked> Stock #
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="override" checked> Status Override
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="vin" checked> VIN
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="price" checked> Price
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="miles" checked> Miles
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="days" checked> Days
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="status"> Status
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="model"> Model Number
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="condition"> Condition
                                    </label>
                                    <label class="inv-col-item">
                                        <input type="checkbox" class="col-toggle" data-col="cost"> Cost
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- end inv-topbar --}}

                    <div class="subview" data-subview="inventory"
                         style="flex:1;display:flex;flex-direction:column;overflow:hidden;">

                        {{-- ── LIST VIEW ── --}}
                        <div class="inv-table-wrap" id="invListView">
                            <table class="inv-table" id="invTable">
                                <thead>
                                    <tr>
                                        <th class="inv-thumb-cell"></th>
                                        <th>Vehicle <i class="bi bi-arrow-down-up inv-sort-icon"></i></th>
                                        <th class="col-th" data-col="stock">
                                            Stock # <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                        <th class="col-th" data-col="override">
                                            Status Override <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                        <th class="col-th" data-col="vin">
                                            VIN <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                        <th class="col-th" data-col="price">
                                            Price <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                        <th class="col-th" data-col="miles">
                                            Miles <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                        <th class="col-th sorted" data-col="days">
                                            Days <i class="bi bi-arrow-up inv-sort-icon"></i>
                                        </th>
                                        {{-- Hidden by default --}}
                                        <th class="col-th" data-col="status" style="display:none;">
                                            Status <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                        <th class="col-th" data-col="model" style="display:none;">
                                            Model # <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                        <th class="col-th" data-col="condition" style="display:none;">
                                            Condition <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                        <th class="col-th" data-col="cost" style="display:none;">
                                            Cost <i class="bi bi-arrow-down-up inv-sort-icon"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vehicles as $vehicle)
                                    <tr>

                                        {{-- Thumbnail --}}
                                        <td class="inv-thumb-cell">
                                            @if($vehicle->primaryPhoto)
                                                <img class="inv-thumb"
                                                     src="{{ $vehicle->primaryPhoto->url }}"
                                                     alt="{{ $vehicle->display_title }}">
                                            @else
                                                <img class="inv-thumb"
                                                     src="https://placehold.co/76x52/e8e8e8/999?text=No+Photo"
                                                     alt="No Photo">
                                            @endif
                                        </td>

                                        {{-- Vehicle Name --}}
                                        <td>
                                            <a href="{{ route('dealer.inventory.vdp.show', $vehicle) }}"
                                               class="inv-veh-link">
                                                {{ strtoupper($vehicle->display_title) }}
                                            </a>
                                        </td>

                                        {{-- Stock # --}}
                                        <td class="col-td" data-col="stock">
                                            {{ $vehicle->stock_number }}
                                        </td>

                                        {{-- Status Override --}}
                                        <td class="col-td" data-col="override">
                                            @if($vehicle->status === 'sold')
                                                <span class="inv-badge-sold">SOLD</span>
                                            @elseif($vehicle->is_on_hold)
                                                <span class="inv-badge-hold">ON HOLD</span>
                                            @elseif($vehicle->status === 'draft')
                                                <span class="inv-badge-draft">DRAFT</span>
                                            @endif
                                        </td>

                                        {{-- VIN --}}
                                        <td class="col-td" data-col="vin"
                                            style="font-family:monospace;font-size:12.5px;letter-spacing:0.3px;">
                                            {{ $vehicle->vin ?? '—' }}
                                        </td>

                                        {{-- Price (inline edit) --}}
                                        <td class="col-td" data-col="price">
                                            <span class="inv-price-display">
                                                ${{ $vehicle->list_price ? number_format($vehicle->list_price) : '—' }}
                                                <i class="bi bi-pencil-square inv-price-edit"
                                                   title="Edit price"
                                                   data-vehicle-id="{{ $vehicle->id }}"
                                                   data-price="{{ $vehicle->list_price }}">
                                                </i>
                                            </span>
                                            <span class="inv-price-editor">
                                                <span class="inv-price-prefix">$</span>
                                                <input type="text"
                                                       class="inv-price-input"
                                                       value="{{ $vehicle->list_price }}">
                                                <button class="inv-price-confirm" type="button"
                                                        data-vehicle-id="{{ $vehicle->id }}"
                                                        data-url="{{ route('dealer.inventory.vdp.pricing', $vehicle) }}"
                                                        title="Confirm">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                                <button class="inv-price-cancel" type="button" title="Cancel">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </span>
                                        </td>

                                        {{-- Miles --}}
                                        <td class="col-td" data-col="miles">
                                            {{ $vehicle->mileage ? number_format($vehicle->mileage) : '—' }}
                                        </td>

                                        {{-- Days on Lot --}}
                                        <td class="col-td" data-col="days">
                                            <span class="inv-days-badge {{ $vehicle->days_on_lot > 90 ? 'inv-days-old' : '' }}">
                                                {{ $vehicle->days_on_lot }}
                                            </span>
                                        </td>

                                        {{-- Hidden columns --}}
                                        <td class="col-td" data-col="status" style="display:none;">
                                            {{ ucfirst($vehicle->status) }}
                                        </td>
                                        <td class="col-td" data-col="model" style="display:none;">
                                            {{ $vehicle->model_number ?? '—' }}
                                        </td>
                                        <td class="col-td" data-col="condition" style="display:none;">
                                            {{ $vehicle->vehicle_condition  }}
                                        </td>
                                        <td class="col-td" data-col="cost" style="display:none;">
                                            {{ $vehicle->prices?->dealer_cost
                                                ? '$' . number_format($vehicle->prices->dealer_cost)
                                                : '—' }}
                                        </td>

                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-5 text-muted">
                                            No vehicles found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- end list view --}}

                        {{-- ── GRID VIEW ── --}}
                        <div class="inv-grid-wrap" id="invGridView">
                            <p class="inv-grid-label">Showing {{ $vehicles->count() }} vehicles</p>
                            <div class="inv-grid">
                                @foreach($vehicles as $vehicle)
                                <div class="inv-grid-card" data-url="{{ route('dealer.inventory.vdp.show', $vehicle) }}">
                                    @if($vehicle->primaryPhoto)
                                        <img class="inv-grid-img"
                                             src="{{ Storage::url($vehicle->primaryPhoto->path) }}"
                                             alt="{{ $vehicle->display_title }}">
                                    @else
                                        <img class="inv-grid-img"
                                             src="https://placehold.co/400x225/e8e8e8/999?text=No+Photo"
                                             alt="No Photo">
                                    @endif
                                    <div class="inv-grid-body">
                                        <div class="inv-grid-name">{{ strtoupper($vehicle->display_title) }}</div>
                                        <div class="inv-grid-meta">
                                            <span><i class="bi bi-hash"></i> {{ $vehicle->stock_number }}</span>
                                            <span><i class="bi bi-currency-dollar"></i> {{ $vehicle->list_price ? number_format($vehicle->list_price) : '—' }}</span>
                                            <span><i class="bi bi-speedometer2"></i> {{ $vehicle->mileage ? number_format($vehicle->mileage) : '—' }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        {{-- end grid view --}}

                        {{-- ── Pagination ── --}}
                        <div class="inv-paging-wrap">
                            <p class="inv-page-count">
                                @if($vehicles->total() > 0)
                                    Showing {{ $vehicles->firstItem() }} of {{ $vehicles->total() }} vehicles
                                @else
                                    Showing 0 of 0 vehicles
                                @endif
                            </p>
                            <div class="inv-pagination">
                                {{-- Prev --}}
                                @if($vehicles->onFirstPage())
                                    <button class="inv-pg-btn disabled" disabled>
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                @else
                                    <a href="{{ $vehicles->previousPageUrl() }}" class="inv-pg-btn">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach($vehicles->getUrlRange(1, $vehicles->lastPage()) as $page => $url)
                                    @if($page == $vehicles->currentPage())
                                        <button class="inv-pg-btn active">{{ $page }}</button>
                                    @elseif(abs($page - $vehicles->currentPage()) <= 2 || $page == 1 || $page == $vehicles->lastPage())
                                        <a href="{{ $url }}" class="inv-pg-btn">{{ $page }}</a>
                                    @elseif(abs($page - $vehicles->currentPage()) == 3)
                                        <span class="inv-pg-ellipsis">…</span>
                                    @endif
                                @endforeach

                                {{-- Next --}}
                                @if($vehicles->hasMorePages())
                                    <a href="{{ $vehicles->nextPageUrl() }}" class="inv-pg-btn">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                @else
                                    <button class="inv-pg-btn disabled" disabled>
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>{{-- end .subview --}}
                </div>{{-- end .inv-content --}}
            </div>{{-- end .inv-main --}}
        </div>{{-- end .view-content --}}
    </main>
@endsection

@push('page-modals')
    @include('dealer.modals.inventory-add-unit')
@endpush