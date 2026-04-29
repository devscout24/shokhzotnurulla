// ── Shared state & utilities ──────────────────────────────────────────────────

window.activeEl = null; // currently selected element

function clearSelected() {
  document.querySelectorAll('.dropped-block.selected')
    .forEach(b => b.classList.remove('selected'));
}

function closeAllPanels() {
  // Close every settings panel by ID
  ['heading-settings-panel', 'text-settings-panel', 'button-settings-panel', 'divider-settings-panel', 'image-settings-panel', 'accordion-settings-panel', 'spacer-settings-panel', 'card-settings-panel', '3col-settings-panel', '2col-settings-panel', 'container-settings-panel', 'icon-settings-panel', 'cart-settings-panel', 'span-settings-panel', 'iframe-settings-panel'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });
  activeEl = null;
  clearSelected();
}

function checkEmptyBlocks() {
  const emptyState = document.getElementById('empty-state');
  if (!document.querySelector('.dropped-block')) {
    emptyState.style.display = 'flex';
  }
}

function placeCursorAtEnd(el) {
  const range = document.createRange();
  range.selectNodeContents(el);
  range.collapse(false);
  const sel = window.getSelection();
  sel.removeAllRanges();
  sel.addRange(range);
}

function rgbToHex(rgb) {
  if (!rgb || !rgb.startsWith('rgb')) return rgb;
  const match = rgb.match(/\d+/g);
  if (!match) return '#ffffff';
  const r = parseInt(match[0]);
  const g = parseInt(match[1]);
  const b = parseInt(match[2]);
  return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}

// ── Attach listeners to any block (h1 / p / button) ──────────────────────────

function attachBlockListeners(block) {
  // Copy / duplicate icon in badge
  const copyBtn = block.querySelector('.copy-btn');
  if (copyBtn) {
    copyBtn.addEventListener('click', e => {
      e.stopPropagation();
      duplicateBlock(block);
    });
  }

  // Span
  const spanEl = block.querySelector('span[contenteditable]');
  if (spanEl) {
    spanEl.addEventListener('click', (e) => { e.stopPropagation(); openSpanSettings(spanEl); });
    spanEl.addEventListener('focus', (e) => { e.stopPropagation(); openSpanSettings(spanEl); });
  }


  // Reorder buttons (up/down arrows)
  const upBtn = block.querySelector('.move-up-btn');
  const downBtn = block.querySelector('.move-down-btn');
  if (upBtn) upBtn.addEventListener('click', e => { e.stopPropagation(); moveBlockUp(block); });
  if (downBtn) downBtn.addEventListener('click', e => { e.stopPropagation(); moveBlockDown(block); });

  // ── Drag-handle reorder ──────────────────────────────────────────────────
  const handle = block.querySelector('.drag-handle');
  if (handle) {
    // Make the whole dropped-block draggable but only when grabbing the handle
    handle.addEventListener('mousedown', () => { block.setAttribute('draggable', 'true'); });
    handle.addEventListener('mouseup', () => { block.removeAttribute('draggable'); });

    block.addEventListener('dragstart', e => {
      // Only proceed when initiated via the handle
      if (!block.hasAttribute('draggable')) return;
      window.reorderBlock = block;
      window.dragType = null; // not a sidebar drop
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', 'reorder'); // required for Firefox
      handle.classList.add('grabbing');
      // Slight delay so the ghost image is captured before we hide the block
      setTimeout(() => block.classList.add('reorder-dragging'), 0);
    });

    block.addEventListener('dragend', () => {
      block.removeAttribute('draggable');
      block.classList.remove('reorder-dragging');
      handle.classList.remove('grabbing');
      window.reorderBlock = null;
      // Hide indicator
      const ind = document.getElementById('drop-indicator');
      if (ind) ind.style.display = 'none';
    });
  }

  // Heading
  const h1 = block.querySelector('h1[contenteditable]');
  if (h1) {
    h1.addEventListener('click', (e) => { e.stopPropagation(); openHeadingSettings(h1); });
    h1.addEventListener('focus', (e) => { e.stopPropagation(); openHeadingSettings(h1); });
  }

  // Text
  const p = block.querySelector('p[contenteditable]');
  if (p) {
    p.addEventListener('click', (e) => { e.stopPropagation(); openTextSettings(p); });
    p.addEventListener('focus', (e) => { e.stopPropagation(); openTextSettings(p); });
  }

  // Button
  const btn = block.querySelector('.dropped-btn');
  if (btn) {
    btn.addEventListener('click', e => {
      e.preventDefault();
      e.stopPropagation();
      openButtonSettings(btn);
    });
  }

  // Divider
  const hr = block.querySelector('.editor-divider');
  if (hr) {
    const inner = block.querySelector('.dropped-block-inner');
    inner.style.cursor = 'pointer'; // Make it look clickable
    inner.addEventListener('click', (e) => { e.stopPropagation(); openDividerSettings(hr); });
  }

  // Image
  const img = block.querySelector('.editor-image');
  if (img) {
    img.addEventListener('click', (e) => { e.stopPropagation(); openImageSettings(img); });
  }

  // Accordion
  const acc = block.querySelector('.editor-accordion');
  if (acc) {
    acc.addEventListener('click', (e) => { e.stopPropagation(); openAccordionSettings(acc); });
  }

  // Spacer
  const spacer = block.querySelector('.editor-spacer');
  if (spacer) {
    const inner = block.querySelector('.dropped-block-inner');
    inner.style.cursor = 'pointer';
    inner.addEventListener('click', (e) => { e.stopPropagation(); openSpacerSettings(spacer); });
  }

  // Card
  const card = block.querySelector('.editor-card');
  if (card) {
    card.addEventListener('click', (e) => { e.stopPropagation(); openCardSettings(card); });
  }

  // 3-Col
  const col3 = block.querySelector('.editor-3col');
  if (col3) {
    col3.addEventListener('click', (e) => {
      // Only open settings if we clicked the column wrapper background, not a child block
      if (e.target === col3 || e.target.classList.contains('col-drop-zone')) {
        e.stopPropagation(); open3ColSettings(col3);
      }
    });
  }

  // IFrame
  const iframe = block.querySelector('.editor-iframe');
  if (iframe) {
    iframe.addEventListener('click', (e) => {
      e.stopPropagation();
      openIFrameSettings(iframe);
    });
  }

  // 2-Col
  const col2 = block.querySelector('.editor-2col');
  if (col2) {
    col2.addEventListener('click', (e) => {
      if (e.target === col2 || e.target.classList.contains('col-drop-zone')) {
        e.stopPropagation(); open2ColSettings(col2);
      }
    });
  }

  // Container
  const container = block.querySelector('.editor-container');
  if (container) {
    container.addEventListener('click', (e) => {
      if (e.target === container) {
        e.stopPropagation(); openContainerSettings(container);
      }
    });
  }
  // Icon
  const icon = block.querySelector('.editor-icon');
  if (icon) {
    icon.addEventListener('click', (e) => {
      e.stopPropagation();
      openIconSettings(icon);
    });
  }
  // Cart
  const cart = block.querySelector('.editor-cart');
  if (cart) {
    cart.addEventListener('click', (e) => {
      e.stopPropagation();
      openCartSettings(cart);
    });
  }
}

