@extends('layouts.dealer.app')
@section('title', __('Apps') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    /* ── Overall Layout ── */
    .ai-layout {
        display: flex;
        min-height: calc(100vh - 60px);
        background: #f2f2f2;
        font-size: 13px;
    }

    /* ── Left Sidebar ── */
    .ai-sidebar {
        width: 220px;
        min-width: 220px;
        background: #fff;
        border-right: 1px solid #e0e0e0;
        padding-top: 8px;
    }
    .ai-sidebar .menu-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        font-size: 13px;
        color: #444;
        cursor: pointer;
        border-left: 3px solid transparent;
        text-decoration: none;
        transition: background 0.15s;
    }
    .ai-sidebar .menu-item:hover { background: #f8f8f8; }
    .ai-sidebar .menu-item.active {
        border-left-color: #c0392b;
        color: #c0392b;
        font-weight: 600;
        background: #fdf5f5;
    }
    .ai-sidebar .menu-item .icon {
        font-size: 14px;
        width: 18px;
        text-align: center;
    }

    /* ── Right Content ── */
    .ai-wrapper {
        flex: 1;
        padding: 16px 20px 30px;
        overflow-x: auto;
    }

    .ai-page-title {
        font-size: 16px;
        font-weight: 600;
        color: #222;
        margin-bottom: 18px;
    }

    /* ── Apps Grid ── */
    .ai-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        max-width: 1100px;
    }

    /* ── App Card ── */
    .ai-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .ai-card:hover {
        box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        border-color: #ccc;
    }

    .ai-card-name {
        font-size: 12.5px;
        font-weight: 600;
        color: #333;
        padding: 10px 12px 8px;
        border-bottom: 1px solid #f0f0f0;
    }

    .ai-card-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        height: 90px;
        padding: 12px;
        background: #fff;
    }

    .logo-text {
        font-size: 18px;
        font-weight: 800;
        color: #333;
        text-align: center;
        line-height: 1.2;
    }

    .ai-card-status {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 8px 12px;
        border-top: 1px solid #f0f0f0;
        font-size: 11.5px;
        color: #999;
    }
    .ai-card-status.configured { color: #27ae60; }
    .ai-card-status .status-icon { font-size: 12px; }

    /* ── Brand Logo Styles ── */
    .logo-700credit   { color: #e85d04; font-size: 19px; font-weight: 900; font-family: Arial, sans-serif; }
    .logo-700credit span { color: #1a1a1a; }
    .logo-autocheck   { color: #003087; font-size: 14px; font-weight: 700; font-family: Arial, sans-serif; text-align: center; line-height: 1.4; }
    .logo-carnow      { color: #5bc0de; font-size: 24px; font-weight: 300; font-family: Georgia, serif; letter-spacing: -1px; }
    .logo-complyauto  { color: #1a1a1a; font-size: 13px; font-weight: 900; font-family: Arial, sans-serif; letter-spacing: 1px; }
    .logo-dealercenter{ color: #1e3a8a; font-size: 16px; font-weight: 900; font-family: Arial Black, sans-serif; font-style: italic; line-height: 1.15; }
    .logo-driveo      { color: #00aaff; font-size: 24px; font-weight: 300; font-family: Georgia, serif; letter-spacing: 2px; }
    .logo-ga4         { font-size: 11px; font-weight: 600; color: #444; font-family: Arial, sans-serif; margin-top: 5px; }
    .logo-gtm         { font-size: 11px; font-weight: 600; color: #444; font-family: Arial, sans-serif; margin-top: 5px; }
    .logo-ipacket     { color: #1a1a1a; font-size: 16px; font-weight: 700; font-family: Arial, sans-serif; }
    .logo-monroney    { color: #cc0000; font-size: 12px; font-weight: 700; font-family: Arial, sans-serif; }
    .logo-promax      { color: #1a1a1a; font-size: 17px; font-weight: 900; font-family: Arial Black, sans-serif; }
    .logo-stripe      { color: #1a1a1a; font-size: 26px; font-weight: 400; font-family: Georgia, serif; letter-spacing: -1px; }
    .logo-carfax      { color: #1a1a1a; font-size: 15px; font-weight: 900; font-family: Arial Black, sans-serif; letter-spacing: 2px; border: 2px solid #1a1a1a; padding: 4px 8px; }

    /* GA4 bar chart icon */
    .ga4-icon {
        display: flex;
        align-items: flex-end;
        gap: 3px;
        margin-bottom: 4px;
    }
    .ga4-icon span {
        display: inline-block;
        border-radius: 1px;
    }

    /* GTM diamond icon */
    .gtm-icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #4285f4 50%, #34a853 50%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
    }
    .gtm-icon::after {
        content: '';
        width: 13px;
        height: 13px;
        background: #fff;
        border-radius: 2px;
        transform: rotate(45deg);
    }

    /* ══════════════════════════════
       INTEGRATION MODAL
    ══════════════════════════════ */
    .int-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(30, 20, 40, 0.55);
        z-index: 1050;
        align-items: flex-start;
        justify-content: center;
        padding: 80px 16px;
        overflow-y: auto;
    }
    .int-overlay.active { display: flex; }

    .int-modal {
        background: #fff;
        border-radius: 6px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.22);
        font-size: 13px;
    }
    .int-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid #eee;
    }
    .int-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #222;
    }
    .int-btn-close {
        background: none;
        border: none;
        font-size: 18px;
        color: #888;
        cursor: pointer;
        line-height: 1;
        padding: 0 2px;
        transition: color 0.15s;
    }
    .int-btn-close:hover { color: #333; }

    .int-body { padding: 20px 18px; }

    .int-form-group { margin-bottom: 16px; }
    .int-label {
        display: block;
        font-size: 12px;
        font-weight: 500;
        color: #444;
        margin-bottom: 5px;
    }
    .int-label .req { color: #c0392b; }
    .int-control {
        width: 100%;
        padding: 8px 10px;
        font-size: 13px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fff;
        color: #333;
        box-sizing: border-box;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .int-control:focus {
        outline: none;
        border-color: #aaa;
        box-shadow: 0 0 0 2px rgba(0,0,0,0.06);
    }
    .int-control::placeholder { color: #bbb; }
    .int-help-text {
        font-size: 11px;
        color: #888;
        margin-top: 4px;
        line-height: 1.5;
    }
    .int-footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 12px 18px;
        border-top: 1px solid #f0f0f0;
    }
    .int-btn-cancel {
        background: #fff;
        color: #555;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 7px 18px;
        font-size: 13px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .int-btn-cancel:hover { background: #f5f5f5; }
    .int-btn-save {
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }
    .int-btn-save:hover { background: #a93226; }
    .main-content{
        padding: 0px !important;
    }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">

    <div class="view-content" data-view="apps">
        <div class="ai-layout">

            {{-- ── LEFT SIDEBAR ── --}}
            <aside class="ai-sidebar">
                <a href="/dealer/connections/apps" class="menu-item active">
                    <span class="icon">&#9776;</span> Apps &amp; integrations
                </a>
                <a href="/dealer/connections/links" class="menu-item">
                    <span class="icon">&#128279;</span> Link shortener
                </a>
            </aside>

            {{-- ── RIGHT CONTENT ── --}}
            <div class="ai-wrapper">

                <div class="ai-page-title">Apps &amp; Integrations</div>

                <div class="ai-grid">

                    {{-- 700Credit --}}
                    <div class="ai-card" data-app="generic" data-name="700Credit">
                        <div class="ai-card-name">700Credit</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-700credit">&#126;700<span>Credit</span></div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- Auto Check --}}
                    <div class="ai-card" data-app="generic" data-name="Auto Check">
                        <div class="ai-card-name">Auto Check</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-autocheck">&#10003; Auto<br>Check</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- CarNow --}}
                    <div class="ai-card" data-app="generic" data-name="CarNow">
                        <div class="ai-card-name">CarNow</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-carnow">CarNow</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- ComplyAuto --}}
                    <div class="ai-card" data-app="generic" data-name="ComplyAuto">
                        <div class="ai-card-name">ComplyAuto</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-complyauto">COMPLYA<span style="color:#c0392b;">&#10003;</span></div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- Dealer Center --}}
                    <div class="ai-card" data-app="generic" data-name="Dealer Center">
                        <div class="ai-card-name">Dealer Center</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-dealercenter">DEALER<br>CENTER</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- Driveo --}}
                    <div class="ai-card" data-app="generic" data-name="Driveo">
                        <div class="ai-card-name">Driveo</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-driveo">Driveo</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- Google Analytics 4 --}}
                    <div class="ai-card" data-app="ga4" data-name="Google Analytics 4">
                        <div class="ai-card-name">Google Analytics 4</div>
                        <div class="ai-card-logo">
                            <div class="ga4-icon">
                                <span style="width:8px;height:20px;background:#f9ab00;"></span>
                                <span style="width:8px;height:32px;background:#e37400;"></span>
                                <span style="width:8px;height:24px;background:#e37400;opacity:0.6;"></span>
                            </div>
                            <div class="logo-text logo-ga4">Google Analytics 4</div>
                        </div>
                        <div class="ai-card-status configured" id="ga4-status">
                            <span class="status-icon">&#10003;</span> Configured
                        </div>
                    </div>

                    {{-- Google Tag Manager --}}
                    <div class="ai-card" data-app="gtm" data-name="Google Tag Manager">
                        <div class="ai-card-name">Google Tag Manager</div>
                        <div class="ai-card-logo">
                            <div class="gtm-icon"></div>
                            <div class="logo-text logo-gtm">Google Tag Manager</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- iPacket --}}
                    <div class="ai-card" data-app="generic" data-name="iPacket">
                        <div class="ai-card-name">iPacket</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-ipacket">&#9673; iPacket&#183;</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- Monroney Labels --}}
                    <div class="ai-card" data-app="generic" data-name="Monroney Labels">
                        <div class="ai-card-name">Monroney Labels</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-monroney">MonroneyLabels<span style="color:#333;">.com</span></div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- ProMax --}}
                    <div class="ai-card" data-app="generic" data-name="ProMax">
                        <div class="ai-card-name">ProMax</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-promax"><span style="color:#c0392b;">&#9650;</span>ProMax</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- Stripe --}}
                    <div class="ai-card" data-app="generic" data-name="Stripe">
                        <div class="ai-card-name">Stripe</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-stripe">stripe</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                    {{-- Carfax --}}
                    <div class="ai-card" data-app="generic" data-name="Carfax">
                        <div class="ai-card-name">Carfax</div>
                        <div class="ai-card-logo">
                            <div class="logo-text logo-carfax">CARFAX</div>
                        </div>
                        <div class="ai-card-status">
                            <span class="status-icon">&#9888;</span> Not Configured
                        </div>
                    </div>

                </div>{{-- end ai-grid --}}
            </div>{{-- end ai-wrapper --}}
        </div>{{-- end ai-layout --}}
    </div>{{-- end view-content --}}
</main>

{{-- ══════════════════════════════
     GOOGLE ANALYTICS 4 MODAL
══════════════════════════════ --}}
<div class="int-overlay" id="ga4Modal">
    <div class="int-modal">
        <div class="int-header">
            <h5>Google Analytics 4</h5>
            <button class="int-btn-close" id="btnCloseGa4">&times;</button>
        </div>
        <div class="int-body">
            <div class="int-form-group">
                <label class="int-label">Measurement ID <span class="req">*</span></label>
                <input type="text" class="int-control" id="ga4MeasurementId"
                       placeholder="e.g. G-XXXXXXXXXX"
                       value="{{ config('integrations.ga4_measurement_id', '') }}">
                <p class="int-help-text">
                    Your GA4 Measurement ID starts with <strong>G-</strong>. Find it in your
                    Google Analytics property under Admin &rarr; Data Streams.
                </p>
            </div>
            <div class="int-form-group">
                <label class="int-label">Enable Tracking</label>
                <select class="int-control" id="ga4Enabled">
                    <option value="1" selected>Yes – Track all pages</option>
                    <option value="0">No – Disable tracking</option>
                </select>
            </div>
        </div>
        <div class="int-footer">
            <button class="int-btn-cancel" id="btnCancelGa4">Cancel</button>
            <button class="int-btn-save" id="btnSaveGa4">&#10003; Save</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     GOOGLE TAG MANAGER MODAL
══════════════════════════════ --}}
<div class="int-overlay" id="gtmModal">
    <div class="int-modal">
        <div class="int-header">
            <h5>Google Tag Manager</h5>
            <button class="int-btn-close" id="btnCloseGtm">&times;</button>
        </div>
        <div class="int-body">
            <div class="int-form-group">
                <label class="int-label">Container ID <span class="req">*</span></label>
                <input type="text" class="int-control" id="gtmContainerId"
                       placeholder="e.g. GTM-XXXXXXX"
                       value="{{ config('integrations.gtm_container_id', '') }}">
                <p class="int-help-text">
                    Your GTM Container ID starts with <strong>GTM-</strong>. Find it in your
                    Google Tag Manager account dashboard.
                </p>
            </div>
            <div class="int-form-group">
                <label class="int-label">Enable Tag Manager</label>
                <select class="int-control" id="gtmEnabled">
                    <option value="1" selected>Yes – Inject GTM snippet</option>
                    <option value="0">No – Disable</option>
                </select>
            </div>
        </div>
        <div class="int-footer">
            <button class="int-btn-cancel" id="btnCancelGtm">Cancel</button>
            <button class="int-btn-save" id="btnSaveGtm">&#10003; Save</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     GENERIC INTEGRATION MODAL
══════════════════════════════ --}}
<div class="int-overlay" id="genericModal">
    <div class="int-modal">
        <div class="int-header">
            <h5 id="genericModalTitle">Integration</h5>
            <button class="int-btn-close" id="btnCloseGeneric">&times;</button>
        </div>
        <div class="int-body">
            <div class="int-form-group">
                <label class="int-label">API Key / Account ID <span class="req">*</span></label>
                <input type="text" class="int-control" id="genericApiKey" placeholder="Enter your credentials">
                <p class="int-help-text">
                    Contact your <span id="genericProviderName"></span> account manager to obtain
                    your API key or account credentials.
                </p>
            </div>
            <div class="int-form-group">
                <label class="int-label">Enable Integration</label>
                <select class="int-control">
                    <option value="1" selected>Yes – Active</option>
                    <option value="0">No – Disabled</option>
                </select>
            </div>
        </div>
        <div class="int-footer">
            <button class="int-btn-cancel" id="btnCancelGeneric">Cancel</button>
            <button class="int-btn-save" id="btnSaveGeneric">&#10003; Save</button>
        </div>
    </div>
</div>

@endsection

@push('page-scripts')
<script>
    function openModal(id)  { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }

    // Close on backdrop click or Escape
    document.querySelectorAll('.int-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) overlay.classList.remove('active');
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.int-overlay.active').forEach(o => o.classList.remove('active'));
        }
    });

    // Card click handlers
    document.querySelectorAll('.ai-card').forEach(card => {
        card.addEventListener('click', () => {
            const app  = card.dataset.app;
            const name = card.dataset.name;
            if (app === 'ga4') {
                openModal('ga4Modal');
            } else if (app === 'gtm') {
                openModal('gtmModal');
            } else {
                document.getElementById('genericModalTitle').textContent = name;
                document.getElementById('genericProviderName').textContent = name;
                document.getElementById('genericApiKey').value = '';
                openModal('genericModal');
            }
        });
    });

    // GA4
    document.getElementById('btnCloseGa4').addEventListener('click',  () => closeModal('ga4Modal'));
    document.getElementById('btnCancelGa4').addEventListener('click', () => closeModal('ga4Modal'));
    document.getElementById('btnSaveGa4').addEventListener('click', () => {
        const id = document.getElementById('ga4MeasurementId').value.trim();
        if (!id) { alert('Please enter a Measurement ID.'); return; }
        const status = document.getElementById('ga4-status');
        status.className = 'ai-card-status configured';
        status.innerHTML = '<span class="status-icon">&#10003;</span> Configured';
        closeModal('ga4Modal');
    });

    // GTM
    document.getElementById('btnCloseGtm').addEventListener('click',  () => closeModal('gtmModal'));
    document.getElementById('btnCancelGtm').addEventListener('click', () => closeModal('gtmModal'));
    document.getElementById('btnSaveGtm').addEventListener('click', () => {
        const id = document.getElementById('gtmContainerId').value.trim();
        if (!id) { alert('Please enter a Container ID.'); return; }
        closeModal('gtmModal');
    });

    // Generic
    document.getElementById('btnCloseGeneric').addEventListener('click',  () => closeModal('genericModal'));
    document.getElementById('btnCancelGeneric').addEventListener('click', () => closeModal('genericModal'));
    document.getElementById('btnSaveGeneric').addEventListener('click',   () => closeModal('genericModal'));
</script>
@endpush