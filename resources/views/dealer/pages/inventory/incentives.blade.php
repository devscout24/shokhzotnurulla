@extends('layouts.dealer.app')

@section('title', __('Incentives') . ' | ' . __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/inventory-incentives.css',
        'resources/js/dealer/pages/inventory-incentives.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">

            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="incentives">

                {{-- ── Filter Bar ── --}}
                <form method="GET" action="{{ route('dealer.inventory.incentives.index') }}"
                      class="incentives-filter-bar d-flex gap-2 align-items-center p-3 bg-white border-bottom flex-wrap"
                      id="incentivesFilterForm">

                    {{-- All Locations (static for now) --}}
                    <div class="dropdown" style="min-width:160px;">
                        <button class="btn btn-outline-secondary d-flex align-items-center gap-2 w-100 text-start"
                                type="button" style="font-size:13px;">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <span class="flex-grow-1">All Locations</span>
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </button>
                    </div>

                    {{-- Search by Title --}}
                    <div style="min-width:220px;">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search by Title"
                               value="{{ request('search') }}"
                               style="font-size:13px;">
                    </div>

                    {{-- Incentive Type --}}
                    <div style="min-width:200px;">
                        <select name="type" class="form-select" style="font-size:13px; color:#6c757d;">
                            <option value="">[ Select incentive type ]</option>
                            <option value="cash"           {{ request('type') === 'cash'           ? 'selected' : '' }}>Cash</option>
                            <option value="finance"        {{ request('type') === 'finance'        ? 'selected' : '' }}>Finance</option>
                            <option value="ivc_dvc"        {{ request('type') === 'ivc_dvc'        ? 'selected' : '' }}>IVC / DVC</option>
                            <option value="lease"          {{ request('type') === 'lease'          ? 'selected' : '' }}>Lease</option>
                            <option value="percentage_off" {{ request('type') === 'percentage_off' ? 'selected' : '' }}>Percentage Off</option>
                        </select>
                    </div>

                    {{-- Program Category (maps to our category filter) --}}
                    <div style="min-width:240px;">
                        <select name="category" class="form-select" style="font-size:13px; color:#6c757d;">
                            <option value="">Type or Select the Program Category</option>
                            <option value="all"  {{ request('category') === 'all'  ? 'selected' : '' }}>All</option>
                            <option value="used" {{ request('category') === 'used' ? 'selected' : '' }}>Used</option>
                            <option value="new"  {{ request('category') === 'new'  ? 'selected' : '' }}>New</option>
                            <option value="cpo"  {{ request('category') === 'cpo'  ? 'selected' : '' }}>CPO</option>
                        </select>
                    </div>

                    @if(request()->hasAny(['search','type','category']))
                        <a href="{{ route('dealer.inventory.incentives.index') }}"
                           class="btn btn-outline-danger btn-sm" style="font-size:13px;">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif

                    {{-- Add Incentive --}}
                    <div class="ms-auto">
                        <button type="button"
                                class="btn btn-danger btn-sm d-flex align-items-center gap-1"
                                id="btnAddIncentive"
                                style="font-size:13px;">
                            <i class="bi bi-plus-lg"></i> Add Incentive
                        </button>
                    </div>

                </form>

                {{-- ── Table ── --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="fw-semibold text-muted py-2 px-3">Dealer</th>
                                <th class="fw-semibold text-muted py-2 px-3">Title</th>
                                <th class="fw-semibold text-muted py-2 px-3">Type</th>
                                <th class="fw-semibold text-muted py-2 px-3">Category</th>
                                <th class="fw-semibold text-muted py-2 px-3">Guaranteed</th>
                                <th class="fw-semibold text-muted py-2 px-3">Featured</th>
                                <th class="fw-semibold text-muted py-2 px-3">Enabled</th>
                                <th class="fw-semibold text-muted py-2 px-3">Expires</th>
                                <th class="fw-semibold text-muted py-2 px-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incentives as $incentive)
                                <tr id="incentive-row-{{ $incentive->id }}">
                                    <td class="px-3">{{ $dealer->name }}</td>
                                    <td class="px-3 fw-semibold">{{ $incentive->title }}</td>
                                    <td class="px-3">
                                        <span class="badge inc-type-badge inc-type-{{ $incentive->type }}">
                                            {{ $incentive->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-3">{{ $incentive->category_label }}</td>
                                    <td class="px-3">
                                        @if($incentive->is_guaranteed)
                                            <span class="badge bg-success-subtle text-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">No</span>
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        @if($incentive->is_featured)
                                            <span class="badge bg-warning-subtle text-warning">Yes</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">No</span>
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        @if($incentive->is_enabled)
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="bi bi-check-circle-fill me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                <i class="bi bi-pause-circle me-1"></i>Disabled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-muted">
                                        {{ $incentive->expires_at ? $incentive->expires_at->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-3">
                                        <div class="d-flex gap-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary btn-edit-incentive"
                                                    data-id="{{ $incentive->id }}"
                                                    data-title="{{ $incentive->title }}"
                                                    data-type="{{ $incentive->type }}"
                                                    data-category="{{ $incentive->category }}"
                                                    data-description="{{ $incentive->description }}"
                                                    data-amount="{{ $incentive->amount }}"
                                                    data-amount-type="{{ $incentive->amount_type }}"
                                                    data-program-code="{{ $incentive->program_code }}"
                                                    data-is-guaranteed="{{ $incentive->is_guaranteed ? '1' : '0' }}"
                                                    data-is-featured="{{ $incentive->is_featured ? '1' : '0' }}"
                                                    data-is-enabled="{{ $incentive->is_enabled ? '1' : '0' }}"
                                                    data-expires-at="{{ $incentive->expires_at?->format('Y-m-d') }}"
                                                    data-update-url="{{ route('dealer.inventory.incentives.update', $incentive) }}"
                                                    style="font-size:12px;">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-delete-incentive"
                                                    data-id="{{ $incentive->id }}"
                                                    data-title="{{ $incentive->title }}"
                                                    data-delete-url="{{ route('dealer.inventory.incentives.destroy', $incentive) }}"
                                                    style="font-size:12px;">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-3 py-4 text-center text-muted">
                                        <i class="bi bi-gift d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                        No incentives found.
                                        @if(request()->hasAny(['search','type','category']))
                                            <a href="{{ route('dealer.inventory.incentives.index') }}" class="text-danger ms-1">Clear filters</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ── Pagination ── --}}
                <div class="d-flex flex-column align-items-center py-3 gap-2">
                    {{ $incentives->links('pagination::bootstrap-5') }}
                    <small class="text-muted">
                        Showing {{ $incentives->firstItem() ?? 0 }} of {{ $incentives->total() }} incentives
                    </small>
                </div>

            </div>{{-- end subview --}}
        </div>
    </main>
@endsection

@push('page-modals')
    @include('dealer.modals.inventory-add-incentive')
@endpush

@push('page-scripts')
    <script>
        window.incentiveRoutes = {
            store: '{{ route('dealer.inventory.incentives.store') }}',
            csrf:  '{{ csrf_token() }}',
        };
    </script>
@endpush