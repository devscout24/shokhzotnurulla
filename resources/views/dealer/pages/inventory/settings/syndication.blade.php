@extends('layouts.dealer.app')
@section('title', __('Syndication') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    .fs-wrapper {
        display: flex;
        min-height: calc(100vh - 60px);
        background: #f2f2f2;
        font-size: 13px;
    }

    /* ── Left Sidebar ── */
    .fs-sidebar {
        width: 220px;
        min-width: 220px;
        background: #fff;
        border-right: 1px solid #e0e0e0;
        padding-top: 8px;
    }
    .fs-sidebar .menu-label {
        font-size: 10px;
        font-weight: 700;
        color: #aaa;
        letter-spacing: 1px;
        padding: 10px 16px 4px;
        text-transform: uppercase;
    }
    .fs-sidebar .menu-item {
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
    .fs-sidebar .menu-item:hover { background: #f8f8f8; }
    .fs-sidebar .menu-item.active {
        border-left-color: #c0392b;
        color: #c0392b;
        font-weight: 600;
        background: #fdf5f5;
    }
    .fs-sidebar .menu-item .icon {
        font-size: 14px;
        width: 18px;
        text-align: center;
    }
    .fs-sidebar .badge-new {
        background: #27ae60;
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        padding: 2px 5px;
        border-radius: 3px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-left: 2px;
    }

    /* ── Right Content ── */
    .syn-page {
        flex: 1;
        padding: 16px 20px;
        overflow-x: auto;
    }

    /* ── Top Bar ── */
    .syn-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .syn-page-title {
        font-size: 16px;
        font-weight: 600;
        color: #222;
    }
    .syn-btn-request {
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: background 0.2s;
        white-space: nowrap;
    }
    .syn-btn-request:hover {
        background: #a93226;
        color: #fff;
        text-decoration: none;
    }
    .syn-btn-request .email-icon {
        font-size: 13px;
    }

    /* ── Card ── */
    .syn-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }
    .syn-card-header {
        padding: 10px 16px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
        font-weight: 600;
        color: #333;
        background: #fff;
    }

    /* ── Table ── */
    .syn-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }
    .syn-table thead tr {
        border-bottom: 1px solid #e8e8e8;
        background: #fff;
    }
    .syn-table thead th {
        padding: 9px 16px;
        font-weight: 600;
        color: #666;
        text-align: left;
        white-space: nowrap;
    }
    .syn-table tbody tr {
        border-bottom: 1px solid #f2f2f2;
        transition: background 0.12s;
    }
    .syn-table tbody tr:last-child { border-bottom: none; }
    .syn-table tbody tr:hover { background: #fafafa; }
    .syn-table tbody td {
        padding: 10px 16px;
        color: #555;
        font-size: 12.5px;
    }
    .syn-table tbody td:first-child {
        color: #333;
        font-weight: 500;
    }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="view-content inventory-view" data-view="inventory">
        @include('dealer.partials.inventory-topbar')

        <div class="subview" data-subview="syndication">
            <div class="fs-wrapper">

                {{-- ── LEFT SIDEBAR ── --}}
                <aside class="fs-sidebar">
                    <div class="menu-label">Menu</div>
                    <a href="/dealer/inventory/settings/rates" class="menu-item">
                        <span class="icon">%</span> Interest Rates
                    </a>
                    <a href="/dealer/inventory/settings/fees" class="menu-item">
                        <span class="icon">$</span> Inventory Fees
                        <span class="badge-new">NEW</span>
                    </a>
                    <a href="/dealer/inventory/settings/syndication" class="menu-item active">
                        <span class="icon">&#8635;</span> Syndication
                    </a>
                </aside>

                {{-- ── RIGHT CONTENT ── --}}
                <div class="syn-page">

                    {{-- Top Bar --}}
                    <div class="syn-topbar">
                        <span class="syn-page-title">Syndication</span>
                            <a href="mailto:support@example.com?subject=Syndication%20Request&body=Hello%2C%20I%20would%20like%20to%20request%20syndication%20for%20our%20dealership."
                            class="syn-btn-request">
                            <span class="email-icon">&#9993;</span>
                            Request Syndication
                        </a>
                    </div>

                    {{-- Syndication Card --}}
                    <div class="syn-card">
                        <div class="syn-card-header">Syndication</div>

                        <table class="syn-table">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Feed Name</th>
                                    <th>Date Enabled</th>
                                    <th>Last Sent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($syndications ?? $defaultSyndications ?? [] as $item)
                                    <tr>
                                        <td>{{ $item['provider'] ?? $item->provider }}</td>
                                        <td>{{ $item['feed_name'] ?? $item->feed_name }}</td>
                                        <td>{{ $item['date_enabled'] ?? $item->date_enabled }}</td>
                                        <td>{{ $item['last_sent'] ?? $item->last_sent }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="color:#999;font-style:italic;padding:12px 16px;">
                                            No syndication feeds found.
                                        </td>
                                    </tr>
                                @endforelse

                                {{-- Static fallback rows matching screenshot --}}
                                @if(empty($syndications) && empty($defaultSyndications))
                                <tr>
                                    <td>Auto Dealers Digital</td>
                                    <td>Auto Dealers Digital - Angel Motors Inc</td>
                                    <td>Friday, February 26, 2026</td>
                                    <td>3/9/2026 12:00 AM UTC</td>
                                </tr>
                                <tr>
                                    <td>Carfax</td>
                                    <td>Carfax - Angel Motors Inc</td>
                                    <td>Friday, February 26, 2026</td>
                                    <td>3/9/2026 12:00 AM UTC</td>
                                </tr>
                                <tr>
                                    <td>CarGurus</td>
                                    <td>CarGurus - Angel Motors Inc</td>
                                    <td>Friday, February 26, 2026</td>
                                    <td>3/9/2026 12:00 AM UTC</td>
                                </tr>
                                <tr>
                                    <td>Cars For Sale</td>
                                    <td>Cars For Sale - Angel Motors Inc</td>
                                    <td>Friday, February 26, 2026</td>
                                    <td>3/9/2026 12:00 AM UTC</td>
                                </tr>
                                <tr>
                                    <td>Cars.com</td>
                                    <td>Cars.com - Angel Motors Inc</td>
                                    <td>Friday, February 26, 2026</td>
                                    <td>3/9/2026 12:00 AM UTC</td>
                                </tr>
                                <tr>
                                    <td>Facebook v2</td>
                                    <td>Facebook v2 - Angel Motors Inc</td>
                                    <td>Saturday, February 27, 2026</td>
                                    <td>3/9/2026 12:00 AM UTC</td>
                                </tr>
                                <tr>
                                    <td>HomeNet</td>
                                    <td>HomeNet - Angel Motors Inc</td>
                                    <td>Friday, February 26, 2026</td>
                                    <td>3/9/2026 12:00 AM UTC</td>
                                </tr>
                                <tr>
                                    <td>TrueCar</td>
                                    <td>TrueCar - Angel Motors Inc</td>
                                    <td>Friday, February 26, 2026</td>
                                    <td>3/9/2026 12:00 AM UTC</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>{{-- end syn-card --}}

                </div>{{-- end syn-page --}}
            </div>{{-- end fs-wrapper --}}
        </div>{{-- end subview --}}
    </div>
</main>
@endsection