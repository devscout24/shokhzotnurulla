@extends('layouts.admin.app')

@section('title', __('Dashboard') . ' | '. __(config('app.name')))

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Dashboard</h2>
            <div class="date-picker-box">
                <form id="dateFilterForm" action="{{ route('admin.dashboard') }}" method="GET">
                    <i class="bi bi-calendar3"></i>
                    <input type="text" name="range" id="dateRange" readonly style="border:none; outline:none; cursor:pointer; font-size: 14px; width: 180px;">
                    <input type="hidden" name="from" id="fromInput" value="{{ $from }}">
                    <input type="hidden" name="to" id="toInput" value="{{ $to }}">
                </form>
            </div>
        </div>

        <hr>

        <div class="view-content" data-view="dashboard">
            @include('dealer.components.dashboard.dashboard-stats')

            {{-- Conversion Stats Row --}}
            <div class="conversion-row">
                <div class="conv-item">
                    <i class="bi bi-funnel"></i>
                    Base Conversion: <span class="fw-bold">{{ $baseConversion }}%</span>
                </div>
                <div class="conv-item">
                    <i class="bi bi-telephone"></i>
                    With Click to Call: <span class="fw-bold">{{ $withClickToCall }}%</span>
                </div>
                <div class="conv-item">
                    <i class="bi bi-clock"></i>
                    Average Session: <span class="fw-bold">{{ $avgSession }}</span>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-xl-9">
                    @include('dealer.components.dashboard.website-activity-graph')
                </div>
                <div class="col-xl-3">
                    @include('dealer.components.dashboard.popular-search')
                </div>
            </div>
        </div>
    </main>
@endsection

@push('page-styles')
<style>
    .conversion-row {
        display: flex;
        gap: 30px;
        margin: 15px 0 25px;
        padding-left: 10px;
    }
    .conv-item {
        font-size: 13px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .conv-item i {
        color: #999;
        font-size: 14px;
    }
    .date-picker-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 5px 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    }
</style>
@endpush

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.flatpickr) {
        flatpickr('#dateRange', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            defaultDate: ["{{ $from }}", "{{ $to }}"],
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const from = instance.formatDate(selectedDates[0], "Y-m-d");
                    const to = instance.formatDate(selectedDates[1], "Y-m-d");
                    document.getElementById('fromInput').value = from;
                    document.getElementById('toInput').value = to;
                    document.getElementById('dateFilterForm').submit();
                }
            },
            onReady: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const fromDisplay = instance.formatDate(selectedDates[0], "n/j/Y");
                    const toDisplay = instance.formatDate(selectedDates[1], "n/j/Y");
                    instance.input.value = fromDisplay + " - " + toDisplay;
                }
            }
        });
    }
});
</script>
@endpush
