@extends('layouts.dealer.app')
@section('title', __('Shortened Links') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    /* ── Layout ── */
    .ls-layout {
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
    .ls-content {
        flex: 1;
        padding: 0;
        overflow-x: auto;
        background: #f2f2f2;
    }

    /* ── Top Bar ── */
    .ls-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;
        background: #f2f2f2;
    }
    .ls-page-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }
    .ls-btn-shorten {
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: background 0.2s;
        white-space: nowrap;
    }
    .ls-btn-shorten:hover { background: #a93226; }

    /* ── Table Card ── */
    .ls-card {
        background: #fff;
        border-top: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
    }

    .ls-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }
    .ls-table thead tr {
        border-bottom: 1px solid #e8e8e8;
        background: #fff;
    }
    .ls-table thead th {
        padding: 10px 20px;
        font-weight: 600;
        color: #555;
        text-align: left;
        white-space: nowrap;
        font-size: 12.5px;
    }
    .ls-table tbody tr {
        border-bottom: 1px solid #f2f2f2;
    }
    .ls-table tbody tr:last-child { border-bottom: none; }
    .ls-table tbody tr:hover { background: #fafafa; }
    .ls-table tbody td {
        padding: 10px 20px;
        color: #555;
        font-size: 12.5px;
    }
    .ls-empty {
        text-align: center;
        color: #999;
        font-size: 13px;
        padding: 30px 20px !important;
    }

    /* ══════════════════════════════
       ADD SHORTENED LINK MODAL
    ══════════════════════════════ */
    .sl-overlay {
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
    .sl-overlay.active { display: flex; }

    .sl-modal {
        background: #fff;
        border-radius: 6px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.22);
        font-size: 13px;
    }

    .sl-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid #eee;
    }
    .sl-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #222;
    }
    .sl-btn-close {
        background: none;
        border: none;
        font-size: 18px;
        color: #888;
        cursor: pointer;
        line-height: 1;
        padding: 0 2px;
        transition: color 0.15s;
    }
    .sl-btn-close:hover { color: #333; }

    .sl-body { padding: 20px 18px 10px; }

    .sl-form-group { margin-bottom: 16px; }
    .sl-label {
        display: block;
        font-size: 12.5px;
        font-weight: 500;
        color: #333;
        margin-bottom: 6px;
    }
    .sl-control {
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
    .sl-control:focus {
        outline: none;
        border-color: #aaa;
        box-shadow: 0 0 0 2px rgba(0,0,0,0.06);
    }
    .sl-control::placeholder { color: #ccc; }

    .sl-select {
        width: 100%;
        padding: 8px 10px;
        font-size: 13px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23888'/%3E%3C/svg%3E") no-repeat right 10px center;
        background-size: 9px;
        appearance: none;
        -webkit-appearance: none;
        color: #555;
        box-sizing: border-box;
        cursor: pointer;
    }
    .sl-select:focus {
        outline: none;
        border-color: #aaa;
        box-shadow: 0 0 0 2px rgba(0,0,0,0.06);
    }

    .sl-footer {
        display: flex;
        justify-content: flex-end;
        padding: 14px 18px;
    }
    .sl-btn-save {
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 22px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }
    .sl-btn-save:hover { background: #a93226; }
    .main-content{
        padding: 0px !important;
    }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="view-content" data-view="links">
        <div class="ls-layout">

            {{-- ── LEFT SIDEBAR ── --}}
            <aside class="ai-sidebar">
                <a href="/dealer/connections/apps" class="menu-item">
                    <span class="icon">&#9776;</span> Apps &amp; integrations
                </a>
                <a href="/dealer/connections/links" class="menu-item active">
                    <span class="icon">&#128279;</span> Link shortener
                </a>
            </aside>

            {{-- ── RIGHT CONTENT ── --}}
            <div class="ls-content">

                {{-- Top Bar --}}
                <div class="ls-topbar">
                    <span class="ls-page-title">Shortened Links</span>
                    <button class="ls-btn-shorten" id="btnShortenLink" type="button">
                        + Shorten Link
                    </button>
                </div>

                {{-- Table --}}
                <div class="ls-card">
                    <table class="ls-table">
                        <thead>
                            <tr>
                                <th>Short link</th>
                                <th>Redirect to</th>
                                <th>Type</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($links ?? [] as $link)
                                <tr>
                                    <td>
                                        <a href="{{ $link->short_url }}" target="_blank"
                                           style="color:#c0392b;text-decoration:none;">
                                            {{ $link->short_url }}
                                        </a>
                                    </td>
                                    <td style="color:#555;">{{ $link->redirect_to }}</td>
                                    <td>{{ ucfirst($link->type) }}</td>
                                    <td>{{ $link->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div style="display:flex;gap:8px;">
                                            <button style="background:none;border:none;cursor:pointer;color:#999;font-size:13px;" title="Copy">&#128203;</button>
                                            <button style="background:none;border:none;cursor:pointer;color:#999;font-size:13px;" title="Delete">&#128465;</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="ls-empty">
                                        You haven't added any shortened links yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>{{-- end ls-card --}}

            </div>{{-- end ls-content --}}
        </div>{{-- end ls-layout --}}
    </div>{{-- end view-content --}}
</main>

{{-- ══════════════════════════════
     ADD SHORTENED LINK MODAL
══════════════════════════════ --}}
<div class="sl-overlay" id="shortenLinkModal">
    <div class="sl-modal">

        <div class="sl-header">
            <h5>Add Shortened Link</h5>
            <button class="sl-btn-close" id="btnCloseShortenModal" type="button">&times;</button>
        </div>

        <div class="sl-body">
            <form id="shortenLinkForm">

                {{-- URL to shorten --}}
                <div class="sl-form-group">
                    <label class="sl-label">URL to shorten</label>
                    <input type="url" class="sl-control" id="slUrl" placeholder="">
                </div>

                {{-- Permanent or temporary --}}
                <div class="sl-form-group">
                    <label class="sl-label">Permanent or temporary</label>
                    <select class="sl-select" id="slType">
                        <option value="" disabled selected>[Select]</option>
                        <option value="permanent">Permanent</option>
                        <option value="temporary">Temporary</option>
                    </select>
                </div>

            </form>
        </div>

        <div class="sl-footer">
            <button type="button" class="sl-btn-save" id="btnSaveShortLink">
                &#10003; Save
            </button>
        </div>

    </div>
</div>

@endsection

@push('page-scripts')
<script>
    const slOverlay = document.getElementById('shortenLinkModal');

    // Open
    document.getElementById('btnShortenLink').addEventListener('click', () => {
        slOverlay.classList.add('active');
    });

    // Close via X
    document.getElementById('btnCloseShortenModal').addEventListener('click', () => {
        slOverlay.classList.remove('active');
    });

    // Close on backdrop click
    slOverlay.addEventListener('click', (e) => {
        if (e.target === slOverlay) slOverlay.classList.remove('active');
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') slOverlay.classList.remove('active');
    });

    // Save — wire to your backend
    document.getElementById('btnSaveShortLink').addEventListener('click', () => {
        const url  = document.getElementById('slUrl').value.trim();
        const type = document.getElementById('slType').value;
        if (!url)  { alert('Please enter a URL to shorten.'); return; }
        if (!type) { alert('Please select permanent or temporary.'); return; }
        // TODO: submit via fetch/axios to your controller
        slOverlay.classList.remove('active');
    });
</script>
@endpush