// ── Duplicate a block (with existing content) ─────────────────────────────────

function duplicateBlock(originalBlock) {
  const newBlock = originalBlock.cloneNode(true); // deep clone — content included
  originalBlock.parentNode.insertBefore(newBlock, originalBlock.nextSibling);
  attachBlockListeners(newBlock);

  // Select & focus the new block
  const el = newBlock.querySelector('h1[contenteditable]') ||
    newBlock.querySelector('p[contenteditable]') ||
    newBlock.querySelector('.dropped-btn') ||
    newBlock.querySelector('.editor-divider') ||
    newBlock.querySelector('.editor-image') ||
    newBlock.querySelector('.editor-accordion') ||
    newBlock.querySelector('.editor-spacer') ||
    newBlock.querySelector('.editor-card') ||
    newBlock.querySelector('.editor-3col') ||
    newBlock.querySelector('.editor-2col') ||
    newBlock.querySelector('.editor-container') ||
    newBlock.querySelector('.editor-icon') ||
    newBlock.querySelector('.editor-cart') ||
    newBlock.querySelector('.editor-iframe') ||
    newBlock.querySelector('span[contenteditable]');

  if (el) {
    if (el.tagName === 'H1') { el.focus(); openHeadingSettings(el); placeCursorAtEnd(el); }
    else if (el.tagName === 'P') { el.focus(); openTextSettings(el); placeCursorAtEnd(el); }
    else if (el.tagName === 'SPAN') { el.focus(); openSpanSettings(el); placeCursorAtEnd(el); }
    else if (el.classList.contains('dropped-btn')) { openButtonSettings(el); }
    else if (el.classList.contains('editor-divider')) { openDividerSettings(el); }
    else if (el.classList.contains('editor-image')) { openImageSettings(el); }
    else if (el.classList.contains('editor-accordion')) { openAccordionSettings(el); }
    else if (el.classList.contains('editor-spacer')) { openSpacerSettings(el); }
    else if (el.classList.contains('editor-card')) { openCardSettings(el); }
    else if (el.classList.contains('editor-3col')) { open3ColSettings(el); }
    else if (el.classList.contains('editor-2col')) { open2ColSettings(el); }
    else if (el.classList.contains('editor-container')) { openContainerSettings(el); }
    else if (el.classList.contains('editor-icon')) { openIconSettings(el); }
    else if (el.classList.contains('editor-cart')) { openCartSettings(el); }
    else if (el.classList.contains('editor-iframe')) { openIFrameSettings(el); if (typeof saveHistory === 'function') saveHistory(); }
  }
}

