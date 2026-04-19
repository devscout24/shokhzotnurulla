@extends('layouts.dealer.app')

@section('title', __('Pricing Specials') . ' | ' . __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/inventory-pricing-specials.css',
        'resources/js/dealer/pages/inventory-pricing-specials.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">

            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="pricing-specials">
                <div class="pricing-specials-wrapper">

                    {{-- ── LEFT SIDEBAR ── --}}
                    <aside class="ps-sidebar">

                        <button class="btn-add-special" id="btnAddPricingSpecial" type="button">
                            <i class="bi bi-plus-lg"></i> Add Pricing Special
                        </button>

                        <form method="GET" action="{{ route('dealer.inventory.pricing-specials.index') }}"
                              id="psSidebarFilterForm">

                            <div class="filter-label">Type</div>
                            <select class="filter-select" name="filter_type">
                                <option value="">Any</option>
                                <option value="formfill"  {{ request('filter_type') === 'formfill'  ? 'selected' : '' }}>Form Fill</option>
                                <option value="override"  {{ request('filter_type') === 'override'  ? 'selected' : '' }}>Override</option>
                            </select>

                            <div class="filter-label">Condition</div>
                            <select class="filter-select" name="filter_condition">
                                <option value="">Any</option>
                                <option value="New"       {{ request('filter_condition') === 'New'       ? 'selected' : '' }}>New</option>
                                <option value="Pre-owned" {{ request('filter_condition') === 'Pre-owned' ? 'selected' : '' }}>Pre-owned</option>
                            </select>

                            <div class="filter-label">Certified?</div>
                            <select class="filter-select" name="filter_certified">
                                <option value="">Any</option>
                                <option value="1" {{ request('filter_certified') === '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ request('filter_certified') === '0' ? 'selected' : '' }}>No</option>
                            </select>

                            <div class="filter-label">Year</div>
                            <div class="year-row">
                                <select class="filter-select" name="filter_year_from">
                                    <option value="">Oldest</option>
                                    @for($y = date('Y') + 1; $y >= 2000; $y--)
                                        <option value="{{ $y }}" {{ request('filter_year_from') == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                                <span>to</span>
                                <select class="filter-select" name="filter_year_to">
                                    <option value="">Newest</option>
                                    @for($y = date('Y') + 1; $y >= 2000; $y--)
                                        <option value="{{ $y }}" {{ request('filter_year_to') == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                        </form>

                    </aside>

                    {{-- ── RIGHT TABLE ── --}}
                    <div class="ps-content">
                        <table class="ps-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Criteria</th>
                                    <th>Rate</th>
                                    <th>Months</th>
                                    <th>Expires</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pricingSpecials as $special)
                                    <tr id="ps-row-{{ $special->id }}"
                                        class="{{ ! $special->is_enabled ? 'ps-row-paused' : '' }}">

                                        {{-- Title + Draft badge --}}
                                        <td class="ps-td-title">
                                            {{ $special->title }}
                                            @if(! $special->is_enabled)
                                                <span class="ps-badge ps-badge-draft ms-1">Draft</span>
                                            @endif
                                        </td>

                                        {{-- Type --}}
                                        <td>
                                            @if($special->type === 'formfill')
                                                <span class="ps-badge ps-badge-formfill">Form Fill</span>
                                            @elseif($special->type === 'override')
                                                <span class="ps-badge ps-badge-override">Override</span>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        {{-- Amount --}}
                                        <td>{{ $special->amount_display }}</td>

                                        {{-- Criteria --}}
                                        <td class="ps-td-criteria">
                                            @if($special->condition)
                                                <div><b>Condition:</b> {{ $special->condition }}</div>
                                            @endif
                                            @if($special->year)
                                                <div><b>Year:</b> {{ $special->year }}</div>
                                            @endif
                                            @if($special->make_id)
                                                <div><b>Make:</b> {{ $special->make?->name ?? $special->make_id }}</div>
                                            @endif
                                            @if($special->make_model_id)
                                                <div><b>Model:</b> {{ $special->makeModel?->name ?? $special->make_model_id }}</div>
                                            @endif
                                            @if($special->trim)
                                                <div><b>Trim:</b> {{ $special->trim }}</div>
                                            @endif
                                            @if($special->stock_number)
                                                <div><b>Stock #:</b> {{ $special->stock_number }}</div>
                                            @endif
                                            @if($special->model_number)
                                                <div><b>Model #:</b> {{ $special->model_number }}</div>
                                            @endif
                                            @if($special->exterior_color_id)
                                                <div><b>Exterior Color:</b> {{ $special->exteriorColor?->name ?? $special->exterior_color_id }}</div>
                                            @endif
                                            @if($special->body_style)
                                                <div><b>Body:</b> {{ $special->body_style }}</div>
                                            @endif
                                            @if(! $special->condition && ! $special->year && ! $special->make_id
                                                && ! $special->trim && ! $special->stock_number && ! $special->body_style)
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Rate --}}
                                        <td>
                                            {{ $special->discount_type === 'special' && $special->finance_rate
                                                ? $special->finance_rate . '%'
                                                : '—' }}
                                        </td>

                                        {{-- Months --}}
                                        <td>{{ $special->months_display }}</td>

                                        {{-- Expires --}}
                                        <td class="text-muted">
                                            {{ $special->ends_at ? $special->ends_at->format('M d, Y') : 'Never' }}
                                        </td>

                                        {{-- Actions --}}
                                        <td>
                                            <div class="ps-row-actions">

                                                {{-- Pause / Activate --}}
                                                <button type="button"
                                                        class="ps-btn-toggle btn-toggle-special {{ $special->is_enabled ? 'is-active' : 'is-paused' }}"
                                                        data-id="{{ $special->id }}"
                                                        data-type="{{ $special->type }}"
                                                        data-enabled="{{ $special->is_enabled ? '1' : '0' }}"
                                                        data-toggle-url="{{ route('dealer.inventory.pricing-specials.toggle', $special) }}"
                                                        title="{{ $special->is_enabled ? 'Pause' : 'Activate' }}">
                                                    <i class="bi {{ $special->is_enabled ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                                                </button>

                                                {{-- Edit --}}
                                                <button type="button"
                                                        class="ps-btn-edit btn-edit-special"
                                                        title="Edit"
                                                        data-id="{{ $special->id }}"
                                                        data-title="{{ $special->title }}"
                                                        data-type="{{ $special->type }}"
                                                        data-button-text="{{ $special->button_text }}"
                                                        data-discount-label="{{ $special->discount_label }}"
                                                        data-stackable="{{ $special->stackable ? '1' : '0' }}"
                                                        data-priority="{{ $special->priority }}"
                                                        data-discount-type="{{ $special->discount_type }}"
                                                        data-amount="{{ $special->amount }}"
                                                        data-finance-rate="{{ $special->finance_rate }}"
                                                        data-finance-term="{{ $special->finance_term }}"
                                                        data-condition="{{ $special->condition }}"
                                                        data-is-certified="{{ $special->is_certified !== null ? ($special->is_certified ? '1' : '0') : '' }}"
                                                        data-model-number="{{ $special->model_number }}"
                                                        data-year="{{ $special->year }}"
                                                        data-make-id="{{ $special->make_id }}"
                                                        data-make-model-id="{{ $special->make_model_id }}"
                                                        data-trim="{{ $special->trim }}"
                                                        data-body-style="{{ $special->body_style }}"
                                                        data-exterior-color-id="{{ $special->exterior_color_id }}"
                                                        data-stock-number="{{ $special->stock_number }}"
                                                        data-tag="{{ $special->tag }}"
                                                        data-min-days="{{ $special->min_days }}"
                                                        data-max-days="{{ $special->max_days }}"
                                                        data-send-email="{{ $special->send_email ? '1' : '0' }}"
                                                        data-hide-price="{{ $special->hide_price ? '1' : '0' }}"
                                                        data-starts-at="{{ $special->starts_at?->format('Y-m-d') }}"
                                                        data-ends-at="{{ $special->ends_at?->format('Y-m-d') }}"
                                                        data-notes="{{ $special->notes }}"
                                                        data-disclaimer="{{ $special->disclaimer }}"
                                                        data-update-url="{{ route('dealer.inventory.pricing-specials.update', $special) }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                {{-- Duplicate --}}
                                                <button type="button"
                                                        class="ps-btn-duplicate btn-duplicate-special"
                                                        title="Duplicate"
                                                        data-id="{{ $special->id }}"
                                                        data-duplicate-url="{{ route('dealer.inventory.pricing-specials.duplicate', $special) }}">
                                                    <i class="bi bi-copy"></i>
                                                </button>

                                                {{-- Delete --}}
                                                <button type="button"
                                                        class="ps-btn-delete btn-delete-special"
                                                        title="Delete"
                                                        data-id="{{ $special->id }}"
                                                        data-title="{{ $special->title }}"
                                                        data-delete-url="{{ route('dealer.inventory.pricing-specials.destroy', $special) }}">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="ps-table-empty">
                                            <i class="bi bi-tags d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                                            No pricing specials found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection

@push('page-modals')
    @include('dealer.modals.inventory-add-pricing-special', [
        'makes'        => $makes,
        'colors'       => $colors,
        'stockNumbers' => $stockNumbers,
    ])
@endpush

@push('page-scripts')
    <script>
        window.psRoutes = {
            store:      '{{ route('dealer.inventory.pricing-specials.store') }}',
            matchCount: '{{ route('dealer.inventory.pricing-specials.match-count') }}',
            models:     '{{ route('dealer.inventory.models') }}',
            csrf:       '{{ csrf_token() }}',
        };
    </script>
@endpush