// ── Heading (H1) Settings Panel ───────────────────────────────────────────────

function openHeadingSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  document.getElementById('heading-settings-panel').style.display = 'block';

  // Sync align buttons
  const cur = el.style.textAlign || 'left';
  document.querySelectorAll('.hs-align-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.align === cur)
  );

  // Sync fields
  document.getElementById('hs-color').value = el.style.color || '';
  document.getElementById('hs-size').value = el.style.fontSize ? parseInt(el.style.fontSize) : '';
  document.getElementById('hs-weight').value = el.style.fontWeight || 'normal';
  document.getElementById('hs-margin-top').value = el.style.marginTop ? parseInt(el.style.marginTop) : '';
  document.getElementById('hs-margin-bottom').value = el.style.marginBottom ? parseInt(el.style.marginBottom) : '';
  document.getElementById('hs-classes').value = el.dataset.cssClasses || '';
}

// Back / Cancel
document.getElementById('hs-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('hs-cancel-btn').addEventListener('click', closeAllPanels);

// Align
document.querySelectorAll('.hs-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.hs-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) activeEl.style.textAlign = btn.dataset.align;
  });
});

// Text color
document.getElementById('hs-color').addEventListener('change', e => {
  if (activeEl) activeEl.style.color = e.target.value || '';
});

// Text size
document.getElementById('hs-size').addEventListener('input', e => {
  if (activeEl && e.target.value) activeEl.style.fontSize = e.target.value + 'px';
  else if (activeEl) activeEl.style.fontSize = '';
});

// Font weight
document.getElementById('hs-weight').addEventListener('change', e => {
  if (activeEl) {
    if (e.target.value === 'normal') {
      activeEl.style.fontWeight = '';
    } else {
      activeEl.style.fontWeight = e.target.value;
    }
  }
});

// Margin top
document.getElementById('hs-margin-top').addEventListener('input', e => {
  if (activeEl && e.target.value) activeEl.style.marginTop = e.target.value + 'px';
  else if (activeEl) activeEl.style.marginTop = '';
});

// Margin bottom
document.getElementById('hs-margin-bottom').addEventListener('input', e => {
  if (activeEl && e.target.value) activeEl.style.marginBottom = e.target.value + 'px';
  else if (activeEl) activeEl.style.marginBottom = '';
});

// CSS classes
document.getElementById('hs-classes').addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.cssClasses = e.target.value;
    activeEl.className = e.target.value;
  }
});

// Remove block
document.getElementById('hs-remove-btn').addEventListener('click', () => {
  if (activeEl) {
    if (activeEl.closest('.editor-card')) {
      activeEl.remove();
    } else {
      activeEl.closest('.dropped-block').remove();
      checkEmptyBlocks();
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
  block.innerHTML = `
    <span class="dropped-block-badge">
      Heading <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <h1 contenteditable="true" spellcheck="false" data-placeholder="Enter Heading..."></h1>
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const h1 = block.querySelector('h1');
  h1.focus();
  openHeadingSettings(h1);
  placeCursorAtEnd(h1);
}
