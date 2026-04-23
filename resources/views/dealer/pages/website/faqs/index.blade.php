@extends('layouts.dealer.app')
@section('title', __('Reusable Content: FAQ') . ' | ' . __(config('app.name')))

@push('page-styles')
    <style>
        /* Main Layout */
        .rc-wrapper {
            display: flex;
            gap: 40px;
            min-height: calc(100vh - 160px);
            padding: 20px 0;
        }

        .rc-sidebar {
            width: 260px;
            min-width: 260px;
        }

        .rc-sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
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
background: #fff; color: #333; font-weight: 600;
        }

        .rc-sidebar-item i {
            font-size: 18px;
            width: 24px;
            text-align: center;
            color: #999;
        }

        .rc-sidebar-item.active i {
            color: #c0392b;
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
            height: fit-content;
        }

        /* Header Actions */
        .rc-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
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
            padding: 10px 18px;
            border-radius: 8px;
            white-space: nowrap;
            transition: all .15s;
            font-weight: 500;
        }

        .rc-btn-outline:hover {
            background: #f8f9fa;
            border-color: #d0d0d0;
        }

        .rc-btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #c0392b;
            color: #fff;
            border: none;
            padding: 11px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }

        .rc-btn-add:hover {
            background: #a93226;
        }

        /* Table Styling */
        .faq-table {
            width: 100%;
            border-collapse: collapse;
        }

        .faq-table th {
            padding: 18px 20px;
            font-weight: 700;
            color: #333;
            text-align: left;
            font-size: 12px;
            border-bottom: 1px solid #f0f0f0;
            background: #fff;
        }

        .faq-table td {
            padding: 25px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f8f9fa;
            background: #fff;
        }

        .faq-table tr:last-child td {
            border-bottom: none;
        }

        .faq-title-cell {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .faq-drag {
            color: #ddd;
            cursor: grab;
            font-size: 16px;
            padding-top: 2px;
        }

        .faq-content-wrap {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .faq-question {
            font-size: 15px;
            font-weight: 500;
            color: #333;
            line-height: 1.4;
        }

        .faq-row-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faq-row-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 12px;
            color: #666;
            background: #fff;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }

        .faq-row-btn:hover {
            background: #f8f8f8;
            color: #333;
            border-color: #ccc;
        }

        .faq-row-btn.trash-btn {
            color: #c0392b;
        }

        .faq-row-btn.trash-btn:hover {
            background: #fff0f0;
            border-color: #f5c6cb;
        }

        .faq-cat-text {
            font-size: 14px;
            color: #555;
        }

        .faq-author-text {
            font-size: 14px;
            color: #555;
        }

        .faq-status-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #444;
        }

        .faq-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .faq-status-dot.published {
            background: #28a745;
        }

        .faq-status-dot.draft {
            background: #ffc107;
        }

        /* Pagination & Summary */
        .faq-pagination-container {
            padding: 40px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .faq-pagination {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .faq-page-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eee;
            background: #fff;
            color: #666;
            border-radius: 8px;
            cursor: pointer;
            transition: all .2s;
            font-size: 14px;
        }

        .faq-page-btn.active {
            background: #c0392b;
            color: #fff;
            border-color: #c0392b;
        }

        .faq-page-btn:hover:not(.active) {
            background: #f8f9fa;
            border-color: #ddd;
        }

        .faq-summary {
            font-size: 13px;
            color: #999;
        }

        /* Filter Dropdown */
        .rc-filter-wrap {
            position: relative;
        }

        .rc-filter-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 5px);
            right: 0;
            width: 240px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow: hidden;
        }

        .rc-filter-dropdown.open {
            display: block;
        }

        .rc-filter-list {
            list-style: none;
            margin: 0;
            padding: 6px 0;
        }

        .rc-filter-item {
            padding: 10px 18px;
            font-size: 14px;
            color: #444;
            cursor: pointer;
            transition: all .1s;
        }

        .rc-filter-item:hover {
            background: #f8f9fa;
            color: #c0392b;
        }

        .rc-filter-item.active {
            color: #c0392b;
            font-weight: 600;
            background: #fff5f5;
        }

        /* Modals */
        .faq-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 2000;
            display: none;
            backdrop-filter: blur(2px);
        }

        .faq-modal-overlay.open {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .faq-modal {
            background: #fff;
            border-radius: 14px;
            width: 550px;
            max-width: 95vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .faq-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
        }

        .faq-modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .faq-modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #aaa;
            cursor: pointer;
            line-height: 1;
            transition: color .2s;
        }

        .faq-modal-close:hover {
            color: #333;
        }

        .faq-modal-body {
            padding: 0;
            overflow-y: auto;
            flex: 1;
        }

        /* Bulk Edit Styles */
        #bulkModalOverlay .faq-modal {
            width: 1200px;
            max-width: 98vw;
            height: 92vh;
            border-radius: 4px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }

        .bulk-modal-content {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #fff;
        }

        .bulk-header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            border-bottom: 1px solid #eee;
        }

        .bulk-header-top h3 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .bulk-header-actions {
            padding: 12px 25px;
            display: flex;
            justify-content: flex-end;
        }

        .bulk-body {
            flex: 1;
            overflow-y: auto;
            padding: 0 25px;
        }

        .bulk-row {
            display: grid;
            grid-template-columns: 180px 180px 1fr 50px;
            gap: 0;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            margin-bottom: 25px;
            background: #fff;
        }

        .bulk-col {
            padding: 15px;
            border-right: 1px solid #f0f0f0;
            background: #f9f9f9;
        }

        .bulk-col:nth-child(3) {
            background: #fff;
            border-right: none;
            padding: 0;
        }

        .bulk-col:last-child {
            background: #fff;
            border-right: none;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 25px;
        }

        .bulk-col-label {
            font-size: 11px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        .bulk-col-label span {
            color: #c0392b;
            margin-left: 2px;
        }

        .bulk-input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #dcdcdc;
            border-radius: 3px;
            font-size: 13px;
            color: #444;
            background: #fff;
        }

        .bulk-select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #dcdcdc;
            border-radius: 3px;
            font-size: 13px;
            color: #444;
            background: #fff;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }

        /* RTE Precision */
        .bulk-rte-wrap {
            padding: 15px;
        }

        .bulk-rte {
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            background: #fff;
        }

        .rte-toolbar {
            background: #f8f8f8;
            border-bottom: 1px solid #e0e0e0;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .rte-tool-group {
            display: flex;
            align-items: center;
            gap: 10px;
            border-right: 1px solid #e0e0e0;
            padding-right: 12px;
        }

        .rte-tool-group:last-child {
            border-right: none;
        }

        .rte-select-wrap {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .rte-select {
            border: none;
            background: transparent;
            font-size: 12px;
            color: #555;
            cursor: pointer;
            outline: none;
            font-weight: 500;
            appearance: none;
        }

        .rte-btn {
            background: none;
            border: none;
            color: #777;
            cursor: pointer;
            font-size: 14px;
            padding: 2px;
            transition: color .2s;
        }

        .rte-btn:hover {
            color: #333;
        }

        .rte-textarea {
            width: 100%;
            border: none;
            outline: none;
            font-size: 13px;
            color: #444;
            min-height: 90px;
            padding: 15px;
            resize: vertical;
            font-family: inherit;
            line-height: 1.6;
            background: #f9f9f9;
        }

        .bulk-action-del {
            color: #c0392b;
            cursor: pointer;
            font-size: 18px;
            opacity: 0.7;
            transition: opacity .2s;
        }

        .bulk-action-del:hover {
            opacity: 1;
        }

        .bulk-footer {
            padding: 15px 25px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #fff;
        }

        .btn-cancel-outline {
            padding: 8px 25px;
            border: 1px solid #dcdcdc;
            background: #fff;
            color: #666;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
        }

        .btn-save-red {
            padding: 8px 25px;
            background: #c0392b;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save-red:hover {
            background: #a93226;
        }


        /* Category Modal List */
        .cat-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 25px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            transition: background .1s;
        }

        .cat-list-item:hover {
            background: #fafafa;
        }

        .cat-list-name {
            font-size: 15px;
            color: #333;
            font-weight: 500;
        }

        .cat-list-count { display: inline-flex; align-items: center; justify-content: center; min-width: 26px; height: 26px; background: #f0f0f0; color: #666; font-size: 12px; font-weight: 700; border-radius: 6px; }

        /* Custom Confirmation Modal */
        .confirm-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 3000; display: none; backdrop-filter: blur(2px); align-items: center; justify-content: center; }
        .confirm-modal-overlay.open { display: flex !important; }
        .confirm-modal { background: #fff; border-radius: 8px; width: 450px; max-width: 90vw; box-shadow: 0 10px 40px rgba(0,0,0,0.2); overflow: hidden; }
        .confirm-modal-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .confirm-modal-title { font-size: 14px; font-weight: 600; color: #333; display: flex; align-items: center; gap: 10px; }
        .confirm-modal-close { background: none; border: none; font-size: 20px; color: #aaa; cursor: pointer; }
        .confirm-modal-body { padding: 30px 20px; font-size: 14px; color: #444; border-bottom: 1px solid #eee; }
        .confirm-modal-footer { padding: 12px 20px; display: flex; justify-content: flex-end; gap: 10px; background: #fff; }

        /* Success Toaster */
        .toaster-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
        .toaster { background: #28a745; color: #fff; padding: 12px 20px; border-radius: 6px; display: flex; align-items: center; gap: 12px; min-width: 250px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transform: translateX(120%); transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
        .toaster.show { transform: translateX(0); }
        .toaster i { font-size: 18px; }
        .toaster span { font-size: 13px; font-weight: 500; flex: 1; }
        .toaster-close { background: none; border: none; color: rgba(255,255,255,0.7); font-size: 18px; cursor: pointer; padding: 0; line-height: 1; }
        .toaster-close:hover { color: #fff; }
    </style>
@endpush

@section('page-content')
    <main class="main-content" id="mainContent" style="padding:0; background: #fafbfc; min-height: 100vh;">
        <div style="padding: 30px 45px 0 45px;">
            <div class="page-header" style="margin-bottom: 20px; border: none;">
                <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222;">
                    {{ __('Reusable Content: FAQ') }}</h2>
                <div class="rc-header-actions">
                    <div class="rc-filter-wrap">
                        <button class="rc-btn-outline" type="button" id="catFilterBtn">
                            <i class="bi bi-funnel"></i>
                            <span id="catFilterLabel">Filter by Category</span>
                            <i class="bi bi-chevron-down" style="font-size:11px; margin-left: 5px;"></i>
                        </button>
                        <div class="rc-filter-dropdown" id="catFilterDropdown">
                            <ul class="rc-filter-list" id="catFilterList"></ul>
                        </div>
                    </div>
                    <button class="rc-btn-outline" type="button" id="manageCatBtn"><i class="bi bi-folder2-open"></i>
                        {{ __('Manage Categories') }}</button>
                    <button class="rc-btn-outline" type="button" id="bulkEditBtn"><i class="bi bi-pencil-square"></i>
                        {{ __('Bulk Edit') }}</button>
                    <button class="rc-btn-add" type="button" id="addFaqBtn"><i class="bi bi-plus-lg"></i>
                        {{ __('Add New FAQ') }}</button>
                </div>
            </div>

            <div class="rc-wrapper">
                @include('dealer.partials.reusable-content-sidebar')
                <div style="flex: 1; display: flex; flex-direction: column;">
                    {{-- List View --}}
                    <div id="faqListView">
                        <div class="rc-main-container">
                            <table class="faq-table" id="faqTable">
                                <thead>
                                    <tr>
                                        <th style="width: 55%;">Title</th>
                                        <th style="width: 15%;">Category</th>
                                        <th style="width: 15%;">Author</th>
                                        <th style="width: 15%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="faqTableBody"></tbody>
                            </table>
                        </div>
                        <div class="faq-pagination-container">
                            <div class="faq-pagination" id="faqPagination"></div>
                            <div class="faq-summary" id="faqSummary"></div>
                        </div>
                    </div>

                    {{-- Add/Edit Form View --}}
                    <div id="faqFormView" style="display: none;">
                        <div style="margin-bottom: 20px;">
                            <button class="rc-btn-outline" id="faqBackBtn"><i class="bi bi-chevron-left"></i> Go
                                Back</button>
                        </div>
                        <div class="rc-main-container" style="padding: 0;">
                            <div style="padding: 20px 30px; border-bottom: 1px solid #f0f0f0;">
                                <h3 style="font-size: 15px; font-weight: 600; color: #333; margin: 0;" id="formViewTitle">
                                    Add FAQ</h3>
                            </div>
                            <div style="padding: 30px;">
                                <div class="bulk-col" style="background: transparent; border: none; padding: 0 0 25px 0;">
                                    <label class="bulk-col-label" style="font-size: 12px;">Question Title
                                        <span>*</span></label>
                                    <input type="text" id="faqQuestionInput" class="bulk-input"
                                        placeholder="What is the question?">
                                </div>

                                <div class="bulk-col" style="background: transparent; border: none; padding: 0 0 25px 0;">
                                    <label class="bulk-col-label" style="font-size: 12px;">Category <span>*</span></label>
                                    <select id="faqCategorySelect" class="bulk-select"></select>
                                </div>

                                <div class="bulk-col" style="background: transparent; border: none; padding: 0 0 30px 0;">
                                    <label class="bulk-col-label" style="font-size: 12px;">Answer <span>*</span></label>
                                    <div class="bulk-rte">
                                        <div class="rte-toolbar">
                                            <div class="rte-tool-group">
                                                <div class="rte-select-wrap"><select class="rte-select">
                                                        <option>Normal</option>
                                                    </select><i class="bi bi-chevron-expand"
                                                        style="font-size:10px;color:#999;"></i></div>
                                            </div>
                                            <div class="rte-tool-group">
                                                <button class="rte-btn"><i class="bi bi-type-bold"></i></button>
                                                <button class="rte-btn"><i class="bi bi-type-italic"></i></button>
                                                <button class="rte-btn"><i class="bi bi-type-underline"></i></button>
                                            </div>
                                            <div class="rte-tool-group">
                                                <button class="rte-btn"><i class="bi bi-text-left"></i></button>
                                            </div>
                                            <div class="rte-tool-group">
                                                <button class="rte-btn"><i class="bi bi-list-task"></i></button>
                                                <button class="rte-btn"><i class="bi bi-list-ol"></i></button>
                                                <button class="rte-btn"><i class="bi bi-text-indent-left"></i></button>
                                                <button class="rte-btn"><i class="bi bi-text-indent-right"></i></button>
                                            </div>
                                            <div class="rte-tool-group">
                                                <button class="rte-btn"><i class="bi bi-link-45deg"></i></button>
                                            </div>
                                        </div>
                                        <textarea id="faqAnswerInput" class="rte-textarea" style="min-height: 150px; background: #fff;"></textarea>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: flex-start;">
                                    <button class="btn-save-red" id="faqSaveBtn" style="padding: 10px 35px;"><i
                                            class="bi bi-check-lg"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Manage Categories Modal --}}
    <div class="faq-modal-overlay" id="catModalOverlay">
        <div class="faq-modal">
            <div class="faq-modal-header">
                <h3 class="faq-modal-title" id="catModalTitle">Manage Categories</h3>
                <button class="faq-modal-close" id="catModalClose">&times;</button>
            </div>
            <div class="faq-modal-body" id="catModalBody"></div>
        </div>
    </div>

    {{-- Bulk Edit FAQ Modal --}}
    <div class="faq-modal-overlay" id="bulkModalOverlay">
        <div class="faq-modal">
            <div class="bulk-modal-content">
                <div class="bulk-header-top">
                    <h3 class="faq-modal-title">Bulk Edit FAQs</h3>
                    <button class="faq-modal-close" id="bulkModalClose">&times;</button>
                </div>
                <div class="bulk-header-actions">
                    <button class="rc-btn-outline" id="bulkAddRowBtn" style="background: #fff;"><i
                            class="bi bi-plus-lg"></i> Add Row</button>
                </div>
                <div class="bulk-body" id="bulkRowsContainer"></div>
                <div class="bulk-footer">
                    <button class="btn-cancel-outline" id="bulkCancelBtn">Cancel</button>
                    <button class="btn-save-red" id="bulkSaveBtn"><i class="bi bi-check-lg"></i> Save</button>
                </div>
            </div>
        </div>
        </div>
    </div>

    {{-- Custom Confirm Modal --}}
    <div class="confirm-modal-overlay" id="confirmModalOverlay">
        <div class="confirm-modal">
            <div class="confirm-modal-header">
                <div class="confirm-modal-title"><i class="bi bi-info-circle-fill" style="color: #f39c12; font-size: 18px;"></i> Are you sure?</div>
                <button class="confirm-modal-close" id="confirmModalClose">&times;</button>
            </div>
            <div class="confirm-modal-body" id="confirmModalBodyText">Are you sure you want to delete this post?</div>
            <div class="confirm-modal-footer">
                <button class="btn-cancel-outline" id="confirmCancelBtn" style="padding: 7px 20px;">Cancel</button>
                <button class="btn-save-red" id="confirmContinueBtn" style="padding: 7px 20px;">Continue</button>
            </div>
        </div>
    </div>

    {{-- Toaster Container --}}
    <div class="toaster-container" id="toasterContainer"></div>
@endsection

@push('page-scripts')
    <script>
        (function() {
            var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            var ROUTES = {
                faqStore: '{{ route('dealer.website.faqs.store') }}',
                faqUpdate: '{{ route('dealer.website.faqs.update', ['faq' => '__ID__']) }}',
                faqDestroy: '{{ route('dealer.website.faqs.destroy', ['faq' => '__ID__']) }}',
                catStore: '{{ route('dealer.website.faqs.categories.store') }}',
                catUpdate: '{{ route('dealer.website.faqs.categories.update', ['faqCategory' => '__ID__']) }}',
                catDestroy: '{{ route('dealer.website.faqs.categories.destroy', ['faqCategory' => '__ID__']) }}',
                bulkUpdate: '{{ route('dealer.website.faqs.bulk-update') }}',
            };

            var categories = @json($categories);
            var faqs = @json($faqs);

            var activeCatFilter = 'all';
            var editingFaqId = null;

            function ajax(method, url, data, cb) {
                var xhr = new XMLHttpRequest();
                xhr.open(method, url, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        cb(null, JSON.parse(xhr.responseText || '{}'));
                    } else {
                        var msg = 'Error';
                        try { msg = JSON.parse(xhr.responseText).message || msg; } catch (e) {}
                        cb(msg);
                    }
                };
                xhr.onerror = function() { cb('Network error'); };
                xhr.send(data ? JSON.stringify(data) : null);
            }

            function showToaster(message) {
                var container = document.getElementById('toasterContainer');
                var t = document.createElement('div');
                t.className = 'toaster';
                t.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span>' + message + '</span><button class="toaster-close">&times;</button>';
                container.appendChild(t);
                setTimeout(function() { t.classList.add('show'); }, 10);
                var remove = function() {
                    t.classList.remove('show');
                    setTimeout(function() { t.remove(); }, 400);
                };
                var timer = setTimeout(remove, 3000);
                t.querySelector('.toaster-close').onclick = function() { clearTimeout(timer); remove(); };
            }

            var confirmOverlay = document.getElementById('confirmModalOverlay');
            var confirmBtn = document.getElementById('confirmContinueBtn');
            var confirmCancel = document.getElementById('confirmCancelBtn');
            var confirmClose = document.getElementById('confirmModalClose');
            var confirmText = document.getElementById('confirmModalBodyText');
            var confirmCallback = null;

            function customConfirm(text, cb) {
                confirmText.textContent = text;
                confirmCallback = cb;
                confirmOverlay.classList.add('open');
            }
            confirmCancel.onclick = confirmClose.onclick = function() {
                confirmOverlay.classList.remove('open');
                confirmCallback = null;
            };
            confirmBtn.onclick = function() {
                if (confirmCallback) confirmCallback();
                confirmOverlay.classList.remove('open');
                confirmCallback = null;
            };

            function renderFaqs() {
                var tbody = document.getElementById('faqTableBody');
                var filtered = activeCatFilter === 'all' ? faqs : faqs.filter(function(f) {
                    return f.faq_category_id == activeCatFilter;
                });
                var html = '';
                filtered.forEach(function(f) {
                    var catName = f.category ? f.category.name : 'Uncategorized';
                    var dotClass = f.status === 'Published' ? 'published' : 'draft';
                    html += '<tr>' +
                        '<td>' +
                        '<div class="faq-title-cell">' +
                        '<div style="display:flex; align-items:flex-start;">' +
                        '<span class="faq-drag"><i class="bi bi-list"></i></span>' +
                        '<div class="faq-content-wrap">' +
                        '<div class="faq-question">' + f.question + '</div>' +
                        '<div class="faq-row-actions">' +
                        '<a href="#" class="faq-row-btn" onclick="window._faqEdit(' + f.id +
                        ');return false;"><i class="bi bi-pencil-square"></i> Edit</a>' +
                        '<button class="faq-row-btn trash-btn" onclick="window._faqTrash(' + f.id +
                        ')"><i class="bi bi-trash"></i> Trash</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</td>' +
                        '<td><span class="faq-cat-text">' + catName + '</span></td>' +
                        '<td><span class="faq-author-text">' + (f.author || 'Monica Ibarra Reyes') +
                        '</span></td>' +
                        '<td>' +
                        '<div class="faq-status-wrap">' +
                        '<span class="faq-status-dot ' + dotClass + '"></span>' +
                        '<span class="faq-status-text">' + f.status + '</span>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                });
                tbody.innerHTML = html ||
                    '<tr><td colspan="4" style="text-align:center;padding:50px;color:#999;">No FAQs found.</td></tr>';
                document.getElementById('faqSummary').textContent = 'Showing ' + filtered.length + ' of ' + faqs
                    .length + ' pages';
                document.getElementById('faqPagination').innerHTML =
                    '<button class="faq-page-btn"><i class="bi bi-chevron-left"></i></button>' +
                    '<button class="faq-page-btn active">1</button>' +
                    '<button class="faq-page-btn"><i class="bi bi-chevron-right"></i></button>';
            }

            function renderCatFilter() {
                var list = document.getElementById('catFilterList');
                var html = '<li class="rc-filter-item' + (activeCatFilter === 'all' ? ' active' : '') +
                    '" data-cat="all">All Categories</li>';
                categories.forEach(function(c) {
                    html += '<li class="rc-filter-item' + (activeCatFilter == c.id ? ' active' : '') +
                        '" data-cat="' + c.id + '">' + c.name + '</li>';
                });
                list.innerHTML = html;
                list.querySelectorAll('.rc-filter-item').forEach(function(item) {
                    item.addEventListener('click', function() {
                        activeCatFilter = item.dataset.cat === 'all' ? 'all' : parseInt(item.dataset
                            .cat);
                        var label = activeCatFilter === 'all' ? 'Filter by Category' : item.textContent;
                        document.getElementById('catFilterLabel').textContent = label;
                        document.getElementById('catFilterDropdown').classList.remove('open');
                        renderCatFilter();
                        renderFaqs();
                    });
                });
            }

            document.getElementById('catFilterBtn').addEventListener('click', function(e) {
                e.stopPropagation();
                document.getElementById('catFilterDropdown').classList.toggle('open');
            });
            document.addEventListener('click', function(e) {
                if (!document.getElementById('catFilterBtn').contains(e.target))
                    document.getElementById('catFilterDropdown').classList.remove('open');
            });

            var catOverlay = document.getElementById('catModalOverlay');
            document.getElementById('manageCatBtn').addEventListener('click', showCatList);
            document.getElementById('catModalClose').addEventListener('click', function() {
                catOverlay.classList.remove('open');
            });
            catOverlay.addEventListener('click', function(e) {
                if (e.target === catOverlay) catOverlay.classList.remove('open');
            });

            function countFaqsInCat(catId) {
                return faqs.filter(function(f) {
                    return f.faq_category_id == catId;
                }).length;
            }

            function showCatList() {
                document.getElementById('catModalTitle').textContent = 'Manage Categories';
                var body = document.getElementById('catModalBody');
                var html = '';
                categories.forEach(function(c) {
                    var count = countFaqsInCat(c.id);
                    html += '<div class="cat-list-item" data-catid="' + c.id +
                        '"><div class="cat-list-left"><span class="cat-list-name">' + c.name +
                        '</span> <span class="cat-list-count">' + count +
                        '</span></div><span class="cat-list-arrow"><i class="bi bi-chevron-right"></i></span></div>';
                });
                html +=
                    '<div style="padding:25px; text-align:center;"><button class="rc-btn-add" id="catAddNewBtn"><i class="bi bi-plus-lg"></i> Add Category</button></div>';
                body.innerHTML = html;
                catOverlay.classList.add('open');
                body.querySelectorAll('.cat-list-item').forEach(function(item) {
                    item.addEventListener('click', function() {
                        var cat = categories.find(function(c) {
                            return c.id == item.dataset.catid;
                        });
                        if (cat) showCatEdit(cat);
                    });
                });
                document.getElementById('catAddNewBtn').addEventListener('click', function() {
                    showCatEdit(null);
                });
            }

            function showCatEdit(cat) {
                var isNew = !cat;
                document.getElementById('catModalTitle').textContent = isNew ? 'Add Category' : 'Edit Category';
                var body = document.getElementById('catModalBody');
                body.innerHTML =
                    '<div style="padding:25px;"><label style="display:block; font-weight:700; font-size:12px; margin-bottom:8px;">Category Name</label>' +
                    '<input type="text" id="catNameInput" class="bulk-input" value="' + (isNew ? '' : cat.name) +
                    '" placeholder="Enter category name">' +
                    '<div style="display:flex; gap:10px; margin-top:20px;"><button class="btn-save-red" id="catSaveBtn" style="padding: 9px 25px;">' +
                    (isNew ? 'Add' : 'Save') + '</button>' +
                    '<button class="btn-cancel-outline" id="catBackBtn" style="padding: 9px 25px;">Back</button>' +
                    (isNew ? '' :
                        '<button class="faq-row-btn trash-btn" id="catDeleteBtn" style="margin-left:auto; padding: 9px 15px;">Delete</button>'
                        ) +
                    '</div></div>';

                document.getElementById('catBackBtn').addEventListener('click', showCatList);

                document.getElementById('catSaveBtn').addEventListener('click', function() {
                    var val = document.getElementById('catNameInput').value.trim();
                    if (!val) return;
                    if (isNew) {
                        ajax('POST', ROUTES.catStore, {
                            name: val
                        }, function(err, res) {
                            if (err) return alert(err);
                            categories.push(res);
                            renderCatFilter();
                            showCatList();
                            showToaster('Category added.');
                        });
                    } else {
                        ajax('PATCH', ROUTES.catUpdate.replace('__ID__', cat.id), {
                            name: val
                        }, function(err, res) {
                            if (err) return alert(err);
                            var idx = categories.findIndex(function(c) {
                                return c.id === cat.id;
                            });
                            if (idx > -1) categories[idx] = res;
                            faqs.forEach(function(f) {
                                if (f.category && f.category.id === cat.id) f.category.name =
                                    res.name;
                            });
                            renderCatFilter();
                            renderFaqs();
                            showCatList();
                            showToaster('Category updated.');
                        });
                    }
                });

                var delBtn = document.getElementById('catDeleteBtn');
                if (delBtn) delBtn.addEventListener('click', function() {
                    customConfirm('Delete category "' + cat.name + '"?', function() {
                        ajax('DELETE', ROUTES.catDestroy.replace('__ID__', cat.id), null, function(err) {
                            if (err) return alert(err);
                            categories = categories.filter(function(c) {
                                return c.id !== cat.id;
                            });
                            faqs.forEach(function(f) {
                                if (f.faq_category_id === cat.id) {
                                    f.faq_category_id = null;
                                    f.category = null;
                                }
                            });
                            if (activeCatFilter === cat.id) activeCatFilter = 'all';
                            renderCatFilter();
                            renderFaqs();
                            showCatList();
                            showToaster('Category deleted.');
                        });
                    });
                });
            }

            var listView = document.getElementById('faqListView');
            var formView = document.getElementById('faqFormView');

            document.getElementById('addFaqBtn').addEventListener('click', function() {
                openFaqForm(null);
            });
            document.getElementById('faqBackBtn').addEventListener('click', closeFaqForm);

            function closeFaqForm() {
                formView.style.display = 'none';
                listView.style.display = 'block';
                editingFaqId = null;
            }

            function populateCatSelect(selectedId) {
                var sel = document.getElementById('faqCategorySelect');
                var html = '<option value="">-- Select Category --</option>';
                categories.forEach(function(c) {
                    html += '<option value="' + c.id + '"' + (c.id == selectedId ? ' selected' : '') + '>' + c
                        .name + '</option>';
                });
                sel.innerHTML = html;
            }

            function openFaqForm(faqId) {
                editingFaqId = faqId;
                var faq = faqId ? faqs.find(function(f) {
                    return f.id === faqId;
                }) : null;
                document.getElementById('formViewTitle').textContent = faq ? 'Edit FAQ' : 'Add FAQ';
                document.getElementById('faqQuestionInput').value = faq ? faq.question : '';
                document.getElementById('faqAnswerInput').value = faq ? (faq.answer || '') : '';
                populateCatSelect(faq ? faq.faq_category_id : '');

                listView.style.display = 'none';
                formView.style.display = 'block';
            }

            document.getElementById('faqSaveBtn').addEventListener('click', function() {
                var q = document.getElementById('faqQuestionInput').value.trim();
                if (!q) return;
                var catId = document.getElementById('faqCategorySelect').value;
                var ans = document.getElementById('faqAnswerInput').value.trim();
                var payload = {
                    question: q,
                    answer: ans,
                    faq_category_id: catId ? parseInt(catId) : null
                };

                if (editingFaqId) {
                    ajax('PATCH', ROUTES.faqUpdate.replace('__ID__', editingFaqId), payload, function(err,
                    res) {
                        if (err) return alert(err);
                        var idx = faqs.findIndex(function(f) {
                            return f.id === editingFaqId;
                        });
                        if (idx > -1) faqs[idx] = res;
                        closeFaqForm();
                        renderFaqs();
                        renderCatFilter();
                        showToaster('Post saved.');
                    });
                } else {
                    ajax('POST', ROUTES.faqStore, payload, function(err, res) {
                        if (err) return alert(err);
                        faqs.push(res);
                        closeFaqForm();
                        renderFaqs();
                        renderCatFilter();
                        showToaster('Post saved.');
                    });
                }
            });

            window._faqEdit = function(id) {
                openFaqForm(id);
            };
            window._faqTrash = function(id) {
                customConfirm('Are you sure you want to delete this post?', function() {
                    ajax('DELETE', ROUTES.faqDestroy.replace('__ID__', id), null, function(err) {
                        if (err) return alert(err);
                        faqs = faqs.filter(function(f) {
                            return f.id !== id;
                        });
                        renderFaqs();
                        renderCatFilter();
                        showToaster('Post deleted.');
                    });
                });
            };

            // ── Bulk Edit Logic ──
            var bulkOverlay = document.getElementById('bulkModalOverlay');
            var bulkRowsContainer = document.getElementById('bulkRowsContainer');

            document.getElementById('bulkEditBtn').addEventListener('click', function() {
                bulkRowsContainer.innerHTML = '';
                faqs.forEach(function(f) {
                    addBulkRow(f);
                });
                bulkOverlay.classList.add('open');
            });

            document.getElementById('bulkModalClose').addEventListener('click', function() {
                bulkOverlay.classList.remove('open');
            });
            document.getElementById('bulkCancelBtn').addEventListener('click', function() {
                bulkOverlay.classList.remove('open');
            });
            document.getElementById('bulkAddRowBtn').addEventListener('click', function() {
                addBulkRow(null);
            });

            function addBulkRow(faq) {
                var div = document.createElement('div');
                div.className = 'bulk-row';
                div.dataset.id = faq ? faq.id : '';

                var catOptions = '<option value="">-- Select --</option>';
                categories.forEach(function(c) {
                    catOptions += '<option value="' + c.id + '"' + (faq && faq.faq_category_id == c.id ?
                        ' selected' : '') + '>' + c.name + '</option>';
                });

                div.innerHTML =
                    '<div class="bulk-col"><label class="bulk-col-label">Question Title <span>*</span></label><input type="text" class="bulk-input bulk-q" value="' +
                    (faq ? faq.question : '') + '"></div>' +
                    '<div class="bulk-col"><label class="bulk-col-label">Category <span>*</span></label><select class="bulk-select bulk-c">' +
                    catOptions + '</select></div>' +
                    '<div class="bulk-col">' +
                    '<div class="bulk-rte-wrap">' +
                    '<label class="bulk-col-label">Answer <span>*</span></label>' +
                    '<div class="bulk-rte">' +
                    '<div class="rte-toolbar">' +
                    '<div class="rte-tool-group"><div class="rte-select-wrap"><select class="rte-select"><option>Normal</option></select><i class="bi bi-chevron-expand" style="font-size:10px;color:#999;"></i></div></div>' +
                    '<div class="rte-tool-group">' +
                    '<button class="rte-btn"><i class="bi bi-type-bold"></i></button>' +
                    '<button class="rte-btn"><i class="bi bi-type-italic"></i></button>' +
                    '<button class="rte-btn"><i class="bi bi-type-underline"></i></button>' +
                    '</div>' +
                    '<div class="rte-tool-group">' +
                    '<button class="rte-btn"><i class="bi bi-text-left"></i></button>' +
                    '</div>' +
                    '<div class="rte-tool-group">' +
                    '<button class="rte-btn"><i class="bi bi-list-task"></i></button>' +
                    '<button class="rte-btn"><i class="bi bi-list-ol"></i></button>' +
                    '<button class="rte-btn"><i class="bi bi-text-indent-left"></i></button>' +
                    '<button class="rte-btn"><i class="bi bi-text-indent-right"></i></button>' +
                    '</div>' +
                    '<div class="rte-tool-group">' +
                    '<button class="rte-btn"><i class="bi bi-link-45deg"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '<textarea class="rte-textarea bulk-a" placeholder="Enter answer here...">' + (faq ? faq.answer :
                        '') + '</textarea>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="bulk-col"><div class="bulk-action-del"><i class="bi bi-trash"></i></div></div>';

                div.querySelector('.bulk-action-del').addEventListener('click', function() {
                    customConfirm('Remove row?', function() {
                        if (div.dataset.id) div.dataset.deleted = 'true';
                        div.style.display = 'none';
                    });
                });
                bulkRowsContainer.appendChild(div);
            }

            document.getElementById('bulkSaveBtn').addEventListener('click', function() {
                var rows = bulkRowsContainer.querySelectorAll('.bulk-row');
                var data = [];
                rows.forEach(function(row) {
                    data.push({
                        id: row.dataset.id ? parseInt(row.dataset.id) : null,
                        question: row.querySelector('.bulk-q').value.trim(),
                        faq_category_id: row.querySelector('.bulk-c').value ? parseInt(row
                            .querySelector('.bulk-c').value) : null,
                        answer: row.querySelector('.bulk-a').value.trim(),
                        is_deleted: row.dataset.deleted === 'true'
                    });
                });

                ajax('POST', ROUTES.bulkUpdate, {
                    faqs: data
                }, function(err, res) {
                    if (err) return alert(err);
                    faqs = res;
                    bulkOverlay.classList.remove('open');
                    renderFaqs();
                    renderCatFilter();
                    showToaster('Bulk update successful.');
                });
            });

            renderCatFilter();
            renderFaqs();
        })();
    </script>
@endpush
