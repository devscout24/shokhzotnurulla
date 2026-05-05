// ── Drag & Drop Zone ──────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function() {
  const zone = document.getElementById('content-editor-zone');
  const blocksContainer = document.getElementById('blocks-container');
  const emptyState = document.getElementById('empty-state');

  if (!zone || !blocksContainer) return;

  window.dragType = null;

  document.querySelectorAll('.block-item[draggable]').forEach(item => {
    item.addEventListener('dragstart', e => {
      window.dragType = item.dataset.type;
      item.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'copy';
      // Required for Firefox
      e.dataTransfer.setData('text/plain', item.dataset.type);
    });
    item.addEventListener('dragend', () => item.classList.remove('dragging'));
  });

  const dropIndicator = document.createElement('div');
  dropIndicator.id = 'drop-indicator';
  dropIndicator.style.display = 'none';
  blocksContainer.appendChild(dropIndicator);

  zone.addEventListener('dragover', e => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
    zone.classList.add('drag-over');
    const after = getDragAfterElement(blocksContainer, e.clientY, e.clientX);
    dropIndicator.style.display = 'block';
    if (after == null) blocksContainer.appendChild(dropIndicator);
    else blocksContainer.insertBefore(dropIndicator, after);
  });

  zone.addEventListener('dragleave', e => {
    if (!zone.contains(e.relatedTarget)) {
      zone.classList.remove('drag-over');
      dropIndicator.style.display = 'none';
    }
  });

  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    dropIndicator.style.display = 'none';
    const after = getDragAfterElement(blocksContainer, e.clientY, e.clientX);
    
    if (window.dragType) {
      createAndInsertBlock(window.dragType, blocksContainer, after);
    } else if (window.reorderBlock) {
      if (after == null) blocksContainer.appendChild(window.reorderBlock);
      else blocksContainer.insertBefore(window.reorderBlock, after);
      if (typeof saveHistory === 'function') saveHistory();
    }
    
    window.dragType = null;
    window.reorderBlock = null;
  });

  zone.addEventListener('click', e => {
    if (e.target === zone || e.target === blocksContainer) {
      if (typeof closeAllPanels === 'function') closeAllPanels();
    }
  });
});

