// ── Container Settings Panel ───────────────────────────────────────────────────

function openContainerSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  const panel = document.getElementById('container-settings-panel');
  if (panel) panel.style.display = 'block';

  // Sync inputs
  document.getElementById('container-padding-top').value = el.style.paddingTop ? parseInt(el.style.paddingTop) : 20;
  document.getElementById('container-padding-bottom').value = el.style.paddingBottom ? parseInt(el.style.paddingBottom) : 20;
  // Sync background color
  const curBg = el.style.backgroundColor;
  if (curBg) {
    document.getElementById('container-bg').value = rgbToHex(curBg) || '#ffffff';
  }

  // Sync Flex
  document.getElementById('container-flex-direction').value = el.style.flexDirection || 'column';
  document.getElementById('container-justify-content').value = el.style.justifyContent || 'flex-start';
  document.getElementById('container-align-items').value = el.style.alignItems || 'stretch';
}

// Back / Cancel
document.getElementById('container-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('container-cancel-btn')?.addEventListener('click', closeAllPanels);

// Padding Top
document.getElementById('container-padding-top')?.addEventListener('input', e => {
  if (activeEl) activeEl.style.paddingTop = (e.target.value || 0) + 'px';
});

// Padding Bottom
document.getElementById('container-padding-bottom')?.addEventListener('input', e => {
  if (activeEl) activeEl.style.paddingBottom = (e.target.value || 0) + 'px';
});

// Background Color
document.getElementById('container-bg')?.addEventListener('input', e => {
  if (activeEl) activeEl.style.backgroundColor = e.target.value;
});

// Flex Controls
document.getElementById('container-flex-direction')?.addEventListener('change', e => {
  if (activeEl) {
    activeEl.style.display = 'flex';
    activeEl.style.flexDirection = e.target.value;
  }
});

document.getElementById('container-justify-content')?.addEventListener('change', e => {
  if (activeEl) activeEl.style.justifyContent = e.target.value;
});

document.getElementById('container-align-items')?.addEventListener('change', e => {
  if (activeEl) activeEl.style.alignItems = e.target.value;
});

// Remove
document.getElementById('container-remove-btn')?.addEventListener('click', () => {
  if (activeEl) {
    activeEl.closest('.dropped-block').remove();
    if (typeof checkEmptyBlocks === 'function') checkEmptyBlocks();
  }
  closeAllPanels();
});

// ── Drop Container Block ───────────────────────────────────────────────────────

function dropContainerBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Container <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="background: transparent; border: none; padding: 0;">
      <div class="editor-container col-drop-zone" style="min-height: 100px; border: 1px dashed #ccc; padding: 20px; background: #ffffff; width: 100%;">
      </div>
    </div>`;

  const containerEl = block.querySelector('.editor-container');
  if (containerEl) setupContainerListeners(containerEl);

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  if (containerEl) openContainerSettings(containerEl);
}

function setupContainerListeners(col) {
  attachDropZoneListeners(col);
  // Optional: keep specialized border color logic if desired, 
  // but for now, global one is fine.
}
