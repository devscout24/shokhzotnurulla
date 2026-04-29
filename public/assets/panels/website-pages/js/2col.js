// ── 2-Column Settings Panel ───────────────────────────────────────────────────

function open2ColSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  const panel = document.getElementById('2col-settings-panel');
  if (panel) panel.style.display = 'block';

  // Sync gap field
  const gapValue = el.style.gap ? parseInt(el.style.gap) : 20;
  document.getElementById('col2-gap').value = gapValue;
}

// Settings panel event listeners
document.getElementById('col2-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('col2-cancel-btn').addEventListener('click', closeAllPanels);

document.getElementById('col2-gap').addEventListener('input', e => {
  if (activeEl && activeEl.classList.contains('editor-2col')) {
    activeEl.style.gap = (parseInt(e.target.value) || 0) + 'px';
  }
});

document.getElementById('col2-remove-btn').addEventListener('click', () => {
  if (activeEl) {
    activeEl.closest('.dropped-block').remove();
    if (typeof checkEmptyBlocks === 'function') checkEmptyBlocks();
  }
  closeAllPanels();
});

// ── Drop 2-Column Block ───────────────────────────────────────────────────────

function drop2ColBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      2-Column <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="background: transparent; border: none; padding: 0;">
      <div class="editor-2col" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; width: 100%;">
        <div class="col-drop-zone" style="min-height: 100px; border: 1px dashed #ccc; padding: 10px; background: transparent;"></div>
        <div class="col-drop-zone" style="min-height: 100px; border: 1px dashed #ccc; padding: 10px; background: transparent;"></div>
      </div>
    </div>`;

  // Attach nested drop zone listeners to columns
  const cols = block.querySelectorAll('.col-drop-zone');
  cols.forEach(col => attachDropZoneListeners(col));

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const colBlock = block.querySelector('.editor-2col');
  if (colBlock) open2ColSettings(colBlock);
}
