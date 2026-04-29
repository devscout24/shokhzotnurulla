// ── Drag & Drop Zone ──────────────────────────────────────────────────────────

const zone = document.getElementById('content-editor-zone');
const blocksContainer = document.getElementById('blocks-container');
const emptyState = document.getElementById('empty-state');

window.dragType = null;

document.querySelectorAll('.block-item[draggable]').forEach(item => {
  item.addEventListener('dragstart', e => {
    window.dragType = item.dataset.type;
    item.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'copy';
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
  if (window.dragType) createAndInsertBlock(window.dragType, blocksContainer, after);
  window.dragType = null;
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
    'overlay': window.dropOverlayBlock,
    'html': window.dropHTMLBlock,
    'css': window.dropCSSBlock,
    'video': window.dropVideoBlock,
    'carousel': window.dropCarouselBlock,
    'tabs': window.dropTabsBlock,
    'check': window.dropCheckBlock,
    'map': window.dropMapBlock,
    'modal': window.dropModalBlock,
    'inventory': window.dropInventoryBlock,
    'plugin': window.dropPluginBlock,
    'form': window.dropFormBlock,
    'blog': window.dropBlogBlock,
    'content_block': window.dropContentBlockBlock,
    'body_types': window.dropBodyTypesBlock,
    'search': window.dropSearchBlock,
    'map_hours': window.dropMapHoursBlock,
  };

  const fn = map[type];
  if (!fn) return;

  const block = fn(true);
  if (!block) return;

  if (afterElement == null) container.appendChild(block);
  else container.insertBefore(block, afterElement);

  attachBlockListeners(block);

  // Hide empty state
  if (emptyState) emptyState.style.display = 'none';

  // Open settings panel for supported types
  const el = block.querySelector('h1[contenteditable], p[contenteditable], span[contenteditable], .dropped-btn, .editor-divider, .editor-image, .editor-accordion, .editor-spacer, .editor-card, .editor-3col, .editor-2col, .editor-container, .editor-icon, .editor-cart, .editor-iframe, .editor-video, .editor-carousel, .editor-tabs, .editor-inventory');
  if (el) {
    if (type==='heading') openHeadingSettings(el);
    else if (type==='text') openTextSettings(el);
    else if (type==='button') openButtonSettings(el);
    else if (type==='divider') openDividerSettings(el);
    else if (type==='image') openImageSettings(el);
    else if (type==='accordion') { setupAccordionListeners(el); openAccordionSettings(el); }
    else if (type==='spacer') openSpacerSettings(el);
    else if (type==='card') openCardSettings(el);
    else if (type==='span') openSpanSettings(el);
    else if (type==='iframe') openIFrameSettings(el);
    else if (type==='2col') open2ColSettings(el);
    else if (type==='3col' && typeof open3ColSettings==='function') open3ColSettings(el);
    else if (type==='container') openContainerSettings(el);
    else if (type==='icon') openIconSettings(el);
    else if (type==='cart') openCartSettings(el);
    else if (type==='video' && typeof openVideoSettings==='function') openVideoSettings(el);
    else if (type==='carousel' && typeof openCarouselSettings==='function') openCarouselSettings(el);
    else if (type==='tabs' && typeof openTabsSettings==='function') openTabsSettings(el);
    else if (type==='inventory' && typeof openInventorySettings==='function') openInventorySettings(el);
  }

  if (typeof saveHistory === 'function') saveHistory();
}

function getDragAfterElement(container, y, x) {
  const els = [...container.querySelectorAll(':scope > .dropped-block:not(.dragging)')];
  const style = window.getComputedStyle(container);
  const isH = style.flexDirection === 'row';
  return els.reduce((closest, child) => {
    const box = child.getBoundingClientRect();
    const offset = isH ? x - box.left - box.width/2 : y - box.top - box.height/2;
    if (offset < 0 && offset > closest.offset) return { offset, element: child };
    return closest;
  }, { offset: Number.NEGATIVE_INFINITY }).element;
}

zone.addEventListener('click', e => {
  if (e.target === zone || e.target === blocksContainer) closeAllPanels();
});
