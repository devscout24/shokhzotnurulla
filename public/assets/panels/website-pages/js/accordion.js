// ── Accordion Settings Panel ──────────────────────────────────────────────────

function openAccordionSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  document.getElementById('accordion-settings-panel').style.display = 'block';

  // Sync fields
  document.getElementById('as-classes').value = el.dataset.cssClasses || '';
}

// Back / Cancel
document.getElementById('as-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('as-cancel-btn').addEventListener('click', closeAllPanels);

// Add Item
document.getElementById('as-add-item').addEventListener('click', () => {
  if (activeEl) {
    addAccordionItem(activeEl);
  }
});

// CSS classes
document.getElementById('as-classes').addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.cssClasses = e.target.value;
    activeEl.className = 'editor-accordion ' + e.target.value;
  }
});

// Remove block
document.getElementById('as-remove-btn').addEventListener('click', () => {
  if (activeEl) {
    activeEl.closest('.dropped-block').remove();
    checkEmptyBlocks();
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
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <div class="editor-accordion w-100">
        <!-- Initial item -->
        <div class="acc-item open">
          <div class="acc-header" contenteditable="true" spellcheck="false">Accordion Item #1</div>
          <div class="acc-content col-drop-zone" style="min-height: 30px;">
            This is the content for the first accordion item. You can edit this text.
          </div>
        </div>
      </div>
    </div>`;

  const dropZone = block.querySelector('.col-drop-zone');
  if (dropZone) attachDropZoneListeners(dropZone);

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const acc = block.querySelector('.editor-accordion');
  setupAccordionListeners(acc);
  openAccordionSettings(acc);
}

function setupAccordionListeners(acc) {
  acc.addEventListener('click', e => {
    const header = e.target.closest('.acc-header');
    if (header) {
      // Toggle logic
      const item = header.closest('.acc-item');
      const isOpen = item.classList.contains('open');

      // Close others (optional, common for accordions)
      acc.querySelectorAll('.acc-item').forEach(i => i.classList.remove('open'));

      if (!isOpen) {
        item.classList.add('open');
      }
    }
  });
}

function addAccordionItem(accEl) {
  const itemCount = accEl.querySelectorAll('.acc-item').length + 1;
  const item = document.createElement('div');
  item.className = 'acc-item';
  item.innerHTML = `
    <div class="acc-header" contenteditable="true" spellcheck="false">Accordion Item #${itemCount}</div>
    <div class="acc-content col-drop-zone" style="min-height: 30px;">
      New content for item #${itemCount}. Click to edit.
    </div>
  `;
  const dropZone = item.querySelector('.col-drop-zone');
  if (dropZone) attachDropZoneListeners(dropZone);
  accEl.appendChild(item);
}


