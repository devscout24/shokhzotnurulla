{{-- ── SUBMISSION DETAIL CANVAS ── --}}
<div class="fs-canvas-overlay" id="fsCanvasOverlay"></div>
<div class="fs-canvas" id="fsCanvas">

    {{-- Fixed header --}}
    <div class="fs-canvas-header">
        <span class="fs-canvas-title">Form Submission</span>
        <button type="button" class="fs-canvas-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Print
        </button>
        <button type="button" class="fs-canvas-close" id="fsCanvasClose">&times;</button>
    </div>

    {{-- Single scrollable body --}}
    <div class="fs-canvas-body">

        {{-- Left: Form Data (scrolls) --}}
        <div class="fs-canvas-left">
            <div class="fs-canvas-section-title">Form Data</div>
            <div id="fsFormSections">
                {{-- Populated by JS --}}
            </div>
        </div>

        {{-- Right: Customer Details (sticky) --}}
        <div class="fs-canvas-right">
            <div class="fs-detail-title">Customer Details</div>
            <div class="fs-detail-row"><span class="fs-detail-label">Submitter IP</span><span class="fs-detail-value" id="fsDetIp">—</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">Location</span><span class="fs-detail-value muted" id="fsDetLocation">--</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">Device</span><span class="fs-detail-value" id="fsDetDevice">—</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">Brand</span><span class="fs-detail-value" id="fsDetBrand">—</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">Model</span><span class="fs-detail-value" id="fsDetModel">—</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">Client</span><span class="fs-detail-value" id="fsDetClient">—</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">OS</span><span class="fs-detail-value" id="fsDetOs">—</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">OS Version</span><span class="fs-detail-value" id="fsDetOsVersion">—</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">Referer</span><span class="fs-detail-value muted" id="fsDetReferer">--</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">Classification</span><span class="fs-detail-value" id="fsDetClassification">—</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">UTM Source</span><span class="fs-detail-value muted" id="fsDetUtmSource">--</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">UTM Campaign</span><span class="fs-detail-value muted" id="fsDetUtmCampaign">--</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">UTM Term</span><span class="fs-detail-value muted" id="fsDetUtmTerm">--</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">UTM Content</span><span class="fs-detail-value muted" id="fsDetUtmContent">--</span></div>
            <div class="fs-detail-row"><span class="fs-detail-label">Visit Duration</span><span class="fs-detail-value" id="fsDetVisitDuration">—</span></div>
        </div>

    </div>
</div>