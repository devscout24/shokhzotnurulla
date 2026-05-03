// ── Cart Settings Panel ──────────────────────────────────────────────────────

function openCartSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  document.getElementById('cart-settings-panel').style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
}

// Back / Cancel
document.getElementById('cart-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('cart-cancel-btn')?.addEventListener('click', closeAllPanels);

// Remove
document.getElementById('cart-remove-btn')?.addEventListener('click', () => {
  if (activeEl) { activeEl.closest('.dropped-block').remove(); checkEmptyBlocks(); if (typeof saveHistory === 'function') saveHistory(); }
  closeAllPanels();
});

// ── Drop ─────────────────────────────────────────────────────────────────────
function dropCartBlock(returnBlock = false) {
  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">Cart <i class="fa-solid fa-copy copy-btn"></i></span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="justify-content:flex-end;padding:10px 20px;">
      <div class="editor-cart" style="display:flex;align-items:center;gap:10px;cursor:pointer;background:#f8f9fa;padding:8px 15px;border-radius:20px;border:1px solid #dee2e6;">
        <i class="fa-solid fa-cart-shopping"></i>
        <span style="font-weight:600;font-size:14px;">0 Items</span>
      </div>
    </div>`;

  if (returnBlock) return block;
  document.getElementById('blocks-container').appendChild(block);
  attachBlockListeners(block);
  const c = block.querySelector('.editor-cart');
  openCartSettings(c);
  if (typeof saveHistory === 'function') saveHistory();
}
