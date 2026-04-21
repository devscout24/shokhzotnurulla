@extends('layouts.dealer.app')
@section('title', __('Reusable Content: Static Page Content') . ' | ' . __(config('app.name')))

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

        .rc-main-container { flex: 1; background: #fff; border: 1px solid #eef0f2; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); display: flex; flex-direction: column; overflow: hidden; height: fit-content; }

        /* Header Actions */
        .rc-header-actions { display: flex; align-items: center; gap: 12px; }
        .rc-btn-outline { display: inline-flex; align-items: center; gap: 8px; border: 1px solid #e0e0e0; background: #fff; font-size: 13px; color: #555; cursor: pointer; padding: 8px 18px; border-radius: 8px; white-space: nowrap; transition: all .15s; font-weight: 500; }
        .rc-btn-outline:hover { background: #f8f9fa; border-color: #d0d0d0; }
        .rc-btn-add { display: inline-flex; align-items: center; gap: 8px; background: #c0392b; color: #fff; border: none; padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; }
        .rc-btn-add:hover { background: #a93226; }

        /* Table Styling */
        .rc-table { width: 100%; border-collapse: collapse; }
        .rc-table th { padding: 18px 20px; font-weight: 700; color: #333; text-align: left; font-size: 12px; border-bottom: 1px solid #f0f0f0; background: #fff; }
        .rc-table td { padding: 25px 20px; vertical-align: middle; border-bottom: 1px solid #f8f9fa; background: #fff; }
        .rc-table tr:last-child td { border-bottom: none; }

        .rc-title-cell { display: flex; align-items: flex-start; gap: 15px; }
        .rc-drag { color: #ddd; cursor: grab; font-size: 16px; padding-top: 2px; }
        .rc-content-wrap { display: flex; flex-direction: column; gap: 12px; }
        .rc-nickname { font-size: 15px; font-weight: 500; color: #333; line-height: 1.4; }
        .rc-row-actions { display: flex; align-items: center; gap: 10px; }
        .rc-row-btn { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 12px; color: #666; background: #fff; cursor: pointer; text-decoration: none; transition: all .2s; }
        .rc-row-btn:hover { background: #f8f8f8; color: #333; border-color: #ccc; }
        .rc-row-btn.trash-btn { color: #c0392b; }
        .rc-row-btn.trash-btn:hover { background: #fff0f0; border-color: #f5c6cb; }

        /* Status Dot */
        .faq-status-wrap { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #444; }
        .faq-status-dot { width: 8px; height: 8px; border-radius: 50%; }
        .faq-status-dot.published { background: #28a745 !important; }
        .faq-status-dot.draft { background: #ffc107 !important; }

        /* Form Controls */
        .bulk-col-label { font-size: 12px; font-weight: 700; color: #333; margin-bottom: 8px; display: block; }
        .bulk-col-label span { color: #c0392b; margin-left: 2px; }
        .bulk-input { width: 100%; padding: 8px 12px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 13px; color: #333; background: #fff; }
        .bulk-input:focus { border-color: #c0392b; outline: none; }
        .bulk-select { width: 100%; padding: 8px 12px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 13px; color: #333; background: #fff; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; }
        .bulk-textarea { width: 100%; padding: 12px; border: 1px solid #dcdcdc; border-radius: 4px; font-size: 13px; color: #333; background: #fff; min-height: 38px; resize: vertical; }

        /* RTE Precision Pass */
        .bulk-rte { border: 1px solid #e0e0e0 !important; border-radius: 4px !important; background: #fff !important; overflow: hidden !important; margin-top: 10px !important; }
        .rte-toolbar { background: #fff !important; border-bottom: 1px solid #eee !important; padding: 8px 15px !important; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 15px !important; flex-wrap: nowrap !important; }
        .rte-tool-group { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 12px !important; border-right: 1px solid #eee !important; padding-right: 15px !important; flex-wrap: nowrap !important; }
        .rte-tool-group:last-child { border-right: none !important; padding-right: 0 !important; }
        .rte-btn { background: none !important; border: none !important; padding: 5px !important; cursor: pointer !important; color: #777 !important; display: flex !important; align-items: center !important; justify-content: center !important; transition: all .2s !important; box-shadow: none !important; outline: none !important; }
        .rte-btn:hover { color: #000 !important; background: #f5f5f5 !important; border-radius: 3px !important; }
        .rte-btn i { font-size: 15px !important; line-height: 1 !important; }
        .rte-select-wrap { display: flex !important; align-items: center !important; gap: 6px !important; cursor: pointer !important; }
        .rte-select { border: none !important; background: transparent !important; font-size: 13px !important; font-weight: 500 !important; color: #444 !important; outline: none !important; cursor: pointer !important; appearance: none !important; padding: 0 !important; margin: 0 !important; }
        .rte-textarea { width: 100% !important; border: none !important; outline: none !important; font-size: 15px !important; color: #333 !important; min-height: 250px !important; padding: 30px 40px !important; line-height: 1.7 !important; background: #fff !important; resize: none !important; box-shadow: none !important; }

        /* Field Hint */
        .field-hint { padding: 12px 15px; background: #f9f9f9; border: 1px solid #eee; border-top: none; font-size: 12px; color: #666; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; line-height: 1.5; }
        .slug-input-wrap .bulk-input { border-bottom-left-radius: 0; border-bottom-right-radius: 0; }

        /* Modals */
        .faq-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 2000; display: none; backdrop-filter: blur(2px); }
        .faq-modal-overlay.open { display: flex !important; align-items: center; justify-content: center; }
        .faq-modal { background: #fff; border-radius: 14px; width: 550px; max-width: 95vw; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }

        /* Bulk Edit Modal Redesign */
        #bulkModalOverlay .faq-modal { width: 95vw; max-width: 1600px; height: 90vh; border-radius: 8px; }
        .bulk-modal-content { display: flex; flex-direction: column; height: 100%; background: #fdfdfd; }
        .bulk-header-top { display: flex; justify-content: space-between; align-items: center; padding: 12px 25px; border-bottom: 1px solid #eee; background: #fff; }
        .bulk-body { flex: 1; overflow: auto; padding: 0 25px 25px 25px; }
        .bulk-table-header { display: grid; grid-template-columns: 160px 150px 180px 180px 200px 140px 180px 300px 110px 40px; gap: 12px; padding: 12px 15px; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 10; margin-bottom: 10px; width: max-content; min-width: 100%; }
        .bulk-header-label { font-size: 11px; font-weight: 700; color: #444; }
        .bulk-header-label span { color: #c0392b; margin-left: 2px; }
        .bulk-row { display: grid; grid-template-columns: 160px 150px 180px 180px 200px 140px 180px 300px 110px 40px; gap: 12px; padding: 15px; border: 1px solid #eef0f2; border-radius: 6px; margin-bottom: 10px; background: #fff; align-items: flex-start; transition: all .2s; width: max-content; min-width: 100%; }
        .bulk-row:hover { border-color: #d1d5db; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .bulk-footer { padding: 15px 25px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 12px; background: #fff; }

        .btn-save-red { background: #c0392b !important; color: #fff !important; border: none !important; border-radius: 4px !important; padding: 10px 25px !important; font-size: 13px !important; font-weight: 700 !important; cursor: pointer !important; display: inline-flex !important; align-items: center !important; gap: 8px !important; }
        .btn-save-red:hover { background: #a93226 !important; box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important; }
        .btn-cancel-outline { padding: 8px 24px; border: 1px solid #e5e7eb; background: #fff; color: #4b5563; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; }

        /* Category Modal List */
        .cat-list-item { display: flex; align-items: center; justify-content: space-between; padding: 15px 25px; border-bottom: 1px solid #f5f5f5; cursor: pointer; transition: background .1s; }
        .cat-list-item:hover { background: #fafafa; }
        .cat-list-name { font-size: 15px; color: #333; font-weight: 500; }
        .cat-list-count { display: inline-flex; align-items: center; justify-content: center; min-width: 26px; height: 26px; background: #f0f0f0; color: #666; font-size: 12px; font-weight: 700; border-radius: 6px; }
    </style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent" style="padding:0; background: #fafbfc; min-height: 100vh;">
    <div style="padding: 30px 45px 0 45px;">
        <div class="page-header" style="margin-bottom: 20px; border: none;">
            <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222;">{{ __('Reusable Content: Static Page Content') }}</h2>
            <div class="rc-header-actions">
                <button class="rc-btn-outline" type="button" id="manageCatBtn"><i class="bi bi-folder2-open"></i> {{ __('Manage Categories') }}</button>
                <button class="rc-btn-outline" type="button" id="bulkEditBtn"><i class="bi bi-pencil-square"></i> {{ __('Bulk Edit') }}</button>
                <button class="rc-btn-add" type="button" id="addSpcBtn"><i class="bi bi-plus-lg"></i> {{ __('Add New Static Page Content') }}</button>
            </div>
        </div>

        <div class="rc-wrapper">
            <div class="rc-sidebar">
                <a href="{{ route('dealer.website.faqs.index') }}" class="rc-sidebar-item"><i class="bi bi-question-circle"></i> FAQs</a>
                <a href="#" class="rc-sidebar-item"><i class="bi bi-megaphone"></i> OEM Promo Banners</a>
                <a href="{{ route('dealer.website.srp-content.index') }}" class="rc-sidebar-item"><i class="bi bi-file-earmark-text"></i> Content: Search Results (SRP)</a>
                <a href="{{ route('dealer.website.static-page-content.index') }}" class="rc-sidebar-item active"><i class="bi bi-file-text"></i> Static Page Content</a>
                <a href="#" class="rc-sidebar-item"><i class="bi bi-star"></i> Customer Reviews</a>
                <a href="#" class="rc-sidebar-item"><i class="bi bi-person"></i> Staff Members</a>
                <a href="#" class="rc-sidebar-item"><i class="bi bi-briefcase"></i> Job Posts</a>
                <a href="#" class="rc-sidebar-item"><i class="bi bi-tags"></i> Service Offers</a>
                <a href="#" class="rc-sidebar-item"><i class="bi bi-calendar3"></i> Events</a>
            </div>
            
            <div style="flex: 1; display: flex; flex-direction: column;">
                {{-- List View --}}
                <div id="spcListView">
                    <div class="rc-main-container">
                        <table class="rc-table" id="spcTable">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Title</th>
                                    <th style="width: 15%;">Category</th>
                                    <th style="width: 20%;">Author</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="spcTableBody"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Add/Edit Form View --}}
                <div id="spcFormView" style="display: none;">
                    <div style="margin-bottom: 20px;">
                        <button class="rc-btn-outline" id="spcBackBtn" style="height: 38px;"><i class="bi bi-chevron-left"></i> Go Back</button>
                    </div>
                    <div class="rc-main-container" style="padding: 0;">
                        <div style="padding: 20px 30px; border-bottom: 1px solid #f0f0f0;">
                            <h3 style="font-size: 15px; font-weight: 600; color: #333; margin: 0;" id="formViewTitle">Add Static Page Content</h3>
                        </div>
                        <div style="padding: 30px;">
                            <div style="margin-bottom: 25px;">
                                <label class="bulk-col-label">H1 Override <span>*</span></label>
                                <input type="text" id="spcH1Input" class="bulk-input" placeholder="Main header of the page">
                            </div>

                            <div style="margin-bottom: 25px;">
                                <label class="bulk-col-label">Meta Title / Title Tag Override (optional)</label>
                                <input type="text" id="spcMetaTitleInput" class="bulk-input" placeholder="Meta title">
                            </div>

                            <div style="margin-bottom: 25px;">
                                <label class="bulk-col-label">Meta Description (optional override - ideally 160 characters or less)</label>
                                <textarea id="spcMetaDescInput" class="bulk-textarea" placeholder="Enter meta description"></textarea>
                            </div>

                            <div style="margin-bottom: 25px;" class="slug-input-wrap">
                                <label class="bulk-col-label">Slug to map content to <span>*</span></label>
                                <input type="text" id="spcSlugInput" class="bulk-input" placeholder="/incentives">
                                <div class="field-hint">
                                    Exact match: <strong>/incentives</strong> or <strong>/incentives?make[]=Chevrolet</strong>. Add '*' for wildcard: <strong>/incentives*</strong> matches any params. Use | for OR: <strong>/incentives?make[]=Chrysler|Dodge|Jeep|Ram</strong> matches any of those makes.
                                </div>
                            </div>

                            <div style="margin-bottom: 25px;">
                                <label class="bulk-col-label">Content Placement (defaults to top of page)</label>
                                <select id="spcPlacementInput" class="bulk-select">
                                    <option value="top">Top of Page</option>
                                    <option value="bottom">Bottom of Page</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 25px;">
                                <label class="bulk-col-label">Category</label>
                                <select id="spcCategorySelect" class="bulk-select"></select>
                            </div>

                            <div style="margin-bottom: 30px;">
                                <label class="bulk-col-label">Page Content</label>
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
                                    <textarea id="spcContentInput" class="rte-textarea" placeholder="Start typing your content..."></textarea>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-start;">
                                <button class="btn-save-red" id="spcSaveBtn"><i class="bi bi-check-lg"></i> Save</button>
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
        <div class="faq-modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #eee;">
            <h3 class="faq-modal-title" id="catModalTitle" style="margin: 0; font-size: 18px; font-weight: 700;">Manage Categories</h3>
            <button class="faq-modal-close" id="catModalClose" style="background: none; border: none; font-size: 24px; color: #aaa; cursor: pointer;">&times;</button>
        </div>
        <div class="faq-modal-body" id="catModalBody"></div>
    </div>
</div>

{{-- Bulk Edit Modal --}}
<div class="faq-modal-overlay" id="bulkModalOverlay">
    <div class="faq-modal">
        <div class="bulk-modal-content">
            <div class="bulk-header-top">
                <h3 class="faq-modal-title" style="font-size: 14px; font-weight: 600;">Bulk Edit Content: Static Page Content</h3>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <button class="rc-btn-outline" id="bulkAddRowBtn" style="padding: 7px 15px; background: #fff; height: 34px;"><i class="bi bi-plus-lg"></i> Add Row</button>
                    <button class="faq-modal-close" id="bulkModalClose" style="font-size: 20px;">&times;</button>
                </div>
            </div>
            <div class="bulk-body">
                <div class="bulk-table-header">
                    <div class="bulk-header-label">Nickname <span>*</span></div>
                    <div class="bulk-header-label">Slug <span>*</span></div>
                    <div class="bulk-header-label">H1 Override</div>
                    <div class="bulk-header-label">Meta Title</div>
                    <div class="bulk-header-label">Meta Description</div>
                    <div class="bulk-header-label">Placement</div>
                    <div class="bulk-header-label">Category</div>
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
@endsection

@push('page-scripts')
<script>
(function(){
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var ROUTES = {
        store:          '{{ route("dealer.website.static-page-content.store") }}',
        update:         '{{ route("dealer.website.static-page-content.update", ["staticPageContent" => "__ID__"]) }}',
        destroy:        '{{ route("dealer.website.static-page-content.destroy", ["staticPageContent" => "__ID__"]) }}',
        catStore:       '{{ route("dealer.website.static-page-content.categories.store") }}',
        catUpdate:      '{{ route("dealer.website.static-page-content.categories.update", ["staticPageCategory" => "__ID__"]) }}',
        catDestroy:     '{{ route("dealer.website.static-page-content.categories.destroy", ["staticPageCategory" => "__ID__"]) }}',
        bulkUpdate:     '{{ route("dealer.website.static-page-content.bulk-update") }}',
    };

    var contents = @json($contents);
    var categories = @json($categories);
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

    function renderTable(){
        var tbody = document.getElementById('spcTableBody');
        var html = '';
        contents.forEach(function(c){
            var catName = c.category ? c.category.name : 'Uncategorized';
            var dotClass = c.status === 'Published' ? 'published' : 'draft';
            html += '<tr>' +
                '<td>' +
                    '<div class="rc-title-cell">' +
                        '<span class="rc-drag"><i class="bi bi-list"></i></span>' +
                        '<div class="rc-content-wrap">' +
                            '<div class="rc-nickname">'+(c.nickname || c.slug)+'</div>' +
                            '<div class="rc-row-actions">' +
                                '<a href="#" class="rc-row-btn" onclick="window._spcEdit('+c.id+');return false;"><i class="bi bi-pencil-square"></i> Edit</a>' +
                                '<button class="rc-row-btn trash-btn" onclick="window._spcTrash('+c.id+')"><i class="bi bi-trash"></i> Trash</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</td>' +
                '<td><span style="color:#666; font-size:14px;">'+catName+'</span></td>' +
                '<td><span style="color:#666; font-size:14px;">'+(c.author||'System')+'</span></td>' +
                '<td>' +
                    '<div class="faq-status-wrap">' +
                        '<span class="faq-status-dot '+dotClass+'"></span>' +
                        '<span style="font-size:14px;color:#444;">'+c.status+'</span>' +
                    '</div>' +
                '</td>' +
                '</tr>';
        });
        tbody.innerHTML = html || '<tr><td colspan="4" style="text-align:center;padding:50px;color:#999;">No results found.</td></tr>';
    }

    var listView = document.getElementById('spcListView');
    var formView = document.getElementById('spcFormView');

    document.getElementById('addSpcBtn').addEventListener('click', function(){ openForm(null); });
    document.getElementById('spcBackBtn').addEventListener('click', function(){
        formView.style.display = 'none';
        listView.style.display = 'block';
        editingId = null;
    });

    function populateCategorySelect(selectedId){
        var sel = document.getElementById('spcCategorySelect');
        var html = '<option value="">-- Uncategorized --</option>';
        categories.forEach(function(cat){
            html += '<option value="'+cat.id+'"'+(cat.id == selectedId ? ' selected' : '')+'>'+cat.name+'</option>';
        });
        sel.innerHTML = html;
    }

    function openForm(id){
        editingId = id;
        var item = id ? contents.find(function(c){ return c.id===id; }) : null;
        document.getElementById('formViewTitle').textContent = item ? 'Edit Static Page Content' : 'Add Static Page Content';
        
        document.getElementById('spcH1Input').value = item ? (item.h1_override||'') : '';
        document.getElementById('spcMetaTitleInput').value = item ? (item.meta_title||'') : '';
        document.getElementById('spcMetaDescInput').value = item ? (item.meta_description||'') : '';
        document.getElementById('spcSlugInput').value = item ? item.slug : '';
        document.getElementById('spcPlacementInput').value = item ? item.placement : 'top';
        populateCategorySelect(item ? item.static_page_category_id : '');
        
        var contentInput = document.getElementById('spcContentInput');
        contentInput.value = item ? (item.content||'') : '';
        
        listView.style.display = 'none';
        formView.style.display = 'block';

        // Auto-resize
        contentInput.style.height = 'auto';
        contentInput.style.height = (contentInput.scrollHeight > 250 ? contentInput.scrollHeight : 250) + 'px';
        contentInput.addEventListener('input', function(){
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight > 250 ? this.scrollHeight : 250) + 'px';
        });
    }

    document.getElementById('spcSaveBtn').addEventListener('click', function(){
        var payload = {
            nickname:         document.getElementById('spcH1Input').value,
            h1_override:      document.getElementById('spcH1Input').value,
            meta_title:       document.getElementById('spcMetaTitleInput').value,
            meta_description: document.getElementById('spcMetaDescInput').value,
            slug:             document.getElementById('spcSlugInput').value.trim(),
            placement:        document.getElementById('spcPlacementInput').value,
            static_page_category_id: document.getElementById('spcCategorySelect').value || null,
            content:          document.getElementById('spcContentInput').value,
            status:           'Published'
        };

        if(!payload.slug || !payload.h1_override) return alert('H1 and Slug are required');

        if(editingId){
            ajax('PATCH', ROUTES.update.replace('__ID__', editingId), payload, function(err, res){
                if(err) return alert(err);
                var idx = contents.findIndex(function(c){ return c.id===editingId; });
                if(idx>-1) contents[idx] = res;
                document.getElementById('spcBackBtn').click(); renderTable();
            });
        } else {
            ajax('POST', ROUTES.store, payload, function(err, res){
                if(err) return alert(err);
                contents.push(res);
                document.getElementById('spcBackBtn').click(); renderTable();
            });
        }
    });

    window._spcEdit = function(id){ openForm(id); };
    window._spcTrash = function(id){
        if(!confirm('Delete this item?')) return;
        ajax('DELETE', ROUTES.destroy.replace('__ID__', id), null, function(err){
            if(err) return alert(err);
            contents = contents.filter(function(c){ return c.id!==id; });
            renderTable();
        });
    };

    // ── Category Management ──────────────────────────────────────────────────
    var catOverlay = document.getElementById('catModalOverlay');
    document.getElementById('manageCatBtn').addEventListener('click', showCatList);
    document.getElementById('catModalClose').addEventListener('click', function(){ catOverlay.classList.remove('open'); });

    function showCatList(){
        document.getElementById('catModalTitle').textContent = 'Manage Categories';
        var body = document.getElementById('catModalBody');
        var html = '';
        categories.forEach(function(c){
            var count = c.contents_count || 0;
            html += '<div class="cat-list-item" data-id="'+c.id+'"><div class="cat-list-left"><span class="cat-list-name">'+c.name+'</span> <span class="cat-list-count">'+count+'</span></div><span class="cat-list-arrow"><i class="bi bi-chevron-right"></i></span></div>';
        });
        html += '<div style="padding:25px; text-align:center;"><button class="rc-btn-add" id="catAddNewBtn"><i class="bi bi-plus-lg"></i> Add Category</button></div>';
        body.innerHTML = html;
        catOverlay.classList.add('open');

        body.querySelectorAll('.cat-list-item').forEach(function(item){
            item.addEventListener('click', function(){
                var cat = categories.find(function(c){ return c.id == item.dataset.id; });
                if(cat) showCatEdit(cat);
            });
        });
        document.getElementById('catAddNewBtn').addEventListener('click', function(){ showCatEdit(null); });
    }

    function showCatEdit(cat){
        var isNew = !cat;
        document.getElementById('catModalTitle').textContent = isNew ? 'Add Category' : 'Edit Category';
        var body = document.getElementById('catModalBody');
        body.innerHTML = '<div style="padding:25px;"><label class="bulk-col-label">Category Name</label>' +
            '<input type="text" id="catNameInput" class="bulk-input" value="'+(isNew?'':cat.name)+'" placeholder="Enter category name">' +
            '<div style="display:flex; gap:10px; margin-top:20px;"><button class="btn-save-red" id="catSaveBtn" style="padding: 9px 25px;">'+(isNew?'Add':'Save')+'</button>' +
            '<button class="btn-cancel-outline" id="catBackBtn" style="padding: 9px 25px;">Back</button>' +
            (isNew?'':'<button class="rc-row-btn trash-btn" id="catDeleteBtn" style="margin-left:auto; padding: 9px 15px;">Delete</button>') +
            '</div></div>';

        document.getElementById('catBackBtn').addEventListener('click', showCatList);
        document.getElementById('catSaveBtn').addEventListener('click', function(){
            var val = document.getElementById('catNameInput').value.trim();
            if(!val) return;
            if(isNew){
                ajax('POST', ROUTES.catStore, {name:val}, function(err, res){
                    if(err) return alert(err);
                    categories.push(res); showCatList();
                });
            } else {
                ajax('PATCH', ROUTES.catUpdate.replace('__ID__', cat.id), {name:val}, function(err, res){
                    if(err) return alert(err);
                    var idx = categories.findIndex(function(c){ return c.id===cat.id; });
                    if(idx>-1) categories[idx] = res;
                    showCatList(); renderTable();
                });
            }
        });

        var delBtn = document.getElementById('catDeleteBtn');
        if(delBtn) delBtn.addEventListener('click', function(){
            if(!confirm('Delete category "'+cat.name+'"?')) return;
            ajax('DELETE', ROUTES.catDestroy.replace('__ID__', cat.id), null, function(err){
                if(err) return alert(err);
                categories = categories.filter(function(c){ return c.id!==cat.id; });
                showCatList(); renderTable();
            });
        });
    }

    // ── Bulk Edit ────────────────────────────────────────────────────────────
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
        
        var catOpts = '<option value="">-- Uncategorized --</option>';
        categories.forEach(function(cat){
            catOpts += '<option value="'+cat.id+'"'+(item && item.static_page_category_id == cat.id ? ' selected' : '')+'>'+cat.name+'</option>';
        });

        div.innerHTML = 
            '<input type="text" class="bulk-input bulk-n" value="'+(item ? (item.nickname||'') : '')+'" placeholder="Nickname">' +
            '<input type="text" class="bulk-input bulk-s" value="'+(item ? item.slug : '')+'" placeholder="/slug">' +
            '<input type="text" class="bulk-input bulk-h1" value="'+(item ? (item.h1_override||'') : '')+'" placeholder="H1">' +
            '<input type="text" class="bulk-input bulk-mt" value="'+(item ? (item.meta_title||'') : '')+'" placeholder="Meta Title">' +
            '<textarea class="bulk-textarea bulk-md" placeholder="Meta Desc">'+(item ? (item.meta_description||'') : '')+'</textarea>' +
            '<select class="bulk-select bulk-p">' +
                '<option value="top" '+(item && item.placement==='top'?'selected':'')+'>Top</option>' +
                '<option value="bottom" '+(item && item.placement==='bottom'?'selected':'')+'>Bottom</option>' +
            '</select>' +
            '<select class="bulk-select bulk-cat">'+catOpts+'</select>' +
            '<textarea class="bulk-textarea bulk-c" style="height: 100px; min-height: 100px;" placeholder="Content">'+(item ? (item.content||'') : '')+'</textarea>' +
            '<select class="bulk-select bulk-status">' +
                '<option value="Published" '+(item && item.status==='Published'?'selected':'')+'>Published</option>' +
                '<option value="Draft" '+(item && item.status==='Draft'?'selected':'')+'>Draft</option>' +
            '</select>' +
            '<div class="bulk-action-del"><i class="bi bi-trash"></i></div>';
        
        div.querySelector('.bulk-action-del').addEventListener('click', function(){
            if(confirm('Remove row?')){
                if(div.dataset.id) div.dataset.deleted = 'true';
                div.style.display = 'none';
            }
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
                static_page_category_id: row.querySelector('.bulk-cat').value || null,
                content: row.querySelector('.bulk-c').value.trim(),
                status: row.querySelector('.bulk-status').value,
                is_deleted: row.dataset.deleted === 'true'
            });
        });

        ajax('POST', ROUTES.bulkUpdate, {contents: data}, function(err, res){
            if(err) return alert(err);
            contents = res; bulkOverlay.classList.remove('open'); renderTable();
        });
    });

    renderTable();
})();
</script>
@endpush
