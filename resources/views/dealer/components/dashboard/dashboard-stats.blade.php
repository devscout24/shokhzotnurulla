{{-- Dashboard Stats Section --}}
{{-- Bootstrap 5 + Custom CSS --}}
{{-- Sirf ye section hai — header, sidebar, heading already set hai --}}

{{-- CSS --}}
<style>
    .stats-section {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 24px 32px;
        margin-bottom: 20px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
    }

    .stat-item {
        padding: 8px 20px;
        border-right: 1px solid #e5e7eb;
    }

    .stat-item:first-child {
        padding-left: 0;
    }

    .stat-item:last-child {
        border-right: none;
    }

    .stat-label {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 8px;
        white-space: nowrap;
    }

    .stat-value-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #111827;
        line-height: 1;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 11px;
        font-weight: 600;
        color: #10b981;
        background: #f0fdf4;
        border-radius: 20px;
        padding: 3px 8px;
        white-space: nowrap;
    }

    .stat-badge svg {
        width: 12px;
        height: 12px;
    }

    /* Responsive */
    @media (max-width: 1199px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .stat-item:nth-child(3) {
            border-right: none;
        }

        .stat-item:nth-child(4) {
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            margin-top: 8px;
        }
        .stat-item:nth-child(5) {
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            margin-top: 8px;
        }
        .stat-item:nth-child(6) {
            border-top: 1px solid #e5e7eb;
            border-right: none;
            padding-top: 16px;
            margin-top: 8px;
        }
    }

    @media (max-width: 767px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-item {
            padding: 12px 16px;
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
        }

        .stat-item:nth-child(odd) {
            border-right: 1px solid #e5e7eb;
        }

        .stat-item:last-child,
        .stat-item:nth-last-child(2):nth-child(odd) {
            border-bottom: none;
        }
    }
</style>

<div class="stats-section">
    <div class="stats-grid">

        {{-- Total Leads --}}
        <div class="stat-item">
            <div class="stat-label">Total Leads</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ $totalLeads ?? 90 }}</span>
                <span class="stat-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    {{ $totalLeadsChange ?? 'Infinity' }}%
                </span>
            </div>
        </div>

        {{-- Web Form Leads --}}
        <div class="stat-item">
            <div class="stat-label">Web Form Leads</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ $webFormLeads ?? 58 }}</span>
                <span class="stat-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    {{ $webFormLeadsChange ?? 'Infinity' }}%
                </span>
            </div>
        </div>

        {{-- Click to Calls --}}
        <div class="stat-item">
            <div class="stat-label">Click to Calls</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ $clickToCalls ?? 17 }}</span>
                <span class="stat-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    {{ $clickToCallsChange ?? 'Infinity' }}%
                </span>
            </div>
        </div>

        {{-- Partial Leads --}}
        <div class="stat-item">
            <div class="stat-label">
                Partial Leads
                <svg style="width:13px;height:13px;color:#9ca3af;vertical-align:middle;margin-left:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
            </div>
            <div class="stat-value-row">
                <span class="stat-value">{{ $partialLeads ?? 15 }}</span>
            </div>
        </div>

        {{-- Unique Visitors --}}
        {{-- ============================================
        <div class="stat-item">
            <div class="stat-label">Unique Visitors</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ number_format($uniqueVisitors ?? 1403) }}</span>
                <span class="stat-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    {{ $uniqueVisitorsChange ?? '70050' }}%
                </span>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-label">Total Visits</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ number_format($totalVisits ?? 1600) }}</span>
                <span class="stat-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    {{ $totalVisitsChange ?? '79900' }}%
                </span>
            </div>
        </div>
        ============================================ --}}

    </div>
</div>
