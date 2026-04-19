(function () {

    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    const ROUTES = window.mediaRoutes;

    // ── State ─────────────────────────────────────────────────────────────────
    let pendingFiles = [];
    let currentTab     = 'all';
    let currentPage    = 1;
    let currentDisplay = 'compact';
    let searchQuery    = '';
    let searchTimer    = null;
    let totalPages     = 1;
    let totalItems     = 0;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const mlGrid           = document.getElementById('mlGrid');
    const mlPagination     = document.getElementById('mlPagination');
    const mlShowing        = document.getElementById('mlShowing');
    const mlPaginationWrap = document.getElementById('mlPaginationWrap');
    const mlDisplayMode    = document.getElementById('mlDisplayMode');
    const mlSearch         = document.getElementById('mlSearch');
    const mlUploadZone     = document.getElementById('mlUploadZone');
    const mlUploadActions  = document.getElementById('mlUploadActions');

    // ── Fetch ─────────────────────────────────────────────────────────────────
    async function fetchMedia() {
        showLoading();

        const params = new URLSearchParams({
            page:   currentPage,
            search: searchQuery,
        });

        // 'all' hone par type parameter mat bhejo
        if (currentTab !== 'all') {
            params.set('type', currentTab);
        }

        try {
            const res  = await fetch(`${ROUTES.list}?${params}`, {
                headers: { 'Accept': 'application/json' },
            });

            if (! res.ok) throw new Error('Server error');

            const data = await res.json();
            totalPages = data.last_page  ?? 1;
            totalItems = data.total      ?? 0;

            render(data.data ?? []);

        } catch (err) {
            console.error('Media fetch error:', err);
            mlGrid.innerHTML = '<div class="ml-empty">Failed to load media. Please refresh.</div>';
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────
    function showLoading() {
        mlGrid.innerHTML = '<div class="ml-empty">Loading...</div>';
        mlPaginationWrap.style.display = 'none';
    }

    function render(items) {
        mlGrid.innerHTML = '';

        if (! items.length) {
            mlGrid.innerHTML = '<div class="ml-empty">No media found.</div>';
            mlPaginationWrap.style.display = 'none';
            return;
        }

        mlPaginationWrap.style.display = '';

        const gridClass =
            currentDisplay === 'comfortable' ? 'ml-grid-comfortable' :
            currentDisplay === 'expanded'    ? 'ml-grid-expanded'    :
                                               'ml-grid-compact';

        const grid = document.createElement('div');
        grid.className = gridClass;

        items.forEach(item => {
            const el = document.createElement('div');
            el.className  = 'ml-item';
            el.dataset.id = item.id;

            const thumb = buildThumb(item);

            if (currentDisplay === 'compact') {
                el.innerHTML = thumb;
            } else if (currentDisplay === 'comfortable') {
                el.innerHTML = `
                    ${thumb}
                    <div class="ml-item-info">
                        <div class="ml-item-name" title="${item.original_name}">${item.original_name}</div>
                        <div><strong>Size:</strong> ${item.size}</div>
                        ${item.dimensions !== '—' ? `<div><strong>Dimensions:</strong> ${item.dimensions}</div>` : ''}
                    </div>`;
            } else {
                // expanded
                el.innerHTML = `
                    ${thumb}
                    <div class="ml-item-info">
                        <div class="ml-item-name" title="${item.original_name}">${item.original_name}</div>
                        <div><strong>Type:</strong> ${item.mime_type}</div>
                        <div><strong>Size:</strong> ${item.size}</div>
                        ${item.dimensions !== '—' ? `<div><strong>Dimensions:</strong> ${item.dimensions}</div>` : ''}
                        <div><strong>Uploaded:</strong> ${item.created_at}</div>
                    </div>`;
            }

            // Click — open offcanvas
            el.addEventListener('click', () => openOffcanvas(item));

            // Delete button
            el.querySelector('.ml-delete-btn')?.addEventListener('click', (e) => {
                e.stopPropagation();
                confirmDelete(item.id, el);
            });

            grid.appendChild(el);
        });

        mlGrid.appendChild(grid);

        // Showing text
        const start = (currentPage - 1) * 50 + 1;
        const end   = Math.min(currentPage * 50, totalItems);
        mlShowing.textContent = totalItems
            ? `Showing ${start}–${end} of ${totalItems}`
            : '';

        renderPagination();
    }

    function buildThumb(item) {
        if (item.type === 'video') {
            return `
                <div class="ml-thumb ml-thumb--video">
                    <i class="bi bi-play-circle-fill"></i>
                </div>`;
        }

        if (item.type === 'file') {
            const icon = fileIcon(item.mime_type);
            return `
                <div class="ml-thumb ml-thumb--file">
                    <i class="bi ${icon}"></i>
                </div>`;
        }

        // image
        return `
            <div class="ml-thumb ml-thumb--image">
                <img src="${item.url}" alt="${item.original_name}" loading="lazy">
            </div>`;
    }

    function fileIcon(mime) {
        if (mime.includes('pdf'))        return 'bi-file-earmark-pdf';
        if (mime.includes('word'))       return 'bi-file-earmark-word';
        if (mime.includes('excel') || mime.includes('spreadsheet')) return 'bi-file-earmark-excel';
        return 'bi-file-earmark';
    }

    // ── Copy URL ──────────────────────────────────────────────────────────────
    function copyUrl(url, el) {
        const fullUrl = window.location.origin + url;
        navigator.clipboard.writeText(fullUrl).then(() => {
            el.classList.add('ml-copied');
            showToast('URL copied to clipboard!', 'success');
            setTimeout(() => el.classList.remove('ml-copied'), 1500);
        }).catch(() => {
            showToast('Could not copy URL.', 'error');
        });
    }

    // ── Offcanvas ─────────────────────────────────────────────────────────────
    let currentMediaId = null;
    let currentMediaItem = null;

    const mlOffcanvas    = document.getElementById('mlOffcanvas');
    const mlOcMeta       = document.getElementById('mlOcMeta');
    const mlOcUrl        = document.getElementById('mlOcUrl');
    const mlOcTitle      = document.getElementById('mlOcTitle');
    const mlOcAltText    = document.getElementById('mlOcAltText');
    const mlOcCopyBtn    = document.getElementById('mlOcCopyBtn');
    const mlOcSaveBtn    = document.getElementById('mlOcSaveBtn');
    const mlOcDeleteBtn  = document.getElementById('mlOcDeleteBtn');
    const mlOcClose      = document.getElementById('mlOffcanvasClose');

    function openOffcanvas(item) {
        currentMediaId = item.id;
        currentMediaItem = item;

        // Preview
        const previewWrap = document.getElementById('mlOcPreviewWrap');
        if (item.type === 'image') {
            previewWrap.innerHTML = `<img class="img-fluid" src="${item.url}" alt="${item.original_name}">`;
        } else if (item.type === 'video') {
            previewWrap.innerHTML = `
                <div style="padding:2rem 0;">
                    <i class="bi bi-play-circle" style="font-size:4rem;color:#888;"></i>
                    <p class="mt-2 mb-0 text-muted small">${item.original_name}</p>
                </div>`;
        } else {
            previewWrap.innerHTML = `
                <div style="padding:2rem 0;">
                    <i class="bi ${fileIcon(item.mime_type)}" style="font-size:4rem;color:#888;"></i>
                    <p class="mt-2 mb-0 text-muted small">${item.original_name}</p>
                </div>`;
        }

        // Meta
        mlOcMeta.innerHTML =
            `<strong>Uploaded:</strong> ${item.created_at}<br>` +
            `<strong>By:</strong> ${item.uploaded_by ?? '—'}<br>` +
            `<strong>Size:</strong> ${item.size}<br>` +
            `<strong>Type:</strong> ${item.mime_type}<br>` +
            (item.dimensions !== '—' ? `<strong>Dimensions:</strong> ${item.dimensions}<br>` : '');

        // URL
        const fullUrl = item.url;
        mlOcUrl.value = fullUrl;

        // Fields
        mlOcTitle.value   = item.title    ?? '';
        mlOcAltText.value = item.alt_text ?? '';

        // Show offcanvas
        mlOffcanvas.classList.add('show');
        mlOffcanvas.style.visibility = 'visible';

        // Backdrop
        let backdrop = document.getElementById('mlOffcanvasBackdrop');
        if (!backdrop) {
            backdrop    = document.createElement('div');
            backdrop.id = 'mlOffcanvasBackdrop';
            backdrop.className = 'offcanvas-backdrop fade show';
            backdrop.addEventListener('click', closeOffcanvas);
            document.body.appendChild(backdrop);
        }
        document.body.classList.add('overflow-hidden');
    }

    function closeOffcanvas() {
        currentMediaId = null;
        mlOffcanvas.classList.remove('show');
        mlOffcanvas.style.visibility = 'hidden';

        const backdrop = document.getElementById('mlOffcanvasBackdrop');
        if (backdrop) backdrop.remove();
        document.body.classList.remove('overflow-hidden');
    }

    // Close button
    mlOcClose.addEventListener('click', closeOffcanvas);

    // Copy URL
    mlOcCopyBtn.addEventListener('click', () => {
        navigator.clipboard.writeText(mlOcUrl.value).then(() => {
            showToast('URL copied to clipboard!', 'success');
        }).catch(() => {
            showToast('Could not copy URL.', 'error');
        });
    });

    // Save
    mlOcSaveBtn.addEventListener('click', async () => {
        if (!currentMediaId) return;

        const url = ROUTES.update.replace('__id__', currentMediaId);

        try {
            const res  = await fetch(url, {
                method:  'PATCH',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept':       'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title:    mlOcTitle.value,
                    alt_text: mlOcAltText.value,
                }),
            });
            const json = await res.json();

            if (res.ok && json.success) {
                // ← Item object in-memory update karo
                currentMediaItem.title    = mlOcTitle.value;
                currentMediaItem.alt_text = mlOcAltText.value;

                showToast(json.message, 'success');
                closeOffcanvas(); // band karo — next baar open hone par fresh data hoga
            } else {
                showToast('Could not save.', 'error');
            }
        } catch (err) {
            showToast('Network error.', 'error');
        }
    });

    // Delete
    mlOcDeleteBtn.addEventListener('click', () => {
        if (!currentMediaId) return;
        confirmDelete(currentMediaId);
    });

    // ── Delete ────────────────────────────────────────────────────────────────
    async function confirmDelete(id, el = null) {
        if (!confirm('Delete this file? This cannot be undone.')) return;

        const url = ROUTES.destroy.replace('__id__', id);

        try {
            const res  = await fetch(url, {
                method:  'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept':       'application/json',
                },
            });
            const json = await res.json();

            if (res.ok && json.success) {
                if (el) el.remove();
                else {
                    // grid se card dhundo aur remove karo
                    const card = mlGrid.querySelector(`[data-id="${id}"]`);
                    if (card) card.remove();
                }
                totalItems--;
                closeOffcanvas();
                showToast(json.message, 'success');
                if (!mlGrid.querySelector('.ml-item')) fetchMedia();
            } else {
                showToast('Could not delete file.', 'error');
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
        }
    }

    // ── Pagination ────────────────────────────────────────────────────────────
    function renderPagination() {
        mlPagination.innerHTML = '';

        if (totalPages <= 1) {
            mlPagination.style.display = 'none';
            return;
        }

        mlPagination.style.display = '';

        const prev = makePageBtn('‹', false, currentPage === 1);
        prev.addEventListener('click', () => { currentPage--; fetchMedia(); });
        mlPagination.appendChild(prev);

        for (let i = 1; i <= totalPages; i++) {
            const btn = makePageBtn(i, i === currentPage, false);
            btn.addEventListener('click', () => { currentPage = i; fetchMedia(); });
            mlPagination.appendChild(btn);
        }

        const next = makePageBtn('›', false, currentPage === totalPages);
        next.addEventListener('click', () => { currentPage++; fetchMedia(); });
        mlPagination.appendChild(next);
    }

    function makePageBtn(label, active, disabled) {
        const btn     = document.createElement('button');
        btn.className = 'ml-page-btn' + (active ? ' active' : '');
        btn.innerHTML = label;
        btn.disabled  = disabled;
        return btn;
    }

    // ── Tabs ──────────────────────────────────────────────────────────────────
    document.querySelectorAll('.ml-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.ml-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTab  = btn.dataset.tab;
            currentPage = 1;
            fetchMedia();
        });
    });

    // ── Display mode ──────────────────────────────────────────────────────────
    mlDisplayMode.addEventListener('change', () => {
        currentDisplay = mlDisplayMode.value;
        fetchMedia();
    });

    // ── Search ────────────────────────────────────────────────────────────────
    mlSearch.addEventListener('input', () => {
        searchQuery = mlSearch.value.trim();
        currentPage = 1;
        clearTimeout(searchTimer);
        searchTimer = setTimeout(fetchMedia, 400);
    });

    // ── Upload toggle ─────────────────────────────────────────────────────────
    document.getElementById('mlBtnAddMedia').addEventListener('click', () => {
        mlUploadZone.classList.contains('visible') ? hideUpload() : showUpload();
    });

    function showUpload() {
        mlUploadZone.classList.add('visible');
        mlUploadActions.classList.add('visible');
    }

    function hideUpload() {
        mlUploadZone.classList.remove('visible', 'dragover');
        mlUploadActions.classList.remove('visible');
        pendingFiles = []; // ADD THIS
        const uploadProgress = document.getElementById('mlUploadProgress');
        if (uploadProgress) uploadProgress.textContent = '';
        const btnDoUpload = document.getElementById('mlBtnDoUpload');
        if (btnDoUpload) {
            btnDoUpload.disabled = true;
            btnDoUpload.innerHTML = '<i class="bi bi-upload"></i> Upload'; // ← YEH ADD KARO
        }
    }

    document.getElementById('mlBtnCloseUpload').addEventListener('click', hideUpload);

    document.getElementById('mlBtnDoUpload').addEventListener('click', () => {
        if (pendingFiles.length) {
            doUpload();
        }
    });

    mlUploadZone.addEventListener('click', () => {
        document.getElementById('mlFileInput').click();
    });

    mlUploadZone.addEventListener('dragover', e => {
        e.preventDefault();
        mlUploadZone.classList.add('dragover');
    });

    mlUploadZone.addEventListener('dragleave', () => {
        mlUploadZone.classList.remove('dragover');
    });

    mlUploadZone.addEventListener('drop', e => {
        e.preventDefault();
        mlUploadZone.classList.remove('dragover');
        stageFiles(Array.from(e.dataTransfer.files)); // handleFiles → stageFiles
    });

    document.getElementById('mlFileInput').addEventListener('change', function () {
        stageFiles(Array.from(this.files)); // handleFiles → stageFiles
        this.value = '';
    });

    // Stage Files
    function stageFiles(files) {
        if (!files.length) return;
        pendingFiles = files;

        // Progress text update karo
        const uploadProgress = document.getElementById('mlUploadProgress');
        if (uploadProgress) {
            uploadProgress.textContent = files.length + ' file' + (files.length > 1 ? 's' : '') + ' selected';
        }

        // Upload button enable karo
        const btnDoUpload = document.getElementById('mlBtnDoUpload');
        if (btnDoUpload) btnDoUpload.disabled = false;
    }

    // ── Upload handler ────────────────────────────────────────────────────────
    async function doUpload() {
        if (!pendingFiles.length) return;

        const files = pendingFiles;
        pendingFiles = [];

        const btnDoUpload = document.getElementById('mlBtnDoUpload');
        if (btnDoUpload) {
            btnDoUpload.disabled = true;
            btnDoUpload.innerHTML = '<i class="bi bi-cloud-upload"></i> Uploading...';
        }

        const formData = new FormData();
        files.forEach(f => formData.append('files[]', f));

        try {
            const res = await fetch(ROUTES.upload, {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept':       'application/json',
                },
                body: formData,
            });

            const json = await res.json();

            // AB hideUpload — upload complete hone ke baad
            hideUpload();

            if (res.ok && json.success) {
                showToast(json.message, 'success');
                currentPage = 1;
                fetchMedia();
            } else if (res.status === 422 && json.errors) {
                const messages = Object.values(json.errors).flat().join(' ');
                showToast(messages, 'error');
                fetchMedia();
            } else {
                showToast('Upload failed. Please try again.', 'error');
                fetchMedia();
            }

        } catch (err) {
            console.error('Upload error:', err);
            hideUpload(); // error pe bhi hide
            showToast('Network error. Please try again.', 'error');
            fetchMedia();
        }
    }

    // ── Toast ─────────────────────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        // Agar toastr available hai (dealer panel mein hona chahiye)
        if (window.toastr) {
            toastr[type](message);
            return;
        }

        // Fallback — simple toast
        const toast = document.createElement('div');
        toast.className = `ml-toast ml-toast--${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    fetchMedia();

})();