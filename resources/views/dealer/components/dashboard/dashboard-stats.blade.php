{{-- Dashboard Stats Section --}}
<style>
    .stats-section {
        background: #ffffff;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        padding: 20px 0;
        margin-bottom: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0;
        max-width: 100%;
    }

    .stat-item {
        padding: 5px 25px;
        border-right: 1px solid #f0f0f0;
    }

    .stat-item:first-child { padding-left: 15px; }
    .stat-item:last-child { border-right: none; }

    .stat-label {
        font-size: 11px;
        color: #888;
        font-weight: 500;
        margin-bottom: 12px;
        text-transform: none;
    }

    .stat-value-row {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        line-height: 1;
    }

    .stat-change {
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .stat-change.negative { color: #e54d42; }
    .stat-change.positive { color: #27ae60; }

    .stat-change svg {
        width: 12px;
        height: 12px;
        stroke-width: 3;
    }

    .help-icon {
        color: #ccc;
        cursor: pointer;
        font-size: 14px;
        margin-left: 4px;
    }

    /* Responsive */
    @media (max-width: 1400px) {
        .stat-item { padding: 5px 15px; }
    }

    @media (max-width: 1199px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 20px 0; }
        .stat-item:nth-child(3n) { border-right: none; }
    }

    @media (max-width: 767px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .stat-item:nth-child(2n) { border-right: none; }
    }
</style>

<div class="stats-section">
    <div class="stats-grid">

        {{-- Total Leads --}}
        <div class="stat-item">
            <div class="stat-label">Total Leads</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ number_format($totalLeads ?? 0) }}</span>
                <span class="stat-change {{ ($totalLeadsChange ?? 0) < 0 ? 'negative' : 'positive' }}">
                    @if(($totalLeadsChange ?? 0) > 0)
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="18 15 12 9 6 15"/></svg>
                    @endif
                    {{ abs($totalLeadsChange ?? 0) }}%
                </span>
            </div>
        </div>

        {{-- Web Form Leads --}}
        <div class="stat-item">
            <div class="stat-label">Web Form Leads</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ number_format($webFormLeads ?? 0) }}</span>
                <span class="stat-change {{ ($webFormLeadsChange ?? 0) < 0 ? 'negative' : 'positive' }}">
                    @if(($webFormLeadsChange ?? 0) > 0)
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="18 15 12 9 6 15"/></svg>
                    @endif
                    {{ abs($webFormLeadsChange ?? 0) }}%
                </span>
            </div>
        </div>

        {{-- Click to Calls --}}
        <div class="stat-item">
            <div class="stat-label">Click to Calls</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ number_format($clickToCalls ?? 0) }}</span>
                <span class="stat-change {{ ($clickToCallsChange ?? 0) < 0 ? 'negative' : 'positive' }}">
                    @if(($clickToCallsChange ?? 0) > 0)
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="18 15 12 9 6 15"/></svg>
                    @endif
                    {{ abs($clickToCallsChange ?? 0) }}%
                </span>
            </div>
        </div>

        {{-- Partial Leads --}}
        <div class="stat-item">
            <div class="stat-label">
                Partial Leads
                <i class="bi bi-question-circle help-icon"></i>
            </div>
            <div class="stat-value-row">
                <span class="stat-value">{{ number_format($partialLeads ?? 0) }}</span>
            </div>
        </div>

        {{-- Unique Visitors --}}
        <div class="stat-item">
            <div class="stat-label">Unique Visitors</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ number_format($uniqueVisitors ?? 0) }}</span>
                <span class="stat-change {{ ($uniqueVisitorsChange ?? 0) < 0 ? 'negative' : 'positive' }}">
                    @if(($uniqueVisitorsChange ?? 0) > 0)
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="18 15 12 9 6 15"/></svg>
                    @endif
                    {{ abs($uniqueVisitorsChange ?? 0) }}%
                </span>
            </div>
        </div>

        {{-- Total Visits --}}
        <div class="stat-item">
            <div class="stat-label">Total Visits</div>
            <div class="stat-value-row">
                <span class="stat-value">{{ number_format($totalVisits ?? 0) }}</span>
                <span class="stat-change {{ ($totalVisitsChange ?? 0) < 0 ? 'negative' : 'positive' }}">
                    @if(($totalVisitsChange ?? 0) > 0)
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="18 15 12 9 6 15"/></svg>
                    @endif
                    {{ abs($totalVisitsChange ?? 0) }}%
                </span>
            </div>
        </div>

    </div>
</div>

