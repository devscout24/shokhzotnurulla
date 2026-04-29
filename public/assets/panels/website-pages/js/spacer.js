// ── Spacer Settings Panel ───────────────────────────────────────────────────

function openSpacerSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  document.getElementById('spacer-settings-panel').style.display = 'block';

  // Sync fields
  document.getElementById('ss-display').value = el.dataset.display || 'all';
  document.getElementById('ss-height-desktop').value = el.dataset.heightDesktop || '10';
  document.getElementById('ss-height-mobile').value = el.dataset.heightMobile || '10';
}

// Back / Cancel
document.getElementById('ss-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('ss-cancel-btn').addEventListener('click', closeAllPanels);

// Display
document.getElementById('ss-display').addEventListener('change', e => {
  if (activeEl) {
    activeEl.dataset.display = e.target.value;
    updateSpacerStyles(activeEl);
  }
});

// Height Desktop
document.getElementById('ss-height-desktop').addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.heightDesktop = e.target.value;
    updateSpacerStyles(activeEl);
  }
});

// Height Mobile
document.getElementById('ss-height-mobile').addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.heightMobile = e.target.value;
    updateSpacerStyles(activeEl);
  }
});

function updateSpacerStyles(el) {
  const hDesktop = el.dataset.heightDesktop || '10';
  // We use CSS variables or direct style for desktop height
  el.style.height = hDesktop + 'px';
  
  // Note: Mobile height would usually be handled via a <style> tag or CSS media queries
  // For the editor, we mostly care about the desktop view, but we store the mobile value in dataset.
}

// Remove block
document.getElementById('ss-remove-btn').addEventListener('click', () => {
  if (activeEl) {
    activeEl.closest('.dropped-block').remove();
    checkEmptyBlocks();
  }
  closeAllPanels();
});

// ── Drop Spacer Block ────────────────────────────────────────────────────────

function dropSpacerBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block spacer-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Spacer <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <div class="editor-spacer" data-height-desktop="10" data-height-mobile="10" data-display="all" style="height: 10px; width: 100%;"></div>
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const spacer = block.querySelector('.editor-spacer');
  openSpacerSettings(spacer);
}
