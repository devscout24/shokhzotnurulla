@extends('layouts.dealer.app')

@section('title', __('Reusable Content: Event') . ' | ' . __(config('app.name')))

@push('page-styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Shared Dashboard Styles */
        .rc-wrapper {
            display: flex;
            gap: 40px;
            min-height: calc(100vh - 160px);
            padding: 20px 0;
            align-items: flex-start;
        }

        .rc-sidebar {
            width: 260px;
            min-width: 260px;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .rc-sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 25px;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
        }

        .rc-sidebar-item:last-child {
            border-bottom: none;
        }

        .rc-sidebar-item:hover {
            background: #f8f8f8;
            color: #333;
        }

        .rc-sidebar-item.active {
            background: #fff;
            color: #333;
            font-weight: 600;
        }

        .rc-sidebar-item i {
            font-size: 18px !important;
            width: 24px;
            text-align: center;
            color: #999;
        }

        .rc-sidebar-item.active i {
            color: #ce4f4b !important;
        }

        .rc-main-container {
            flex: 1;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .rc-header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .rc-btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ce4f4b;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background .2s;
        }

        .rc-btn-add:hover {
            background: #b33e3a;
        }

        .rc-btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e0e0e0;
            background: #fff;
            font-size: 13px;
            color: #555;
            cursor: pointer;
            padding: 8px 18px;
            border-radius: 8px;
            white-space: nowrap;
            transition: all .15s;
            font-weight: 500;
        }

        .rc-btn-outline:hover {
            background: #f8f9fa;
            border-color: #d0d0d0;
        }

        .rc-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rc-table th {
            padding: 18px 20px;
            font-weight: 700;
            color: #333;
            text-align: left;
            font-size: 12px;
            border-bottom: 1px solid #f0f0f0;
            background: #fff;
        }

        .rc-table td {
            padding: 25px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f8f9fa;
            background: #fff;
        }

        .rc-table tr:hover {
            background: #fafafa;
        }

        .rc-title-cell {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .rc-drag {
            color: #ddd;
            cursor: grab;
            font-size: 16px;
            padding-top: 2px;
        }

        .rc-nickname {
            font-size: 15px;
            font-weight: 500;
            color: #333;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .rc-row-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .rc-row-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border: 1px solid #eef0f2;
            border-radius: 4px;
            font-size: 11px;
            color: #444;
            background: #fff;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
            font-weight: 500;
        }

        .rc-row-btn:hover {
            background: #f8f9fa;
            color: #000;
            border-color: #d0d0d0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .rc-row-btn i {
            font-size: 14px;
        }

        .rc-row-btn.trash-btn {
            color: #ce4f4b;
        }

        .rc-row-btn.trash-btn:hover {
            background: #fff5f5;
            border-color: #f5c6cb;
        }

        /* Form Styles */
        .bulk-col-label {
            font-size: 11px;
            font-weight: 700;
            color: #444;
            margin-bottom: 6px;
            display: block;
            text-transform: none;
        }

        .bulk-col-label span {
            color: #ce4f4b;
            margin-left: 2px;
        }

        .bulk-input,
        .bulk-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #eef0f2;
            border-radius: 4px;
            font-size: 13px;
            color: #333;
            transition: all .2s;
            background: #fff;
            height: 38px;
        }

        .bulk-input:focus,
        .bulk-select:focus {
            border-color: #ce4f4b;
            outline: none;
            background: #fff;
            box-shadow: none;
        }

        .bulk-input::placeholder {
            color: #9da3a8;
        }

        /* RTE Enhanced Styles */
        .bulk-rte {
            border: 1px solid #eef0f2;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            transition: border-color 0.2s;
        }

        .bulk-rte:focus-within {
            border-color: #ce4f4b;
        }

        .rte-toolbar {
            background: #fff;
            border-bottom: 1px solid #eef0f2;
            padding: 8px 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .rte-tool-group {
            display: flex;
            gap: 2px;
            border-right: 1px solid #eee;
            padding-right: 6px;
            align-items: center;
        }

        .rte-tool-group:last-child {
            border-right: none;
            padding-right: 0;
        }

        .rte-btn {
            background: none;
            border: 1px solid transparent;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            color: #555;
            cursor: pointer;
            transition: all .2s;
        }

        .rte-btn:hover {
            background: #f8f9fa;
            color: #ce4f4b;
            border-color: #eee;
        }

        .rte-btn.active {
            background: #fff5f5;
            color: #ce4f4b;
            border-color: #f5c6cb;
        }

        .rte-select-format {
            height: 32px;
            padding: 0 10px;
            border: 1px solid #eee;
            border-radius: 4px;
            font-size: 13px;
            color: #444;
            font-weight: 600;
            background: #fff;
            cursor: pointer;
            outline: none;
        }

        .rte-select-format:hover {
            border-color: #ddd;
        }

        .rte-textarea {
            width: 100%;
            border: none;
            padding: 20px;
            min-height: 180px;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            outline: none;
            background: #fcfdfe;
        }

        .rte-textarea:focus {
            background: #fff;
        }

        .btn-save-red {
            background: #ce4f4b;
            color: #fff;
            border: none;
            padding: 8px 25px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background .2s;
        }

        .btn-save-red:hover {
            background: #b33e3a;
        }

        /* Modals */
        .faq-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        .faq-modal-overlay.open {
            display: flex;
        }

        .faq-modal {
            background: #fff;
            border-radius: 12px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .confirm-modal-header {
            padding: 15px 25px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .confirm-modal-title {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .confirm-modal-title i {
            color: #f39c12;
            font-size: 18px;
        }

        .confirm-modal-body {
            padding: 30px 25px;
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }

        .confirm-modal-footer {
            padding: 15px 25px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #fff;
        }

        /* Custom Dropdown for Filter */
        .rc-dropdown-wrap {
            position: relative;
        }

        .rc-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 100;
            min-width: 200px;
            margin-top: 8px;
            overflow: hidden;
        }

        .rc-dropdown-menu.open {
            display: block;
        }

        .rc-dropdown-item {
            padding: 12px 20px;
            font-size: 13px;
            color: #555;
            cursor: pointer;
            transition: all .15s;
        }

        .rc-dropdown-item:hover {
            background: #f8f9fa;
            color: #333;
        }

        .rc-dropdown-item.active {
            background: #f0f2f5;
            color: #000;
            font-weight: 600;
        }

        /* Bulk Edit Styles - Modal Style */
        .bulk-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        .bulk-overlay.open {
            display: flex;
        }

        .bulk-modal {
            background: #f0f2f5;
            width: 98%;
            max-width: 1700px;
            height: 95vh;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .bulk-header {
            padding: 20px 30px;
            border-bottom: 1px solid #eef0f2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }

        .bulk-body {
            flex: 1;
            overflow: auto;
            padding: 30px;
        }

        .bulk-footer {
            padding: 15px 30px;
            border-top: 1px solid #eef0f2;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            background: #fff;
        }

        .bulk-container {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow-x: auto;
            position: relative;
        }

        .bulk-table {
            width: max-content;
            min-width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .bulk-table th {
            background: #fff;
            padding: 18px 15px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #f0f0f0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .bulk-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #f8f9fa;
            vertical-align: top;
            background: #fff;
        }

        .bulk-edit-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #eef0f2;
            border-radius: 6px;
            font-size: 13px;
            color: #333;
            background: #fcfdfe;
            transition: all .2s;
        }

        .bulk-edit-input:focus {
            border-color: #ce4f4b;
            outline: none;
            background: #fff;
        }

        .bulk-rte-wrap {
            width: 100%;
            border: 1px solid #eef0f2;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .bulk-rte-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            padding: 6px 10px;
            background: #fff;
            border-bottom: 1px solid #eef0f2;
        }

        .bulk-rte-content {
            padding: 15px;
            min-height: 120px;
            max-height: 300px;
            overflow-y: auto;
            font-size: 13px;
            line-height: 1.6;
            color: #444;
            outline: none;
            background: #fcfdfe;
        }

        .bulk-rte-content:focus {
            background: #fff;
        }

        .toaster-container {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 5000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toaster {
            background: #00b612;
            color: #fff;
            padding: 16px 40px 16px 20px;
            border-radius: 6px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            transition: transform .4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            position: relative;
            font-weight: 500;
            font-size: 14px;
            min-width: 250px;
        }

        .toaster.show {
            transform: translateX(0);
        }

        .toaster i {
            font-size: 20px;
            color: #fff;
        }

        .toaster-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #fff;
            position: absolute;
            top: 14px;
            right: 15px;
            opacity: 0.8;
            padding: 0;
            line-height: 1;
        }

        .toaster-close:hover {
            opacity: 1;
        }

        .status-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #666;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ccc;
        }

        .status-dot.active {
            background: #28a745;
        }

        .status-dot.inactive {
            background: #9da3a8;
        }

        /* Media Row / Preview */
        .media-row {
            margin-bottom: 30px;
            position: relative;
        }

        .media-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .media-btn-select {
            font-size: 12px;
            font-weight: 600;
            color: #ce4f4b;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .media-preview-wrap {
            margin-top: 15px;
            display: none;
            position: relative;
            width: fit-content;
        }

        .media-preview-wrap img {
            max-width: 400px;
            max-height: 250px;
            border-radius: 4px;
            border: 1px solid #eee;
        }

        .media-preview-remove {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ce4f4b;
            color: #fff;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            border: 2px solid #fff;
        }

        /* New Category Modal Styles */
        .cat-modal-body {
            padding: 25px;
            max-height: 400px;
            overflow-y: auto;
        }

        .cat-item-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 8px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cat-item-card:hover {
            border-color: #ce4f4b;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .cat-item-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cat-item-name {
            font-size: 15px;
            font-weight: 500;
            color: #333;
        }

        .cat-item-count {
            background: #f0f2f5;
            color: #999;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .cat-item-chevron {
            color: #ccc;
            font-size: 18px;
        }

        .cat-modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            background: #fff;
        }
    </style>
@endpush

@section('page-content')
    <main class="main-content" id="mainContent" style="padding:0; background: #f0f2f5; min-height: 100vh;">
        <div style="padding: 30px 45px;">
            <div class="page-header" style="margin-bottom: 25px; border: none; background: transparent; padding: 0;">
                <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222;">
                    {{ __('Reusable Content: Event') }}</h2>
                <div class="rc-header-actions">
                    <div class="rc-dropdown-wrap">
                        <button class="rc-btn-outline" type="button" id="filterBtn"><i class="bi bi-funnel"></i> Filter by
                            Category <i class="bi bi-chevron-down" style="font-size: 10px; margin-left: 5px;"></i></button>
                        <div class="rc-dropdown-menu" id="filterMenu">
                            <div class="rc-dropdown-item active" data-id="All">All Categories</div>
                        </div>
                    </div>
                    <button class="rc-btn-outline" type="button" id="manageCatBtn"><i class="bi bi-folder2-open"></i>
                        {{ __('Manage Categories') }}</button>
                    <button class="rc-btn-outline" type="button" id="bulkEditBtn"><i class="bi bi-pencil-square"></i>
                        {{ __('Bulk Edit') }}</button>
                    <button class="rc-btn-add" type="button" id="addBtn"><i class="bi bi-plus-lg"></i>
                        {{ __('Add New Event') }}</button>
                </div>
            </div>

            <div class="rc-wrapper">
                @include('dealer.partials.reusable-content-sidebar')

                <div style="flex: 1; display: flex; flex-direction: column;">
                    {{-- List View --}}
                    <div id="listView">
                        <div class="rc-main-container">
                            <table class="rc-table" id="eventTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Title</th>
                                        <th style="width: 15%;">Category</th>
                                        <th style="width: 20%;">Author</th>
                                        <th style="width: 15%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="eventTableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Add/Edit Form View --}}
                    <div id="formView" style="display: none;">
                        <div style="margin-bottom: 25px;">
                            <button class="rc-btn-outline" id="backBtn"
                                style="border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 25px;"><i
                                    class="bi bi-chevron-left"></i> Go Back</button>
                        </div>
                        <div class="rc-main-container" style="border: 1px solid #e0e0e0; border-radius: 8px;">
                            <div
                                style="padding: 15px 30px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                                <h3 style="font-size: 14px; font-weight: 700; color: #333; margin: 0;" id="formViewTitle">
                                    Add Event</h3>
                            </div>
                            <div style="padding: 30px; background: #fff;">

                                <div style="margin-bottom: 30px;">
                                    <label class="bulk-col-label">Event Title <span>*</span></label>
                                    <input type="text" id="eventTitleInput" class="bulk-input"
                                        placeholder="Cars & Coffee">
                                </div>

                                <div style="margin-bottom: 30px;">
                                    <label class="bulk-col-label">Category <span>*</span></label>
                                    <select id="categorySelect" class="bulk-select"></select>
                                </div>

                                <div class="media-row">
                                    <div class="media-header">
                                        <label class="bulk-col-label" style="margin: 0;">Event Image (600x300) <span>*</span></label>
                                        <span class="media-btn-select" id="selectMediaBtn" style="color: #ce4f4b;"><i
                                                class="bi bi-images"></i> Select Media</span>
                                    </div>
                                    <input type="text" id="photoUrlInput" class="bulk-input"
                                        placeholder="https://path.to.image/event.jpg">
                                    <div class="media-preview-wrap" id="photoPreviewWrap">
                                        <img src="" id="photoPreview">
                                        <span class="media-preview-remove" id="photoRemoveBtn">&times;</span>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                                    <div style="flex: 1;">
                                        <label class="bulk-col-label">Event Detail Link</label>
                                        <input type="text" id="detailLinkInput" class="bulk-input" placeholder="https://">
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="bulk-col-label">Event Registration Link</label>
                                        <input type="text" id="registrationLinkInput" class="bulk-input" placeholder="https://">
                                    </div>
                                </div>

                                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                                    <div style="flex: 1;">
                                        <label class="bulk-col-label">Event Date <span>*</span></label>
                                        <input type="date" id="eventDateInput" class="bulk-input">
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="bulk-col-label">Start Time <span>*</span></label>
                                        <input type="time" id="startTimeInput" class="bulk-input">
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="bulk-col-label">End Time <span>*</span></label>
                                        <input type="time" id="endTimeInput" class="bulk-input">
                                    </div>
                                </div>

                                <div style="margin-bottom: 40px;">
                                    <label class="bulk-col-label">Description <span>*</span></label>
                                    <div class="bulk-rte">
                                        <div class="rte-toolbar">
                                            <div class="rte-tool-group">
                                                <select class="rte-select-format" data-cmd="formatBlock">
                                                    <option value="p">Normal</option>
                                                    <option value="h2">Heading 2</option>
                                                    <option value="h3">Heading 3</option>
                                                    <option value="h4">Heading 4</option>
                                                </select>
                                            </div>
                                            <div class="rte-tool-group">
                                                <button class="rte-btn" type="button" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
                                                <button class="rte-btn" type="button" data-cmd="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
                                                <button class="rte-btn" type="button" data-cmd="underline" title="Underline"><i class="bi bi-type-underline"></i></button>
                                            </div>
                                            <div class="rte-tool-group">
                                                <button class="rte-btn" type="button" data-cmd="justifyLeft" title="Align Left"><i class="bi bi-text-left"></i></button>
                                                <button class="rte-btn" type="button" data-cmd="justifyCenter" title="Align Center"><i class="bi bi-text-center"></i></button>
                                                <button class="rte-btn" type="button" data-cmd="justifyRight" title="Align Right"><i class="bi bi-text-right"></i></button>
                                            </div>
                                            <div class="rte-tool-group">
                                                <button class="rte-btn" type="button" data-cmd="insertUnorderedList" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                                <button class="rte-btn" type="button" data-cmd="insertOrderedList" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                                <button class="rte-btn" type="button" data-cmd="outdent" title="Decrease Indent"><i class="bi bi-text-indent-left"></i></button>
                                                <button class="rte-btn" type="button" data-cmd="indent" title="Increase Indent"><i class="bi bi-text-indent-right"></i></button>
                                            </div>
                                            <div class="rte-tool-group">
                                                <button class="rte-btn" type="button" data-cmd="createLink" title="Insert Link"><i class="bi bi-link-45deg"></i></button>
                                                <button class="rte-btn" type="button" data-cmd="unlink" title="Remove Link"><i class="bi bi-link"></i></button>
                                            </div>
                                        </div>
                                        <div contenteditable="true" id="descInput" class="rte-textarea"></div>
                                    </div>
                                </div>

                                <button class="btn-save-red" id="saveBtn" style="padding: 12px 40px; border-radius: 8px;">
                                    <i class="bi bi-check-lg"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Modals --}}
    <div class="faq-modal-overlay" id="catModalOverlay">
        <div class="faq-modal" style="width: 550px;">
            <div
                style="padding: 20px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 700;" id="catModalTitle">Manage Categories</h3>
                <button style="background:none; border:none; font-size:24px; cursor:pointer; color: #999;"
                    id="catModalClose">&times;</button>
            </div>
            <div id="catModalContent">
                <div class="cat-modal-body" id="catModalBody"></div>
                <div class="cat-modal-footer" id="catModalFooter">
                    <button class="rc-btn-add" id="catAddBtn"><i class="bi bi-plus-lg"></i> Add Category</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirm Modal --}}
    <div class="faq-modal-overlay" id="confirmModalOverlay">
        <div class="faq-modal" style="width: 450px;">
            <div class="confirm-modal-header">
                <div class="confirm-modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Are you sure?</div>
                <button style="background:none; border:none; font-size:20px; cursor:pointer; color:#999;"
                    id="confirmModalCloseBtn">&times;</button>
            </div>
            <div class="confirm-modal-body" id="confirmModalBodyText">Are you sure you want to delete this event?</div>
            <div class="confirm-modal-footer">
                <button class="rc-btn-outline" style="padding: 8px 20px;" id="confirmCancelBtn">Cancel</button>
                <button class="btn-save-red" style="padding: 8px 20px; background: #ce4f4b;"
                    id="confirmContinueBtn">Continue</button>
            </div>
        </div>
    </div>

    {{-- Bulk Edit Overlay --}}
    <div class="bulk-overlay" id="bulkOverlay">
        <div class="bulk-modal">
            <div class="bulk-header">
                <h2 style="font-size: 18px; font-weight: 700; margin: 0; color: #333;">Bulk Edit Events</h2>
                <button style="background:none; border:none; font-size:24px; cursor:pointer; color:#999;"
                    id="bulkEditCloseBtn">&times;</button>
            </div>
            <div class="bulk-body">
                <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px;">
                    <button class="rc-btn-outline" id="addBulkRowBtn"><i class="bi bi-plus-lg"></i> Add Row</button>
                </div>
                <div class="bulk-container">
                    <table class="bulk-table" id="bulkTableMain">
                        <thead>
                            <tr>
                                <th style="width: 250px;">Event Title *</th>
                                <th style="width: 180px;">Category *</th>
                                <th style="width: 350px;">Event Image (600x300) *</th>
                                <th style="width: 200px;">Detail Link</th>
                                <th style="width: 200px;">Reg. Link</th>
                                <th style="width: 150px;">Date *</th>
                                <th style="width: 120px;">Start *</th>
                                <th style="width: 120px;">End *</th>
                                <th style="width: 500px;">Description *</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bulkTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="bulk-footer">
                <button class="rc-btn-outline" style="border:none; color:#666;" id="bulkEditCancelBtn">Cancel</button>
                <button class="btn-save-red" id="bulkEditSaveBtn" style="padding: 10px 30px;">Save</button>
            </div>
        </div>
    </div>

    {{-- Media Selector Modal --}}
    @include('dealer.partials.media-selector')

    <div class="toaster-container" id="toasterContainer"></div>

