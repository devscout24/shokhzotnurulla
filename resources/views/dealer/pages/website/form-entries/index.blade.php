@extends('layouts.dealer.app')
@section('title', __('Form Submissions') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/form-entries.css',
        'resources/js/dealer/pages/form-entries.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">

        <div class="page-header">
            <h2 class="view-title">Form Submissions</h2>
            <div class="fs-topbar-actions">

                {{-- Search --}}
                <div class="fs-search-wrap">
                    <span class="fs-search-icon"><i class="bi bi-search"></i></span>
                    <input type="text" class="fs-search-input" id="fsSearch"
                           placeholder="Search by name"
                           value="{{ request('search') }}">
                </div>

                {{-- Date range --}}
                <div class="date-picker-box">
                    <i class="bi bi-calendar3"></i>
                    <input type="text" id="dateRange" readonly
                           placeholder="Select date range"
                           value="{{ request('from') && request('to') ? request('from') . ' - ' . request('to') : '' }}">
                </div>

                {{-- Filter by Form --}}
                <div class="fs-filter-wrap">
                    <button class="fs-filter-btn" type="button" id="fsFilterBtn">
                        <i class="bi bi-funnel"></i>
                        <span id="fsFilterLabel">
                            {{ request('form_type') ? ($formTypes[request('form_type')] ?? 'Filter by Form') : 'Filter by Form' }}
                        </span>
                        <i class="bi bi-chevron-down fs-filter-chevron" id="fsFilterChevron"></i>
                    </button>
                    <div class="fs-filter-dropdown" id="fsFilterDropdown">
                        <ul class="fs-filter-list">
                            <li class="fs-filter-item {{ !request('form_type') ? 'active' : '' }}"
                                data-form="">All Forms</li>
                            <hr class="fs-filter-divider">
                            @foreach($formTypes as $key => $label)
                                <li class="fs-filter-item {{ request('form_type') === $key ? 'active' : '' }}"
                                    data-form="{{ $key }}">{{ $label }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <div class="view-content" data-view="form-submissions">
            <div class="fs-card">

                {{-- Card Header --}}
                <div class="fs-card-header">
                    <span class="fs-card-title">Submissions</span>
                    <div class="fs-header-actions">
                        <button type="button" class="fs-btn-export" id="btnExport">
                            <i class="bi bi-upload"></i> Export
                        </button>
                        <button type="button" class="fs-btn-unread" id="btnMarkUnread">
                            <i class="bi bi-circle"></i> Mark as Unread
                        </button>
                        <button type="button" class="fs-btn-delete" id="btnDelete">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>

                {{-- Tabs --}}
                @php $currentTab = request('tab', 'all'); @endphp
                <div class="fs-tabs">
                    <button class="fs-tab {{ $currentTab === 'all'       ? 'active' : '' }}" data-tab="all"       type="button">All <span class="fs-tab-count">{{ $counts['all'] }}</span></button>
                    <button class="fs-tab {{ $currentTab === 'unread'    ? 'active' : '' }}" data-tab="unread"    type="button">Unread <span class="fs-tab-count">{{ $counts['unread'] }}</span></button>
                    <button class="fs-tab {{ $currentTab === 'complete'  ? 'active' : '' }}" data-tab="complete"  type="button">Completed <span class="fs-tab-count">{{ $counts['complete'] }}</span></button>
                    <button class="fs-tab {{ $currentTab === 'abandoned' ? 'active' : '' }}" data-tab="abandoned" type="button">Abandoned <span class="fs-tab-count">{{ $counts['abandoned'] }}</span></button>
                    <button class="fs-tab {{ $currentTab === 'archived'  ? 'active' : '' }}" data-tab="archived"  type="button">Read / Archived <span class="fs-tab-count">{{ $counts['archived'] }}</span></button>
                </div>

                {{-- Table --}}
                <div class="fs-table-wrap">
                    <table class="fs-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="fs-cb" id="fsSelectAll"></th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Form Alias</th>
                                <th>Vehicle / Referrer</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="fsTableBody">
                            @forelse($entries as $entry)
                                <tr class="fs-row {{ !$entry->is_read ? 'fs-row-unread' : '' }}"
                                    data-id="{{ $entry->id }}"
                                    data-status="{{ $entry->status }}"
                                    data-show-url="{{ route('dealer.website.form-entries.show', $entry) }}">

                                    <td><input type="checkbox" class="fs-cb fs-row-cb" value="{{ $entry->id }}"></td>

                                    <td>
                                        @if(!$entry->is_read)
                                            <span class="fs-unread-dot"></span>
                                        @endif
                                        {{ $entry->full_name }}
                                    </td>

                                    <td style="color:#555;">{{ $entry->form_type_label }}</td>

                                    <td>
                                        @if($entry->borrower_type)
                                            <span class="fs-badge">{{ $entry->borrower_type_label }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($entry->vehicle)
                                            <a href="{{ route('dealer.inventory.vdp.show', $entry->vehicle) }}"
                                               class="fs-vehicle-link"
                                               onclick="event.stopPropagation()">
                                                {{ $entry->vehicle->stock_number }}: {{ $entry->vehicle->display_title }}
                                            </a>
                                        @elseif($entry->referrer)
                                            <span class="fs-badge">{{ parse_url($entry->referrer, PHP_URL_PATH) ?? $entry->referrer }}</span>
                                        @else
                                            <span style="color:#bbb;">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="fs-status">
                                            <span class="fs-dot {{ $entry->status }}"></span>
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </td>

                                    <td style="white-space:nowrap;color:#555;">
                                        {{ $entry->submitted_at->format('n/j/Y g:i A') }}
                                    </td>

                                    <td>
                                        <button class="fs-arrow-btn" type="button">
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align:center;padding:40px;color:#aaa;">
                                        No form submissions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="fs-footer" id="fsFooter">
                    @if($entries->total() > 0)
                        Showing results {{ $entries->firstItem() }} - {{ $entries->lastItem() }} of {{ $entries->total() }}
                    @else
                        No results found.
                    @endif
                </div>

            </div>
        </div>
    </main>
@endsection

@push('page-modals')
    @include('dealer.offcanvas.form-entry-detail')
@endpush

@push('page-scripts')
    <script>
        window.fsRoutes = {
            index:       '{{ route('dealer.website.form-entries.index') }}',
            bulkRead:    '{{ route('dealer.website.form-entries.bulk-read') }}',
            bulkDestroy: '{{ route('dealer.website.form-entries.bulk-destroy') }}',
            export:      '{{ route('dealer.website.form-entries.export') }}',
        };
        window.fsCsrf = '{{ csrf_token() }}';
    </script>
@endpush

