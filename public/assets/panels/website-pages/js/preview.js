// ── Separate Page Preview ───────────────────────────────────────────────────

const btnPreview = document.getElementById('btn-preview');

if (btnPreview) {
  btnPreview.addEventListener('click', (e) => {
    e.preventDefault();

    // 1. Get the content
    const blocksContainer = document.getElementById('blocks-container');
    const clone = blocksContainer.cloneNode(true);

    // 2. Clean the clone (remove editor-only elements)
    const selectorsToRemove = [
      '.dropped-block-badge',
      '.block-reorder-tools',
      '.copy-btn',
      '#drop-indicator'
    ];

    selectorsToRemove.forEach(selector => {
      clone.querySelectorAll(selector).forEach(el => el.remove());
    });

    // Remove contenteditable attributes
    clone.querySelectorAll('[contenteditable]').forEach(el => {
      el.removeAttribute('contenteditable');
      el.removeAttribute('spellcheck');
    });

    // 3. Get the page title
    const pageTitle = document.querySelector('input[name="title"]').value || 'Untitled Page';

    // 4. Save to localStorage
    localStorage.setItem('previewContent', clone.innerHTML);
    localStorage.setItem('previewTitle', pageTitle);

    // 5. Open preview in new tab
    window.open('preview.html', '_blank');
  });
}
