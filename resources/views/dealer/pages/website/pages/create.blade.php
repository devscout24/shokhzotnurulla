@extends('layouts.dealer.app')
@section('title', __('Add Page'))
@push('page-assets')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link rel="stylesheet" href="{{ asset('assets/panels/website-pages/css/editor.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/panels/website-pages/css/heading.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/panels/website-pages/css/p.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/panels/website-pages/css/button.css') }}"/>
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
.block-item.dragging{opacity:.5;border:2px dashed #c0392b!important}
.canvas-left{flex-grow:1;overflow:auto;background:#f8f9fa;padding:40px}
#drop-indicator{height:4px;background:#c0392b;margin:10px 0;border-radius:2px}
.hs-row{margin-bottom:22px}
.hs-row label{display:block;font-size:11px;font-weight:800;text-transform:uppercase;color:#4f566b;margin-bottom:8px;letter-spacing:.8px}
.hs-input,.hs-select{width:100%;padding:10px 12px;border:1px solid #e0e6ed;border-radius:8px;font-size:14px;color:#1a1f36;transition:all .2s;background:#fff}
.hs-input:focus,.hs-select:focus{border-color:#c0392b;outline:none;box-shadow:0 0 0 3px rgba(192,57,43,.1)}
.hs-divider{border:0;border-top:1px solid #f1f3f5;margin:20px 0}
.hs-back-btn{background:none;border:none;font-weight:800;color:#1a1f36;margin-bottom:25px;display:flex;align-items:center;gap:10px;padding:0;font-size:16px}
.hs-actions{display:flex;gap:10px;margin-top:20px}
.hs-btn-remove{background:#fff5f5;color:#c0392b;border:1px solid #ffe3e3;padding:10px 15px;border-radius:8px;font-weight:700;font-size:13px;display:flex;align-items:center;gap:8px;flex:1;justify-content:center;cursor:pointer}
.hs-btn-remove:hover{background:#c0392b;color:#fff}
.hs-btn-cancel{background:#f8f9fa;color:#4f566b;border:1px solid #e0e6ed;padding:10px 15px;border-radius:8px;font-weight:700;font-size:13px;flex:1;cursor:pointer}
[id$="-settings-panel"]{display:none}
</style>
@endpush
@section('page-content')
<div class="of-master-frame">
<form action="{{ $routes['store'] }}" method="POST" id="page-builder-form" style="display:contents">
@csrf
<div class="of-header p-4 d-flex align-items-center bg-white border-bottom shadow-sm">
<h4 class="fw-bold m-0">Add Page</h4>
<div class="ms-auto d-flex align-items-center gap-2">
<a href="#" class="btn btn-outline-secondary btn-sm px-4 fw-bold" id="btn-preview"><i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Preview</a>
<button type="button" class="btn btn-outline-secondary btn-sm px-4 fw-bold"><i class="fa-solid fa-gear me-2"></i>Page Settings</button>
<button type="button" class="btn btn-outline-secondary btn-sm px-4 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Page Revisions</button>
<button type="submit" class="btn btn-danger btn-sm px-5 fw-bold" style="background:#c0392b"><i class="fa-solid fa-check me-2"></i>Save</button>
</div>
</div>
<div class="input-group border-bottom bg-white" style="height:55px">
<span class="bg-lighter input-group-text border-0 px-4 fw-bold small text-muted" style="min-width:140px">PAGE TITLE</span>
<input type="text" value="The name of our country" class="form-control border-0 px-4 fs-6" name="title" id="page-title" required autocomplete="off">
<span class="bg-white input-group-text border-0 text-muted small px-4 border-start"><i class="fa-solid fa-circle me-2" style="font-size:8px;color:#ced4da"></i> Status: Draft</span>
<input type="hidden" name="slug" id="page-slug">
</div>
<div class="d-flex flex-grow-1 overflow-hidden">
<div class="canvas-left flex-grow-1 overflow-auto">
<div class="bg-white border rounded shadow-sm mx-auto my-4" style="max-width:1000px;min-height:1000px">
<div class="card border-0">
<div class="fw-bold bg-lighter card-header d-flex justify-content-between align-items-center py-3">
<span class="small text-dark">Content Editor</span>
<div class="d-flex align-items-center gap-2">
<button type="button" class="btn btn-link btn-sm text-dark text-decoration-none fw-bold small"><i class="fa-solid fa-arrow-down-to-line me-1"></i>Export</button>
<button type="button" class="btn btn-link btn-sm text-dark text-decoration-none fw-bold small"><i class="fa-solid fa-arrow-up-to-line me-1"></i>Import</button>
<div class="btn-group bg-white border ms-2">
<button type="button" class="btn btn-light btn-sm border-end" id="btn-undo" disabled><i class="fa-solid fa-rotate-left"></i> Undo</button>
<button type="button" class="btn btn-light btn-sm" id="btn-redo"><i class="fa-solid fa-rotate-right text-primary"></i> Redo</button>
</div>
</div>
</div>
<div class="p-4">
<div id="content-editor-zone">
<div class="editor-empty-state" id="empty-state"><span class="editor-empty-badge">Document</span><input class="editor-empty-input" placeholder="" readonly/></div>
<div id="blocks-container" style="min-height:800px"></div>
</div>
</div>
</div>
</div>
</div>
<div class="sidebar-right">
{{-- All existing panels from JS files --}}
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
<div class="block-item" draggable="true" data-type="card"><i class="fa-solid fa-id-card"></i><span>Card</span></div>
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
<input type="hidden" name="content" id="page-content-json">
</form>
</div>
@endsection
@push('pannel-scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/panels/website-pages/js/shared.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/history.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/heading.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/text.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/button.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/divider.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/image.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/accordion.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/card.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/3col.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/spacer.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/overfuel-blocks.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/main.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/save.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/span.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/iFrame.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/2col.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/container.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/icon.js') }}"></script>
<script src="{{ asset('assets/panels/website-pages/js/cart.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
// Back buttons for new panels
['vs','crs','tbs','inv','plg','frm','blg','sch','mh'].forEach(function(p){
var btn=document.getElementById(p+'-back-btn');
if(btn)btn.addEventListener('click',function(){
document.querySelectorAll('[id$="-settings-panel"]').forEach(function(el){el.style.display='none';});
var d=document.getElementById('sidebar-default-content');if(d)d.style.display='block';
});
var rm=document.getElementById(p+'-remove-btn');
if(rm)rm.addEventListener('click',function(){
if(window.activeEl){var b=window.activeEl.closest('.dropped-block');if(b)b.remove();}
document.querySelectorAll('[id$="-settings-panel"]').forEach(function(el){el.style.display='none';});
var d=document.getElementById('sidebar-default-content');if(d)d.style.display='block';
if(typeof saveHistory==='function')saveHistory();
});
});
// openVideoSettings
window.openVideoSettings=function(el){
window.activeEl=el;
document.getElementById('sidebar-default-content').style.display='none';
document.querySelectorAll('[id$="-settings-panel"]').forEach(function(p){p.style.display='none';});
document.getElementById('video-settings-panel').style.display='block';
};
window.openCarouselSettings=function(el){
window.activeEl=el;
document.getElementById('sidebar-default-content').style.display='none';
document.querySelectorAll('[id$="-settings-panel"]').forEach(function(p){p.style.display='none';});
document.getElementById('carousel-settings-panel').style.display='block';
};
window.openTabsSettings=function(el){
window.activeEl=el;
document.getElementById('sidebar-default-content').style.display='none';
document.querySelectorAll('[id$="-settings-panel"]').forEach(function(p){p.style.display='none';});
document.getElementById('tabs-settings-panel').style.display='block';
};
window.openInventorySettings=function(el){
window.activeEl=el;
document.getElementById('sidebar-default-content').style.display='none';
document.querySelectorAll('[id$="-settings-panel"]').forEach(function(p){p.style.display='none';});
document.getElementById('inventory-settings-panel').style.display='block';
};
// Fix layout
var layout=document.querySelector('.layout');
if(layout){layout.style.display='flex';layout.style.width='100%';layout.style.maxWidth='100%';layout.style.flex='1';layout.style.overflow='hidden';}
});
</script>
@endpush
