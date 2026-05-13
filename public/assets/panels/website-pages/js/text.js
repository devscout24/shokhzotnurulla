// ── Text (P) Settings Panel ───────────────────────────────────────────────────

function openTextSettings(el) {
  openPanel('text-settings-panel');
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync align buttons
  const cur = el.style.textAlign || 'left';
  document.querySelectorAll('.ts-align-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.align === cur)
  );

  // Sync fields
  document.getElementById('ts-color').value = rgbToHex(el.style.color) || '#111827';
  document.getElementById('ts-size').value = el.style.fontSize ? parseInt(el.style.fontSize) : '';
}

// Back / Cancel
document.getElementById('ts-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('ts-cancel-btn')?.addEventListener('click', closeAllPanels);

// Align
document.querySelectorAll('.ts-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.ts-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) activeEl.style.textAlign = btn.dataset.align;
    if (typeof saveHistory === 'function') saveHistory();
  });
});

// Text color
document.getElementById('ts-color')?.addEventListener('change', e => {
  if (activeEl) activeEl.style.color = e.target.value || '';
  if (typeof saveHistory === 'function') saveHistory();
});

// Text size
document.getElementById('ts-size')?.addEventListener('input', e => {
  if (activeEl && e.target.value) activeEl.style.fontSize = e.target.value + 'px';
  else if (activeEl) activeEl.style.fontSize = '';
  if (typeof saveHistory === 'function') saveHistory();
});

// Remove block
document.getElementById('ts-remove-btn')?.addEventListener('click', () => {
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

// ── Drop Text (P) Block ───────────────────────────────────────────────────────

function dropTextBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.dataset.blockType = 'Text';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Text <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <p contenteditable="true" spellcheck="false" data-placeholder="Enter text here..." style="min-height:40px; padding:5px; margin:0; width:100%; display:block; outline:none;"></p>
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const p = block.querySelector('p');
  setTimeout(() => {
    p.focus();
    placeCursorAtEnd(p);
    openTextSettings(p);
  }, 100);
  if (typeof saveHistory === 'function') saveHistory();
}
