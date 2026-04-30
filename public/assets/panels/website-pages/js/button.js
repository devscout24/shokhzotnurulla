// ── Button Settings Panel ─────────────────────────────────────────────────────

function openButtonSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  document.getElementById('button-settings-panel').style.display = 'block';

  // Sync fields
  document.getElementById('bs-text').value = el.textContent.trim();
  document.getElementById('bs-theme').value = el.dataset.theme || 'red';
  document.getElementById('bs-link').value = el.getAttribute('href') !== '#' ? el.getAttribute('href') : '';
  document.getElementById('bs-icon').value = el.dataset.icon || '';

  const fw = el.classList.contains('full-width');
  document.getElementById('bs-fullwidth').checked = fw;
  document.getElementById('bs-fullwidth-label').textContent = fw ? 'Yes' : 'No';

  const nt = el.getAttribute('target') === '_blank';
  document.getElementById('bs-newtab').checked = nt;
  document.getElementById('bs-newtab-label').textContent = nt ? 'Yes' : 'No';

  // Size buttons
  const curSize = el.dataset.size || 'medium';
  document.querySelectorAll('.bs-toggle-btn[data-size]').forEach(b =>
    b.classList.toggle('active', b.dataset.size === curSize)
  );

  // Style buttons
  const curStyle = el.dataset.bstyle || 'solid';
  document.querySelectorAll('.bs-toggle-btn[data-bstyle]').forEach(b =>
    b.classList.toggle('active', b.dataset.bstyle === curStyle)
  );

  // Align buttons
  let currentAlign = 'center';
  if (el.closest('.editor-card')) {
    const as = el.style.alignSelf || 'flex-start';
    const revMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
    currentAlign = revMap[as] || 'left';
  } else {
    const jc = wrapper.style.justifyContent || 'center';
    const alignMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
    currentAlign = alignMap[jc] || 'center';
  }

  document.querySelectorAll('.bs-align-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.align === currentAlign)
  );
}

// Back / Cancel
document.getElementById('bs-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('bs-cancel-btn').addEventListener('click', closeAllPanels);

// Button text
document.getElementById('bs-text').addEventListener('input', e => {
  if (activeEl) activeEl.textContent = e.target.value;
});

// Theme
document.getElementById('bs-theme').addEventListener('change', e => {
  if (activeEl) activeEl.dataset.theme = e.target.value;
});

// Link
document.getElementById('bs-link').addEventListener('input', e => {
  if (activeEl) activeEl.setAttribute('href', e.target.value || '#');
});

// Full-width
document.getElementById('bs-fullwidth').addEventListener('change', e => {
  if (activeEl) {
    const isFull = e.target.checked;
    activeEl.classList.toggle('full-width', isFull);
    document.getElementById('bs-fullwidth-label').textContent = isFull ? 'Yes' : 'No';
    
    if (activeEl.closest('.editor-card')) {
        activeEl.style.width = isFull ? '100%' : 'fit-content';
        if (isFull) activeEl.style.alignSelf = 'stretch';
        else {
            // Restore previous alignment if not full width
            const map = { left: 'flex-start', center: 'center', right: 'flex-end' };
            activeEl.style.alignSelf = map[activeEl.dataset.align || 'left'];
        }
    }
  }
});

// Open in new tab
document.getElementById('bs-newtab').addEventListener('change', e => {
  if (activeEl) {
    activeEl.setAttribute('target', e.target.checked ? '_blank' : '');
    document.getElementById('bs-newtab-label').textContent = e.target.checked ? 'Yes' : 'No';
  }
});

// Size
document.querySelectorAll('.bs-toggle-btn[data-size]').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.bs-toggle-btn[data-size]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) activeEl.dataset.size = btn.dataset.size;
  });
});

// Style (Solid / Outline)
document.querySelectorAll('.bs-toggle-btn[data-bstyle]').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.bs-toggle-btn[data-bstyle]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) activeEl.dataset.bstyle = btn.dataset.bstyle;
  });
});

// Alignment
document.querySelectorAll('.bs-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.bs-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) {
      const align = btn.dataset.align;
      const map = { left: 'flex-start', center: 'center', right: 'flex-end' };
      const flexVal = map[align];
      
      // Save alignment in dataset
      activeEl.dataset.align = align;

      // Case 1: Button is inside a Card
      if (activeEl.closest('.editor-card')) {
        activeEl.style.alignSelf = flexVal;
        // If centered/right, we must remove width:100% if it was there
        if (!activeEl.classList.contains('full-width')) {
            activeEl.style.width = 'fit-content';
        }
      } 
      // Case 2: Standard Button Block
      else {
        const wrapper = activeEl.closest('.dropped-block-inner');
        if (wrapper) wrapper.style.justifyContent = flexVal;
      }
    }
  });
});

// Remove
document.getElementById('bs-remove-btn').addEventListener('click', () => {
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

// ── Drop Button Block ─────────────────────────────────────────────────────────

function dropButtonBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Button <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="justify-content:center; padding:20px 16px;">
      <a class="dropped-btn"
         data-theme="red"
         data-bstyle="solid"
         data-size="medium"
         href="#">GO FOR LIVE</a>
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const btn = block.querySelector('.dropped-btn');
  openButtonSettings(btn);
}
