@extends('layouts.dealer.app')

@section('title', __('Reusable Content: Job') . ' | ' . __(config('app.name')))

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
            font-size: 12px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .bulk-col-label span {
            color: #ce4f4b;
        }

        .bulk-input,
        .bulk-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #eef0f2;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            transition: all .2s;
            background: #fcfdfe;
        }

        .bulk-input:focus,
        .bulk-select:focus {
            border-color: #ce4f4b;
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(208, 2, 27, 0.05);
        }

        .bulk-input::placeholder {
            color: #9da3a8;
        }

        .hint-box {
            font-size: 12px;
            color: #888;
            margin-top: 6px;
            line-height: 1.4;
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
            min-height: 200px;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            outline: none;
            background: #fcfdfe;
        }

        .rte-textarea:focus {
            background: #fff;
        }

        /* Bulk Edit Styles */
        .bulk-body {
            flex: 1;
            overflow: auto;
            padding: 20px;
            background: #f8f9fa;
        }

        .bulk-container {
            width: max-content;
            min-width: 100%;
        }

        .bulk-table {
            width: max-content;
            border-collapse: separate;
            border-spacing: 0 10px;
            min-width: 100%;
        }

        .bulk-table th {
            background: #fff;
            padding: 12px 15px;
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
            padding: 15px;
            background: #fff;
            border-top: 1px solid #eef0f2;
            border-bottom: 1px solid #eef0f2;
            vertical-align: top;
        }

        .bulk-table td:first-child {
            border-left: 1px solid #eef0f2;
            border-radius: 8px 0 0 8px;
        }

        .bulk-table td:last-child {
            border-right: 1px solid #eef0f2;
            border-radius: 0 8px 8px 0;
        }

        .bulk-rte-wrap {
            width: 450px;
            border: 1px solid #eef0f2;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .bulk-edit-rte-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            padding: 6px 10px;
            background: #fff !important;
            border-bottom: 1px solid #eef0f2 !important;
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

        .btn-save-red {
            background: #ce4f4b;
            color: #fff;
            border: none;
            padding: 12px 35px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
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
            width: 95%;
            max-width: 1400px;
            height: 90vh;
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
        }

        .bulk-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 100%;
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
        }

        .bulk-table td {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            vertical-align: top;
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
            width: 450px;
            border: 1px solid #eef0f2;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
        }

        .bulk-rte-toolbar {
            display: flex;
            gap: 5px;
            padding: 8px;
            background: #f9f9f9;
            border-bottom: 1px solid #eef0f2;
        }

        .bulk-rte-content {
            padding: 12px;
            min-height: 80px;
            font-size: 13px;
            line-height: 1.5;
            color: #444;
            outline: none;
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
    </style>
@endpush

@section('page-content')
    <main class="main-content" id="mainContent" style="padding:0; background: #f0f2f5; min-height: 100vh;">
        <div style="padding: 30px 45px;">
            <div class="page-header" style="margin-bottom: 25px; border: none; background: transparent; padding: 0;">
                <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222;">
                    {{ __('Reusable Content: Job') }}</h2>
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
                        {{ __('Add New Job') }}</button>
                </div>
            </div>

            <div class="rc-wrapper">
                @include('dealer.partials.reusable-content-sidebar')

                <div style="flex: 1; display: flex; flex-direction: column;">
                    {{-- List View --}}
                    <div id="listView">
                        <div class="rc-main-container">
                            <table class="rc-table" id="jobTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Title</th>
                                        <th style="width: 15%;">Category</th>
                                        <th style="width: 20%;">Author</th>
                                        <th style="width: 15%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="jobTableBody"></tbody>
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
                                style="padding: 20px 30px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                                <h3 style="font-size: 15px; font-weight: 700; color: #333; margin: 0;" id="formViewTitle">
                                    Add Job</h3>
                            </div>
                            <div style="padding: 30px; background: #fff;">
                                <div style="margin-bottom: 30px;">
                                    <label class="bulk-col-label">Job Title <span>*</span></label>
                                    <input type="text" id="jobTitleInput" class="bulk-input"
                                        placeholder="What is the question?">
                                </div>

                                <div style="margin-bottom: 30px;">
                                    <label class="bulk-col-label">Category <span>*</span></label>
                                    <select id="categorySelect" class="bulk-select"></select>
                                </div>

                                <div style="margin-bottom: 40px;">
                                    <label class="bulk-col-label">Job Description <span>*</span></label>
                                    <div class="bulk-rte">
                                        <div class="rte-toolbar">
                                            <div class="rte-tool-group">
                                                <select class="rte-select-format" data-cmd="formatBlock">
                                                    <option value="p">Normal</option>
                                                    <option value="h2">Heading 2</option>
                                                    <option value="h3">Heading 3</option>
                                                    <option value="h4">Heading 4</option>
                                                    <option value="h5">Heading 5</option>
                                                    <option value="h6">Heading 6</option>
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

                                <button class="btn-save-red" id="saveBtn"
                                    style="border-radius: 8px; padding: 12px 40px;"><i class="bi bi-check-lg"></i>
                                    Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Modals --}}
    <div class="faq-modal-overlay" id="catModalOverlay">
        <div class="faq-modal">
            <div
                style="padding: 20px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 700;" id="catModalTitle">Manage Categories</h3>
                <button style="background:none; border:none; font-size:24px; cursor:pointer;"
                    id="catModalClose">&times;</button>
            </div>
            <div id="catModalBody"></div>
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
            <div class="confirm-modal-body" id="confirmModalBodyText">Are you sure you want to delete this post?</div>
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
                <h2 style="font-size: 18px; font-weight: 700; margin: 0; color: #333;">Bulk Edit Job Posts</h2>
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
                                <th style="width: 300px;">Job Title *</th>
                                <th style="width: 250px;">Category *</th>
                                <th style="width: 500px;">Job Description *</th>
                                <th style="width: 80px;">Actions</th>
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

    <div class="toaster-container" id="toasterContainer"></div>

