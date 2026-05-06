// ── Container Settings Panel ───────────────────────────────────────────────────

function openContainerSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  const panel = document.getElementById('container-settings-panel');
  if (panel) panel.style.display = 'block';

  // Sync Visibility
  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  // Sync inputs
  document.getElementById('container-padding').value = el.style.paddingTop ? parseInt(el.style.paddingTop) : 40;
  
  const curBg = el.style.backgroundColor;
  if (curBg) {
    document.getElementById('container-bg').value = rgbToHex(curBg) || '#ffffff';
  }
}

// Back / Cancel
document.getElementById('container-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('container-cancel-btn')?.addEventListener('click', closeAllPanels);

// Padding Vertical
document.getElementById('container-padding')?.addEventListener('input', e => {
  if (activeEl) {
    const val = (e.target.value || 0) + 'px';
    activeEl.style.paddingTop = val;
    activeEl.style.paddingBottom = val;
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Background Color
document.getElementById('container-bg')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.backgroundColor = e.target.value;
    if (typeof saveHistory === 'function') saveHistory();
  }
});

// Remove block
document.getElementById('container-remove-btn')?.addEventListener('click', () => {
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
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="background: transparent; border: none; padding: 0;">
      <div class="editor-container col-drop-zone" style="min-height: 100px; padding: 40px; background: #ffffff; width: 100%;">
        <p contenteditable="true" spellcheck="false" data-placeholder="Add content here or drag blocks..." style="margin:0; color:#6c757d; font-size:14px; min-height:24px; outline:none;"></p>
      </div>
    </div>`;

  const containerEl = block.querySelector('.editor-container');
  const placeholderText = block.querySelector('[contenteditable]');
  
  if (containerEl) {
    attachDropZoneListeners(containerEl);
    
    // Handle placeholder behavior
    if (placeholderText) {
      placeholderText.classList.add('is-placeholder');
      
      // Hide the CSS-based placeholder if the container has dropped blocks
      const observer = new MutationObserver(() => {
        const hasBlocks = containerEl.querySelector('.dropped-block') !== null;
        if (hasBlocks) {
          placeholderText.classList.add('d-none');
        } else {
          placeholderText.classList.remove('d-none');
        }
      });
      observer.observe(containerEl, { childList: true, subtree: true });
    }
  }

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  if (containerEl) openContainerSettings(containerEl);
  if (typeof saveHistory === 'function') saveHistory();
}
