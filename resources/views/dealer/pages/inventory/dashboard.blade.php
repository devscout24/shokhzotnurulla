@extends('layouts.dealer.app')

@section('title', __('Inventory Dashboard') . ' | ' . __(config('app.name')))

@push('page-styles')
    <style>
        .inv-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 24px;
        }

        .inv-stat-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            transition: all 0.2s;
        }

        .inv-stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        .stat-main {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100px;
        }

        .stat-value {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin-bottom: 8px;
        }

        .stat-value .number {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }

        .stat-value .label {
            font-size: 14px;
            color: #71717a;
            font-weight: 400;
        }

        .stat-amount {
            font-size: 18px;
            font-weight: 700;
            color: #16a34a;
            /* Green for amounts */
        }

        .stat-label-only {
            font-size: 14px;
            color: #71717a;
            margin-top: 4px;
        }

        .stat-footer {
            padding: 12px 20px;
            border-top: 1px solid #f1f1f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-title {
            font-size: 14px;
            color: #52525b;
            font-weight: 400;
        }

        .stat-footer i {
            font-size: 16px;
            color: #333;
        }

        /* Ensure links don't look like default links */
        a.inv-stat-card,
        a.inv-stat-card:hover,
        a.inv-stat-card:focus {
            text-decoration: none !important;
            color: inherit !important;
            border: 1px solid #e0e0e0;
        }

        .inv-location-dropdown-wrapper {
            position: relative;
        }

        .inv-location-dropdown {
            padding: 10px 14px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            color: #333;
            font-size: 14px;
            min-width: 240px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .inv-location-dropdown i:first-child {
            color: #ef4444;
            font-size: 18px;
        }

        .inv-location-dropdown i:last-child {
            margin-left: auto;
            font-size: 12px;
            color: #999;
        }

        .inv-location-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-top: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
            padding: 5px 0;
        }

        .inv-location-menu.show {
            display: block;
        }

        .inv-location-item {
            padding: 10px 14px;
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .inv-location-item:hover {
            background: #f3f4f6;
        }

        .inv-location-item i {
            color: #ef4444;
            font-size: 14px;
        }

        .inv-location-item.active {
            color: #111827;
            font-weight: 500;
        }


        .inv-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .inv-table th {
            background: #fafafa;
            color: #6b7280;
            text-transform: none;
            font-weight: 500;
            font-size: 13px;
            padding: 12px 16px;
            border-bottom: 1px solid #edf2f7;
        }

        .inv-table td {
            padding: 16px;
            font-size: 14px;
            color: #1f2937;
            border-bottom: 1px solid #edf2f7;
        }

        .inv-table tr:hover {
            background: #f9fafb;
        }

        .table-expand {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s;
        }

        .table-expand:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .model-data-wrapper {
            background: #f1f1f1;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .model-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .model-table-header h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 500;
            color: #333;
        }

        .btn-export-make {
            background: #ce4f4b;
            color: #fff;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-export-make:hover {
            background: #b84341;
            color: #fff;
            text-decoration: none;
        }

        .btn-export-all {
            background: #ce4f4b;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-export-all:hover {
            background: #b84341;
            color: #fff;
            text-decoration: none;
        }

        .model-table {
            width: 100%;
            background: #fff;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
        }

        .model-table th {
            background: #fff;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #333;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        .model-table th.text-center {
            text-align: center;
        }

        .model-table td {
            padding: 12px 16px;
            font-size: 13px;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .model-table td.text-center {
            text-align: center;
        }

        .inv-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Daterangepicker Custom Styling - Exact Match */
        .daterangepicker {
            font-family: 'Inter', -apple-system, sans-serif;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            padding: 0;
            display: flex !important;
            flex-direction: row;
        }

        .daterangepicker .ranges {
            width: 160px;
            margin: 0 !important;
            border-right: 1px solid #f3f4f6;
            padding: 0 !important;
        }

        .daterangepicker .ranges ul {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .daterangepicker .ranges li {
            font-size: 14px;
            color: #6b7280;
            padding: 12px 16px !important;
            margin: 0 !important;
            border-bottom: 1px solid #f3f4f6;
            border-radius: 0 !important;
            background-color: transparent !important;
        }

        .daterangepicker .ranges li:hover {
            background-color: #f9fafb !important;
        }

        .daterangepicker .ranges li.active {
            background-color: #f3f4f6 !important;
            color: #4b5563;
        }

        .daterangepicker .drp-calendar {
            max-width: none;
            padding: 16px !important;
        }

        .daterangepicker .drp-calendar.left {
            border-left: none !important;
        }

        .daterangepicker .calendar-table {
            border: none !important;
            padding: 0 !important;
        }

        .daterangepicker .calendar-table th,
        .daterangepicker .calendar-table td {
            min-width: 36px;
            height: 36px;
            border: none;
            border-radius: 40px !important;
            font-size: 13px;
        }

        .daterangepicker .calendar-table td.off {
            color: #d1d5db;
        }

        .daterangepicker .calendar-table td.available:hover {
            background-color: #f3f4f6 !important;
        }

        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            background-color: #4b5563 !important;
            color: #fff !important;
        }

        .daterangepicker td.in-range {
            background-color: #f3f4f6 !important;
            border-radius: 0 !important;
            color: #374151 !important;
        }

        .daterangepicker td.start-date {
            border-radius: 40px 0 0 40px !important;
        }

        .daterangepicker td.end-date {
            border-radius: 0 40px 40px 0 !important;
        }

        .daterangepicker td.start-date.end-date {
            border-radius: 40px !important;
        }

        /* Current day red underline as per SS */
        .daterangepicker td.today {
            position: relative;
        }

        .daterangepicker td.today::after {
            content: '';
            position: absolute;
            bottom: 6px;
            left: 25%;
            width: 50%;
            height: 2px;
            background-color: #ef4444;
        }

        .daterangepicker .drp-buttons {
            display: none !important;
            /* Auto-apply or hide if not needed */
        }

        /* Top inputs match */
        .daterangepicker .drp-calendar .calendar-time {
            margin: 0 0 10px 0 !important;
        }

        .daterangepicker .calendar-table .next span,
        .daterangepicker .calendar-table .prev span {
            border-color: #9ca3af;
        }
    </style>
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">
            {{-- Inventory Top Bar --}}
            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="dashboard">
                <div class="inv-main-content row g-4 px-3">
                    {{-- Main Section: Cards and Units Sold Table --}}
                    <div class="col-xl-8 col-lg-7">
                        <div class="inv-top-bar">
                            <div class="inv-location-dropdown-wrapper">
                                <div class="inv-location-dropdown" id="locationDropdownToggle">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span id="selectedLocationName">
                                        {{ $currentDealerId ? $dealers->firstWhere('id', $currentDealerId)->name : 'All Locations' }}
                                    </span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="inv-location-menu" id="locationMenu">
                                    <div class="inv-location-item {{ !$currentDealerId ? 'active' : '' }}" data-id="0">
                                        @if (!$currentDealerId)
                                            <i class="bi bi-check2"></i>
                                        @endif
                                        All Locations
                                    </div>
                                    @foreach ($dealers as $dealer)
                                        <div class="inv-location-item {{ $currentDealerId == $dealer->id ? 'active' : '' }}"
                                            data-id="{{ $dealer->id }}">
                                            @if ($currentDealerId == $dealer->id)
                                                <i class="bi bi-check2"></i>
                                            @endif
                                            {{ $dealer->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="inv-date-range">
                                <i class="bi bi-calendar3"></i>
                                <input type="text" id="inventoryDateRange" placeholder="Date range"
                                    value="{{ $dateRange }}" readonly>
                            </div>
                        </div>

                        <div class="inv-cards-and-chart">
                            <div class="inv-cards-grid">
                                {{-- Card 1: In Stock --}}
                                <a href="{{ route('dealer.inventory.index', ['status' => 'active', 'dealer_id' => $currentDealerId]) }}"
                                    class="inv-stat-card">
                                    <div class="stat-main">
                                        <div class="stat-value">
                                            <span class="number">{{ number_format($inStockCount) }}</span>
                                            <span class="label">in stock</span>
                                        </div>
                                        <div class="stat-amount">${{ number_format($inStockValue, 0) }}</div>
                                    </div>
                                    <div class="stat-footer">
                                        <span class="footer-title">Inventory</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </div>
                                </a>

                                {{-- Card 2: Sold --}}
                                <a href="{{ route('dealer.inventory.index', ['status' => 'sold', 'dealer_id' => $currentDealerId, 'date_range' => $dateRange]) }}"
                                    class="inv-stat-card">
                                    <div class="stat-main">
                                        <div class="stat-value">
                                            <span class="number">{{ number_format($soldCount) }}</span>
                                            <span class="label">sold</span>
                                        </div>
                                        <div class="stat-amount">${{ number_format($soldValue, 0) }}</div>
                                    </div>
                                    <div class="stat-footer">
                                        <span class="footer-title">Sold Report</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </div>
                                </a>

                                {{-- Card 3: No Photos --}}
                                <a href="{{ route('dealer.inventory.index', ['no_photos' => 1, 'dealer_id' => $currentDealerId]) }}"
                                    class="inv-stat-card">
                                    <div class="stat-main">
                                        <div class="stat-value">
                                            <span class="number">{{ number_format($noPhotosCount) }}</span>
                                        </div>
                                        <div class="stat-label-only">No Photos</div>
                                    </div>
                                    <div class="stat-footer">
                                        <span class="footer-title">Fix</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </div>
                                </a>

                                {{-- Card 4: No Price --}}
                                <a href="{{ route('dealer.inventory.index', ['sortby' => 'price', 'sortorder' => 'asc', 'dealer_id' => $currentDealerId]) }}"
                                    class="inv-stat-card">
                                    <div class="stat-main">
                                        <div class="stat-value">
                                            <span class="number">{{ number_format($noPriceCount) }}</span>
                                        </div>
                                        <div class="stat-label-only">No Price</div>
                                    </div>
                                    <div class="stat-footer">
                                        <span class="footer-title">Fix</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="inv-table-header">
                            <h3>Inventory: Units Sold</h3>
                            <a id="exportAllBtn"
                                href="{{ route('dealer.inventory.dashboard.export-sold', ['dealer_id' => $currentDealerId, 'date_range' => $dateRange]) }}"
                                class="btn-export-all" title="Download CSV of sold inventory">
                                <i class="bi bi-cloud-arrow-down"></i> Export All
                            </a>
                        </div>

                        <div class="inv-table-wrapper">
                            <table class="inv-table">
                                <thead>
                                    <tr>
                                        <th>Make</th>
                                        <th class="text-center">Units Sold</th>
                                        <th class="text-center">Avg. Days</th>
                                        <th class="text-center">Est. Sales</th>
                                        <th class="text-center">Avg. Price</th>
                                        <th class="text-center"># Changes</th>
                                        <th class="text-center">Avg. Change</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($soldMakes as $makeData)
                                        <tr class="make-row" data-make-id="{{ $makeData['make_id'] }}"
                                            data-make-name="{{ $makeData['make_name'] }}">
                                            <td>{{ $makeData['make_name'] }}</td>
                                            <td class="text-center">{{ $makeData['units_sold'] }}</td>
                                            <td class="text-center">{{ $makeData['avg_days'] }}</td>
                                            <td class="text-center text-success">
                                                ${{ number_format($makeData['est_sales']) }}</td>
                                            <td class="text-center">
                                                ${{ number_format($makeData['avg_price']) }}</td>
                                            <td class="text-center">{{ $makeData['changes_count'] }}</td>
                                            <td class="text-center">{{ $makeData['avg_change'] }}</td>
                                            <td class="text-right">
                                                <span class="table-expand"><i class="bi bi-plus-lg"></i></span>
                                            </td>
                                        </tr>
                                        <tr class="model-row-container" id="model-row-{{ $makeData['make_id'] }}"
                                            style="display: none;">
                                            <td colspan="8" class="p-0">
                                                <div class="model-data-wrapper">
                                                    <div class="model-table-header">
                                                        <h4>{{ $makeData['make_name'] }} Units sold by model
                                                        </h4>
                                                        <a href="{{ route('dealer.inventory.dashboard.export-make', [
                                                            'make_id' => $makeData['make_id'],
                                                            'dealer_id' => $currentDealerId,
                                                            'date_range' => $dateRange,
                                                        ]) }}"
                                                            class="btn-export-make"
                                                            title="Download CSV for {{ $makeData['make_name'] }}">
                                                            <i class="bi bi-cloud-arrow-down"></i> Export
                                                            {{ $makeData['make_name'] }}
                                                        </a>
                                                    </div>
                                                    <table class="model-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Model</th>
                                                                <th class="text-center">Sold</th>
                                                                <th class="text-center">Est. Sales</th>
                                                                <th class="text-center">Avg. Price</th>
                                                                <th class="text-center">Avg. Days</th>
                                                                <th class="text-center">Min. Days</th>
                                                                <th class="text-center">Max. Days</th>
                                                                <th class="text-center"># Changes</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="model-tbody">
                                                            {{-- Loaded via AJAX --}}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">No data found for the selected
                                                criteria.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Sidebar: Charts Section --}}
                    <aside class="inv-sidebar col-xl-4 col-lg-5">
                        <div class="inv-card-box">
                            <div class="inv-card-box-title">Inventory Activity</div>
                            <div class="inv-card-box-content">
                                <div class="chart-container" style="position: relative; height:340px; width:100%;">
                                    <canvas id="invActivityChartV2"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="inv-card-box">
                            <div class="inv-card-box-title">Days in Inventory</div>
                            <div class="inv-card-box-content">
                                <div class="chart-container"
                                    style="position: relative; height:200px; width:100%; margin-bottom: 20px;">
                                    <canvas id="invDaysChartV2"></canvas>
                                </div>
                                <table class="inv-mini-table">
                                    <thead>
                                        <tr>
                                            <th>Days</th>
                                            <th>Units</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($daysStats as $range => $stat)
                                            <tr>
                                                <td>{{ $range }}</td>
                                                <td>{{ $stat['units'] }}</td>
                                                <td>${{ number_format($stat['total']) }}</td>
                                                <td>${{ number_format($stat['avg']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="inv-card-box border-0 bg-transparent p-0 mb-2">
                            <div class="inv-card-box-title bg-transparent ps-0 pb-1"
                                style="font-size: 15px; font-weight: 600; border-bottom: 0;">Inventory by Location</div>
                        </div>

                        <div class="inv-card-box">
                            <table class="inv-mini-table">
                                <thead>
                                    <tr>
                                        <th>Units</th>
                                        <th>Total</th>
                                        <th>Average</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($locationStats as $stat)
                                        <tr class="bg-light">
                                            <td colspan="3" class="fw-bold py-2"
                                                style="font-size: 14px; color: #333;">{{ $stat['name'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $stat['units'] }}</td>
                                            <td>${{ number_format($stat['total']) }}</td>
                                            <td>${{ number_format($stat['avg']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>

    <form id="filterForm" method="GET" style="display: none;">
        <input type="hidden" name="dealer_id" id="filterDealerId" value="{{ $currentDealerId }}">
        <input type="hidden" name="date_range" id="filterDateRange" value="{{ $dateRange }}">
    </form>
@endsection

@push('page-scripts')
    {{-- CDN Fallback for Chart.js to ensure it always loads --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Location Dropdown
            const toggle = document.getElementById('locationDropdownToggle');
            const menu = document.getElementById('locationMenu');
            const filterForm = document.getElementById('filterForm');
            const dealerIdInput = document.getElementById('filterDealerId');

            if (toggle) {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menu.classList.toggle('show');
                });
            }

            document.addEventListener('click', function() {
                if (menu) menu.classList.remove('show');
            });

            document.querySelectorAll('.inv-location-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    dealerIdInput.value = id;
                    filterForm.submit();
                });
            });

            // Date Range Picker
            const dateInput = $('#inventoryDateRange');
            if (dateInput.length) {
                const initialDate = "{{ $dateRange }}";
                let startDate = moment().subtract(29, 'days');
                let endDate = moment();

                if (initialDate && initialDate.includes(' - ')) {
                    const parts = initialDate.split(' - ');
                    startDate = moment(parts[0], 'MM/DD/YYYY');
                    endDate = moment(parts[1], 'MM/DD/YYYY');
                }

                dateInput.daterangepicker({
                    startDate: startDate,
                    endDate: endDate,
                    opens: 'left',
                    autoUpdateInput: true,
                    alwaysShowCalendars: true,
                    ranges: {
                        'Last week': [moment().subtract(6, 'days'), moment()],
                        'Month to date': [moment().startOf('month'), moment().endOf('month')],
                        'Last 28 days': [moment().subtract(27, 'days'), moment()],
                        'Last 30 days': [moment().subtract(29, 'days'), moment()],
                        'Last 90 days': [moment().subtract(89, 'days'), moment()],
                        'Last 180 days': [moment().subtract(179, 'days'), moment()],
                        'Last 12 months': [moment().subtract(1, 'year').add(1, 'day'), moment()]
                    },
                    locale: {
                        format: 'MMM D, YYYY',
                        separator: ' - ',
                        applyLabel: 'Apply',
                        cancelLabel: 'Cancel',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom',
                        weekLabel: 'W',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July',
                            'August', 'September', 'October', 'November', 'December'
                        ],
                        firstDay: 1
                    }
                }, function(start, end, label) {
                    const dateStr = start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY');
                    document.getElementById('filterDateRange').value = dateStr;
                    filterForm.submit();
                });
            }

        });
    </script>

    <script>
        // Independent Expandable Rows Logic
        (function() {
            function initExpandableRows() {
                document.addEventListener('click', function(e) {
                    const expandBtn = e.target.closest('.table-expand');
                    if (!expandBtn) return;

                    e.preventDefault();
                    e.stopPropagation();

                    const row = expandBtn.closest('.make-row');
                    if (!row) return;

                    const makeId = row.getAttribute('data-make-id');
                    const modelRow = document.getElementById('model-row-' + makeId);
                    if (!modelRow) return;

                    const tbody = modelRow.querySelector('.model-tbody');
                    const isCollapsed = window.getComputedStyle(modelRow).display === 'none';

                    if (isCollapsed) {
                        modelRow.style.display = 'table-row';
                        expandBtn.innerHTML = '<i class="bi bi-dash-lg"></i>';

                        if (tbody && tbody.children.length === 0) {
                            tbody.innerHTML =
                                '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-danger"></div> Loading...</td></tr>';

                            const dealerId = "{{ $currentDealerId }}";
                            const dateRange = "{{ $dateRange }}";
                            const url =
                                `{{ route('dealer.inventory.dashboard.sold-models') }}?make_id=${makeId}&dealer_id=${dealerId}&date_range=${encodeURIComponent(dateRange)}`;

                            fetch(url)
                                .then(response => response.json())
                                .then(data => {
                                    tbody.innerHTML = '';
                                    if (data.length === 0) {
                                        tbody.innerHTML =
                                            '<tr><td colspan="8" class="text-center py-4">No data available</td></tr>';
                                        return;
                                    }
                                    data.forEach(model => {
                                        const tr = document.createElement('tr');
                                        tr.innerHTML = `
                                                    <td>${model.model_name}</td>
                                                    <td class="text-center">${model.sold}</td>
                                                    <td class="text-center text-success">$${model.est_sales}</td>
                                                    <td class="text-center">$${model.avg_price}</td>
                                                    <td class="text-center">${model.avg_days}</td>
                                                    <td class="text-center">${model.min_days}</td>
                                                    <td class="text-center">${model.max_days}</td>
                                                    <td class="text-center">${model.changes_count}</td>
                                                `;
                                        tbody.appendChild(tr);
                                    });
                                })
                                .catch(error => {
                                    tbody.innerHTML =
                                        '<tr><td colspan="8" class="text-center text-danger py-4">Error loading data</td></tr>';
                                });
                        }
                    } else {
                        modelRow.style.display = 'none';
                        expandBtn.innerHTML = '<i class="bi bi-plus-lg"></i>';
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initExpandableRows);
            } else {
                initExpandableRows();
            }
        })();
    </script>

    <script>
        // Separate Chart Initialization
        (function() {
            function initCharts() {
                if (typeof Chart === 'undefined') {
                    setTimeout(initCharts, 100);
                    return;
                }

                const ctx1 = document.getElementById('invActivityChartV2');
                if (ctx1) {
                    new Chart(ctx1, {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                label: 'Total Views',
                                data: @json($chartViews),
                                borderColor: '#3ab5f5',
                                backgroundColor: 'rgba(58, 181, 245, 0.08)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 0,
                                yAxisID: 'y1'
                            }, {
                                label: 'Units In Stock',
                                data: @json($chartStock),
                                borderColor: '#f56e4e',
                                backgroundColor: '#f56e4e',
                                fill: false,
                                tension: 0,
                                borderWidth: 2,
                                yAxisID: 'y2',
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#f56e4e'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    align: 'end'
                                }
                            },
                            scales: {
                                y1: {
                                    type: 'linear',
                                    position: 'left',
                                    beginAtZero: true
                                },
                                y2: {
                                    type: 'linear',
                                    position: 'right',
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                const ctx2 = document.getElementById('invDaysChartV2');
                if (ctx2) {
                    new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: ['0-30', '31-60', '61-90', '91-120', '120+'],
                            datasets: [{
                                data: @json($chartDays),
                                backgroundColor: '#f56e4e',
                                borderRadius: 6,
                                barThickness: 24
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCharts);
            } else {
                initCharts();
            }
        })();
    </script>
@endpush
