{{-- Traffic Channel Statistics Table --}}
<div class="website-activity-container mt-4 p-4 bg-white rounded shadow-sm">
    <div class="activity-header mb-3">
        <div class="activity-title h5 mb-0 text-dark fw-bold">Traffic Channels</div>
    </div>

    @php
        // Calculate totals for percent calculations
        $totalVisits = array_sum(array_column($trafficData['summary'], 'visits'));
        $totalForms = array_sum(array_column($trafficData['summary'], 'forms'));
        $totalCalls = array_sum(array_column($trafficData['summary'], 'calls'));
    @endphp

    <div class="table-responsive">
        <table class="table custom-analytics-table align-middle">
            <thead>
                <tr>
                    <th class="align-middle text-start text-muted py-3"
                        style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Channel</th>

                    <th class="align-middle text-end py-3" style="width: 140px;">
                        <div class="header-metric-toggle-group">
                            <span class="metric-title d-block mb-1">Visits</span>
                            <div class="btn-group btn-group-sm rounded p-0 bg-light border overflow-hidden"
                                role="group">
                                <button type="button" class="btn btn-metric-toggle active py-0 px-2 btn-visits-mode"
                                    data-mode="count">#</button>
                                <button type="button" class="btn btn-metric-toggle py-0 px-2 btn-visits-mode"
                                    data-mode="percent">%</button>
                            </div>
                        </div>
                    </th>
                    <th class="align-middle text-end text-muted py-3"
                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; width: 100px;">Change</th>

                    <th class="align-middle text-end py-3" style="width: 140px;">
                        <div class="header-metric-toggle-group">
                            <span class="metric-title d-block mb-1">Forms</span>
                            <div class="btn-group btn-group-sm rounded p-0 bg-light border overflow-hidden"
                                role="group">
                                <button type="button" class="btn btn-metric-toggle active py-0 px-2 btn-forms-mode"
                                    data-mode="count">#</button>
                                <button type="button" class="btn btn-metric-toggle py-0 px-2 btn-forms-mode"
                                    data-mode="percent">%</button>
                            </div>
                        </div>
                    </th>
                    <th class="align-middle text-end text-muted py-3"
                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; width: 100px;">Change</th>

                    <th class="align-middle text-end py-3" style="width: 140px;">
                        <div class="header-metric-toggle-group">
                            <span class="metric-title d-block mb-1">Calls</span>
                            <div class="btn-group btn-group-sm rounded p-0 bg-light border overflow-hidden"
                                role="group">
                                <button type="button" class="btn btn-metric-toggle active py-0 px-2 btn-calls-mode"
                                    data-mode="count">#</button>
                                <button type="button" class="btn btn-metric-toggle py-0 px-2 btn-calls-mode"
                                    data-mode="percent">%</button>
                            </div>
                        </div>
                    </th>
                    <th class="align-middle text-end text-muted py-3"
                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; width: 100px;">Change</th>

                    <th class="align-middle text-end text-muted py-3"
                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; width: 120px;">Conversion
                    </th>
                    <th class="align-middle text-end text-muted py-3"
                        style="font-size: 11px; font-weight: 600; text-transform: uppercase; width: 130px;">Avg. Session
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trafficData['summary'] as $channel => $stats)
                    @php
                        $chSlug = strtolower(str_replace(' ', '-', $channel));

                        // Conversion calculation: (Forms + Calls) / Visits
                        $conversion = 0.0;
                        if ($stats['visits'] > 0) {
                            $conversion = (($stats['forms'] + $stats['calls']) / $stats['visits']) * 100;
                        }
                    @endphp
                    <tr>
                        <td class="text-start py-3">
                            <div class="d-flex align-items-center gap-3">
                                <label class="custom-checkbox-container">
                                    <input type="checkbox" checked class="channel-toggle-checkbox"
                                        data-channel="{{ $channel }}">
                                    <span class="custom-checkmark {{ $chSlug }}"></span>
                                </label>
                                <span class="channel-name fw-semibold text-dark">{{ $channel }}</span>
                            </div>
                        </td>

                        {{-- Visits --}}
                        <td class="text-end py-3 fw-semibold text-dark visits-cell" data-count="{{ $stats['visits'] }}"
                            data-percent="{{ $totalVisits > 0 ? number_format(($stats['visits'] / $totalVisits) * 100, 1) : 0 }}%">
                            {{ number_format($stats['visits']) }}
                        </td>
                        <td
                            class="text-end py-3 fw-bold {{ ($stats['visits_change'] ?? 0) > 0 ? 'text-success-custom' : (($stats['visits_change'] ?? 0) < 0 ? 'text-danger-custom' : 'text-muted-custom') }}">
                            @if (isset($stats['visits_change']) && $stats['visits_change'] !== 0 && $stats['visits'] > 5)
                                {{ $stats['visits_change'] > 0 ? '' : '' }}{{ $stats['visits_change'] }}%
                            @endif
                        </td>

                        {{-- Forms --}}
                        <td class="text-end py-3 fw-semibold text-dark forms-cell" data-count="{{ $stats['forms'] }}"
                            data-percent="{{ $totalForms > 0 ? number_format(($stats['forms'] / $totalForms) * 100, 1) : 0 }}%">
                            {{ number_format($stats['forms']) }}
                        </td>
                        <td
                            class="text-end py-3 fw-bold {{ ($stats['forms_change'] ?? 0) > 0 ? 'text-success-custom' : (($stats['forms_change'] ?? 0) < 0 ? 'text-danger-custom' : 'text-muted-custom') }}">
                            @if (isset($stats['forms_change']) && $stats['forms_change'] !== 0 && $stats['visits'] > 5)
                                {{ $stats['forms_change'] > 0 ? '' : '' }}{{ $stats['forms_change'] }}%
                            @endif
                        </td>

                        {{-- Calls --}}
                        <td class="text-end py-3 fw-semibold text-dark calls-cell" data-count="{{ $stats['calls'] }}"
                            data-percent="{{ $totalCalls > 0 ? number_format(($stats['calls'] / $totalCalls) * 100, 1) : 0 }}%">
                            {{ number_format($stats['calls']) }}
                        </td>
                        <td
                            class="text-end py-3 fw-bold {{ ($stats['calls_change'] ?? 0) > 0 ? 'text-success-custom' : (($stats['calls_change'] ?? 0) < 0 ? 'text-danger-custom' : 'text-muted-custom') }}">
                            @if (isset($stats['calls_change']) && $stats['calls_change'] !== 0 && $stats['visits'] > 5)
                                {{ $stats['calls_change'] > 0 ? '' : '' }}{{ $stats['calls_change'] }}%
                            @endif
                        </td>

                        {{-- Conversion & Avg Session --}}
                        <td class="text-end py-3 fw-semibold text-dark">
                            @if ($stats['visits'] === 0)
                                -
                            @elseif($conversion < 1.0)
                                &lt;1%
                            @else
                                {{ number_format($conversion, 1) }}%
                            @endif
                        </td>
                        <td class="text-end py-3 text-dark fw-medium">{{ $stats['avg_session'] ?? '0m 00s' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .custom-analytics-table {
        margin-top: 10px;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .custom-analytics-table thead th {
        border-bottom: 2px solid #f0f0f0 !important;
        border-top: none !important;
        background-color: #fff;
    }

    .custom-analytics-table tbody tr {
        background-color: #fff;
        transition: background-color 0.15s ease;
    }

    .custom-analytics-table tbody tr:hover {
        background-color: #fafbfc;
    }

    .custom-analytics-table tbody td {
        padding: 12px 15px;
        font-size: 13px;
        border-bottom: 1px solid #f0f0f0 !important;
        color: #444;
    }

    /* Custom Checkbox Design matching SS */
    .custom-checkbox-container {
        display: block;
        position: relative;
        width: 16px;
        height: 16px;
        cursor: pointer;
        user-select: none;
    }

    .custom-checkbox-container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .custom-checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 16px;
        width: 16px;
        background-color: #fff;
        border: 1.5px solid #d2d6dc;
        border-radius: 4px;
        transition: all 0.2s ease-in-out;
    }

    /* Checked Theme Colors */
    .custom-checkbox-container input:checked~.custom-checkmark.direct {
        background-color: #e05e4d;
        border-color: #e05e4d;
    }

    .custom-checkbox-container input:checked~.custom-checkmark.google-business-profile {
        background-color: #1b6ce6;
        border-color: #1b6ce6;
    }

    .custom-checkbox-container input:checked~.custom-checkmark.referral {
        background-color: #f7943c;
        border-color: #f7943c;
    }

    .custom-checkbox-container input:checked~.custom-checkmark.organic-search {
        background-color: #8e44ad;
        border-color: #8e44ad;
    }

    .custom-checkbox-container input:checked~.custom-checkmark.paid-social {
        background-color: #f89db2;
        border-color: #f89db2;
    }

    .custom-checkbox-container input:checked~.custom-checkmark.social {
        background-color: #3498db;
        border-color: #3498db;
    }

    .custom-checkbox-container input:checked~.custom-checkmark.display {
        background-color: #9bcdf7;
        border-color: #9bcdf7;
    }

    .custom-checkbox-container input:checked~.custom-checkmark.email {
        background-color: #d1abf6;
        border-color: #d1abf6;
    }

    /* Checkmark indicators */
    .custom-checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .custom-checkbox-container input:checked~.custom-checkmark:after {
        display: block;
    }

    .custom-checkbox-container .custom-checkmark:after {
        left: 4px;
        top: 1px;
        width: 5px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* Change columns custom text colors */
    .text-success-custom {
        color: #2ecc71 !important;
    }

    .text-danger-custom {
        color: #e74c3c !important;
    }

    .text-muted-custom {
        color: #999 !important;
    }

    /* Toggle button styles */
    .header-metric-toggle-group {
        display: inline-flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .header-metric-toggle-group .metric-title {
        font-size: 11px;
        text-transform: uppercase;
        color: #999;
        font-weight: 600;
    }

    .btn-metric-toggle {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 9px;
        border: none;
        background: transparent;
        color: #000000;
        transition: all 0.15s ease-in-out;
        line-height: 1.2;
    }

    .btn-metric-toggle.active {
        background: purple !important;
        color: #fff !important;
        border-radius: 4px;
    }

    .btn-metric-toggle:focus {
        box-shadow: none !important;
        outline: none !important;
    }
</style>

@push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Connect checkboxes to toggle line visibility in Chart.js
            const checkboxes = document.querySelectorAll('.channel-toggle-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const channelName = this.getAttribute('data-channel');
                    if (window.trafficChartInstance) {
                        const chart = window.trafficChartInstance;
                        const datasetIndex = chart.data.datasets.findIndex(ds => ds.label ===
                            channelName);
                        if (datasetIndex !== -1) {
                            chart.setDatasetVisibility(datasetIndex, this.checked);
                            chart.update();
                        }
                    }
                });
            });

            // 2. Visits Dual Mode toggles (# vs %)
            const visitsToggleBtns = document.querySelectorAll('.btn-visits-mode');
            visitsToggleBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    visitsToggleBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const mode = this.getAttribute('data-mode');
                    const cells = document.querySelectorAll('.visits-cell');
                    cells.forEach(cell => {
                        const count = cell.getAttribute('data-count');
                        const percent = cell.getAttribute('data-percent');
                        cell.textContent = (mode === 'count') ? Number(count)
                            .toLocaleString() : percent;
                    });
                });
            });

            // 3. Forms Dual Mode toggles (# vs %)
            const formsToggleBtns = document.querySelectorAll('.btn-forms-mode');
            formsToggleBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    formsToggleBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const mode = this.getAttribute('data-mode');
                    const cells = document.querySelectorAll('.forms-cell');
                    cells.forEach(cell => {
                        const count = cell.getAttribute('data-count');
                        const percent = cell.getAttribute('data-percent');
                        cell.textContent = (mode === 'count') ? Number(count)
                            .toLocaleString() : percent;
                    });
                });
            });

            // 4. Calls Dual Mode toggles (# vs %)
            const callsToggleBtns = document.querySelectorAll('.btn-calls-mode');
            callsToggleBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    callsToggleBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const mode = this.getAttribute('data-mode');
                    const cells = document.querySelectorAll('.calls-cell');
                    cells.forEach(cell => {
                        const count = cell.getAttribute('data-count');
                        const percent = cell.getAttribute('data-percent');
                        cell.textContent = (mode === 'count') ? Number(count)
                            .toLocaleString() : percent;
                    });
                });
            });
        });
    </script>
@endpush
