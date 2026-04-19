(function () {
    'use strict';

    /* ══════════════════════════════════
       HELPERS
    ══════════════════════════════════ */
    function qs(sel)  { return document.querySelector(sel); }
    function qsa(sel) { return document.querySelectorAll(sel); }

    function getCsrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }

    function fetchJson(url, options) {
        return fetch(url, Object.assign({
            headers: {
                'X-CSRF-TOKEN':     getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
        }, options || {}));
    }

    /* ══════════════════════════════════
       PAGE DATA
    ══════════════════════════════════ */
    var page           = qs('.ph-page');
    var urlUpload      = page ? page.dataset.urlUpload      : '';
    var urlReorder     = page ? page.dataset.urlReorder     : '';
    var urlBulkDelete  = page ? page.dataset.urlBulkDelete  : '';

    /* ══════════════════════════════════
       STATE
    ══════════════════════════════════ */
    var selectedIndex = 0;
    var pendingFiles  = [];

    /* ══════════════════════════════════
       ELEMENT REFS
    ══════════════════════════════════ */
    var grid              = qs('#photoGrid');
    var previewImg        = qs('#previewImg');
    var previewEmpty      = qs('#previewEmpty');
    var previewImgWrap    = qs('#previewImgWrap');
    var dropZone          = qs('#dropZone');
    var fileInput         = qs('#fileInput');
    var uploadActions     = qs('#uploadActions');
    var uploadProgress    = qs('#uploadProgress');
    var btnToggleUpload   = qs('#btnToggleUpload');
    var btnCancelUpload   = qs('#btnCancelUpload');
    var btnDoUpload       = qs('#btnDoUpload');
    var btnBulkEdit       = qs('#btnBulkEdit');
    var bulkMenu          = qs('#bulkMenu');
    var btnBulkPublishAll = qs('#btnBulkPublishAll');
    var btnBulkDraftAll   = qs('#btnBulkDraftAll');
    var btnBulkDelete     = qs('#btnBulkDelete');
    var btnPublishAll     = qs('#btnPublishAll');
    var btnPrev           = qs('#btnPrevPhoto');
    var btnNext           = qs('#btnNextPhoto');
    var liveCountEl       = qs('.ph-count-live');
    var draftCountEl      = qs('.ph-count-draft');

    /* ══════════════════════════════════
       TOASTR
    ══════════════════════════════════ */
    function toast(type, msg) {
        if (window.toastr) {
            window.toastr[type](msg);
        } else {
            alert(msg);
        }
    }

    /* ══════════════════════════════════
       CARD HELPERS
    ══════════════════════════════════ */
    function getCards() {
        return Array.from(grid.querySelectorAll('.ph-card'));
    }

    function updateCounts() {
        var cards = getCards();
        var live  = cards.filter(function (c) { return c.dataset.status === 'live'; }).length;
        var draft = cards.filter(function (c) { return c.dataset.status === 'draft'; }).length;

        if (liveCountEl)  liveCountEl.textContent  = live + ' live';
        if (draftCountEl) draftCountEl.textContent = draft + ' draft';

        // Publish all — enable only if draft photos exist
        if (btnBulkPublishAll) {
            btnBulkPublishAll.classList.toggle('disabled', draft === 0);
            btnBulkPublishAll.disabled = draft === 0;
        }

        // Set all to Draft — enable only if live photos exist
        if (btnBulkDraftAll) {
            btnBulkDraftAll.classList.toggle('disabled', live === 0);
            btnBulkDraftAll.disabled = live === 0;
        }
    }

    /* ══════════════════════════════════
       PUBLISH ALL (shared logic)
    ══════════════════════════════════ */
    function publishAllDraftPhotos() {
        var cards      = getCards();
        var draftCards = cards.filter(function (c) { return c.dataset.status === 'draft'; });

        if (!draftCards.length) {
            toast('info', 'All photos are already live.');
            return;
        }

        var promises = draftCards.map(function (card) {
            return fetchJson(card.dataset.urlStatus, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify({ status: 'live' }),
            });
        });

        Promise.all(promises)
            .then(function () {
                draftCards.forEach(function (card) { setCardStatus(card, 'live'); });
                updateCounts();
                toast('success', draftCards.length + ' photo(s) published.');
            })
            .catch(function () {
                toast('error', 'Some photos could not be published.');
            });
    }

    /* ══════════════════════════════════
       SET ALL TO DRAFT (shared logic)
    ══════════════════════════════════ */
    function draftAllLivePhotos() {
        var cards     = getCards();
        var liveCards = cards.filter(function (c) { return c.dataset.status === 'live'; });

        if (!liveCards.length) {
            toast('info', 'All photos are already in draft.');
            return;
        }

        var promises = liveCards.map(function (card) {
            return fetchJson(card.dataset.urlStatus, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify({ status: 'draft' }),
            });
        });

        Promise.all(promises)
            .then(function () {
                liveCards.forEach(function (card) { setCardStatus(card, 'draft'); });
                updateCounts();
                toast('success', liveCards.length + ' photo(s) set to Draft.');
            })
            .catch(function () {
                toast('error', 'Some photos could not be updated.');
            });
    }

    /* ══════════════════════════════════
       SELECT CARD
    ══════════════════════════════════ */
    function selectCard(index) {
        var cards = getCards();
        if (!cards.length) {
            if (previewImg)   previewImg.style.display   = 'none';
            if (previewEmpty) previewEmpty.style.display = 'flex';
            return;
        }

        if (index < 0) index = 0;
        if (index >= cards.length) index = cards.length - 1;
        selectedIndex = index;

        cards.forEach(function (c) { c.classList.remove('selected'); });
        var card = cards[index];
        if (!card) return;
        card.classList.add('selected');

        if (previewImg && card.dataset.src) {
            previewImg.src           = card.dataset.src;
            previewImg.style.display = 'block';
            if (previewEmpty) previewEmpty.style.display = 'none';
        }

        card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    /* ══════════════════════════════════
       CLICK ON CARD IMAGE
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var card = e.target.closest('.ph-card');
        if (!card) return;
        if (e.target.closest('.ph-card-footer')) return;
        var cards = getCards();
        selectCard(cards.indexOf(card));
    });

    /* ══════════════════════════════════
       PREV / NEXT
    ══════════════════════════════════ */
    if (btnPrev) btnPrev.addEventListener('click', function () { selectCard(selectedIndex - 1); });
    if (btnNext) btnNext.addEventListener('click', function () { selectCard(selectedIndex + 1); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft')  selectCard(selectedIndex - 1);
        if (e.key === 'ArrowRight') selectCard(selectedIndex + 1);
    });

    /* ══════════════════════════════════
       UPLOAD — toggle drop zone
    ══════════════════════════════════ */
    function openUpload() {
        if (dropZone)      dropZone.classList.add('active');
        if (uploadActions) uploadActions.classList.add('active');
        pendingFiles = [];
        if (uploadProgress) uploadProgress.textContent = '';
        if (btnDoUpload)    btnDoUpload.disabled = true;
    }

    function closeUpload() {
        if (dropZone)      dropZone.classList.remove('active');
        if (uploadActions) uploadActions.classList.remove('active');
        pendingFiles = [];
        if (uploadProgress) uploadProgress.textContent = '';
        if (fileInput)      fileInput.value = '';
    }

    if (btnToggleUpload) btnToggleUpload.addEventListener('click', openUpload);
    if (btnCancelUpload) btnCancelUpload.addEventListener('click', closeUpload);

    if (dropZone) {
        dropZone.addEventListener('click', function () {
            if (fileInput) fileInput.click();
        });
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropZone.style.background = '#fff0f0';
        });
        dropZone.addEventListener('dragleave', function () {
            dropZone.style.background = '';
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.style.background = '';
            var files = Array.from(e.dataTransfer.files).filter(function (f) {
                return f.type.startsWith('image/');
            });
            if (files.length) stageFiles(files);
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var files = Array.from(fileInput.files).filter(function (f) {
                return f.type.startsWith('image/');
            });
            if (files.length) stageFiles(files);
        });
    }

    function stageFiles(files) {
        pendingFiles = files;
        if (uploadProgress) {
            uploadProgress.textContent = files.length + ' file' + (files.length > 1 ? 's' : '') + ' selected';
        }
        if (btnDoUpload) btnDoUpload.disabled = false;
    }

    /* ══════════════════════════════════
       DO UPLOAD
    ══════════════════════════════════ */
    if (btnDoUpload) {
        btnDoUpload.addEventListener('click', function () {
            if (!pendingFiles.length || !urlUpload) return;

            var formData = new FormData();
            pendingFiles.forEach(function (f) { formData.append('photos[]', f); });

            btnDoUpload.disabled    = true;
            btnDoUpload.textContent = 'Uploading...';
            if (uploadProgress) uploadProgress.textContent = '';

            fetchJson(urlUpload, { method: 'POST', body: formData })
            .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toast('success', result.data.message);
                    result.data.photos.forEach(function (p) { appendCard(p); });
                    var emptyEl = qs('#photoEmpty');
                    if (emptyEl) emptyEl.style.display = 'none';
                    updateCounts();
                    closeUpload();
                    selectCard(getCards().length - 1);
                } else {
                    toast('error', result.data.message || 'Upload failed.');
                }
                btnDoUpload.disabled  = false;
                btnDoUpload.innerHTML = '<i class="bi bi-cloud-upload"></i> Upload';
            })
            .catch(function () {
                toast('error', 'Network error. Please try again.');
                btnDoUpload.disabled  = false;
                btnDoUpload.innerHTML = '<i class="bi bi-cloud-upload"></i> Upload';
            });
        });
    }

    /* ══════════════════════════════════
       APPEND CARD (after upload)
    ══════════════════════════════════ */
    function appendCard(photo) {
        var card = document.createElement('div');
        card.className          = 'ph-card';
        card.dataset.id         = photo.id;
        card.dataset.src        = photo.url;
        card.dataset.status     = photo.status;
        card.dataset.urlStatus  = page.dataset.urlPhotoStatusBase.replace('__ID__', photo.id);
        card.dataset.urlDestroy = page.dataset.urlPhotoDestroyBase.replace('__ID__', photo.id);
        card.dataset.urlPrimary = page.dataset.urlPhotoPrimaryBase.replace('__ID__', photo.id);
        card.draggable          = true;

        card.innerHTML =
            '<img class="ph-card-img" src="' + photo.url + '" alt="Photo" loading="lazy">' +
            '<div class="ph-card-footer">' +
                '<button type="button" class="ph-status-btn draft" data-card-id="' + photo.id + '">' +
                    '<i class="bi bi-clock ph-status-icon"></i> Draft <i class="bi bi-chevron-down ph-status-chevron"></i>' +
                '</button>' +
                '<div class="ph-card-menu" id="cardMenu' + photo.id + '">' +
                    '<button class="ph-card-menu-item" data-action="live" data-id="' + photo.id + '">' +
                        '<i class="bi bi-check-circle green"></i> Set to Live' +
                    '</button>' +
                    '<button class="ph-card-menu-item" data-action="primary" data-id="' + photo.id + '">' +
                        '<i class="bi bi-star-fill" style="color:#f39c12;"></i> Set as Primary' +
                    '</button>' +
                    '<button class="ph-card-menu-item" data-action="delete" data-id="' + photo.id + '">' +
                        '<i class="bi bi-trash3 red"></i> Delete Photo' +
                    '</button>' +
                '</div>' +
            '</div>';

        grid.appendChild(card);
    }

    /* ══════════════════════════════════
       BULK EDIT DROPDOWN
    ══════════════════════════════════ */
    if (btnBulkEdit) {
        btnBulkEdit.addEventListener('click', function (e) {
            e.stopPropagation();
            bulkMenu.classList.toggle('open');
        });
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.ph-bulk-wrap') && bulkMenu) {
            bulkMenu.classList.remove('open');
        }
        if (!e.target.closest('.ph-card-footer')) {
            qsa('.ph-card-menu.open').forEach(function (m) { m.classList.remove('open'); });
        }
        // Close bulk menu on download link click
        var dlLink = e.target.closest('.ph-bulk-menu a');
        if (dlLink && bulkMenu) {
            bulkMenu.classList.remove('open');
        }
    });

    /* ══════════════════════════════════
       BULK PUBLISH ALL
    ══════════════════════════════════ */
    if (btnBulkPublishAll) {
        btnBulkPublishAll.addEventListener('click', function () {
            if (btnBulkPublishAll.disabled) return;
            if (bulkMenu) bulkMenu.classList.remove('open');
            publishAllDraftPhotos();
        });
    }

    /* ══════════════════════════════════
       BULK SET ALL TO DRAFT
    ══════════════════════════════════ */
    if (btnBulkDraftAll) {
        btnBulkDraftAll.addEventListener('click', function () {
            if (btnBulkDraftAll.disabled) return;
            if (bulkMenu) bulkMenu.classList.remove('open');
            draftAllLivePhotos();
        });
    }

    /* ══════════════════════════════════
       BULK DELETE
    ══════════════════════════════════ */
    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function () {
            if (bulkMenu) bulkMenu.classList.remove('open');
            if (!confirm('Are you sure you want to delete ALL photos? This cannot be undone.')) return;

            fetchJson(urlBulkDelete, { method: 'DELETE' })
            .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toast('success', result.data.message);
                    grid.innerHTML = '';
                    updateCounts();
                    selectCard(0);
                } else {
                    toast('error', result.data.message || 'Something went wrong.');
                }
            })
            .catch(function () { toast('error', 'Network error.'); });
        });
    }

    /* ══════════════════════════════════
       PUBLISH ALL (toolbar button)
    ══════════════════════════════════ */
    if (btnPublishAll) {
        btnPublishAll.addEventListener('click', publishAllDraftPhotos);
    }

    /* ══════════════════════════════════
       PER-CARD STATUS DROPDOWN
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var statusBtn = e.target.closest('.ph-status-btn');
        if (!statusBtn) return;
        e.stopPropagation();

        qsa('.ph-card-menu.open').forEach(function (m) { m.classList.remove('open'); });

        var cardId = statusBtn.dataset.cardId;
        var menu   = document.getElementById('cardMenu' + cardId);
        if (menu) menu.classList.toggle('open');
    });

    /* ══════════════════════════════════
       CARD MENU ACTIONS
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var item = e.target.closest('.ph-card-menu-item');
        if (!item) return;
        e.stopPropagation();

        var action = item.dataset.action;
        var id     = item.dataset.id;
        var card   = grid.querySelector('[data-id="' + id + '"]');
        if (!card) return;

        var menu = document.getElementById('cardMenu' + id);
        if (menu) menu.classList.remove('open');

        if (action === 'live' || action === 'draft') {
            fetchJson(card.dataset.urlStatus, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify({ status: action }),
            })
            .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    setCardStatus(card, action);
                    updateCounts();
                    toast('success', action === 'live' ? 'Photo set to Live.' : 'Photo set to Draft.');
                } else {
                    toast('error', result.data.message || 'Something went wrong.');
                }
            })
            .catch(function () { toast('error', 'Network error.'); });

        } else if (action === 'primary') {
            fetchJson(card.dataset.urlPrimary, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify({}),
            })
            .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    // Remove "Set as Primary" from all cards
                    getCards().forEach(function (c) {
                        var m = c.querySelector('.ph-card-menu');
                        if (!m) return;
                        var primaryBtn = m.querySelector('[data-action="primary"]');
                        if (primaryBtn) primaryBtn.remove();
                    });
                    // Add "Set as Primary" to all except clicked card
                    getCards().forEach(function (c) {
                        if (c.dataset.id === card.dataset.id) return;
                        var m = c.querySelector('.ph-card-menu');
                        if (!m) return;
                        var firstItem = m.querySelector('.ph-card-menu-item');
                        var newBtn = document.createElement('button');
                        newBtn.className = 'ph-card-menu-item';
                        newBtn.dataset.action = 'primary';
                        newBtn.dataset.id = c.dataset.id;
                        newBtn.innerHTML = '<i class="bi bi-star-fill" style="color:#f39c12;"></i> Set as Primary';
                        m.insertBefore(newBtn, firstItem);
                    });
                    // set primary badge accordingly
                    card.classList.add('primary');
                    getCards().forEach(function (c) {
                        if (c.dataset.id !== card.dataset.id) {
                            c.classList.remove('primary');
                        }
                    });

                    toast('success', result.data.message || 'Primary photo updated.');
                } else {
                    toast('error', result.data.message || 'Something went wrong.');
                }
            })
            .catch(function () { toast('error', 'Network error.'); });

        } else if (action === 'delete') {
            if (!confirm('Are you sure you want to delete this photo?')) return;

            fetchJson(card.dataset.urlDestroy, { method: 'DELETE' })
            .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toast('success', result.data.message || 'Photo deleted.');
                    var cards    = getCards();
                    var idx      = cards.indexOf(card);
                    card.remove();
                    updateCounts();
                    var newCards = getCards();
                    if (card.classList.contains('primary') && newCards.length) {
                        newCards[0].classList.add('primary');
                    }
                    selectCard(newCards.length ? Math.min(idx, newCards.length - 1) : 0);
                } else {
                    toast('error', result.data.message || 'Something went wrong.');
                }
            })
            .catch(function () { toast('error', 'Network error.'); });
        }
    });

    /* ══════════════════════════════════
       SET CARD STATUS (UI update)
    ══════════════════════════════════ */
    function setCardStatus(card, status) {
        card.dataset.status = status;
        var btn  = card.querySelector('.ph-status-btn');
        var menu = card.querySelector('.ph-card-menu');
        if (!btn) return;

        if (status === 'live') {
            btn.className = 'ph-status-btn';
            btn.innerHTML = '<i class="bi bi-check-circle-fill ph-status-icon"></i> Live <i class="bi bi-chevron-down ph-status-chevron"></i>';
            if (menu) menu.innerHTML =
                '<button class="ph-card-menu-item" data-action="draft" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-clock" style="color:#888;"></i> Set to Draft' +
                '</button>' +
                '<button class="ph-card-menu-item" data-action="delete" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-trash3 red"></i> Delete Photo' +
                '</button>';
            if (menu) menu.innerHTML =
                '<button class="ph-card-menu-item" data-action="primary" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-star-fill" style="color:#f39c12;"></i> Set as Primary' +
                '</button>' +
                '<button class="ph-card-menu-item" data-action="draft" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-clock" style="color:#888;"></i> Set to Draft' +
                '</button>' +
                '<button class="ph-card-menu-item" data-action="delete" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-trash3 red"></i> Delete Photo' +
                '</button>';
        } else {
            btn.className = 'ph-status-btn draft';
            btn.innerHTML = '<i class="bi bi-clock ph-status-icon"></i> Draft <i class="bi bi-chevron-down ph-status-chevron"></i>';
            if (menu) menu.innerHTML =
                '<button class="ph-card-menu-item" data-action="live" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-check-circle green"></i> Set to Live' +
                '</button>' +
                '<button class="ph-card-menu-item" data-action="delete" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-trash3 red"></i> Delete Photo' +
                '</button>';
            if (menu) menu.innerHTML =
                '<button class="ph-card-menu-item" data-action="primary" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-star-fill" style="color:#f39c12;"></i> Set as Primary' +
                '</button>' +
                '<button class="ph-card-menu-item" data-action="live" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-check-circle green"></i> Set to Live' +
                '</button>' +
                '<button class="ph-card-menu-item" data-action="delete" data-id="' + card.dataset.id + '">' +
                    '<i class="bi bi-trash3 red"></i> Delete Photo' +
                '</button>';
        }
    }

    /* ══════════════════════════════════
       DRAG TO REORDER
    ══════════════════════════════════ */
    var dragSrcCard = null;

    function enableDragReorder() {
        grid.addEventListener('dragstart', function (e) {
            var card = e.target.closest('.ph-card');
            if (!card) return;
            dragSrcCard = card;
            card.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
        });

        grid.addEventListener('dragend', function (e) {
            var card = e.target.closest('.ph-card');
            if (card) card.style.opacity = '';
            dragSrcCard = null;
        });

        grid.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            var card = e.target.closest('.ph-card');
            if (!card || card === dragSrcCard) return;
            var rect     = card.getBoundingClientRect();
            var midpoint = rect.left + rect.width / 2;
            if (e.clientX < midpoint) {
                grid.insertBefore(dragSrcCard, card);
            } else {
                grid.insertBefore(dragSrcCard, card.nextSibling);
            }
        });

        grid.addEventListener('drop', function (e) {
            e.preventDefault();
            if (!dragSrcCard) return;
            dragSrcCard.style.opacity = '';

            var ids = getCards().map(function (c) { return parseInt(c.dataset.id); });
            fetchJson(urlReorder, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify({ ids: ids }),
            })
            .then(function (res) { return res.json(); })
            .then(function (result) {
                if (!result.success) toast('error', 'Reorder failed.');
            })
            .catch(function () { toast('error', 'Network error.'); });
        });
    }

    enableDragReorder();

    /* ══════════════════════════════════
       INIT
    ══════════════════════════════════ */
    getCards().forEach(function (c) { c.draggable = true; });
    selectCard(0);
    updateCounts();

})();