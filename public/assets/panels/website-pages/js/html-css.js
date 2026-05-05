function openHTMLSettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    document.getElementById('html-settings-panel').style.display = 'block';
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('html-code').value = el.dataset.code || '';
}

function openCSSSettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    document.getElementById('css-settings-panel').style.display = 'block';
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
    document.getElementById('css-code').value = el.dataset.code || '';
}

// Side Panel Listeners for HTML/CSS
document.getElementById('html-code')?.addEventListener('input', e => {
    if (activeEl) {
        activeEl.dataset.code = e.target.value;
        // Render HTML code safely
        try {
            activeEl.innerHTML = e.target.value || '<div style="padding:10px;background:#f8f9fa;border-radius:4px;font-family:monospace;font-size:13px;color:#999">&lt;!-- Enter HTML code here --&gt;</div>';
        } catch(err) {
            activeEl.innerHTML = '<div style="padding:10px;background:#fff5f5;border-radius:4px;font-family:monospace;font-size:13px;color:#c0392b">❌ Invalid HTML</div>';
        }
        if (typeof saveHistory === 'function') saveHistory();
    }
});
document.getElementById('css-code')?.addEventListener('input', e => {
    if (activeEl) {
        activeEl.dataset.code = e.target.value;
        // Apply CSS code to a style tag or the element itself
        let styleId = 'html-css-' + Math.random().toString(36).substr(2, 9);
        let existingStyle = document.getElementById(styleId);
        if (existingStyle) existingStyle.remove();
        
        if (e.target.value) {
            const style = document.createElement('style');
            style.id = styleId;
            // Add scope to CSS rules to target elements inside this block
            style.textContent = e.target.value;
            document.head.appendChild(style);
            activeEl.dataset.styleId = styleId;
        }
        if (typeof saveHistory === 'function') saveHistory();
    }
});

function dropHTMLBlock(ret=false){
    const b = makeOFBlock('html','HTML','<div class="html-preview" style="padding:15px;background:#f0f9ff;border-radius:4px;border:2px dashed #0ea5e9;font-family:monospace;font-size:12px;color:#0369a1;text-align:center"><i class="fa-solid fa-code" style="display:block;font-size:20px;margin-bottom:8px"></i>Click to add HTML code</div>');
    const inner = b.querySelector('.editor-html');
    inner.addEventListener('click', (e) => { e.stopPropagation(); openHTMLSettings(inner); });
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openHTMLSettings(inner);
}
function dropCSSBlock(ret=false){
    const b = makeOFBlock('css','CSS','<div class="css-preview" style="padding:15px;background:#fef5f3;border-radius:4px;border:2px dashed #f97316;font-family:monospace;font-size:12px;color:#c2410c;text-align:center"><i class="fa-solid fa-palette" style="display:block;font-size:20px;margin-bottom:8px"></i>Click to add CSS code</div>');
    const inner = b.querySelector('.editor-css');
    inner.addEventListener('click', (e) => { e.stopPropagation(); openCSSSettings(inner); });
    if(ret)return b; document.getElementById('blocks-container').appendChild(b); attachBlockListeners(b); openCSSSettings(inner);
}

window.openHTMLSettings = openHTMLSettings;
window.openCSSSettings = openCSSSettings;
window.dropHTMLBlock = dropHTMLBlock;
window.dropCSSBlock = dropCSSBlock;
