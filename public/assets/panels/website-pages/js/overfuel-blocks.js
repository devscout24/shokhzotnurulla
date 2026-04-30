// ── Overfuel Blocks + Missing Elements ───────────────────────────────────────
// Each block uses editor-container class so openContainerSettings works (like reference site)

function makeOFBlock(type, label, innerHtml) {
    const b = document.createElement('div');
    b.className = 'dropped-block';
    b.innerHTML = `
        <span class="dropped-block-badge">${label} <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i></span>
        <div class="block-reorder-tools">
            <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
            <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
            <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
        </div>
        <div class="dropped-block-inner" style="background:transparent;border:none;padding:0">
            <div class="editor-container editor-${type}" style="min-height:80px;padding:20px;background:#fff;width:100%">
                ${innerHtml}
            </div>
        </div>`;
    // Click opens container settings (same as reference site)
    const el = b.querySelector('.editor-container');
    if (el) {
        el.addEventListener('click', e => {
            e.stopPropagation();
            if (typeof openContainerSettings === 'function') openContainerSettings(el);
        });
    }
    return b;
}

function openHTMLSettings(el) {
    closeAllPanels();
    activeEl = el;
    el.closest('.dropped-block').classList.add('selected');
    document.getElementById('html-settings-panel').style.display = 'block';
    document.getElementById('html-code').value = el.dataset.code || '';
}

function openCSSSettings(el) {
    closeAllPanels();
    activeEl = el;
    el.closest('.dropped-block').classList.add('selected');
    document.getElementById('css-settings-panel').style.display = 'block';
    document.getElementById('css-code').value = el.dataset.code || '';
}

