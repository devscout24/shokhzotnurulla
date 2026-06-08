// ── Carousel Settings Panel ──────────────────────────────────────────────────

function openCarouselSettings(el) {
  openPanel('carousel-settings-panel');
  activeEl = el;
  const block = el.closest('.dropped-block');
  if (block) block.classList.add('selected');

  if (typeof syncVisibilityToggles === 'function') syncVisibilityToggles(block);

  document.getElementById('car-interval').value = el.dataset.interval || 5000;
  document.getElementById('car-autoplay').checked = el.dataset.autoplay !== 'false';
  document.getElementById('car-nav-style').value = el.dataset.navStyle || 'both';
  document.getElementById('car-height').value = el.dataset.height || 400;

  renderSlideGallery(el);
}

function renderSlideGallery(el) {
  const gallery = document.getElementById('car-slide-gallery');
  if (!gallery) return;
  gallery.innerHTML = '';

  let slides = [];
  try {
    slides = JSON.parse(el.dataset.slides || '[]');
  } catch (e) {
    slides = [];
  }

  if (slides.length === 0) {
    gallery.innerHTML = '<div class="text-muted small text-center py-3">No slides yet. Add one below.</div>';
    return;
  }

  slides.forEach((slide, i) => {
    const slideEl = document.createElement('div');
    slideEl.className = 'car-slide-item';
    slideEl.style.cssText = 'display:flex;align-items:center;gap:10px;padding:8px;border:1px solid #e0e6ed;border-radius:6px;margin-bottom:6px;background:#fcfdfe';
    slideEl.innerHTML = `
      <div style="width:50px;height:50px;border-radius:4px;overflow:hidden;flex-shrink:0;background:#f1f3f5;display:flex;align-items:center;justify-content:center">
        ${slide.url
          ? `<img src="${slide.url}" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display='none'" />`
          : '<i class="fa-solid fa-image text-muted"></i>'}
      </div>
      <div style="flex:1;min-width:0">
        <input class="hs-input car-slide-url" data-index="${i}" value="${slide.url || ''}" placeholder="Image URL" style="font-size:12px;padding:6px 8px" />
      </div>
      <div style="display:flex;gap:4px;flex-shrink:0">
        <button type="button" class="car-slide-up btn btn-sm btn-outline-secondary" data-index="${i}" ${i === 0 ? 'disabled' : ''} style="padding:2px 6px;font-size:11px"><i class="fa-solid fa-chevron-up"></i></button>
        <button type="button" class="car-slide-down btn btn-sm btn-outline-secondary" data-index="${i}" ${i === slides.length - 1 ? 'disabled' : ''} style="padding:2px 6px;font-size:11px"><i class="fa-solid fa-chevron-down"></i></button>
        <button type="button" class="car-slide-remove btn btn-sm btn-outline-danger" data-index="${i}" style="padding:2px 6px;font-size:11px"><i class="fa-solid fa-trash-can"></i></button>
      </div>
    `;
    gallery.appendChild(slideEl);
  });

  gallery.querySelectorAll('.car-slide-url').forEach(input => {
    input.addEventListener('input', onSlideUrlChange);
  });
  gallery.querySelectorAll('.car-slide-up').forEach(btn => {
    btn.addEventListener('click', onSlideMoveUp);
  });
  gallery.querySelectorAll('.car-slide-down').forEach(btn => {
    btn.addEventListener('click', onSlideMoveDown);
  });
  gallery.querySelectorAll('.car-slide-remove').forEach(btn => {
    btn.addEventListener('click', onSlideRemove);
  });
}

function getSlides() {
  if (!activeEl) return [];
  try {
    return JSON.parse(activeEl.dataset.slides || '[]');
  } catch (e) {
    return [];
  }
}

function setSlides(slides) {
  if (!activeEl) return;
  activeEl.dataset.slides = JSON.stringify(slides);
  rebuildCarouselPreview(activeEl, slides);
  renderSlideGallery(activeEl);
  if (typeof saveHistory === 'function') saveHistory();
}

function rebuildCarouselPreview(el, slides) {
  if (!slides || slides.length === 0) {
    el.innerHTML = `
      <div class="carousel-empty" style="background:#333;height:200px;display:flex;align-items:center;justify-content:center;color:#fff;border-radius:4px;flex-direction:column;gap:8px">
        <i class="fa-solid fa-images" style="font-size:2rem"></i>
        <span style="font-size:13px;opacity:0.8">No slides — add images in settings</span>
      </div>
    `;
    return;
  }

  const interval = parseInt(el.dataset.interval) || 5000;
  const navStyle = el.dataset.navStyle || 'both';
  const height = parseInt(el.dataset.height) || 400;

  let indicators = '';
  let controls = '';

  if (navStyle === 'dots' || navStyle === 'both') {
    indicators = '<div class="carousel-indicators" style="position:absolute;bottom:10px;left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:2">' +
      slides.map((_, i) => `<button type="button" style="width:10px;height:10px;border-radius:50%;border:2px solid #fff;background:${i === 0 ? '#fff' : 'transparent'};cursor:pointer;padding:0" data-slide="${i}" class="car-dot"></button>`).join('') +
      '</div>';
  }

  if (navStyle === 'arrows' || navStyle === 'both') {
    controls = `
      <button type="button" class="car-prev" style="position:absolute;top:50%;left:10px;transform:translateY(-50%);z-index:2;background:rgba(0,0,0,0.4);color:#fff;border:none;border-radius:50%;width:36px;height:36px;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-chevron-left"></i></button>
      <button type="button" class="car-next" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);z-index:2;background:rgba(0,0,0,0.4);color:#fff;border:none;border-radius:50%;width:36px;height:36px;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-chevron-right"></i></button>
    `;
  }

  el.innerHTML = `
    <div class="carousel-slides-wrapper" style="position:relative;width:100%;height:${height}px;overflow:hidden;border-radius:4px;background:#1a1a2e">
      ${slides.map((slide, i) => `
        <div class="carousel-slide ${i === 0 ? 'active' : ''}" data-index="${i}" style="position:absolute;inset:0;transition:opacity 0.5s ease;opacity:${i === 0 ? 1 : 0};display:flex;align-items:center;justify-content:center;background:#1a1a2e">
          ${slide.url
            ? `<img src="${slide.url}" style="width:100%;height:100%;object-fit:cover" onerror="this.closest('.carousel-slide').innerHTML='<span style=color:#666>Bad URL</span>'" />`
            : '<span style="color:#555">No image</span>'}
        </div>
      `).join('')}
      ${indicators}
      ${controls}
    </div>
  `;

  const wrapper = el.querySelector('.carousel-slides-wrapper');
  if (wrapper) {
    let currentSlide = 0;
    const slidesEls = wrapper.querySelectorAll('.carousel-slide');
    const dots = wrapper.querySelectorAll('.car-dot');
    let autoplayTimer = null;

    function goToSlide(index) {
      slidesEls.forEach((s, i) => {
        s.classList.toggle('active', i === index);
        s.style.opacity = i === index ? 1 : 0;
      });
      dots.forEach((d, i) => {
        d.style.background = i === index ? '#fff' : 'transparent';
      });
      currentSlide = index;
    }

    function nextSlide() {
      goToSlide((currentSlide + 1) % slidesEls.length);
    }

    function prevSlide() {
      goToSlide((currentSlide - 1 + slidesEls.length) % slidesEls.length);
    }

    const prevBtn = wrapper.querySelector('.car-prev');
    const nextBtn = wrapper.querySelector('.car-next');

    if (prevBtn) prevBtn.addEventListener('click', (e) => { e.stopPropagation(); prevSlide(); });
    if (nextBtn) nextBtn.addEventListener('click', (e) => { e.stopPropagation(); nextSlide(); });

    dots.forEach(dot => {
      dot.addEventListener('click', (e) => {
        e.stopPropagation();
        goToSlide(parseInt(dot.dataset.slide));
      });
    });

    if (activeEl && activeEl.dataset.autoplay !== 'false') {
      autoplayTimer = setInterval(nextSlide, interval);
      wrapper.addEventListener('mouseenter', () => { if (autoplayTimer) clearInterval(autoplayTimer); });
      wrapper.addEventListener('mouseleave', () => { autoplayTimer = setInterval(nextSlide, interval); });
    }
  }
}

// ── Slide Event Handlers ─────────────────────────────────────────────────────

function onSlideUrlChange(e) {
  const index = parseInt(e.target.dataset.index);
  const slides = getSlides();
  if (!slides[index]) return;
  slides[index].url = e.target.value;
  setSlides(slides);
}

function onSlideMoveUp(e) {
  const index = parseInt(e.target.closest('.car-slide-up').dataset.index);
  if (index <= 0) return;
  const slides = getSlides();
  [slides[index - 1], slides[index]] = [slides[index], slides[index - 1]];
  setSlides(slides);
}

function onSlideMoveDown(e) {
  const index = parseInt(e.target.closest('.car-slide-down').dataset.index);
  const slides = getSlides();
  if (index >= slides.length - 1) return;
  [slides[index], slides[index + 1]] = [slides[index + 1], slides[index]];
  setSlides(slides);
}

function onSlideRemove(e) {
  const index = parseInt(e.target.closest('.car-slide-remove').dataset.index);
  const slides = getSlides();
  slides.splice(index, 1);
  setSlides(slides);
}

// ── Upload ───────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
  const uploadBtn = document.getElementById('car-upload-btn');
  const uploadInput = document.getElementById('car-upload-input');

  if (uploadBtn && uploadInput) {
    let targetEl = null;

    uploadBtn.onclick = (e) => {
      e.preventDefault();
      e.stopPropagation();
      targetEl = activeEl;
      if (!targetEl) {
        alert('Please click a carousel block first.');
        return;
      }
      uploadInput.value = '';
      uploadInput.click();
    };

    uploadInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      const currentTarget = targetEl;
      if (!currentTarget) {
        alert('Carousel block not found. Please try again.');
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
        if (data.success && data.media && data.media[0] && data.media[0].url) {
          const url = data.media[0].url;
          const slides = getSlides();
          slides.push({ url: url });
          setSlides(slides);
        } else {
          alert('Upload failed: ' + (data.message || JSON.stringify(data)));
        }
      })
      .catch(err => {
        console.error('[Carousel Upload] Error:', err);
        alert('Upload error: ' + err.message);
      })
      .finally(() => {
        uploadBtn.innerHTML = '<i class="fa-solid fa-upload"></i>';
        uploadBtn.disabled = false;
        uploadInput.value = '';
      });
    });
  }
});

// ── Panel Event Listeners ──────────────────────────────────────────────────

document.getElementById('car-interval')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.interval = e.target.value;
    rebuildCarouselPreview(activeEl, getSlides());
    if (typeof saveHistory === 'function') saveHistory();
  }
});

document.getElementById('car-autoplay')?.addEventListener('change', e => {
  if (activeEl) {
    activeEl.dataset.autoplay = e.target.checked;
    rebuildCarouselPreview(activeEl, getSlides());
    if (typeof saveHistory === 'function') saveHistory();
  }
});

document.getElementById('car-nav-style')?.addEventListener('change', e => {
  if (activeEl) {
    activeEl.dataset.navStyle = e.target.value;
    rebuildCarouselPreview(activeEl, getSlides());
    if (typeof saveHistory === 'function') saveHistory();
  }
});

document.getElementById('car-height')?.addEventListener('input', e => {
  if (activeEl) {
    activeEl.dataset.height = e.target.value;
    rebuildCarouselPreview(activeEl, getSlides());
    if (typeof saveHistory === 'function') saveHistory();
  }
});

document.getElementById('car-add-url-btn')?.addEventListener('click', () => {
  if (!activeEl) return;
  const urlInput = document.getElementById('car-add-url');
  const url = urlInput ? urlInput.value.trim() : '';
  if (!url) {
    alert('Please enter an image URL or use the upload button.');
    return;
  }
  const slides = getSlides();
  slides.push({ url: url });
  setSlides(slides);
  urlInput.value = '';
});

document.getElementById('car-back-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('car-cancel-btn')?.addEventListener('click', closeAllPanels);
document.getElementById('car-remove-btn')?.addEventListener('click', () => {
  if (activeEl) {
    const block = activeEl.closest('.dropped-block');
    if (block) {
      block.remove();
      checkEmptyBlocks();
      if (typeof saveHistory === 'function') saveHistory();
    }
  }
  closeAllPanels();
});

// ── Drop Carousel Block ─────────────────────────────────────────────────────

function dropCarouselBlock(returnBlock = false) {
  const emptyState = document.getElementById('empty-state');
  const blocksContainer = document.getElementById('blocks-container');
  if (emptyState) emptyState.style.display = 'none';

  const block = document.createElement('div');
  block.className = 'dropped-block';
  block.innerHTML = `
    <span class="dropped-block-badge">Carousel <i class="fa-solid fa-copy copy-btn" title="Duplicate"></i></span>
    <div class="block-reorder-tools">
      <button type="button" class="reorder-btn drag-handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></button>
      <button type="button" class="reorder-btn move-up-btn" title="Move Up"><i class="fa-solid fa-chevron-up"></i></button>
      <button type="button" class="reorder-btn move-down-btn" title="Move Down"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
    <div class="dropped-block-inner">
      <div class="editor-carousel" style="width:100%">
        <div style="background:#333;height:200px;display:flex;align-items:center;justify-content:center;color:#fff;border-radius:4px;flex-direction:column;gap:8px">
          <i class="fa-solid fa-images" style="font-size:3rem"></i>
          <span style="font-size:13px;opacity:0.8">Click to configure carousel</span>
        </div>
      </div>
    </div>`;

  const car = block.querySelector('.editor-carousel');

  if (returnBlock) return block;

  blocksContainer.appendChild(block);
  attachBlockListeners(block);

  if (car) openCarouselSettings(car);
  if (typeof saveHistory === 'function') saveHistory();
}

window.dropCarouselBlock = dropCarouselBlock;
window.openCarouselSettings = openCarouselSettings;
