// ── Text (P) Settings Panel ───────────────────────────────────────────────────

function openTextSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  document.getElementById('text-settings-panel').style.display = 'block';

  // Sync align buttons
  const cur = el.style.textAlign || 'left';
  document.querySelectorAll('.ts-align-btn').forEach(b =>
    b.classList.toggle('active', b.dataset.align === cur)
  );

  // Sync fields
  document.getElementById('ts-color').value = el.style.color || '';
  document.getElementById('ts-size').value = el.style.fontSize ? parseInt(el.style.fontSize) : '';
  document.getElementById('ts-classes').value = el.dataset.cssClasses || '';
}

// Back / Cancel
document.getElementById('ts-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('ts-cancel-btn').addEventListener('click', closeAllPanels);

// Align
document.querySelectorAll('.ts-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.ts-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (activeEl) activeEl.style.textAlign = btn.dataset.align;
  });
});

// Text color
document.getElementById('ts-color').addEventListener('change', e => {
  if (activeEl) activeEl.style.color = e.target.value || '';
});

// Text size
document.getElementById('ts-size').addEventListener('input', e => {
  if (activeEl && e.target.value) activeEl.style.fontSize = e.target.value + 'px';
  else if (activeEl) activeEl.style.fontSize = '';
});

// CSS classes
document.getElementById('ts-classes').addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.cssClasses = e.target.value;
    activeEl.className = e.target.value;
  }
});

// Remove block
document.getElementById('ts-remove-btn').addEventListener('click', () => {
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

// ── Drop Text (P) Block ───────────────────────────────────────────────────────

function dropTextBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Text <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <p contenteditable="true" spellcheck="false" data-placeholder="Enter text details..." style="margin:0;font-size:15px;color:#444;width:100%;"></p>
    </div>`;

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const p = block.querySelector('p');
  p.focus();
  openTextSettings(p);
  placeCursorAtEnd(p);
}
