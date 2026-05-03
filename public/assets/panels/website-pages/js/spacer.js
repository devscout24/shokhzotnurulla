// ── Spacer Settings Panel ────────────────────────────────────────────────────

function openSpacerSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  document.getElementById('spacer-settings-panel').style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync Height
  document.getElementById('ss-height').value = parseInt(el.style.height) || 40;
}

// Back / Cancel
document.getElementById('ss-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('ss-cancel-btn')?.addEventListener('click', closeAllPanels);

// Height
document.getElementById('ss-height')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.height = (e.target.value || 40) + 'px';
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Remove
document.getElementById('ss-remove-btn')?.addEventListener('click', () => {
  if (activeEl) { activeEl.closest('.dropped-block').remove(); checkEmptyBlocks(); if (typeof saveHistory === 'function') saveHistory(); }
  closeAllPanels();
});

// ── Drop ─────────────────────────────────────────────────────────────────────
function dropSpacerBlock(returnBlock = false) {
  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">Spacer <i class="fa-solid fa-copy copy-btn"></i></span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <div class="editor-spacer" style="width:100%;height:40px;background:repeating-linear-gradient(45deg, #f8f9fa, #f8f9fa 10px, #ffffff 10px, #ffffff 20px);opacity:0.6;cursor:pointer;border:1px dashed #dee2e6;"></div>
    </div>`;

  if (returnBlock) return block;
  document.getElementById('blocks-container').appendChild(block);
  attachBlockListeners(block);
  const s = block.querySelector('.editor-spacer');
  openSpacerSettings(s);
  if (typeof saveHistory === 'function') saveHistory();
}
