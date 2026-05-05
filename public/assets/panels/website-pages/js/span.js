// ── Span Settings Panel ─────────────────────────────────────────────────────

function openSpanSettings(el) {
    closeAllPanels();
    activeEl = el;
    const block = el.closest('.dropped-block');
    block.classList.add('selected');
    const panel = document.getElementById('span-settings-panel');
    if (panel) panel.style.display = 'block';

    // Sync Visibility
    if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

    // Load current values
    document.getElementById('span-color').value = el.style.color || '';
    const currentSize = el.style.fontSize;
    document.getElementById('span-size').value = currentSize ? parseInt(currentSize) : '';
    
    // Sync align
    const container = el.closest('.dropped-block-inner');
    let curAlign = 'left';
    if (container) {
        const jc = container.style.justifyContent;
        if (jc === 'center') curAlign = 'center';
        else if (jc === 'flex-end') curAlign = 'right';
    }
    document.querySelectorAll('.span-align-btn').forEach(b => 
        b.classList.toggle('active', b.dataset.align === curAlign)
    );

    document.getElementById('span-weight').value = el.style.fontWeight || 'normal';
}

// Back and Cancel Buttons
document.getElementById('span-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('span-cancel-btn')?.addEventListener('click', closeAllPanels);

// Text Color Change
document.getElementById('span-color')?.addEventListener('change', e => {
    if (activeEl && activeEl.tagName === 'SPAN') {
        activeEl.style.color = e.target.value;
    }
});

// Text Size Change
document.getElementById('span-size')?.addEventListener('input', e => {
    if (activeEl && activeEl.tagName === 'SPAN') {
        const val = e.target.value;
        activeEl.style.fontSize = val ? val + 'px' : '';
    }
});

// Align
document.querySelectorAll('.span-align-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.span-align-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        if (activeEl) {
            const container = activeEl.closest('.dropped-block-inner');
            if (container) {
                container.style.display = 'flex';
                if (btn.dataset.align === 'left') container.style.justifyContent = 'flex-start';
                else if (btn.dataset.align === 'center') container.style.justifyContent = 'center';
                else if (btn.dataset.align === 'right') container.style.justifyContent = 'flex-end';
            }
        }
    });
});

// Weight
document.getElementById('span-weight')?.addEventListener('change', e => {
    if (activeEl) activeEl.style.fontWeight = e.target.value;
});

// Italic
document.getElementById('span-italic')?.addEventListener('change', e => {
    if (activeEl) activeEl.style.fontStyle = e.target.checked ? 'italic' : 'normal';
});

// Margin Top
document.getElementById('span-margin-top')?.addEventListener('input', e => {
    if (activeEl) activeEl.style.marginTop = e.target.value ? e.target.value + 'px' : '';
});

// Margin Bottom
document.getElementById('span-margin-bottom')?.addEventListener('input', e => {
    if (activeEl) activeEl.style.marginBottom = e.target.value ? e.target.value + 'px' : '';
});

// Remove Block
document.getElementById('span-remove-btn')?.addEventListener('click', () => {
    if (activeEl) {
        activeEl.closest('.dropped-block').remove();
        checkEmptyBlocks();
    }
    closeAllPanels();
});

// ── Drop Span Block ──────────────────────────────────────────────────────────

function dropSpanBlock(returnBlock = false) {
    const emptyState = document.getElementById('empty-state');
    const blocksContainer = document.getElementById('blocks-container');
    if (emptyState) emptyState.style.display = 'none';

    const block = document.createElement('div');
    block.className = 'dropped-block';
    block.innerHTML = `
    <span class="dropped-block-badge">
      Span <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i>
    </span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="display: flex; align-items: flex-start; gap: 8px;">
      <i class="fa-solid fa-quote-left" style="color: #ef4444; font-size: 14px; opacity: 0.6;"></i>
      <span contenteditable="true" spellcheck="false" style="display:inline-block; outline:none; min-width:100px; min-height:24px; padding:2px 5px; font-style: italic; color: #555;">
        Inline span text
      </span>
      <i class="fa-solid fa-quote-right" style="color: #ef4444; font-size: 14px; opacity: 0.6; align-self: flex-end;"></i>
    </div>`;

    if (returnBlock) return block;
    blocksContainer.appendChild(block);
    attachBlockListeners(block);

    const spanEl = block.querySelector('span[contenteditable]');
    if (spanEl) {
        spanEl.focus();
        openSpanSettings(spanEl);
        placeCursorAtEnd(spanEl);
    }
}
