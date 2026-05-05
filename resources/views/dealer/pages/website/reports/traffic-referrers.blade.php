@extends('layouts.dealer.app')

@section('title', __('Traffic Referrers Report') . ' | ' . __(config('app.name')))

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

        .traffic-table {
            width: 100%;
            border-collapse: collapse;
        }

        .traffic-table th {
            text-align: left;
            padding: 12px 15px;
            font-size: 11px;
            font-weight: 600;
            color: #888;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
            text-transform: uppercase;
        }

        .traffic-table td {
            padding: 15px;
            font-size: 13px;
            color: #444;
            border-bottom: 1px solid #f8f8f8;
        }

        .traffic-table tr:hover {
            background: #f9f9f9;
        }

        .referrer-value {
            font-weight: 600;
            color: #333;
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
                    <form action="{{ route('dealer.website.reports.traffic-referrers') }}" method="GET" id="reportFilterForm">
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
                    <h3 class="report-table-title">{{ __('Traffic Referrers') }}</h3>
                    
                    <table class="traffic-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">{{ __('Value') }}</th>
                                <th style="width: 10%;">{{ __('Visits') }}</th>
                                <th style="width: 12%;">{{ __('Engaged Visits') }}</th>
                                <th style="width: 10%;">{{ __('Visitors') }}</th>
                                <th style="width: 12%;">{{ __('Average Time') }}</th>
                                <th style="width: 12%;">{{ __('Avg. Pageviews') }}</th>
                                <th style="width: 8%;">{{ __('Leads') }}</th>
                                <th style="width: 11%;">{{ __('% Leads') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats as $s)
                                <tr>
                                    <td class="referrer-value">{{ $s->value }}</td>
                                    <td>{{ number_format($s->visits) }}</td>
                                    <td>{{ number_format($s->engaged_visits) }}</td>
                                    <td>{{ number_format($s->visitors) }}</td>
                                    <td>{{ $s->avg_time }}</td>
                                    <td>{{ $s->avg_pageviews }}</td>
                                    <td>{{ number_format($s->leads) }}</td>
                                    <td>{{ $s->pct_leads }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
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
