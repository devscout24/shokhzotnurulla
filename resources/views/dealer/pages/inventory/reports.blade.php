@extends('layouts.dealer.app')

@section('title', __('Inventory Reports') . ' | '. __(config('app.name')))

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">
            {{-- Inventory Top Bar --}}
            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="reports">
                <div class="reports-container">
                    
                    {{-- Filters Section --}}
                    <div class="report-filters-bar">
                        <form action="{{ route('dealer.inventory.reports.index') }}" method="GET" class="filters-form" id="reportsFilterForm">
                            <div class="filter-item date-range-picker-wrapper">
                                <div class="date-range-display" id="dateRangeDisplay">
                                    <i class="bi bi-calendar3"></i>
                                    <span id="dateRangeText">3/30/2026 - 4/26/2026</span>
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </div>
                                <div class="date-picker-dropdown" id="datePickerDropdown">
                                    <div class="date-picker-sidebar">
                                        <ul>
                                            <li data-range="last-week">Last week</li>
                                            <li data-range="month-to-date">Month to date</li>
                                            <li data-range="last-28-days">Last 28 days</li>
                                            <li data-range="last-30-days" class="active">Last 30 days</li>
                                            <li data-range="last-90-days">Last 90 days</li>
                                            <li data-range="last-180-days">Last 180 days</li>
                                            <li data-range="last-12-months">Last 12 months</li>
                                        </ul>
                                    </div>
                                    <div class="date-picker-calendar">
                                        <div id="inlineFlatpickr"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="date_range" id="reportDateRange" value="{{ request('date_range') }}">
                                <input type="hidden" name="from" id="fromDate" value="{{ request('from') }}">
                                <input type="hidden" name="to" id="toDate" value="{{ request('to') }}">
                            </div>

                            <div class="filter-item select-wrapper">
                                <select name="location_id" class="form-select">
                                    <option value="">[Location]</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-item select-wrapper">
                                <select name="make_id" class="form-select" id="makeSelect">
                                    <option value="">[Make]</option>
                                    @foreach($makes as $make)
                                        <option value="{{ $make->id }}" {{ request('make_id') == $make->id ? 'selected' : '' }}>{{ $make->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-item select-wrapper">
                                <select name="model_id" class="form-select" id="modelSelect">
                                    <option value="">[Model]</option>
                                </select>
                            </div>

                            <div class="filter-item search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Stock # or VIN">
                            </div>

                            <div class="filter-actions">
                                <a href="{{ route('dealer.inventory.reports.export', request()->all()) }}" class="btn-export">
                                    <i class="bi bi-cloud-download"></i> Export
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- Summary Cards Section --}}
                    <div class="report-summary-cards">
                        <div class="summary-card">
                            <div class="card-icon"><i class="bi bi-calendar-event"></i></div>
                            <div class="card-value">{{ number_format($avgDaysOnMarket, 0) }}</div>
                            <div class="card-label">Avg. Days on Market</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon text-success"><i class="bi bi-cash-stack"></i></div>
                            <div class="card-value">${{ number_format($totalInvestment, 0) }}</div>
                            <div class="card-label">Total Investment</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon text-primary"><i class="bi bi-file-earmark-text"></i></div>
                            <div class="card-value">${{ number_format($totalSales, 0) }}</div>
                            <div class="card-label">Total Sales</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon text-info"><i class="bi bi-pie-chart"></i></div>
                            <div class="card-value">${{ number_format($grossProfit, 0) }}</div>
                            <div class="card-label">Gross Profit</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon text-warning"><i class="bi bi-percent"></i></div>
                            <div class="card-value">{{ number_format($grossMargin, 1) }}%</div>
                            <div class="card-label">Gross Margin</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon text-danger"><i class="bi bi-fire"></i></div>
                            <div class="card-value">${{ number_format($avgGrossProfit, 0) }}</div>
                            <div class="card-label">Avg. Gross Profit</div>
                        </div>
                    </div>

                    {{-- Table Section --}}
                    <div class="report-table-section">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Date <i class="bi bi-caret-down-fill text-danger"></i></th>
                                    <th>Days <i class="bi bi-arrow-down-up text-secondary"></i></th>
                                    <th>Vehicle ({{ $vehicles->count() }})</th>
                                    <th>Stock # <i class="bi bi-arrow-down-up text-secondary"></i></th>
                                    <th>Mileage</th>
                                    <th>Cost</th>
                                    <th>Price <i class="bi bi-arrow-down-up text-secondary"></i></th>
                                    <th>Sold Price <i class="bi bi-arrow-down-up text-secondary"></i></th>
                                    <th>Profit</th>
                                    <th>Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicles as $vehicle)
                                    @php
                                        $days = $vehicle->report_days;
                                        $dayColor = 'bg-danger';
                                        if ($days <= 30) $dayColor = 'bg-success';
                                        elseif ($days <= 60) $dayColor = 'bg-warning';
                                        
                                        // User specifically said: "if sold date is blank then mark as green"
                                        if (empty($vehicle->prices->sold_date)) $dayColor = 'bg-success';
                                    @endphp
                                    <tr>
                                        <td>{{ $vehicle->inventory_date ? $vehicle->inventory_date->format('n/j/Y') : ($vehicle->created_at ? $vehicle->created_at->format('n/j/Y') : '--') }}</td>
                                        <td>
                                            <span class="days-badge {{ $dayColor }}">{{ $days }}</span>
                                        </td>
                                        <td class="vehicle-info-cell">
                                            <div class="vehicle-thumb">
                                                <img src="{{ $vehicle->primaryPhoto?->url ?? asset('assets/panels/common/images/logos/no-image.png') }}" alt="">
                                            </div>
                                            <div class="vehicle-title-wrap">
                                                <a href="{{ route('dealer.inventory.vdp.show', $vehicle) }}" class="vehicle-title">{{ $vehicle->display_title }}</a>
                                                <i class="bi bi-box-arrow-up-right text-danger ms-1 small"></i>
                                            </div>
                                        </td>
                                        <td>{{ $vehicle->stock_number }}</td>
                                        <td>{{ number_format($vehicle->mileage) }}</td>
                                        <td>
                                            @if($vehicle->prices->dealer_cost)
                                                ${{ number_format($vehicle->prices->dealer_cost) }} <i class="bi bi-pencil-square text-secondary ms-1"></i>
                                            @else
                                                -- <i class="bi bi-pencil-square text-secondary ms-1"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if($vehicle->prices->msrp)
                                                ${{ number_format($vehicle->prices->msrp) }} <i class="bi bi-pencil-square text-secondary ms-1"></i>
                                            @else
                                                -- <i class="bi bi-pencil-square text-secondary ms-1"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if($vehicle->prices->sold_price)
                                                ${{ number_format($vehicle->prices->sold_price) }} <i class="bi bi-pencil-square text-secondary ms-1"></i>
                                            @else
                                                -- <i class="bi bi-pencil-square text-secondary ms-1"></i>
                                            @endif
                                        </td>
                                        <td class="{{ $vehicle->report_profit !== null && $vehicle->report_profit > 0 ? 'text-success' : ($vehicle->report_profit !== null && $vehicle->report_profit < 0 ? 'text-danger' : '') }}">
                                            @if($vehicle->report_profit !== null)
                                                ${{ number_format($vehicle->report_profit) }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="{{ $vehicle->report_margin !== null && $vehicle->report_margin > 0 ? 'text-success' : ($vehicle->report_margin !== null && $vehicle->report_margin < 0 ? 'text-danger' : '') }}">
                                            @if($vehicle->report_margin !== null)
                                                {{ number_format($vehicle->report_margin, 1) }}%
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        .reports-container {
            padding: 20px;
        }
        .report-filters-bar {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        .filters-form {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-item {
            position: relative;
        }

        /* Date Range Picker Style */
        .date-range-picker-wrapper {
            position: relative;
            min-width: 250px;
        }
        .date-range-display {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background-color: #f8f9fa;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        .date-range-display i {
            color: #6c757d;
        }
        .date-picker-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: none;
            margin-top: 5px;
            flex-direction: row;
        }
        .date-picker-dropdown.show {
            display: flex;
        }
        .date-picker-sidebar {
            width: 160px;
            border-right: 1px solid #f1f1f1;
            padding: 10px 0;
        }
        .date-picker-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .date-picker-sidebar li {
            padding: 8px 20px;
            font-size: 13px;
            color: #495057;
            cursor: pointer;
            transition: all 0.2s;
        }
        .date-picker-sidebar li:hover {
            background-color: #f8f9fa;
            color: #007bff;
        }
        .date-picker-sidebar li.active {
            background-color: #e7f1ff;
            color: #007bff;
            font-weight: 600;
        }
        .date-picker-calendar {
            padding: 10px;
        }
        
        /* Select wrappers */
        .filter-item select {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 14px;
            min-width: 150px;
            background-color: #f8f9fa;
            cursor: pointer;
        }
        .search-box input {
            padding: 8px 12px 8px 32px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 14px;
            min-width: 250px;
            background-color: #f8f9fa;
        }
        .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .btn-export {
            background: #d9534f;
            color: #fff;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        .btn-export:hover {
            background: #c9302c;
            color: #fff;
        }

        .report-summary-cards {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .summary-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .card-icon {
            font-size: 24px;
            margin-bottom: 10px;
            color: #6c757d;
        }
        .card-value {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 4px;
        }
        .card-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        .report-table-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-table th {
            background: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
        }
        .report-table td {
            padding: 12px 15px;
            font-size: 14px;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
        }
        .days-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            color: #fff;
            font-weight: 600;
            font-size: 12px;
            min-width: 35px;
            text-align: center;
        }
        .vehicle-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 250px;
        }
        .vehicle-thumb {
            width: 48px;
            height: 36px;
            border-radius: 4px;
            overflow: hidden;
            background: #f0f0f0;
        }
        .vehicle-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .vehicle-title {
            font-weight: 600;
            color: #212529;
            text-decoration: none;
        }
        .vehicle-title:hover {
            color: #d9534f;
        }
        .text-success { color: #28a745 !important; }
        .text-danger { color: #dc3545 !important; }
        .bg-success { background-color: #28a745 !important; }
        .bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        .bg-danger { background-color: #dc3545 !important; }

        /* Flatpickr overrides for inline calendar */
        .flatpickr-calendar.inline {
            box-shadow: none;
            border: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateRangeDisplay = document.getElementById('dateRangeDisplay');
            const datePickerDropdown = document.getElementById('datePickerDropdown');
            const dateRangeText = document.getElementById('dateRangeText');
            const sidebarItems = document.querySelectorAll('.date-picker-sidebar li');
            const fromInput = document.getElementById('fromDate');
            const toInput = document.getElementById('toDate');
            const rangeInput = document.getElementById('reportDateRange');
            const form = document.getElementById('reportsFilterForm');

            // Initialize inline flatpickr
            const fp = flatpickr("#inlineFlatpickr", {
                inline: true,
                mode: "range",
                dateFormat: "m/d/Y",
                defaultDate: [fromInput.value, toInput.value],
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const from = instance.formatDate(selectedDates[0], "Y-m-d");
                        const to = instance.formatDate(selectedDates[1], "Y-m-d");
                        const fromDisp = instance.formatDate(selectedDates[0], "n/j/Y");
                        const toDisp = instance.formatDate(selectedDates[1], "n/j/Y");
                        
                        updateDateRange(from, to, `${fromDisp} - ${toDisp}`);
                        
                        // Clear active sidebar selection
                        sidebarItems.forEach(i => i.classList.remove('active'));
                    }
                }
            });

            // Toggle dropdown
            dateRangeDisplay.addEventListener('click', function(e) {
                e.stopPropagation();
                datePickerDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!datePickerDropdown.contains(e.target) && e.target !== dateRangeDisplay) {
                    datePickerDropdown.classList.remove('show');
                }
            });

            function updateDateRange(from, to, display) {
                fromInput.value = from;
                toInput.value = to;
                dateRangeText.innerText = display;
                rangeInput.value = display;
                
                // Optional: trigger form submit
                // form.submit();
            }

            // Sidebar shortcuts
            sidebarItems.forEach(item => {
                item.addEventListener('click', function() {
                    sidebarItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    
                    const range = this.getAttribute('data-range');
                    let start = new Date();
                    let end = new Date();
                    
                    switch(range) {
                        case 'last-week':
                            start.setDate(end.getDate() - 7);
                            break;
                        case 'month-to-date':
                            start.setDate(1);
                            break;
                        case 'last-28-days':
                            start.setDate(end.getDate() - 28);
                            break;
                        case 'last-30-days':
                            start.setDate(end.getDate() - 30);
                            break;
                        case 'last-90-days':
                            start.setDate(end.getDate() - 90);
                            break;
                        case 'last-180-days':
                            start.setDate(end.getDate() - 180);
                            break;
                        case 'last-12-months':
                            start.setFullYear(end.getFullYear() - 1);
                            break;
                    }
                    
                    fp.setDate([start, end]);
                    
                    const fromStr = fp.formatDate(start, "Y-m-d");
                    const toStr = fp.formatDate(end, "Y-m-d");
                    const fromDisp = fp.formatDate(start, "n/j/Y");
                    const toDisp = fp.formatDate(end, "n/j/Y");
                    
                    updateDateRange(fromStr, toStr, `${fromDisp} - ${toDisp}`);
                    
                    // Auto-submit after selection
                    form.submit();
                });
            });

            // Apply existing values to display
            if (fromInput.value && toInput.value) {
                const d1 = new Date(fromInput.value);
                const d2 = new Date(toInput.value);
                dateRangeText.innerText = `${d1.toLocaleDateString()} - ${d2.toLocaleDateString()}`;
            }

            // Handle make change to fetch models
            const makeSelect = document.getElementById('makeSelect');
            const modelSelect = document.getElementById('modelSelect');

            makeSelect.addEventListener('change', function() {
                const makeId = this.value;
                modelSelect.innerHTML = '<option value="">[Model]</option>';
                
                if (makeId) {
                    fetch(`{{ route('dealer.inventory.models') }}?make_id=${makeId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(model => {
                                const option = document.createElement('option');
                                option.value = model.id;
                                option.textContent = model.name;
                                modelSelect.appendChild(option);
                            });
                        });
                }
            });

            // Auto-submit on select change
            document.querySelectorAll('#reportsFilterForm select').forEach(select => {
                select.addEventListener('change', function() {
                    if (this.id !== 'makeSelect') {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
