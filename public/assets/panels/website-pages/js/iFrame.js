// ── IFrame Settings Panel ─────────────────────────────────────────────────────

function openIFrameSettings(el) {
    closeAllPanels();
    activeEl = el;
    el.closest('.dropped-block').classList.add('selected');
    const panel = document.getElementById('iframe-settings-panel');
    if (panel) panel.style.display = 'block';

    // Sync inputs
    const iframe = el.querySelector('iframe');
    const placeholder = el.querySelector('.iframe-placeholder');

    document.getElementById('iframe-url').value = (iframe && iframe.src) ? iframe.src : '';
    document.getElementById('iframe-title').value = (iframe && iframe.title) ? iframe.title : '';
    document.getElementById('iframe-height').value = (iframe && iframe.height) ? parseInt(iframe.height) : (placeholder ? parseInt(placeholder.style.height) : 300);
}

// Back / Cancel
document.getElementById('iframe-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('iframe-cancel-btn')?.addEventListener('click', closeAllPanels);

// URL Change
document.getElementById('iframe-url')?.addEventListener('input', e => {
    if (!activeEl) return;
    const url = e.target.value.trim();
    const inner = activeEl;

    if (url) {
        let iframe = inner.querySelector('iframe');
        if (!iframe) {
            inner.innerHTML = ''; 
            iframe = document.createElement('iframe');
            iframe.style.width = '100%';
            iframe.style.border = 'none';
            iframe.height = document.getElementById('iframe-height').value || 300;
            inner.appendChild(iframe);
        }
        iframe.src = url;
        iframe.title = document.getElementById('iframe-title').value || '';
    } else {
        // Show placeholder if empty
        renderIFramePlaceholder(inner);
    }
});

// Title Change
document.getElementById('iframe-title')?.addEventListener('input', e => {
    if (!activeEl) return;
    const iframe = activeEl.querySelector('iframe');
    if (iframe) iframe.title = e.target.value;
});

// Height Change
document.getElementById('iframe-height')?.addEventListener('input', e => {
    if (!activeEl) return;
    const val = e.target.value || 300;
    const iframe = activeEl.querySelector('iframe');
    if (iframe) iframe.height = val;
    const placeholder = activeEl.querySelector('.iframe-placeholder');
    if (placeholder) placeholder.style.height = val + 'px';
});

// Remove
document.getElementById('iframe-remove-btn')?.addEventListener('click', () => {
    if (activeEl) {
        activeEl.closest('.dropped-block').remove();
        checkEmptyBlocks();
    }
    closeAllPanels();
});

function renderIFramePlaceholder(container) {
    const height = document.getElementById('iframe-height').value || 300;
    container.innerHTML = `
    <div class="iframe-placeholder" style="width: 100%; height: ${height}px; background: #f8f9fa; border: 1px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 14px;">
      <div class="text-center">
        <i class="fa-solid fa-window-maximize mb-2" style="font-size: 24px; opacity: 0.5;"></i>
        <div>Enter an iFrame URL</div>
      </div>
    </div>
  `;
}



function dropIFrameBlock(returnBlock = false) {
    const emptyState = document.getElementById('empty-state');
    const blocksContainer = document.getElementById('blocks-container');
    if (emptyState) emptyState.style.display = 'none';

    const block = document.createElement('div');
    block.className = 'dropped-block';
    block.innerHTML = `
    <span class="dropped-block-badge">
      iFrame <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner editor-iframe">
      <div class="iframe-placeholder" style="width: 100%; height: 300px; background: #f8f9fa; border: 1px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 14px;">
        <div class="text-center">
          <i class="fa-solid fa-window-maximize mb-2" style="font-size: 24px; opacity: 0.5;"></i>
          <div>Enter an iFrame URL</div>
        </div>
      </div>
    </div>`;

    if (returnBlock) return block;
    blocksContainer.appendChild(block);
    attachBlockListeners(block);

    const inner = block.querySelector('.editor-iframe');
    if (inner) openIFrameSettings(inner);
}
