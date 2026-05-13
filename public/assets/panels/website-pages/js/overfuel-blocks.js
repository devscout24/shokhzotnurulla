// ── Overfuel Blocks + Missing Elements ───────────────────────────────────────
// Specialized blocks for Overfuel parity (Inventory, Search, Form, etc.)


function openInventorySettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('inventory-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('inv-dealer-id').value = el.dataset.dealerId || '';
    document.getElementById('inv-condition').value = el.dataset.condition || 'all';
}

function openFormSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('form-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('fs-email').value = el.dataset.email || '';
    document.getElementById('fs-success').value = el.dataset.success || '';
}

function openSearchSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('search-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('search-placeholder').value = el.dataset.placeholder || '';
}

function openCarouselSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('carousel-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('car-interval').value = el.dataset.interval || 5000;
}

function openTabsSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('tabs-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
}

function openMapSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('map-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('map-address').value = el.dataset.address || '';
    document.getElementById('map-zoom').value = el.dataset.zoom || 14;
}

function openBlogSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('blog-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('blg-category').value = el.dataset.category || 'all';
    document.getElementById('blg-count').value = el.dataset.count || 3;
}

function openContentBlockSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('content-block-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('cb-id').value = el.dataset.blockId || '';
}

function openBodyTypesSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('body-types-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('bt-mode').value = el.dataset.mode || 'grid';
}

function openPluginSettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('plugin-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('plg-id').value = el.dataset.pluginId || '';
}

function openOverlaySettings(el) {
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    openPanel('overlay-settings-panel');
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
}

// Side Panel Buttons for Overfuel
['inv-back-btn', 'fs-back-btn', 'search-back-btn', 'car-back-btn', 'map-back-btn', 'blg-back-btn', 'cb-back-btn', 'bt-back-btn', 'plg-back-btn', 'tabs-back-btn', 'ov-back-btn'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', closeAllPanels);
});
['inv-cancel-btn', 'fs-cancel-btn', 'search-cancel-btn', 'car-cancel-btn', 'map-cancel-btn', 'blg-cancel-btn', 'cb-cancel-btn', 'bt-cancel-btn', 'plg-cancel-btn', 'tabs-cancel-btn', 'ov-cancel-btn'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', closeAllPanels);
});

// Tabs Add Item
document.getElementById('tabs-add-btn')?.addEventListener('click', () => {
    if (activeEl && activeEl.classList.contains('editor-tabs')) {
        const nav = activeEl.querySelector('.nav-tabs');
        const count = nav.children.length + 1;
        const newTab = document.createElement('div');
        newTab.className = 'nav-link';
        newTab.innerText = 'Tab ' + count;
        nav.appendChild(newTab);
        if (typeof saveHistory === 'function') saveHistory();
    }
});

// Blog Category
document.getElementById('blg-category')?.addEventListener('change', e => {
    if (activeEl) activeEl.dataset.category = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});

// Blog Count
document.getElementById('blg-count')?.addEventListener('input', e => {
    if (activeEl) activeEl.dataset.count = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});

// Body Types Mode
document.getElementById('bt-mode')?.addEventListener('change', e => {
    if (activeEl) activeEl.dataset.mode = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});

// Content Block ID
document.getElementById('cb-id')?.addEventListener('change', e => {
    if (activeEl) activeEl.dataset.blockId = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});

// Plugin ID
document.getElementById('plg-id')?.addEventListener('input', e => {
    if (activeEl) activeEl.dataset.pluginId = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});

// Inventory Settings
document.getElementById('inv-dealer-id')?.addEventListener('input', e => {
    if (activeEl) activeEl.dataset.dealerId = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});
document.getElementById('inv-condition')?.addEventListener('change', e => {
    if (activeEl) activeEl.dataset.condition = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});

// Form Settings
document.getElementById('fs-email')?.addEventListener('input', e => {
    if (activeEl) activeEl.dataset.email = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});
document.getElementById('fs-success')?.addEventListener('input', e => {
    if (activeEl) activeEl.dataset.success = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});

// Search Settings
document.getElementById('search-placeholder')?.addEventListener('input', e => {
    if (activeEl) activeEl.dataset.placeholder = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
});

// Map Settings
document.getElementById('map-title')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.title = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('map-subtitle')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.subtitle = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('map-address')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.address = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('map-zoom')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.zoom = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });

