// ── Image Settings Panel ─────────────────────────────────────────────────────

function openImageSettings(el) {
  closeAllPanels();
  window.activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  document.getElementById('image-settings-panel').style.display = 'block';

  // Sync fields
  document.getElementById('is-url').value = el.src || '';
  document.getElementById('is-alt').value = el.alt || '';
  document.getElementById('is-width').value = el.style.width ? parseInt(el.style.width) : 100;
  document.getElementById('is-classes').value = el.dataset.cssClasses || '';

  // Sync align buttons
  let currentAlign = 'left';
  if (el.closest('.editor-card')) {
    const as = el.style.alignSelf || 'flex-start';
    const revMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
    currentAlign = revMap[as] || 'left';
  } else {
    const parent = el.closest('.dropped-block-inner');
    const curAlign = parent ? (parent.style.justifyContent || 'flex-start') : 'flex-start';
    const alignMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
    currentAlign = alignMap[curAlign] || 'left';
  }

  document.querySelectorAll('.is-align-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.align === currentAlign)
  );
}

// Back / Cancel
document.getElementById('is-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('is-cancel-btn').addEventListener('click', closeAllPanels);

// Image URL
const urlInput = document.getElementById('is-url');
if (urlInput) {
  const updateImageSrc = () => {
    if (window.activeEl && window.activeEl.tagName === 'IMG') {
      const url = urlInput.value.trim();
      const finalUrl = url ? url : 'https://via.placeholder.com/300x150?text=No+Image';
      
      // Update both property and attribute
      window.activeEl.src = finalUrl;
      window.activeEl.setAttribute('src', finalUrl);
      
      // Update data attribute for extra consistency
      window.activeEl.dataset.src = finalUrl;
      
      if (window.saveHistory) window.saveHistory();
    }
  };
  urlInput.addEventListener('input', updateImageSrc);
  urlInput.addEventListener('change', updateImageSrc);
}

// Alt text
document.getElementById('is-alt').addEventListener('input', e => {
  if (window.activeEl) window.activeEl.alt = e.target.value;
});

// Width
document.getElementById('is-width').addEventListener('input', e => {
  if (window.activeEl && e.target.value) window.activeEl.style.width = e.target.value + '%';
});

// Align
document.querySelectorAll('.is-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.is-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (window.activeEl) {
      const align = btn.dataset.align;
      const map = { left: 'flex-start', center: 'center', right: 'flex-end' };
      const flexVal = map[align];

      // Save alignment in dataset for persistence
      window.activeEl.dataset.align = align;

      if (window.activeEl.closest('.editor-card')) {
        // If inside a card, use align-self on the image
        window.activeEl.style.alignSelf = flexVal;
        
        // If centered or right-aligned, ensure width isn't forced to 100% 
        // unless explicitly set by the user
        if (window.activeEl.style.width === '100%') {
           // Optional: you might want to leave it at 100% 
           // but for alignment to show, it usually needs a smaller width
        }
      } else {
        // Standard image block uses justifyContent on wrapper
        const parent = window.activeEl.closest('.dropped-block-inner');
        if (parent) parent.style.justifyContent = flexVal;
      }
      
      if (window.saveHistory) window.saveHistory();
    }
  });
});

// CSS classes
document.getElementById('is-classes').addEventListener('input', e => {
  if (window.activeEl) {
    window.activeEl.dataset.cssClasses = e.target.value;
    window.activeEl.className = 'editor-image ' + e.target.value;
  }
});

// Remove block
document.getElementById('is-remove-btn').addEventListener('click', () => {
  if (window.activeEl) {
    if (window.activeEl.closest('.editor-card')) {
      window.activeEl.remove();
    } else {
      window.activeEl.closest('.dropped-block').remove();
      checkEmptyBlocks();
    }
  }
  closeAllPanels();
});

// ── Drop Image Block ─────────────────────────────────────────────────────────

function dropImageBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Image <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <img src="https://via.placeholder.com/300x150?text=Click+to+set+image" class="editor-image" style="width: 100%; max-width: 100%; height: auto;" />
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const img = block.querySelector('img');
  openImageSettings(img);
}
