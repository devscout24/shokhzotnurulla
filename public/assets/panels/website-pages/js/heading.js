// ── Heading (H1) Settings Panel ───────────────────────────────────────────────

function openHeadingSettings(el) {
  openPanel('heading-settings-panel');
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync align buttons
  const cur = el.style.textAlign || 'left';
  document.querySelectorAll('.hs-align-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.align === cur)
  );

  // Sync fields
  document.getElementById('hs-color').value = rgbToHex(el.style.color) || '#111827';
  document.getElementById('hs-size').value = el.style.fontSize ? parseInt(el.style.fontSize) : '';
  document.getElementById('hs-classes').value = el.dataset.cssClasses || '';
}

// Back / Cancel
document.getElementById('hs-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('hs-cancel-btn')?.addEventListener('click', closeAllPanels);

// Align
document.querySelectorAll('.hs-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.hs-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) activeEl.style.textAlign = btn.dataset.align;
    if (typeof saveHistory === 'function') saveHistory();
  });
});

// Text color
document.getElementById('hs-color')?.addEventListener('change', e => {
  if (activeEl) activeEl.style.color = e.target.value || '';
  if (typeof saveHistory === 'function') saveHistory();
});

// Text size
document.getElementById('hs-size')?.addEventListener('input', e => {
  if (activeEl && e.target.value) activeEl.style.fontSize = e.target.value + 'px';
  else if (activeEl) activeEl.style.fontSize = '';
  if (typeof saveHistory === 'function') saveHistory();
});

// CSS classes
document.getElementById('hs-classes')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.cssClasses = e.target.value;
    activeEl.className = e.target.value;
  }
  if (typeof saveHistory === 'function') saveHistory();
});

// Remove block
document.getElementById('hs-remove-btn')?.addEventListener('click', () => {
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

// ── Drop Heading Block ────────────────────────────────────────────────────────

function dropHeadingBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.dataset.blockType = 'Heading';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Heading <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <h1 contenteditable="true" spellcheck="false" data-placeholder="Enter Heading..." style="min-height:40px; padding:5px; margin:0; width:100%; display:block; outline:none;"></h1>
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const h1 = block.querySelector('h1');
  setTimeout(() => {
    h1.focus();
    placeCursorAtEnd(h1);
    openHeadingSettings(h1);
  }, 100);
  if (typeof saveHistory === 'function') saveHistory();
}
