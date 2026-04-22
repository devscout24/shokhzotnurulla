@extends('layouts.dealer.app')
@section('title', __('Reusable Content: SRP Content') . ' | ' . __(config('app.name')))

@push('page-styles')
<style>
/* Reset some potential conflicts from global CSS */
.main-content { padding: 30px 45px !important; background: #fafbfc; min-height: calc(100vh - 60px); }

/* Main Layout */
.rc-wrapper { display: flex; gap: 40px; align-items: flex-start; }
.rc-sidebar { width: 260px; min-width: 260px; background: transparent; }
.rc-sidebar-item { display: flex; align-items: center; gap: 12px; padding: 15px; font-size: 14px; color: #666; cursor: pointer; transition: all .2s; text-decoration: none; border-bottom: 1px solid #f0f0f0; }
.rc-sidebar-item:last-child { border-bottom: none; }
.rc-sidebar-item:hover { background: #f8f8f8; color: #333; }
.rc-sidebar-item.active { color: #000; font-weight: 650; background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.rc-sidebar-item i { font-size: 18px !important; width: 24px; text-align: center; color: #999; }
.rc-sidebar-item.active i { color: #c0392b !important; }

.rc-main-container { flex: 1; background: #fff; border: 1px solid #eef0f2; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); display: flex; flex-direction: column; overflow: hidden; height: fit-content; }

/* Header Actions */
.rc-header-actions { display: flex; align-items: center; gap: 12px; }
.rc-btn-outline { display: inline-flex; align-items: center; gap: 8px; border: 1px solid #e0e0e0; background: #fff; font-size: 12px; color: #666 !important; cursor: pointer; padding: 7px 15px; border-radius: 6px; white-space: nowrap; transition: all .15s; font-weight: 600; text-decoration: none; }
.rc-btn-outline:hover { background: #f8f9fa; border-color: #d0d0d0; color: #333 !important; }
.rc-btn-add { display: inline-flex; align-items: center; gap: 8px; background: #c0392b; color: #fff !important; border: none; padding: 9px 20px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; }
.btn-save-red { 
    padding: 10px 30px !important; 
    background: #c0392b !important; 
    color: #fff !important; 
    border: none !important; 
    border-radius: 4px !important; 
    cursor: pointer !important; 
    font-size: 13px !important; 
    font-weight: 700 !important; 
    display: inline-flex !important; 
    align-items: center !important; 
    gap: 8px !important; 
    transition: all .2s !important;
    text-decoration: none !important;
}
.btn-save-red:hover { background: #a93226 !important; box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important; }
.btn-save-red i { font-size: 18px !important; font-weight: 800 !important; }
.rc-btn-add:hover { background: #a93226; color: #fff !important; }

/* Table Styling */
.faq-table { width: 100%; border-collapse: collapse; }
.faq-table th { padding: 18px 20px; font-weight: 700; color: #333; text-align: left; font-size: 12px; border-bottom: 1px solid #f0f0f0; background: #fff; }
.faq-table td { padding: 25px 20px; vertical-align: middle; border-bottom: 1px solid #f8f9fa; background: #fff; }

.faq-title-cell { display: flex; align-items: flex-start; gap: 15px; }
.faq-drag { color: #ddd; cursor: grab; font-size: 16px; padding-top: 2px; }
.faq-content-wrap { display: flex; flex-direction: column; gap: 12px; }
.faq-question { font-size: 15px; font-weight: 500; color: #333; line-height: 1.4; }
.faq-row-actions { display: flex; align-items: center; gap: 10px; }
.faq-row-btn { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 12px; color: #666 !important; background: #fff; cursor: pointer; text-decoration: none; transition: all .2s; }
.faq-row-btn:hover { background: #f8f8f8; color: #333 !important; border-color: #ccc; }
.faq-row-btn.trash-btn { color: #c0392b !important; }
.faq-row-btn.trash-btn:hover { background: #fff0f0; border-color: #f5c6cb; }

.faq-status-wrap { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #444; }
.faq-status-dot { width: 8px; height: 8px; border-radius: 50%; background: #28a745; }

/* Pagination & Summary */
.faq-pagination-container { padding: 40px 0; display: flex; flex-direction: column; align-items: center; gap: 15px; }
.faq-pagination { display: flex; align-items: center; gap: 8px; }
.faq-page-btn { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #eee; background: #fff; color: #666; border-radius: 8px; cursor: pointer; transition: all .2s; font-size: 14px; }
.faq-page-btn.active { background: #c0392b; color: #fff; border-color: #c0392b; }
.faq-page-btn:hover:not(.active) { background: #f8f9fa; border-color: #ddd; }
.faq-summary { font-size: 13px; color: #999; }

/* Form Field Styles */
.field-group { margin-bottom: 25px; }
.field-label { font-size: 11px; font-weight: 800; color: #333; margin-bottom: 6px; display: block; text-transform: none; }
.field-label span { color: #c0392b; margin-left: 2px; }
.field-hint { font-size: 11px; color: #555; margin-top: 8px; background: #f8f9fa; padding: 12px 15px; border-radius: 4px; border: 1px solid #eee; }
.field-input { width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 13px; color: #444; background: #fff; transition: all .2s; }
.field-input:focus { border-color: #c0392b; box-shadow: 0 0 0 2px rgba(192, 57, 43, 0.05); outline: none; }
.field-textarea { min-height: 80px; resize: vertical; }
.field-select { width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 13px; color: #444; background: #fff; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; }

/* Available Variables Styling */
.variables-wrap { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
.var-label { font-size: 11px; color: #777; display: flex; align-items: center; gap: 4px; font-weight: 500; }
.var-btn { padding: 4px 12px; background: #eff2f5; border: 1px solid #e0e6ed; border-radius: 4px; font-size: 11px; font-weight: 600; color: #444; cursor: pointer; }
.var-btn:hover { background: #e6eaf0; }

/* Modals */
.faq-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 2000; display: none; backdrop-filter: blur(2px); }
.faq-modal-overlay.open { display: flex !important; align-items: center; justify-content: center; }
.faq-modal { background: #fff; border-radius: 14px; width: 550px; max-width: 95vw; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }

/* Bulk Edit Modal Redesign */
#bulkModalOverlay .faq-modal { width: 95vw; max-width: 1600px; height: 90vh; border-radius: 8px; }
.bulk-modal-content { display: flex; flex-direction: column; height: 100%; background: #fdfdfd; }
.bulk-header-top { display: flex; justify-content: space-between; align-items: center; padding: 12px 25px; border-bottom: 1px solid #eee; background: #fff; }
.bulk-header-actions { padding: 15px 25px; display: flex; justify-content: flex-end; background: #fff; }
.bulk-body { flex: 1; overflow: auto; padding: 0 25px 25px 25px; }
.bulk-table-header { display: grid; grid-template-columns: 160px 150px 180px 180px 200px 140px 300px 110px 40px; gap: 12px; padding: 12px 15px; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 10; margin-bottom: 10px; width: max-content; min-width: 100%; }
.bulk-header-label { font-size: 11px; font-weight: 700; color: #444; }
.bulk-header-label span { color: #c0392b; margin-left: 2px; }
.bulk-row { display: grid; grid-template-columns: 160px 150px 180px 180px 200px 140px 300px 110px 40px; gap: 12px; padding: 15px; border: 1px solid #eef0f2; border-radius: 6px; margin-bottom: 10px; background: #fff; align-items: flex-start; transition: all .2s; width: max-content; min-width: 100%; }
.bulk-row:hover { border-color: #d1d5db; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
.bulk-input { width: 100%; padding: 8px 12px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 13px; color: #333; background: #fff; }
.bulk-textarea { width: 100%; padding: 8px 12px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 13px; color: #333; background: #fff; min-height: 38px; height: 38px; resize: vertical; }
.bulk-select { width: 100%; padding: 8px 12px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 13px; color: #333; background: #fff; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; }
.bulk-action-del { color: #9ca3af; cursor: pointer; font-size: 18px; padding-top: 8px; transition: color .2s; }
.bulk-action-del:hover { color: #c0392b; }
.bulk-footer { padding: 15px 25px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 12px; background: #fff; }
.btn-cancel-outline { padding: 8px 24px; border: 1px solid #e5e7eb; background: #fff; color: #4b5563; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; }
.btn-cancel-outline:hover { background: #f9fafb; border-color: #d1d5db; }

/* Status Dot Fix */
.faq-status-dot.published { background: #28a745 !important; }

/* Field Improvements */
.field-input { border: 1px solid #e0e0e0 !important; padding: 8px 12px !important; font-size: 13px !important; }
.field-input::placeholder { color: #ccc !important; }
.field-select { border: 1px solid #e0e0e0 !important; padding: 8px 12px !important; font-size: 13px !important; color: #444 !important; }
.field-select { border: 1px solid #e0e0e0 !important; padding: 8px 12px !important; font-size: 13px !important; color: #444 !important; }

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

/* RTE Styling */
.bulk-rte { border: 1px solid #e0e0e0; border-radius: 4px; background: #fff; overflow: hidden; margin-top: 10px; }
.rte-toolbar { background: #fff; border-bottom: 1px solid #eee; padding: 8px 15px; display: flex; flex-direction: row; align-items: center; gap: 15px; flex-wrap: nowrap; }
.rte-tool-group { display: flex; flex-direction: row; align-items: center; gap: 12px; border-right: 1px solid #eee; padding-right: 15px; flex-wrap: nowrap; }
.rte-tool-group:last-child { border-right: none; padding-right: 0; }
.rte-btn { background: none; border: none; padding: 5px; cursor: pointer; color: #777; display: flex; align-items: center; justify-content: center; transition: all .2s; outline: none; }
.rte-btn:hover { color: #000; background: #f5f5f5; border-radius: 3px; }
.rte-btn i { font-size: 15px; line-height: 1; }
.rte-select-wrap { display: flex; align-items: center; gap: 6px; cursor: pointer; }
.rte-select { border: none; background: transparent; font-size: 13px; font-weight: 500; color: #444; outline: none; cursor: pointer; appearance: none; padding: 0; margin: 0; }
.rte-textarea { width: 100% !important; border: none !important; outline: none !important; font-size: 15px !important; color: #333 !important; min-height: 250px !important; padding: 30px 40px !important; line-height: 1.7 !important; background: #fff !important; resize: none !important; }
</style>
@endpush

@section('page-content')
<div class="main-content">
    <div class="page-header" style="margin-bottom: 30px; border: none; justify-content: space-between;">
        <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222; margin: 0;">{{ __('Reusable Content: SRP Content') }}</h2>
        <div class="rc-header-actions">
            <button class="rc-btn-outline" type="button" id="bulkEditBtn"><i class="bi bi-pencil-square"></i> {{ __('Bulk Edit') }}</button>
            <button class="rc-btn-add" type="button" id="addSrpBtn"><i class="bi bi-plus-lg"></i> {{ __('Add New SRP Content') }}</button>
        </div>
    </div>

    <div class="rc-wrapper">
        <div class="rc-sidebar">
            <a href="{{ route('dealer.website.faqs.index') }}" class="rc-sidebar-item"><i class="bi bi-question-circle"></i> FAQs</a>
            <a href="#" class="rc-sidebar-item"><i class="bi bi-megaphone"></i> OEM Promo Banners</a>
            <a href="{{ route('dealer.website.srp-content.index') }}" class="rc-sidebar-item active"><i class="bi bi-file-earmark-text"></i> Content: Search Results (SRP)</a>
            <a href="{{ route('dealer.website.static-page-content.index') }}" class="rc-sidebar-item"><i class="bi bi-file-text"></i> Static Page Content</a>
            <a href="#" class="rc-sidebar-item"><i class="bi bi-star"></i> Customer Reviews</a>
            <a href="#" class="rc-sidebar-item"><i class="bi bi-person"></i> Staff Members</a>
            <a href="#" class="rc-sidebar-item"><i class="bi bi-briefcase"></i> Job Posts</a>
            <a href="#" class="rc-sidebar-item"><i class="bi bi-tags"></i> Service Offers</a>
            <a href="#" class="rc-sidebar-item"><i class="bi bi-calendar3"></i> Events</a>
        </div>
        
        <div style="flex: 1; display: flex; flex-direction: column;">
            {{-- List View --}}
            <div id="srpListView">
                <div class="rc-main-container">
                    <table class="faq-table" id="srpTable">
                        <thead><tr>
                            <th style="width: 45%;">Title</th>
                            <th style="width: 25%;">Slug</th>
                            <th style="width: 15%;">Author</th>
                            <th style="width: 15%;">Status</th>
                        </tr></thead>
                        <tbody id="srpTableBody"></tbody>
                    </table>
                </div>
                <div class="faq-pagination-container">
                    <div class="faq-pagination" id="srpPagination">
                        <button class="faq-page-btn"><i class="bi bi-chevron-left"></i></button>
                        <button class="faq-page-btn active">1</button>
                        <button class="faq-page-btn"><i class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="faq-summary" id="srpSummary">Showing 0 of 0 pages</div>
                </div>
            </div>

            {{-- Add/Edit Form View --}}
            <div id="srpFormView" style="display: none;">
                <div style="margin-bottom: 20px;">
                    <button class="rc-btn-outline" id="srpBackBtn" style="padding: 6px 14px; font-size: 11px;"><i class="bi bi-chevron-left" style="font-size: 10px;"></i> Go Back</button>
                </div>
                <div class="rc-main-container" style="padding: 0;">
                    <div style="padding: 15px 25px; border-bottom: 1px solid #f0f0f0;">
                        <h3 style="font-size: 14px; font-weight: 700; color: #333; margin: 0;" id="formViewTitle">Add SRP Content</h3>
                    </div>
                    <div style="padding: 25px 30px;">
                        <div class="field-group">
                            <label class="field-label">Content nickname (ex: Honda content for /honda) <span>*</span></label>
                            <input type="text" id="srpNicknameInput" class="field-input" placeholder="Used Cars, Trucks, and SUVs in Atlanta, GA">
                        </div>
                        
                        <div class="field-group">
                            <label class="field-label">SRP Slug to map content to <span>*</span></label>
                            <input type="text" id="srpSlugInput" class="field-input" placeholder="/trucks">
                            <div class="field-hint">Must match SRP URL slug exactly, i.e. /cars, /trucks, /suvs</div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Main header of the page - H1 (optional override)</label>
                            <input type="text" id="srpH1Input" class="field-input" placeholder="Used Cars, Trucks, and SUVs in Atlanta, GA">
                            <div class="variables-wrap">
                                <span class="var-label"><i class="bi bi-braces"></i> Available Variables:</span>
                                <button class="var-btn" type="button">Vehicles Count</button>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Meta Title (optional override)</label>
                            <input type="text" id="srpMetaTitleInput" class="field-input" placeholder="Used Cars, Trucks, and SUVs in Atlanta, GA">
                        </div>

                        <div class="field-group">
                            <label class="field-label">Meta Description (optional override - ideally 160 characters or less)</label>
                            <textarea id="srpMetaDescInput" class="field-input field-textarea" placeholder="Shop used cars, trucks, and SUVs in Atlanta, GA. Find the best deals on used vehicles at our dealership."></textarea>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Content Placement (defaults to bottom of page)</label>
                            <select id="srpPlacementInput" class="field-select">
                                <option value="bottom">Bottom of Page</option>
                                <option value="top">Top of Page</option>
                            </select>
                        </div>

                        <div class="field-group" style="margin-bottom: 35px;">
                            <label class="field-label">Page Content</label>
                            <div class="bulk-rte">
                                <div class="rte-toolbar">
                                    <div class="rte-tool-group">
                                        <div class="rte-select-wrap"><select class="rte-select"><option>Normal</option></select><i class="bi bi-chevron-expand" style="font-size:10px;color:#999;"></i></div>
                                    </div>
                                    <div class="rte-tool-group">
                                        <button class="rte-btn" type="button"><i class="bi bi-type-bold"></i></button>
                                        <button class="rte-btn" type="button"><i class="bi bi-type-italic"></i></button>
                                        <button class="rte-btn" type="button"><i class="bi bi-type-underline"></i></button>
                                    </div>
                                    <div class="rte-tool-group">
                                        <button class="rte-btn" type="button"><i class="bi bi-text-left"></i></button>
                                    </div>
                                    <div class="rte-tool-group">
                                        <button class="rte-btn" type="button"><i class="bi bi-list-task"></i></button>
                                        <button class="rte-btn" type="button"><i class="bi bi-list-ol"></i></button>
                                        <button class="rte-btn" type="button"><i class="bi bi-text-indent-left"></i></button>
                                        <button class="rte-btn" type="button"><i class="bi bi-text-indent-right"></i></button>
                                    </div>
                                    <div class="rte-tool-group">
                                        <button class="rte-btn" type="button"><i class="bi bi-link-45deg"></i></button>
                                    </div>
                                </div>
                                <textarea id="srpContentInput" class="rte-textarea" placeholder="Used cars for sale in Smyrna, TN..."></textarea>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-start;">
                            <button class="btn-save-red" id="srpSaveBtn"><i class="bi bi-check-lg"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Edit Modal --}}
<div class="faq-modal-overlay" id="bulkModalOverlay">
    <div class="faq-modal">
        <div class="bulk-modal-content">
            <div class="bulk-header-top">
                <h3 class="faq-modal-title" style="font-size: 14px; font-weight: 600;">Bulk Edit Content: Search Results (SRP)</h3>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <button class="rc-btn-outline" id="bulkAddRowBtn" style="padding: 7px 15px; background: #fff; height: 34px;"><i class="bi bi-plus-lg"></i> Add Row</button>
                    <button class="faq-modal-close" id="bulkModalClose" style="font-size: 20px;">&times;</button>
                </div>
            </div>
            <div class="bulk-body">
                <div class="bulk-table-header">
                    <div class="bulk-header-label">Nickname <span>*</span></div>
                    <div class="bulk-header-label">SRP Slug <span>*</span></div>
                    <div class="bulk-header-label">H1 Override</div>
                    <div class="bulk-header-label">Meta Title</div>
                    <div class="bulk-header-label">Meta Description</div>
                    <div class="bulk-header-label">Placement</div>
                    <div class="bulk-header-label">Page Content</div>
                    <div class="bulk-header-label">Status</div>
                    <div></div>
                </div>
                <div id="bulkRowsContainer"></div>
            </div>
            <div class="bulk-footer">
                <button class="btn-cancel-outline" id="bulkCancelBtn">Cancel</button>
                <button class="btn-save-red" id="bulkSaveBtn"><i class="bi bi-check-lg"></i> Save</button>
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
(function(){
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var ROUTES = {
        store:   '{{ route("dealer.website.srp-content.store") }}',
        update:  '{{ route("dealer.website.srp-content.update", ["srpContent" => "__ID__"]) }}',
        destroy: '{{ route("dealer.website.srp-content.destroy", ["srpContent" => "__ID__"]) }}',
        bulk:    '{{ route("dealer.website.srp-content.bulk-update") }}',
    };

    var contents = @json($contents);
    var editingId = null;

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
                try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e){}
                cb(msg);
            }
        };
        xhr.onerror = function(){ cb('Network error'); };
        xhr.send(data ? JSON.stringify(data) : null);
    }

    function showToaster(message) {
        var container = document.getElementById('toasterContainer');
        var t = document.createElement('div');
        t.className = 'toaster';
        t.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span>' + message + '</span><button class="toaster-close">&times;</button>';
        container.appendChild(t);
        setTimeout(function(){ t.classList.add('show'); }, 10);
        var remove = function(){
            t.classList.remove('show');
            setTimeout(function(){ t.remove(); }, 400);
        };
        var timer = setTimeout(remove, 3000);
        t.querySelector('.toaster-close').onclick = function(){ clearTimeout(timer); remove(); };
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
    confirmCancel.onclick = confirmClose.onclick = function(){ confirmOverlay.classList.remove('open'); confirmCallback = null; };
    confirmBtn.onclick = function(){
        if(confirmCallback) confirmCallback();
        confirmOverlay.classList.remove('open');
        confirmCallback = null;
    };

    function renderTable(){
        var tbody = document.getElementById('srpTableBody');
        var html = '';
        contents.forEach(function(c){
            html += '<tr>' +
                '<td>' +
                    '<div class="faq-title-cell">' +
                        '<span class="faq-drag"><i class="bi bi-list"></i></span>' +
                        '<div class="faq-content-wrap">' +
                            '<div class="faq-question">'+c.nickname+'</div>' +
                            '<div class="faq-row-actions">' +
                                '<a href="#" class="faq-row-btn" onclick="window._srpEdit('+c.id+');return false;"><i class="bi bi-pencil-square"></i> Edit</a>' +
                                '<button class="faq-row-btn trash-btn" onclick="window._srpTrash('+c.id+')"><i class="bi bi-trash"></i> Trash</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</td>' +
                '<td><span style="color:#666; font-size:14px;">'+c.slug+'</span></td>' +
                '<td><span style="color:#666; font-size:14px;">'+(c.author||'Austin Scholl')+'</span></td>' +
                '<td><div class="faq-status-wrap"><span class="faq-status-dot published"></span><span>'+c.status+'</span></div></td>' +
                '</tr>';
        });
        tbody.innerHTML = html || '<tr><td colspan="4" style="text-align:center;padding:50px;">No content found.</td></tr>';
        document.getElementById('srpSummary').textContent = 'Showing ' + contents.length + ' of ' + contents.length + ' pages';
    }

    var listView = document.getElementById('srpListView');
    var formView = document.getElementById('srpFormView');

    document.getElementById('addSrpBtn').addEventListener('click', function(){ openForm(null); });
    document.getElementById('srpBackBtn').addEventListener('click', function(){
        formView.style.display = 'none';
        listView.style.display = 'block';
        editingId = null;
    });

    function openForm(id){
        editingId = id;
        var item = id ? contents.find(function(c){ return c.id===id; }) : null;
        document.getElementById('formViewTitle').textContent = item ? 'Edit SRP Content' : 'Add SRP Content';
        document.getElementById('srpNicknameInput').value = item ? item.nickname : '';
        document.getElementById('srpSlugInput').value = item ? item.slug : '';
        document.getElementById('srpH1Input').value = item ? (item.h1_override||'') : '';
        document.getElementById('srpMetaTitleInput').value = item ? (item.meta_title||'') : '';
        document.getElementById('srpMetaDescInput').value = item ? (item.meta_description||'') : '';
        document.getElementById('srpPlacementInput').value = item ? item.placement : 'bottom';
        
        var contentInput = document.getElementById('srpContentInput');
        contentInput.value = item ? (item.content||'') : '';
        
        listView.style.display = 'none';
        formView.style.display = 'block';

        // Auto-resize textarea
        contentInput.style.height = 'auto';
        contentInput.style.height = (contentInput.scrollHeight > 250 ? contentInput.scrollHeight : 250) + 'px';
        contentInput.addEventListener('input', function(){
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight > 250 ? this.scrollHeight : 250) + 'px';
        });
    }

    document.getElementById('srpSaveBtn').addEventListener('click', function(){
        var payload = {
            nickname: document.getElementById('srpNicknameInput').value.trim(),
            slug: document.getElementById('srpSlugInput').value.trim(),
            h1_override: document.getElementById('srpH1Input').value.trim(),
            meta_title: document.getElementById('srpMetaTitleInput').value.trim(),
            meta_description: document.getElementById('srpMetaDescInput').value.trim(),
            placement: document.getElementById('srpPlacementInput').value,
            content: document.getElementById('srpContentInput').value.trim(),
            status: 'Published'
        };
        if(!payload.nickname || !payload.slug) return alert('Nickname and Slug are required');

        if(editingId){
            ajax('PATCH', ROUTES.update.replace('__ID__', editingId), payload, function(err, res){
                if(err) return alert(err);
                var idx = contents.findIndex(function(c){ return c.id===editingId; });
                contents[idx] = res;
                document.getElementById('srpBackBtn').click(); renderTable();
                showToaster('Post saved.');
            });
        } else {
            ajax('POST', ROUTES.store, payload, function(err, res){
                if(err) return alert(err);
                contents.push(res);
                document.getElementById('srpBackBtn').click(); renderTable();
                showToaster('Post saved.');
            });
        }
    });

    window._srpEdit = function(id){ openForm(id); };
    window._srpTrash = function(id){
        customConfirm('Are you sure you want to delete this post?', function(){
            ajax('DELETE', ROUTES.destroy.replace('__ID__', id), null, function(err){
                if(err) return alert(err);
                contents = contents.filter(function(c){ return c.id!==id; });
                renderTable();
                showToaster('Post deleted.');
            });
        });
    };

    var bulkOverlay = document.getElementById('bulkModalOverlay');
    var bulkRowsContainer = document.getElementById('bulkRowsContainer');
    document.getElementById('bulkEditBtn').addEventListener('click', function(){
        bulkRowsContainer.innerHTML = '';
        contents.forEach(function(c){ addBulkRow(c); });
        bulkOverlay.classList.add('open');
    });
    document.getElementById('bulkModalClose').addEventListener('click', function(){ bulkOverlay.classList.remove('open'); });
    document.getElementById('bulkCancelBtn').addEventListener('click', function(){ bulkOverlay.classList.remove('open'); });
    document.getElementById('bulkAddRowBtn').addEventListener('click', function(){ addBulkRow(null); });

    function addBulkRow(item){
        var div = document.createElement('div');
        div.className = 'bulk-row';
        div.dataset.id = item ? item.id : '';
        div.innerHTML = 
            '<input type="text" class="bulk-input bulk-n" value="'+(item ? item.nickname : '')+'" placeholder="Nickname">' +
            '<input type="text" class="bulk-input bulk-s" value="'+(item ? item.slug : '')+'" placeholder="/trucks">' +
            '<input type="text" class="bulk-input bulk-h1" value="'+(item ? (item.h1_override||'') : '')+'" placeholder="H1 Override">' +
            '<input type="text" class="bulk-input bulk-mt" value="'+(item ? (item.meta_title||'') : '')+'" placeholder="Meta Title">' +
            '<textarea class="bulk-textarea bulk-md" placeholder="Meta Description">'+(item ? (item.meta_description||'') : '')+'</textarea>' +
            '<select class="bulk-select bulk-p">' +
                '<option value="bottom" '+(item && item.placement==='bottom'?'selected':'')+'>Bottom</option>' +
                '<option value="top" '+(item && item.placement==='top'?'selected':'')+'>Top</option>' +
            '</select>' +
            '<textarea class="bulk-textarea bulk-c" style="height: 100px; min-height: 100px;" placeholder="Page Content">'+(item ? (item.content||'') : '')+'</textarea>' +
            '<select class="bulk-select bulk-status">' +
                '<option value="Published" '+(item && item.status==='Published'?'selected':'')+'>Published</option>' +
                '<option value="Draft" '+(item && item.status==='Draft'?'selected':'')+'>Draft</option>' +
            '</select>' +
            '<div class="bulk-action-del"><i class="bi bi-trash"></i></div>';
        
        div.querySelector('.bulk-action-del').addEventListener('click', function(){
            customConfirm('Remove row?', function(){
                if(div.dataset.id) div.dataset.deleted = 'true';
                div.style.display = 'none';
            });
        });
        bulkRowsContainer.appendChild(div);
    }

    document.getElementById('bulkSaveBtn').addEventListener('click', function(){
        var data = [];
        bulkRowsContainer.querySelectorAll('.bulk-row').forEach(function(row){
            data.push({
                id: row.dataset.id ? parseInt(row.dataset.id) : null,
                nickname: row.querySelector('.bulk-n').value.trim(),
                slug: row.querySelector('.bulk-s').value.trim(),
                h1_override: row.querySelector('.bulk-h1').value.trim(),
                meta_title: row.querySelector('.bulk-mt').value.trim(),
                meta_description: row.querySelector('.bulk-md').value.trim(),
                placement: row.querySelector('.bulk-p').value,
                content: row.querySelector('.bulk-c').value.trim(),
                status: row.querySelector('.bulk-status').value,
                is_deleted: row.dataset.deleted === 'true'
            });
        });
        ajax('POST', ROUTES.bulk, {contents: data}, function(err, res){
            if(err) return alert(err);
            contents = res;
            bulkOverlay.classList.remove('open');
            renderTable();
            showToaster('Bulk update successful.');
        });
    });

    renderTable();
})();
</script>
@endpush
