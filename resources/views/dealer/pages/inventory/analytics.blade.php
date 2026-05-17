@extends('layouts.dealer.app')
@section('title', __('Vehicle Analytics') . ' | ' . __(config('app.name')))

@push('page-assets')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    @vite([
        'resources/css/dealer/pages/inventory-details.css',
        'resources/js/dealer/pages/inventory-details.js',
    ])
    <style>
        .vd-sv-main {
            padding: 24px;
            background: #fdfdfd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            flex: 1;
        }
        .vd-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid #eee;
            padding-bottom: 16px;
        }
        .vd-header-title {
            font-size: 20px;
            font-weight: 700;
            color: #222;
        }
        .vd-header-title span {
            font-size: 14px;
            color: #777;
            font-weight: 400;
            margin-left: 8px;
        }
        .vd-header-actions {
            display: flex;
            gap: 10px;
        }
        .vd-btn-view-listing {
            background: #fff;
            border: 1px solid #ccc;
            color: #333;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .vd-btn-view-listing:hover {
            background: #f5f5f5;
        }
        .vd-btn-remove-listing {
            background: #fff;
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .vd-btn-remove-listing:hover {
            background: #dc3545;
            color: #fff;
        }
        .vd-analytics-stat-card {
            border: 1px solid #eaeaea;
            box-shadow: 0 2px 6px rgba(0,0,0,0.01);
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            flex: 1;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.2s ease;
        }
        .vd-analytics-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.04);
        }
        .vd-analytics-stat-icon {
            font-size: 24px;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .vd-analytics-stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
            margin-bottom: 2px;
        }
        .vd-analytics-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #222;
        }
        .vd-analytics-card {
            background: #fff;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 24px;
            margin-top: 24px;
        }
        .vd-analytics-card-title {
            font-size: 15px;
            font-weight: 600;
            color: #333;
        }
        .vd-period-btn {
            background: #f5f5f5;
            border: 1px solid #ddd;
            color: #666;
            padding: 4px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .vd-period-btn:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        .vd-period-btn:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .vd-period-btn.active {
            background: #fff;
            border-color: #5b2d8e;
            color: #5b2d8e;
            font-weight: 600;
        }
        .vd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .vd-table th {
            text-align: left;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            border-bottom: 1px solid #eee;
            background: #f9f9f9;
        }
        .vd-table td {
            padding: 12px;
            font-size: 13px;
            color: #444;
            border-bottom: 1px solid #f5f5f5;
        }
        .vd-table tr:hover td {
            background: #fcfcfc;
        }
        .vd-change-negative {
            color: #dc3545;
            font-weight: 500;
        }
        .vd-change-positive {
            color: #28a745;
            font-weight: 500;
        }
        .vd-sync-notice {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .vd-left .vd-nav-item[data-nav="analytics"] {
            background: rgba(220, 53, 69, 0.05) !important;
            color: #dc3545 !important;
            font-weight: 600;
        }
        .vd-left .vd-nav-item[data-nav="analytics"] i {
            color: #dc3545 !important;
        }
    </style>
@endpush

@php
    $vehicleImgSrc = $vehicle->primaryPhoto
        ? $vehicle->primaryPhoto->url
        : 'https://placehold.co/400x250/e8e8e8/999?text=' . urlencode($vehicle->year . ' ' . $vehicle->makeModel->name);

    $vehicleTitle = strtoupper(
        $vehicle->year . ' ' .
        $vehicle->make->name . ' ' .
        $vehicle->makeModel->name .
        ($vehicle->trim ? ' ' . $vehicle->trim : '')
    );

    // Compute Price Change Calculations
    $totalChangeAmount = 0;
    $totalPercentChange = 0;
    $computedHistory = [];
    $sortedHistory = $priceHistory->sortBy('created_at');

    foreach ($sortedHistory as $history) {
        $oldPrice = $history->old_price;
        $newPrice = $history->new_price;
        
        if ($oldPrice > 0) {
            $change = $newPrice - $oldPrice;
            $percent = ($change / $oldPrice) * 100;
        } else {
            $change = 0;
            $percent = 0;
        }
        
        $computedHistory[] = [
            'date' => \Carbon\Carbon::parse($history->created_at)->format('n/j/Y'),
            'days_ago' => \Carbon\Carbon::parse($history->created_at)->diffInDays(now()),
            'new_price' => $newPrice,
            'change' => $change,
            'percent' => $percent,
        ];
    }

    if ($sortedHistory->isNotEmpty()) {
        $firstEntry = $sortedHistory->first();
        $lastEntry = $sortedHistory->last();
        $initialPrice = $firstEntry->old_price ?: $firstEntry->new_price;
        $latestPrice = $lastEntry->new_price;
        
        if ($initialPrice > 0) {
            $totalChangeAmount = $latestPrice - $initialPrice;
            $totalPercentChange = ($totalChangeAmount / $initialPrice) * 100;
        }
    }
@endphp

@section('page-content')
<main class="main-content" id="mainContent" style="padding:0;overflow:hidden;">
    <div class="view-content inventory-view" data-view="inventory" style="padding:0;">

        @include('dealer.partials.inventory-topbar')

        <div class="subview vd-page">
            {{-- ═══════════════════
                 TOP HEADER
            ═══════════════════ --}}
            <div class="vd-header" style="padding: 16px 24px; background: #fff; margin-bottom: 0;">
                <div class="vd-header-title">
                    {{ $vehicleTitle }}
                    <span>({{ $vehicle->stock_number }})</span>
                </div>
                <div class="vd-header-actions">
                    <button type="button" id="view-listing-btn" class="vd-btn-view-listing" data-listing-url="{{ route('frontend.inventory.show', $vehicle->slug) }}">
                        <i class="bi bi-box-arrow-up-right"></i> View Listing
                    </button>
                    <button type="button" class="vd-btn-remove-listing" id="btnRemoveListing" data-url-destroy="{{ route('dealer.inventory.vdp.destroy', $vehicle) }}">
                        <i class="bi bi-trash3"></i> Remove Listing
                    </button>
                </div>
            </div>

            {{-- ═══════════════════
                 BODY
            ═══════════════════ --}}
            <div class="vd-body" style="display: flex; gap: 0;">
                
                {{-- LEFT PANEL --}}
                @include('dealer.partials.inventory-details-sidebar')

                {{-- MAIN CONTENT --}}
                <div class="vd-sv-main">
                    
                    {{-- Warning message when GA4 is not configured --}}
                    @if(!$isConfigured)
                        <div class="vd-sync-notice">
                            <i class="bi bi-exclamation-triangle-fill" style="font-size: 16px;"></i>
                            <span>Google Analytics is not configured for this account. Please add your Measurement ID, Property ID, and Service Account JSON in your Dashboard settings to view live metrics. Showing database estimates instead.</span>
                        </div>
                    @endif

                    {{-- Metrics Row --}}
                    <div class="vd-analytics-stats-row" style="display: flex; gap: 20px;">
                        <div class="vd-analytics-stat-card">
                            <div class="vd-analytics-stat-icon">
                                <i class="bi bi-eye"></i>
                            </div>
                            <div>
                                <div class="vd-analytics-stat-label">Total Views</div>
                                <div class="vd-analytics-stat-value">
                                    {{ $ga4Data ? $ga4Data['views'] : $vehicle->total_views }}
                                </div>
                            </div>
                        </div>

                        <div class="vd-analytics-stat-card">
                            <div class="vd-analytics-stat-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <div class="vd-analytics-stat-label">Days on Lot</div>
                                <div class="vd-analytics-stat-value">
                                    {{ $vehicle->days_on_lot }}
                                </div>
                            </div>
                        </div>

                        <div class="vd-analytics-stat-card">
                            <div class="vd-analytics-stat-icon">
                                <i class="bi bi-funnel"></i>
                            </div>
                            <div>
                                <div class="vd-analytics-stat-label">Leads</div>
                                <div class="vd-analytics-stat-value">
                                    {{ $ga4Data ? $ga4Data['leads'] : $vehicle->total_leads }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Trend Line Chart --}}
                    <div class="vd-analytics-card">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                            <div class="vd-analytics-card-title" style="margin-bottom:0;">Views</div>
                            <div class="vd-analytics-period-btns">
                                <a href="?days=7" class="vd-period-btn {{ $days === 7 ? 'active' : '' }}">7 days</a>
                                <a href="?days=30" class="vd-period-btn {{ $days === 30 ? 'active' : '' }}">30 days</a>
                                <a href="?days=90" class="vd-period-btn {{ $days === 90 ? 'active' : '' }}">90 days</a>
                            </div>
                        </div>
                        <div class="vd-chart-wrap" style="position: relative; height: 300px; width: 100%;">
                            <canvas id="analyticsChart"></canvas>
                        </div>
                    </div>

                    {{-- Price Change History log --}}
                    <div class="vd-analytics-card" style="margin-bottom: 24px;">
                        <div class="vd-analytics-card-title">
                            Price Change History 
                            @if($totalChangeAmount != 0)
                                <span class="{{ $totalChangeAmount < 0 ? 'vd-change-negative' : 'vd-change-positive' }}">
                                    ({{ ($totalPercentChange >= 0 ? '+' : '') . number_format($totalPercentChange, 1) }}%) 
                                    {{ ($totalChangeAmount >= 0 ? '+$' : '-$') . number_format(abs($totalChangeAmount)) }}
                                </span>
                            @endif
                        </div>
                        <table class="vd-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Days ago</th>
                                    <th>New price</th>
                                    <th>$ change</th>
                                    <th>% change</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(array_reverse($computedHistory) as $item)
                                    <tr>
                                        <td>{{ $item['date'] }}</td>
                                        <td>{{ $item['days_ago'] }}</td>
                                        <td>${{ number_format($item['new_price']) }}</td>
                                        <td class="{{ $item['change'] < 0 ? 'vd-change-negative' : ($item['change'] > 0 ? 'vd-change-positive' : '') }}">
                                            @if($item['change'] == 0)
                                                --
                                            @else
                                                {{ ($item['change'] < 0 ? '-$' : '+$') . number_format(abs($item['change'])) }}
                                            @endif
                                        </td>
                                        <td class="{{ $item['change'] < 0 ? 'vd-change-negative' : ($item['change'] > 0 ? 'vd-change-positive' : '') }}">
                                            @if($item['change'] == 0)
                                                --
                                            @else
                                                {{ ($item['percent'] < 0 ? '' : '+') . number_format($item['percent'], 1) }}%
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #888; padding: 24px;">
                                            No price changes logged yet.
                                        </td>
                                    </tr>
                                @endforelse
                                
                                @if(count($computedHistory) > 0)
                                    <tr style="font-weight: bold; border-top: 2px solid #eaeaea;">
                                        <td colspan="3">Total</td>
                                        <td class="{{ $totalChangeAmount < 0 ? 'vd-change-negative' : ($totalChangeAmount > 0 ? 'vd-change-positive' : '') }}">
                                            {{ ($totalChangeAmount < 0 ? '-$' : '+$') . number_format(abs($totalChangeAmount)) }}
                                        </td>
                                        <td class="{{ $totalChangeAmount < 0 ? 'vd-change-negative' : ($totalChangeAmount > 0 ? 'vd-change-positive' : '') }}">
                                            {{ ($totalPercentChange >= 0 ? '+' : '') . number_format($totalPercentChange, 1) }}%
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>{{-- end vd-sv-main --}}
            </div>{{-- end vd-body --}}
        </div>{{-- end vd-page --}}
    </div>{{-- end inventory-view --}}
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Line chart Initialization
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const chartData = @json($chartData);
        
        const labels = chartData.map(item => item.date);
        const data = chartData.map(item => item.views);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Views',
                    data: data,
                    borderColor: '#5b2d8e',
                    backgroundColor: 'rgba(91, 45, 142, 0.05)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#5b2d8e',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // Remove Listing btn implementation
        const btnRemoveListing = document.getElementById('btnRemoveListing');
        if (btnRemoveListing) {
            btnRemoveListing.addEventListener('click', function () {
                const url = this.getAttribute('data-url-destroy');
                if (!confirm('Are you sure you want to remove this listing? This action cannot be undone.')) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            });
        }
    });
</script>
@endsection
