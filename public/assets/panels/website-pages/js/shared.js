// ── Shared state & utilities ──────────────────────────────────────────────────

window.activeEl = null; // currently selected element

function clearSelected() {
  document.querySelectorAll('.dropped-block.selected')
    .forEach(b => b.classList.remove('selected'));
}

function openPanel(id) {
  // Switch to 'Edit Block' tab
  const editTab = document.querySelector('.sidebar-tab[data-tab="edit"]');
  const addTab = document.querySelector('.sidebar-tab[data-tab="add"]');
  if (editTab) {
      editTab.classList.add('active');
      addTab.classList.remove('active');
  }

  // Save currently focused element so focus isn't stolen by panel DOM changes
  const previouslyFocused = document.activeElement;
  const wasContentEditable = previouslyFocused && previouslyFocused.isContentEditable;

  closeAllPanels(true); // pass true to prevent recursive tab switching
  const panel = document.getElementById(id);
  if (panel) panel.style.display = 'block';
  const defaultContent = document.getElementById('sidebar-default-content');
  if (defaultContent) defaultContent.style.display = 'none';

  // Restore focus to the contenteditable element after panel opens
  if (wasContentEditable) {
    setTimeout(() => { previouslyFocused.focus(); }, 0);
  }
}

function closeAllPanels(keepTab = false) {
  if (!keepTab) {
      const addTab = document.querySelector('.sidebar-tab[data-tab="add"]');
      const editTab = document.querySelector('.sidebar-tab[data-tab="edit"]');
      if (addTab) {
          addTab.classList.add('active');
          editTab.classList.remove('active');
      }
  }

  // Close every settings panel by ID
  const panels = [
    'heading-settings-panel', 'text-settings-panel', 'button-settings-panel', 'divider-settings-panel', 
    'image-settings-panel', 'accordion-settings-panel', 'spacer-settings-panel', 'card-settings-panel', 
    '3col-settings-panel', '2col-settings-panel', 'container-settings-panel', 'icon-settings-panel', 
    'cart-settings-panel', 'span-settings-panel', 'iframe-settings-panel', 'video-settings-panel', 
    'inventory-settings-panel', 'form-settings-panel', 'search-settings-panel', 'carousel-settings-panel', 
    'tabs-settings-panel', 'map-settings-panel', 'overlay-settings-panel', 'html-settings-panel', 
    'css-settings-panel', 'blog-settings-panel', 'content-block-settings-panel', 
    'body-types-settings-panel', 'plugin-settings-panel'
  ];
  
  panels.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });

  const defaultContent = document.getElementById('sidebar-default-content');
  if (defaultContent) defaultContent.style.display = 'block';

  activeEl = null;
  clearSelected();
}

