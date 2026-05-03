// ── Accordion Settings Panel ──────────────────────────────────────────────────

function openAccordionSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  document.getElementById('accordion-settings-panel').style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);
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
      <div class="acc-content col-drop-zone" style="padding:15px;display:none;min-height:50px;"></div>
    `;
    activeEl.appendChild(newItem);
    
    const header = newItem.querySelector('.acc-header');
    const content = newItem.querySelector('.acc-content');
    
    header.addEventListener('click', (e) => {
      e.stopPropagation();
      const isVisible = content.style.display === 'block';
      content.style.display = isVisible ? 'none' : 'block';
    });
    
    attachDropZoneListeners(content);
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
          <div class="acc-header" style="padding:10px; background:#f8f9fa; cursor:pointer; font-weight:600;" contenteditable="true">Accordion Item #1</div>
          <div class="acc-content col-drop-zone" style="padding:15px; min-height:50px;"></div>
        </div>
      </div>
    </div>`;

  const acc = block.querySelector('.editor-accordion');
  const header = block.querySelector('.acc-header');
  const content = block.querySelector('.acc-content');

  header.addEventListener('click', (e) => {
    e.stopPropagation();
    const isVisible = content.style.display === 'block';
    content.style.display = isVisible ? 'none' : 'block';
  });

  attachDropZoneListeners(content);

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  if (acc) openAccordionSettings(acc);
  if (typeof saveHistory === 'function') saveHistory();
}
