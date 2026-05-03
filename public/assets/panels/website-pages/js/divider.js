// ── Divider Settings Panel ───────────────────────────────────────────────────

function openDividerSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  const panel = document.getElementById('divider-settings-panel');
  if (panel) panel.style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync fields
  document.getElementById('ds-color').value = rgbToHex(el.style.backgroundColor) || '#ced4da';
  document.getElementById('ds-width').value = el.style.width ? parseInt(el.style.width) : 100;
  document.getElementById('ds-height').value = el.style.height ? parseInt(el.style.height) : 2;
}

// Back / Cancel
document.getElementById('ds-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('ds-cancel-btn')?.addEventListener('click', closeAllPanels);

// Color
document.getElementById('ds-color')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.backgroundColor = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Width
document.getElementById('ds-width')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.width = (e.target.value || 100) + '%';
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Height
document.getElementById('ds-height')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.height = (e.target.value || 2) + 'px';
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Remove block
document.getElementById('ds-remove-btn')?.addEventListener('click', () => {
  if (activeEl) {
    const block = activeEl.closest('.dropped-block');
    if (block) {
      block.remove();
      checkEmptyBlocks();
      if (typeof saveHistory === 'function') saveHistory();
    }
  }
  closeAllPanels();
});

// ── Drop Divider Block ────────────────────────────────────────────────────────

function dropDividerBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Divider <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="padding: 20px 0; justify-content: center;">
      <div class="editor-divider" style="width: 100%; height: 2px; background-color: #ced4da; margin: 0 auto;"></div>
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const divider = block.querySelector('.editor-divider');
  openDividerSettings(divider);
  if (typeof saveHistory === 'function') saveHistory();
}