@endsection

@push('page-scripts')
    <script>
        (function() {
            var events = @json($events);
            var categories = @json($categories);
            var editingId = null;

            var ROUTES = {
                store: "{{ route('dealer.website.events.store') }}",
                update: "{{ route('dealer.website.events.update', '__ID__') }}",
                destroy: "{{ route('dealer.website.events.destroy', '__ID__') }}",
                bulkUpdate: "{{ route('dealer.website.events.bulk-update') }}",
                catStore: "{{ route('dealer.website.events.categories.store') }}",
                catUpdate: "{{ route('dealer.website.events.categories.update', '__ID__') }}",
                catDestroy: "{{ route('dealer.website.events.categories.destroy', '__ID__') }}"
            };

            function ajax(method, url, data, cb) {
                var xhr = new XMLHttpRequest();
                xhr.open(method, url);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                xhr.onload = function() {
                    var res = null;
                    try {
                        res = JSON.parse(xhr.responseText);
                    } catch (e) {}
                    if (xhr.status >= 200 && xhr.status < 300) cb(null, res);
                    else cb(res && res.message ? res.message : 'Error processing request');
                };
                xhr.send(data ? JSON.stringify(data) : null);
            }

            function showToaster(text) {
                var container = document.getElementById('toasterContainer');
                var t = document.createElement('div');
                t.className = 'toaster';
                t.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span>' + text +
                    '</span><button class="toaster-close">&times;</button>';
                container.appendChild(t);
                setTimeout(() => t.classList.add('show'), 10);
                var remove = () => {
                    t.classList.remove('show');
                    setTimeout(() => t.remove(), 400);
                };
                var timer = setTimeout(remove, 3000);
                t.querySelector('.toaster-close').onclick = () => {
                    clearTimeout(timer);
                    remove();
                };
            }

            var confirmOverlay = document.getElementById('confirmModalOverlay');
            var confirmCallback = null;

            function customConfirm(text, cb) {
                document.getElementById('confirmModalBodyText').textContent = text;
                confirmCallback = cb;
                confirmOverlay.classList.add('open');
            }
            document.getElementById('confirmCancelBtn').onclick = () => confirmOverlay.classList.remove('open');
            document.getElementById('confirmModalCloseBtn').onclick = () => confirmOverlay.classList.remove('open');
            document.getElementById('confirmContinueBtn').onclick = () => {
                if (confirmCallback) confirmCallback();
                confirmOverlay.classList.remove('open');
            };

            function getDisplayStatus(b) {
                return b.status === 'Inactive' ? 'Draft' : 'Active';
            }

            window._trash = function(id) {
                customConfirm('Are you sure you want to delete this event?', function() {
                    ajax('DELETE', ROUTES.destroy.replace('__ID__', id), null, function(err) {
                        if (err) return alert(err);
                        events = events.filter(r => r.id != id);
                        renderTable();
                        showToaster('Event deleted.');
                    });
                });
            };

            function renderTable() {
                var catFilter = selectedCategoryId;
                var tbody = document.getElementById('eventTableBody');
                var html = '';
                var filtered = events.filter(b => (catFilter === 'All' || b.event_category_id == catFilter));

                filtered.forEach(b => {
                    var status = getDisplayStatus(b);
                    html += `<tr>
                <td><div class="rc-title-cell"><span class="rc-drag"><i class="bi bi-list"></i></span>
                <div class="rc-content-wrap"><div class="rc-nickname">${b.title}</div>
                <div class="rc-row-actions">
                    <a href="#edit/${b.id}" class="rc-row-btn" onclick="openForm(${b.id})"><i class="bi bi-pencil-square"></i> Edit</a>
                    <button class="rc-row-btn trash-btn" onclick="window._trash(${b.id})"><i class="bi bi-trash"></i> Trash</button>
                </div></div></div></td>
                <td><span style="color:#666; font-size:14px;">${b.category?b.category.name:'Uncategorized'}</span></td>
                <td><span style="color:#666; font-size:14px;">${b.author||'System'}</span></td>
                <td><div class="status-wrap"><span class="status-dot ${status === 'Active' ? 'active' : 'inactive'}"></span><span style="font-size:14px;">${status === 'Active' ? 'Published' : status}</span></div></td>
            </tr>`;
                });
                tbody.innerHTML = html ||
                    '<tr><td colspan="4" style="text-align:center;padding:50px;color:#999;">No results found.</td></tr>';
            }

            var selectedCategoryId = 'All';
            document.getElementById('filterBtn').onclick = function(e) {
                e.stopPropagation();
                document.getElementById('filterMenu').classList.toggle('open');
            };
            document.addEventListener('click', function() {
                document.getElementById('filterMenu').classList.remove('open');
            });

            function populateFilters() {
                var menu = document.getElementById('filterMenu');
                var html = '<div class="rc-dropdown-item ' + (selectedCategoryId === 'All' ? 'active' : '') +
                    '" data-id="All">All Categories</div>';
                categories.forEach(function(c) {
                    html += '<div class="rc-dropdown-item ' + (selectedCategoryId == c.id ? 'active' : '') +
                        '" data-id="' + c.id + '">' + c.name + '</div>';
                });
                menu.innerHTML = html;

                menu.querySelectorAll('.rc-dropdown-item').forEach(function(item) {
                    item.onclick = function() {
                        selectedCategoryId = this.dataset.id;
                        document.getElementById('filterBtn').innerHTML = '<i class="bi bi-funnel"></i> ' + (
                                selectedCategoryId === 'All' ? 'Filter by Category' : this.textContent) +
                            ' <i class="bi bi-chevron-down" style="font-size: 10px; margin-left: 5px;"></i>';
                        populateFilters();
                        renderTable();
                    };
                });
            }

            function navigate() {
                var h = window.location.hash;
                if (h === '#add') openForm(null);
                else if (h.indexOf('#edit/') === 0) openForm(parseInt(h.replace('#edit/', '')));
                else showList();
            }

            function showList() {
                document.getElementById('listView').style.display = 'block';
                document.getElementById('formView').style.display = 'none';
                if (window.location.hash !== '#list') history.replaceState(null, null, '#list');
            }
            document.getElementById('addBtn').onclick = function() {
                window.location.hash = '#add';
            };
            document.getElementById('backBtn').onclick = function() {
                window.location.hash = '#list';
            };
            window.onhashchange = navigate;

            window.openForm = function(id) {
                editingId = id;
                var item = id ? events.find(function(b) {
                    return b.id == id;
                }) : null;
                document.getElementById('formViewTitle').textContent = item ? 'Edit Event' : 'Add Event';

                document.getElementById('eventTitleInput').value = item ? item.title : '';
                document.getElementById('photoUrlInput').value = item ? item.photo_url : '';
                document.getElementById('detailLinkInput').value = item ? (item.detail_link || '') : '';
                document.getElementById('registrationLinkInput').value = item ? (item.registration_link || '') : '';
                document.getElementById('eventDateInput').value = item ? (item.event_date || '') : '';
                document.getElementById('startTimeInput').value = item ? (item.start_time || '') : '';
                document.getElementById('endTimeInput').value = item ? (item.end_time || '') : '';
                document.getElementById('descInput').innerHTML = item ? (item.description || '') : '';

                updatePhotoPreview('photoUrlInput', 'photoPreviewWrap', 'photoPreview');

                var catSel = document.getElementById('categorySelect');
                catSel.innerHTML = '<option value="">Uncategorized</option>';
                categories.forEach(function(c) {
                    catSel.innerHTML += '<option value="' + c.id + '"' + (item && item
                            .event_category_id == c.id ? ' selected' : '') + '>' + c.name +
                        '</option>';
                });

                document.getElementById('listView').style.display = 'none';
                document.getElementById('formView').style.display = 'block';
                
                // Init RTE
                document.querySelectorAll('.bulk-rte').forEach(rte => initRTE(rte));
            }

            function updatePhotoPreview(inputId, wrapId, previewId) {
                var val = document.getElementById(inputId).value;
                var wrap = document.getElementById(wrapId);
                var img = document.getElementById(previewId);
                if (val) {
                    img.src = val;
                    wrap.style.display = 'block';
                } else {
                    wrap.style.display = 'none';
                }
            }

            document.getElementById('photoRemoveBtn').onclick = function() {
                document.getElementById('photoUrlInput').value = '';
                updatePhotoPreview('photoUrlInput', 'photoPreviewWrap', 'photoPreview');
            };

            document.getElementById('selectMediaBtn').onclick = function() {
                window.openMediaSelector('photoUrlInput', 'photoPreviewWrap', 'photoPreview');
            };

            document.getElementById('saveBtn').onclick = function() {
                var p = {
                    title: document.getElementById('eventTitleInput').value.trim(),
                    photo_url: document.getElementById('photoUrlInput').value.trim(),
                    detail_link: document.getElementById('detailLinkInput').value.trim(),
                    registration_link: document.getElementById('registrationLinkInput').value.trim(),
                    event_date: document.getElementById('eventDateInput').value,
                    start_time: document.getElementById('startTimeInput').value,
                    end_time: document.getElementById('endTimeInput').value,
                    description: document.getElementById('descInput').innerHTML.trim(),
                    event_category_id: document.getElementById('categorySelect').value,
                    status: 'Active'
                };

                if (!p.title) return alert('Event Title is required');
                if (!p.event_category_id) return alert('Category is required');
                if (!p.event_date) return alert('Event Date is required');
                if (!p.start_time) return alert('Start Time is required');
                if (!p.end_time) return alert('End Time is required');
                if (!p.description) return alert('Description is required');

                ajax(editingId ? 'PATCH' : 'POST', editingId ? ROUTES.update.replace('__ID__', editingId) : ROUTES
                    .store, p,
                    function(err, res) {
                        if (err) return alert(err);
                        if (editingId) {
                            var idx = events.findIndex(function(b) {
                                return b.id == editingId;
                            });
                            if (idx > -1) events[idx] = res;
                        } else events.push(res);
                        window.location.hash = '#list';
                        renderTable();
                        showToaster('Event saved.');
                    });
            };

            // [Category Management]
            var catOverlay = document.getElementById('catModalOverlay');
            document.getElementById('manageCatBtn').onclick = showCatList;
            document.getElementById('catModalClose').onclick = function() {
                catOverlay.classList.remove('open');
            };

            window.showCatList = showCatList;

            function showCatList() {
                var body = document.getElementById('catModalBody');
                var footer = document.getElementById('catModalFooter');
                footer.style.display = 'flex';
                
                var html = '';
                categories.forEach(function(c) {
                    var count = events.filter(e => e.event_category_id == c.id).length;
                    html += `
                        <div class="cat-item-card" onclick="window._editCat(${c.id})">
                            <div class="cat-item-info">
                                <span class="cat-item-name">${c.name}</span>
                                <span class="cat-item-count">${count}</span>
                            </div>
                            <i class="bi bi-chevron-right cat-item-chevron"></i>
                        </div>
                    `;
                });
                
                body.innerHTML = html || '<div style="text-align:center; padding:30px; color:#999;">No categories found.</div>';
                catOverlay.classList.add('open');
                
                document.getElementById('catAddBtn').onclick = function() {
                    showCatEdit(null);
                };
            }

            window._editCat = function(id) {
                var c = categories.find(function(x) {
                    return x.id == id;
                });
                showCatEdit(c);
            };
            window._delCat = function(id) {
                customConfirm('Delete category? All events in this category will become uncategorized.', function() {
                    ajax('DELETE', ROUTES.catDestroy.replace('__ID__', id), null, function(err) {
                        if (err) return alert(err);
                        categories = categories.filter(function(x) {
                            return x.id !== id;
                        });
                        showCatList();
                        populateFilters();
                        showToaster('Category deleted.');
                    });
                });
            };

            function showCatEdit(cat) {
                var isNew = !cat;
                var body = document.getElementById('catModalBody');
                var footer = document.getElementById('catModalFooter');
                footer.style.display = 'none';

                body.innerHTML = `
                    <div style="padding: 10px 5px;">
                        <div style="margin-bottom: 25px;">
                            <label class="bulk-col-label">Category Name</label>
                            <input type="text" id="catNameInput" class="bulk-input" value="${isNew ? '' : cat.name}" placeholder="e.g. Community Events">
                        </div>
                        <div style="display:flex; justify-content: space-between; align-items:center;">
                            <div style="display:flex; gap:10px;">
                                <button class="btn-save-red" id="catSaveBtn" style="padding: 10px 30px;">${isNew ? 'Create Category' : 'Save Changes'}</button>
                                <button class="rc-btn-outline" onclick="showCatList()" style="padding: 10px 25px;">Cancel</button>
                            </div>
                            ${!isNew ? `<button class="rc-row-btn trash-btn" onclick="window._delCat(${cat.id})" style="padding: 8px 15px;"><i class="bi bi-trash"></i> Delete</button>` : ''}
                        </div>
                    </div>
                `;

                document.getElementById('catSaveBtn').onclick = function() {
                    var val = document.getElementById('catNameInput').value.trim();
                    if (!val) return alert('Category name is required');
                    ajax(isNew ? 'POST' : 'PATCH', isNew ? ROUTES.catStore : ROUTES.catUpdate.replace('__ID__', cat.id), {
                        name: val
                    }, function(err, res) {
                        if (err) return alert(err);
                        if (isNew) categories.push(res);
                        else {
                            var idx = categories.findIndex(function(x) {
                                return x.id == cat.id;
                            });
                            categories[idx] = res;
                        }
                        showCatList();
                        populateFilters();
                        showToaster('Category saved.');
                    });
                };
            }

            // [Bulk Edit]
            var bulkOverlay = document.getElementById('bulkOverlay');
            var bulkTableBody = document.getElementById('bulkTableBody');

            document.getElementById('bulkEditBtn').onclick = openBulkEdit;
            document.getElementById('bulkEditCancelBtn').onclick = function() {
                bulkOverlay.classList.remove('open');
            };
            document.getElementById('bulkEditCloseBtn').onclick = function() {
                bulkOverlay.classList.remove('open');
            };
            bulkOverlay.onclick = function(e) {
                if (e.target === bulkOverlay) bulkOverlay.classList.remove('open');
            };
            document.getElementById('addBulkRowBtn').onclick = function() {
                addBulkRow(null);
            };

            function openBulkEdit() {
                bulkTableBody.innerHTML = '';
                if (events.length > 0) events.forEach(function(b) {
                    addBulkRow(b);
                });
                else addBulkRow(null);
                bulkOverlay.classList.add('open');
            }

            function addBulkRow(item) {
                var row = document.createElement('tr');
                row.dataset.id = item ? item.id : '';
                var rowId = 'row-' + Math.random().toString(36).substr(2, 9);

                var toolbarHtml = `
                    <div class="bulk-rte-toolbar">
                        <select class="rte-select-format" data-cmd="formatBlock" style="height:28px; font-size:11px;">
                            <option value="p">Normal</option>
                            <option value="h2">H2</option>
                            <option value="h3">H3</option>
                        </select>
                        <button class="rte-btn" type="button" data-cmd="bold" style="width:28px; height:28px;"><i class="bi bi-type-bold"></i></button>
                        <button class="rte-btn" type="button" data-cmd="italic" style="width:28px; height:28px;"><i class="bi bi-type-italic"></i></button>
                        <button class="rte-btn" type="button" data-cmd="underline" style="width:28px; height:28px;"><i class="bi bi-type-underline"></i></button>
                        <button class="rte-btn" type="button" data-cmd="justifyLeft" style="width:28px; height:28px;"><i class="bi bi-text-left"></i></button>
                        <button class="rte-btn" type="button" data-cmd="justifyCenter" style="width:28px; height:28px;"><i class="bi bi-text-center"></i></button>
                        <button class="rte-btn" type="button" data-cmd="insertUnorderedList" style="width:28px; height:28px;"><i class="bi bi-list-ul"></i></button>
                        <button class="rte-btn" type="button" data-cmd="createLink" style="width:28px; height:28px;"><i class="bi bi-link-45deg"></i></button>
                    </div>
                `;

                row.innerHTML = `
            <td><input type="text" class="bulk-edit-input bulk-title" value="${item ? item.title : ''}"></td>
            <td>
                <select class="bulk-edit-input bulk-category">
                    <option value="">[Select]</option>
                    ${categories.map(c => `<option value="${c.id}" ${item && item.event_category_id == c.id ? 'selected' : ''}>${c.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <div style="display:flex; flex-direction:column; gap:5px; width:320px;">
                    <div style="display:flex; gap:5px; align-items:center;">
                        <input type="text" id="${rowId}-input" class="bulk-edit-input bulk-photo" value="${item ? item.photo_url || '' : ''}">
                        <button class="rc-row-btn" style="padding:8px;" type="button" onclick="window.openMediaSelector('${rowId}-input', '${rowId}-preview-wrap', '${rowId}-preview')"><i class="bi bi-images"></i></button>
                    </div>
                    <div class="media-preview-wrap" style="width:100%; height:120px; display:${item && item.photo_url ? 'block' : 'none'};" id="${rowId}-preview-wrap">
                        <img id="${rowId}-preview" src="${item && item.photo_url ? item.photo_url : ''}" style="width:100%; height:100%; object-fit:cover; border-radius:4px;">
                    </div>
                </div>
            </td>
            <td><input type="text" class="bulk-edit-input bulk-detail-link" value="${item ? item.detail_link || '' : ''}"></td>
            <td><input type="text" class="bulk-edit-input bulk-reg-link" value="${item ? item.registration_link || '' : ''}"></td>
            <td><input type="date" class="bulk-edit-input bulk-date" value="${item ? item.event_date || '' : ''}"></td>
            <td><input type="time" class="bulk-edit-input bulk-start" value="${item ? item.start_time || '' : ''}"></td>
            <td><input type="time" class="bulk-edit-input bulk-end" value="${item ? item.end_time || '' : ''}"></td>
            <td>
                <div class="bulk-rte-wrap">
                    ${toolbarHtml}
                    <div contenteditable="true" class="bulk-rte-content bulk-desc">${item ? item.description || '' : ''}</div>
                </div>
            </td>
            <td><button class="rc-row-btn trash-btn" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
        `;
                bulkTableBody.appendChild(row);
                
                // Initialize RTE for the new row
                row.querySelectorAll('.bulk-rte-wrap').forEach(wrap => initRTE(wrap));
            }

            document.getElementById('bulkEditSaveBtn').onclick = function() {
                var rows = bulkTableBody.querySelectorAll('tr');
                var data = [];
                rows.forEach(function(row) {
                    var title = row.querySelector('.bulk-title').value.trim();
                    if (!title) return;
                    data.push({
                        id: row.dataset.id || null,
                        title: title,
                        photo_url: row.querySelector('.bulk-photo').value.trim(),
                        detail_link: row.querySelector('.bulk-detail-link').value.trim(),
                        registration_link: row.querySelector('.bulk-reg-link').value.trim(),
                        event_date: row.querySelector('.bulk-date').value,
                        start_time: row.querySelector('.bulk-start').value,
                        end_time: row.querySelector('.bulk-end').value,
                        description: row.querySelector('.bulk-desc').innerHTML,
                        event_category_id: row.querySelector('.bulk-category').value || null,
                        status: 'Active'
                    });
                });

                ajax('POST', ROUTES.bulkUpdate, {
                    events: data
                }, function(err, res) {
                    if (err) return alert(err);
                    events = res;
                    renderTable();
                    bulkOverlay.classList.remove('open');
                    showToaster('Bulk events saved.');
                });
            };

            // Global Media Integration
            var selectedMediaUrl = null,
                mediaPage = 1;
            window.openMediaSelector = function(inputId, wrapId, previewId) {
                window._currentMediaTarget = {
                    input: inputId,
                    wrap: wrapId,
                    preview: previewId
                };
                document.getElementById('mediaModalOverlay').classList.add('open');
                loadMedia(1);
            };

            document.getElementById('mediaModalClose').onclick = function() {
                document.getElementById('mediaModalOverlay').classList.remove('open');
            };

            var mediaSearchTimer = null;
            document.getElementById('mediaSearchInput').oninput = function() {
                var val = this.value;
                clearTimeout(mediaSearchTimer);
                mediaSearchTimer = setTimeout(function() {
                    loadMedia(1, val);
                }, 300);
            };

            document.getElementById('mediaDisplaySelect').onchange = function() {
                var g = document.getElementById('mediaGrid');
                g.className = 'media-grid ' + this.value.toLowerCase();
            };

            function loadMedia(page, search = '') {
                mediaPage = page;
                var s = search || document.getElementById('mediaSearchInput').value;
                var url = "{{ route('dealer.website.media.list') }}?page=" + page + "&search=" + s + "&type=image";
                ajax('GET', url, null, function(err, res) {
                    if (err) return alert(err);
                    renderMediaGrid(res.data);
                    renderMediaPagination(res);
                });
            }

            function renderMediaGrid(items) {
                var html = '';
                items.forEach(function(item) {
                    var dims = item.dimensions !== '—' ? item.dimensions : 'Unknown';
                    var size = item.size || 'Unknown';
                    var author = item.uploaded_by || 'System';
                    var title = item.title || item.original_name || 'Untitled';

                    html += '<div class="media-card" data-url="' + item.url + '">' +
                        '<img src="' + item.url + '" class="media-thumb">' +
                        '<div class="media-info">' +
                        '<div class="media-info-author"><b>By:</b> ' + author + '</div>' +
                        '<div class="media-info-dims"><b>Dimensions:</b> ' + dims + '</div>' +
                        '<div class="media-info-size"><b>Size:</b> ' + size + '</div>' +
                        '<div class="media-info-title"><b>Title:</b> ' + title + '</div>' +
                        '</div>' +
                        '</div>';
                });
                document.getElementById('mediaGrid').innerHTML = html ||
                    '<div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #999;">No media found.</div>';

                document.querySelectorAll('.media-card').forEach(function(card) {
                    card.onclick = function() {
                        document.querySelectorAll('.media-card').forEach(function(c) {
                            c.classList.remove('selected');
                        });
                        this.classList.add('selected');
                        selectedMediaUrl = this.dataset.url;
                        var btn = document.getElementById('confirmMediaBtn');
                        btn.style.opacity = '1';
                        btn.style.pointerEvents = 'auto';
                        btn.style.background = '#c0392b';
                    };
                });
            }

            function renderMediaPagination(res) {
                var html = '';
                var cur = res.current_page;
                var last = res.last_page;
                html += '<button class="pag-btn ' + (cur === 1 ? 'disabled' : '') + '" onclick="loadMedia(' + (cur -
                    1) + ')"><i class="bi bi-chevron-left"></i></button>';
                for (var i = 1; i <= last; i++) {
                    if (i === 1 || i === last || (i >= cur - 1 && i <= cur + 1)) {
                        html += '<button class="pag-btn ' + (i === cur ? 'active' : '') + '" onclick="loadMedia(' + i +
                            ')">' + i + '</button>';
                    } else if (i === cur - 2 || i === cur + 2) {
                        html += '<span class="pag-dots">...</span>';
                    }
                }
                html += '<button class="pag-btn ' + (cur === last ? 'disabled' : '') + '" onclick="loadMedia(' + (cur +
                    1) + ')"><i class="bi bi-chevron-right"></i></button>';
                document.getElementById('mediaPagination').innerHTML = html;
                document.getElementById('mediaPaginationInfo').textContent = 'Showing ' + res.from + ' to ' + res.to +
                    ' of ' + res.total;
            }

            document.getElementById('confirmMediaBtn').onclick = function() {
                if (selectedMediaUrl && window._currentMediaTarget) {
                    document.getElementById(window._currentMediaTarget.input).value = selectedMediaUrl;
                    var wrap = document.getElementById(window._currentMediaTarget.wrap);
                    var img = document.getElementById(window._currentMediaTarget.preview);
                    if (wrap && img) {
                        img.src = selectedMediaUrl;
                        wrap.style.display = 'block';
                    }
                    document.getElementById('mediaModalOverlay').classList.remove('open');
                }
            };

            populateFilters();
            navigate();
            renderTable();
        })();

        // RTE Initialization & Logic
        function initRTE(container) {
            if (!container) return;

            // Handle Buttons
            container.querySelectorAll('.rte-btn').forEach(btn => {
                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var cmd = this.dataset.cmd;
                    var val = null;
                    if (cmd === 'createLink') {
                        val = prompt('Enter URL:', 'https://');
                        if (!val) return;
                    }
                    document.execCommand(cmd, false, val);
                    updateToolbarState(container);
                };
            });

            // Handle Format Dropdown
            container.querySelectorAll('.rte-select-format').forEach(sel => {
                sel.onchange = function() {
                    document.execCommand('formatBlock', false, this.value);
                    updateToolbarState(container);
                };
            });

            // Update toolbar state on selection change
            var area = container.querySelector('.rte-textarea') || container.querySelector(
                '.bulk-rte-content');
            if (area) {
                area.addEventListener('keyup', () => updateToolbarState(container));
                area.addEventListener('mouseup', () => updateToolbarState(container));
                area.addEventListener('focus', () => updateToolbarState(container));
            }
        }

        function updateToolbarState(container) {
            container.querySelectorAll('.rte-btn').forEach(btn => {
                var cmd = btn.dataset.cmd;
                if (document.queryCommandState(cmd)) btn.classList.add('active');
                else btn.classList.remove('active');
            });

            // Update format dropdown
            var sel = container.querySelector('.rte-select-format');
            if (sel) {
                var block = document.queryCommandValue('formatBlock');
                if (block) {
                    var val = block.toLowerCase().replace('<', '').replace('>', '');
                    if (['h2', 'h3', 'h4', 'h5', 'h6'].includes(val)) sel.value = val;
                    else sel.value = 'p';
                }
            }
        }
    </script>
@endpush