// ── Move blocks Up / Down ─────────────────────────────────────────────────────

function moveBlockUp(block) {
  const prev = block.previousElementSibling;
  if (prev && !prev.classList.contains('editor-empty-state')) {
    block.parentNode.insertBefore(block, prev); if (typeof saveHistory === 'function') saveHistory();
  }
}

function moveBlockDown(block) {
  const next = block.nextElementSibling;
  if (next) {
    block.parentNode.insertBefore(next, block); if (typeof saveHistory === 'function') saveHistory();
  }
}


// ── Free Movement Dragging ───────────────────────────────────────────────────
let isManualDragging = false;
let dragStartX, dragStartY, initialTop, initialLeft, manualDragBlock;

document.addEventListener('mousedown', e => {
  const handle = e.target.closest('.free-moving .drag-handle');
  if (handle) {
    isManualDragging = true;
    manualDragBlock = handle.closest('.dropped-block');
    
    dragStartX = e.clientX;
    dragStartY = e.clientY;
    initialTop = parseInt(manualDragBlock.style.top) || 0;
    initialLeft = parseInt(manualDragBlock.style.left) || 0;
    
    manualDragBlock.style.cursor = 'grabbing';
    e.preventDefault();
    e.stopPropagation();
  }
});

document.addEventListener('mousemove', e => {
  if (isManualDragging && manualDragBlock) {
    const dx = e.clientX - dragStartX;
    const dy = e.clientY - dragStartY;
    
    const newTop = initialTop + dy;
    const newLeft = initialLeft + dx;
    
    manualDragBlock.style.top = newTop + 'px';
    manualDragBlock.style.left = newLeft + 'px';
    
    // Sync settings panel inputs if they exist and are visible
    const topInput = document.getElementById('icon-top');
    const leftInput = document.getElementById('icon-left');
    if (topInput && manualDragBlock.querySelector('.editor-icon')) topInput.value = newTop;
    if (leftInput && manualDragBlock.querySelector('.editor-icon')) leftInput.value = newLeft;
  }
});

document.addEventListener('mouseup', () => {
  if (isManualDragging) {
    isManualDragging = false;
    if (manualDragBlock) {
      manualDragBlock.style.cursor = '';
    }
    manualDragBlock = null;
  }
});

function attachDropZoneListeners(col) {
  col.addEventListener('dragover', e => {
    e.preventDefault();
    e.stopPropagation();
    e.dataTransfer.dropEffect = 'copy';
    col.style.background = '#fff5f5';

    const afterElement = getDragAfterElement(col, e.clientY, e.clientX);
    const indicator = document.getElementById('drop-indicator');
    if (indicator) {
      indicator.style.display = 'block';
      if (afterElement == null) col.appendChild(indicator);
      else col.insertBefore(indicator, afterElement);
    }
  });

  col.addEventListener('dragleave', e => {
    e.stopPropagation();
    if (!col.contains(e.relatedTarget)) {
      col.style.background = 'transparent';
      const indicator = document.getElementById('drop-indicator');
      if (indicator) indicator.style.display = 'none';
    }
  });

  col.addEventListener('drop', e => {
    e.preventDefault();
    e.stopPropagation();
    col.style.background = 'transparent';
    const indicator = document.getElementById('drop-indicator');
    if (indicator) indicator.style.display = 'none';

    if (window.dragType) {
      const afterElement = getDragAfterElement(col, e.clientY, e.clientX);
      if (typeof createAndInsertBlock === 'function') {
        createAndInsertBlock(window.dragType, col, afterElement);
      }
    }
    window.dragType = null;
  });
}
