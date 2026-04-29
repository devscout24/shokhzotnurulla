// ── Cart Settings Panel ──────────────────────────────────────────────────────

function openCartSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  const panel = document.getElementById('cart-settings-panel');
  if (panel) panel.style.display = 'block';

  // Sync inputs
  const cartTextEl = el.querySelector('.cart-text');
  const cartLinkEl = el.querySelector('a');
  
  if (cartTextEl) {
    document.getElementById('cart-text').value = cartTextEl.textContent || '';
  }
  if (cartLinkEl) {
    document.getElementById('cart-link').value = cartLinkEl.getAttribute('href') || '#';
  }

  // Sync floating
  const isFloating = activeEl.closest('.dropped-block').classList.contains('free-moving');
  const floatSwitch = document.getElementById('cart-floating');
  if (floatSwitch) floatSwitch.checked = isFloating;

  // Sync align
  const curAlign = el.style.justifyContent || 'flex-start';
  let btnAlign = 'left';
  if (curAlign === 'center') btnAlign = 'center';
  else if (curAlign === 'flex-end') btnAlign = 'right';

  document.querySelectorAll('.cart-align-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.align === btnAlign)
  );
}

// Back / Cancel
document.getElementById('cart-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('cart-cancel-btn')?.addEventListener('click', closeAllPanels);

// Cart Text Change
document.getElementById('cart-text')?.addEventListener('input', e => {
  if (activeEl) {
    const textEl = activeEl.querySelector('.cart-text');
    if (textEl) textEl.textContent = e.target.value || 'Items (0)';
  }
});

// Cart Link Change
document.getElementById('cart-link')?.addEventListener('input', e => {
  if (activeEl) {
    const linkEl = activeEl.querySelector('a');
    if (linkEl) linkEl.setAttribute('href', e.target.value || '#');
  }
});

// Floating Mode Toggle
document.getElementById('cart-floating')?.addEventListener('change', e => {
  if (activeEl) {
    const block = activeEl.closest('.dropped-block');
    if (e.target.checked) {
      block.classList.add('free-moving');
      block.style.position = 'absolute';
      block.style.zIndex = '100';
      if (!block.style.left) {
          block.style.left = '20px';
          block.style.top = '20px';
      }
    } else {
      block.classList.remove('free-moving');
      block.style.position = 'relative';
      block.style.left = '';
      block.style.top = '';
      block.style.zIndex = '';
    }
  }
});

// Align
document.querySelectorAll('.cart-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.cart-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) {
      if (btn.dataset.align === 'left') activeEl.style.justifyContent = 'flex-start';
      else if (btn.dataset.align === 'center') activeEl.style.justifyContent = 'center';
      else if (btn.dataset.align === 'right') activeEl.style.justifyContent = 'flex-end';
    }
  });
});

// Remove
document.getElementById('cart-remove-btn')?.addEventListener('click', () => {
  if (activeEl) {
    activeEl.closest('.dropped-block').remove();
    checkEmptyBlocks();
  }
  closeAllPanels();
});

// ── Drop Cart Block ───────────────────────────────────────────────────────────

function dropCartBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Cart <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner editor-cart" style="display: flex; justify-content: flex-start; padding: 10px;">
      <a href="#" style="text-decoration: none; color: #000; display: flex; align-items: center; gap: 8px; border: 1px solid #ccc; padding: 8px 15px; rounded: 4px;">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="cart-text">Items (0)</span>
      </a>
    </div>`;

  if (returnBlock) return block;
  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const inner = block.querySelector('.editor-cart');
  if (inner) openCartSettings(inner);
}
