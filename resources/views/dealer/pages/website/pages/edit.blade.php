@extends('layouts.dealer.app')
@section('title', __('Edit Page'))
@push('page-assets')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('assets/panels/website-pages/css/editor.css') }}?v=5.1"/>
<link rel="stylesheet" href="{{ asset('assets/panels/website-pages/css/heading.css') }}?v=5.1"/>
<link rel="stylesheet" href="{{ asset('assets/panels/website-pages/css/p.css') }}?v=5.1"/>
<link rel="stylesheet" href="{{ asset('assets/panels/website-pages/css/button.css') }}?v=5.1"/>
<style>
.layout{display:flex!important;width:100%!important;max-width:100%!important;margin:0!important;padding:0!important;height:100vh;overflow:hidden}
.of-master-frame{display:flex;flex-direction:column;width:100%;height:100vh;background:#f8f9fa}
.bg-lighter{background-color:#f1f3f5!important}
.of-header h4{white-space:nowrap;margin-bottom:0;font-size:28px!important;color:#1a1f36}
.sidebar-right{width:450px;border-left:1px solid #e0e6ed;background:#fff;height:100%;overflow-y:auto;padding:25px;flex-shrink:0}
.section-title{font-size:11px;font-weight:800;color:#adb5bd;text-transform:uppercase;letter-spacing:1.2px;margin:25px 0 15px}
.block-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.block-item{border:1px solid #f1f3f5;border-radius:8px;padding:18px 5px;text-align:center;cursor:grab;background:#fff;transition:all .15s ease}
.block-item:hover{border-color:#c0392b;background:#fffcfc;transform:translateY(-2px);box-shadow:0 6px 15px rgba(0,0,0,.06)}
.block-item i{font-size:22px;display:block;margin-bottom:10px;color:#c0392b}
.block-item span{font-size:11px;font-weight:700;color:#4f566b;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.canvas-left{flex-grow:1;overflow:auto;background:#f8f9fa;padding:40px}
.hs-row{margin-bottom:22px}
.hs-row label{display:block;font-size:11px;font-weight:800;text-transform:uppercase;color:#4f566b;margin-bottom:8px;letter-spacing:.8px}
.hs-input,.hs-select{width:100%;padding:10px 12px;border:1px solid #e0e6ed;border-radius:8px;font-size:14px;color:#1a1f36;transition:all .2s;background:#fff}
.hs-divider{border:0;border-top:1px solid #f1f3f5;margin:20px 0}
.hs-actions{display:flex;gap:10px;margin-top:20px}
.hs-btn-remove{background:#fff5f5;color:#c0392b;border:1px solid #ffe3e3;padding:10px 15px;border-radius:8px;font-weight:700;font-size:13px;display:flex;align-items:center;gap:8px;flex:1;justify-content:center;cursor:pointer}
.hs-btn-remove:hover{background:#c0392b;color:#fff}
[id$="-settings-panel"]{display:none}

.side-panel {
    position: fixed;
    right: -450px;
    top: 0;
    width: 450px;
    height: 100vh;
    background: #fff;
    box-shadow: -10px 0 30px rgba(0,0,0,0.05);
    z-index: 1050;
    transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}
.side-panel.open { right: 0; }
.side-panel-header {
    padding: 24px;
    border-bottom: 1px solid #f1f3f5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.side-panel-title { font-size: 18px; font-weight: 800; color: #1a1f36; }
.side-panel-body { padding: 24px; overflow-y: auto; flex: 1; }
.side-panel-footer { padding: 24px; border-top: 1px solid #f1f3f5; display: flex; gap: 12px; }

.ps-section-title {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    color: #adb5bd;
    margin-bottom: 15px;
    letter-spacing: 1px;
}
.featured-image-box {
    border: 2px dashed #e0e6ed;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #fcfdfe;
}
.featured-image-box:hover { border-color: #c0392b; background: #fffcfc; }
.featured-image-box i { font-size: 32px; color: #adb5bd; margin-bottom: 12px; }
.featured-image-box p { font-size: 13px; color: #4f566b; margin: 0; font-weight: 600; }

.revision-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #f1f3f5;
}
.revision-info { display: flex; align-items: center; gap: 12px; }
.revision-version {
    width: 28px;
    height: 28px;
    background: #f1f3f5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    color: #4f566b;
}
.revision-date { font-size: 13px; color: #1a1f36; font-weight: 600; }
.btn-restore {
    background: #fff5f5;
    color: #c0392b;
    border: 1px solid #ffe3e3;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}
.btn-restore:hover { background: #c0392b; color: #fff; }

#drop-indicator {
    height: 4px;
    background: #c0392b;
    border-radius: 2px;
    margin: 10px 0;
    width: 100%;
    transition: all 0.2s ease;
    box-shadow: 0 0 10px rgba(192, 57, 43, 0.4);
}
.drag-over {
    background-color: rgba(192, 57, 43, 0.02) !important;
    outline: 2px dashed #c0392b !important;
    outline-offset: -2px;
}

.editor-empty-state {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f1f3f5;
    margin-bottom: 20px;
    color: #adb5bd;
}
.editor-empty-badge {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    margin-right: 12px;
    color: #6c757d;
}
.editor-empty-input {
    border: none;
    background: transparent;
    font-size: 14px;
    color: #adb5bd;
    width: 100%;
    outline: none;
}
/* Layout columns are always interactive */
.col-drop-zone {
    pointer-events: auto !important;
}
/* Blocks and editables are always interactive */
.dropped-block,
.dropped-block-inner,
[contenteditable="true"],
[contenteditable="true"] * {
    pointer-events: auto !important;
    user-select: text !important;
}
</style>
@endpush
@section('page-content')
<div class="overlay" id="side-overlay"></div>
<div class="of-master-frame">
<form action="{{ route('dealer.website.pages.update', $page->id) }}" method="POST" id="page-builder-form" style="display:contents" onsubmit="return prepareFormSubmit()">
@csrf
@method('PATCH')
<div class="of-header p-4 d-flex align-items-center bg-white border-bottom shadow-sm">
<h4 class="fw-bold m-0">Edit Page</h4>
<div class="ms-auto d-flex align-items-center gap-2">
<a href="{{ route('dealer.website.pages.index') }}" class="btn btn-outline-secondary btn-sm px-4 fw-bold"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
<button type="button" class="btn btn-outline-secondary btn-sm px-4 fw-bold" onclick="toggleSidePanel('page-settings')"><i class="fa-solid fa-gear me-2"></i>Page Settings</button>
<button type="button" class="btn btn-outline-secondary btn-sm px-4 fw-bold" onclick="toggleSidePanel('page-revisions')"><i class="fa-solid fa-clock-rotate-left me-2"></i>Page Revisions</button>
<button type="submit" class="btn btn-danger btn-sm px-5 fw-bold" style="background:#c0392b"><i class="fa-solid fa-check me-2"></i>Update</button>
</div>
</div>
<div class="input-group border-bottom bg-white" style="height:55px">
<span class="bg-lighter input-group-text border-0 px-4 fw-bold small text-muted" style="min-width:140px">PAGE TITLE</span>
<input type="text" value="{{ $page->title }}" class="form-control border-0 px-4 fs-6" name="title" id="page-title" required autocomplete="off" placeholder="Enter page title...">
<span class="bg-white input-group-text border-0 text-muted small px-4 border-start" id="top-status-badge">
    <i class="fa-solid fa-circle me-2" style="font-size:8px;color:{{ $page->is_active ? '#27ae60' : '#ced4da' }}"></i> Status: {{ $page->is_active ? 'Published' : 'Draft' }}
</span>
</div>

<div class="d-flex flex-grow-1 overflow-hidden">
<div class="canvas-left flex-grow-1 overflow-auto">
<div class="bg-white border rounded shadow-sm mx-auto my-4" style="max-width:1000px;min-height:1000px">
<div class="card border-0">
<div class="fw-bold bg-lighter card-header d-flex justify-content-between align-items-center py-3">
<span class="small text-dark">Content Editor</span>
<div class="d-flex align-items-center gap-2">
<div class="btn-group bg-white border">
<button type="button" class="btn btn-light btn-sm border-end" id="btn-undo"><i class="fa-solid fa-rotate-left"></i> Undo</button>
<button type="button" class="btn btn-light btn-sm" id="btn-redo"><i class="fa-solid fa-rotate-right text-primary"></i> Redo</button>
</div>
</div>
</div>
<div class="p-4">
<div id="content-editor-zone">
<div id="blocks-container" style="min-height:800px"></div>
</div>
</div>
</div>
</div>
</div>
<div class="sidebar-right">
@include('dealer.pages.website.pages._panels')
<div id="sidebar-default-content">
<p class="text-muted small mb-3 ps-1" style="font-size:11px">Select a block from the editor to adjust its settings.</p>
<div class="section-title">Layout</div>
<div class="block-grid">
<div class="block-item" draggable="true" data-type="container"><i class="fa-solid fa-square-poll-horizontal"></i><span>Container</span></div>
<div class="block-item" draggable="true" data-type="2col"><i class="fa-solid fa-columns"></i><span>2-Col</span></div>
<div class="block-item" draggable="true" data-type="3col"><i class="fa-solid fa-table-columns"></i><span>3-Col</span></div>
<div class="block-item" draggable="true" data-type="overlay"><i class="fa-solid fa-clone"></i><span>Overlay</span></div>
<div class="block-item" draggable="true" data-type="html"><i class="fa-solid fa-code"></i><span>HTML</span></div>
<div class="block-item" draggable="true" data-type="css"><i class="fa-solid fa-terminal"></i><span>CSS</span></div>
</div>
<div class="section-title">Elements</div>
<div class="block-grid">
<div class="block-item" draggable="true" data-type="span"><i class="fa-solid fa-quote-left"></i><span>Span</span></div>
<div class="block-item" draggable="true" data-type="heading"><i class="fa-solid fa-heading"></i><span>Heading</span></div>
<div class="block-item" draggable="true" data-type="text"><i class="fa-solid fa-align-left"></i><span>Text</span></div>
<div class="block-item" draggable="true" data-type="button"><i class="fa-solid fa-toggle-on"></i><span>Button</span></div>
<div class="block-item" draggable="true" data-type="divider"><i class="fa-solid fa-minus"></i><span>Divider</span></div>
<div class="block-item" draggable="true" data-type="image"><i class="fa-solid fa-image"></i><span>Image</span></div>
<div class="block-item" draggable="true" data-type="video"><i class="fa-solid fa-video"></i><span>Video</span></div>
<div class="block-item" draggable="true" data-type="carousel"><i class="fa-solid fa-images"></i><span>Carousel</span></div>
<div class="block-item" draggable="true" data-type="accordion"><i class="fa-solid fa-list"></i><span>Accordion</span></div>
<div class="block-item" draggable="true" data-type="spacer"><i class="fa-solid fa-arrows-up-down"></i><span>Spacer</span></div>
<div class="block-item" draggable="true" data-type="icon"><i class="fa-solid fa-icons"></i><span>Icon</span></div>
<div class="block-item" draggable="true" data-type="cart"><i class="fa-solid fa-cart-shopping"></i><span>Cart</span></div>
<div class="block-item" draggable="true" data-type="iframe"><i class="fa-solid fa-window-maximize"></i><span>iFrame</span></div>
<div class="block-item" draggable="true" data-type="card"><i class="fa-solid fa-id-card"></i><span>Card</span></div>
</div>
<div class="section-title">Overfuel Blocks</div>
<div class="block-grid">
<div class="block-item" draggable="true" data-type="inventory"><i class="fa-solid fa-car-side"></i><span>Inventory</span></div>
<div class="block-item" draggable="true" data-type="search"><i class="fa-solid fa-magnifying-glass"></i><span>Search</span></div>
<div class="block-item" draggable="true" data-type="form"><i class="fa-solid fa-file-invoice"></i><span>Form</span></div>
<div class="block-item" draggable="true" data-type="blog"><i class="fa-solid fa-newspaper"></i><span>Blog Posts</span></div>
<div class="block-item" draggable="true" data-type="content_block"><i class="fa-solid fa-cubes"></i><span>Content Block</span></div>
<div class="block-item" draggable="true" data-type="body_types"><i class="fa-solid fa-truck-pickup"></i><span>Body Types</span></div>
<div class="block-item" draggable="true" data-type="map_hours"><i class="fa-solid fa-clock"></i><span>Map / Hours</span></div>
<div class="block-item" draggable="true" data-type="plugin"><i class="fa-solid fa-plug"></i><span>Plugin</span></div>
</div>
</div>
</div>
</div>

{{-- Page Settings Panel (Right Off-canvas) --}}
<div class="side-panel" id="side-panel-page-settings">
    <div class="side-panel-header">
        <span class="side-panel-title">Page Settings</span>
        <button type="button" class="btn-close" onclick="toggleSidePanel('page-settings')"></button>
    </div>
    <div class="side-panel-body">
        <div class="ps-section-title">Post Metadata</div>
        <div class="hs-row"><label>Status</label>
            <select class="hs-select" name="is_active" id="ps-status" onchange="updateTopStatus(this)">
                <option value="1" {{ $page->is_active ? 'selected' : '' }}>Published</option>
                <option value="0" {{ !$page->is_active ? 'selected' : '' }}>Draft</option>
                <option value="pending">Pending Review</option>
            </select>
        </div>
        <div class="hs-row"><label>Publish Date</label>
            <input type="datetime-local" class="hs-input" name="published_at" id="ps-published-at" value="{{ $page->published_at ? $page->published_at->format('Y-m-d\TH:i') : '' }}">
        </div>
        <div class="hs-row"><label>Visibility</label>
            <select class="hs-select"><option value="public" selected>Public</option><option value="private">Private</option></select>
        </div>
        <div class="hs-row"><label>Author</label>
            <select class="hs-select"><option value="1" selected>Admin</option></select>
        </div>

        <hr class="hs-divider">
        <div class="ps-section-title">SEO & Taxonomy</div>
        <div class="hs-row"><label>Page Slug</label><input class="hs-input" name="slug" id="page-slug" value="{{ $page->slug }}" placeholder="home"></div>
        <div class="hs-row"><label>Tags (Comma separated)</label><input class="hs-input" name="tags" id="ps-tags" value="{{ is_array($page->tags) ? implode(', ', $page->tags) : '' }}" placeholder="news, update, gallery"></div>
        <div class="hs-row"><label>Meta Title</label><input class="hs-input" name="meta_title" id="ps-meta-title" value="{{ $page->meta_title }}" placeholder="Page browser title"></div>
        <div class="hs-row"><label>Meta Description</label><textarea class="hs-input" name="meta_description" id="ps-meta-description" style="min-height:80px">{{ $page->meta_description }}</textarea></div>

        <hr class="hs-divider">
        <div class="ps-section-title">Style</div>
        <div class="d-flex align-items-center justify-content-between mb-4">
            <span style="font-size:13px;font-weight:600;color:#1a1f36">Hide Page Header</span>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox"></div>
        </div>

        <hr class="hs-divider">
        <div class="ps-section-title">Featured Image</div>
        <div class="featured-image-box">
            <i class="fa-regular fa-image"></i>
            <p>Click or drag image to upload</p>
        </div>
    </div>
    <div class="side-panel-footer">
        <button type="button" class="btn btn-danger w-100 fw-bold" onclick="toggleSidePanel('page-settings')" style="background:#c0392b">Apply Settings</button>
    </div>
</div>

{{-- Page Revisions Panel (Right Off-canvas) --}}
<div class="side-panel" id="side-panel-page-revisions">
    <div class="side-panel-header">
        <span class="side-panel-title">Page Revisions</span>
        <button type="button" class="btn-close" onclick="toggleSidePanel('page-revisions')"></button>
    </div>
    <div class="side-panel-body">
        <div class="revision-item">
            <div class="revision-info">
                <div class="revision-version">5</div>
                <div>
                    <div class="revision-date">Mar 29, 2026 11:20 PM</div>
                    <div class="text-muted small">Current Version</div>
                </div>
            </div>
        </div>
        {{-- Revision History placeholders --}}
        <div class="revision-item">
            <div class="revision-info">
                <div class="revision-version">4</div>
                <div><div class="revision-date">{{ $page->updated_at->subDay()->format('M d, Y h:i A') }}</div></div>
            </div>
            <button type="button" class="btn-restore">Restore</button>
        </div>
    </div>
</div>

<input type="hidden" name="content" id="page-content-json" value="{{ $page->content }}">
<input type="hidden" name="meta_keywords" id="meta-keywords-hidden" value="{{ $page->meta_keywords }}">
<input type="hidden" name="is_featured" value="{{ $page->is_featured ? 1 : 0 }}">
</form>
</div>
@endsection
@push('pannel-scripts')
<script>
// Error Catcher
window.onerror = function(msg, url, lineNo, columnNo, error) {
    alert("Error: " + msg + "\nLine: " + lineNo + "\nFile: " + url);
    return false;
};

// Brute Force Pointer Events Fix
setInterval(() => {
    window.CMS_CONFIG = {
        upload_url: "{{ route('dealer.website.media.upload') }}",
        csrf_token: "{{ csrf_token() }}"
    };
</script>
<script src="{{ asset('assets/panels/website-pages/js/shared.js') }}?v=5.3"></script>
<script src="{{ asset('assets/panels/website-pages/js/overfuel-blocks.js') }}?v=5.3"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/panels/website-pages/js/heading.js') }}?v=5.3"></script>
<script src="{{ asset('assets/panels/website-pages/js/text.js') }}?v=5.3"></script>
<script src="{{ asset('assets/panels/website-pages/js/button.js') }}?v=5.3"></script>
<script src="{{ asset('assets/panels/website-pages/js/divider.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/image.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/video.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/accordion.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/card.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/3col.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/spacer.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/span.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/iFrame.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/2col.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/container.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/icon.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/cart.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/html-css.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/main.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/save.js') }}?v=5.4"></script>
<script src="{{ asset('assets/panels/website-pages/js/history.js') }}?v=5.4"></script>

<script>
// NUCLEAR FOCUS FIX: High-priority mousedown listener
document.addEventListener('mousedown', function(e) {
    const editable = e.target.closest('[contenteditable="true"]');
    if (editable) {
        console.log('Nuclear focus triggered for:', editable.tagName);
        setTimeout(() => {
            editable.focus();
            // Re-enable typing just in case
            editable.style.webkitUserModify = 'read-write';
        }, 0);
    }
}, true);
</script>
<script>
// Sync Top Bar Status
function updateTopStatus(select) {
    const badge = document.getElementById('top-status-badge');
    const dot = badge.querySelector('i');
    const text = select.options[select.selectedIndex].text;
    
    if (select.value === '1') {
        dot.style.color = '#27ae60';
    } else {
        dot.style.color = '#ced4da';
    }
    badge.innerHTML = `<i class="fa-solid fa-circle me-2" style="font-size:8px;color:${dot.style.color}"></i> Status: ${text}`;
}

function toggleSidePanel(id) {
    const panels = ['page-settings', 'page-revisions'];
    const overlay = document.getElementById('side-overlay');
    panels.forEach(p => {
        const el = document.getElementById('side-panel-' + p);
        if (p === id) {
            if (el.classList.contains('open')) { el.classList.remove('open'); overlay.style.display = 'none'; }
            else { panels.forEach(o => document.getElementById('side-panel-' + o).classList.remove('open')); el.classList.add('open'); overlay.style.display = 'block'; }
        } else { el.classList.remove('open'); }
    });
}
document.getElementById('side-overlay').addEventListener('click', function() {
    document.querySelectorAll('.side-panel').forEach(p => p.classList.remove('open'));
    this.style.display = 'none';
});
function prepareFormSubmit() {
    // Collect content
    var blocksContainer = document.getElementById('blocks-container');
    if (typeof collectBlocksFromContainer === 'function') {
        var contentData = collectBlocksFromContainer(blocksContainer);
        document.getElementById('page-content-json').value = JSON.stringify(contentData);
    }

    // Sync Page Settings from sidebar to hidden inputs (if any)
    var title = document.getElementById('page-title').value;
    var slugField = document.getElementById('page-slug');
    if (!slugField.value && title) {
        slugField.value = title.toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-').replace(/^-+|-+$/g, '');
    }

    return true;
}
// Slug auto-generation & sanitization
document.getElementById('page-title').addEventListener('input', function() {
    var slug = this.value.toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-').replace(/^-+|-+$/g, '');
    document.getElementById('page-slug').value = slug;
});

document.getElementById('page-slug').addEventListener('input', function() {
    this.value = this.value.toLowerCase().replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-');
});

document.getElementById('page-slug').addEventListener('blur', function() {
    this.value = this.value.replace(/^-+|-+$/g, '');
});
document.addEventListener('DOMContentLoaded', function() {
    try {
        var contentData = {!! $page->content ?: '[]' !!};
        if (window.renderExistingContent) {
            window.renderExistingContent(contentData);
        }
    } catch (e) {
        console.error("Error loading page content:", e);
    }
    
    ['vs','crs','tbs','inv','plg','frm','blg','sch','mh'].forEach(function(p) {
        var btn = document.getElementById(p + '-back-btn');
        if (btn) btn.addEventListener('click', function() {
            document.querySelectorAll('[id$="-settings-panel"]').forEach(function(el) { el.style.display = 'none'; });
            var d = document.getElementById('sidebar-default-content'); if (d) d.style.display = 'block';
        });
        var rm = document.getElementById(p + '-remove-btn');
        if (rm) rm.addEventListener('click', function() {
            if (window.activeEl) { var b = window.activeEl.closest('.dropped-block'); if (b) b.remove(); }
            document.querySelectorAll('[id$="-settings-panel"]').forEach(function(el) { el.style.display = 'none'; });
            var d = document.getElementById('sidebar-default-content'); if (d) d.style.display = 'block';
        });
    });
    var layout = document.querySelector('.layout');
    if (layout) { layout.style.display = 'flex'; layout.style.width = '100%'; layout.style.maxWidth = '100%'; layout.style.flex = '1'; layout.style.overflow = 'visible'; }

    // ── GUARANTEED TEXT EDITING FIX ──────────────────────────────────────
    document.addEventListener('click', function(e) {
        var el = e.target;
        while (el && el !== document.body) {
            if (el.isContentEditable) {
                el.focus();
                try {
                    if (document.caretRangeFromPoint) {
                        var range = document.caretRangeFromPoint(e.clientX, e.clientY);
                        if (range) { var sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(range); }
                    } else if (document.caretPositionFromPoint) {
                        var pos = document.caretPositionFromPoint(e.clientX, e.clientY);
                        if (pos) { var range = document.createRange(); range.setStart(pos.offsetNode, pos.offset); range.collapse(true); var sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(range); }
                    }
                } catch(err) {}
                break;
            }
            el = el.parentElement;
        }
    }, true);
});
</script>
@endpush
