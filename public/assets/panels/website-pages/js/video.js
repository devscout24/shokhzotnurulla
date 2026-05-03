// ── Video Settings Panel ─────────────────────────────────────────────────────

function openVideoSettings(el) {
  closeAllPanels();
  activeEl = el;
  const block = el.closest('.dropped-block');
  block.classList.add('selected');
  document.getElementById('video-settings-panel').style.display = 'block';

  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  document.getElementById('vs-host').value = el.dataset.host || 'youtube';
  document.getElementById('vs-url').value = el.dataset.url || '';
  document.getElementById('vs-poster').value = el.dataset.poster || '';
  document.getElementById('vs-autoplay').checked = el.dataset.autoplay === 'true';
  document.getElementById('vs-loop').checked = el.dataset.loop === 'true';
  document.getElementById('vs-controls').checked = el.dataset.controls !== 'false';
}

// Back / Cancel
document.getElementById('vs-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('vs-cancel-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('vs-apply-btn')?.addEventListener('click', closeAllPanels);

// Host / URL
document.getElementById('vs-host')?.addEventListener('change', e => { if(activeEl) { activeEl.dataset.host = e.target.value; updateVideoPreview(activeEl); if(typeof saveHistory === 'function') saveHistory(); } });
document.getElementById('vs-url')?.addEventListener('input', e => { if(activeEl) { activeEl.dataset.url = e.target.value; updateVideoPreview(activeEl); if(typeof saveHistory === 'function') saveHistory(); } });
document.getElementById('vs-poster')?.addEventListener('input', e => { if(activeEl) { activeEl.dataset.poster = e.target.value; updateVideoPreview(activeEl); if(typeof saveHistory === 'function') saveHistory(); } });

// Switches
['vs-autoplay', 'vs-loop', 'vs-controls'].forEach(id => {
  document.getElementById(id)?.addEventListener('change', e => {
    if (activeEl) { activeEl.dataset[id.replace('vs-', '')] = e.target.checked; if(typeof saveHistory === 'function') saveHistory(); }
  });
});

function updateVideoPreview(el) {
  const host = el.dataset.host || 'youtube';
  const url = el.dataset.url || '';
  const inner = el.querySelector('.video-inner-preview');
  if (!inner) return;

  if (!url) {
    inner.innerHTML = '<div style="background:#000;height:200px;display:flex;align-items:center;justify-content:center;color:#fff"><i class="fa-solid fa-play-circle" style="font-size:3rem;opacity:0.5"></i></div>';
    return;
  }

  if (host === 'youtube') {
    const id = extractYouTubeId(url);
    inner.innerHTML = `<img src="https://img.youtube.com/vi/${id}/hqdefault.jpg" style="width:100%;height:200px;object-fit:cover;display:block;"/><div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:3rem;text-shadow:0 2px 10px rgba(0,0,0,0.5)"><i class="fa-solid fa-play-circle"></i></div>`;
  } else {
    inner.innerHTML = `<div style="background:#222;height:200px;display:flex;align-items:center;justify-content:center;color:#fff;flex-direction:column;gap:10px"><i class="fa-solid fa-file-video" style="font-size:2.5rem;opacity:0.7"></i><span style="font-size:12px">MP4 Video</span></div>`;
  }
}

function extractYouTubeId(url) {
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
  const match = url.match(regExp);
  return (match && match[2].length === 11) ? match[2] : url;
}

// ── Upload ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const uploadBtn = document.getElementById('vs-upload-btn');
  if (uploadBtn) {
    uploadBtn.onclick = (e) => {
      e.preventDefault();
      const currentTarget = activeEl;
      if (!currentTarget) {
        alert('Please select a video block first.');
        return;
      }

      let input = document.getElementById('vs-upload-input-dynamic');
      if (!input) {
        input = document.createElement('input');
        input.type = 'file';
        input.id = 'vs-upload-input-dynamic';
        input.style.display = 'none';
        input.accept = 'video/*';
        document.body.appendChild(input);
      }

      input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
          const formData = new FormData();
          formData.append('files[]', file);
          uploadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

          const uploadUrl = (window.CMS_CONFIG && window.CMS_CONFIG.upload_url) ? window.CMS_CONFIG.upload_url : '/dealer/website/media/upload';
          fetch(uploadUrl, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
          })
          .then(r => {
            if (!r.ok) throw new Error('Upload failed with status ' + r.status);
            return r.json();
          })
          .then(data => {
            if (data.success && data.media?.[0]?.url) {
              const url = data.media[0].url;
              document.getElementById('vs-url').value = url;
              document.getElementById('vs-host').value = 'overfuel';
              
              if (currentTarget) {
                currentTarget.dataset.url = url;
                currentTarget.dataset.host = 'overfuel';
                updateVideoPreview(currentTarget);
              }
              if (typeof saveHistory === 'function') saveHistory();
            } else {
              alert('Upload failed: ' + (data.message || 'Unknown error'));
            }
          })
          .catch(err => {
            console.error('Upload Error:', err);
            alert('Error uploading video. Please check your connection or file size.');
          })
          .finally(() => { 
            uploadBtn.innerHTML = '<i class="fa-solid fa-upload"></i>'; 
            input.value = ''; 
          });
        }
      };
      input.click();
    };
  }
});

// Remove
document.getElementById('vs-remove-btn')?.addEventListener('click', () => {
  if (activeEl) { activeEl.closest('.dropped-block').remove(); checkEmptyBlocks(); if(typeof saveHistory === 'function') saveHistory(); }
  closeAllPanels();
});

// ── Drop ─────────────────────────────────────────────────────────────────────
function dropVideoBlock(returnBlock = false) {
  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">Video <i class="fa-solid fa-copy copy-btn"></i></span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <div class="editor-video" style="width:100%;position:relative;cursor:pointer;" data-host="youtube" data-controls="true">
        <div class="video-inner-preview">
           <div style="background:#000;height:200px;display:flex;align-items:center;justify-content:center;color:#fff"><i class="fa-solid fa-play-circle" style="font-size:3rem;opacity:0.5"></i></div>
        </div>
      </div>
    </div>`;

  if (returnBlock) return block;
  document.getElementById('blocks-container').appendChild(block);
  attachBlockListeners(block);
  const v = block.querySelector('.editor-video');
  openVideoSettings(v);
  if (typeof saveHistory === 'function') saveHistory();
}

window.dropVideoBlock = dropVideoBlock;
window.openVideoSettings = openVideoSettings;
