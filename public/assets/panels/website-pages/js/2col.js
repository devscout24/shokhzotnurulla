// ── 2-Column Settings Panel ───────────────────────────────────────────────────

function open2ColSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  const panel = document.getElementById('2col-settings-panel');
  if (panel) panel.style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync gap field
  const gapValue = el.style.gap ? parseInt(el.style.gap) : 20;
  const gapInput = document.getElementById('col2-gap');
  if (gapInput) gapInput.value = gapValue;
}

// Back / Cancel
document.getElementById('col2-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('col2-cancel-btn')?.addEventListener('click', closeAllPanels);

// Gap Input
document.getElementById('col2-gap')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.gap = (e.target.value || 0) + 'px';
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Remove block
document.getElementById('col2-remove-btn')?.addEventListener('click', () => {
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
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <div class="editor-2col" style="display: flex; gap: 20px; width: 100%;">
        <div class="col-drop-zone flex-grow-1" style="min-height: 80px; border: 1px dashed #ced4da; border-radius: 4px; padding: 10px;">
          <p contenteditable="true" spellcheck="false" data-placeholder="Column 1" style="margin:0; color:#adb5bd; font-size:13px; min-height:20px; outline:none;"></p>
        </div>
        <div class="col-drop-zone flex-grow-1" style="min-height: 80px; border: 1px dashed #ced4da; border-radius: 4px; padding: 10px;">
          <p contenteditable="true" spellcheck="false" data-placeholder="Column 2" style="margin:0; color:#adb5bd; font-size:13px; min-height:20px; outline:none;"></p>
        </div>
      </div>
    </div>`;

  const col2 = block.querySelector('.editor-2col');
  const zones = block.querySelectorAll('.col-drop-zone');
  const placeholders = block.querySelectorAll('[contenteditable]');
  
  zones.forEach((zone, idx) => {
    attachDropZoneListeners(zone);
    
      // Handle placeholder behavior in each column
      if (placeholders[idx]) {
        const placeholder = placeholders[idx];
        placeholder.classList.add('is-placeholder');
        
        // Hide the CSS-based placeholder if the column has dropped blocks
        const observer = new MutationObserver(() => {
          const hasBlocks = zone.querySelector('.dropped-block') !== null;
          if (hasBlocks) {
            placeholder.classList.add('d-none');
          } else {
            placeholder.classList.remove('d-none');
          }
        });
        observer.observe(zone, { childList: true, subtree: true });
      }
  });

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  if (col2) open2ColSettings(col2);
  if (typeof saveHistory === 'function') saveHistory();
}
