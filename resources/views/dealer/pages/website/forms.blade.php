@extends('layouts.dealer.app')
@section('title', __('Form Submissions') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    .fs-topbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: auto;
    }
    .fs-search-wrap {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        overflow: hidden;
        min-width: 260px;
    }
    .fs-search-icon { padding: 0 10px; color: #aaa; font-size: 14px; flex-shrink: 0; }
    .fs-search-input {
        border: none; outline: none; font-size: 13px;
        padding: 7px 10px 7px 0; color: #333; background: transparent; width: 100%;
    }
    .fs-search-input::placeholder { color: #bbb; }

    /* ── Filter dropdown ── */
    .fs-filter-wrap { position: relative; flex-shrink: 0; }
    .fs-filter-btn {
        display: inline-flex; align-items: center; gap: 6px;
        border: 1px solid #ddd; background: #fff; font-size: 13px;
        color: #444; cursor: pointer; padding: 7px 14px;
        border-radius: 6px; white-space: nowrap; transition: background 0.15s;
    }
    .fs-filter-btn:hover { background: #f5f5f5; }
    .fs-filter-btn.open  { background: #f5f5f5; border-color: #bbb; }
    .fs-filter-chevron   { font-size: 11px; transition: transform 0.2s; }

    .fs-filter-dropdown {
        display: none; position: absolute; top: calc(100% + 4px); right: 0;
        width: 280px; background: #fff; border: 1px solid #e0e0e0;
        border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        z-index: 999; overflow: hidden;
    }
    .fs-filter-dropdown.open { display: block; }
    .fs-filter-list {
        list-style: none; margin: 0; padding: 4px 0;
        max-height: 340px; overflow-y: auto;
    }
    .fs-filter-list::-webkit-scrollbar { width: 5px; }
    .fs-filter-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 3px; }
    .fs-filter-item {
        padding: 9px 16px; font-size: 13px; color: #333;
        cursor: pointer; transition: background 0.12s;
        display: flex; align-items: center; justify-content: space-between;
    }
    .fs-filter-item:hover { background: #f8f8f8; }
    .fs-filter-item.active { color: #c0392b; font-weight: 600; background: #fff5f5; }
    .fs-filter-item.active::after { content: '✓'; font-size: 12px; color: #c0392b; }
    .fs-filter-divider { border: none; border-top: 1px solid #f0f0f0; margin: 4px 0; }
    .date-picker-box{padding: 3px 24px;}

    /* ── Card ── */
    .fs-card {
        background: #fff; border: 1px solid #e0e0e0;
        border-radius: 8px; overflow: hidden; font-size: 13px;
    }
    .fs-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; border-bottom: 1px solid #eee;
    }
    .fs-card-title { font-size: 14px; font-weight: 600; color: #222; }
    .fs-header-actions { display: flex; align-items: center; gap: 8px; }

    .fs-btn-export {
        background: none; border: 1px solid #ddd; border-radius: 5px;
        color: #555; font-size: 12px; padding: 6px 14px; cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px; transition: background 0.15s;
    }
    .fs-btn-export:hover { background: #f5f5f5; }

    .fs-btn-unread {
        background: none; border: 1px solid #ddd; border-radius: 5px;
        color: #555; font-size: 12px; padding: 6px 14px; cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        opacity: 0.45; pointer-events: none; transition: background 0.15s, opacity 0.2s;
    }
    .fs-btn-unread.enabled { opacity: 1; pointer-events: auto; }
    .fs-btn-unread.enabled:hover { background: #f5f5f5; }

    .fs-btn-delete {
        background: #c0392b; border: none; border-radius: 5px;
        color: #fff; font-size: 12px; padding: 6px 14px; cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        opacity: 0.4; pointer-events: none; transition: background 0.2s, opacity 0.2s;
    }
    .fs-btn-delete.enabled { opacity: 1; pointer-events: auto; }
    .fs-btn-delete.enabled:hover { background: #a93226; }

    /* ── Tabs ── */
    .fs-tabs { display: flex; padding: 0 18px; border-bottom: 1px solid #eee; }
    .fs-tab {
        padding: 10px 14px; font-size: 13px; color: #777;
        cursor: pointer; background: none; border: none;
        border-bottom: 2px solid transparent; margin-bottom: -1px;
        transition: color 0.15s, border-color 0.15s; white-space: nowrap;
    }
    .fs-tab:hover { color: #333; }
    .fs-tab.active { color: #c0392b; border-bottom-color: #c0392b; font-weight: 600; }

    /* ── Table ── */
    .fs-table-wrap {
        overflow-x: auto;
        max-height: calc(100vh - 360px);
        overflow-y: auto;
    }
    .fs-table-wrap::-webkit-scrollbar { width: 5px; }
    .fs-table-wrap::-webkit-scrollbar-thumb { background: #ddd; border-radius: 3px; }

    .fs-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .fs-table thead th {
        padding: 10px 14px; font-weight: 600; color: #444;
        text-align: left; background: #fff;
        border-bottom: 1px solid #e8e8e8; white-space: nowrap;
        position: sticky; top: 0; z-index: 1;
    }
    .fs-table thead th:first-child { width: 40px; padding-left: 18px; }
    .fs-table tbody tr {
        border-bottom: 1px solid #f2f2f2;
        transition: background 0.1s; cursor: pointer;
    }
    .fs-table tbody tr:last-child { border-bottom: none; }
    .fs-table tbody tr:hover { background: #fafafa; }
    .fs-table tbody tr.fs-row-selected { background: #fff8f8; }
    .fs-table tbody td { padding: 11px 14px; color: #333; vertical-align: middle; }
    .fs-table tbody td:first-child { padding-left: 18px; width: 40px; }
    .fs-table tbody td:last-child { width: 36px; text-align: center; }

    .fs-cb { width: 16px; height: 16px; accent-color: #c0392b; cursor: pointer; display: block; }

    .fs-badge {
        display: inline-block; background: #e8e8e8; color: #555;
        font-size: 11.5px; padding: 3px 8px; border-radius: 4px; font-family: monospace;
    }
    .fs-vehicle-link { color: #c0392b; text-decoration: none; font-size: 13px; }
    .fs-vehicle-link:hover { text-decoration: underline; }

    .fs-status { display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .fs-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; display: inline-block; }
    .fs-dot.complete  { background: #27ae60; }
    .fs-dot.abandoned { background: #f39c12; }
    .fs-dot.unread    { background: #3498db; }

    .fs-arrow-btn {
        background: none; border: none; color: #999; font-size: 16px;
        cursor: pointer; padding: 4px 8px; border-radius: 4px;
        transition: color 0.15s, background 0.15s;
    }
    .fs-arrow-btn:hover { color: #333; background: #f0f0f0; }

    .fs-footer {
        padding: 14px 18px; text-align: center;
        font-size: 12.5px; color: #888; border-top: 1px solid #f0f0f0;
    }
    /* ── Submission Detail Canvas ── */
    .fs-canvas-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(30, 20, 40, 0.45);
        z-index: 1040;
    }
    .fs-canvas-overlay.open { display: block; }

    .fs-canvas {
        position: fixed;
        top: 0;
        right: -1060px;
        width: 1060px;
        max-width: 100vw;
        height: 100vh;
        background: #fff;
        z-index: 1045;
        display: flex;
        flex-direction: column;
        transition: right 0.3s ease;
        box-shadow: -4px 0 24px rgba(0,0,0,0.14);
    }
    .fs-canvas.open { right: 0; }

    /* Canvas header — fixed at top, never scrolls */
    .fs-canvas-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 24px;
        border-bottom: 1px solid #eee;
        flex-shrink: 0;
        background: #fff;
        z-index: 2;
    }
    .fs-canvas-title {
        font-size: 16px;
        font-weight: 600;
        color: #222;
        flex: 1;
    }
    .fs-canvas-print {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 5px;
        font-size: 13px;
        color: #555;
        padding: 6px 14px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .fs-canvas-print:hover { background: #f5f5f5; }
    .fs-canvas-close {
        background: none; border: none;
        font-size: 22px; color: #888;
        cursor: pointer; padding: 0 4px;
        line-height: 1; transition: color 0.15s;
    }
    .fs-canvas-close:hover { color: #222; }

    /* Single scrollable body */
    .fs-canvas-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
    }
    .fs-canvas-body::-webkit-scrollbar { width: 6px; }
    .fs-canvas-body::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
    .fs-canvas-body::-webkit-scrollbar-track { background: #f5f5f5; }

    /* Left: Form Data */
    .fs-canvas-left {
        flex: 1;
        padding: 24px;
        border-right: 1px solid #eee;
        min-width: 0;
    }
    .fs-canvas-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #222;
        margin-bottom: 14px;
    }

    /* Form sections */
    .fs-form-card {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 0;
    }
    .fs-form-type-row {
        background: #f7f7f7;
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 500;
        color: #444;
        border-bottom: 1px solid #e8e8e8;
    }
    .fs-form-field-row {
        display: flex;
        align-items: flex-start;
        padding: 11px 16px;
        border-bottom: 1px solid #f2f2f2;
        font-size: 13px;
    }
    .fs-form-field-row:last-child { border-bottom: none; }
    .fs-form-field-label {
        width: 260px;
        min-width: 260px;
        color: #555;
        padding-right: 16px;
    }
    .fs-form-field-value { color: #222; flex: 1; }

    /* Arrow connector between sections */
    .fs-section-arrow {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 10px 16px;
        color: #bbb;
        font-size: 18px;
        border-left: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        background: #fff;
    }

    .fs-form-ended {
        background: #f7f7f7;
        padding: 14px 16px;
        font-size: 13px;
        color: #666;
        font-style: italic;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-top: 0;
    }

    /* Right: Customer Details — sticky so it stays in view while scrolling */
    .fs-canvas-right {
        width: 280px;
        min-width: 280px;
        padding: 24px 20px;
        position: sticky;
        top: 0;
        align-self: flex-start;
    }
    .fs-detail-title {
        font-size: 14px;
        font-weight: 600;
        color: #222;
        margin-bottom: 16px;
    }
    .fs-detail-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px solid #f2f2f2;
        font-size: 13px;
        gap: 8px;
    }
    .fs-detail-row:last-child { border-bottom: none; }
    .fs-detail-label { color: #666; flex-shrink: 0; }
    .fs-detail-value { color: #222; text-align: right; font-weight: 500; }
    .fs-detail-value.muted { color: #aaa; font-weight: 400; }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">

    <div class="page-header">
        <h2 class="view-title">Form Submissions</h2>
        <div class="fs-topbar-actions">

            {{-- Search --}}
            <div class="fs-search-wrap">
                <span class="fs-search-icon"><i class="bi bi-search"></i></span>
                <input type="text" class="fs-search-input" id="fsSearch" placeholder="Search by name">
            </div>

            {{-- Date range --}}
            <div class="date-picker-box">
                <i class="bi bi-calendar3"></i>
                <input type="text" id="dateRange" readonly placeholder="2/12/2026 - 3/11/2026">
            </div>

            {{-- Filter by Form --}}
            <div class="fs-filter-wrap">
                <button class="fs-filter-btn" type="button" id="fsFilterBtn">
                    <i class="bi bi-funnel"></i>
                    <span id="fsFilterLabel">Filter by Form</span>
                    <i class="bi bi-chevron-down fs-filter-chevron" id="fsFilterChevron"></i>
                </button>
                <div class="fs-filter-dropdown" id="fsFilterDropdown">
                    <ul class="fs-filter-list">
                        <li class="fs-filter-item active" data-form="all">All Forms</li>
                        <hr class="fs-filter-divider">
                        <li class="fs-filter-item" data-form="Contact Form">Contact Form</li>
                        <li class="fs-filter-item" data-form="Hard Credit App (Joint Borrowers)">Hard Credit App (Joint Borrowers)</li>
                        <li class="fs-filter-item" data-form="Hard Credit App (Single Borrower)">Hard Credit App (Single Borrower)</li>
                        <li class="fs-filter-item" data-form="Hard Credit App (Single - Con..">Hard Credit App (Single - Con..</li>
                        <li class="fs-filter-item" data-form="Hard Credit App (Single Borro..">Hard Credit App (Single Borro..</li>
                        <li class="fs-filter-item" data-form="Schedule a Test Drive">Schedule a Test Drive</li>
                        <li class="fs-filter-item" data-form="Schedule Service">Schedule Service</li>
                        <li class="fs-filter-item" data-form="Soft Credit App (Joint Borrowers)">Soft Credit App (Joint Borrowers)</li>
                        <li class="fs-filter-item" data-form="Soft Credit App (Single Borrower)">Soft Credit App (Single Borrower)</li>
                        <li class="fs-filter-item" data-form="Trade-In Form">Trade-In Form</li>
                        <li class="fs-filter-item" data-form="Trade-in History Questionnaire">Trade-in History Questionnaire</li>
                        <li class="fs-filter-item" data-form="Trade-in Contact Information">Trade-in Contact Information</li>
                        <li class="fs-filter-item" data-form="Unlock Calculator">Unlock Calculator</li>
                        <li class="fs-filter-item" data-form="Unlock e-Price">Unlock e-Price</li>
                        <li class="fs-filter-item" data-form="Unlock Pricing">Unlock Pricing</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <div class="view-content" data-view="form-submissions">
        <div class="fs-card">

            {{-- Card Header --}}
            <div class="fs-card-header">
                <span class="fs-card-title">Submissions</span>
                <div class="fs-header-actions">
                    <button type="button" class="fs-btn-export">
                        <i class="bi bi-upload"></i> Export
                    </button>
                    <button type="button" class="fs-btn-unread" id="btnMarkUnread">
                        <i class="bi bi-circle"></i> Mark as Unread
                    </button>
                    <button type="button" class="fs-btn-delete" id="btnDelete">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="fs-tabs">
                <button class="fs-tab active" data-tab="all"       type="button">All</button>
                <button class="fs-tab"        data-tab="unread"    type="button">Unread</button>
                <button class="fs-tab"        data-tab="completed" type="button">Completed</button>
                <button class="fs-tab"        data-tab="abandoned" type="button">Abandoned</button>
                <button class="fs-tab"        data-tab="archived"  type="button">Read / Archived</button>
            </div>

            {{-- Table --}}
            <div class="fs-table-wrap">
                <table class="fs-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="fs-cb" id="fsSelectAll"></th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Form Alias</th>
                            <th>Vehicle / Referrer</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="fsTableBody">
                        @php
                        $submissions = $submissions ?? [
                            ['name'=>'Devin Farlow',      'type'=>'Hard Credit App (Single Borro..',  'alias'=>'', 'vehicle'=>'/get-approved',                    'link'=>false, 'status'=>'Abandoned', 'submitted'=>'3/10/2026 3:54 PM'],
                            ['name'=>'Christopher Rose',  'type'=>'Hard Credit App (Single - Con..', 'alias'=>'', 'vehicle'=>'/get-approved',                    'link'=>false, 'status'=>'Complete',  'submitted'=>'3/10/2026 11:52 AM'],
                            ['name'=>'Christopher Rose',  'type'=>'Hard Credit App (Single Borro..',  'alias'=>'', 'vehicle'=>'/get-approved',                    'link'=>false, 'status'=>'Abandoned', 'submitted'=>'3/10/2026 11:42 AM'],
                            ['name'=>'Mattison Reeder',   'type'=>'Hard Credit App (Single Borro..',  'alias'=>'', 'vehicle'=>'3641: 2021 NISSAN ALTIMA SR',       'link'=>true,  'status'=>'Abandoned', 'submitted'=>'3/9/2026 3:09 PM'],
                            ['name'=>'Trevor Hawks',      'type'=>'Unlock Calculator',                'alias'=>'', 'vehicle'=>'1388: 2023 MAZDA 3 PREFERRED',     'link'=>true,  'status'=>'Complete',  'submitted'=>'3/8/2026 5:21 PM'],
                            ['name'=>'Susie Ramirez',     'type'=>'Unlock Calculator',                'alias'=>'', 'vehicle'=>'3641: 2021 NISSAN ALTIMA SR',       'link'=>true,  'status'=>'Complete',  'submitted'=>'3/8/2026 10:11 AM'],
                            ['name'=>'Juan Alba',         'type'=>'Unlock Calculator',                'alias'=>'', 'vehicle'=>'7196: 2015 HONDA CIVIC LX',         'link'=>true,  'status'=>'Complete',  'submitted'=>'3/8/2026 7:49 AM'],
                            ['name'=>'Sabastian Smith',   'type'=>'Hard Credit App (Single - Con..', 'alias'=>'', 'vehicle'=>'2019 Chevrolet Colorado Work Truck', 'link'=>true,  'status'=>'Complete',  'submitted'=>'3/7/2026 9:42 PM'],
                            ['name'=>'Kamaurion Conley',  'type'=>'Hard Credit App (Single - Con..', 'alias'=>'', 'vehicle'=>'/get-approved',                    'link'=>false, 'status'=>'Complete',  'submitted'=>'3/7/2026 6:37 PM'],
                            ['name'=>'Kelly Hall',        'type'=>'Hard Credit App (Single - Con..', 'alias'=>'', 'vehicle'=>'5086: 2019 INFINITI QX50 ESSENTIAL','link'=>true,  'status'=>'Complete',  'submitted'=>'3/6/2026 4:34 PM'],
                            ['name'=>'Maria Gonzalez',    'type'=>'Contact Form',                     'alias'=>'', 'vehicle'=>'/contact',                         'link'=>false, 'status'=>'Complete',  'submitted'=>'3/5/2026 2:10 PM'],
                            ['name'=>'James Whitfield',   'type'=>'Trade-In Form',                    'alias'=>'', 'vehicle'=>'4421: 2020 TOYOTA CAMRY SE',        'link'=>true,  'status'=>'Abandoned', 'submitted'=>'3/4/2026 11:30 AM'],
                            ['name'=>'Linda Torres',      'type'=>'Unlock Calculator',                'alias'=>'', 'vehicle'=>'8812: 2022 KIA SORENTO LX',         'link'=>true,  'status'=>'Complete',  'submitted'=>'3/3/2026 9:15 AM'],
                            ['name'=>'Robert Nguyen',     'type'=>'Hard Credit App (Single Borro..',  'alias'=>'', 'vehicle'=>'/get-approved',                    'link'=>false, 'status'=>'Abandoned', 'submitted'=>'3/2/2026 8:00 PM'],
                            ['name'=>'Patricia Simmons',  'type'=>'Schedule Service',                 'alias'=>'', 'vehicle'=>'/service',                         'link'=>false, 'status'=>'Complete',  'submitted'=>'3/1/2026 3:45 PM'],
                        ];
                        @endphp

                        @foreach($submissions as $row)
                        <tr class="fs-row" data-status="{{ strtolower($row['status']) }}" data-type="{{ $row['type'] }}">
                            <td><input type="checkbox" class="fs-cb fs-row-cb"></td>
                            <td>{{ $row['name'] }}</td>
                            <td style="color:#555;">{{ $row['type'] }}</td>
                            <td>@if($row['alias'])<span class="fs-badge">{{ $row['alias'] }}</span>@endif</td>
                            <td>
                                @if($row['link'])
                                    <a href="#" class="fs-vehicle-link">{{ $row['vehicle'] }}</a>
                                @else
                                    <span class="fs-badge">{{ $row['vehicle'] }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fs-status">
                                    <span class="fs-dot {{ strtolower($row['status']) }}"></span>
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td style="white-space:nowrap;color:#555;">{{ $row['submitted'] }}</td>
                            <td>
                                <button class="fs-arrow-btn" type="button">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="fs-footer" id="fsFooter">
                Showing results 1 - {{ count($submissions ?? []) }} of {{ count($submissions ?? []) }}
            </div>

        </div>
    </div>
</main>
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
@endsection

@push('page-scripts')
<script>
(function () {

    var btnDelete  = document.getElementById('btnDelete');
    var btnUnread  = document.getElementById('btnMarkUnread');
    var selectAll  = document.getElementById('fsSelectAll');
    var activeTab  = 'all';
    var activeForm = 'all';
    var searchQ    = '';

    /* ── Visibility logic (combines tab + form filter + search) ── */
    function isVisible(row) {
        var status = row.dataset.status;
        var type   = row.dataset.type || '';
        var name   = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

        var tabOk =
            activeTab === 'all'       ? true :
            activeTab === 'completed' ? status === 'complete' :
            activeTab === 'abandoned' ? status === 'abandoned' :
            activeTab === 'unread'    ? status === 'unread' :
            activeTab === 'archived'  ? status === 'archived' : true;

        var formOk = activeForm === 'all' ? true : type === activeForm;
        var searchOk = searchQ === '' ? true : name.includes(searchQ);

        return tabOk && formOk && searchOk;
    }

    function applyFilters() {
        document.querySelectorAll('.fs-row').forEach(function (row) {
            var show = isVisible(row);
            row.style.display = show ? '' : 'none';
            if (!show) {
                var cb = row.querySelector('.fs-row-cb');
                if (cb) { cb.checked = false; row.classList.remove('fs-row-selected'); }
            }
        });
        selectAll.checked = false;
        updateBtns();
        updateFooter();
    }

    /* ── Buttons state ── */
    function getChecked()    { return document.querySelectorAll('.fs-row-cb:checked'); }
    function getVisibleCbs() { return document.querySelectorAll('.fs-row:not([style*="display:none"]) .fs-row-cb'); }

    function updateBtns() {
        var n   = getChecked().length;
        var vis = getVisibleCbs().length;
        btnDelete.classList.toggle('enabled', n > 0);
        btnUnread.classList.toggle('enabled', n > 0);
        selectAll.indeterminate = n > 0 && n < vis;
        selectAll.checked       = vis > 0 && n === vis;
    }

    /* ── Footer ── */
    function updateFooter() {
        var vis   = document.querySelectorAll('.fs-row:not([style*="display:none"])').length;
        var total = document.querySelectorAll('.fs-row').length;
        document.getElementById('fsFooter').textContent =
            'Showing results 1 - ' + vis + ' of ' + total;
    }

    /* ── Row checkboxes ── */
    document.getElementById('fsTableBody').addEventListener('change', function (e) {
        if (!e.target.classList.contains('fs-row-cb')) return;
        e.target.closest('tr').classList.toggle('fs-row-selected', e.target.checked);
        updateBtns();
    });

    /* Select all */
    selectAll.addEventListener('change', function () {
        getVisibleCbs().forEach(function (cb) {
            cb.checked = selectAll.checked;
            cb.closest('tr').classList.toggle('fs-row-selected', selectAll.checked);
        });
        updateBtns();
    });

    /* Delete */
    btnDelete.addEventListener('click', function () {
        var checked = getChecked();
        if (!checked.length) return;
        if (!confirm('Delete ' + checked.length + ' submission(s)?')) return;
        checked.forEach(function (cb) { cb.closest('tr').remove(); });
        updateBtns();
        updateFooter();
    });

    /* ── Tabs ── */
    document.querySelectorAll('.fs-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.fs-tab').forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            activeTab = tab.dataset.tab;
            applyFilters();
        });
    });

    /* ── Search ── */
    document.getElementById('fsSearch').addEventListener('input', function () {
        searchQ = this.value.toLowerCase();
        applyFilters();
    });

    /* ── Filter by Form ── */
    var fsFilterBtn      = document.getElementById('fsFilterBtn');
    var fsFilterDropdown = document.getElementById('fsFilterDropdown');
    var fsFilterLabel    = document.getElementById('fsFilterLabel');
    var fsFilterChevron  = document.getElementById('fsFilterChevron');

    fsFilterBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = fsFilterDropdown.classList.toggle('open');
        fsFilterBtn.classList.toggle('open', isOpen);
        fsFilterChevron.style.transform = isOpen ? 'rotate(180deg)' : '';
    });

    document.querySelectorAll('.fs-filter-item').forEach(function (item) {
        item.addEventListener('click', function () {
            document.querySelectorAll('.fs-filter-item').forEach(function (i) { i.classList.remove('active'); });
            item.classList.add('active');
            activeForm = item.dataset.form;
            fsFilterLabel.textContent = activeForm === 'all' ? 'Filter by Form' : activeForm;
            fsFilterDropdown.classList.remove('open');
            fsFilterBtn.classList.remove('open');
            fsFilterChevron.style.transform = '';
            applyFilters();
        });
    });

    document.addEventListener('click', function (e) {
        if (!fsFilterBtn.contains(e.target)) {
            fsFilterDropdown.classList.remove('open');
            fsFilterBtn.classList.remove('open');
            fsFilterChevron.style.transform = '';
        }
    });

    /* ── Row arrow click ── */
    document.getElementById('fsTableBody').addEventListener('click', function (e) {
        if (e.target.closest('.fs-row-cb')) { e.stopPropagation(); return; }
        var arrow = e.target.closest('.fs-arrow-btn');
        var row   = e.target.closest('.fs-row');
        if (arrow || row) {
            window.location.href = '#'; // replace with actual route
        }
    });

    /* init */
    updateBtns();

    /* ── Submission Detail Canvas ── */
    var fsCanvas        = document.getElementById('fsCanvas');
    var fsCanvasOverlay = document.getElementById('fsCanvasOverlay');

    /* Demo data with multiple sections per submission */
    var demoData = {
        'Kelly Hall': {
            sections: [
                {
                    title: 'Hard Credit App (Single Borrower)',
                    fields: [
                        { label: 'Vehicle',                             value: '2019 INFINITI QX50 ESSENTIAL (5086)' },
                        { label: 'First Name',                          value: 'Kelly' },
                        { label: 'Last Name',                           value: 'Hall' },
                        { label: 'Email Address',                       value: 'kellyahall1971@gmail.com' },
                        { label: 'Phone Number',                        value: '(931) 265-1427' },
                        { label: 'What is the best way to contact you?',value: 'Email' },
                    ]
                },
                {
                    title: 'Hard Credit App (Single - Address)',
                    fields: [
                        { label: 'Street Address',                      value: '516 Bowerwood Circle' },
                        { label: 'City',                                value: 'Cookeville' },
                        { label: 'State',                               value: 'TN' },
                        { label: 'Zip Code',                            value: '38501' },
                        { label: 'Type of Residence',                   value: 'Other' },
                        { label: 'How many years have you lived here?', value: '5' },
                        { label: 'Monthly rent/mortgage payment',       value: '0' },
                    ]
                },
                {
                    title: 'Hard Credit App (Single - Employment)',
                    fields: [
                        { label: 'Employer Name',                       value: 'Adapthealth' },
                        { label: 'Job Title',                           value: 'Intake Coordinator' },
                        { label: 'Employer Phone Number',               value: '(470) 235-0113' },
                        { label: 'Monthly income',                      value: '2300' },
                        { label: 'Years worked here',                   value: '6' },
                        { label: 'Months worked here',                  value: '1' },
                        { label: 'Do you have another source of income',value: 'N' },
                    ]
                },
                {
                    title: 'Hard Credit App (Single - Consent + SSN/DOB)',
                    fields: [
                        { label: 'Electronic Signature',                value: 'Kelly A Hall' },
                        { label: 'Social Security Number',              value: '414-49-0781' },
                        { label: 'Date of Birth',                       value: '7/23/1971' },
                        { label: 'Authorization / consent checkbox',    value: '1' },
                    ]
                },
            ],
            customer: {
                ip: '97.81.178.6', location: '--', device: 'Desktop',
                brand: '--', model: '--', client: 'Chrome',
                os: 'Windows', osVersion: '10', referer: 'google.com',
                classification: 'organic search', utmSource: '--',
                utmCampaign: '--', utmTerm: '--', utmContent: '--',
                visitDuration: '19.7 min',
            }
        },
        'Devin Farlow': {
            sections: [
                {
                    title: 'Hard Credit App (Single Borrower)',
                    fields: [
                        { label: 'First Name',                          value: 'Devin' },
                        { label: 'Last Name',                           value: 'Farlow' },
                        { label: 'Email Address',                       value: 'plusonehandyman@myyahoo.com' },
                        { label: 'Phone Number',                        value: '(615) 836-9265' },
                        { label: 'What is the best way to contact you?',value: 'Text' },
                    ]
                },
            ],
            customer: {
                ip: '166.194.158.25', location: '--', device: 'Smartphone',
                brand: 'Apple', model: 'iPhone', client: 'Google Search App',
                os: 'iOS', osVersion: '26.3.0', referer: '--',
                classification: 'direct', utmSource: '--',
                utmCampaign: '--', utmTerm: '--', utmContent: '--',
                visitDuration: '24.9 min',
            }
        }
    };

    function buildSections(sections) {
        var html = '';
        sections.forEach(function (sec, idx) {
            /* Section card */
            html += '<div class="fs-form-card">';
            html += '<div class="fs-form-type-row">' + sec.title + '</div>';
            sec.fields.forEach(function (f) {
                html +=
                    '<div class="fs-form-field-row">' +
                    '<span class="fs-form-field-label">' + f.label + '</span>' +
                    '<span class="fs-form-field-value">'  + f.value + '</span>' +
                    '</div>';
            });
            html += '</div>';
            /* Arrow between sections, not after last */
            if (idx < sections.length - 1) {
                html += '<div class="fs-section-arrow"><i class="bi bi-arrow-down"></i></div>';
            }
        });
        /* Final arrow + ended */
        html += '<div class="fs-section-arrow"><i class="bi bi-arrow-down"></i></div>';
        html += '<div class="fs-form-ended">Form submission ended.</div>';
        return html;
    }

    function openCanvas(name, type) {
        var data = demoData[name];

        if (!data) {
            /* Fallback for rows not in demoData */
            data = {
                sections: [{
                    title: type || '—',
                    fields: [
                        { label: 'First Name', value: name.split(' ')[0] || '—' },
                        { label: 'Last Name',  value: name.split(' ')[1] || '—' },
                        { label: 'Email Address', value: '—' },
                        { label: 'Phone Number',  value: '—' },
                    ]
                }],
                customer: {
                    ip: '—', location: '--', device: '—', brand: '—',
                    model: '—', client: '—', os: '—', osVersion: '—',
                    referer: '--', classification: '—', utmSource: '--',
                    utmCampaign: '--', utmTerm: '--', utmContent: '--',
                    visitDuration: '—',
                }
            };
        }

        /* Render sections */
        document.getElementById('fsFormSections').innerHTML = buildSections(data.sections);

        /* Render customer details */
        var c = data.customer;
        function set(id, val) {
            var el = document.getElementById(id);
            if (!el) return;
            el.textContent = val;
            el.className = 'fs-detail-value' + (val === '--' || val === '—' ? ' muted' : '');
        }
        set('fsDetIp',             c.ip);
        set('fsDetLocation',       c.location);
        set('fsDetDevice',         c.device);
        set('fsDetBrand',          c.brand);
        set('fsDetModel',          c.model);
        set('fsDetClient',         c.client);
        set('fsDetOs',             c.os);
        set('fsDetOsVersion',      c.osVersion);
        set('fsDetReferer',        c.referer);
        set('fsDetClassification', c.classification);
        set('fsDetUtmSource',      c.utmSource);
        set('fsDetUtmCampaign',    c.utmCampaign);
        set('fsDetUtmTerm',        c.utmTerm);
        set('fsDetUtmContent',     c.utmContent);
        set('fsDetVisitDuration',  c.visitDuration);

        /* Scroll body back to top */
        document.querySelector('.fs-canvas-body').scrollTop = 0;

        /* Open */
        fsCanvasOverlay.classList.add('open');
        fsCanvas.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeCanvas() {
        fsCanvas.classList.remove('open');
        fsCanvasOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.getElementById('fsCanvasClose').addEventListener('click', closeCanvas);
    fsCanvasOverlay.addEventListener('click', closeCanvas);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeCanvas();
    });

    /* Wire arrow buttons and row clicks */
    document.getElementById('fsTableBody').addEventListener('click', function (e) {
        if (e.target.closest('.fs-row-cb')) { e.stopPropagation(); return; }
        var row = e.target.closest('.fs-row');
        if (!row) return;
        var name = row.querySelector('td:nth-child(2)').textContent.trim();
        var type = row.querySelector('td:nth-child(3)').textContent.trim();
        openCanvas(name, type);
    });

})();
</script>
@endpush