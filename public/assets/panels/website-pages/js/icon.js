// ── Icon Settings Panel ──────────────────────────────────────────────────────

function openIconSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  document.getElementById('icon-settings-panel').style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync fields
  document.getElementById('ics-icon').value = Array.from(el.classList).find(c => c.startsWith('fa-')) || 'fa-star';
  document.getElementById('ics-size').value = parseInt(el.style.fontSize) || 24;
  document.getElementById('ics-color').value = rgbToHex(el.style.color) || '#111827';
}

// Back / Cancel
document.getElementById('ics-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('ics-cancel-btn')?.addEventListener('click', closeAllPanels);

// Icon class
document.getElementById('ics-icon')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.className = 'editor-icon fa-solid ' + e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Size
document.getElementById('ics-size')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.fontSize = (e.target.value || 24) + 'px';
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Color
document.getElementById('ics-color')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.color = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Remove
document.getElementById('ics-remove-btn')?.addEventListener('click', () => {
  if (activeEl) { activeEl.closest('.dropped-block').remove(); checkEmptyBlocks(); if (typeof saveHistory === 'function') saveHistory(); }
  closeAllPanels();
});

// ── Drop ─────────────────────────────────────────────────────────────────────
function dropIconBlock(returnBlock = false) {
  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">Icon <i class="fa-solid fa-copy copy-btn"></i></span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="justify-content:center;padding:20px;">
      <i class="editor-icon fa-solid fa-star" style="font-size:24px;color:#111827;cursor:pointer;"></i>
    </div>`;

  if (returnBlock) return block;
  document.getElementById('blocks-container').appendChild(block);
  attachBlockListeners(block);
  const i = block.querySelector('.editor-icon');
  openIconSettings(i);
  if (typeof saveHistory === 'function') saveHistory();
}
