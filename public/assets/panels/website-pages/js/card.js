// ── Card Settings Panel ─────────────────────────────────────────────────────

function openCardSettings(el) {
  closeAllPanels();
  activeEl = el;
  el.closest('.dropped-block').classList.add('selected');
  document.getElementById('card-settings-panel').style.display = 'block';

  // Read background color
  document.getElementById('cs-bg-color').value = el.style.backgroundColor || 'transparent';
  
  // Width
  const currentWidth = el.style.width || '100%';
  document.getElementById('cs-width').value = parseInt(currentWidth) || 100;

  // Flex — read from the .dropped-block-inner wrapper
  const wrapper = el.closest('.dropped-block-inner');
  if (wrapper) {
    const cs = wrapper.style;
    document.getElementById('cs-flex-direction').value  = cs.flexDirection  || 'row';
    document.getElementById('cs-justify-content').value = cs.justifyContent || 'flex-start';
    document.getElementById('cs-align-items').value     = cs.alignItems     || 'stretch';
    document.getElementById('cs-flex-wrap').value       = cs.flexWrap       || 'nowrap';
    document.getElementById('cs-gap').value             = parseInt(cs.gap)  || 0;
  }

}

// Back / Cancel
document.getElementById('cs-back-btn').addEventListener('click', closeAllPanels);
document.getElementById('cs-cancel-btn').addEventListener('click', closeAllPanels);

// Width
document.getElementById('cs-width').addEventListener('input', e => {
  if (activeEl) {
    const w = Math.min(100, Math.max(5, parseInt(e.target.value) || 100));
    activeEl.style.width = w + '%';
  }
});

// Background Color
document.getElementById('cs-bg-color').addEventListener('change', e => {
  if (activeEl) {
    activeEl.style.backgroundColor = e.target.value !== 'transparent' ? e.target.value : '';
  }
});

// ── Flex Controls (applied to .dropped-block-inner wrapper) ─────────────────
function getFlexWrapper() {
  return activeEl ? activeEl.closest('.dropped-block-inner') : null;
}

document.getElementById('cs-flex-direction').addEventListener('change', e => {
  const w = getFlexWrapper();
  if (w) { w.style.display = 'flex'; w.style.flexDirection = e.target.value; }
});

document.getElementById('cs-justify-content').addEventListener('change', e => {
  const w = getFlexWrapper();
  if (w) { w.style.display = 'flex'; w.style.justifyContent = e.target.value; }
});

document.getElementById('cs-align-items').addEventListener('change', e => {
  const w = getFlexWrapper();
  if (w) { w.style.display = 'flex'; w.style.alignItems = e.target.value; }
});

document.getElementById('cs-flex-wrap').addEventListener('change', e => {
  const w = getFlexWrapper();
  if (w) { w.style.display = 'flex'; w.style.flexWrap = e.target.value; }
});

document.getElementById('cs-gap').addEventListener('input', e => {
  const w = getFlexWrapper();
  if (w) { w.style.display = 'flex'; w.style.gap = (parseInt(e.target.value) || 0) + 'px'; }
});



// Remove block
document.getElementById('cs-remove-btn').addEventListener('click', () => {
  if (activeEl) {
    activeEl.closest('.dropped-block').remove();
    checkEmptyBlocks();
  }
  closeAllPanels();
});

// ── Drop Card Block ──────────────────────────────────────────────────────────

function dropCardBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">
      Card <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="align-items: stretch;">
      <div class="card editor-card" style="width: 100%; display: flex; flex-direction: column;">
        <img src="https://via.placeholder.com/300x150?text=Card+Image" class="editor-image card-img-top" alt="Card image" style="width: 100%;">
        <div class="card-body col-drop-zone" style="display: flex; flex-direction: column; gap: 10px; min-height: 50px;">
          <h1 contenteditable="true" spellcheck="false" class="card-title m-0" style="font-size: 20px;">Card Title</h1>
          <p contenteditable="true" spellcheck="false" class="card-text m-0">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
          <a class="dropped-btn" data-theme="red" data-bstyle="solid" data-size="small" data-align="left" href="#" style="align-self: flex-start; width: fit-content;">Go somewhere</a>
        </div>
      </div>
    </div>`;

  const col = block.querySelector('.col-drop-zone');
  if (col) attachDropZoneListeners(col);

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  const card = block.querySelector('.editor-card');
  const img = block.querySelector('.editor-image');
  const h1 = block.querySelector('h1');
  const p = block.querySelector('p');
  const btn = block.querySelector('.dropped-btn');

  // Attach specific listeners so children open their own panels
  if (img) img.addEventListener('click', (e) => { e.stopPropagation(); openImageSettings(img); });
  if (h1) {
    h1.addEventListener('click', (e) => { e.stopPropagation(); openHeadingSettings(h1); });
    h1.addEventListener('focus', (e) => { e.stopPropagation(); openHeadingSettings(h1); });
  }
  if (p) {
    p.addEventListener('click', (e) => { e.stopPropagation(); openTextSettings(p); });
    p.addEventListener('focus', (e) => { e.stopPropagation(); openTextSettings(p); });
  }
  if (btn) btn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); openButtonSettings(btn); });
  
  // Card background itself opens card settings
  card.addEventListener('click', () => openCardSettings(card));

  openCardSettings(card);
}
