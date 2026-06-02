// ── Accordion Settings Panel ──────────────────────────────────────────────────

function openAccordionSettings(el) {
  closeAllPanels();
  activeEl = el.classList.contains('editor-accordion') ? el : el.closest('.editor-accordion') || el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  document.getElementById('accordion-settings-panel').style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  renderAccordionItemList();
}

function renderAccordionItemList() {
  const list = document.getElementById('accordion-item-list');
  if (!list || !activeEl) return;
  list.innerHTML = '';
  activeEl.querySelectorAll('.acc-item').forEach((item, i) => {
    const row = document.createElement('div');
    row.style.cssText = 'display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #f1f3f5';
    const hdr = item.querySelector('.acc-header');
    row.innerHTML = `
      <span style="flex:1;font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${hdr ? hdr.innerText : 'Item ' + (i+1)}</span>
      <button type="button" class="btn btn-sm btn-outline-danger accordion-item-remove" data-index="${i}" style="padding:2px 8px;font-size:11px"><i class="fa-solid fa-xmark"></i></button>
    `;
    list.appendChild(row);
  });
  list.querySelectorAll('.accordion-item-remove').forEach(btn => {
    btn.addEventListener('click', () => {
      if (!activeEl) return;
      const idx = parseInt(btn.dataset.index);
      const items = activeEl.querySelectorAll('.acc-item');
      if (items[idx]) {
        items[idx].remove();
        renderAccordionItemList();
        if (typeof saveHistory === 'function') saveHistory();
      }
    });
  });
}

// Back / Cancel
document.getElementById('as-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('as-cancel-btn')?.addEventListener('click', closeAllPanels);

// Add Item
document.getElementById('as-add-item')?.addEventListener('click', () => {
  if (activeEl) {
    const newItem = document.createElement('div');
    newItem.className = 'acc-item';
    newItem.style.border = '1px solid #dee2e6';
    newItem.style.marginBottom = '5px';
    newItem.style.borderRadius = '4px';
    newItem.innerHTML = `
      <div class="acc-header" style="padding:10px;background:#f8f9fa;cursor:pointer;font-weight:600;" contenteditable="true">New Item Title</div>
      <div class="acc-content col-drop-zone" style="padding:15px;display:none;min-height:50px;">
        <p contenteditable="true" spellcheck="false" data-placeholder="Enter content here..." style="min-height:40px; padding:5px; margin:0; width:100%; display:block; outline:none;"></p>
      </div>
    `;
    activeEl.appendChild(newItem);
    
    const header = newItem.querySelector('.acc-header');
    const content = newItem.querySelector('.acc-content');
    
    // Do NOT use stopPropagation on contenteditable elements - it blocks text input
    // Instead, use keyboard event for toggling
    header.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        const isVisible = content.style.display === 'block';
        content.style.display = isVisible ? 'none' : 'block';
      }
    });
    
    attachDropZoneListeners(content);
    renderAccordionItemList();
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Remove block
document.getElementById('as-remove-btn')?.addEventListener('click', () => {
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

// ── Drop Accordion Block ──────────────────────────────────────────────────────

function dropAccordionBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Accordion <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <div class="editor-accordion" style="width: 100%;">
        <div class="acc-item" style="border: 1px solid #dee2e6; margin-bottom: 5px; border-radius: 4px;">
          <div class="acc-header" style="padding:10px; background:#f8f9fa; cursor:pointer; font-weight:600; min-height:40px; display:block;" contenteditable="true">Accordion Item #1</div>
          <div class="acc-content col-drop-zone" style="padding:15px; min-height:50px;">
            <p contenteditable="true" spellcheck="false" data-placeholder="Enter content here..." style="min-height:40px; padding:5px; margin:0; width:100%; display:block; outline:none;"></p>
          </div>
        </div>
      </div>
    </div>`;

  const acc = block.querySelector('.editor-accordion');
  const header = block.querySelector('.acc-header');
  const content = block.querySelector('.acc-content');

  // Do NOT use stopPropagation on contenteditable elements - it blocks text input
  // Instead, use keyboard event for toggling
  header.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const isVisible = content.style.display === 'block';
      content.style.display = isVisible ? 'none' : 'block';
    }
  });

  attachDropZoneListeners(content);

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  if (acc) { openAccordionSettings(acc); renderAccordionItemList(); }
  if (typeof saveHistory === 'function') saveHistory();
}