// Side Panel Listeners for HTML/CSS
document.getElementById('html-code')?.addEventListener('input', e => {
    if (activeEl) {
        activeEl.dataset.code = e.target.value;
        activeEl.innerHTML = `<div style="padding:10px;background:#f8f9fa;border-radius:4px;font-family:monospace;font-size:13px;color:#555">&lt;!-- Custom HTML --&gt;</div>`;
    }
});
document.getElementById('css-code')?.addEventListener('input', e => {
    if (activeEl) activeEl.dataset.code = e.target.value;
});
document.getElementById('html-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('css-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('html-remove-btn')?.addEventListener('click', () => { if(activeEl) activeEl.closest('.dropped-block').remove(); closeAllPanels(); });
document.getElementById('css-remove-btn')?.addEventListener('click', () => { if(activeEl) activeEl.closest('.dropped-block').remove(); closeAllPanels(); });

// ── Layout blocks ─────────────────────────────────────────────────────────────
function dropOverlayBlock(ret=false){
    const b = makeOFBlock('overlay','Overlay',
        '<div style="min-height:100px;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;color:#fff;border-radius:4px"><span>Overlay Layer</span></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropHTMLBlock(ret=false){
    const b = makeOFBlock('html','HTML','<div class="html-preview" style="padding:10px;background:#f8f9fa;border-radius:4px;font-family:monospace;font-size:13px;color:#555">&lt;!-- Custom HTML --&gt;</div>');
    const inner = b.querySelector('.editor-html');
    inner.addEventListener('click', (e) => { e.stopPropagation(); openHTMLSettings(inner); });
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openHTMLSettings(inner);
}
function dropCSSBlock(ret=false){
    const b = makeOFBlock('css','CSS','<div class="css-preview" style="padding:10px;background:#f8f9fa;border-radius:4px;font-family:monospace;font-size:13px;color:#555">/* Custom CSS */</div>');
    const inner = b.querySelector('.editor-css');
    inner.addEventListener('click', (e) => { e.stopPropagation(); openCSSSettings(inner); });
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openCSSSettings(inner);
}

// ── Element blocks ────────────────────────────────────────────────────────────
function dropVideoBlock(ret=false){
    const b = makeOFBlock('video','Video',
        '<div style="background:#000;min-height:200px;display:flex;align-items:center;justify-content:center;border-radius:4px"><i class="fa-solid fa-play-circle" style="font-size:4rem;color:rgba(255,255,255,0.5)"></i></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropCarouselBlock(ret=false){
    const b = makeOFBlock('carousel','Carousel',
        '<div style="background:#f1f3f5;min-height:180px;display:flex;align-items:center;justify-content:center;border-radius:4px;flex-direction:column;gap:8px"><i class="fa-solid fa-images" style="font-size:2.5rem;color:#adb5bd"></i><span style="color:#adb5bd;font-size:13px">Image Carousel</span></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropTabsBlock(ret=false){
    const b = makeOFBlock('tabs','Tabs',
        '<ul class="nav nav-tabs mb-0" style="border-bottom:1px solid #dee2e6"><li class="nav-item"><a class="nav-link active" href="#">Tab 1</a></li><li class="nav-item"><a class="nav-link" href="#">Tab 2</a></li><li class="nav-item"><a class="nav-link" href="#">Tab 3</a></li></ul><div style="padding:15px;border:1px solid #dee2e6;border-top:none;border-radius:0 0 4px 4px;background:#fff">Tab content area</div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropCheckBlock(ret=false){
    const b = makeOFBlock('check','Check',
        '<div style="display:flex;align-items:center;gap:10px"><i class="fa-solid fa-square-check" style="color:#28a745;font-size:1.2rem"></i><span contenteditable="true" style="font-size:15px">Checkbox label text</span></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropMapBlock(ret=false){
    const b = makeOFBlock('map','Map',
        '<div style="background:#e9ecef;min-height:200px;display:flex;align-items:center;justify-content:center;border-radius:4px;flex-direction:column;gap:10px"><i class="fa-solid fa-map-location-dot" style="font-size:3rem;color:#dc3545"></i><span style="color:#6c757d;font-size:13px">Google Map Placeholder</span></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropModalBlock(ret=false){
    const b = makeOFBlock('modal','Modal',
        '<div style="background:#f1f3f5;padding:20px;border-radius:4px;text-align:center"><i class="fa-solid fa-window-restore" style="font-size:2rem;color:#adb5bd;display:block;margin-bottom:8px"></i><span style="color:#6c757d;font-size:13px">Modal Block — Click to configure</span></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}

// ── Overfuel blocks ───────────────────────────────────────────────────────────
function dropInventoryBlock(ret=false){
    const b = makeOFBlock('inventory','Inventory',
        '<div style="text-align:center;padding:30px;background:#f8f9fa;border-radius:4px"><i class="fa-solid fa-car-side" style="font-size:3rem;color:#adb5bd;display:block;margin-bottom:10px"></i><strong>Inventory List</strong><p style="color:#6c757d;font-size:13px;margin:5px 0 0">Vehicles will display here on the live site</p></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropSearchBlock(ret=false){
    const b = makeOFBlock('search','Search Input',
        '<div class="input-group"><input type="text" class="form-control" placeholder="Search Inventory..." readonly><button class="btn btn-danger"><i class="fa-solid fa-magnifying-glass"></i></button></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropFormBlock(ret=false){
    const b = makeOFBlock('form','Form',
        '<div style="padding:10px"><div class="mb-2"><input type="text" class="form-control form-control-sm" placeholder="Name" readonly></div><div class="mb-2"><input type="email" class="form-control form-control-sm" placeholder="Email" readonly></div><div class="mb-2"><textarea class="form-control form-control-sm" rows="2" placeholder="Message" readonly></textarea></div><button class="btn btn-danger btn-sm w-100">Submit</button></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropBlogBlock(ret=false){
    const b = makeOFBlock('blog','Blog Posts',
        '<div class="row g-2"><div class="col-4"><div style="background:#dee2e6;height:80px;border-radius:4px;margin-bottom:6px"></div><div style="font-size:12px;font-weight:600;color:#333">Blog Post Title</div><div style="font-size:11px;color:#6c757d">Jan 1, 2024</div></div><div class="col-4"><div style="background:#dee2e6;height:80px;border-radius:4px;margin-bottom:6px"></div><div style="font-size:12px;font-weight:600;color:#333">Blog Post Title</div><div style="font-size:11px;color:#6c757d">Jan 1, 2024</div></div><div class="col-4"><div style="background:#dee2e6;height:80px;border-radius:4px;margin-bottom:6px"></div><div style="font-size:12px;font-weight:600;color:#333">Blog Post Title</div><div style="font-size:11px;color:#6c757d">Jan 1, 2024</div></div></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropContentBlockBlock(ret=false){
    const b = makeOFBlock('content_block','Content Block',
        '<div style="text-align:center;padding:30px;background:#f8f9fa;border-radius:4px"><i class="fa-solid fa-cubes" style="font-size:2.5rem;color:#adb5bd;display:block;margin-bottom:8px"></i><span style="color:#6c757d;font-size:13px">Reusable Content Block</span></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropBodyTypesBlock(ret=false){
    const b = makeOFBlock('body_types','Body Types',
        '<div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:center;padding:10px"><span style="border:1px solid #dee2e6;padding:8px 14px;border-radius:20px;font-size:12px;font-weight:600">SUV</span><span style="border:1px solid #dee2e6;padding:8px 14px;border-radius:20px;font-size:12px;font-weight:600">Sedan</span><span style="border:1px solid #dee2e6;padding:8px 14px;border-radius:20px;font-size:12px;font-weight:600">Truck</span><span style="border:1px solid #dee2e6;padding:8px 14px;border-radius:20px;font-size:12px;font-weight:600">Coupe</span><span style="border:1px solid #dee2e6;padding:8px 14px;border-radius:20px;font-size:12px;font-weight:600">Van</span></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropMapHoursBlock(ret=false){
    const b = makeOFBlock('map_hours','Map / Hours',
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0"><div style="background:#e9ecef;min-height:200px;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-map-location-dot" style="font-size:2.5rem;color:#dc3545"></i></div><div style="padding:20px;background:#fff;border:1px solid #dee2e6"><strong style="display:block;margin-bottom:10px;font-size:14px">Business Hours</strong><div style="font-size:13px;color:#333;line-height:1.8">Mon–Fri: 9am–6pm<br>Saturday: 10am–4pm<br>Sunday: Closed</div></div></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}
function dropPluginBlock(ret=false){
    const b = makeOFBlock('plugin','Plugin',
        '<div style="text-align:center;padding:30px;background:#f8f9fa;border-radius:4px"><i class="fa-solid fa-plug" style="font-size:2.5rem;color:#adb5bd;display:block;margin-bottom:8px"></i><span style="color:#6c757d;font-size:13px">External Plugin / Widget</span></div>');
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openContainerSettings(b.querySelector('.editor-container'));
}

// Expose globally
window.dropOverlayBlock=dropOverlayBlock; window.dropHTMLBlock=dropHTMLBlock;
window.dropCSSBlock=dropCSSBlock; window.dropVideoBlock=dropVideoBlock;
window.dropCarouselBlock=dropCarouselBlock; window.dropTabsBlock=dropTabsBlock;
window.dropCheckBlock=dropCheckBlock; window.dropMapBlock=dropMapBlock;
window.dropModalBlock=dropModalBlock; window.dropInventoryBlock=dropInventoryBlock;
window.dropSearchBlock=dropSearchBlock; window.dropFormBlock=dropFormBlock;
window.dropBlogBlock=dropBlogBlock; window.dropContentBlockBlock=dropContentBlockBlock;
window.dropBodyTypesBlock=dropBodyTypesBlock; window.dropMapHoursBlock=dropMapHoursBlock;
window.dropPluginBlock=dropPluginBlock;
