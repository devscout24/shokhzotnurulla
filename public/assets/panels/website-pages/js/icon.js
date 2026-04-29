// ── Icon Settings Panel ─────────────────────────────────────────────────────

function openIconSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  const panel = document.getElementById('icon-settings-panel');
  if (panel) panel.style.display = 'block';

  // Sync inputs
  const target = el.querySelector('i, svg');

  if (target) {
    // Handle FontAwesome class sync
    const classes = target.tagName === 'svg' ? target.getAttribute('data-icon') : Array.from(target.classList).join(' ');
    document.getElementById('icon-class').value = classes;
    document.getElementById('icon-size').value = target.style.fontSize ? parseInt(target.style.fontSize) : 24;
  }

  // Sync width & padding
  document.getElementById('icon-width').value = el.style.width ? parseInt(el.style.width) : 100;
  document.getElementById('icon-padding').value = el.style.paddingTop ? parseInt(el.style.paddingTop) : 10;

  // Sync display & margins
  document.getElementById('icon-display').value = el.style.display || 'flex';
  document.getElementById('icon-m-top').value = parseInt(el.style.marginTop) || 0;
  document.getElementById('icon-m-bottom').value = parseInt(el.style.marginBottom) || 0;
  document.getElementById('icon-m-left').value = parseInt(el.style.marginLeft) || 0;
  document.getElementById('icon-m-right').value = parseInt(el.style.marginRight) || 0;

  // Sync floating
  const block = el.closest('.dropped-block');
  const isFloating = block.classList.contains('free-moving');
  const floatSwitch = document.getElementById('icon-floating');
  if (floatSwitch) floatSwitch.checked = isFloating;
  
  const floatControls = document.getElementById('icon-float-controls');
  if (floatControls) floatControls.style.display = isFloating ? 'block' : 'none';

  if (isFloating) {
    document.getElementById('icon-top').value = parseInt(block.style.top) || 0;
    document.getElementById('icon-left').value = parseInt(block.style.left) || 0;
  }

  // Sync align
  const curAlign = el.style.justifyContent || 'flex-start';
  let btnAlign = 'left';
  if (curAlign === 'center') btnAlign = 'center';
  else if (curAlign === 'flex-end') btnAlign = 'right';

  document.querySelectorAll('.icon-align-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.align === btnAlign)
  );
}

// Back / Cancel
document.getElementById('icon-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('icon-cancel-btn')?.addEventListener('click', closeAllPanels);

// Icon Class Change
document.getElementById('icon-class')?.addEventListener('input', e => {
  if (activeEl) {
    let iTag = activeEl.querySelector('i');
    if (!iTag) {
      activeEl.innerHTML = '<i class="fa-solid fa-icons"></i>';
      iTag = activeEl.querySelector('i');
    }
    iTag.className = e.target.value || 'fa-solid fa-icons';
    iTag.style.fontSize = (document.getElementById('icon-size').value || 24) + 'px';
  }
});

// Width Change
document.getElementById('icon-width')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.style.width = (e.target.value || 100) + '%';
  }
});

// Padding Change
document.getElementById('icon-padding')?.addEventListener('input', e => {
  if (activeEl) {
    const val = (e.target.value || 0) + 'px';
    activeEl.style.paddingTop = val;
    activeEl.style.paddingBottom = val;
  }
});

// Display Mode
document.getElementById('icon-display')?.addEventListener('change', e => {
  if (activeEl) activeEl.style.display = e.target.value;
});

// Margins
['top', 'bottom', 'left', 'right'].forEach(side => {
  document.getElementById(`icon-m-${side}`)?.addEventListener('input', e => {
    if (activeEl) {
      const prop = 'margin' + side.charAt(0).toUpperCase() + side.slice(1);
      activeEl.style[prop] = (e.target.value || 0) + 'px';
    }
  });
});

// Floating Mode
document.getElementById('icon-floating')?.addEventListener('change', e => {
  if (activeEl) {
    const block = activeEl.closest('.dropped-block');
    const isChecked = e.target.checked;
    document.getElementById('icon-float-controls').style.display = isChecked ? 'block' : 'none';
    
    if (isChecked) {
      block.classList.add('free-moving');
      block.style.position = 'absolute';
      block.style.zIndex = '1000';
      if (!block.style.top) {
        block.style.top = '50px';
        block.style.left = '50px';
      }
      // Update inputs
      document.getElementById('icon-top').value = parseInt(block.style.top);
      document.getElementById('icon-left').value = parseInt(block.style.left);
    } else {
      block.classList.remove('free-moving');
      block.style.position = '';
      block.style.top = '';
      block.style.left = '';
      block.style.zIndex = '';
    }
  }
});

// Coordinates
['top', 'left'].forEach(prop => {
  document.getElementById(`icon-${prop}`)?.addEventListener('input', e => {
    if (activeEl) {
      const block = activeEl.closest('.dropped-block');
      if (block.classList.contains('free-moving')) {
        block.style[prop] = (e.target.value || 0) + 'px';
      }
    }
  });
});





// Icon Size
document.getElementById('icon-size')?.addEventListener('input', e => {
  if (activeEl) {
    const target = activeEl.querySelector('i');
    if (target) target.style.fontSize = (e.target.value || 24) + 'px';
  }
});

// Align
document.querySelectorAll('.icon-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.icon-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) {
      if (btn.dataset.align === 'left') activeEl.style.justifyContent = 'flex-start';
      else if (btn.dataset.align === 'center') activeEl.style.justifyContent = 'center';
      else if (btn.dataset.align === 'right') activeEl.style.justifyContent = 'flex-end';
    }
  });
});

// Remove
document.getElementById('icon-remove-btn')?.addEventListener('click', () => {
  if (activeEl) {
    activeEl.closest('.dropped-block').remove();
    checkEmptyBlocks();
  }
  closeAllPanels();
});

// ── Drop Icon Block ───────────────────────────────────────────────────────────

function dropIconBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block is-icon-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Icon <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner editor-icon" style="display: flex; justify-content: flex-start; padding: 5px; width: auto;">
      <i class="fa-solid fa-circle-check" style="font-size: 24px;"></i>
    </div>`;

  if (returnBlock) return block;
  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const inner = block.querySelector('.editor-icon');
  if (inner) openIconSettings(inner);
}
