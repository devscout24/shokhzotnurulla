// ── Export & Import Functionality ─────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function() {
  // ── Export Button ──────────────────────────────────────────────────────────
  document.querySelector('button[title="Export"]')?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const blocksContainer = document.getElementById('blocks-container');
    if (!blocksContainer) {
      alert('No blocks to export');
      return;
    }

    // Collect all blocks data
    const payload = collectBlocksFromContainer(blocksContainer);
    
    if (payload.length === 0) {
      alert('No blocks to export');
      return;
    }

    // Create JSON file
    const jsonString = JSON.stringify(payload, null, 2);
    const pageTitle = document.getElementById('page-title').value || 'page';
    const fileName = `${pageTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_export_${new Date().getTime()}.json`;
    
    // Create blob and download
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);

    console.log('✅ Page exported successfully!', { fileName, blockCount: payload.length });
  });

  // ── Import Button ──────────────────────────────────────────────────────────
  document.querySelector('button[title="Import"]')?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    // Create hidden file input
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = '.json,application/json';
    fileInput.style.display = 'none';

    fileInput.addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file is JSON
      if (!file.name.endsWith('.json')) {
        alert('Please select a valid JSON file');
        return;
      }

      const reader = new FileReader();
      reader.onload = function(e) {
        try {
          const importedPayload = JSON.parse(e.target.result);
          
          // Validate payload is an array
          if (!Array.isArray(importedPayload)) {
            alert('Invalid export file: Must contain an array of blocks');
            return;
          }

          // Confirm before importing (will clear current content)
          if (document.getElementById('blocks-container').children.length > 1) {
            if (!confirm('This will replace all current blocks. Continue?')) {
              return;
            }
          }

          // Clear existing blocks
          const blocksContainer = document.getElementById('blocks-container');
          blocksContainer.innerHTML = '';
          const emptyState = document.getElementById('empty-state');
          if (emptyState) emptyState.style.display = 'block';

          // Reconstruct blocks from imported data
          importedPayload.forEach((blockData, index) => {
            reconstructBlockFromData(blockData, blocksContainer);
          });

          console.log('✅ Page imported successfully!', { blockCount: importedPayload.length });
          if (typeof saveHistory === 'function') saveHistory();
          alert(`✅ Successfully imported ${importedPayload.length} blocks!`);

        } catch (error) {
          console.error('❌ Import error:', error);
          alert('Error reading file: ' + error.message);
        }
      };

      reader.readAsText(file);
    });

    // Trigger file picker
    fileInput.click();
  });
});

// ── Reconstruct Block from Exported Data ───────────────────────────────────
function reconstructBlockFromData(blockData, container) {
  const { type, ...data } = blockData;

  const blockTypeToFunction = {
    'heading': dropHeadingBlock,
    'text': dropTextBlock,
    'button': dropButtonBlock,
    'divider': dropDividerBlock,
    'image': dropImageBlock,
    'video': dropVideoBlock,
    'accordion': dropAccordionBlock,
    'spacer': dropSpacerBlock,
    'card': dropCardBlock,
    'span': dropSpanBlock,
    'iframe': dropIFrameBlock,
    '2col': drop2ColBlock,
    '3col': drop3ColBlock,
    'container': dropContainerBlock,
    'icon': dropIconBlock,
    'cart': dropCartBlock,
    'overlay': dropOverlayBlock,
    'html': dropHTMLBlock,
    'css': dropCSSBlock,
    'inventory': dropInventoryBlock,
    'search': dropSearchBlock,
    'form': dropFormBlock,
    'blog': dropBlogBlock,
    'content_block': dropContentBlockBlock,
    'body_types': dropBodyTypesBlock,
    'map_hours': dropMapHoursBlock,
    'map': dropMapBlock,
    'plugin': dropPluginBlock,
    'carousel': dropCarouselBlock,
    'tabs': dropTabsBlock,
  };

  const blockFn = blockTypeToFunction[type];
  if (!blockFn || typeof blockFn !== 'function') {
    console.warn(`⚠️ Block type "${type}" function not found, skipping`);
    return;
  }

  // Create the block
  const block = blockFn(true);
  if (!block) return;

  container.appendChild(block);

  // Restore block properties based on type
  restoreBlockProperties(block, type, data);

  // Attach listeners
  if (typeof attachBlockListeners === 'function') {
    attachBlockListeners(block);
  }

  // Hide empty state
  const emptyState = document.getElementById('empty-state');
  if (emptyState) emptyState.style.display = 'none';
}

