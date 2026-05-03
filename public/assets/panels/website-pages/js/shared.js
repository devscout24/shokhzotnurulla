// ── Shared state & utilities ──────────────────────────────────────────────────

window.activeEl = null; // currently selected element

function clearSelected() {
  document.querySelectorAll('.dropped-block.selected')
    .forEach(b => b.classList.remove('selected'));
}

function openPanel(id) {
  closeAllPanels();
  const panel = document.getElementById(id);
  if (panel) panel.style.display = 'block';
  const defaultContent = document.getElementById('sidebar-default-content');
  if (defaultContent) defaultContent.style.display = 'none';
}

function closeAllPanels() {
  // Close every settings panel by ID
  const panels = ['heading-settings-panel', 'text-settings-panel', 'button-settings-panel', 'divider-settings-panel', 'image-settings-panel', 'accordion-settings-panel', 'spacer-settings-panel', 'card-settings-panel', '3col-settings-panel', '2col-settings-panel', 'container-settings-panel', 'icon-settings-panel', 'cart-settings-panel', 'span-settings-panel', 'iframe-settings-panel', 'video-settings-panel', 'inventory-settings-panel', 'form-settings-panel', 'search-settings-panel', 'carousel-settings-panel', 'tabs-settings-panel', 'map-settings-panel', 'overlay-settings-panel', 'html-settings-panel', 'css-settings-panel'];
  
  panels.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });

  const defaultContent = document.getElementById('sidebar-default-content');
  if (defaultContent) defaultContent.style.display = 'block';

  activeEl = null;
  clearSelected();
}

// ── Visibility Toggle Logic ──────────────────────────────────────────────────
document.addEventListener('change', e => {
  if (e.target.classList.contains('visibility-toggle')) {
    if (!activeEl) return;
    const block = activeEl.closest('.dropped-block');
    if (!block) return;
    
    const device = e.target.dataset.device; // "desktop" or "mobile"
    const isVisible = e.target.checked;
    
    block.dataset[`visibility${device.charAt(0).toUpperCase() + device.slice(1)}`] = isVisible ? 'visible' : 'hidden';
    if (typeof saveHistory === 'function') saveHistory();
  }
});

function syncVisibilityToggles(block) {
  const desktopToggle = document.querySelector('.visibility-toggle[data-device="desktop"]');
  const mobileToggle = document.querySelector('.visibility-toggle[data-device="mobile"]');
  
  if (desktopToggle) desktopToggle.checked = block.dataset.visibilityDesktop !== 'hidden';
  if (mobileToggle) mobileToggle.checked = block.dataset.visibilityMobile !== 'hidden';
}

