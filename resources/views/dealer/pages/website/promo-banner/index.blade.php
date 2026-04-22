@extends('layouts.dealer.app')
@section('title', __('Reusable Content: Promo') . ' | ' . __(config('app.name')))

@push('page-styles')
    <style>
        /* Main Layout */
        .rc-wrapper { display: flex; gap: 40px; min-height: calc(100vh - 160px); padding: 20px 0; }
        .rc-sidebar { width: 260px; min-width: 260px; }
        .rc-sidebar-item { display: flex; align-items: center; gap: 12px; padding: 15px; font-size: 14px; color: #666; cursor: pointer; transition: all .2s; text-decoration: none; border-bottom: 1px solid #f0f0f0; }
        .rc-sidebar-item:last-child { border-bottom: none; }
        .rc-sidebar-item:hover { background: #f8f8f8; color: #333; }
        .rc-sidebar-item.active { color: #000; font-weight: 650; background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .rc-sidebar-item i { font-size: 18px !important; width: 24px; text-align: center; color: #999; }
        .rc-sidebar-item.active i { color: #c0392b !important; }

        .rc-main-container { flex: 1; background: #fff; border: 1px solid #eef0f2; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); display: flex; flex-direction: column; overflow: hidden; height: fit-content; }

        /* Header Actions */
        .rc-header-actions { display: flex; align-items: center; gap: 12px; }
        .rc-btn-outline { display: inline-flex; align-items: center; gap: 8px; border: 1px solid #e0e0e0; background: #fff; font-size: 13px; color: #555; cursor: pointer; padding: 8px 18px; border-radius: 8px; white-space: nowrap; transition: all .15s; font-weight: 500; text-decoration: none; }
        .rc-btn-outline:hover { background: #f8f9fa; border-color: #d0d0d0; }
        .rc-btn-add { display: inline-flex; align-items: center; gap: 8px; background: #c0392b; color: #fff; border: none; padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; }
        .rc-btn-add:hover { background: #a93226; }

        /* Table Styling */
        .rc-table { width: 100%; border-collapse: collapse; }
        .rc-table th { padding: 18px 20px; font-weight: 700; color: #333; text-align: left; font-size: 12px; border-bottom: 1px solid #f0f0f0; background: #fff; }
        .rc-table td { padding: 25px 20px; vertical-align: middle; border-bottom: 1px solid #f8f9fa; background: #fff; }
        
        .rc-title-cell { display: flex; align-items: flex-start; gap: 15px; }
        .rc-drag { color: #ddd; cursor: grab; font-size: 16px; padding-top: 2px; }
        .rc-content-wrap { display: flex; flex-direction: column; gap: 12px; }
        .rc-nickname { font-size: 15px; font-weight: 500; color: #333; line-height: 1.4; }
        .rc-row-actions { display: flex; align-items: center; gap: 10px; }
        .rc-row-btn { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 12px; color: #666; background: #fff; cursor: pointer; text-decoration: none; transition: all .2s; }
        .rc-row-btn:hover { background: #f8f8f8; color: #333; border-color: #ccc; }
        .rc-row-btn.trash-btn { color: #c0392b; }

        /* Status Dot */
        .status-wrap { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #444; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; }
        .status-dot.active { background: #28a745 !important; }
        .status-dot.expired { background: #c0392b !important; }
        .status-dot.draft { background: #ffc107 !important; }
        .status-dot.scheduled { background: #17a2b8 !important; }

        /* Form Styling */
        .bulk-col-label { font-size: 13px; font-weight: 700; color: #444; margin-bottom: 10px; display: block; }
        .bulk-col-label span { color: #c0392b; margin-left: 2px; }
        .bulk-input { width: 100%; padding: 10px 15px; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #333; background: #fff; transition: border-color .2s; height: 42px; }
        .bulk-input:focus { border-color: #c0392b; outline: none; }
        .bulk-select { width: 100%; padding: 10px 15px; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #333; background: #fff; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 15px center; height: 42px; }
        
        /* Date Picker Styling */
        .date-picker-wrap { position: relative; display: flex; align-items: stretch; border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden; background: #fff; height: 42px; }
        .date-picker-icon-box { background: #f8f9fa; border-right: 1px solid #e0e0e0; padding: 0 15px; display: flex; align-items: center; color: #666; cursor: pointer; }
        .date-picker-input { border: none !important; flex: 1; padding: 0 15px; font-size: 14px; color: #333; background: transparent; height: 100%; }
        .date-picker-input:focus { outline: none; }

        .hint-box { background: #f7f7f7; border: 1px solid #e0e0e0; border-top: none; padding: 12px 15px; border-radius: 0 0 4px 4px; margin-top: -1px; font-size: 13px; color: #666; line-height: 1.5; }

        /* RTE Toolbar */
        .bulk-rte { border: 1px solid #e0e0e0; border-radius: 4px; background: #fff; overflow: hidden; }
        .rte-toolbar { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 8px 12px; display: flex; align-items: center; gap: 15px; }
        .rte-dropdown { padding: 5px 10px; border: 1px solid #eee; border-radius: 4px; font-size: 13px; color: #666; cursor: pointer; background: #fff; }
        .rte-tool-group { display: flex; align-items: center; gap: 8px; border-right: 1px solid #eee; padding-right: 12px; }
        .rte-tool-group:last-child { border-right: none; }
        .rte-btn { background: none; border: none; padding: 4px 8px; cursor: pointer; color: #666; transition: all .2s; font-size: 16px; border-radius: 3px; display: flex; align-items: center; justify-content: center; }
        .rte-btn:hover { background: #f5f5f5; color: #333; }
        .rte-textarea { width: 100%; border: none; outline: none; font-size: 14px; color: #444; min-height: 150px; padding: 20px; line-height: 1.6; background: #fff; resize: vertical; }

        /* Media */
        .media-row { margin-bottom: 30px; }
        .media-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .media-btn-select { color: #c0392b; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; }
        .media-btn-select:hover { text-decoration: underline; }
        .media-preview-wrap { margin-top: 15px; border: 1px solid #eee; border-radius: 8px; overflow: hidden; display: none; position: relative; width: fit-content; max-width: 100%; }
        .media-preview-wrap img { max-height: 200px; display: block; }
        .media-preview-remove { position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.5); color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; }

        /* Colors */
        .color-picker-wrap { position: relative; display: flex; align-items: center; gap: 10px; }
        .color-box-btn { width: 34px; height: 34px; border: 1px solid #e0e0e0; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; background: #fff; flex-shrink: 0; }
        .color-preview { width: 20px; height: 20px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1); }
        .color-popup { position: absolute; bottom: calc(100% + 5px); left: 0; background: #fff; border: 1px solid #eee; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 15px; z-index: 1000; display: none; width: 220px; }
        .color-popup.open { display: block; }
        .color-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin-bottom: 12px; }
        .color-swatch { width: 24px; height: 24px; border-radius: 4px; cursor: pointer; border: 1px solid rgba(0,0,0,0.05); }

        /* Modals */
        .faq-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 2000; display: none; backdrop-filter: blur(2px); align-items: center; justify-content: center; }
        .faq-modal-overlay.open { display: flex !important; }
        .faq-modal { background: #fff; border-radius: 14px; width: 550px; max-width: 95vw; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        
        /* Media Grid */
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; padding: 25px; flex: 1; overflow-y: auto; }
        .media-item { aspect-ratio: 1; border: 2px solid transparent; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all .2s; }
        .media-item:hover { transform: scale(1.02); }
        .media-item.selected { border-color: #c0392b; box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.1); }
        .media-item img { width: 100%; height: 100%; object-fit: cover; }

        /* Toaster */
        .toaster-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
        .toaster { background: #28a745; color: #fff; padding: 12px 20px; border-radius: 6px; display: flex; align-items: center; gap: 12px; min-width: 250px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transform: translateX(120%); transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
        .toaster.show { transform: translateX(0); }

        /* Confirm Modal */
        .confirm-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 3000; display: none; backdrop-filter: blur(2px); align-items: center; justify-content: center; }
        .confirm-modal-overlay.open { display: flex !important; }
        .confirm-modal { background: #fff; border-radius: 8px; width: 400px; max-width: 90vw; box-shadow: 0 10px 40px rgba(0,0,0,0.2); overflow: hidden; }
        .confirm-modal-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .confirm-modal-title { font-size: 14px; font-weight: 600; color: #333; display: flex; align-items: center; gap: 10px; }
        .confirm-modal-footer { padding: 12px 20px; display: flex; justify-content: flex-end; gap: 10px; }

        .btn-save-red { background: #c0392b; color: #fff; border: none; border-radius: 4px; padding: 10px 25px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .btn-save-red:hover { background: #a93226; }
        .btn-cancel-outline { padding: 8px 24px; border: 1px solid #e0e0e0; background: #fff; color: #666; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; }
    </style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent" style="padding:0; background: #f9f9f9; min-height: 100vh;">
    <div style="padding: 30px 45px;">
        <div class="page-header" style="margin-bottom: 25px; border: none;">
            <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222;">{{ __('Reusable Content: Promo') }}</h2>
            <div class="rc-header-actions">
                <button class="rc-btn-outline" type="button" id="manageCatBtn"><i class="bi bi-folder2-open"></i> {{ __('Manage Categories') }}</button>
                <button class="rc-btn-outline" type="button" id="bulkEditBtn"><i class="bi bi-pencil-square"></i> {{ __('Bulk Edit') }}</button>
                <button class="rc-btn-add" type="button" id="addBtn"><i class="bi bi-plus-lg"></i> {{ __('Add New Promo') }}</button>
            </div>
        </div>

        <div class="rc-wrapper">
            <div class="rc-sidebar">
                <a href="{{ route('dealer.website.faqs.index') }}" class="rc-sidebar-item"><i class="bi bi-question-circle"></i> FAQs</a>
                <a href="{{ route('dealer.website.promo-banners.index') }}" class="rc-sidebar-item active"><i class="bi bi-megaphone"></i> OEM Promo Banners</a>
                <a href="{{ route('dealer.website.srp-content.index') }}" class="rc-sidebar-item"><i class="bi bi-file-earmark-text"></i> Content: Search Results (SRP)</a>
                <a href="{{ route('dealer.website.static-page-content.index') }}" class="rc-sidebar-item"><i class="bi bi-file-text"></i> Static Page Content</a>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column;">
                {{-- List View --}}
                <div id="listView">
                    <div class="rc-main-container">
                        <div style="padding: 20px 25px; border-bottom: 1px solid #f0f0f0;">
                            <label style="font-size: 12px; font-weight: 700; color: #333; margin-bottom: 8px; display: block;">Filter Status</label>
                            <select id="statusFilter" class="bulk-select" style="width: 250px;">
                                <option value="Active">Currently Active</option>
                                <option value="Expired">Expired</option>
                                <option value="All">All Items</option>
                            </select>
                        </div>
                        <table class="rc-table" id="promoTable">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Title</th>
                                    <th style="width: 15%;">Category</th>
                                    <th style="width: 20%;">Author</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="promoTableBody"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Add/Edit Form View --}}
                <div id="formView" style="display: none;">
                    <div style="margin-bottom: 25px;">
                        <button class="rc-btn-outline" id="backBtn" style="border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 25px;"><i class="bi bi-chevron-left"></i> Go Back</button>
                    </div>
                    <div class="rc-main-container" style="border: 1px solid #e0e0e0; border-radius: 8px;">
                        <div style="padding: 15px 30px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                            <h3 style="font-size: 15px; font-weight: 700; color: #333; margin: 0;" id="formViewTitle">Add Promo</h3>
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('dealer.website.promo-banners.download-template') }}" class="rc-btn-outline" style="height: 34px; padding: 0 15px; font-size: 12px; border-color: #eee;"><i class="bi bi-file-earmark-text"></i> Template</a>
                                <button class="rc-btn-outline" id="uploadCsvBtn" style="height: 34px; padding: 0 15px; font-size: 12px; border-color: #eee;"><i class="bi bi-cloud-upload"></i> Upload CSV</button>
                                <input type="file" id="csvFileInput" style="display: none;" accept=".csv">
                            </div>
                        </div>
                        <div style="padding: 30px; background: #fff;">
                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Promo Title / Image Alt Text <span>*</span></label>
                                <input type="text" id="titleInput" class="bulk-input" placeholder="Promo Title">
                            </div>

                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Disclaimer <span>*</span></label>
                                <div class="bulk-rte">
                                    <div class="rte-toolbar">
                                        <select class="rte-dropdown"><option>Normal</option></select>
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
                                            <button class="rte-btn" type="button" data-cmd="outdent"><i class="bi bi-text-indent-left"></i></button>
                                            <button class="rte-btn" type="button" data-cmd="indent"><i class="bi bi-text-indent-right"></i></button>
                                        </div>
                                        <div class="rte-tool-group">
                                            <button class="rte-btn" type="button" data-cmd="createLink"><i class="bi bi-link-45deg"></i></button>
                                        </div>
                                    </div>
                                    <textarea id="disclaimerInput" class="rte-textarea" placeholder=""></textarea>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                                <div>
                                    <label class="bulk-col-label">Category</label>
                                    <select id="categorySelect" class="bulk-select"></select>
                                </div>
                                <div>
                                    <label class="bulk-col-label">Start Date</label>
                                    <div class="date-picker-wrap">
                                        <div class="date-picker-icon-box"><i class="bi bi-calendar3"></i></div>
                                        <input type="date" id="startDateInput" class="date-picker-input">
                                    </div>
                                </div>
                                <div>
                                    <label class="bulk-col-label">End Date</label>
                                    <div class="date-picker-wrap">
                                        <div class="date-picker-icon-box"><i class="bi bi-calendar3"></i></div>
                                        <input type="date" id="endDateInput" class="date-picker-input">
                                    </div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                                <div>
                                    <label class="bulk-col-label">Condition</label>
                                    <select id="conditionInput" class="bulk-select">
                                        <option value="">[Select]</option>
                                        <option value="New">New</option>
                                        <option value="Pre-owned">Pre-owned</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="bulk-col-label">Certified</label>
                                    <select id="certifiedInput" class="bulk-select" disabled>
                                        <option value="">[Select]</option>
                                        <option value="CPO">CPO</option>
                                        <option value="VPO">VPO</option>
                                    </select>
                                </div>
                            </div>

                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Link URL</label>
                                <input type="text" id="linkUrlInput" class="bulk-input" placeholder="https://example.com">
                                <div class="hint-box">Make sure to include necessary filters, i.e. make, model, and condition.</div>
                            </div>

                            @php
                                $mediaFields = [
                                    ['id' => 'desktop_image_url', 'label' => 'Desktop Image (Carousel / List)', 'hint' => 'For pages / Content Blocks. File dimensions may vary: 1440x552 (Mitsubishi), 1110x358 (Honda / Acura).'],
                                    ['id' => 'mobile_image_url', 'label' => 'Mobile Version (Carousel / List)', 'hint' => 'Optional: if not provided, we\'ll use the desktop version.'],
                                    ['id' => 'srp_desktop_banner_url', 'label' => 'SRP: Top Banner (Desktop)', 'hint' => 'For display at the top of the SRP. If multiple promos are active, we\'ll rotate them on a timer. Recommended file dimensions: 1400x72'],
                                    ['id' => 'srp_mobile_banner_url', 'label' => 'SRP: Top Banner (Mobile)', 'hint' => 'For display at the top of the SRP. If multiple promos are active, we\'ll rotate them on a timer.'],
                                ];
                            @endphp

                            @foreach($mediaFields as $field)
                                <div class="media-row">
                                    <div class="media-header">
                                        <label class="bulk-col-label" style="margin: 0;">{{ $field['label'] }}</label>
                                        <span class="media-btn-select" data-field="{{ $field['id'] }}"><i class="bi bi-images"></i> Select Media</span>
                                    </div>
                                    <input type="text" id="{{ $field['id'] }}Input" class="bulk-input" placeholder="https://path.to.image/photo.jpg">
                                    <div class="hint-box">{{ $field['hint'] }}</div>
                                    <div class="media-preview-wrap" id="{{ $field['id'] }}PreviewWrap">
                                        <img src="" id="{{ $field['id'] }}Preview">
                                        <span class="media-preview-remove" data-field="{{ $field['id'] }}">&times;</span>
                                    </div>
                                </div>
                            @endforeach

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 40px;">
                                <div>
                                    <label class="bulk-col-label">SRP: Primary background color</label>
                                    <div class="color-picker-wrap">
                                        <div class="color-box-btn" data-field="primary_color"><div class="color-preview" id="primary_colorPreview"></div></div>
                                        <input type="text" id="primary_colorInput" class="bulk-input" placeholder="#000000">
                                        <div class="color-popup" id="primary_colorPopup">
                                            <div class="color-grid"></div>
                                            <div class="color-hex-wrap"><span>#</span><input type="text" class="color-hex-input"></div>
                                        </div>
                                    </div>
                                    <div class="hint-box">Background color (or starting color of a gradient).</div>
                                </div>
                                <div>
                                    <label class="bulk-col-label">SRP: Secondary background color</label>
                                    <div class="color-picker-wrap">
                                        <div class="color-box-btn" data-field="secondary_color"><div class="color-preview" id="secondary_colorPreview"></div></div>
                                        <input type="text" id="secondary_colorInput" class="bulk-input" placeholder="#000000">
                                        <div class="color-popup" id="secondary_colorPopup">
                                            <div class="color-grid"></div>
                                            <div class="color-hex-wrap"><span>#</span><input type="text" class="color-hex-input"></div>
                                        </div>
                                    </div>
                                    <div class="hint-box">Optional: ending color of the gradient background.</div>
                                </div>
                            </div>

                            <button class="btn-save-red" id="saveBtn"><i class="bi bi-check-lg"></i> Save</button>
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
    <div class="faq-modal" style="width: 900px; max-width: 95vw; height: 85vh;">
        <div style="padding: 20px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700;">Select Media</h3>
            <button style="background:none; border:none; font-size:24px; cursor:pointer;" id="mediaModalClose">&times;</button>
        </div>
        <div style="padding: 15px 25px; border-bottom: 1px solid #f9f9f9; display: flex; align-items: center; justify-content: space-between;">
            <div style="flex: 1; max-width: 300px;"><input type="text" id="mediaSearchInput" class="bulk-input" placeholder="Search media..."></div>
            <button class="btn-save-red" style="padding: 7px 15px; font-size: 12px;"><i class="bi bi-plus-lg"></i> Add Media</button>
        </div>
        <div class="media-grid" id="mediaGrid"></div>
        <div style="padding: 15px 25px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <div id="mediaPaginationInfo" style="font-size: 12px; color: #777;"></div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <div id="mediaPagination"></div>
                <button class="btn-save-red" id="confirmMediaBtn" style="opacity: 0.5; pointer-events: none;"><i class="bi bi-check-lg"></i> Select Media</button>
            </div>
        </div>
    </div>
</div>

<div class="confirm-modal-overlay" id="confirmModalOverlay">
    <div class="confirm-modal">
        <div class="confirm-modal-header">
            <div class="confirm-modal-title"><i class="bi bi-info-circle-fill" style="color: #f39c12; font-size: 18px;"></i> Are you sure?</div>
            <button style="background:none; border:none; font-size:24px; cursor:pointer;" id="confirmModalClose">&times;</button>
        </div>
        <div style="padding: 30px 20px; font-size: 14px; color: #444;" id="confirmModalBodyText"></div>
        <div class="confirm-modal-footer">
            <button class="btn-cancel-outline" id="confirmCancelBtn">Cancel</button>
            <button class="btn-save-red" id="confirmContinueBtn">Continue</button>
        </div>
    </div>
</div>

<div class="toaster-container" id="toasterContainer"></div>
@endsection

@push('page-scripts')
<script>
(function(){
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var ROUTES = {
        store:          '{{ route("dealer.website.promo-banners.store") }}',
        update:         '{{ route("dealer.website.promo-banners.update", ["promoBanner" => "__ID__"]) }}',
        destroy:        '{{ route("dealer.website.promo-banners.destroy", ["promoBanner" => "__ID__"]) }}',
        catStore:       '{{ route("dealer.website.promo-banners.categories.store") }}',
        catUpdate:      '{{ route("dealer.website.promo-banners.categories.update", ["promoCategory" => "__ID__"]) }}',
        catDestroy:     '{{ route("dealer.website.promo-banners.categories.destroy", ["promoCategory" => "__ID__"]) }}',
        bulkUpdate:     '{{ route("dealer.website.promo-banners.bulk-update") }}',
        mediaList:      '{{ route("dealer.website.media.list") }}',
        uploadCsv:      '{{ route("dealer.website.promo-banners.upload-csv") }}',
    };

    var banners = @json($banners);
    var categories = @json($categories);
    var editingId = null;

    function ajax(method, url, data, cb, isFile = false) {
        var xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        if(!isFile) xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) cb(null, JSON.parse(xhr.responseText || '{}'));
            else { var m = 'Error'; try { m = JSON.parse(xhr.responseText).message || m; } catch(e){} cb(m); }
        };
        xhr.onerror = function(){ cb('Network error'); };
        xhr.send(isFile ? data : (data ? JSON.stringify(data) : null));
    }

    function showToaster(message) {
        var container = document.getElementById('toasterContainer');
        var t = document.createElement('div');
        t.className = 'toaster';
        t.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span>' + message + '</span>';
        container.appendChild(t);
        setTimeout(function(){ t.classList.add('show'); }, 10);
        setTimeout(function(){ t.classList.remove('show'); setTimeout(function(){ t.remove(); }, 400); }, 3000);
    }

    // [Confirmation Logic]
    var confirmOverlay = document.getElementById('confirmModalOverlay');
    var confirmCallback = null;
    function customConfirm(text, cb) {
        document.getElementById('confirmModalBodyText').textContent = text;
        confirmCallback = cb;
        confirmOverlay.classList.add('open');
    }
    document.getElementById('confirmCancelBtn').onclick = document.getElementById('confirmModalClose').onclick = function(){ confirmOverlay.classList.remove('open'); };
    document.getElementById('confirmContinueBtn').onclick = function(){ if(confirmCallback) confirmCallback(); confirmOverlay.classList.remove('open'); };

    function getDisplayStatus(b) {
        var now = new Date(), start = b.start_date ? new Date(b.start_date) : null, end = b.end_date ? new Date(b.end_date) : null;
        if (b.status === 'Inactive') return 'Draft';
        if (start && now < start) return 'Scheduled';
        if (end && now > end) return 'Expired';
        return 'Active';
    }

    function renderTable(){
        var filter = document.getElementById('statusFilter').value;
        var tbody = document.getElementById('promoTableBody');
        var html = '';
        var filtered = banners.filter(function(b){
            var s = getDisplayStatus(b);
            if (filter === 'Active') return s === 'Active';
            if (filter === 'Expired') return s === 'Expired';
            return true;
        });

        filtered.forEach(function(b){
            var status = getDisplayStatus(b);
            html += '<tr>' +
                '<td><div class="rc-title-cell"><span class="rc-drag"><i class="bi bi-list"></i></span>' +
                '<div class="rc-content-wrap"><div class="rc-nickname">'+b.title+'</div>' +
                '<div class="rc-row-actions"><a href="#edit/'+b.id+'" class="rc-row-btn"><i class="bi bi-pencil-square"></i> Edit</a>' +
                '<button class="rc-row-btn trash-btn" onclick="window._trash('+b.id+')"><i class="bi bi-trash"></i> Trash</button></div></div></div></td>' +
                '<td><span style="color:#666; font-size:14px;">'+(b.category?b.category.name:'Uncategorized')+'</span></td>' +
                '<td><span style="color:#666; font-size:14px;">'+(b.author||'System')+'</span></td>' +
                '<td><div class="status-wrap"><span class="status-dot '+status.toLowerCase()+'"></span><span style="font-size:14px;">'+(status==='Active'?'Currently Active':status)+'</span></div></td>' +
                '</tr>';
        });
        tbody.innerHTML = html || '<tr><td colspan="4" style="text-align:center;padding:50px;color:#999;">No results found.</td></tr>';
    }

    document.getElementById('statusFilter').onchange = renderTable;

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
        var item = id ? banners.find(function(b){ return b.id===id; }) : null;
        document.getElementById('formViewTitle').textContent = item ? 'Edit Promo' : 'Add Promo';
        document.getElementById('titleInput').value = item ? item.title : '';
        document.getElementById('disclaimerInput').value = item ? (item.disclaimer||'') : '';
        
        // Reset/Set Dates
        document.getElementById('startDateInput').value = item && item.start_date ? item.start_date.substring(0,10) : '';
        document.getElementById('endDateInput').value = item && item.end_date ? item.end_date.substring(0,10) : '';
        
        document.getElementById('linkUrlInput').value = item ? (item.link_url||'') : '';
        
        var cond = document.getElementById('conditionInput');
        cond.value = item ? (item.condition||'') : '';
        cond.dispatchEvent(new Event('change'));
        if(item && item.certified) document.getElementById('certifiedInput').value = item.certified;

        ['desktop_image_url', 'mobile_image_url', 'srp_desktop_banner_url', 'srp_mobile_banner_url'].forEach(function(f){
            setMediaValue(f, item ? (item[f]||'') : '');
        });

        // Set Colors (Initial null if no item)
        setColorValue('primary_color', item ? (item.primary_color||'') : '');
        setColorValue('secondary_color', item ? (item.secondary_color||'') : '');

        var catSel = document.getElementById('categorySelect');
        catSel.innerHTML = '<option value="">Uncategorized</option>';
        categories.forEach(function(c){ catSel.innerHTML += '<option value="'+c.id+'"'+(item && item.promo_category_id == c.id ? ' selected' : '')+'>'+c.name+'</option>'; });

        document.getElementById('listView').style.display = 'none';
        document.getElementById('formView').style.display = 'block';
    }

    function setMediaValue(f, url){
        document.getElementById(f + 'Input').value = url;
        var p = document.getElementById(f + 'Preview'), w = document.getElementById(f + 'PreviewWrap');
        if(url){ p.src = url; w.style.display = 'block'; } else { w.style.display = 'none'; }
    }

    function setColorValue(f, c){
        document.getElementById(f + 'Input').value = c;
        var p = document.getElementById(f + 'Preview');
        if(c) p.style.background = c;
        else p.style.background = '#fff';
    }

    document.getElementById('conditionInput').onchange = function(){
        var cert = document.getElementById('certifiedInput');
        cert.disabled = !this.value;
        cert.innerHTML = '<option value="">[Select]</option>';
        if(this.value === 'New') cert.innerHTML += '<option value="VPO">VPO</option>';
        else if(this.value === 'Pre-owned') cert.innerHTML += '<option value="CPO">CPO</option>';
    };

    // [Media Selection]
    var currentMediaField = null, selectedMediaUrl = null;
    document.querySelectorAll('.media-btn-select').forEach(function(btn){
        btn.onclick = function(){ currentMediaField = this.dataset.field; document.getElementById('mediaModalOverlay').classList.add('open'); loadMedia(1); };
    });
    document.getElementById('mediaModalClose').onclick = function(){ document.getElementById('mediaModalOverlay').classList.remove('open'); };

    function loadMedia(page, search = ''){
        ajax('GET', ROUTES.mediaList + '?page='+page+'&search='+search+'&type=image', null, function(err, res){
            if(err) return alert(err);
            var html = '';
            res.data.forEach(function(item){ html += '<div class="media-item" data-url="'+item.url+'"><img src="'+item.url+'"></div>'; });
            document.getElementById('mediaGrid').innerHTML = html;
            document.getElementById('mediaPaginationInfo').textContent = 'Showing ' + res.from + ' to ' + res.to + ' of ' + res.total;
            
            document.querySelectorAll('.media-item').forEach(function(item){
                item.onclick = function(){
                    document.querySelectorAll('.media-item').forEach(function(i){ i.classList.remove('selected'); });
                    this.classList.add('selected');
                    selectedMediaUrl = this.dataset.url;
                    document.getElementById('confirmMediaBtn').style.opacity = '1';
                    document.getElementById('confirmMediaBtn').style.pointerEvents = 'auto';
                };
            });
        });
    }

    document.getElementById('confirmMediaBtn').onclick = function(){
        setMediaValue(currentMediaField, selectedMediaUrl);
        document.getElementById('mediaModalOverlay').classList.remove('open');
    };

    // [Save]
    document.getElementById('saveBtn').onclick = function(){
        var p = {
            title: document.getElementById('titleInput').value.trim(),
            disclaimer: document.getElementById('disclaimerInput').value.trim(),
            promo_category_id: document.getElementById('categorySelect').value || null,
            condition: document.getElementById('conditionInput').value || null,
            certified: document.getElementById('certifiedInput').value || null,
            start_date: document.getElementById('startDateInput').value || null,
            end_date: document.getElementById('endDateInput').value || null,
            link_url: document.getElementById('linkUrlInput').value.trim(),
            desktop_image_url: document.getElementById('desktop_image_urlInput').value.trim(),
            mobile_image_url: document.getElementById('mobile_image_urlInput').value.trim(),
            srp_desktop_banner_url: document.getElementById('srp_desktop_banner_urlInput').value.trim(),
            srp_mobile_banner_url: document.getElementById('srp_mobile_banner_urlInput').value.trim(),
            primary_color: document.getElementById('primary_colorInput').value.trim(),
            secondary_color: document.getElementById('secondary_colorInput').value.trim(),
            status: 'Active'
        };
        if(!p.title) return alert('Title is required');
        ajax(editingId?'PATCH':'POST', editingId?ROUTES.update.replace('__ID__', editingId):ROUTES.store, p, function(err, res){
            if(err) return alert(err);
            if(editingId){ var idx = banners.findIndex(function(b){ return b.id===editingId; }); if(idx>-1) banners[idx] = res; }
            else banners.push(res);
            window.location.hash = '#list'; renderTable(); showToaster('Promo saved.');
        });
    };

    window._trash = function(id){
        customConfirm('Are you sure you want to delete this promo?', function(){
            ajax('DELETE', ROUTES.destroy.replace('__ID__', id), null, function(err){
                if(err) return alert(err);
                banners = banners.filter(function(b){ return b.id!==id; }); renderTable(); showToaster('Promo deleted.');
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
        html += '<div style="padding:20px; text-align:center;"><button class="rc-btn-add" id="catAddBtn"><i class="bi bi-plus-lg"></i> Add Category</button></div></div>';
        body.innerHTML = html;
        catOverlay.classList.add('open');
        document.getElementById('catAddBtn').onclick = function(){ showCatEdit(null); };
    }

    window._editCat = function(id){ var c = categories.find(function(x){ return x.id == id; }); showCatEdit(c); };
    window._delCat = function(id){
        customConfirm('Delete category?', function(){
            ajax('DELETE', ROUTES.catDestroy.replace('__ID__', id), null, function(err){
                if(err) return alert(err);
                categories = categories.filter(function(x){ return x.id !== id; }); showCatList(); showToaster('Category deleted.');
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
                '<button class="btn-cancel-outline" onclick="showCatList()">Back</button>' +
            '</div></div>';
        document.getElementById('catSaveBtn').onclick = function(){
            var val = document.getElementById('catNameInput').value.trim();
            if(!val) return;
            ajax(isNew?'POST':'PATCH', isNew?ROUTES.catStore:ROUTES.catUpdate.replace('__ID__', cat.id), {name:val}, function(err, res){
                if(err) return alert(err);
                if(isNew) categories.push(res); else { var idx = categories.findIndex(function(x){ return x.id == cat.id; }); categories[idx] = res; }
                showCatList(); showToaster('Category saved.');
            });
        };
    }

    // [CSV]
    var csvBtn = document.getElementById('uploadCsvBtn'), csvInput = document.getElementById('csvFileInput');
    csvBtn.onclick = function(){ csvInput.click(); };
    csvInput.onchange = function(){
        if(!this.files.length) return;
        var fd = new FormData(); fd.append('file', this.files[0]);
        ajax('POST', ROUTES.uploadCsv, fd, function(err, res){
            if(err) return alert(err);
            if(res.length > 0) populateFormWithData(res[0]);
            showToaster('CSV data imported.');
        }, true);
    };

    function populateFormWithData(data){
        if(data.title) document.getElementById('titleInput').value = data.title;
        if(data.disclaimer) document.getElementById('disclaimerInput').value = data.disclaimer;
        if(data.condition){
            document.getElementById('conditionInput').value = data.condition;
            document.getElementById('conditionInput').dispatchEvent(new Event('change'));
            if(data.certified) document.getElementById('certifiedInput').value = data.certified;
        }
        if(data.category_name){
            var c = categories.find(function(x){ return x.name.toLowerCase() === data.category_name.toLowerCase(); });
            if(c) document.getElementById('categorySelect').value = c.id;
        }
        ['start_date', 'end_date'].forEach(function(f){ if(data[f]) document.getElementById(f+'Input').value = data[f].substring(0,10); });
        if(data.link_url) document.getElementById('linkUrlInput').value = data.link_url;
        ['desktop_image_url', 'mobile_image_url', 'srp_desktop_banner_url', 'srp_mobile_banner_url'].forEach(function(f){ if(data[f]) setMediaValue(f, data[f]); });
        if(data.primary_color) setColorValue('primary_color', data.primary_color);
        if(data.secondary_color) setColorValue('secondary_color', data.secondary_color);
    }

    // [Color Grid Population]
    var swatchColors = ['#000000', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#00FFFF', '#FF00FF', '#C0C0C0', '#808080', '#800000', '#808000', '#008000', '#800080', '#008080', '#000080', '#FFA500', '#A52A2A'];
    document.querySelectorAll('.color-picker-wrap').forEach(function(wrap){
        var btn = wrap.querySelector('.color-box-btn'), popup = wrap.querySelector('.color-popup'), grid = wrap.querySelector('.color-grid');
        var input = wrap.querySelector('.bulk-input'), preview = wrap.querySelector('.color-preview'), hex = wrap.querySelector('.color-hex-input');
        
        swatchColors.forEach(function(c){
            var s = document.createElement('div'); s.className = 'color-swatch'; s.style.background = c;
            s.onclick = function(){ input.value = c; preview.style.background = c; hex.value = c.replace('#',''); popup.classList.remove('open'); };
            grid.appendChild(s);
        });

        btn.onclick = function(e){ e.stopPropagation(); popup.classList.toggle('open'); };
        input.oninput = function(){ var c = this.value; if(/^#[0-9A-F]{6}$/i.test(c)){ preview.style.background = c; hex.value = c.replace('#',''); } };
        hex.oninput = function(){ var c = '#'+this.value; if(/^#[0-9A-F]{6}$/i.test(c)){ input.value = c; preview.style.background = c; } };
    });
    document.onclick = function(){ document.querySelectorAll('.color-popup').forEach(function(p){ p.classList.remove('open'); }); };

    renderTable();
    navigate();
})();
</script>
@endpush
