// ── Card Settings Panel ─────────────────────────────────────────────────────

function openCardSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  document.getElementById('card-settings-panel').style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync Width
  const currentWidth = el.style.width || '100%';
  document.getElementById('cs-width').value = parseInt(currentWidth) || 100;
}

// Back / Cancel
document.getElementById('cs-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('cs-cancel-btn')?.addEventListener('click', closeAllPanels);

// Width Input
document.getElementById('cs-width')?.addEventListener('input', e => {
  if (activeEl) {
    const w = Math.min(100, Math.max(5, parseInt(e.target.value) || 100));
    activeEl.style.width = w + '%';
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Remove block
document.getElementById('cs-remove-btn')?.addEventListener('click', () => {
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

// ── Drop Card Block ──────────────────────────────────────────────────────────

function dropCardBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Card <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <div class="card editor-card" style="width: 100%; display: flex; flex-direction: column; border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden; background: #fff;">
        <img src="https://via.placeholder.com/300x150?text=Card+Image" class="editor-image card-img-top" alt="Card image" style="width: 100%; height: auto; cursor: pointer;">
        <div class="card-body col-drop-zone" style="padding: 15px; display: flex; flex-direction: column; gap: 10px; min-height: 50px;">
          <h1 contenteditable="true" spellcheck="false" class="card-title m-0" style="font-size: 20px; font-weight: bold;">Card Title</h1>
          <p contenteditable="true" spellcheck="false" class="card-text m-0" style="font-size: 14px; color: #6c757d;">Example card description text.</p>
        </div>
      </div>
    </div>`;

  const card = block.querySelector('.editor-card');
  const img = block.querySelector('.editor-image');
  const h1 = block.querySelector('h1');
  const p = block.querySelector('p');
  const col = block.querySelector('.col-drop-zone');

  if (col) attachDropZoneListeners(col);

  // Child listeners
  if (img) img.addEventListener('click', (e) => { e.stopPropagation(); openImageSettings(img); });
  if (h1) h1.addEventListener('focus', (e) => { e.stopPropagation(); openHeadingSettings(h1); });
  if (p) p.addEventListener('focus', (e) => { e.stopPropagation(); openTextSettings(p); });
  
  card.addEventListener('click', (e) => {
    if (e.target === card || e.target.classList.contains('card-body')) {
      e.stopPropagation();
      openCardSettings(card);
    }
  });

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  if (card) openCardSettings(card);
  if (typeof saveHistory === 'function') saveHistory();
}
