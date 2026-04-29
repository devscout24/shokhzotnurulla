@extends('layouts.dealer.app')

@section('title', __('Inventory Dashboard') . ' | '. __(config('app.name')))

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">
            {{-- Inventory Top Bar --}}
            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="dashboard">
                <div class="inv-main-content">
                    <div class="inv-table-section">
                        <!-- filters and summary cards -->
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
                                        @if(!$currentDealerId) <i class="bi bi-check2"></i> @endif
                                        All Locations
                                    </div>
                                    @foreach($dealers as $dealer)
                                        <div class="inv-location-item {{ $currentDealerId == $dealer->id ? 'active' : '' }}" data-id="{{ $dealer->id }}">
                                            @if($currentDealerId == $dealer->id) <i class="bi bi-check2"></i> @endif
                                            {{ $dealer->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="inv-date-range">
                                <i class="bi bi-calendar3"></i>
                                <input type="text" id="inventoryDateRange" placeholder="Date range" value="{{ $dateRange }}" readonly>
                            </div>
                        </div>

                        <div class="inv-cards-and-chart">
                            <div class="inv-cards-grid">
                                {{-- Card 1: In Stock --}}
                                <a href="{{ route('dealer.inventory.index', ['status' => 'active', 'dealer_id' => $currentDealerId]) }}" class="inv-stat-card">
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
                                <a href="{{ route('dealer.inventory.index', ['status' => 'sold', 'dealer_id' => $currentDealerId, 'date_range' => $dateRange]) }}" class="inv-stat-card">
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
                                <a href="{{ route('dealer.inventory.index', ['no_photos' => 1, 'dealer_id' => $currentDealerId]) }}" class="inv-stat-card">
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
                                <a href="{{ route('dealer.inventory.index', ['sortby' => 'price', 'sortorder' => 'asc', 'dealer_id' => $currentDealerId]) }}" class="inv-stat-card">
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
                            <button class="btn-export-all">
                                <i class="bi bi-download"></i> Export All
                            </button>
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
                                        <tr class="make-row" data-make-id="{{ $makeData['make_id'] }}" data-make-name="{{ $makeData['make_name'] }}">
                                            <td>{{ strtoupper($makeData['make_name']) }}</td>
                                            <td class="text-center">{{ $makeData['units_sold'] }}</td>
                                            <td class="text-center">{{ $makeData['avg_days'] }}</td>
                                            <td class="text-center text-success">${{ number_format($makeData['est_sales']) }}</td>
                                            <td class="text-center text-success">${{ number_format($makeData['avg_price']) }}</td>
                                            <td class="text-center">{{ $makeData['changes_count'] }}</td>
                                            <td class="text-center">
                                                {{ $makeData['avg_change'] }}
                                            </td>
                                            <td class="text-right">
                                                <span class="table-expand"><i class="bi bi-plus-lg"></i></span>
                                            </td>
                                        </tr>
                                        <tr class="model-row-container" id="model-row-{{ $makeData['make_id'] }}" style="display: none;">
                                            <td colspan="8" class="p-0">
                                                <div class="model-data-wrapper">
                                                    <div class="model-table-header">
                                                        <h4>{{ strtoupper($makeData['make_name']) }} Units sold by model</h4>
                                                        <button class="btn-export-make">
                                                            <i class="bi bi-download"></i> Export {{ strtoupper($makeData['make_name']) }}
                                                        </button>
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
                                            <td colspan="8" class="text-center py-4">No data found for the selected criteria.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <aside class="inv-sidebar">
                        {{-- Charts are placeholders as requested to build first section and table --}}
                        <div class="inv-card-box">
                            <div class="inv-card-box-title">Inventory Activity</div>
                            <div class="inv-card-box-content">
                                <img src="{{ asset('assets/panels/common/images/logos/graph-1.png') }}" alt="Inventory activity">
                            </div>
                        </div>

                        <div class="inv-card-box">
                            <div class="inv-card-box-title">Days in Inventory</div>
                            <div class="inv-card-box-content">
                                <img src="{{ asset('assets/panels/common/images/logos/graph-2.png') }}" alt="Days in inventory">
                                <table class="inv-mini-table">
                                    <tbody>
                                        <tr>
                                            <td>Days</td>
                                            <td>Units</td>
                                            <td>Total</td>
                                            <td>Average</td>
                                        </tr>
                                        <tr>
                                            <td>0-30</td>
                                            <td>22</td>
                                            <td>$466,296</td>
                                            <td>$21,195</td>
                                        </tr>
                                        <tr>
                                            <td>31-60</td>
                                            <td>9</td>
                                            <td>$163,397</td>
                                            <td>$18,155</td>
                                        </tr>
                                        <tr>
                                            <td>61-90</td>
                                            <td>9</td>
                                            <td>$153,696</td>
                                            <td>$17,077</td>
                                        </tr>
                                        <tr>
                                            <td>91-120</td>
                                            <td>6</td>
                                            <td>$110,398</td>
                                            <td>$18,400</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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

@push('styles')
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
            color: #16a34a; /* Green for amounts */
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
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
            background: #f8fafc;
            padding: 24px;
            border-bottom: 1px solid #e2e8f0;
        }
        .model-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .model-table-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }
        .btn-export-make {
            background: #dc2626;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-export-make:hover {
            background: #b91c1c;
        }
        .model-table {
            width: 100%;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .model-table th {
            background: #f1f5f9;
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }
        .model-table td {
            padding: 12px 16px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .inv-sidebar {
            width: 320px;
        }
    </style>
@endpush

@push('scripts')
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
            const dateInput = document.getElementById('inventoryDateRange');
            if (dateInput) {
                flatpickr(dateInput, {
                    mode: "range",
                    dateFormat: "m/d/Y",
                    defaultDate: "{{ $dateRange }}",
                    onClose: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            document.getElementById('filterDateRange').value = dateStr;
                            filterForm.submit();
                        }
                    }
                });
            }

            // Expandable Rows
            document.querySelectorAll('.make-row').forEach(row => {
                const expandBtn = row.querySelector('.table-expand');
                const makeId = row.getAttribute('data-make-id');
                const modelRow = document.getElementById(`model-row-${makeId}`);
                const tbody = modelRow.querySelector('.model-tbody');

                expandBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isExpanded = modelRow.style.display !== 'none';

                    if (!isExpanded) {
                        // Expand
                        modelRow.style.display = 'table-row';
                        this.innerHTML = '<i class="bi bi-dash-lg"></i>';

                        // Fetch data if not already loaded
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-danger" role="status"></div> Loading...</td></tr>';
                            
                            const dealerId = "{{ $currentDealerId }}";
                            const dateRange = "{{ $dateRange }}";
                            
                            fetch(`{{ route('dealer.inventory.dashboard.sold-models') }}?make_id=${makeId}&dealer_id=${dealerId}&date_range=${dateRange}`)
                                .then(response => response.json())
                                .then(data => {
                                    tbody.innerHTML = '';
                                    if (data.length === 0) {
                                        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No data available</td></tr>';
                                        return;
                                    }
                                    data.forEach(model => {
                                        const tr = document.createElement('tr');
                                        tr.innerHTML = `
                                            <td>${model.model_name}</td>
                                            <td class="text-center">${model.sold}</td>
                                            <td class="text-center text-success">$${model.est_sales}</td>
                                            <td class="text-center text-success">$${model.avg_price}</td>
                                            <td class="text-center">${model.avg_days}</td>
                                            <td class="text-center">${model.min_days}</td>
                                            <td class="text-center">${model.max_days}</td>
                                            <td class="text-center">${model.changes_count}</td>
                                        `;
                                        tbody.appendChild(tr);
                                    });
                                })
                                .catch(error => {
                                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Error loading data</td></tr>';
                                    console.error('Error:', error);
                                });
                        }
                    } else {
                        // Collapse
                        modelRow.style.display = 'none';
                        this.innerHTML = '<i class="bi bi-plus-lg"></i>';
                    }
                });
            });
        </div>);
    </script>
@endpush