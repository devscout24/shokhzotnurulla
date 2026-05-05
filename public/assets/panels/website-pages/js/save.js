// ── Save: collect all block data ─────────────────────────────────────────────
// Debug helper: log page content when save button is clicked
document.addEventListener('DOMContentLoaded', function() {
  // Target the submit button inside the page-builder-form (works for both create & edit)
  const form = document.getElementById('page-builder-form');
  if (form) {
    const saveBtn = form.querySelector('[type="submit"]');
    if (saveBtn) {
      saveBtn.addEventListener('click', () => {
        const blocksContainer = document.getElementById('blocks-container');
        const payload = collectBlocksFromContainer(blocksContainer);
        console.log('📄 Page Content Data:', payload);
        console.log('📦 Full JSON String:', JSON.stringify(payload, null, 2));
      });
    }
  }
});

function collectBlocksFromContainer(container) {
  if (!container) return [];
  const payload = [];
  
  const children = container.querySelectorAll(':scope > .dropped-block, :scope > h1, :scope > p, :scope > .dropped-btn, :scope > .editor-image, :scope > .editor-divider, :scope > .editor-spacer, :scope > .editor-icon, :scope > .editor-iframe, :scope > .editor-cart, :scope > span, :scope > .editor-accordion, :scope > .editor-2col, :scope > .editor-3col, :scope > .editor-container');

  children.forEach((el, index) => {
    const data = extractBlockData(el, index);
    if (data) payload.push(data);
  });

  return payload;
}

