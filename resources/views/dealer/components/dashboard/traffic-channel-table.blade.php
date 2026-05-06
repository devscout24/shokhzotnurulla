{{-- Traffic Channel Statistics Table --}}
<div class="website-activity-container mt-4">
    <div class="activity-header">
        <div class="activity-title">Visits by Traffic Channel</div>
    </div>

    <div class="table-responsive">
        <table class="table custom-analytics-table">
            <thead>
                <tr>
                    <th>Channel</th>
                    <th class="text-end">Visits</th>
                    <th class="text-end">Unique Visitors</th>
                    <th class="text-end">Leads</th>
                    <th class="text-end">Conversion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trafficData['summary'] as $channel => $stats)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="channel-dot {{ strtolower(str_replace(' ', '-', $channel)) }}"></span>
                                {{ $channel }}
                            </div>
                        </td>
                        <td class="text-end fw-semibold">{{ number_format($stats['visits']) }}</td>
                        <td class="text-end">{{ number_format($stats['visitors']) }}</td>
                        <td class="text-end">{{ number_format($stats['leads']) }}</td>
                        <td class="text-end text-success fw-bold">
                            {{ $stats['visits'] > 0 ? number_format(($stats['leads'] / $stats['visits']) * 100, 2) : 0 }}%
                        </td>
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
        border-spacing: 0 8px;
    }

    .custom-analytics-table thead th {
        font-size: 11px;
        text-transform: uppercase;
        color: #999;
        font-weight: 600;
        border: none;
        padding: 10px 15px;
    }

    .custom-analytics-table tbody tr {
        background: #fcfcfc;
        transition: transform 0.2s;
    }

    .custom-analytics-table tbody tr:hover {
        background: #f8f9fa;
        transform: translateY(-1px);
    }

    .custom-analytics-table tbody td {
        padding: 12px 15px;
        font-size: 13px;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
        color: #444;
    }

    .custom-analytics-table tbody td:first-child {
        border-left: 1px solid #f0f0f0;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    .custom-analytics-table tbody td:last-child {
        border-right: 1px solid #f0f0f0;
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .channel-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .channel-dot.organic-search {
        background: #3498db;
    }

    .channel-dot.direct {
        background: #e67e22;
    }

    .channel-dot.referral {
        background: #2ecc71;
    }

    .channel-dot.social {
        background: #9b59b6;
    }
</style>