function createAndInsertBlock(type, container, afterElement) {
  const map = {
    'heading': typeof dropHeadingBlock !== 'undefined' ? dropHeadingBlock : null,
    'text': typeof dropTextBlock !== 'undefined' ? dropTextBlock : null,
    'button': typeof dropButtonBlock !== 'undefined' ? dropButtonBlock : null,
    'divider': typeof dropDividerBlock !== 'undefined' ? dropDividerBlock : null,
    'image': typeof dropImageBlock !== 'undefined' ? dropImageBlock : null,
    'accordion': typeof dropAccordionBlock !== 'undefined' ? dropAccordionBlock : null,
    'spacer': typeof dropSpacerBlock !== 'undefined' ? dropSpacerBlock : null,
    'card': typeof dropCardBlock !== 'undefined' ? dropCardBlock : null,
    'span': typeof dropSpanBlock !== 'undefined' ? dropSpanBlock : null,
    'iframe': typeof dropIFrameBlock !== 'undefined' ? dropIFrameBlock : null,
    '2col': typeof drop2ColBlock !== 'undefined' ? drop2ColBlock : null,
    '3col': typeof drop3ColBlock !== 'undefined' ? drop3ColBlock : null,
    'container': typeof dropContainerBlock !== 'undefined' ? dropContainerBlock : null,
    'icon': typeof dropIconBlock !== 'undefined' ? dropIconBlock : null,
    'cart': typeof dropCartBlock !== 'undefined' ? dropCartBlock : null,
    'overlay': typeof dropOverlayBlock !== 'undefined' ? dropOverlayBlock : null,
    'html': typeof dropHTMLBlock !== 'undefined' ? dropHTMLBlock : null,
    'css': typeof dropCSSBlock !== 'undefined' ? dropCSSBlock : null,
    'video': typeof dropVideoBlock !== 'undefined' ? dropVideoBlock : null,
    'carousel': typeof dropCarouselBlock !== 'undefined' ? dropCarouselBlock : null,
    'tabs': typeof dropTabsBlock !== 'undefined' ? dropTabsBlock : null,
    'check': typeof dropCheckBlock !== 'undefined' ? dropCheckBlock : null,
    'map': typeof dropMapBlock !== 'undefined' ? dropMapBlock : null,
    'modal': typeof dropModalBlock !== 'undefined' ? dropModalBlock : null,
    'inventory': typeof dropInventoryBlock !== 'undefined' ? dropInventoryBlock : null,
    'plugin': typeof dropPluginBlock !== 'undefined' ? dropPluginBlock : null,
    'form': typeof dropFormBlock !== 'undefined' ? dropFormBlock : null,
    'blog': typeof dropBlogBlock !== 'undefined' ? dropBlogBlock : null,
    'content_block': typeof dropContentBlockBlock !== 'undefined' ? dropContentBlockBlock : null,
    'body_types': typeof dropBodyTypesBlock !== 'undefined' ? dropBodyTypesBlock : null,
    'search': typeof dropSearchBlock !== 'undefined' ? dropSearchBlock : null,
    'map_hours': typeof dropMapHoursBlock !== 'undefined' ? dropMapHoursBlock : null,
  };

  const fn = map[type];
  if (!fn) {
    console.error(`Block type "${type}" function not found.`);
    return;
  }

  const block = fn(true);
  if (!block) return;

  if (afterElement == null) container.appendChild(block);
  else container.insertBefore(block, afterElement);

  if (typeof attachBlockListeners === 'function') {
    attachBlockListeners(block);
  }

  // Hide empty state
  const emptyState = document.getElementById('empty-state');
  if (emptyState) emptyState.style.display = 'none';

  if (typeof saveHistory === 'function') saveHistory();

  // Focus the new element if it's text-based, THEN open settings
  const editable = block.querySelector('[contenteditable="true"]');
  if (editable) {
      editable.focus();
      if (typeof placeCursorAtEnd === 'function') placeCursorAtEnd(editable);
      
      // Open settings panel AFTER focus is established (delay to prevent focus stealing)
      setTimeout(() => {
          editable.focus(); // re-focus after settings panel DOM changes
          if (editable.tagName === 'H1' && typeof openHeadingSettings === 'function') openHeadingSettings(editable);
          else if (editable.tagName === 'P' && typeof openTextSettings === 'function') openTextSettings(editable);
          else if (editable.tagName === 'SPAN' && typeof openSpanSettings === 'function') openSpanSettings(editable);
          else if (editable.classList.contains('acc-header') && typeof openAccordionSettings === 'function') openAccordionSettings(editable);
          // Re-focus AFTER settings panel opens (settings panel might steal focus)
          setTimeout(() => editable.focus(), 50);
      }, 100);
  }
  
  const btn = block.querySelector('.dropped-btn');
  if (btn && typeof openButtonSettings === 'function') openButtonSettings(btn);
  
  const div = block.querySelector('.editor-divider');
  if (div && typeof openDividerSettings === 'function') openDividerSettings(div);

  const spacer = block.querySelector('.editor-spacer');
  if (spacer && typeof openSpacerSettings === 'function') openSpacerSettings(spacer);

  const icon = block.querySelector('.editor-icon');
  if (icon && typeof openIconSettings === 'function') openIconSettings(icon);
}

function getDragAfterElement(container, y, x) {
  const draggableElements = [...container.querySelectorAll(':scope > .dropped-block:not(.dragging)')];
  
  const computedStyle = getComputedStyle(container);
  const isH = computedStyle.display === 'flex' && computedStyle.flexDirection === 'row';

  return draggableElements.reduce((closest, child) => {
    const box = child.getBoundingClientRect();
    const offset = isH ? x - (box.left + box.width/2) : y - (box.top + box.height/2);

    if (offset < 0 && offset > closest.offset) {
      return { offset: offset, element: child };
    } else {
      return closest;
    }
  }, { offset: Number.NEGATIVE_INFINITY }).element;
}
