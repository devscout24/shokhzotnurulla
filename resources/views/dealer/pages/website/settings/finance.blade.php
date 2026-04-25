@extends('layouts.dealer.app')
@section('title', __('Finance Settings') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    .ws-layout {
        display: flex;
        min-height: calc(100vh - 60px);
        background: #f2f2f2;
        font-size: 13px;
    }

    /* ── Sidebar ── */
    .ws-sidebar {
        width: 240px;
        min-width: 240px;
        background: #fff;
        border-right: 1px solid #e8e8e8;
        padding: 12px 0;
    }
    .ws-sidebar .menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        font-size: 13px;
        color: #555;
        text-decoration: none;
        border-left: 3px solid transparent;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s;
        line-height: 1.3;
    }
    .ws-sidebar .menu-item:last-child { border-bottom: none; }
    .ws-sidebar .menu-item:hover { background: #f8f8f8; color: #555; }
    .ws-sidebar .menu-item.active {
        color: #c0392b;
        font-weight: 700;
        background: #fff;
    }
    .ws-sidebar .menu-item .ws-icon {
        font-size: 15px;
        width: 20px;
        text-align: center;
        color: #aaa;
        flex-shrink: 0;
    }
    .ws-sidebar .menu-item.active .ws-icon { color: #c0392b; }

    /* ── Right Content ── */
    .ir-content {
        flex: 1;
        padding: 16px 20px;
        overflow-x: auto;
    }

    /* ── Card ── */
    .ir-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        max-width: 1200px;
        overflow: hidden;
    }

    .ir-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid #eee;
    }
    .ir-card-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }
    .ir-btn-add {
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 7px 16px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background 0.2s;
    }
    .ir-btn-add:hover { background: #a93226; }

    /* ── Notice row ── */
    .ir-notice {
        padding: 18px 20px;
        font-size: 13px;
        color: #444;
        line-height: 1.6;
    }
    .ir-notice a {
        color: #c0392b;
        text-decoration: underline;
        transition: color 0.15s;
    }
    .ir-notice a:hover { color: #a93226; }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header">
        <h2 class="view-title">Website Settings</h2>
    </div>

    <div class="view-content" data-view="interest-rates">
        <div class="ws-layout">

            {{-- ── LEFT SIDEBAR ── --}}
            <aside class="ws-sidebar">
                <a href="/dealer/website/settings" class="menu-item">
                    <span class="ws-icon"><i class="bi bi-info-circle"></i></span>
                    <span>General</span>
                </a>
                <a href="/dealer/website/settings/locations" class="menu-item">
                    <span class="ws-icon"><i class="bi bi-geo-alt"></i></span>
                    <span>Locations &amp; Hours</span>
                </a>
                <a href="/dealer/website/settings/banners" class="menu-item">
                    <span class="ws-icon"><i class="bi bi-card-image"></i></span>
                    <span>Banners /<br>Announcements</span>
                </a>
                <a href="/dealer/website/settings/finance" class="menu-item active">
                    <span class="ws-icon"><i class="bi bi-percent"></i></span>
                    <span>Interest Rates</span>
                </a>
                <a href="/dealer/website/settings/retail" class="menu-item">
                    <span class="ws-icon"><i class="bi bi-grid"></i></span>
                    <span>Digital Retail</span>
                </a>
                {{-- <a href="/dealer/website/settings/domains" class="menu-item">
                    <span class="ws-icon"><i class="bi bi-globe"></i></span>
                    <span>Domains</span>
                </a> --}}
                <a href="/dealer/website/settings/redirects" class="menu-item">
                    <span class="ws-icon"><i class="bi bi-arrow-left-right"></i></span>
                    <span>Redirects</span>
                </a>
                <a href="/dealer/website/settings/ips" class="menu-item">
                    <span class="ws-icon"><i class="bi bi-hdd-network"></i></span>
                    <span>Dealer IP Addresses</span>
                </a>
            </aside>

            {{-- ── RIGHT CONTENT ── --}}
            <div class="ir-content">
                <div class="ir-card">

                    {{-- Card Header --}}
                    <div class="ir-card-header">
                        <span class="ir-card-title">Manage Interest Rates</span>
                        <button type="button" class="ir-btn-add">
                            + Add Rate
                        </button>
                    </div>

                    {{-- Notice --}}
                    <div class="ir-notice">
                        The interest rates screen has been moved to
                        <a href="{{ route('dealer.inventory.settings.rates.index') }}">
                            Inventory Settings &gt; Interest Rates
                        </a>.
                    </div>

                </div>
            </div>

        </div>
    </div>
</main>
@endsection