// ── Restore Block Properties from Exported Data ────────────────────────────
function restoreBlockProperties(block, type, data) {
  const inner = block.querySelector('.dropped-block-inner');
  if (!inner) return;

  switch (type) {
    case 'heading':
      const h1 = inner.querySelector('h1');
      if (h1) {
        h1.innerText = data.text || '';
        h1.style.textAlign = data.textAlign || 'left';
        h1.style.color = data.color || '';
        h1.style.fontSize = data.fontSize || '';
        h1.dataset.cssClasses = data.cssClasses || '';
        if (data.cssClasses) h1.className = data.cssClasses;
      }
      break;

    case 'text':
      const p = inner.querySelector('p[contenteditable]');
      if (p) {
        p.innerText = data.text || '';
        p.style.color = data.color || '';
        p.style.fontSize = data.fontSize || '';
        p.dataset.cssClasses = data.cssClasses || '';
        if (data.cssClasses) p.className = data.cssClasses;
      }
      break;

    case 'span':
      const span = inner.querySelector('span[contenteditable]');
      if (span) {
        span.innerText = data.text || '';
        span.style.color = data.color || '';
        span.style.fontSize = data.fontSize ? data.fontSize + 'px' : '';
        span.style.fontWeight = data.fontWeight || 'normal';
        span.style.textAlign = data.align || 'left';
      }
      break;

    case 'button':
      const btn = inner.querySelector('a.dropped-btn');
      if (btn) {
        btn.textContent = data.text || '';
        btn.dataset.theme = data.theme || 'red';
        btn.setAttribute('href', data.link || '#');
        if (data.fullwidth) btn.classList.add('full-width');
      }
      break;

    case 'image':
      const img = inner.querySelector('img');
      if (img) {
        img.src = data.src || '';
        img.alt = data.alt || '';
        img.style.width = data.width || '100%';
        img.style.height = data.height || '';
        img.style.opacity = data.opacity || '1';
        img.dataset.link = data.link || '';
        img.dataset.newtab = data.newtab || '';
      }
      break;

    case 'video':
      const video = inner.querySelector('video');
      if (video) {
        const source = video.querySelector('source');
        if (source) {
          source.src = data.url || '';
          source.type = 'video/mp4';
        }
        video.autoplay = data.autoplay || false;
        video.controls = data.controls !== false;
      }
      break;

    case 'divider':
      const divider = inner.querySelector('.editor-divider');
      if (divider) {
        divider.style.borderColor = data.color || '#e0e6ed';
        divider.style.height = (data.height || 2) + 'px';
      }
      break;

    case 'spacer':
      const spacer = inner.querySelector('.editor-spacer');
      if (spacer) {
        spacer.style.height = (data.height || 40) + 'px';
      }
      break;

    case 'icon':
      const icon = inner.querySelector('i');
      if (icon) {
        icon.className = data.iconClass || 'fa-solid fa-star';
        icon.style.color = data.color || '#111827';
        icon.style.fontSize = (data.size || 32) + 'px';
      }
      break;

    case 'container':
      const container = inner.querySelector('.editor-container');
      if (container) {
        container.style.paddingTop = (data.padding || 40) + 'px';
        container.style.paddingBottom = (data.padding || 40) + 'px';
        container.style.backgroundColor = data.bgColor || '#ffffff';
      }
      break;

    case '2col':
      const col2 = inner.querySelector('.editor-2col');
      if (col2) {
        col2.style.gap = (data.gap || 20) + 'px';
      }
      break;

    case '3col':
      const col3 = inner.querySelector('.editor-3col');
      if (col3) {
        col3.style.gap = (data.gap || 20) + 'px';
      }
      break;

    case 'inventory':
      const invBlock = inner.querySelector('.editor-inventory');
      if (invBlock) {
        invBlock.dataset.dealerId = data.dealerId || '';
        invBlock.dataset.condition = data.condition || 'all';
        invBlock.dataset.make = data.make || '';
        invBlock.dataset.model = data.model || '';
        invBlock.dataset.minPrice = data.minPrice || '';
        invBlock.dataset.maxPrice = data.maxPrice || '';
        invBlock.dataset.maxMileage = data.maxMileage || '';
        invBlock.dataset.sort = data.sort || '';
        invBlock.dataset.highlighted = data.highlighted || '';
      }
      break;

    case 'form':
      const formBlock = inner.querySelector('.editor-form');
      if (formBlock) {
        formBlock.dataset.formId = data.formId || '';
        formBlock.dataset.email = data.email || '';
        formBlock.dataset.success = data.success || '';
        formBlock.dataset.formName = data.formName || '';
      }
      break;

    case 'search':
      const searchBlock = inner.querySelector('.editor-search');
      if (searchBlock) {
        searchBlock.dataset.placeholder = data.placeholder || '';
        searchBlock.dataset.size = data.size || '';
        searchBlock.dataset.honda = data.honda || '';
        searchBlock.dataset.acura = data.acura || '';
      }
      break;

    case 'map':
      const mapBlock = inner.querySelector('.editor-map');
      if (mapBlock) {
        mapBlock.dataset.address = data.address || '';
        mapBlock.dataset.zoom = data.zoom || '14';
        mapBlock.dataset.title = data.title || '';
        mapBlock.dataset.subtitle = data.subtitle || '';
      }
      break;

    case 'blog':
      const blogBlock = inner.querySelector('.editor-blog');
      if (blogBlock) {
        blogBlock.dataset.category = data.category || 'all';
        blogBlock.dataset.count = data.count || '3';
      }
      break;

    case 'accordion':
      const accBlock = inner.querySelector('.editor-accordion');
      if (accBlock && data.items) {
        const accContent = accBlock.querySelector('.acc-content');
        if (accContent) {
          accContent.innerHTML = '';
          data.items.forEach(item => {
            const itemEl = document.createElement('div');
            itemEl.className = 'acc-item';
            itemEl.innerHTML = `
              <div class="acc-header" contenteditable="true">${item.title || ''}</div>
              <div class="acc-body" contenteditable="true">${item.content || ''}</div>
            `;
            accContent.appendChild(itemEl);
          });
        }
      }
      break;

    case 'tabs':
      const tabsBlock = inner.querySelector('.editor-tabs');
      if (tabsBlock && data.tabs) {
        const navTabs = tabsBlock.querySelector('.nav-tabs');
        if (navTabs) {
          navTabs.innerHTML = '';
          data.tabs.forEach(tab => {
            const tabEl = document.createElement('div');
            tabEl.className = 'nav-link';
            tabEl.innerText = tab;
            navTabs.appendChild(tabEl);
          });
        }
      }
      break;

    case 'overlay':
      block.dataset.opacity = data.opacity || '0.5';
      block.dataset.color = data.color || '#000000';
      break;

    case 'html':
      const htmlBlock = inner.querySelector('.editor-html');
      if (htmlBlock) {
        htmlBlock.innerText = data.html || '';
      }
      break;

    case 'css':
      const cssBlock = inner.querySelector('.editor-css');
      if (cssBlock) {
        cssBlock.innerText = data.css || '';
      }
      break;

    default:
      // Other blocks handled generically
      break;
  }
}