function checkEmptyBlocks() {
  const emptyState = document.getElementById('empty-state');
  if (!emptyState) return;
  if (!document.querySelector('.dropped-block')) {
    emptyState.style.display = 'flex';
  } else {
    emptyState.style.display = 'none';
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
  // Selection Logic
  block.addEventListener('click', (e) => {
    e.stopPropagation();
    clearSelected();
    block.classList.add('selected');
    
    // Auto-focus editable if it's a text/heading block
    const editable = block.querySelector('[contenteditable="true"]');
    if (editable && (e.target === block || e.target.classList.contains('dropped-block-inner'))) {
      editable.focus();
    }
  });

  // Copy / duplicate icon
  const copyBtn = block.querySelector('.copy-btn');
  if (copyBtn) {
    copyBtn.addEventListener('click', e => {
      e.stopPropagation();
      duplicateBlock(block);
    });
  }

  // Reorder buttons
  const upBtn = block.querySelector('.move-up-btn');
  const downBtn = block.querySelector('.move-down-btn');
  if (upBtn) upBtn.addEventListener('click', e => { e.stopPropagation(); moveBlockUp(block); });
  if (downBtn) downBtn.addEventListener('click', e => { e.stopPropagation(); moveBlockDown(block); });

  // Drag handle
  const handle = block.querySelector('.drag-handle');
  if (handle) {
    // ── Drag & Drop Reordering (Handle Based) ──────────────────────────
    handle.addEventListener('mousedown', () => { 
        block.setAttribute('draggable', 'true'); 
    });
    handle.addEventListener('mouseup', () => { 
        block.removeAttribute('draggable'); 
    });

    block.addEventListener('dragstart', e => {
      if (!block.hasAttribute('draggable')) {
        e.preventDefault();
        return;
      }
      block.classList.add('dragging');
      window.reorderBlock = block;
      e.dataTransfer.setData('text/plain', '');
      e.dataTransfer.effectAllowed = 'move';
    });

    block.addEventListener('dragend', () => {
      block.classList.remove('dragging');
      block.removeAttribute('draggable');
      window.reorderBlock = null;
      const indicator = document.getElementById('drop-indicator');
      if (indicator) indicator.style.display = 'none';
    });
  }

  // Individual Settings Handlers
  const h1 = block.querySelector('h1[contenteditable]');
  if (h1) {
    h1.addEventListener('click', (e) => { e.stopPropagation(); openHeadingSettings(h1); });
    h1.addEventListener('focus', (e) => { 
        clearSelected(); 
        block.classList.add('selected'); 
        openHeadingSettings(h1); 
    });
  }

  const p = block.querySelector('p[contenteditable]');
  if (p) {
    p.addEventListener('click', (e) => { e.stopPropagation(); openTextSettings(p); });
    p.addEventListener('focus', (e) => { 
        clearSelected(); 
        block.classList.add('selected'); 
        openTextSettings(p); 
    });
  }

  const spanEl = block.querySelector('span[contenteditable]');
  if (spanEl) {
    spanEl.addEventListener('click', (e) => { e.stopPropagation(); openSpanSettings(spanEl); });
    spanEl.addEventListener('focus', (e) => { 
        clearSelected(); 
        block.classList.add('selected'); 
        openSpanSettings(spanEl); 
    });
  }

  const btn = block.querySelector('.dropped-btn');
  if (btn) btn.addEventListener('click', e => { e.preventDefault(); e.stopPropagation(); openButtonSettings(btn); });

  const hr = block.querySelector('.editor-divider');
  if (hr) {
    const dividerWrapper = hr.closest('.dropped-block-inner');
    if (dividerWrapper) dividerWrapper.addEventListener('click', (e) => { e.stopPropagation(); openDividerSettings(hr); });
  }

  const img = block.querySelector('.editor-image');
  if (img) img.addEventListener('click', (e) => { e.stopPropagation(); openImageSettings(img); });

  const video = block.querySelector('.editor-video');
  if (video) video.addEventListener('click', (e) => { e.stopPropagation(); openVideoSettings(video); });

  const inventory = block.querySelector('.editor-inventory');
  if (inventory) inventory.addEventListener('click', (e) => { e.stopPropagation(); openInventorySettings(inventory); });

  const search = block.querySelector('.editor-search');
  if (search) search.addEventListener('click', (e) => { e.stopPropagation(); openSearchSettings(search); });

  const form = block.querySelector('.editor-form');
  if (form) form.addEventListener('click', (e) => { e.stopPropagation(); openFormSettings(form); });

  const acc = block.querySelector('.editor-accordion');
  if (acc) acc.addEventListener('click', (e) => { e.stopPropagation(); openAccordionSettings(acc); });

  const spacer = block.querySelector('.editor-spacer');
  if (spacer) spacer.addEventListener('click', (e) => { e.stopPropagation(); openSpacerSettings(spacer); });

  const card = block.querySelector('.editor-card');
  if (card) card.addEventListener('click', (e) => { e.stopPropagation(); openCardSettings(card); });

  const iframe = block.querySelector('.editor-iframe');
  if (iframe) iframe.addEventListener('click', (e) => { e.stopPropagation(); openIFrameSettings(iframe); });

  const icon = block.querySelector('.editor-icon');
  if (icon) icon.addEventListener('click', (e) => { e.stopPropagation(); openIconSettings(icon); });

  const cart = block.querySelector('.editor-cart');
  if (cart) cart.addEventListener('click', (e) => { e.stopPropagation(); openCartSettings(cart); });

  // Initialize Nested Drop Zones (for layout blocks)
  block.querySelectorAll('.col-drop-zone').forEach(zone => {
    attachDropZoneListeners(zone);
  });

  const col3 = block.querySelector('.editor-3col');
  if (col3) col3.addEventListener('click', (e) => { if (e.target === col3 || e.target.classList.contains('col-drop-zone')) { e.stopPropagation(); open3ColSettings(col3); } });

  const col2 = block.querySelector('.editor-2col');
  if (col2) col2.addEventListener('click', (e) => { if (e.target === col2 || e.target.classList.contains('col-drop-zone')) { e.stopPropagation(); open2ColSettings(col2); } });

  const container = block.querySelector('.editor-container');
  if (container) container.addEventListener('click', (e) => { if (e.target === container) { e.stopPropagation(); openContainerSettings(container); } });
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

    const afterElement = getDragAfterElement(col, e.clientY, e.clientX);

    if (window.dragType) {
      if (typeof createAndInsertBlock === 'function') {
        createAndInsertBlock(window.dragType, col, afterElement);
      }
    } else if (window.reorderBlock) {
      if (afterElement == null) col.appendChild(window.reorderBlock);
      else col.insertBefore(window.reorderBlock, afterElement);
      if (typeof saveHistory === 'function') saveHistory();
    }

    window.dragType = null;
    window.reorderBlock = null;
  });
}

