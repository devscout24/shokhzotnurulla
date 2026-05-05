// ── Image Settings Panel ─────────────────────────────────────────────────────

function openImageSettings(el) {
  openPanel('image-settings-panel');
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');

  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  document.getElementById('is-url').value = el.src || '';
  document.getElementById('is-alt').value = el.alt || '';
  document.getElementById('is-link').value = el.dataset.link || '';
  document.getElementById('is-newtab').checked = el.dataset.newtab === 'true';
  document.getElementById('is-width').value = el.style.width ? parseInt(el.style.width) : 100;
  document.getElementById('is-height').value = el.style.height && el.style.height !== 'auto' ? parseInt(el.style.height) : '';
  document.getElementById('is-opacity').value = el.style.opacity || 1;

  const wrapper = el.closest('.dropped-block-inner');
  const jc = wrapper ? wrapper.style.justifyContent : 'flex-start';
  const alignMap = { 'flex-start': 'left', 'center': 'center', 'flex-end': 'right' };
  const curAlign = alignMap[jc] || 'left';
  
  document.querySelectorAll('.is-align-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.align === curAlign);
  });
}

// Back / Cancel / Apply
document.getElementById('is-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('is-cancel-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('is-apply-btn')?.addEventListener('click', closeAllPanels);

// Inputs
document.getElementById('is-url')?.addEventListener('input', e => { if(activeEl) { activeEl.src = e.target.value; if(typeof saveHistory === 'function') saveHistory(); } });
document.getElementById('is-alt')?.addEventListener('input', e => { if(activeEl) { activeEl.alt = e.target.value; if(typeof saveHistory === 'function') saveHistory(); } });
document.getElementById('is-link')?.addEventListener('input', e => { if(activeEl) { activeEl.dataset.link = e.target.value; if(typeof saveHistory === 'function') saveHistory(); } });
document.getElementById('is-newtab')?.addEventListener('change', e => { if(activeEl) { activeEl.dataset.newtab = e.target.checked; if(typeof saveHistory === 'function') saveHistory(); } });
document.getElementById('is-width')?.addEventListener('input', e => { if(activeEl) { activeEl.style.width = (e.target.value || 100) + '%'; if(typeof saveHistory === 'function') saveHistory(); } });
document.getElementById('is-height')?.addEventListener('input', e => { 
    if(activeEl) { 
        if (e.target.value) {
            activeEl.style.height = e.target.value + 'px';
        } else {
            activeEl.style.height = 'auto';
        }
        if(typeof saveHistory === 'function') saveHistory(); 
    } 
});
document.getElementById('is-opacity')?.addEventListener('input', e => { if(activeEl) { activeEl.style.opacity = e.target.value; if(typeof saveHistory === 'function') saveHistory(); } });

// Align
document.querySelectorAll('.is-align-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    if(!activeEl) return;
    document.querySelectorAll('.is-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const align = btn.dataset.align;
    const wrapper = activeEl.closest('.dropped-block-inner');
    if (wrapper) {
        wrapper.style.display = 'flex';
        const jcMap = { 'left': 'flex-start', 'center': 'center', 'right': 'flex-end' };
        wrapper.style.justifyContent = jcMap[align] || 'flex-start';
    }
    if(typeof saveHistory === 'function') saveHistory();
  });
});

// ── Upload ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const uploadBtn = document.getElementById('is-upload-btn');
  const uploadInput = document.getElementById('is-upload-input');

  if (uploadBtn && uploadInput) {
    // Store the target element at click time so file dialog doesn't lose it
    let targetEl = null;

    uploadBtn.onclick = (e) => {
      e.preventDefault();
      e.stopPropagation();
      targetEl = activeEl; // capture NOW, before file dialog opens
      if (!targetEl) {
        alert('Please select an image block first by clicking on it.');
        return;
      }
      uploadInput.value = '';
      uploadInput.click();
    };

    uploadInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      // Use targetEl captured at click time — NOT activeEl (may be null after dialog)
      const currentTarget = targetEl;
      if (!currentTarget) {
        alert('Image block not found. Please click the image block and try again.');
        return;
      }

      const formData = new FormData();
      formData.append('files[]', file);
      uploadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
      uploadBtn.disabled = true;

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
      const uploadUrl = (window.CMS_CONFIG && window.CMS_CONFIG.upload_url)
        ? window.CMS_CONFIG.upload_url
        : '/dealer/website/media/upload';

      console.log('[Image Upload] Uploading to:', uploadUrl);

      fetch(uploadUrl, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': csrfToken }
      })
      .then(r => {
        if (!r.ok) throw new Error('Server error: HTTP ' + r.status);
        return r.json();
      })
      .then(data => {
        console.log('[Image Upload] Response:', data);
        if (data.success && data.media && data.media[0] && data.media[0].url) {
          const url = data.media[0].url;
          // Update the settings panel URL input
          const urlInput = document.getElementById('is-url');
          if (urlInput) urlInput.value = url;
          // Update the actual image element on canvas
          currentTarget.src = url;
          currentTarget.setAttribute('src', url);
          if (typeof saveHistory === 'function') saveHistory();
        } else {
          alert('Upload failed: ' + (data.message || JSON.stringify(data)));
        }
      })
      .catch(err => {
        console.error('[Image Upload] Error:', err);
        alert('Upload error: ' + err.message + '\n\nCheck browser console (F12) for details.');
      })
      .finally(() => {
        uploadBtn.innerHTML = '<i class="fa-solid fa-upload"></i>';
        uploadBtn.disabled = false;
        uploadInput.value = '';
      });
    });
  } else {
    console.warn('[Image Upload] uploadBtn or uploadInput not found in DOM');
  }
});

// Remove
document.getElementById('is-remove-btn')?.addEventListener('click', () => {
  if (activeEl) { activeEl.closest('.dropped-block').remove(); checkEmptyBlocks(); if (typeof saveHistory === 'function') saveHistory(); }
  closeAllPanels();
});

// ── Drop ─────────────────────────────────────────────────────────────────────
function dropImageBlock(returnBlock = false) {
  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">Image <i class="fa-solid fa-copy copy-btn"></i></span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner" style="display:flex; justify-content:flex-start;">
      <img src="https://via.placeholder.com/300x150?text=Click+to+set+image" class="editor-image" style="width:100%;height:auto;display:block;"/>
    </div>`;

  if (returnBlock) return block;
  document.getElementById('blocks-container').appendChild(block);
  attachBlockListeners(block);
  const img = block.querySelector('img');
  openImageSettings(img);
  if (typeof saveHistory === 'function') saveHistory();
}

window.dropImageBlock = dropImageBlock;
window.openImageSettings = openImageSettings;
