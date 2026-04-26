@extends('layouts.dealer.app')

@section('title', __('Inventory Reports') . ' | '. __(config('app.name')))

@section('page-content')
    {{-- Flatpickr for Date Range --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">
            {{-- Inventory Top Bar --}}
            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="reports">
                <div class="reports-container">
                    
                    {{-- Filters Section --}}
                    <div class="report-filters-bar">
                        <form action="{{ route('dealer.inventory.reports.index') }}" method="GET" class="filters-form" id="reportsFilterForm">
                            {{-- Date Range with Prefix --}}
                            <div class="filter-item input-group-custom date-range-picker-wrapper">
                                <div class="input-group-prefix" id="dateRangeDisplay">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="form-control-wrapper">
                                    <span id="dateRangeText">{{ request('from') && request('to') ? date('n/j/Y', strtotime(request('from'))) . ' - ' . date('n/j/Y', strtotime(request('to'))) : 'Select Date Range' }}</span>
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

                            {{-- Search with Prefix --}}
                            <div class="filter-item input-group-custom">
                                <div class="input-group-prefix">
                                    <i class="bi bi-search"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Stock # or VIN" class="form-control">
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
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 448 512">
                                    <path fill="currentColor" d="M120 0c13.3 0 24 10.7 24 24l0 40 160 0 0-40c0-13.3 10.7-24 24-24s24 10.7 24 24l0 40 32 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 128C0 92.7 28.7 64 64 64l32 0 0-40c0-13.3 10.7-24 24-24zm0 112l-56 0c-8.8 0-16 7.2-16 16l0 288c0 8.8 7.2 16 16 16l320 0c8.8 0 16-7.2 16-16l0-288c0-8.8-7.2-16-16-16l-264 0zm24 144l0 80 80 0 0-80-80 0zm-48 0c0-26.5 21.5-48 48-48l80 0c26.5 0 48 21.5 48 48l0 80c0 26.5-21.5 48-48 48l-80 0c-26.5 0-48-21.5-48-48l0-80z"></path>
                                </svg>
                            </div>
                            <div class="card-value text-danger">{{ number_format($avgDaysOnMarket, 0) }}</div>
                            <div class="card-label">Avg. Days on Market</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 640 512">
                                    <path fill="currentColor" d="M96.1 112l384 0c8.8 0 16 7.2 16 16l0 93.2c14.3-11.1 30.9-18 48-20.6l0-72.5c0-35.3-28.7-64-64-64l-384 0c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64l191.5 0c5.3-18 15-34.4 28.3-47.8l.2-.2-220 0c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16zm328 160l-96 0c-13.3 0-24 10.7-24 24s10.7 24 24 24l68 0 42.9-42.9c-4.1-3.2-9.2-5.1-14.8-5.1zm-120-56c0 13.3 10.7 24 24 24l96 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-96 0c-13.3 0-24 10.7-24 24zm-108-64c-11 0-20 9-20 20l0 4.2c-24.7 2.3-44 23.1-44 48.4 0 23.2 16.3 43.1 39 47.6l34.1 6.8c4 .8 6.9 4.3 6.9 8.4 0 4.7-3.8 8.6-8.6 8.6l-43.4 0c-11 0-20 9-20 20s9 20 20 20l16 0 0 4c0 11 9 20 20 20s20-9 20-20l0-5.6c20.7-5.5 36-24.5 36-46.9 0-23.2-16.3-43.1-39-47.6L179 233c-4-.8-6.9-4.3-6.9-8.4 0-4.7 3.8-8.6 8.6-8.6l43.4 0c11 0 20-9 20-20s-9-20-20-20l-8 0 0-4c0-11-9-20-20-20zM332.3 466.9l-11.9 59.6c-.2 .9-.3 1.9-.3 2.9 0 8 6.5 14.6 14.6 14.6 1 0 1.9-.1 2.9-.3l59.6-11.9c12.4-2.5 23.8-8.6 32.7-17.5l118.9-118.9-80-80-118.9 118.9c-8.9 8.9-15 20.3-17.5 32.7zm267.8-123c22.1-22.1 22.1-57.9 0-80s-57.9-22.1-80 0l-28.8 28.8 80 80 28.8-28.8z"></path>
                                </svg>
                            </div>
                            <div class="card-value text-success">${{ number_format($totalInvestment, 0) }}</div>
                            <div class="card-label">Total Investment</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 384 512">
                                    <path fill="currentColor" d="M32.4 1.5C25.1-1.2 16.8-.2 10.3 4.3S0 16.1 0 24L0 488c0 7.9 3.9 15.2 10.3 19.7s14.7 5.5 22.1 2.7l55-20.6 47.1 20.2c6.5 2.8 13.9 2.6 20.2-.6l37.3-18.6 37.3 18.6c6.3 3.2 13.7 3.4 20.2 .6l47.1-20.2 55 20.6c7.4 2.8 15.6 1.7 22.1-2.7S384 495.9 384 488l0-464c0-7.9-3.9-15.2-10.3-19.7s-14.7-5.5-22.1-2.7l-55 20.6-47.1-20.2C243-.8 235.6-.6 229.3 2.5L192 21.2 154.7 2.5C148.4-.6 141-.8 134.5 1.9L87.4 22.1 32.4 1.5zM48 453.4L48 58.6 79.6 70.5c5.8 2.2 12.2 2 17.9-.4l45.8-19.6 38 19c6.8 3.4 14.7 3.4 21.5 0l38-19 45.8 19.6c5.7 2.4 12.1 2.6 17.9 .4l31.6-11.8 0 394.7-31.6-11.8c-5.8-2.2-12.2-2-17.9 .4l-45.8 19.6-38-19c-6.8-3.4-14.7-3.4-21.5 0l-38 19-45.8-19.6c-5.7-2.4-12.1-2.6-17.9-.4L48 453.4zM120 136c-13.3 0-24 10.7-24 24s10.7 24 24 24l144 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-144 0zm0 192c-13.3 0-24 10.7-24 24s10.7 24 24 24l144 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-144 0zM96 256c0 13.3 10.7 24 24 24l144 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-144 0c-13.3 0-24 10.7-24 24z"></path>
                                </svg>
                            </div>
                            <div class="card-value text-success">${{ number_format($totalSales, 0) }}</div>
                            <div class="card-label">Total Sales</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 576 512">
                                    <path fill="currentColor" d="M352.4 54l0 138 138 0C473 124.6 419.9 71.4 352.4 54zm-144 210l0-173.1c-74.6 26.4-128 97.5-128 181.1 0 106 86 192 192 192 24.6 0 48-4.6 69.5-12.9L225 309.9c-10.7-12.9-16.6-29.2-16.6-45.9zm333.9-55.9c2.3 17.5-12.2 31.9-29.9 31.9l-176 0c-17.7 0-32-14.3-32-32l0-176c0-17.7 14.4-32.2 31.9-29.9 107 14.2 191.8 99 206 206zM256.4 66.7l0 197.3c0 5.6 2 11 5.5 15.3L394 438.7c11.7 14.1 9.2 35.4-6.9 44.1-34.1 18.6-73.2 29.2-114.7 29.2-132.5 0-240-107.5-240-240 0-115.5 81.5-211.9 190.2-234.8 18.1-3.8 33.8 11 33.8 29.5zM541.7 288c18.5 0 33.3 15.7 29.5 33.8-10.2 48.4-35 91.4-69.6 124.2-12.3 11.7-31.6 9.2-42.4-3.9L374.9 340.4c-17.3-20.9-2.4-52.4 24.6-52.4l142.2 0z"></path>
                                </svg>
                            </div>
                            <div class="card-value">${{ number_format($grossProfit, 0) }}</div>
                            <div class="card-label">Gross Profit</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512">
                                    <path fill="currentColor" d="M200.3 81.5C210.9 61.5 231.9 48 256 48s45.1 13.5 55.7 33.5c5.4 10.2 17.2 15.1 28.2 11.7 21.6-6.6 46.1-1.4 63.1 15.7s22.3 41.5 15.7 63.1c-3.4 11 1.5 22.9 11.7 28.2 20 10.6 33.5 31.6 33.5 55.7s-13.5 45.1-33.5 55.7c-10.2 5.4-15.1 17.2-11.7 28.2 6.6 21.6 1.4 46.1-15.7 63.1s-41.5 22.3-63.1 15.7c-11-3.4-22.9 1.5-28.2 11.7-10.6 20-31.6 33.5-55.7 33.5s-45.1-13.5-55.7-33.5c-5.4-10.2-17.2-15.1-28.2-11.7-21.6 6.6-46.1 1.4-63.1-15.7S86.6 361.6 93.2 340c3.4-11-1.5-22.9-11.7-28.2-20-10.6-33.5-31.6-33.5-55.7s13.5-45.1 33.5-55.7c10.2-5.4 15.1-17.2 11.7-28.2-6.6-21.6-1.4-46.1 15.7-63.1S150.4 86.6 172 93.2c11 3.4 22.9-1.5 28.2-11.7zM256 0c-35.9 0-67.8 17-88.1 43.4-33-4.3-67.6 6.2-93 31.6s-35.9 60-31.6 93C17 188.2 0 220.1 0 256s17 67.8 43.4 88.1c-4.3 33 6.2 67.6 31.6 93s60 35.9 93 31.6C188.2 495 220.1 512 256 512s67.8-17 88.1-43.4c33 4.3 67.6-6.2 93-31.6s35.9-60 31.6-93C495 323.8 512 291.9 512 256s-17-67.8-43.4-88.1c4.3-33-6.2-67.6-31.6-93s-60-35.9-93-31.6C323.8 17 291.9 0 256 0zM192 224a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM320 352a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm17-177c-9.4-9.4-24.6-9.4-33.9 0L175 303c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0L337 209c9.4-9.4 9.4-24.6 0-33.9z"></path>
                                </svg>
                            </div>
                            <div class="card-value">{{ number_format($grossMargin, 1) }}%</div>
                            <div class="card-label">Gross Margin</div>
                        </div>
                        <div class="summary-card">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512">
                                    <path fill="currentColor" d="M32 96a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM280 56c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 220.8-191.4 160.8c-10.1 8.5-11.5 23.7-2.9 33.8s23.7 11.5 33.8 2.9l184.6-155 184.6 155c10.2 8.5 25.3 7.2 33.8-2.9s7.2-25.3-2.9-33.8L280 276.8 280 56zM384 96a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm32 160a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm64-64a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM160 160a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM64 256a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM256 480a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"></path>
                                </svg>
                            </div>
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
                                     <tr data-vehicle-id="{{ $vehicle->id }}">
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
                                         <td class="editable-cell">
                                             <div class="view-val">
                                                 @if($vehicle->prices->dealer_cost)
                                                     ${{ number_format($vehicle->prices->dealer_cost) }} <i class="bi bi-pencil-square text-secondary ms-1 edit-trigger"></i>
                                                 @else
                                                     -- <i class="bi bi-pencil-square text-secondary ms-1 edit-trigger"></i>
                                                 @endif
                                             </div>
                                             <input type="number" class="form-control edit-input edit-cost" value="{{ (float)$vehicle->prices->dealer_cost }}" style="display:none; width: 100px;">
                                         </td>
                                         <td class="editable-cell">
                                             <div class="view-val">
                                                 @if($vehicle->prices->msrp)
                                                     ${{ number_format($vehicle->prices->msrp) }} <i class="bi bi-pencil-square text-secondary ms-1 edit-trigger"></i>
                                                 @else
                                                     -- <i class="bi bi-pencil-square text-secondary ms-1 edit-trigger"></i>
                                                 @endif
                                             </div>
                                             <input type="number" class="form-control edit-input edit-price" value="{{ (float)$vehicle->prices->msrp }}" style="display:none; width: 100px;">
                                         </td>
                                         <td class="editable-cell">
                                             <div class="view-val">
                                                 @if($vehicle->prices->sold_price)
                                                     ${{ number_format($vehicle->prices->sold_price) }} <i class="bi bi-pencil-square text-secondary ms-1 edit-trigger"></i>
                                                 @else
                                                     -- <i class="bi bi-pencil-square text-secondary ms-1 edit-trigger"></i>
                                                 @endif
                                             </div>
                                             <input type="number" class="form-control edit-input edit-sold-price" value="{{ (float)$vehicle->prices->sold_price }}" style="display:none; width: 100px;">
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

        /* ── Mobile Header & Nav (Exactly like screenshot) ──────────────── */
        @media (max-width: 992px) {
            .topbar-secondary {
                display: flex !important;
                flex-direction: column !important;
                background: linear-gradient(90deg, #4b0082, #8d1b3d) !important; /* Specific gradient */
                padding: 0 !important;
                border: none !important;
                margin: 0 !important;
            }
            .mobile-report-header {
                padding: 18px 20px !important;
                display: flex !important;
                align-items: center !important;
                gap: 15px !important;
            }
            .mobile-logo {
                width: 40px !important;
                height: 40px !important;
                border: 2px solid #ff9900 !important; /* Orange accent border from logo */
                border-radius: 50% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                color: #fff !important;
                font-size: 22px !important;
                background: transparent !important;
            }
            .mobile-title {
                color: #fff !important;
                font-size: 19px !important;
                font-weight: 600 !important;
                letter-spacing: 0.5px !important;
            }
            .nav-links {
                background: #fdfdfd !important;
                width: 100% !important;
                display: flex !important;
                overflow-x: auto !important;
                padding: 10px 20px !important;
                gap: 30px !important;
                border-bottom: 1px solid #eee !important;
                box-shadow: none !important;
            }
            .nav-item {
                background: transparent !important; /* Remove blocky background */
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                flex-direction: row !important;
                gap: 6px !important;
                color: #888 !important;
                font-size: 15px !important;
                padding: 8px 0 !important;
                font-weight: 500 !important;
                position: relative !important;
                opacity: 1 !important;
            }
            .nav-item i {
                font-size: 18px !important;
                color: #aaa !important;
            }
            .nav-item.active {
                color: #333 !important;
            }
            .nav-item.active i {
                color: #666 !important;
            }
            .nav-item.active::after {
                content: '' !important;
                position: absolute !important;
                bottom: -2px !important;
                left: 0 !important;
                width: 100% !important;
                height: 3px !important;
                background: #ccc !important; /* Grey underline as in SS */
            }
            .nav-item .badge {
                display: none !important; /* Remove badges on mobile as in SS */
            }
            .top-search {
                background: #fdfdfd !important;
                width: 100% !important;
                padding: 15px 20px !important;
                margin: 0 !important;
                border-bottom: 1px solid #eee !important;
            }
            .top-search input {
                width: 100% !important;
                background: #fff !important;
                border: 1px solid #ddd !important;
                border-radius: 8px !important;
                padding: 12px 15px 12px 45px !important;
                font-size: 15px !important;
                color: #333 !important;
            }
            .top-search .search-icon {
                left: 35px !important;
                color: #888 !important;
                font-size: 18px !important;
            }
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

        /* Custom Input Group Style */
        .input-group-custom {
            display: flex;
            align-items: stretch;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
        }
        .input-group-prefix {
            background: #f1f1f1;
            padding: 8px 15px;
            border-right: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #495057;
            font-size: 16px;
        }
        .input-group-custom .form-control, 
        .input-group-custom .form-control-wrapper {
            border: none;
            flex: 1;
            padding: 10px 12px;
            font-size: 15px;
            display: flex;
            align-items: center;
            background: #fff;
            color: #212529;
        }
        .input-group-custom .form-control:focus {
            box-shadow: none;
        }

        /* Date Range Picker Style */
        .date-range-picker-wrapper {
            min-width: 280px;
            cursor: pointer;
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
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 15px;
            min-width: 160px;
            background-color: #fff;
            cursor: pointer;
        }
        .btn-export {
            background: #d9534f;
            color: #fff;
            padding: 10px 25px;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
            width: 100%;
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
            padding: 12px 10px; /* Reduced padding */
            border-radius: 4px; /* Slightly sharper corners */
            text-align: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #f0f0f0;
            min-height: 90px; /* Smaller height */
        }
        .card-icon {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
        }
        .card-icon svg {
            width: 24px; /* Smaller icons */
            height: 24px;
        }
        .card-value {
            font-size: 16px; /* Smaller font */
            font-weight: 700;
            color: #212529;
            margin-bottom: 2px;
        }
        .card-label {
            font-size: 11px; /* Smaller label */
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
            padding: 10px 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            white-space: nowrap;
        }
        .report-table td {
            padding: 8px 15px;
            font-size: 13px;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
        }
        /* Zebra Striping (Color Combo) */
        .report-table tbody tr:nth-child(even) {
            background-color: #fcfcfc; /* Off/dark white */
        }
        .report-table tbody tr:nth-child(odd) {
            background-color: #ffffff; /* White */
        }
        .report-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        /* Inline Edit Styles */
        .editable-cell .view-val {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .edit-trigger {
            opacity: 0.3;
            transition: opacity 0.2s;
        }
        .editable-cell:hover .edit-trigger {
            opacity: 1;
        }
        tr.editing .view-val {
            display: none !important;
        }
        tr.editing .edit-input {
            display: block !important;
            padding: 4px 8px !important;
            height: auto !important;
            font-size: 13px !important;
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

        /* ── Responsive Styles ────────────────────────────────────────── */
        @media (max-width: 1200px) {
            .report-summary-cards {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .filters-form {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-item, .filter-item select, .filter-item input {
                width: 100% !important;
                min-width: 0 !important;
            }
            .date-picker-dropdown {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 400px;
                max-height: 90vh;
                overflow-y: auto;
                flex-direction: column;
            }
            .date-picker-sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #f1f1f1;
            }
            .date-picker-sidebar ul {
                display: flex;
                flex-wrap: wrap;
                padding: 5px;
            }
            .date-picker-sidebar li {
                padding: 8px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 768px) {
            .report-summary-cards {
                grid-template-columns: 1fr; /* Exact stack like screenshot */
            }
            .reports-container {
                padding: 10px;
            }
            .summary-card {
                padding: 30px 20px;
                border-radius: 0; /* Flat look like screenshot */
                box-shadow: none;
                border-bottom: 1px solid #eee;
                margin-bottom: 0;
            }
            .card-value {
                font-size: 24px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure mobile header is present
            const topbar = document.querySelector('.topbar-secondary');
            if (topbar && !document.querySelector('.mobile-report-header')) {
                const mobileHeader = document.createElement('div');
                mobileHeader.className = 'mobile-report-header d-md-none';
                mobileHeader.innerHTML = `
                    <div class="mobile-logo">
                        <i class="bi bi-speedometer"></i>
                    </div>
                    <div class="mobile-title">Mobile Inventory Management</div>
                `;
                topbar.prepend(mobileHeader);
            }

            const dateRangeWrapper = document.querySelector('.date-range-picker-wrapper');
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

                        // Auto-submit on calendar range selection
                        form.submit();
                    }
                }
            });

            // Toggle dropdown
            dateRangeWrapper.addEventListener('click', function(e) {
                // If the click is on the sidebar or calendar, don't toggle (close)
                if (datePickerDropdown.contains(e.target)) return;
                
                e.stopPropagation();
                datePickerDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!datePickerDropdown.contains(e.target) && !dateRangeWrapper.contains(e.target)) {
                    datePickerDropdown.classList.remove('show');
                }
            });

            function updateDateRange(from, to, display) {
                fromInput.value = from;
                toInput.value = to;
                dateRangeText.innerText = display;
                rangeInput.value = display;
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

            if (makeSelect) {
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
            }

            // Auto-submit on select change
            document.querySelectorAll('#reportsFilterForm select').forEach(select => {
                select.addEventListener('change', function() {
                    if (this.id !== 'makeSelect') {
                        form.submit();
                    }
                });
            });

            // Handle search input submit on Enter and reset when cleared
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        form.submit();
                    }
                });

                // Reset when text is manually cleared and user clicks away or presses enter
                searchInput.addEventListener('input', function() {
                    if (this.value === '') {
                        // Optional: auto-submit when cleared
                        // form.submit();
                    }
                });
            }

            // Inline Editing Logic
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('edit-trigger')) {
                    const tr = e.target.closest('tr');
                    tr.classList.add('editing');
                    tr.querySelector('.edit-cost').focus();
                }
            });

            // Save on Enter
            document.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.classList.contains('edit-input')) {
                    const tr = e.target.closest('tr');
                    saveVehiclePrices(tr);
                }
            });

            // Also save on blur? Maybe just on Enter for now to avoid accidental saves
            // document.addEventListener('focusout', function(e) {
            //     if (e.target.classList.contains('edit-input')) {
            //         const tr = e.target.closest('tr');
            //         // setTimeout to allow clicking other inputs in same row
            //         setTimeout(() => {
            //             if (!tr.contains(document.activeElement)) {
            //                 saveVehiclePrices(tr);
            //             }
            //         }, 100);
            //     }
            // });

            function saveVehiclePrices(tr) {
                const vehicleId = tr.getAttribute('data-vehicle-id');
                const cost = tr.querySelector('.edit-cost').value;
                const price = tr.querySelector('.edit-price').value;
                const soldPrice = tr.querySelector('.edit-sold-price').value;

                tr.classList.remove('editing');
                tr.style.opacity = '0.5';

                fetch(`/dealer/inventory/reports/${vehicleId}/price`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        dealer_cost: cost,
                        msrp: price,
                        sold_price: soldPrice
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh to update calculations or update DOM manually
                        window.location.reload(); 
                    } else {
                        alert('Error saving prices');
                        tr.style.opacity = '1';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving prices');
                    tr.style.opacity = '1';
                });
            }
        });
    </script>
@endsection
