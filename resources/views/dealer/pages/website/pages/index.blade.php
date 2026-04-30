@extends('layouts.dealer.app')

@section('title', __('Pages'))

@push('page-assets')
<style>
    body { background-color: #f8f9fa; }
    .main-content {
        padding: 40px;
        flex: 1;
        width: 100%;
    }
    .page-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #1a1f36;
        margin: 0;
    }
    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .search-input-group {
        position: relative;
        width: 280px;
    }
    .search-input {
        width: 100%;
        padding: 8px 12px 8px 35px;
        border-radius: 8px;
        border: 1px solid #e0e6ed;
        font-size: 14px;
        color: #4f566b;
        background: #fff;
    }
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8792a2;
        font-size: 13px;
    }
    .btn-template {
        background: #fff;
        border: 1px solid #e0e6ed;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        color: #4f566b;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-add-page {
        background: #c0392b;
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-add-page:hover { background: #a93226; color: #fff; }

    .content-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0e6ed;
        overflow: hidden;
    }
    .table { margin: 0; }
    .table thead th {
        background: #fcfdfe;
        border-bottom: 1px solid #e0e6ed;
        padding: 12px 15px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #8792a2;
        letter-spacing: 0.5px;
    }
    .table tbody td {
        padding: 15px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
        font-size: 14px;
        color: #4f566b;
    }
    .table tbody tr:hover { background-color: #fcfdfe; }
    
    .chk-col { width: 40px; }
    .chk-custom { width: 16px; height: 16px; border: 1px solid #e0e6ed; border-radius: 4px; }

    .title-col { color: #1a1f36; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px; }
    .external-link { color: #8792a2; font-size: 11px; }
    
    .inline-actions { display: flex; gap: 8px; margin-top: 8px; }
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border: 1px solid #e0e6ed;
        border-radius: 6px;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        color: #4f566b;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-action:hover { background: #f8f9fa; border-color: #d0d7de; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .btn-action i { font-size: 12px; color: #8792a2; }
    .btn-action.action-trash { color: #c0392b; }
    .btn-action.action-trash i { color: #c0392b; }

    /* Custom Modal */
    .custom-modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(2px);
    }
    .custom-modal {
        background: #fff;
        width: 440px;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        overflow: hidden;
        animation: modalFadeIn 0.3s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-title { display: flex; align-items: center; gap: 10px; font-weight: 700; color: #1a1f36; font-size: 16px; }
    .modal-title i { color: #f39c12; font-size: 20px; }
    .modal-body { padding: 24px 20px; color: #4f566b; font-size: 15px; line-height: 1.5; }
    .modal-footer {
        padding: 16px 20px;
        background: #f8f9fa;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        border-top: 1px solid #f1f3f5;
    }
    .btn-modal-cancel {
        padding: 8px 20px;
        border: 1px solid #d0d7de;
        background: #fff;
        border-radius: 6px;
        font-weight: 600;
        color: #4f566b;
        cursor: pointer;
    }
    .btn-modal-continue {
        padding: 8px 25px;
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-modal-continue:hover { background: #a93226; }

    .url-text { color: #8792a2; font-family: monospace; display: flex; align-items: center; gap: 6px; }
    
    .status-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        font-size: 13px;
    }
    .dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-published { background: #27ae60; }
    .dot-draft { background: #8792a2; }
    
    .meta-check { color: #27ae60; font-size: 16px; }
    
    .date-text { font-size: 13px; color: #4f566b; }
    
    .empty-state {
        padding: 80px 20px;
        text-align: center;
    }
</style>
@endpush

@section('page-content')
<div class="main-content">
    <div class="page-header-flex">
        <h1 class="page-title">Pages</h1>
        <div class="header-actions">
            <div class="search-input-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search pages">
            </div>
            <a href="#" class="btn-template">
                <i class="far fa-file-alt"></i> Start From Template
            </a>
            <a href="{{ $routes['create'] }}" class="btn-add-page">
                <i class="fas fa-plus"></i> Add Page
            </a>
        </div>
    </div>

    @if ($pages->count())
        <div class="content-card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="chk-col"><input type="checkbox" class="chk-custom"></th>
                            <th width="35%">Title</th>
                            <th>URL</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th class="text-center">Meta Desc.</th>
                            <th>Created</th>
                            <th>Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr>
                                <td class="chk-col"><input type="checkbox" class="chk-custom"></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <a href="{{ str_replace('__ID__', $page->id, $routes['edit']) }}" class="title-col m-0">
                                                {{ $page->title }}
                                            </a>
                                            <a href="{{ url($page->slug) }}" target="_blank" rel="noopener noreferrer" class="ms-2 text-muted" style="font-size:12px">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                        <div class="inline-actions">
                                            <a href="{{ str_replace('__ID__', $page->id, $routes['edit']) }}" class="btn-action">
                                                <i class="fa-regular fa-pen-to-square"></i> Edit
                                            </a>
                                            <button type="button" class="btn-action action-trash" onclick="showDeleteModal({{ $page->id }})">
                                                <i class="fa-regular fa-trash-can"></i> Trash
                                            </button>
                                            <button type="button" class="btn-action" onclick="duplicatePage({{ $page->id }})">
                                                <i class="fa-regular fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="url-text d-flex align-items-center">
                                        <span class="text-muted">/{{ $page->slug }}</span>
                                        <a href="{{ url($page->slug) }}" target="_blank" rel="noopener noreferrer" class="ms-2 text-muted" style="font-size:11px">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    {{-- Showing dealer name or placeholder as author --}}
                                    <span class="date-text">{{ $page->dealer->name ?? 'Admin' }}</span>
                                </td>
                                <td>
                                    @php $isPublished = $page->is_active && $page->published_at && $page->published_at <= now(); @endphp
                                    <div class="status-dot">
                                        <div class="dot {{ $isPublished ? 'dot-published' : 'dot-draft' }}"></div>
                                        {{ $isPublished ? 'Published' : 'Draft' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($page->meta_description)
                                        <i class="fas fa-check-circle meta-check"></i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><span class="date-text">{{ $page->created_at->format('M d, Y') }}</span></td>
                                <td><span class="date-text">{{ $page->updated_at->format('M d, Y') }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($pages->hasPages())
                <div class="p-4 border-top">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="content-card empty-state">
            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
            <h3 class="text-dark fw-bold">No pages found</h3>
            <p class="text-muted">You haven't created any pages yet.</p>
            <a href="{{ $routes['create'] }}" class="btn-add-page d-inline-flex mt-2">
                <i class="fas fa-plus"></i> Add Page
            </a>
        </div>
    @endif
</div>

<div class="custom-modal-overlay" id="deleteModalOverlay">
    <div class="custom-modal">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa-solid fa-circle-info"></i> Are you sure?
            </div>
            <button type="button" class="btn-close" style="border:none; background:none; font-size:20px; cursor:pointer;" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            Are you sure you want to delete this post?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn-modal-continue" id="confirmDeleteBtn">Continue</button>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="duplicateForm" method="POST" style="display: none;">
    @csrf
</form>

<script>
let pageIdToDelete = null;

function showDeleteModal(pageId) {
    pageIdToDelete = pageId;
    document.getElementById('deleteModalOverlay').style.display = 'flex';
}

function closeDeleteModal() {
    pageIdToDelete = null;
    document.getElementById('deleteModalOverlay').style.display = 'none';
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (pageIdToDelete) {
        const form = document.getElementById('deleteForm');
        let url = '{{ route("dealer.website.pages.destroy", ":id") }}';
        form.action = url.replace(':id', pageIdToDelete);
        form.submit();
    }
});

function duplicatePage(pageId) {
    if (confirm('Are you sure you want to duplicate this page?')) {
        const form = document.getElementById('duplicateForm');
        let url = '{{ route("dealer.website.pages.duplicate", ":id") }}';
        form.action = url.replace(':id', pageId);
        form.submit();
    }
}
</script>
@endsection
