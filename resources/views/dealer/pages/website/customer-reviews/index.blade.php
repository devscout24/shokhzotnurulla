@extends('layouts.dealer.app')

@section('title', __('Reusable Content: Review') . ' | ' . __(config('app.name')))

@push('page-styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Shared Dashboard Styles */
        .rc-sidebar { width: 280px; background: #fff; border-right: 1px solid #eee; padding: 20px 0; min-height: calc(100vh - 150px); }
        .rc-sidebar-item { display: flex; align-items: center; gap: 12px; padding: 14px 25px; color: #666; text-decoration: none; font-size: 14px; transition: all .2s; border-left: 3px solid transparent; }
        .rc-sidebar-item:hover { background: #f9f9f9; color: #333; }
        .rc-sidebar-item.active { background: #fff; color: #333; font-weight: 600; }
        .rc-sidebar-item.active i { color: #d0021b; }
        .rc-sidebar-item i { font-size: 18px; }

        .rc-wrapper { display: flex; gap: 40px; min-height: calc(100vh - 160px); padding: 20px 0; }
        .rc-sidebar { width: 260px; min-width: 260px; }
        .rc-sidebar-item { display: flex; align-items: center; gap: 12px; padding: 15px; font-size: 14px; color: #666; cursor: pointer; transition: all .2s; text-decoration: none; border-bottom: 1px solid #f0f0f0; }
        .rc-sidebar-item:last-child { border-bottom: none; }
        .rc-sidebar-item:hover { background: #f8f8f8; color: #333; }
        .rc-sidebar-item.active { background: #fff; color: #333; font-weight: 600; }
        .rc-sidebar-item i { font-size: 18px !important; width: 24px; text-align: center; color: #999; }
        .rc-sidebar-item.active i { color: #d0021b !important; }

        .rc-main-container { flex: 1; background: #fff; border: 1px solid #eef0f2; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); display: flex; flex-direction: column; overflow: hidden; height: fit-content; }
        
        .rc-header-actions { display: flex; gap: 12px; align-items: center; }
        .rc-btn-add { display: inline-flex; align-items: center; gap: 8px; background: #d0021b; color: #fff; border: none; padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; transition: background .2s; }
        .rc-btn-add:hover { background: #b00217; }
        .rc-btn-outline { display: inline-flex; align-items: center; gap: 8px; border: 1px solid #e0e0e0; background: #fff; font-size: 13px; color: #555; cursor: pointer; padding: 8px 18px; border-radius: 8px; white-space: nowrap; transition: all .15s; font-weight: 500; }
        .rc-btn-outline:hover { background: #f8f9fa; border-color: #d0d0d0; }

        .rc-table { width: 100%; border-collapse: collapse; }
        .rc-table th { padding: 18px 20px; font-weight: 700; color: #333; text-align: left; font-size: 12px; border-bottom: 1px solid #f0f0f0; background: #fff; }
        .rc-table td { padding: 25px 20px; vertical-align: middle; border-bottom: 1px solid #f8f9fa; background: #fff; }
        .rc-table tr:hover { background: #fafafa; }

        .rc-title-cell { display: flex; align-items: flex-start; gap: 15px; }
        .rc-drag { color: #ddd; cursor: grab; font-size: 16px; padding-top: 2px; }
        .rc-nickname { font-size: 15px; font-weight: 500; color: #333; line-height: 1.4; margin-bottom: 8px; }
        .rc-row-actions { display: flex; align-items: center; gap: 10px; opacity: 0; transition: opacity .2s; }
        .rc-table tr:hover .rc-row-actions { opacity: 1; }
        .rc-row-btn { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 12px; color: #666; background: #fff; cursor: pointer; text-decoration: none; transition: all .2s; }
        .rc-row-btn:hover { background: #f8f8f8; color: #333; border-color: #ccc; }
        .rc-row-btn.trash-btn { color: #d0021b; }
        .rc-row-btn.trash-btn:hover { background: #fff0f0; border-color: #f5c6cb; }

        /* Form Styles */
        .bulk-col-label { font-size: 12px; font-weight: 700; color: #333; margin-bottom: 8px; display: block; }
        .bulk-col-label span { color: #d0021b; }
        .bulk-input, .bulk-select { width: 100%; padding: 12px 15px; border: 1px solid #eef0f2; border-radius: 8px; font-size: 14px; color: #333; transition: all .2s; background: #fcfdfe; }
        .bulk-input:focus, .bulk-select:focus { border-color: #d0021b; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(208, 2, 27, 0.05); }
        .bulk-input::placeholder { color: #9da3a8; }
        .bulk-input:focus, .bulk-select:focus { border-color: #d0021b; outline: none; }
        .bulk-input::placeholder { color: #bbb; }
        .hint-box { font-size: 12px; color: #888; margin-top: 6px; line-height: 1.4; }

        .media-row { margin-bottom: 30px; position: relative; }
        .media-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .media-btn-select { font-size: 12px; font-weight: 600; color: #d0021b; cursor: pointer; display: flex; align-items: center; gap: 5px; }
        .media-preview-wrap { margin-top: 10px; display: none; position: relative; width: fit-content; }
        .media-preview-wrap img { max-width: 200px; max-height: 120px; border-radius: 4px; border: 1px solid #eee; }
        .media-preview-remove { position: absolute; top: -8px; right: -8px; background: #d0021b; color: #fff; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; border: 2px solid #fff; }

        /* RTE Placeholder */
        .bulk-rte { border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; }
        .rte-toolbar { background: #f9f9f9; border-bottom: 1px solid #e0e0e0; padding: 8px 12px; display: flex; gap: 10px; align-items: center; }
        .rte-tool-group { display: flex; gap: 2px; border-right: 1px solid #ddd; padding-right: 10px; }
        .rte-tool-group:last-child { border-right: none; }
        .rte-btn { background: none; border: none; padding: 5px 8px; border-radius: 4px; color: #555; cursor: pointer; transition: all .2s; }
        .rte-btn:hover { background: #eee; color: #000; }
        .rte-textarea { width: 100%; border: none; padding: 15px; min-height: 150px; font-size: 14px; color: #333; resize: vertical; }
        .rte-textarea:focus { outline: none; }

        .btn-save-red { background: #d0021b; color: #fff; border: none; padding: 12px 35px; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: background .2s; }
        .btn-save-red:hover { background: #b00217; }

        /* Modals */
        .faq-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; display: none; align-items: center; justify-content: center; }
        .faq-modal-overlay.open { display: flex; }
        .faq-modal { background: #fff; border-radius: 12px; width: 500px; max-width: 90%; box-shadow: 0 15px 40px rgba(0,0,0,0.2); overflow: hidden; }
        
        /* Media Library Modal */
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; padding: 25px; max-height: 60vh; overflow-y: auto; }
        .media-item { border: 2px solid transparent; border-radius: 8px; overflow: hidden; cursor: pointer; position: relative; aspect-ratio: 1; background: #f5f5f5; }
        .media-item.selected { border-color: #d0021b; }
        .media-item img { width: 100%; height: 100%; object-fit: cover; }
        .media-item-check { position: absolute; top: 5px; right: 5px; background: #d0021b; color: #fff; width: 22px; height: 22px; border-radius: 50%; display: none; align-items: center; justify-content: center; font-size: 12px; }
        .media-item.selected .media-item-check { display: flex; }

        /* Custom Dropdown for Filter */
        .rc-dropdown-wrap { position: relative; }
        .rc-dropdown-menu { position: absolute; top: 100%; left: 0; background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: none; z-index: 100; min-width: 200px; margin-top: 8px; overflow: hidden; }
        .rc-dropdown-menu.open { display: block; }
        .rc-dropdown-item { padding: 12px 20px; font-size: 13px; color: #555; cursor: pointer; transition: all .15s; }
        .rc-dropdown-item:hover { background: #f8f9fa; color: #333; }
        .rc-dropdown-item.active { background: #f0f2f5; color: #000; font-weight: 600; }

        /* Bulk Edit Styles */
        .bulk-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; z-index: 2000; display: none; flex-direction: column; }
        .bulk-overlay.open { display: flex; }
        .bulk-header { padding: 15px 30px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fff; }
        .bulk-body { flex: 1; overflow: auto; padding: 0; }
        .bulk-footer { padding: 15px 30px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 15px; background: #fff; }
        .bulk-table { width: 100%; border-collapse: collapse; min-width: 1500px; }
        .bulk-table th { position: sticky; top: 0; background: #f9f9f9; z-index: 10; padding: 12px 15px; text-align: left; font-size: 12px; font-weight: 700; color: #333; border-bottom: 1px solid #eee; }
        .bulk-table td { padding: 10px 15px; border-bottom: 1px solid #f0f0f0; }
        .bulk-edit-input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; }
        .bulk-edit-rte { width: 100%; min-height: 80px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; background: #fff; overflow-y: auto; }
        
        .toaster { position: fixed; bottom: 30px; right: 30px; background: #333; color: #fff; padding: 12px 25px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 3000; display: flex; align-items: center; gap: 10px; transform: translateY(100px); opacity: 0; transition: all .4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .toaster.show { transform: translateY(0); opacity: 1; }
        #mediaModalOverlay .btn-save-red:disabled { background: #f59e9e; cursor: not-allowed; }
        #mediaModalOverlay .btn-save-red { border-radius: 8px; padding: 10px 25px; }
        
        .media-pagination { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 30px; }
        .pag-btn { width: 35px; height: 35px; border: 1px solid #eef0f2; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #fff; color: #666; cursor: pointer; transition: all .2s; }
        .pag-btn:hover { border-color: #d0021b; color: #d0021b; }
        .pag-btn.active { background: #d0021b; border-color: #d0021b; color: #fff; }
        .pag-btn.disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent" style="padding:0; background: #f0f2f5; min-height: 100vh;">
    <div style="padding: 30px 45px;">
        <div class="page-header" style="margin-bottom: 25px; border: none; background: transparent; padding: 0;">
            <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222;">{{ __('Reusable Content: Review') }}</h2>
            <div class="rc-header-actions">
                <div class="rc-dropdown-wrap">
                    <button class="rc-btn-outline" type="button" id="filterBtn"><i class="bi bi-funnel"></i> Filter by Category <i class="bi bi-chevron-down" style="font-size: 10px; margin-left: 5px;"></i></button>
                    <div class="rc-dropdown-menu" id="filterMenu">
                        <div class="rc-dropdown-item active" data-id="All">All Categories</div>
                    </div>
                </div>
                <button class="rc-btn-outline" type="button" id="manageCatBtn"><i class="bi bi-folder2-open"></i> {{ __('Manage Categories') }}</button>
                <button class="rc-btn-outline" type="button" id="bulkEditBtn"><i class="bi bi-pencil-square"></i> {{ __('Bulk Edit') }}</button>
                <button class="rc-btn-add" type="button" id="addBtn"><i class="bi bi-plus-lg"></i> {{ __('Add New Review') }}</button>
            </div>
        </div>

        <div class="rc-wrapper">
            @include('dealer.partials.reusable-content-sidebar')
            
            <div style="flex: 1; display: flex; flex-direction: column;">
                {{-- List View --}}
                <div id="listView">
                    <div class="rc-main-container">
                        <table class="rc-table" id="reviewTable">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Title</th>
                                    <th style="width: 15%;">Category</th>
                                    <th style="width: 20%;">Author</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="reviewTableBody"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Add/Edit Form View --}}
                <div id="formView" style="display: none;">
                    <div style="margin-bottom: 25px;">
                        <button class="rc-btn-outline" id="backBtn" style="border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 25px;"><i class="bi bi-chevron-left"></i> Go Back</button>
                    </div>
                    <div class="rc-main-container" style="border: 1px solid #e0e0e0; border-radius: 8px;">
                        <div style="padding: 20px 30px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                            <h3 style="font-size: 15px; font-weight: 700; color: #333; margin: 0;" id="formViewTitle">Add Review</h3>
                        </div>
                        <div style="padding: 30px; background: #fff;">
                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Reviewer Name <span>*</span></label>
                                <input type="text" id="reviewerNameInput" class="bulk-input" placeholder="Jane Smith, Google, AutoTrader">
                            </div>

                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Review Headline</label>
                                <input type="text" id="reviewHeadlineInput" class="bulk-input" placeholder="Jane Smith, Google, AutoTrader">
                            </div>

                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Review Date</label>
                                <input type="date" id="reviewDateInput" class="bulk-input" placeholder="Sep. 9, 2025">
                            </div>

                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Review Source</label>
                                <select id="reviewSourceInput" class="bulk-select">
                                    <option value="">[Select]</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Google">Google</option>
                                    <option value="DealerRater">DealerRater</option>
                                    <option value="General Motors Service">General Motors Service</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Star Count</label>
                                <input type="number" id="starCountInput" class="bulk-input" min="1" max="5" value="5">
                            </div>

                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Category <span>*</span></label>
                                <select id="categorySelect" class="bulk-select"></select>
                            </div>

                            <div class="media-row">
                                <div class="media-header">
                                    <label class="bulk-col-label" style="margin: 0;">Review Photo URL</label>
                                    <span class="media-btn-select" id="selectMediaBtn" style="color: #d0021b;"><i class="bi bi-images"></i> Select Media</span>
                                </div>
                                <input type="text" id="photoUrlInput" class="bulk-input" placeholder="https://path.to.image/photo.jpg">
                                <div class="media-preview-wrap" id="photoPreviewWrap">
                                    <img src="" id="photoPreview">
                                    <span class="media-preview-remove" id="photoRemoveBtn">&times;</span>
                                </div>
                            </div>

                            <div style="margin-bottom: 40px;">
                                <label class="bulk-col-label">Review Content <span>*</span></label>
                                <div class="bulk-rte">
                                    <div class="rte-toolbar" style="background: #fff; padding: 10px 15px;">
                                        <select class="rte-dropdown" style="border:none; font-weight:600; color:#444;"><option>Normal</option></select>
                                        <div class="rte-tool-group">
                                            <button class="rte-btn" type="button" data-cmd="bold"><i class="bi bi-type-bold"></i></button>
                                            <button class="rte-btn" type="button" data-cmd="italic"><i class="bi bi-type-italic"></i></button>
                                            <button class="rte-btn" type="button" data-cmd="underline"><i class="bi bi-type-underline"></i></button>
                                        </div>
                                        <div class="rte-tool-group">
                                            <button class="rte-btn" type="button" data-cmd="justifyLeft"><i class="bi bi-text-left"></i></button>
                                            <button class="rte-btn" type="button" data-cmd="justifyCenter"><i class="bi bi-text-center"></i></button>
                                        </div>
                                        <div class="rte-tool-group">
                                            <button class="rte-btn" type="button" data-cmd="insertUnorderedList"><i class="bi bi-list-ul"></i></button>
                                            <button class="rte-btn" type="button" data-cmd="insertOrderedList"><i class="bi bi-list-ol"></i></button>
                                        </div>
                                        <div class="rte-tool-group">
                                            <button class="rte-btn" type="button" data-cmd="createLink"><i class="bi bi-link-45deg"></i></button>
                                        </div>
                                    </div>
                                    <div contenteditable="true" id="contentInput" class="rte-textarea" style="background: #fcfdfe; border-top: 1px solid #eef0f2;"></div>
                                </div>
                            </div>

                            <button class="btn-save-red" id="saveBtn" style="border-radius: 8px; padding: 12px 40px;"><i class="bi bi-check-lg"></i> Save</button>
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
        <div style="padding: 20px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700;" id="catModalTitle">Manage Categories</h3>
            <button style="background:none; border:none; font-size:24px; cursor:pointer;" id="catModalClose">&times;</button>
        </div>
        <div id="catModalBody"></div>
    </div>
</div>

<div class="faq-modal-overlay" id="mediaModalOverlay">
    <div class="faq-modal" style="width: 1100px; max-width: 95vw; height: 90vh;">
        <div style="padding: 20px 30px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #333;">Select Media</h3>
            <button style="background:none; border:none; font-size:24px; cursor:pointer; color: #999;" id="mediaModalClose">&times;</button>
        </div>
        <div style="padding: 25px 30px; border-bottom: 1px solid #f9f9f9; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 14px; font-weight: 700; color: #333;">Select image:</div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div style="width: 320px;"><input type="text" id="mediaSearchInput" class="bulk-input" placeholder="Search media..."></div>
                <button class="btn-save-red" style="padding: 10px 22px; font-size: 13px;"><i class="bi bi-plus-lg"></i> Add Media</button>
            </div>
        </div>
        <div style="padding: 15px 30px; display: flex; justify-content: flex-end; align-items: center; gap: 10px; border-bottom: 1px solid #f9f9f9;">
            <div style="font-size: 12px; color: #666; background: #f8f9fa; padding: 8px 15px; border: 1px solid #eef0f2; border-radius: 4px 0 0 4px; border-right: none;">Display</div>
            <select class="bulk-select" style="width: 150px; padding: 7px 10px; font-size: 12px; border-radius: 0 4px 4px 0;">
                <option>Compact</option>
                <option>Large</option>
            </select>
        </div>
        <div class="media-grid" id="mediaGrid" style="padding: 30px; border-bottom: 1px solid #f9f9f9;"></div>
        <div style="padding: 20px 30px; background: #fff; display: flex; flex-direction: column; align-items: center;">
            <div style="width: 100%; display: flex; justify-content: flex-end; margin-bottom: 20px;">
                <button class="btn-save-red" id="mediaSelectBtn" disabled style="padding: 12px 35px;"><i class="bi bi-check-lg"></i> Select Media</button>
            </div>
            <div class="media-pagination" id="mediaPagination"></div>
            <div style="margin-top: 15px; font-size: 13px; color: #888;" id="mediaShowingText"></div>
        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="faq-modal-overlay" id="confirmModalOverlay">
    <div class="faq-modal" style="width: 400px;">
        <div style="padding: 30px; text-align: center;">
            <div style="width: 60px; height: 60px; background: #fff5f5; color: #d0021b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 20px;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h3 style="margin: 0 0 10px; font-size: 18px; font-weight: 700; color: #333;">Are you sure?</h3>
            <p style="margin: 0 0 25px; color: #666; font-size: 14px;" id="confirmModalBodyText">This action cannot be undone.</p>
            <div style="display: flex; gap: 10px;">
                <button class="rc-btn-outline" style="flex: 1; justify-content: center;" id="confirmCancelBtn">Cancel</button>
                <button class="btn-save-red" style="flex: 1; justify-content: center; background: #d0021b;" id="confirmContinueBtn">Continue</button>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Edit Overlay --}}
<div class="bulk-overlay" id="bulkOverlay">
    <div class="bulk-header">
        <h2 style="font-size: 18px; font-weight: 700; margin: 0; color: #333;">Bulk Edit Reviews</h2>
        <button class="rc-btn-outline" id="bulkEditCancelBtn">Cancel</button>
    </div>
    <div class="bulk-body">
        <table class="bulk-table">
            <thead>
                <tr>
                    <th style="width: 200px;">Reviewer Name</th>
                    <th style="width: 200px;">Headline</th>
                    <th style="width: 150px;">Date</th>
                    <th style="width: 150px;">Source</th>
                    <th style="width: 100px;">Stars</th>
                    <th style="width: 150px;">Category</th>
                    <th style="width: 300px;">Photo URL</th>
                    <th style="width: 400px;">Content</th>
                </tr>
            </thead>
            <tbody id="bulkTableBody"></tbody>
        </table>
        <div style="padding: 20px; text-align: center;">
            <button class="rc-btn-outline" style="margin: 0 auto;" id="addBulkRowBtn"><i class="bi bi-plus-lg"></i> Add Another Row</button>
        </div>
    </div>
    <div class="bulk-footer">
        <button class="btn-save-red" id="bulkEditSaveBtn">Save Changes</button>
    </div>
</div>

@endsection

@push('page-scripts')
<script>
(function(){
    var reviews = @json($reviews);
    var categories = @json($categories);
    var media = [];
    var editingId = null;
    var selectedMediaUrl = null;

    var ROUTES = {
        store: "{{ route('dealer.website.customer-reviews.store') }}",
        update: "{{ route('dealer.website.customer-reviews.update', '__ID__') }}",
        destroy: "{{ route('dealer.website.customer-reviews.destroy', '__ID__') }}",
        bulkUpdate: "{{ route('dealer.website.customer-reviews.bulk-update') }}",
        catStore: "{{ route('dealer.website.customer-reviews.categories.store') }}",
        catUpdate: "{{ route('dealer.website.customer-reviews.categories.update', '__ID__') }}",
        catDestroy: "{{ route('dealer.website.customer-reviews.categories.destroy', '__ID__') }}",
        mediaList: "{{ route('dealer.website.media.list') }}"
    };

    function ajax(method, url, data, cb) {
        var xhr = new XMLHttpRequest();
        xhr.open(method, url);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.onload = function() {
            var res = null;
            try { res = JSON.parse(xhr.responseText); } catch(e) {}
            if (xhr.status >= 200 && xhr.status < 300) cb(null, res);
            else cb(res && res.message ? res.message : 'Error processing request');
        };
        xhr.send(data ? JSON.stringify(data) : null);
    }

    function showToaster(text) {
        var container = document.getElementById('mainContent');
        var t = document.createElement('div');
        t.className = 'toaster';
        t.innerHTML = '<i class="bi bi-check-circle-fill" style="color:#28a745;"></i> ' + text;
        container.appendChild(t);
        setTimeout(function(){ t.classList.add('show'); }, 10);
        setTimeout(function(){ t.classList.remove('show'); setTimeout(function(){ t.remove(); }, 400); }, 3000);
    }

    var confirmOverlay = document.getElementById('confirmModalOverlay');
    var confirmCallback = null;
    function customConfirm(text, cb) {
        document.getElementById('confirmModalBodyText').textContent = text;
        confirmCallback = cb;
        confirmOverlay.classList.add('open');
    }
    document.getElementById('confirmCancelBtn').onclick = function(){ confirmOverlay.classList.remove('open'); };
    document.getElementById('confirmContinueBtn').onclick = function(){ if(confirmCallback) confirmCallback(); confirmOverlay.classList.remove('open'); };

    function getDisplayStatus(b) {
        if (b.status === 'Inactive') return 'Draft';
        return 'Active';
    }

    function renderTable(){
        var catFilter = selectedCategoryId;
        var tbody = document.getElementById('reviewTableBody');
        var html = '';
        var filtered = reviews.filter(function(b){
            var catMatch = (catFilter === 'All' || b.customer_review_category_id == catFilter);
            return catMatch;
        });

        filtered.forEach(function(b){
            var status = getDisplayStatus(b);
            html += '<tr>' +
                '<td><div class="rc-title-cell"><span class="rc-drag"><i class="bi bi-list"></i></span>' +
                '<div class="rc-content-wrap"><div class="rc-nickname">'+b.reviewer_name+'</div>' +
                '<div class="rc-row-actions"><a href="#edit/'+b.id+'" class="rc-row-btn"><i class="bi bi-pencil-square"></i> Edit</a>' +
                '<button class="rc-row-btn trash-btn" onclick="window._trash('+b.id+')"><i class="bi bi-trash"></i> Trash</button></div></div></div></td>' +
                '<td><span style="color:#666; font-size:14px;">'+(b.category?b.category.name:'Uncategorized')+'</span></td>' +
                '<td><span style="color:#666; font-size:14px;">'+(b.author||'System')+'</span></td>' +
                '<td><div class="status-wrap"><span class="status-dot '+status.toLowerCase()+'"></span><span style="font-size:14px;">'+(status==='Active'?'Currently Active':status)+'</span></div></td>' +
                '</tr>';
        });
        tbody.innerHTML = html || '<tr><td colspan="4" style="text-align:center;padding:50px;color:#999;">No results found.</td></tr>';
    }

    var selectedCategoryId = 'All';
    document.getElementById('filterBtn').onclick = function(e){ e.stopPropagation(); document.getElementById('filterMenu').classList.toggle('open'); };
    document.addEventListener('click', function(){ document.getElementById('filterMenu').classList.remove('open'); });

    function populateFilters(){
        var menu = document.getElementById('filterMenu');
        var html = '<div class="rc-dropdown-item '+(selectedCategoryId === 'All' ? 'active' : '')+'" data-id="All">All Categories</div>';
        categories.forEach(function(c){ 
            html += '<div class="rc-dropdown-item '+(selectedCategoryId == c.id ? 'active' : '')+'" data-id="'+c.id+'">'+c.name+'</div>'; 
        });
        menu.innerHTML = html;
        
        menu.querySelectorAll('.rc-dropdown-item').forEach(function(item){
            item.onclick = function(){
                selectedCategoryId = this.dataset.id;
                document.getElementById('filterBtn').innerHTML = '<i class="bi bi-funnel"></i> ' + (selectedCategoryId === 'All' ? 'Filter by Category' : this.textContent) + ' <i class="bi bi-chevron-down" style="font-size: 10px; margin-left: 5px;"></i>';
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
    document.getElementById('addBtn').onclick = function(){ window.location.hash = '#add'; };
    document.getElementById('backBtn').onclick = function(){ window.location.hash = '#list'; };
    window.onhashchange = navigate;

    function openForm(id){
        editingId = id;
        var item = id ? reviews.find(function(b){ return b.id == id; }) : null;
        document.getElementById('formViewTitle').textContent = item ? 'Edit Review' : 'Add Review';
        document.getElementById('reviewerNameInput').value = item ? item.reviewer_name : '';
        document.getElementById('reviewHeadlineInput').value = item ? (item.review_headline||'') : '';
        document.getElementById('reviewDateInput').value = item && item.review_date ? item.review_date.substring(0,10) : '';
        document.getElementById('reviewSourceInput').value = item ? (item.review_source||'') : '';
        document.getElementById('starCountInput').value = item ? item.star_count : 5;
        document.getElementById('photoUrlInput').value = item ? (item.photo_url||'') : '';
        document.getElementById('contentInput').innerHTML = item ? (item.content||'') : '';
        
        updatePhotoPreview(item ? (item.photo_url||'') : '');

        var catSel = document.getElementById('categorySelect');
        catSel.innerHTML = '<option value="">Uncategorized</option>';
        categories.forEach(function(c){ catSel.innerHTML += '<option value="'+c.id+'"'+(item && item.customer_review_category_id == c.id ? ' selected' : '')+'>'+c.name+'</option>'; });

        document.getElementById('listView').style.display = 'none';
        document.getElementById('formView').style.display = 'block';
    }

    function updatePhotoPreview(url) {
        var wrap = document.getElementById('photoPreviewWrap');
        var img = document.getElementById('photoPreview');
        if(url) { img.src = url; wrap.style.display = 'block'; }
        else { wrap.style.display = 'none'; }
    }

    document.getElementById('photoRemoveBtn').onclick = function(){
        document.getElementById('photoUrlInput').value = '';
        updatePhotoPreview('');
    };

    document.getElementById('saveBtn').onclick = function(){
        var p = {
            reviewer_name: document.getElementById('reviewerNameInput').value.trim(),
            review_headline: document.getElementById('reviewHeadlineInput').value.trim(),
            review_date: document.getElementById('reviewDateInput').value,
            review_source: document.getElementById('reviewSourceInput').value,
            star_count: document.getElementById('starCountInput').value,
            customer_review_category_id: document.getElementById('categorySelect').value,
            photo_url: document.getElementById('photoUrlInput').value.trim(),
            content: document.getElementById('contentInput').innerHTML.trim(),
            status: 'Active'
        };

        if(!p.reviewer_name) return alert('Reviewer Name is required');
        if(!p.customer_review_category_id) return alert('Category is required');
        if(!p.content) return alert('Review Content is required');

        ajax(editingId?'PATCH':'POST', editingId?ROUTES.update.replace('__ID__', editingId):ROUTES.store, p, function(err, res){
            if(err) return alert(err);
            if(editingId){ var idx = reviews.findIndex(function(b){ return b.id == editingId; }); if(idx>-1) reviews[idx] = res; }
            else reviews.push(res);
            window.location.hash = '#list'; renderTable(); showToaster('Review saved.');
        });
    };

    window._trash = function(id){
        customConfirm('Are you sure you want to delete this review?', function(){
            ajax('DELETE', ROUTES.destroy.replace('__ID__', id), null, function(err){
                if(err) return alert(err);
                reviews = reviews.filter(function(b){ return b.id != id; }); renderTable(); showToaster('Review deleted.');
            });
        });
    };

    // [Category Management]
    var catOverlay = document.getElementById('catModalOverlay');
    document.getElementById('manageCatBtn').onclick = showCatList;
    document.getElementById('catModalClose').onclick = function(){ catOverlay.classList.remove('open'); };

    function showCatList(){
        var body = document.getElementById('catModalBody');
        var html = '<div style="padding:10px;">';
        categories.forEach(function(c){
            html += '<div style="display:flex; justify-content:space-between; padding:15px; border-bottom:1px solid #f0f0f0; align-items:center;">' +
                    '<span>'+c.name+'</span>' +
                    '<div style="display:flex; gap:10px;">' +
                        '<button class="rc-row-btn" onclick="window._editCat('+c.id+')"><i class="bi bi-pencil"></i></button>' +
                        '<button class="rc-row-btn trash-btn" onclick="window._delCat('+c.id+')"><i class="bi bi-trash"></i></button>' +
                    '</div></div>';
        });
        html += '<div style="padding:20px; text-align:center;"><button class="rc-btn-add" id="catAddBtn" style="margin: 0 auto;"><i class="bi bi-plus-lg"></i> Add Category</button></div></div>';
        body.innerHTML = html;
        catOverlay.classList.add('open');
        document.getElementById('catAddBtn').onclick = function(){ showCatEdit(null); };
    }

    window._editCat = function(id){ var c = categories.find(function(x){ return x.id == id; }); showCatEdit(c); };
    window._delCat = function(id){
        customConfirm('Delete category?', function(){
            ajax('DELETE', ROUTES.catDestroy.replace('__ID__', id), null, function(err){
                if(err) return alert(err);
                categories = categories.filter(function(x){ return x.id !== id; }); 
                showCatList(); 
                populateFilters();
                showToaster('Category deleted.');
            });
        });
    };

    function showCatEdit(cat){
        var isNew = !cat;
        var body = document.getElementById('catModalBody');
        body.innerHTML = '<div style="padding:25px;">' +
            '<label class="bulk-col-label">Category Name</label>' +
            '<input type="text" id="catNameInput" class="bulk-input" value="'+(isNew?'':cat.name)+'">' +
            '<div style="display:flex; gap:10px; margin-top:20px;">' +
                '<button class="btn-save-red" id="catSaveBtn">'+(isNew?'Add':'Save')+'</button>' +
                '<button class="rc-btn-outline" onclick="showCatList()">Back</button>' +
            '</div></div>';
        document.getElementById('catSaveBtn').onclick = function(){
            var val = document.getElementById('catNameInput').value.trim();
            if(!val) return;
            ajax(isNew?'POST':'PATCH', isNew?ROUTES.catStore:ROUTES.catUpdate.replace('__ID__', cat.id), {name:val}, function(err, res){
                if(err) return alert(err);
                if(isNew) categories.push(res); else { var idx = categories.findIndex(function(x){ return x.id == cat.id; }); categories[idx] = res; }
                showCatList(); populateFilters(); showToaster('Category saved.');
            });
        };
    }

    // [Media Library]
    var mediaOverlay = document.getElementById('mediaModalOverlay');
    var mediaGrid = document.getElementById('mediaGrid');
    var mediaSelectBtn = document.getElementById('mediaSelectBtn');
    var activeMediaField = null;

    document.getElementById('selectMediaBtn').onclick = function(){ openMediaModal('photoUrlInput'); };
    document.getElementById('mediaModalClose').onclick = function(){ mediaOverlay.classList.remove('open'); };

    var mediaCurrentPage = 1;
    var mediaTotalPages = 1;

    function openMediaModal(field){
        activeMediaField = field;
        mediaOverlay.classList.add('open');
        loadMedia(1);
    }

    function loadMedia(page){
        mediaCurrentPage = page || 1;
        var url = ROUTES.mediaList + '?page=' + mediaCurrentPage;
        ajax('GET', url, null, function(err, res){
            if(err) return alert(err);
            media = res.data;
            mediaTotalPages = res.last_page;
            renderMedia();
            renderMediaPagination(res);
        });
    }

    function renderMedia(){
        var html = '';
        media.forEach(function(m){
            html += '<div class="media-item" data-url="'+m.url+'">' +
                    '<img src="'+m.url+'">' +
                    '<div class="media-item-check"><i class="bi bi-check"></i></div>' +
                    '</div>';
        });
        mediaGrid.innerHTML = html;
        mediaGrid.querySelectorAll('.media-item').forEach(function(item){
            item.onclick = function(){
                mediaGrid.querySelectorAll('.media-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                selectedMediaUrl = this.dataset.url;
                mediaSelectBtn.disabled = false;
            };
        });
    }

    function renderMediaPagination(res){
        var pag = document.getElementById('mediaPagination');
        var text = document.getElementById('mediaShowingText');
        text.textContent = 'Showing ' + res.from + ' to ' + res.to + ' of ' + res.total;

        var html = '';
        html += '<button class="pag-btn '+(res.current_page === 1 ? 'disabled' : '')+'" onclick="if(mediaCurrentPage>1) loadMedia(mediaCurrentPage-1)"><i class="bi bi-chevron-left"></i></button>';
        
        for(var i=1; i<=res.last_page; i++){
            if(i === 1 || i === res.last_page || (i >= res.current_page - 1 && i <= res.current_page + 1)){
                html += '<button class="pag-btn '+(i === res.current_page ? 'active' : '')+'" onclick="loadMedia('+i+')">'+i+'</button>';
            } else if(i === 2 || i === res.last_page - 1){
                html += '<span style="color:#ccc;">...</span>';
            }
        }

        html += '<button class="pag-btn '+(res.current_page === res.last_page ? 'disabled' : '')+'" onclick="if(mediaCurrentPage<mediaTotalPages) loadMedia(mediaCurrentPage+1)"><i class="bi bi-chevron-right"></i></button>';
        pag.innerHTML = html;
    }

    mediaSelectBtn.onclick = function(){
        if(!selectedMediaUrl) return;
        var input = document.getElementById(activeMediaField);
        if(input) {
            input.value = selectedMediaUrl;
            if(activeMediaField === 'photoUrlInput') updatePhotoPreview(selectedMediaUrl);
        }
        mediaOverlay.classList.remove('open');
    };

    // [Bulk Edit]
    var bulkOverlay = document.getElementById('bulkOverlay');
    var bulkTableBody = document.getElementById('bulkTableBody');
    document.getElementById('bulkEditBtn').onclick = openBulkEdit;
    document.getElementById('bulkEditCancelBtn').onclick = function(){ bulkOverlay.classList.remove('open'); };
    document.getElementById('addBulkRowBtn').onclick = function(){ addBulkRow(null); };

    function openBulkEdit(){
        bulkTableBody.innerHTML = '';
        if(reviews.length > 0) reviews.forEach(function(b){ addBulkRow(b); });
        else addBulkRow(null);
        bulkOverlay.classList.add('open');
    }

    function addBulkRow(item){
        var row = document.createElement('tr');
        row.dataset.id = item ? item.id : '';
        
        row.innerHTML = `
            <td><input type="text" class="bulk-edit-input bulk-reviewer-name" value="${item ? item.reviewer_name : ''}"></td>
            <td><input type="text" class="bulk-edit-input bulk-headline" value="${item ? item.review_headline || '' : ''}"></td>
            <td><input type="date" class="bulk-edit-input bulk-date" value="${item && item.review_date ? item.review_date.substring(0,10) : ''}"></td>
            <td>
                <select class="bulk-edit-input bulk-source">
                    <option value="">[Select]</option>
                    <option value="Facebook" ${item && item.review_source == 'Facebook' ? 'selected' : ''}>Facebook</option>
                    <option value="Google" ${item && item.review_source == 'Google' ? 'selected' : ''}>Google</option>
                    <option value="DealerRater" ${item && item.review_source == 'DealerRater' ? 'selected' : ''}>DealerRater</option>
                    <option value="General Motors Service" ${item && item.review_source == 'General Motors Service' ? 'selected' : ''}>General Motors Service</option>
                </select>
            </td>
            <td><input type="number" class="bulk-edit-input bulk-stars" value="${item ? item.star_count : 5}" min="1" max="5"></td>
            <td>
                <select class="bulk-edit-input bulk-category">
                    <option value="">[Select]</option>
                    ${categories.map(c => `<option value="${c.id}" ${item && item.customer_review_category_id == c.id ? 'selected' : ''}>${c.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <div style="display:flex; gap:5px;">
                    <input type="text" class="bulk-edit-input bulk-photo-url" value="${item ? item.photo_url || '' : ''}">
                    <button class="rc-btn-outline" style="padding: 5px 10px;" onclick="window._bulkMedia(this)"><i class="bi bi-images"></i></button>
                </div>
            </td>
            <td><div contenteditable="true" class="bulk-edit-rte bulk-content">${item ? item.content : ''}</div></td>
        `;
        bulkTableBody.appendChild(row);
    }

    window._bulkMedia = function(btn){
        var input = btn.previousElementSibling;
        window._activeBulkInput = input;
        openMediaModal('__BULK__');
    };

    // Override mediaSelectBtn for bulk
    var originalMediaSelect = mediaSelectBtn.onclick;
    mediaSelectBtn.onclick = function(){
        if(activeMediaField === '__BULK__'){
            if(window._activeBulkInput) window._activeBulkInput.value = selectedMediaUrl;
            mediaOverlay.classList.remove('open');
        } else {
            originalMediaSelect();
        }
    };

    document.getElementById('bulkEditSaveBtn').onclick = function(){
        var rows = bulkTableBody.querySelectorAll('tr');
        var data = [];
        rows.forEach(function(row){
            var name = row.querySelector('.bulk-reviewer-name').value.trim();
            if(!name) return;
            data.push({
                id: row.dataset.id || null,
                reviewer_name: name,
                review_headline: row.querySelector('.bulk-headline').value,
                review_date: row.querySelector('.bulk-date').value,
                review_source: row.querySelector('.bulk-source').value,
                star_count: row.querySelector('.bulk-stars').value,
                customer_review_category_id: row.querySelector('.bulk-category').value || null,
                photo_url: row.querySelector('.bulk-photo-url').value,
                content: row.querySelector('.bulk-content').innerHTML,
                status: 'Active'
            });
        });

        ajax('POST', ROUTES.bulkUpdate, {reviews: data}, function(err, res){
            if(err) return alert(err);
            reviews = res; renderTable(); bulkOverlay.classList.remove('open'); showToaster('Bulk data saved.');
        });
    };

    populateFilters();
    navigate();
    renderTable();
})();

// RTE Logic
document.querySelectorAll('.rte-btn').forEach(btn => {
    btn.onclick = function(){
        var cmd = this.dataset.cmd;
        var val = null;
        if(cmd === 'createLink') val = prompt('Enter URL:');
        document.execCommand(cmd, false, val);
    };
});
</script>
@endpush
