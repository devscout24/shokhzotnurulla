// ── Overfuel Blocks + Missing Elements ───────────────────────────────────────
// Specialized blocks for Overfuel parity (Inventory, Search, Form, etc.)

function makeOFBlock(type, label, innerHtml) {
    const b = document.createElement('div');
    b.className = 'dropped-block';
    b.innerHTML = `
        <span class="dropped-block-badge">${label} <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i></span>
        <div class="block-reorder-tools">
            <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
            <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
            <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
        </div>
        <div class="dropped-block-inner" style="background:transparent;border:none;padding:0">
            <div class="editor-container editor-${type}" style="min-height:80px;padding:20px;background:#fff;width:100%">
                ${innerHtml}
            </div>
        </div>`;
    return b;
}

function openInventorySettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    document.getElementById('inventory-settings-panel').style.display = 'block';
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('inv-dealer-id').value = el.dataset.dealerId || '';
    document.getElementById('inv-condition').value = el.dataset.condition || 'all';
}

function openFormSettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    document.getElementById('form-settings-panel').style.display = 'block';
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('fs-email').value = el.dataset.email || '';
}

function openSearchSettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    document.getElementById('search-settings-panel').style.display = 'block';
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('search-placeholder').value = el.dataset.placeholder || '';
}

function openCarouselSettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    document.getElementById('carousel-settings-panel').style.display = 'block';
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('car-interval').value = el.dataset.interval || 5000;
}

function openTabsSettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    document.getElementById('tabs-settings-panel').style.display = 'block';
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
}

function openMapSettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    document.getElementById('map-settings-panel').style.display = 'block';
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('map-address').value = el.dataset.address || '';
    document.getElementById('map-zoom').value = el.dataset.zoom || 14;
}

// Side Panel Buttons for Overfuel
['inv-back-btn', 'fs-back-btn', 'search-back-btn', 'car-back-btn', 'map-back-btn'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', closeAllPanels);
});
['inv-cancel-btn', 'fs-cancel-btn', 'search-cancel-btn', 'car-cancel-btn', 'map-cancel-btn'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', closeAllPanels);
});

// Drop Functions for specialized blocks
function dropInventoryBlock(ret=false){
    const b = makeOFBlock('inventory','Inventory', '<div style="text-align:center;padding:30px;background:#f8f9fa;border-radius:4px"><i class="fa-solid fa-car-side" style="font-size:3rem;color:#adb5bd;display:block;margin-bottom:10px"></i><strong>Inventory List</strong></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropSearchBlock(ret=false){
    const b = makeOFBlock('search','Search', '<div class="input-group"><input type="text" class="form-control" placeholder="Search Inventory..." readonly><button class="btn btn-danger"><i class="fa-solid fa-magnifying-glass"></i></button></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropFormBlock(ret=false){
    const b = makeOFBlock('form','Form', '<div style="padding:10px;background:#f8f9fa;border-radius:4px"><div class="mb-2"><input type="text" class="form-control form-control-sm" placeholder="Name" readonly></div><button class="btn btn-danger btn-sm w-100">Submit</button></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}
function dropMapBlock(ret=false){
    const b = makeOFBlock('map','Map', '<div style="background:#e9ecef;min-height:150px;display:flex;align-items:center;justify-content:center;border-radius:4px"><i class="fa-solid fa-map-location-dot" style="font-size:3rem;color:#dc3545"></i></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b);
}

// Export Globals
window.dropInventoryBlock=dropInventoryBlock;
window.dropSearchBlock=dropSearchBlock;
window.dropFormBlock=dropFormBlock;
window.dropMapBlock=dropMapBlock;
window.openInventorySettings=openInventorySettings;
window.openFormSettings=openFormSettings;
window.openSearchSettings=openSearchSettings;
window.openCarouselSettings=openCarouselSettings;
window.openTabsSettings=openTabsSettings;
window.openMapSettings=openMapSettings;
