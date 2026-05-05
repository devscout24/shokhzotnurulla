@extends('layouts.dealer.app')

@section('title', $title . ' | ' . __(config('app.name')))

@push('page-styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .rc-wrapper {
            display: flex;
            gap: 40px;
            min-height: calc(100vh - 160px);
            padding: 20px 0;
            align-items: flex-start;
        }

        .rc-sidebar {
            width: 280px;
            min-width: 280px;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .rc-sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            border-bottom: 1px solid #f8f9fa;
        }

        .rc-sidebar-item:last-child {
            border-bottom: none;
        }

        .rc-sidebar-item:hover {
            background: #fdfdfd;
            color: #333;
        }

        .rc-sidebar-item.active {
            background: #fff;
            color: #ce4f4b;
            font-weight: 600;
        }

        .rc-sidebar-item i {
            font-size: 16px !important;
            width: 24px;
            text-align: center;
            color: #999;
        }

        .rc-sidebar-item.active i {
            color: #ce4f4b !important;
        }

        .rc-main-container {
            flex: 1;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 25px;
        }

        /* Top Bar */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .report-controls {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .date-picker-wrapper {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 14px;
            color: #444;
            min-width: 220px;
        }

        .btn-export {
            background: #ce4f4b;
            color: #fff !important;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background .2s;
        }

        .btn-export:hover {
            background: #b33f3b;
        }

        /* Table Styles */
        .report-table-title {
            font-size: 16px;
            font-weight: 600;
            color: #444;
            margin-bottom: 20px;
        }

        .analytics-table {
            width: 100%;
            border-collapse: collapse;
        }

        .analytics-table th {
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            font-weight: 600;
            color: #888;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .analytics-table td {
            padding: 12px 15px;
            font-size: 14px;
            color: #444;
            border-bottom: 1px solid #f8f8f8;
            vertical-align: middle;
        }

        .analytics-table tr:hover {
            background: #f9f9f9;
        }

        .value-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            color: #333;
        }

        .value-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #666;
        }

        .flag-icon {
            width: 20px;
            height: 14px;
            object-fit: cover;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .pct-cell {
            width: 150px;
        }

        .progress-container {
            width: 100%;
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 4px;
        }

        .progress-bar {
            height: 100%;
            background: #ce4f4b;
            border-radius: 4px;
        }
    </style>
@endpush

@section('page-content')
    <main class="main-content" id="mainContent" style="padding:0; background: #f0f2f5; min-height: 100vh;">
        <div style="padding: 30px 45px;">
            <div class="report-header">
                <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222;">
                    {{ __('Website Reports') }}</h2>
                
                <div class="report-controls">
                    <form action="{{ url()->current() }}" method="GET" id="reportFilterForm">
                        <div class="date-picker-wrapper" id="datePickerTrigger">
                            <i class="bi bi-calendar3"></i>
                            <span id="dateRangeDisplay">{{ date('n/j/Y', strtotime($from)) }} - {{ date('n/j/Y', strtotime($to)) }}</span>
                            <input type="hidden" name="from" id="fromDate" value="{{ $from }}">
                            <input type="hidden" name="to" id="toDate" value="{{ $to }}">
                        </div>
                    </form>

                    <button class="btn-export">
                        <i class="bi bi-cloud-download"></i> {{ __('Export') }}
                    </button>
                </div>
            </div>

            <div class="rc-wrapper">
                @include('dealer.partials.reports-sidebar')

                <div class="rc-main-container">
                    <h3 class="report-table-title">{{ $title }}</h3>
                    
                    <table class="analytics-table">
                        <thead>
                            <tr>
                                <th style="width: 60%;">{{ __('Value') }}</th>
                                <th style="width: 20%;">{{ __('Page Views') }}</th>
                                <th style="width: 20%;">{{ __('%') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats as $item)
                                <tr>
                                    <td>
                                        <div class="value-cell">
                                            @if($type === 'devices')
                                                <div class="value-icon">
                                                    @if(Str::contains(strtolower($item->value), 'apple'))
                                                        <i class="bi bi-apple"></i>
                                                    @elseif(Str::contains(strtolower($item->value), 'samsung'))
                                                        <i class="bi bi-phone"></i>
                                                    @elseif(Str::contains(strtolower($item->value), 'android'))
                                                        <i class="bi bi-android"></i>
                                                    @else
                                                        <i class="bi bi-laptop"></i>
                                                    @endif
                                                </div>
                                            @elseif($type === 'countries')
                                                @php
                                                    // Map country names to codes for flags if possible, 
                                                    // or just use a placeholder if not found.
                                                    // This is a simplified version.
                                                    $countryCode = 'unknown';
                                                    $countries = [
                                                        'United States' => 'us', 'Singapore' => 'sg', 'United Kingdom' => 'gb',
                                                        'Sweden' => 'se', 'France' => 'fr', 'Spain' => 'es', 'Japan' => 'jp',
                                                        'Germany' => 'de', 'Australia' => 'au', 'Canada' => 'ca'
                                                    ];
                                                    $code = $countries[$item->value] ?? null;
                                                @endphp
                                                @if($code)
                                                    <img src="https://flagcdn.com/w40/{{ $code }}.png" class="flag-icon" alt="{{ $item->value }}">
                                                @else
                                                    <div class="value-icon"><i class="bi bi-question-circle"></i></div>
                                                @endif
                                            @else
                                                <div class="value-icon"><i class="bi bi-geo-alt"></i></div>
                                            @endif
                                            <span>{{ $item->value ?: 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->page_views) }}</td>
                                    <td class="pct-cell">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span>{{ number_format($item->pct, 2) }}%</span>
                                        </div>
                                        <div class="progress-container">
                                            <div class="progress-bar" style="width: {{ $item->pct }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 40px; color: #999;">
                                        {{ __('No data available for the selected period.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fromInput = document.getElementById('fromDate');
            const toInput = document.getElementById('toDate');
            const filterForm = document.getElementById('reportFilterForm');

            flatpickr("#datePickerTrigger", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [fromInput.value, toInput.value],
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        fromInput.value = instance.formatDate(selectedDates[0], "Y-m-d");
                        toInput.value = instance.formatDate(selectedDates[1], "Y-m-d");
                        filterForm.submit();
                    }
                }
            });
        });
    </script>
@endpush