window.renderExistingContent = function(content) {
  console.log("Rendering content:", content);
  const container = document.getElementById('blocks-container');
  if (!container) {
    console.error("Blocks container not found!");
    return;
  }
  if (!content) return;

  // Handle string content if necessary
  if (typeof content === 'string') {
    try {
      content = JSON.parse(content);
      console.log("Parsed content string successfully");
    } catch (e) {
      console.error("Failed to parse content string:", e);
      return;
    }
  }

  container.innerHTML = ''; // Clear existing
  
  // Re-add drop indicator
  const indicator = document.createElement('div');
  indicator.id = 'drop-indicator';
  indicator.style.display = 'none';
  container.appendChild(indicator);

  if (Array.isArray(content)) {
    content.forEach((data, index) => {
      const block = renderBlockData(data);
      if (block) {
        container.appendChild(block);
        attachBlockListeners(block);
      }
    });
  }
  
  checkEmptyBlocks();
};

function renderBlockData(data) {
  let block = null;
  
  switch(data.type) {
    case 'heading':
      if (typeof dropHeadingBlock === 'function') {
        block = dropHeadingBlock(true);
        const h1 = block.querySelector('h1');
        h1.innerText = data.text || '';
        h1.style.textAlign = data.textAlign || 'left';
        h1.style.color = data.color || '';
        h1.style.fontSize = data.fontSize || '';
        h1.dataset.cssClasses = data.cssClasses || '';
      }
      break;
    case 'text':
      if (typeof dropTextBlock === 'function') {
        block = dropTextBlock(true);
        const p = block.querySelector('p');
        p.innerText = data.text || '';
        p.style.color = data.color || '';
        p.style.fontSize = data.fontSize || '';
        p.dataset.cssClasses = data.cssClasses || '';
      }
      break;
    case 'span':
      if (typeof dropSpanBlock === 'function') {
        block = dropSpanBlock(true);
        const span = block.querySelector('span');
        span.innerText = data.text || '';
        span.style.color = data.color || '';
        span.style.fontSize = data.fontSize || '';
      }
      break;
    case 'button':
      if (typeof dropButtonBlock === 'function') {
        block = dropButtonBlock(true);
        const btn = block.querySelector('.dropped-btn');
        btn.innerText = data.text || '';
        btn.dataset.theme = data.theme || 'red';
        btn.dataset.bstyle = data.style || 'solid';
        btn.dataset.size = data.size || 'medium';
        btn.setAttribute('href', data.href || '#');
        if (data.newTab) btn.setAttribute('target', '_blank');
        if (data.fullWidth) btn.classList.add('full-width');
        
        const wrapper = btn.closest('.dropped-block-inner');
        if (wrapper) {
            const alignMap = { 'left': 'flex-start', 'center': 'center', 'right': 'flex-end' };
            wrapper.style.justifyContent = alignMap[data.align] || 'center';
        }
      }
      break;
    case 'image':
      if (typeof dropImageBlock === 'function') {
        block = dropImageBlock(true);
        const img = block.querySelector('.editor-image');
        img.src = data.src || '';
        img.alt = data.alt || '';
        img.style.width = data.width || '100%';
        img.style.height = data.height || 'auto';
        img.dataset.cssClasses = data.cssClasses || '';
        
        const wrapper = img.closest('.dropped-block-inner');
        if (wrapper) {
            wrapper.style.display = 'flex';
            const alignMap = { 'left': 'flex-start', 'center': 'center', 'right': 'flex-end' };
            wrapper.style.justifyContent = alignMap[data.align] || 'flex-start';
        }
      }
      break;
    case 'divider':
      if (typeof dropDividerBlock === 'function') {
        block = dropDividerBlock(true);
        const hr = block.querySelector('.editor-divider');
        hr.style.borderColor = data.color || '';
        hr.dataset.cssClasses = data.cssClasses || '';
      }
      break;
    case 'spacer':
      if (typeof dropSpacerBlock === 'function') {
        block = dropSpacerBlock(true);
        const spacer = block.querySelector('.editor-spacer');
        spacer.dataset.heightDesktop = data.heightDesktop || '10';
        spacer.dataset.heightMobile = data.heightMobile || '10';
        spacer.dataset.display = data.display || 'all';
        spacer.style.height = data.heightDesktop + 'px';
      }
      break;
    case 'container':
      if (typeof dropContainerBlock === 'function') {
        block = dropContainerBlock(true);
        const container = block.querySelector('.editor-container');
        container.innerHTML = ''; // Clear default
        container.style.paddingTop = data.paddingTop || '20px';
        container.style.paddingBottom = data.paddingBottom || '20px';
        container.style.backgroundColor = data.backgroundColor || 'transparent';
        container.style.flexDirection = data.flexDirection || 'column';
        container.style.justifyContent = data.justifyContent || 'flex-start';
        container.style.alignItems = data.alignItems || 'stretch';
        
        if (data.blocks) {
          data.blocks.forEach(childData => {
            const childBlock = renderBlockData(childData);
            if (childBlock) {
              container.appendChild(childBlock);
              attachBlockListeners(childBlock);
            }
          });
        }
      }
      break;
    case '2col':
    case '3col':
      const fn = data.type === '2col' ? drop2ColBlock : drop3ColBlock;
      if (typeof fn === 'function') {
        block = fn(true);
        const colWrapper = block.querySelector('.editor-2col, .editor-3col');
        colWrapper.style.gap = data.gap || '20px';
        
        const zones = colWrapper.querySelectorAll('.col-drop-zone');
        zones.forEach((zone, i) => {
          zone.innerHTML = ''; // Clear default
          if (data.columns && data.columns[i]) {
            data.columns[i].forEach(childData => {
              const childBlock = renderBlockData(childData);
              if (childBlock) {
                zone.appendChild(childBlock);
                attachBlockListeners(childBlock);
              }
            });
          }
        });
      }
      break;
    case 'icon':
      if (typeof dropIconBlock === 'function') {
        block = dropIconBlock(true);
        const icon = block.querySelector('i');
        if (data.iconClass) icon.className = data.iconClass;
        icon.style.color = data.color || '';
        icon.style.fontSize = data.fontSize || '';
      }
      break;
    case 'accordion':
      if (typeof dropAccordionBlock === 'function') block = dropAccordionBlock(true);
      break;
    case 'card':
      if (typeof dropCardBlock === 'function') {
        block = dropCardBlock(true);
        const card = block.querySelector('.editor-card');
        card.style.backgroundColor = data.backgroundColor || 'transparent';
        card.style.width = data.width || '100%';
        
        const cardImg = card.querySelector('.editor-image');
        if (cardImg && data.image) {
            cardImg.src = data.image.src || '';
            cardImg.alt = data.image.alt || '';
            cardImg.style.width = data.image.width || '100%';
            cardImg.style.height = data.image.height || 'auto';
        }
        
        const cardBody = card.querySelector('.card-body');
        if (cardBody && data.blocks) {
            cardBody.innerHTML = '';
            data.blocks.forEach(childData => {
                const childBlock = renderBlockData(childData);
                if (childBlock) {
                    cardBody.appendChild(childBlock);
                    attachBlockListeners(childBlock);
                }
            });
        }
      }
      break;
    case 'inventory':
      if (typeof dropInventoryBlock === 'function') block = dropInventoryBlock(true);
      break;
    case 'video':
      if (typeof dropVideoBlock === 'function') {
        block = dropVideoBlock(true);
        const v = block.querySelector('.editor-video');
        v.dataset.host = data.host || 'youtube';
        v.dataset.url = data.url || '';
        v.dataset.poster = data.poster || '';
        v.dataset.autoplay = data.autoplay || false;
        v.dataset.loop = data.loop || false;
        v.dataset.controls = data.controls !== undefined ? data.controls : true;
        if (typeof updateVideoPreview === 'function') updateVideoPreview(v);
      }
      break;
    case 'carousel':
      if (typeof dropCarouselBlock === 'function') block = dropCarouselBlock(true);
      break;
    case 'tabs':
      if (typeof dropTabsBlock === 'function') block = dropTabsBlock(true);
      break;
    case 'search':
      if (typeof dropSearchBlock === 'function') block = dropSearchBlock(true);
      break;
    case 'cart':
      if (typeof dropCartBlock === 'function') block = dropCartBlock(true);
      break;
    case 'html-css':
      if (typeof dropHtmlCssBlock === 'function') block = dropHtmlCssBlock(true);
      break;
  }
  
  return block;
}