// Inventory Advanced
document.getElementById('inv-make')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.make = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('inv-model')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.model = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('inv-min-price')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.minPrice = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('inv-max-price')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.maxPrice = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('inv-max-mileage')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.maxMileage = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('inv-sort')?.addEventListener('change', e => { if (activeEl) activeEl.dataset.sort = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('inv-highlighted')?.addEventListener('change', e => { if (activeEl) activeEl.dataset.highlighted = e.target.checked; if (typeof saveHistory === 'function') saveHistory(); });

// Form Advanced
document.getElementById('fs-id')?.addEventListener('change', e => { if (activeEl) activeEl.dataset.formId = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('fs-name')?.addEventListener('input', e => { if (activeEl) activeEl.dataset.formName = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });

// Search Advanced
document.getElementById('search-size')?.addEventListener('change', e => { if (activeEl) activeEl.dataset.size = e.target.value; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('search-honda')?.addEventListener('change', e => { if (activeEl) activeEl.dataset.honda = e.target.checked; if (typeof saveHistory === 'function') saveHistory(); });
document.getElementById('search-acura')?.addEventListener('change', e => { if (activeEl) activeEl.dataset.acura = e.target.checked; if (typeof saveHistory === 'function') saveHistory(); });

// Overlay Opacity
document.getElementById('ov-opacity')?.addEventListener('input', e => {
    if (activeEl) {
        const overlay = activeEl.querySelector('div[style*="background:rgba"]');
        if (overlay) {
            const color = document.getElementById('ov-color').value || '#000000';
            const opacity = e.target.value || 0.5;
            overlay.style.background = hexToRgba(color, opacity);
            activeEl.dataset.opacity = opacity;
        }
    }
    if (typeof saveHistory === 'function') saveHistory();
});

// Overlay Color
document.getElementById('ov-color')?.addEventListener('input', e => {
    if (activeEl) {
        const overlay = activeEl.querySelector('div[style*="background:rgba"]');
        if (overlay) {
            const color = e.target.value || '#000000';
            const opacity = document.getElementById('ov-opacity').value || 0.5;
            overlay.style.background = hexToRgba(color, opacity);
            activeEl.dataset.color = color;
        }
    }
    if (typeof saveHistory === 'function') saveHistory();
});

function hexToRgba(hex, alpha) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Drop Functions for specialized blocks
function makeOFBlock(type, label, inner) {
    const b = document.createElement('div');
    b.className = 'dropped-block';
    b.dataset.blockType = label;
    b.innerHTML = `
        <div class="block-hierarchy-tools">
            <button type="button" class="hierarchy-btn select-parent-btn" title="Select Parent"><i class="fa-solid fa-arrow-up"></i></button>
            <button type="button" class="hierarchy-btn select-child-btn" title="Select Child"><i class="fa-solid fa-arrow-down"></i></button>
        </div>
        <div class="block-reorder-tools">
            <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
            <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
            <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
        </div>
        <div class="dropped-block-inner editor-${type}">${inner}</div>`;
    return b;
}

function dropInventoryBlock(ret=false){
    const inner = `
    <div class="p-4 border rounded bg-light text-center">
        <i class="fa-solid fa-car-side fs-1 text-muted mb-2"></i>
        <h5 class="m-0 fw-bold">Inventory List</h5>
        <p class="small text-muted mb-0">Filtered Inventory results will appear here.</p>
        <div class="d-flex justify-content-center gap-1 mt-3">
            <div class="p-3 bg-white border rounded flex-1 shadow-sm" style="width:60px;height:40px"></div>
            <div class="p-3 bg-white border rounded flex-1 shadow-sm" style="width:60px;height:40px"></div>
            <div class="p-3 bg-white border rounded flex-1 shadow-sm" style="width:60px;height:40px"></div>
        </div>
    </div>`;
    const b = makeOFBlock('inventory','Inventory', inner);
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}

function dropFormBlock(ret=false){
    const inner = `
    <div class="p-4 border rounded bg-white text-start">
        <div class="mb-3"><label class="small fw-bold">Name</label><div class="bg-light p-2 border rounded" style="height:35px"></div></div>
        <div class="mb-3"><label class="small fw-bold">Email</label><div class="bg-light p-2 border rounded" style="height:35px"></div></div>
        <div class="mb-0"><div class="btn btn-danger btn-sm w-100 py-2 disabled" style="background:#ce4f4b">SUBMIT</div></div>
    </div>`;
    const b = makeOFBlock('form','Form', inner);
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}

function dropSearchBlock(ret=false){
    const inner = `
    <div class="p-4 border rounded bg-white text-center">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search Inventory..." readonly>
            <button class="btn btn-danger" type="button"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
        <p class="small text-muted mb-0">Search block placeholder for live inventory search.</p>
    </div>`;
    const b = makeOFBlock('search','Search', inner);
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}

function dropMapBlock(ret=false){
    const inner = `
    <div class="p-5 border rounded bg-light text-center position-relative" style="min-height:200px; background-image: radial-gradient(#dee2e6 1px, transparent 1px); background-size: 20px 20px;">
        <i class="fa-solid fa-location-dot fs-1 text-danger mb-2"></i>
        <h5 class="m-0 fw-bold">Location Map</h5>
        <p class="small text-muted">Map & Hours for your locations</p>
    </div>`;
    const b = makeOFBlock('map','Map', inner);
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropMapHoursBlock(ret=false){
    const inner = `
    <div class="d-flex border rounded bg-light overflow-hidden">
        <div class="flex-grow-1 p-5 text-center bg-white border-end">
            <i class="fa-solid fa-map-location-dot fs-1 text-danger mb-2"></i>
            <h6 class="fw-bold m-0">Location Map</h6>
        </div>
        <div class="p-3 bg-light" style="width:200px">
            <label class="small fw-bold border-bottom d-block mb-2 pb-1">BUSINESS HOURS</label>
            <div class="small text-muted" style="line-height:1.4">
                Mon-Fri: 9am - 7pm<br>
                Sat: 10am - 5pm<br>
                Sun: Closed
            </div>
        </div>
    </div>`;
    const b = makeOFBlock('map_hours','Map / Hours', inner);
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropBlogBlock(ret=false){
    const inner = `
    <div class="p-4 border rounded bg-white">
        <h6 class="fw-bold mb-3 border-bottom pb-2">LATEST BLOG POSTS</h6>
        <div class="row g-3">
            <div class="col-4"><div class="bg-light rounded" style="height:120px"></div></div>
            <div class="col-4"><div class="bg-light rounded" style="height:120px"></div></div>
            <div class="col-4"><div class="bg-light rounded" style="height:120px"></div></div>
        </div>
    </div>`;
    const b = makeOFBlock('blog','Blog Posts', inner);
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropContentBlockBlock(ret=false){
    const b = makeOFBlock('content_block','Content Block', '<div class="p-4 border border-2 border-dashed rounded text-center text-muted"><i class="fa-solid fa-cubes fs-2 mb-2"></i><br><strong>Reusable Content Block</strong><br><small>Select a shared block from settings</small></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropBodyTypesBlock(ret=false){
    const inner = `
    <div class="p-4 border rounded bg-white text-center">
        <div class="d-flex justify-content-center gap-4">
            <div class="text-center"><div class="bg-light rounded-circle mb-2" style="width:60px;height:60px"></div><small class="fw-bold">SUV</small></div>
            <div class="text-center"><div class="bg-light rounded-circle mb-2" style="width:60px;height:60px"></div><small class="fw-bold">SEDAN</small></div>
            <div class="text-center"><div class="bg-light rounded-circle mb-2" style="width:60px;height:60px"></div><small class="fw-bold">TRUCK</small></div>
        </div>
    </div>`;
    const b = makeOFBlock('body_types','Body Types', inner);
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropPluginBlock(ret=false){
    const b = makeOFBlock('plugin','Plugin', '<div style="padding:20px;background:#fffbe6;border:1px solid #ffe58f;text-align:center">Plugin Placeholder</div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropCarouselBlock(ret=false){
    const b = makeOFBlock('carousel','Carousel', '<div style="background:#333;height:200px;display:flex;align-items:center;justify-content:center;color:#fff;border-radius:4px"><i class="fa-solid fa-images" style="font-size:3rem"></i></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropTabsBlock(ret=false){
    const b = makeOFBlock('tabs','Tabs', '<div class="nav nav-tabs mb-2"><div class="nav-link active">Tab 1</div><div class="nav-link">Tab 2</div></div><div style="padding:15px;border:1px solid #dee2e6;border-top:0" class="col-drop-zone"></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropOverlayBlock(ret=false){
    const b = makeOFBlock('overlay','Overlay', '<div style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1;pointer-events:none"></div><div style="position:relative;z-index:2;padding:40px" class="col-drop-zone"></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropCheckBlock(ret=false){
    const b = makeOFBlock('check','Check', '<div class="form-check"><input class="form-check-input" type="checkbox" checked><label class="form-check-label">Check Item</label></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropModalBlock(ret=false){
    const b = makeOFBlock('modal','Modal', '<div style="padding:20px;border:1px solid #eee;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1)"><strong>Modal Content</strong><div class="col-drop-zone" style="min-height:50px;margin-top:10px"></div></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}

// Export Globals
window.dropInventoryBlock=dropInventoryBlock;
window.dropSearchBlock=dropSearchBlock;
window.dropFormBlock=dropFormBlock;
window.dropMapBlock=dropMapBlock;
window.dropMapHoursBlock=dropMapHoursBlock;
window.dropBlogBlock=dropBlogBlock;
window.dropContentBlockBlock=dropContentBlockBlock;
window.dropBodyTypesBlock=dropBodyTypesBlock;
window.dropPluginBlock=dropPluginBlock;
window.dropCarouselBlock=dropCarouselBlock;
window.dropTabsBlock=dropTabsBlock;
window.dropOverlayBlock=dropOverlayBlock;
window.dropCheckBlock=dropCheckBlock;
window.dropModalBlock=dropModalBlock;

window.openInventorySettings=openInventorySettings;
window.openFormSettings=openFormSettings;
window.openSearchSettings=openSearchSettings;
window.openCarouselSettings=openCarouselSettings;
window.openTabsSettings=openTabsSettings;
window.openMapSettings=openMapSettings;
window.openBlogSettings=openBlogSettings;
window.openContentBlockSettings=openContentBlockSettings;
window.openBodyTypesSettings=openBodyTypesSettings;
window.openPluginSettings=openPluginSettings;
window.openOverlaySettings=openOverlaySettings;
