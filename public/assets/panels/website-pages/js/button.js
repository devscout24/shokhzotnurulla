// ── Button Settings Panel ─────────────────────────────────────────────────────

function openButtonSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  document.getElementById('button-settings-panel').style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync fields
  document.getElementById('bs-text').value = el.textContent.trim();
  document.getElementById('bs-theme').value = el.dataset.theme || 'red';
  document.getElementById('bs-link').value = el.getAttribute('href') !== '#' ? el.getAttribute('href') : '';

  const fw = el.classList.contains('full-width');
  document.getElementById('bs-fullwidth').checked = fw;
}

// Back / Cancel
document.getElementById('bs-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('bs-cancel-btn')?.addEventListener('click', closeAllPanels);

// Button text
document.getElementById('bs-text')?.addEventListener('input', e => {
  if (activeEl) activeEl.textContent = e.target.value;
  if (typeof saveHistory === 'function') saveHistory();
});

// Theme
document.getElementById('bs-theme')?.addEventListener('change', e => {
  if (activeEl) activeEl.dataset.theme = e.target.value;
  if (typeof saveHistory === 'function') saveHistory();
});

// Link
document.getElementById('bs-link')?.addEventListener('input', e => {
  if (activeEl) activeEl.setAttribute('href', e.target.value || '#');
  if (typeof saveHistory === 'function') saveHistory();
});

// Full-width
document.getElementById('bs-fullwidth')?.addEventListener('change', e => {
  if (activeEl) {
    activeEl.classList.toggle('full-width', e.target.checked);
  }
  if (typeof saveHistory === 'function') saveHistory();
});

// Remove block
document.getElementById('bs-remove-btn')?.addEventListener('click', () => {
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

// ── Drop Button Block ─────────────────────────────────────────────────────────

function dropButtonBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Button <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="justify-content:center; padding:20px 16px;">
      <a class="dropped-btn" data-theme="red" href="#">GO FOR LIVE</a>
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const btn = block.querySelector('.dropped-btn');
  openButtonSettings(btn);
  if (typeof saveHistory === 'function') saveHistory();
}