@endsection

@push('page-scripts')
    <script>
        (function() {
            var jobs = @json($jobs);
            var categories = @json($categories);
            var editingId = null;

            var ROUTES = {
                store: "{{ route('dealer.website.job-posts.store') }}",
                update: "{{ route('dealer.website.job-posts.update', '__ID__') }}",
                destroy: "{{ route('dealer.website.job-posts.destroy', '__ID__') }}",
                bulkUpdate: "{{ route('dealer.website.job-posts.bulk-update') }}",
                catStore: "{{ route('dealer.website.job-posts.categories.store') }}",
                catUpdate: "{{ route('dealer.website.job-posts.categories.update', '__ID__') }}",
                catDestroy: "{{ route('dealer.website.job-posts.categories.destroy', '__ID__') }}"
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
                customConfirm('Are you sure you want to delete this job?', function() {
                    ajax('DELETE', ROUTES.destroy.replace('__ID__', id), null, function(err) {
                        if (err) return alert(err);
                        jobs = jobs.filter(r => r.id != id);
                        renderTable();
                        showToaster('Job deleted successfully.');
                    });
                });
            };

            function renderTable() {
                var catFilter = selectedCategoryId;
                var tbody = document.getElementById('jobTableBody');
                var html = '';
                var filtered = jobs.filter(b => (catFilter === 'All' || b.job_post_category_id == catFilter));

                filtered.forEach(b => {
                    var status = getDisplayStatus(b);
                    html += `<tr>
                <td><div class="rc-title-cell"><span class="rc-drag"><i class="bi bi-list"></i></span>
                <div class="rc-content-wrap"><div class="rc-nickname">${b.job_title}</div>
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
                var item = id ? jobs.find(function(b) {
                    return b.id == id;
                }) : null;
                document.getElementById('formViewTitle').textContent = item ? 'Edit Job' : 'Add Job';
                document.getElementById('jobTitleInput').value = item ? item.job_title : '';
                document.getElementById('descInput').innerHTML = item ? (item.job_description || '') : '';

                var catSel = document.getElementById('categorySelect');
                catSel.innerHTML = '<option value="">Uncategorized</option>';
                categories.forEach(function(c) {
                    catSel.innerHTML += '<option value="' + c.id + '"' + (item && item
                        .job_post_category_id == c.id ? ' selected' : '') + '>' + c.name + '</option>';
                });

                document.getElementById('listView').style.display = 'none';
                document.getElementById('formView').style.display = 'block';

                // Initialize RTE
                document.querySelectorAll('.bulk-rte').forEach(rte => initRTE(rte));
            }

            document.getElementById('saveBtn').onclick = function() {
                var p = {
                    job_title: document.getElementById('jobTitleInput').value.trim(),
                    job_post_category_id: document.getElementById('categorySelect').value,
                    job_description: document.getElementById('descInput').innerHTML.trim(),
                    status: 'Active'
                };

                if (!p.job_title) return alert('Job Title is required');
                if (!p.job_post_category_id) return alert('Category is required');
                if (!p.job_description) return alert('Job Description is required');

                ajax(editingId ? 'PATCH' : 'POST', editingId ? ROUTES.update.replace('__ID__', editingId) : ROUTES
                    .store, p,
                    function(err, res) {
                        if (err) return alert(err);
                        if (editingId) {
                            var idx = jobs.findIndex(function(b) {
                                return b.id == editingId;
                            });
                            if (idx > -1) jobs[idx] = res;
                        } else jobs.push(res);
                        window.location.hash = '#list';
                        renderTable();
                        showToaster('Job saved.');
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
                var html = '<div style="padding:10px;">';
                categories.forEach(function(c) {
                    html +=
                        '<div style="display:flex; justify-content:space-between; padding:15px; border-bottom:1px solid #f0f0f0; align-items:center;">' +
                        '<span>' + c.name + '</span>' +
                        '<div style="display:flex; gap:10px;">' +
                        '<button class="rc-row-btn" onclick="window._editCat(' + c.id +
                        ')"><i class="bi bi-pencil"></i></button>' +
                        '<button class="rc-row-btn trash-btn" onclick="window._delCat(' + c.id +
                        ')"><i class="bi bi-trash"></i></button>' +
                        '</div></div>';
                });
                html +=
                    '<div style="padding:20px; text-align:center;"><button class="rc-btn-add" id="catAddBtn" style="margin: 0 auto;"><i class="bi bi-plus-lg"></i> Add Category</button></div></div>';
                body.innerHTML = html;
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
                customConfirm('Delete category?', function() {
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
                body.innerHTML = '<div style="padding:25px;">' +
                    '<label class="bulk-col-label">Category Name</label>' +
                    '<input type="text" id="catNameInput" class="bulk-input" value="' + (isNew ? '' : cat.name) + '">' +
                    '<div style="display:flex; gap:10px; margin-top:20px;">' +
                    '<button class="btn-save-red" id="catSaveBtn">' + (isNew ? 'Add' : 'Save') + '</button>' +
                    '<button class="rc-btn-outline" onclick="showCatList()">Back</button>' +
                    '</div></div>';
                document.getElementById('catSaveBtn').onclick = function() {
                    var val = document.getElementById('catNameInput').value.trim();
                    if (!val) return;
                    ajax(isNew ? 'POST' : 'PATCH', isNew ? ROUTES.catStore : ROUTES.catUpdate.replace('__ID__', cat
                        .id), {
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
                if (jobs.length > 0) jobs.forEach(function(b) {
                    addBulkRow(b);
                });
                else addBulkRow(null);
                bulkOverlay.classList.add('open');
            }

            function addBulkRow(item) {
                var row = document.createElement('tr');
                row.dataset.id = item ? item.id : '';

                row.innerHTML = `
            <td><input type="text" class="bulk-edit-input bulk-title" value="${item ? item.job_title : ''}"></td>
            <td>
                <select class="bulk-edit-input bulk-category">
                    <option value="">[Select]</option>
                    ${categories.map(c => `<option value="${c.id}" ${item && item.job_post_category_id == c.id ? 'selected' : ''}>${c.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <div class="bulk-rte-wrap">
                    <div class="bulk-edit-rte-toolbar" style="display:flex; flex-wrap:wrap; gap:4px; padding:6px 10px; background:#fff; border-bottom:1px solid #eef0f2;">
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
                    <div contenteditable="true" class="bulk-rte-content bulk-desc">${item ? item.job_description || '' : ''}</div>
                </div>
            </td>
            <td><button class="rc-row-btn trash-btn" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
        `;
                bulkTableBody.appendChild(row);

                // Initialize RTE for new row
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
                        job_title: title,
                        job_post_category_id: row.querySelector('.bulk-category').value || null,
                        job_description: row.querySelector('.bulk-desc').innerHTML,
                        status: 'Active'
                    });
                });

                ajax('POST', ROUTES.bulkUpdate, {
                    jobs: data
                }, function(err, res) {
                    if (err) return alert(err);
                    jobs = res;
                    renderTable();
                    bulkOverlay.classList.remove('open');
                    showToaster('Bulk data saved.');
                });
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
                    // Normalize block name (sometimes browser returns tags differently)
                    var val = block.toLowerCase().replace('<', '').replace('>', '');
                    if (['h2', 'h3', 'h4', 'h5', 'h6'].includes(val)) sel.value = val;
                    else sel.value = 'p';
                }
            }
        }
    </script>
@endpush
