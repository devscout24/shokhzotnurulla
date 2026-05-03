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
        activeEl.innerHTML = `<div style="padding:10px;background:#f8f9fa;border-radius:4px;font-family:monospace;font-size:13px;color:#555">&lt;!-- Custom HTML --&gt;</div>`;
    }
});
document.getElementById('css-code')?.addEventListener('input', e => {
    if (activeEl) activeEl.dataset.code = e.target.value;
});

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

window.openHTMLSettings = openHTMLSettings;
window.openCSSSettings = openCSSSettings;
window.dropHTMLBlock = dropHTMLBlock;
window.dropCSSBlock = dropCSSBlock;