// Sidebar Tab Switching
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sidebar-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const type = tab.dataset.tab;
            if (type === 'add') {
                closeAllPanels();
            } else {
                // If we're already on an active element, the panel is already there.
                // Otherwise, keep it as is or show a 'select something' message.
                if (!activeEl) {
                    closeAllPanels(true); // show default content but keep 'edit' tab
                    const addTab = document.querySelector('.sidebar-tab[data-tab="add"]');
                    const editTab = document.querySelector('.sidebar-tab[data-tab="edit"]');
                    addTab.classList.remove('active');
                    editTab.classList.add('active');
                }
            }
        });
    });
});

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
  // ── Selection: Click anywhere on block to select it ──────────────────
  block.addEventListener('click', (e) => {
    // If clicking directly on interactive elements,
    // let the browser handle focus natively
    const interactive = e.target.closest('[contenteditable="true"], input, textarea, select, button');
    if (interactive) {
      // Just mark as selected — browser handles the rest
      clearSelected();
      block.classList.add('selected');
      block.classList.add('is-selected');
      return;
    }

    // Clicking on non-text parts: select block and open settings
    e.stopPropagation();
    clearSelected();
    block.classList.add('selected');
    block.classList.add('is-selected');
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
      handle.addEventListener('mousedown', (e) => {
          block.setAttribute('draggable', 'true');
      });

      block.addEventListener('dragstart', e => {
        const isHandle = e.target.closest('.drag-handle') || e.target.classList.contains('drag-handle');
        if (!isHandle && !block.getAttribute('draggable')) {
          e.preventDefault();
          return false;
        }
        block.classList.add('dragging');
        window.reorderBlock = block;
        e.dataTransfer.setData('text/plain', 'reorder');
        e.dataTransfer.effectAllowed = 'move';
      });

      block.addEventListener('dragend', () => {
        block.classList.remove('dragging');
        block.setAttribute('draggable', 'false');
        window.reorderBlock = null;
        const indicator = document.getElementById('drop-indicator');
        if (indicator) indicator.style.display = 'none';
      });
    }

    // Hierarchy Buttons
    const parentBtn = block.querySelector('.select-parent-btn');
    if (parentBtn) {
        parentBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const parent = block.parentElement.closest('.dropped-block');
            if (parent) {
                clearSelected();
                parent.classList.add('is-selected', 'selected');
                triggerBlockSettings(parent);
            }
        });
    }

    const childBtn = block.querySelector('.select-child-btn');
    if (childBtn) {
        childBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const child = block.querySelector('.dropped-block');
            if (child) {
                clearSelected();
                child.classList.add('is-selected', 'selected');
                triggerBlockSettings(child);
            }
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

  const blog = block.querySelector('.editor-blog');
  if (blog) blog.addEventListener('click', (e) => { e.stopPropagation(); openBlogSettings(blog); });

  const contentBlock = block.querySelector('.editor-content_block');
  if (contentBlock) contentBlock.addEventListener('click', (e) => { e.stopPropagation(); openContentBlockSettings(contentBlock); });

  const bodyTypes = block.querySelector('.editor-body_types');
  if (bodyTypes) bodyTypes.addEventListener('click', (e) => { e.stopPropagation(); openBodyTypesSettings(bodyTypes); });

  const mapHours = block.querySelector('.editor-map_hours');
  if (mapHours) mapHours.addEventListener('click', (e) => { e.stopPropagation(); openMapSettings(mapHours); });

  const plugin = block.querySelector('.editor-plugin');
  if (plugin) plugin.addEventListener('click', (e) => { e.stopPropagation(); openPluginSettings(plugin); });

  const carousel = block.querySelector('.editor-carousel');
  if (carousel) carousel.addEventListener('click', (e) => { e.stopPropagation(); openCarouselSettings(carousel); });

  const tabs = block.querySelector('.editor-tabs');
  if (tabs) tabs.addEventListener('click', (e) => { e.stopPropagation(); openTabsSettings(tabs); });

  const overlay = block.querySelector('.editor-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { e.stopPropagation(); openOverlaySettings(overlay); });

  // Initialize Nested Drop Zones (for layout blocks)
  block.querySelectorAll('.col-drop-zone').forEach(zone => {
    attachDropZoneListeners(zone);
  });

  // Layout Block Settings Handlers (Container, 2Col, 3Col)
  const layoutEl = block.querySelector('.editor-3col, .editor-2col, .editor-container');
  if (layoutEl) {
    block.addEventListener('click', (e) => {
        // If clicking exactly on the layout block or its drop zone (but not a nested block)
        if (e.target === layoutEl || e.target.classList.contains('col-drop-zone')) {
            e.stopPropagation();
            clearSelected();
            block.classList.add('selected');
            
            if (layoutEl.classList.contains('editor-3col')) if (typeof open3ColSettings === 'function') open3ColSettings(layoutEl);
            if (layoutEl.classList.contains('editor-2col')) if (typeof open2ColSettings === 'function') open2ColSettings(layoutEl);
            if (layoutEl.classList.contains('editor-container')) if (typeof openContainerSettings === 'function') openContainerSettings(layoutEl);
        }
    });
  }
}

// ── Selection Highlight Engine ──────────────────
document.addEventListener('mousedown', (e) => {
    const block = e.target.closest('.dropped-block');
    const isSettingsPanel = e.target.closest('[id$="-settings-panel"]') || e.target.closest('.sidebar-right') || e.target.closest('.side-panel') || e.target.closest('.offcanvas');

    if (block) {
        // If clicking a block that is NOT already selected, update selection
        if (!block.classList.contains('selected')) {
            document.querySelectorAll('.dropped-block').forEach(b => b.classList.remove('is-selected', 'selected'));
            block.classList.add('is-selected', 'selected');
        }
    } else if (!isSettingsPanel && !e.target.closest('[contenteditable="true"]')) {
        // Clicked completely outside — deselect and close panels
        document.querySelectorAll('.dropped-block').forEach(b => b.classList.remove('is-selected', 'selected'));
        if (typeof closeAllPanels === 'function') closeAllPanels();
    }
});

// Trigger settings based on block content
function triggerBlockSettings(block) {
    const h1 = block.querySelector('h1');
    const p = block.querySelector('p');
    const btn = block.querySelector('.dropped-btn');
    const img = block.querySelector('.editor-image');
    const video = block.querySelector('.editor-video');
    const col2 = block.querySelector('.editor-2col');
    const col3 = block.querySelector('.editor-3col');
    const container = block.querySelector('.editor-container');

    if (h1 && typeof openHeadingSettings === 'function') openHeadingSettings(h1);
    else if (p && typeof openTextSettings === 'function') openTextSettings(p);
    else if (btn && typeof openButtonSettings === 'function') openButtonSettings(btn);
    else if (img && typeof openImageSettings === 'function') openImageSettings(img);
    else if (video && typeof openVideoSettings === 'function') openVideoSettings(video);
    else if (col2 && typeof open2ColSettings === 'function') open2ColSettings(col2);
    else if (col3 && typeof open3ColSettings === 'function') open3ColSettings(col3);
    else if (container && typeof openContainerSettings === 'function') openContainerSettings(container);
}

document.addEventListener('dragend', (e) => {
    const block = e.target.closest('.dropped-block');
    if (block) block.setAttribute('draggable', 'false');
});

// Prevent drag starting when clicking inside editable or interactive areas
document.addEventListener('dragstart', (e) => {
    if (e.target.closest('[contenteditable="true"], input, textarea, select, button')) {
        e.preventDefault();
        e.stopPropagation();
    }
}, true);

// Capture-phase: open settings panel when contenteditable is clicked.
// We do NOT call stopPropagation or preventDefault so the browser
// naturally places the cursor. Focus is restored by openPanel().
document.addEventListener('click', (e) => {
    const el = e.target.closest('[contenteditable="true"]');
    if (!el) return;

    const block = el.closest('.dropped-block');
    if (block) {
        clearSelected();
        block.classList.add('selected', 'is-selected');
    }

    if (el.tagName === 'H1') {
        if (typeof openHeadingSettings === 'function') openHeadingSettings(el);
    } else if (el.tagName === 'P') {
        if (typeof openTextSettings === 'function') openTextSettings(el);
    } else if (el.tagName === 'SPAN') {
        if (typeof openSpanSettings === 'function') openSpanSettings(el);
    } else if (el.classList.contains('acc-header')) {
        if (typeof openAccordionSettings === 'function') openAccordionSettings(el);
    }
}, true); // capture phase


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
  let prev = block.previousElementSibling;
  // Skip over non-block helper elements
  while (prev && (prev.id === 'drop-indicator' || prev.classList.contains('editor-empty-state'))) {
    prev = prev.previousElementSibling;
  }
  if (prev) {
    block.parentNode.insertBefore(block, prev);
    if (typeof saveHistory === 'function') saveHistory();
  }
}