function extractBlockData(el, index) {
  let target = el;
  if (el.classList.contains('dropped-block')) {
    target = el.querySelector(':scope > .dropped-block-inner > *');
    if (!target) target = el.querySelector('.editor-card');
  }

  if (!target) return null;

  // 1. Heading
  if (target.tagName === 'H1') {
    return {
      index,
      type: 'heading',
      text: target.innerText,
      textAlign: target.style.textAlign || 'left',
      color: target.style.color || '',
      fontSize: target.style.fontSize || '',
      cssClasses: target.dataset.cssClasses || '',
    };
  }

  // 2. Text
  if (target.tagName === 'P') {
    return {
      index,
      type: 'text',
      text: target.innerText,
      color: target.style.color || '',
      fontSize: target.style.fontSize || '',
      cssClasses: target.dataset.cssClasses || '',
    };
  }

  // 3. Span
  if (target.tagName === 'SPAN') {
    return {
      index,
      type: 'span',
      text: target.innerText,
      color: target.style.color || '',
      fontSize: target.style.fontSize || '',
    };
  }

  // 4. Button
  if (target.classList.contains('dropped-btn')) {
    const wrapper = target.closest('.dropped-block-inner') || target.parentElement;
    let currentAlign = 'center';

    if (target.closest('.editor-card')) {
      const as = target.style.alignSelf || 'flex-start';
      const revMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right', 'stretch': 'center' };
      currentAlign = revMap[as] || 'left';
    } else {
      const jc = wrapper ? wrapper.style.justifyContent : 'center';
      const alignMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
      currentAlign = alignMap[jc] || 'center';
    }

    return {
      index,
      type: 'button',
      text: target.innerText.trim(),
      theme: target.dataset.theme || 'red',
      style: target.dataset.bstyle || 'solid',
      size: target.dataset.size || 'medium',
      href: target.getAttribute('href') || '#',
      newTab: target.getAttribute('target') === '_blank',
      fullWidth: target.classList.contains('full-width'),
      align: currentAlign,
    };
  }

  // 5. Image
  if (target.classList.contains('editor-image')) {
    const wrapper = target.closest('.dropped-block-inner') || target.parentElement;
    let currentAlign = 'left';

    if (target.closest('.editor-card')) {
      const as = target.style.alignSelf || 'flex-start';
      const revMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
      currentAlign = revMap[as] || 'left';
    } else {
      const jc = wrapper ? wrapper.style.justifyContent : 'flex-start';
      const alignMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
      currentAlign = alignMap[jc] || 'left';
    }
    
    const currentSrc = target.getAttribute('src') || target.src;
    return {
      index,
      type: 'image',
      src: currentSrc,
      alt: target.alt || '',
      width: target.style.width || '100%',
      height: target.style.height || 'auto',
      align: currentAlign,
      cssClasses: target.dataset.cssClasses || '',
    };
  }

  // 6. Divider
  if (target.classList.contains('editor-divider')) {
    return {
      index,
      type: 'divider',
      color: target.style.borderColor || '',
      cssClasses: target.dataset.cssClasses || '',
    };
  }

  // 7. Spacer
  if (target.classList.contains('editor-spacer')) {
    return {
      index,
      type: 'spacer',
      heightDesktop: target.dataset.heightDesktop || '10',
      heightMobile: target.dataset.heightMobile || '10',
      display: target.dataset.display || 'all',
    };
  }

  // 8. Accordion
  if (target.classList.contains('editor-accordion')) {
    const items = [];
    target.querySelectorAll('.acc-item').forEach(item => {
      const header = item.querySelector('.acc-header');
      const contentZone = item.querySelector('.acc-content');
      items.push({
        header: header ? header.innerText : '',
        blocks: collectBlocksFromContainer(contentZone)
      });
    });
    return {
      index,
      type: 'accordion',
      items: items,
      cssClasses: target.dataset.cssClasses || '',
    };
  }

  // 9. Columns (2col / 3col)
  if (target.classList.contains('editor-2col') || target.classList.contains('editor-3col')) {
    const columns = [];
    target.querySelectorAll('.col-drop-zone').forEach(col => {
      columns.push(collectBlocksFromContainer(col));
    });
    return {
      index,
      type: target.classList.contains('editor-2col') ? '2col' : '3col',
      gap: target.style.gap || '20px',
      columns: columns
    };
  }

  // 10. Container
  if (target.classList.contains('editor-container')) {
    return {
      index,
      type: 'container',
      paddingTop: target.style.paddingTop || '20px',
      paddingBottom: target.style.paddingBottom || '20px',
      backgroundColor: target.style.backgroundColor || 'transparent',
      flexDirection: target.style.flexDirection || 'column',
      justifyContent: target.style.justifyContent || 'flex-start',
      alignItems: target.style.alignItems || 'stretch',
      blocks: collectBlocksFromContainer(target)
    };
  }

  // 11. Video
  if (target.classList.contains('editor-video')) {
    return {
      index,
      type: 'video',
      host: target.dataset.host || 'youtube',
      url: target.dataset.url || '',
      poster: target.dataset.poster || '',
      autoplay: target.dataset.autoplay === 'true',
      loop: target.dataset.loop === 'true',
      controls: target.dataset.controls !== 'false'
    };
  }

  // 11. Icon
  if (target.classList.contains('editor-icon')) {
    const i = target.querySelector('i');
    const jc = target.style.justifyContent || 'flex-start';
    const alignMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
    const block = target.closest('.dropped-block');
    return {
      index,
      type: 'icon',
      iconClass: i ? i.className : '',
      size: i ? i.style.fontSize : '24px',
      color: i ? i.style.color : '',
      width: target.style.width || 'auto',
      padding: target.style.paddingTop || '10px',
      align: alignMap[jc] || 'left',
      marginTop: target.style.marginTop || '0px',
      marginBottom: target.style.marginBottom || '0px',
      isFloating: block ? block.classList.contains('free-moving') : false,
      top: block ? block.style.top : '',
      left: block ? block.style.left : ''
    };
  }

  // 12. IFrame
  if (target.classList.contains('editor-iframe')) {
    const ifr = target.querySelector('iframe');
    return {
      index,
      type: 'iframe',
      src: ifr ? ifr.src : '',
      title: ifr ? ifr.title : '',
      height: ifr ? ifr.height : (target.querySelector('.iframe-placeholder') ? target.querySelector('.iframe-placeholder').style.height : '300')
    };
  }

  // 13. Card
  if (target.classList.contains('editor-card')) {
    const cardBody = target.querySelector('.card-body');
    const cardImg = target.querySelector('.editor-image');
    const wrapper = target.closest('.dropped-block-inner');
    return {
      index,
      type: 'card',
      backgroundColor: target.style.backgroundColor || 'transparent',
      width: target.style.width || '100%',
      flexDirection: wrapper ? wrapper.style.flexDirection : 'column',
      justifyContent: wrapper ? wrapper.style.justifyContent : 'flex-start',
      alignItems: wrapper ? wrapper.style.alignItems : 'stretch',
      gap: wrapper ? wrapper.style.gap : '0px',
      image: cardImg ? {
        src: cardImg.getAttribute('src') || cardImg.src,
        alt: cardImg.alt || '',
        width: cardImg.style.width || '100%',
        height: cardImg.style.height || 'auto'
      } : null,
      blocks: collectBlocksFromContainer(cardBody)
    };
  }

  // 14. Cart
  if (target.classList.contains('editor-cart')) {
    const textEl = target.querySelector('.cart-text');
    const linkEl = target.querySelector('a');
    const blockParent = target.closest('.dropped-block');
    return {
      index,
      type: 'cart',
      text: textEl ? textEl.textContent : 'Items (0)',
      href: linkEl ? linkEl.getAttribute('href') : '#',
      isFloating: blockParent ? blockParent.classList.contains('free-moving') : false,
      top: blockParent ? blockParent.style.top : '',
      left: blockParent ? blockParent.style.left : ''
    };
  }

  return null;
}
