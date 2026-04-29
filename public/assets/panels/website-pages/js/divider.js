// ── Divider Settings Panel ───────────────────────────────────────────────────

function openDividerSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  document.getElementById('divider-settings-panel').style.display = 'block';

  // Sync fields
  document.getElementById('ds-classes').value = el.dataset.cssClasses || '';
  document.getElementById('ds-color').value = el.style.borderColor || '';
}

// Back / Cancel
document.getElementById('ds-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('ds-cancel-btn').addEventListener('click', closeAllPanels);

// CSS classes
document.getElementById('ds-classes').addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.cssClasses = e.target.value;
    activeEl.className = 'editor-divider ' + e.target.value;
  }
});

// Color
document.getElementById('ds-color').addEventListener('change', e => {
  if (activeEl) {
    activeEl.style.borderColor = e.target.value || '';
  }
});

// Remove block
document.getElementById('ds-remove-btn').addEventListener('click', () => {
  if (activeEl) {
    activeEl.closest('.dropped-block').remove();
    checkEmptyBlocks();
  }
  closeAllPanels();
});

// ── Drop Divider Block ────────────────────────────────────────────────────────

function dropDividerBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Divider <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <hr class="editor-divider" />
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const hr = block.querySelector('hr');
  openDividerSettings(hr);
}