function moveBlockDown(block) {
  let next = block.nextElementSibling;
  // Skip over non-block helper elements
  while (next && (next.id === 'drop-indicator' || next.classList.contains('editor-empty-state'))) {
    next = next.nextElementSibling;
  }
  if (next) {
    block.parentNode.insertBefore(next, block); // Insert next sibling before current block = move current block down
    if (typeof saveHistory === 'function') saveHistory();
  }
}


// ── Free Movement Dragging ───────────────────────────────────────────────────
let isManualDragging = false;
let dragStartX, dragStartY, initialTop, initialLeft, manualDragBlock;

document.addEventListener('mousedown', e => {
  // Skip if clicking inside a contenteditable element — must not block text focus
  if (e.target.closest('[contenteditable="true"]')) return;

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
        block.dataset.blockType = 'Heading';
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
    case 'html':
      if (typeof dropHTMLBlock === 'function') {
        block = dropHTMLBlock(true);
        const htmlEl = block.querySelector('.editor-html');
        htmlEl.dataset.code = data.code || '';
        htmlEl.dataset.styleId = data.styleId || '';
        if (data.code) {
          htmlEl.innerHTML = data.code;
        }
        // Re-apply CSS if it was saved
        if (data.styleId && data.code) {
          const style = document.createElement('style');
          style.id = data.styleId;
          style.textContent = data.code;
          document.head.appendChild(style);
        }
      }
      break;
    case 'css':
      if (typeof dropCSSBlock === 'function') {
        block = dropCSSBlock(true);
        const cssEl = block.querySelector('.editor-css');
        cssEl.dataset.code = data.code || '';
        cssEl.dataset.styleId = data.styleId || '';
        if (data.code) {
          const style = document.createElement('style');
          style.id = data.styleId || ('css-' + Math.random().toString(36).substr(2, 9));
          style.textContent = data.code;
          document.head.appendChild(style);
          cssEl.dataset.styleId = style.id;
        }
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
      if (typeof dropAccordionBlock === 'function') {
        block = dropAccordionBlock(true);
        const acc = block.querySelector('.editor-accordion');
        if (data.items) {
          const container = acc.querySelector('.accordion-items') || acc;
          container.innerHTML = '';
          data.items.forEach(itemData => {
            const item = document.createElement('div');
            item.className = 'acc-item';
            item.innerHTML = `<div class="acc-header" contenteditable="true">${itemData.header}</div><div class="acc-content col-drop-zone"></div>`;
            container.appendChild(item);
            const contentZone = item.querySelector('.acc-content');
            if (itemData.blocks) {
              itemData.blocks.forEach(childData => {
                const childBlock = renderBlockData(childData);
                if (childBlock) { contentZone.appendChild(childBlock); attachBlockListeners(childBlock); }
              });
            }
          });
        }
      }
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
    case 'inventory':
      if (typeof dropInventoryBlock === 'function') {
        block = dropInventoryBlock(true);
        const el = block.querySelector('.editor-inventory');
        Object.assign(el.dataset, data);
      }
      break;
    case 'search':
      if (typeof dropSearchBlock === 'function') {
        block = dropSearchBlock(true);
        const el = block.querySelector('.editor-search');
        Object.assign(el.dataset, data);
        if (data.placeholder) {
          const input = el.querySelector('input');
          if (input) input.placeholder = data.placeholder;
        }
      }
      break;
    case 'form':
      if (typeof dropFormBlock === 'function') {
        block = dropFormBlock(true);
        const el = block.querySelector('.editor-form');
        Object.assign(el.dataset, data);
      }
      break;
    case 'blog':
      if (typeof dropBlogBlock === 'function') {
        block = dropBlogBlock(true);
        const el = block.querySelector('.editor-blog');
        Object.assign(el.dataset, data);
      }
      break;
    case 'content_block':
      if (typeof dropContentBlockBlock === 'function') {
        block = dropContentBlockBlock(true);
        const el = block.querySelector('.editor-content_block');
        Object.assign(el.dataset, data);
      }
      break;
    case 'body_types':
      if (typeof dropBodyTypesBlock === 'function') {
        block = dropBodyTypesBlock(true);
        const el = block.querySelector('.editor-body_types');
        Object.assign(el.dataset, data);
      }
      break;
    case 'plugin':
      if (typeof dropPluginBlock === 'function') {
        block = dropPluginBlock(true);
        const el = block.querySelector('.editor-plugin');
        Object.assign(el.dataset, data);
      }
      break;
    case 'map_hours':
      if (typeof dropMapHoursBlock === 'function') {
        block = dropMapHoursBlock(true);
        const el = block.querySelector('.editor-map_hours');
        Object.assign(el.dataset, data);
      }
      break;
    case 'map':
      if (typeof dropMapBlock === 'function') {
        block = dropMapBlock(true);
        const el = block.querySelector('.editor-map');
        Object.assign(el.dataset, data);
      }
      break;
    case 'carousel':
      if (typeof dropCarouselBlock === 'function') {
        block = dropCarouselBlock(true);
        const el = block.querySelector('.editor-carousel');
        Object.assign(el.dataset, data);
      }
      break;
    case 'tabs':
      if (typeof dropTabsBlock === 'function') {
        block = dropTabsBlock(true);
        const el = block.querySelector('.editor-tabs');
        Object.assign(el.dataset, data);
        const zone = el.querySelector('.col-drop-zone');
        if (zone && data.blocks) {
          zone.innerHTML = '';
          data.blocks.forEach(childData => {
            const childBlock = renderBlockData(childData);
            if (childBlock) { zone.appendChild(childBlock); attachBlockListeners(childBlock); }
          });
        }
      }
      break;
    case 'overlay':
      if (typeof dropOverlayBlock === 'function') {
        block = dropOverlayBlock(true);
        const el = block.querySelector('.editor-overlay');
        Object.assign(el.dataset, data);
        const zone = el.querySelector('.col-drop-zone');
        if (zone && data.blocks) {
          zone.innerHTML = '';
          data.blocks.forEach(childData => {
            const childBlock = renderBlockData(childData);
            if (childBlock) { zone.appendChild(childBlock); attachBlockListeners(childBlock); }
          });
        }
      }
      break;
    case 'cart':
      if (typeof dropCartBlock === 'function') {
        block = dropCartBlock(true);
        const el = block.querySelector('.editor-cart');
        if (data.text) {
            const span = el.querySelector('span');
            if (span) span.textContent = data.text;
        }
        // Cart usually doesn't have a direct href on the div, but we can store it in dataset
        Object.assign(el.dataset, data);
      }
      break;
    case 'iframe':
      if (typeof dropIFrameBlock === 'function') block = dropIFrameBlock(true);
      break;
    case 'html-css':
      if (typeof dropHtmlCssBlock === 'function') block = dropHtmlCssBlock(true);
      break;
  }
  
  return block;
}

// End of Shared JS
