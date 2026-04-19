(function () {
    'use strict';

    // View Listing
    const viewListingBtn = document.getElementById('view-listing-btn');

    viewListingBtn.addEventListener('click', function() {
        const url = this.getAttribute('data-listing-url');
        window.open(url, '_blank');
    });

    /* ══════════════════════════════════
       HELPERS
    ══════════════════════════════════ */
    function qs(sel)  { return document.querySelector(sel); }
    function qsa(sel) { return document.querySelectorAll(sel); }
    function escHtml(str) { var d = document.createElement('span'); d.textContent = str; return d.innerHTML; }
    function getCsrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }

    /* ══════════════════════════════════
       PAGE DATA ATTRIBUTES
    ══════════════════════════════════ */
    var vdPage     = qs('.vd-page');
    var urlModels  = vdPage ? vdPage.dataset.urlModels  : '';
    var urlDestroy = vdPage ? vdPage.dataset.urlDestroy : '';

    /* ══════════════════════════════════
       QUILL INSTANCES
    ══════════════════════════════════ */
    var quillToolbar = [
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ indent: '-1' }, { indent: '+1' }],
        ['link'], ['clean'],
    ];

    function makeQuill(id, placeholder, minH) {
        var el = document.getElementById(id);
        if (!el) return null;
        var q = new Quill('#' + id, {
            theme: 'snow',
            placeholder: placeholder || '',
            modules: { toolbar: quillToolbar },
        });
        if (minH) {
            var editor = el.querySelector('.ql-editor');
            if (editor) editor.style.minHeight = minH;
        }
        el.__quill = q;
        return q;
    }

    makeQuill('rtePricing',       'Enter pricing disclaimer...', '80px');
    makeQuill('rteNotes',         'Write internal notes here...', '200px');
    makeQuill('rteDealerNotes',   'Write dealer notes here...', '120px');
    makeQuill('rteExtDescription','Write extended description here...', '120px');

    /* ══════════════════════════════════
       AJAX SAVE
    ══════════════════════════════════ */
    function syncQuillEditors(form) {
        var containers = form
            ? form.querySelectorAll('[data-quill-hidden]')
            : document.querySelectorAll('[data-quill-hidden]');

        containers.forEach(function (el) {
            var hiddenId = el.dataset.quillHidden;
            var hidden   = document.getElementById(hiddenId);
            if (!hidden || !el.__quill) return;
            var html = el.__quill.root.innerHTML;
            hidden.value = (html === '<p><br></p>') ? '' : html;
        });
    }

    function ajaxSave(form, btn) {
        var url = form ? form.dataset.ajaxUrl : null;
        if (!url) return;

        syncQuillEditors(form);

        var origHtml = btn ? btn.innerHTML : null;
        if (btn) {
            btn.disabled  = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:12px;height:12px;border-width:2px;"></span>';
        }

        var data = new FormData(form);

        form.querySelectorAll('.vd-numeric').forEach(function (inp) {
            if (inp.name && data.has(inp.name)) {
                data.set(inp.name, inp.value.replace(/,/g, ''));
            }
        });

        form.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
            if (!cb.checked && cb.name) {
                data.set(cb.name, '0');
            }
        });

        fetch(url, {
            method:  'POST',
            headers: {
                'X-CSRF-TOKEN':     getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
            body: data,
        })
        .then(function (res) {
            return res.json().then(function (d) {
                return { ok: res.ok, status: res.status, data: d };
            });
        })
        .then(function (result) {
            if (result.ok && result.data.success) {
                toastr.success(result.data.message || 'Saved successfully.', 'Success');
                if (btn) {
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Saved!';
                    setTimeout(function () {
                        btn.innerHTML = origHtml;
                        btn.disabled  = false;
                    }, 1800);
                }
            } else {
                if (btn) { btn.innerHTML = origHtml; btn.disabled = false; }
                if (result.status === 422 && result.data.errors) {
                    var msgs = Object.values(result.data.errors).flat().slice(0, 3).join(' | ');
                    toastr.error(msgs, 'Error');
                } else {
                    toastr.error(result.data.message || 'Something went wrong.', 'Error');
                }
            }
        })
        .catch(function () {
            if (btn) { btn.innerHTML = origHtml; btn.disabled = false; }
            toastr.error('Network error. Please try again.', 'Error');
        });
    }

    /* ══════════════════════════════════
       SAVE BUTTON — click handler
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.vd-btn-save');
        if (!btn) return;

        // Premium build save — handled by form submit
        if (btn.id === 'btnSaveOption') return;

        var formId = btn.dataset.form;
        if (formId) {
            var form = document.getElementById(formId);
            if (form) {
                e.preventDefault();
                ajaxSave(form, btn);
                return;
            }
        }

        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Saved!';
        btn.disabled  = true;
        setTimeout(function () { btn.innerHTML = orig; btn.disabled = false; }, 1800);
    });

    /* ══════════════════════════════════
       NOTES SUBVIEW — standalone save
    ══════════════════════════════════ */
    var notesAjaxUrl = vdPage ? vdPage.dataset.urlNotes : '';

    function saveNotesField(hiddenDealerNotesId, hiddenAiDescId, btn) {
        if (!notesAjaxUrl) return;

        var dNEl  = document.getElementById('rteDealerNotes');
        var aiEl  = document.getElementById('rteExtDescription');
        var dNHid = document.getElementById(hiddenDealerNotesId);
        var aiHid = document.getElementById(hiddenAiDescId);

        if (dNEl && dNEl.__quill && dNHid) {
            var h = dNEl.__quill.root.innerHTML;
            dNHid.value = h === '<p><br></p>' ? '' : h;
        }
        if (aiEl && aiEl.__quill && aiHid) {
            var h2 = aiEl.__quill.root.innerHTML;
            aiHid.value = h2 === '<p><br></p>' ? '' : h2;
        }

        var tmpForm = document.createElement('form');
        tmpForm.dataset.ajaxUrl = notesAjaxUrl;

        function appendHidden(name, val) {
            var inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = name; inp.value = val || '';
            tmpForm.appendChild(inp);
        }

        appendHidden('_method', 'PATCH');
        appendHidden('_token', getCsrf());
        if (dNHid) appendHidden('dealer_notes',   dNHid.value);
        if (aiHid) appendHidden('ai_description', aiHid.value);

        ajaxSave(tmpForm, btn);
    }

    var btnSaveDealerNotes = document.getElementById('btnSaveDealerNotes');
    var btnSaveAiDesc      = document.getElementById('btnSaveAiDescription');

    if (btnSaveDealerNotes) {
        btnSaveDealerNotes.addEventListener('click', function () {
            saveNotesField('hiddenDealerNotes', 'hiddenAiDescription', btnSaveDealerNotes);
        });
    }
    if (btnSaveAiDesc) {
        btnSaveAiDesc.addEventListener('click', function () {
            saveNotesField('hiddenDealerNotes', 'hiddenAiDescription', btnSaveAiDesc);
        });
    }

    /* ══════════════════════════════════
       RIGHT SIDEBAR — auto-save on change
    ══════════════════════════════════ */
    var formStatus = document.getElementById('formStatus');
    if (formStatus) {
        var statusSaveTimer = null;
        formStatus.addEventListener('change', function () {
            clearTimeout(statusSaveTimer);
            statusSaveTimer = setTimeout(function () {
                ajaxSave(formStatus, null);
            }, 400);
        });
    }

    /* ══════════════════════════════════
       TOGGLE LABELS (Yes/No)
    ══════════════════════════════════ */
    document.addEventListener('change', function (e) {
        if (!e.target.closest('.vd-toggle')) return;
        var row   = e.target.closest('.vd-toggle-row');
        var label = row && row.querySelector('.vd-toggle-no');
        if (label) label.textContent = e.target.checked ? 'Yes' : 'No';
    });

    /* ══════════════════════════════════
       REMOVE LISTING
    ══════════════════════════════════ */
    var btnRemoveListing = document.getElementById('btnRemoveListing');
    if (btnRemoveListing && urlDestroy) {
        btnRemoveListing.addEventListener('click', function () {
            if (!confirm('Are you sure you want to remove this listing? This action cannot be undone.')) return;

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = urlDestroy;
            form.innerHTML =
                '<input type="hidden" name="_token" value="' + escHtml(getCsrf()) + '">' +
                '<input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        });
    }

    /* ══════════════════════════════════
       VIEW ANALYTICS LINK
    ══════════════════════════════════ */
    var btnViewAnalytics = document.getElementById('btnViewAnalytics');
    if (btnViewAnalytics) {
        btnViewAnalytics.addEventListener('click', function (e) {
            e.preventDefault();
            showNav('analytics');
        });
    }

    /* ══════════════════════════════════
       EDIT DETAILS — MAKE → MODEL CASCADE
    ══════════════════════════════════ */
    var detMakeId  = document.getElementById('detMakeId');
    var detModelId = document.getElementById('detModelId');

    if (detMakeId && detModelId && urlModels) {
        detMakeId.addEventListener('change', function () {
            var makeId = this.value;
            if (!makeId) {
                detModelId.innerHTML = '<option value="">Select Make first</option>';
                detModelId.disabled  = true;
                return;
            }
            detModelId.innerHTML = '<option value="">Loading...</option>';
            detModelId.disabled  = true;

            fetch(urlModels + '?make_id=' + makeId, {
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
            })
            .then(function (res) { return res.json(); })
            .then(function (models) {
                detModelId.innerHTML = '<option value="">Select Model</option>';
                models.forEach(function (m) {
                    var opt         = document.createElement('option');
                    opt.value       = m.id;
                    opt.textContent = m.name;
                    detModelId.appendChild(opt);
                });
                detModelId.disabled = false;
            })
            .catch(function () {
                detModelId.innerHTML = '<option value="">Could not load models</option>';
                detModelId.disabled  = false;
            });
        });
    }

    /* ══════════════════════════════════
       OVERVIEW TABS
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var tab = e.target.closest('.vd-tab');
        if (!tab) return;
        var key = tab.dataset.tab;
        qsa('.vd-tab').forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        qsa('.vd-tab-pane').forEach(function (p) { p.classList.remove('active'); });
        var pane = document.getElementById('tab-' + key);
        if (pane) pane.classList.add('active');
    });

    /* ══════════════════════════════════
       LEFT NAV — switches subviews
    ══════════════════════════════════ */
    var vdBody = qs('.vd-body');

    var navMap = {
        'overview':        null,
        'photos':          null,
        'videos':          'svVideos',
        'notes':           'svNotes',
        'factory-options': 'svFactoryOptions',
        'premium-build':   'svPremiumBuild',
        'incentives':      'svIncentives',
        'printables':      'svPrintables',
        'analytics':       'svAnalytics',
        'syndication':     'svSyndication',
    };

    function showNav(key) {
        qsa('.vd-nav-item, .sv-nav-item').forEach(function (n) { n.classList.remove('active'); });
        qsa('[data-nav="' + key + '"], [data-sv-nav="' + key + '"]').forEach(function (n) {
            n.classList.add('active');
        });

        var panelId = navMap[key];

        if (panelId === null) {
            if (vdBody) vdBody.style.display = '';
            qsa('.vd-subview-panel').forEach(function (p) { p.classList.remove('active'); });
        } else {
            if (vdBody) vdBody.style.display = 'none';
            qsa('.vd-subview-panel').forEach(function (p) { p.classList.remove('active'); });
            var panel = document.getElementById(panelId);
            if (panel) panel.classList.add('active');
            if (key === 'analytics') initAnalyticsChart();
        }
    }

    document.addEventListener('click', function (e) {
        var item = e.target.closest('.vd-nav-item, .sv-nav-item');
        if (!item) return;
        var key = item.dataset.nav || item.dataset.svNav;
        if (!key) return;
        if (key === 'photos') return;
        e.preventDefault();
        showNav(key);
    });

    /* ══════════════════════════════════
       TAG INPUT
    ══════════════════════════════════ */
    var tagInput = document.getElementById('tagInput');
    var tagWrap  = document.getElementById('tagInputWrap');

    function addTagChip(val) {
        val = val.trim();
        if (!val) return;

        var existing = Array.from(tagWrap.querySelectorAll('input[name="tags[]"]'))
            .map(function (i) { return i.value.toLowerCase(); });
        if (existing.indexOf(val.toLowerCase()) !== -1) {
            tagInput.value = '';
            return;
        }

        var chip = document.createElement('span');
        chip.className = 'vd-tag-chip';
        chip.innerHTML =
            escHtml(val) +
            '<button type="button" title="Remove">&times;</button>' +
            '<input type="hidden" name="tags[]" value="' + escHtml(val) + '">';
        tagWrap.insertBefore(chip, tagInput);
        tagInput.value = '';
    }

    if (tagInput && tagWrap) {
        tagInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTagChip(tagInput.value);
            }
            if (e.key === 'Backspace' && !tagInput.value) {
                var chips = tagWrap.querySelectorAll('.vd-tag-chip');
                if (chips.length) chips[chips.length - 1].remove();
            }
        });
        tagInput.addEventListener('paste', function (e) {
            e.preventDefault();
            var text = e.clipboardData.getData('text');
            text.split(',').forEach(function (t) { addTagChip(t); });
        });
        tagWrap.addEventListener('click', function () { tagInput.focus(); });
    }

    document.addEventListener('click', function (e) {
        var rmBtn = e.target.closest('.vd-tag-chip button');
        if (!rmBtn) return;
        rmBtn.closest('.vd-tag-chip').remove();
    });

    /* ══════════════════════════════════
       ACCORDION
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('.vd-accordion-trigger');
        if (!trigger) return;
        var key    = trigger.dataset.accordion;
        var body   = document.getElementById('acc-' + key);
        var isOpen = trigger.classList.contains('open');
        trigger.classList.toggle('open', !isOpen);
        if (body) body.classList.toggle('open', !isOpen);
    });

    /* ══════════════════════════════════
       VIDEO PREVIEW
    ══════════════════════════════════ */
    var videoUrlInput  = document.getElementById('videoUrl');
    var videoSourceSel = document.getElementById('videoSource');
    var previewBody    = document.getElementById('videoPreviewBody');
    var previewEmbed   = document.getElementById('videoPreviewEmbed');
    var videoIframe    = document.getElementById('videoIframe');

    function detectVideoSource(url) {
        if (!url) return '';
        if (/youtu\.be|youtube\.com/i.test(url))        return 'youtube';
        if (/glo3d\.com/i.test(url))                    return 'glo3d';
        if (/lesautomotive\.com|lesa\.auto/i.test(url)) return 'lesautomotive';
        if (/dealerimagepro\.com/i.test(url))           return 'dealerimagepro';
        if (/dealervideopro\.com/i.test(url))           return 'dealervideopro';
        if (/spincar\.com/i.test(url))                  return 'spincar';
        if (/unityworks\.com/i.test(url))               return 'unityworks';
        if (/flickfusion\.com/i.test(url))              return 'flickfusion';
        return '';
    }

    function buildEmbedUrl(url, source) {
        if (!url || !source) return '';
        if (source === 'youtube') {
            var m = url.match(/[?&]v=([^&]+)/) ||
                    url.match(/youtu\.be\/([^?&]+)/) ||
                    url.match(/embed\/([^?&\s]+)/) ||
                    url.match(/shorts\/([^?&\s]+)/);
            var id = m ? m[1] : null;
            return id ? 'https://www.youtube.com/embed/' + id + '?rel=0&modestbranding=1' : url;
        }
        return url;
    }

    function updateVideoPreview() {
        var url    = videoUrlInput  ? videoUrlInput.value.trim() : '';
        var source = videoSourceSel ? videoSourceSel.value       : '';

        var saveBtn = document.getElementById('btnVideoSave');
        if (saveBtn) {
            saveBtn.disabled      = !url;
            saveBtn.style.opacity = url ? '1' : '0.45';
            saveBtn.style.cursor  = url ? 'pointer' : 'not-allowed';
        }

        if (url) {
            var detected = detectVideoSource(url);
            if (detected && videoSourceSel) { videoSourceSel.value = detected; source = detected; }
        }

        if (url && source) {
            var embedUrl = buildEmbedUrl(url, source);
            if (embedUrl && videoIframe) {
                videoIframe.src = embedUrl;
                if (previewBody)  previewBody.style.display  = 'none';
                if (previewEmbed) previewEmbed.style.display = 'block';
                return;
            }
        }
        if (videoIframe)  videoIframe.src            = '';
        if (previewEmbed) previewEmbed.style.display = 'none';
        if (previewBody)  previewBody.style.display  = 'flex';
    }

    if (videoUrlInput)  videoUrlInput.addEventListener('input',  updateVideoPreview);
    if (videoUrlInput)  videoUrlInput.addEventListener('paste',  function () { setTimeout(updateVideoPreview, 50); });
    if (videoSourceSel) videoSourceSel.addEventListener('change', updateVideoPreview);

    var btnVideoRemove = document.getElementById('btnVideoRemove');
    if (btnVideoRemove) {
        btnVideoRemove.addEventListener('click', function () {
            if (videoUrlInput)  videoUrlInput.value  = '';
            if (videoSourceSel) videoSourceSel.value = '';
            if (videoIframe)    videoIframe.src       = '';
            if (previewEmbed)   previewEmbed.style.display = 'none';
            if (previewBody)    previewBody.style.display  = 'flex';
            var sb = document.getElementById('btnVideoSave');
            if (sb) { sb.disabled = true; sb.style.opacity = '0.45'; sb.style.cursor = 'not-allowed'; }

            if (urlVideo) {
                fetch(urlVideo, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':     getCsrf(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept':           'application/json',
                        'Content-Type':     'application/json',
                    },
                    body: JSON.stringify({ _method: 'PATCH', url: '', source: '' }),
                })
                .then(function (res) { return res.json(); })
                .then(function (result) {
                    if (result.success) {
                        toastr.success('Video removed.', 'Success');
                    }
                })
                .catch(function () {
                    toastr.error('Network error.', 'Error');
                });
            }
        });
    }

    /* ══════════════════════════════════
       VIDEO SAVE
    ══════════════════════════════════ */
    var btnVideoSave = document.getElementById('btnVideoSave');
    var urlVideo     = vdPage ? vdPage.dataset.urlVideo : '';

    if (btnVideoSave && urlVideo) {
        btnVideoSave.addEventListener('click', function () {
            var url    = videoUrlInput  ? videoUrlInput.value.trim()  : '';
            var source = videoSourceSel ? videoSourceSel.value.trim() : '';

            var origHtml      = btnVideoSave.innerHTML;
            btnVideoSave.disabled  = true;
            btnVideoSave.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:12px;height:12px;border-width:2px;"></span>';

            fetch(urlVideo, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify({ _method: 'PATCH', url: url, source: source }),
            })
            .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toastr.success(result.data.message || 'Video saved.', 'Success');
                    updateVideoPreview();
                    btnVideoSave.innerHTML = '<i class="bi bi-check-lg"></i> Saved!';
                    setTimeout(function () {
                        btnVideoSave.innerHTML = origHtml;
                        btnVideoSave.disabled  = false;
                    }, 1800);
                } else {
                    toastr.error(result.data.message || 'Something went wrong.', 'Error');
                    btnVideoSave.innerHTML = origHtml;
                    btnVideoSave.disabled  = false;
                }
            })
            .catch(function () {
                toastr.error('Network error.', 'Error');
                btnVideoSave.innerHTML = origHtml;
                btnVideoSave.disabled  = false;
            });
        });
    }

    /* ══════════════════════════════════
       MODALS
    ══════════════════════════════════ */
    function openModal(id) {
        var m = document.getElementById(id);
        if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
    }
    function closeModal(id) {
        var m = document.getElementById(id);
        if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
    }

    var btnAddOption    = document.getElementById('btnAddOption');
    if (btnAddOption)    btnAddOption.addEventListener('click',    function () { openModal('modalAddOption'); });

    document.addEventListener('click', function (e) {
        var closeBtn = e.target.closest('[data-close-modal]');
        if (closeBtn) { closeModal(closeBtn.dataset.closeModal); return; }
        if (e.target.classList.contains('vd-modal-overlay')) {
            e.target.classList.remove('open');
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            qsa('.vd-modal-overlay.open').forEach(function (m) {
                m.classList.remove('open');
            });
            document.body.style.overflow = '';
            closeCanvas();
        }
    });

    /* ══════════════════════════════════
       FIND CONTACT CANVAS
    ══════════════════════════════════ */
    var canvasOverlay = document.getElementById('canvasOverlay');
    var findCanvas    = document.getElementById('findContactCanvas');
    var contactSearch = document.getElementById('contactSearch');

    function openCanvas() {
        if (canvasOverlay) canvasOverlay.classList.add('open');
        if (findCanvas)    findCanvas.classList.add('open');
        document.body.style.overflow = 'hidden';
        showScreen('screenSearch');
        if (contactSearch) contactSearch.value = '';
    }
    function closeCanvas() {
        if (canvasOverlay) canvasOverlay.classList.remove('open');
        if (findCanvas)    findCanvas.classList.remove('open');
        document.body.style.overflow = '';
    }
    function showScreen(id) {
        qsa('.vd-canvas-screen').forEach(function (s) { s.classList.remove('active'); });
        var scr = document.getElementById(id);
        if (scr) scr.classList.add('active');
    }

    var btnSelectCustomer = document.getElementById('btnSelectCustomer');
    var canvasClose       = document.getElementById('canvasClose');
    var btnCreateNew      = document.getElementById('btnCreateNew');
    var btnCanvasBack     = document.getElementById('btnCanvasBack');

    if (btnSelectCustomer) btnSelectCustomer.addEventListener('click', openCanvas);
    if (canvasClose)       canvasClose.addEventListener('click', closeCanvas);
    if (canvasOverlay)     canvasOverlay.addEventListener('click', closeCanvas);
    if (btnCreateNew)      btnCreateNew.addEventListener('click', function () { showScreen('screenCreateNew'); });
    if (btnCanvasBack)     btnCanvasBack.addEventListener('click', function () { showScreen('screenSearch'); });

    var btnCanvasSave = qs('.vd-btn-canvas-save');
    if (btnCanvasSave) {
        btnCanvasSave.addEventListener('click', function () {
            var screen = qs('#screenCreateNew');
            var textInputs = screen ? screen.querySelectorAll('input[type="text"]') : [];
            var first = textInputs[0] ? textInputs[0].value.trim() : '';
            var last  = textInputs[1] ? textInputs[1].value.trim() : '';
            var name  = (first + ' ' + last).trim() || 'Customer';

            var inputSoldTo = document.getElementById('inputSoldTo');
            if (inputSoldTo) inputSoldTo.value = name;
            if (btnSelectCustomer) {
                btnSelectCustomer.innerHTML =
                    '<i class="bi bi-person-check" style="font-size:14px;"></i> ' + escHtml(name);
            }
            closeCanvas();
        });
    }

    /* ══════════════════════════════════
       ANALYTICS CHART
    ══════════════════════════════════ */
    var analyticsChartInstance = null;

    function initAnalyticsChart() {
        var ctx = document.getElementById('analyticsChart');
        if (!ctx || analyticsChartInstance) return;

        var labels = ['2/10','2/12','2/14','2/16','2/18','2/20','2/22','2/24','2/26','2/28','3/2','3/4','3/6','3/8','3/10'];
        var data30 = [0, 0, 0, 0, 0, 0, 0, 0, 2, 3, 5, 6, 4, 3, 2];

        analyticsChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label:           'Views',
                    data:            data30,
                    borderColor:     '#5b2d8e',
                    backgroundColor: 'rgba(91,45,142,0.06)',
                    borderWidth:     2,
                    pointRadius:     2,
                    tension:         0.4,
                    fill:            true,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 12 } } } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 2 }, grid: { color: '#f0f0f0' } },
                    x: { grid: { color: '#f0f0f0' }, ticks: { font: { size: 11 } } },
                },
            },
        });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.vd-period-btn');
        if (!btn) return;
        qsa('.vd-period-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');

        if (!analyticsChartInstance) return;
        var days = parseInt(btn.dataset.days);
        var newData   = days === 7  ? [0, 1, 2, 3, 2, 3, 2]
                      : days === 30 ? [0, 0, 0, 0, 0, 0, 0, 0, 2, 3, 5, 6, 4, 3, 2]
                      : [0,0,1,0,0,2,1,3,2,4,5,3,2,4,3,2,5,6,4,3,2,3,4,2,1,3,2,1,2,3];
        var newLabels = days === 7  ? ['3/4','3/5','3/6','3/7','3/8','3/9','3/10']
                      : days === 30 ? ['2/10','2/12','2/14','2/16','2/18','2/20','2/22','2/24','2/26','2/28','3/2','3/4','3/6','3/8','3/10']
                      : Array.from({ length: 30 }, function (_, i) { return '1/' + (i + 10); });

        analyticsChartInstance.data.labels           = newLabels;
        analyticsChartInstance.data.datasets[0].data = newData;
        analyticsChartInstance.update();
    });

    /* ══════════════════════════════════
       NUMERIC INPUTS — comma formatting
    ══════════════════════════════════ */
    document.querySelectorAll('.vd-numeric').forEach(function (inp) {
        inp.addEventListener('blur', function () {
            var raw = this.value.replace(/[^0-9.]/g, '');
            if (!raw) return;
            var num = parseFloat(raw);
            if (!isNaN(num)) {
                this.value = num % 1 === 0
                    ? num.toLocaleString('en-US')
                    : num.toLocaleString('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 2 });
            }
        });
        inp.addEventListener('focus', function () {
            this.value = this.value.replace(/,/g, '');
        });
    });

    /* ══════════════════════════════════
       FACTORY OPTIONS
    ══════════════════════════════════ */
    var svMain            = document.querySelector('#svFactoryOptions .vd-sv-main');
    var urlFactoryOptions = svMain ? svMain.dataset.urlFactoryOptions : '';

    function saveFactoryOptions() {
        if (!urlFactoryOptions) return;

        var selectedIds = [];
        document.querySelectorAll('.vd-factory-option-cb:checked').forEach(function (cb) {
            selectedIds.push(parseInt(cb.dataset.optionId));
        });

        fetch(urlFactoryOptions, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN':     getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'Content-Type':     'application/json',
            },
            body: JSON.stringify({
                selected_ids: selectedIds,
                starred_ids:  [],
            }),
        })
        .then(function (res) { return res.json(); })
        .then(function (result) {
            if (result.success) {
                toastr.success(result.message || 'Saved.', 'Success');
            } else {
                toastr.error(result.message || 'Something went wrong.', 'Error');
            }
        })
        .catch(function () {
            toastr.error('Network error.', 'Error');
        });
    }

    document.addEventListener('click', function (e) {
        var label = e.target.closest('.vd-feat-check-label');
        if (!label) return;
        e.preventDefault();
        var cb   = label.querySelector('.vd-factory-option-cb');
        var icon = label.querySelector('.vd-feat-checkbox-icon i');
        if (!cb) return;
        cb.checked = !cb.checked;
        if (icon) {
            icon.className = cb.checked ? 'bi bi-check-square-fill' : 'bi bi-square';
        }
        saveFactoryOptions();
    });

    /* ══════════════════════════════════
       INIT
    ══════════════════════════════════ */
    if (videoUrlInput && videoUrlInput.value.trim()) {
        updateVideoPreview();
    }

    showNav('overview');

    /* ══════════════════════════════════
       PREMIUM BUILD OPTIONS
       Must be AFTER showNav() so that
       @push('page-modals') DOM exists
    ══════════════════════════════════ */
    var urlPremiumOptions    = vdPage ? vdPage.dataset.urlPremiumOptions    : '';
    var urlPremiumOptionBase = vdPage ? vdPage.dataset.urlPremiumOptionBase : '';
    var pboTableBody         = document.getElementById('pboTableBody');
    var formAddOption        = document.getElementById('formAddOption');

    if (formAddOption && urlPremiumOptions) {

        formAddOption.querySelectorAll('.vd-input[required]').forEach(function (inp) {
            inp.addEventListener('input', function () {
                inp.classList.remove('is-invalid');
            });
        });

        formAddOption.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!formAddOption.checkValidity()) {
                var firstInvalid = formAddOption.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.classList.add('is-invalid');
                }
                return;
            }

            var btn      = document.getElementById('btnSaveOption');
            var origHtml = btn.innerHTML;
            btn.disabled  = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:12px;height:12px;border-width:2px;"></span>';

            var data = {
                factory_code: formAddOption.querySelector('[name="factory_code"]').value.trim(),
                category:     formAddOption.querySelector('[name="category"]').value.trim(),
                name:         formAddOption.querySelector('[name="name"]').value.trim(),
                description:  formAddOption.querySelector('[name="description"]').value.trim(),
                msrp:         formAddOption.querySelector('[name="msrp"]').value.replace(/[^0-9.]/g, '') || null,
            };

            fetch(urlPremiumOptions, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, status: res.status, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toastr.success(result.data.message, 'Success');
                    appendPboRow(result.data.option);
                    closeModal('modalAddOption');
                    formAddOption.reset();
                } else if (result.status === 422 && result.data.errors) {
                    Object.values(result.data.errors).forEach(function (msgs) {
                        msgs.forEach(function (msg) { toastr.error(msg, 'Error'); });
                    });
                } else {
                    toastr.error(result.data.message || 'Something went wrong.', 'Error');
                }
            })
            .catch(function () { toastr.error('Network error.', 'Error'); })
            .finally(function () {
                btn.disabled  = false;
                btn.innerHTML = origHtml;
            });
        });
    }

    function appendPboRow(opt) {
        var emptyRow = document.getElementById('pboEmptyRow');
        if (emptyRow) emptyRow.remove();

        var tr = document.createElement('tr');
        tr.dataset.optionId = opt.id;
        tr.innerHTML =
            '<td>' + escHtml(opt.factory_code) + '</td>' +
            '<td>' + escHtml(opt.category) + '</td>' +
            '<td>' + escHtml(opt.name) + '</td>' +
            '<td>' + escHtml(opt.description || '—') + '</td>' +
            '<td>' + (opt.msrp ? '$' + parseFloat(opt.msrp).toLocaleString('en-US', {minimumFractionDigits:2}) : '—') + '</td>' +
            '<td><button type="button" class="vd-btn-remove pbo-delete" data-id="' + opt.id + '" style="padding:4px 10px;font-size:12px;"><i class="bi bi-trash3"></i></button></td>';

        if (pboTableBody) pboTableBody.appendChild(tr);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.pbo-delete');
        if (!btn || !urlPremiumOptionBase) return;

        if (!confirm('Delete this option?')) return;

        var id  = btn.dataset.id;
        var url = urlPremiumOptionBase.replace('__ID__', id);

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN':     getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
        })
        .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
        .then(function (result) {
            if (result.ok && result.data.success) {
                var row = pboTableBody ? pboTableBody.querySelector('[data-option-id="' + id + '"]') : null;
                if (row) row.remove();
                if (pboTableBody && !pboTableBody.querySelector('tr')) {
                    var empty = document.createElement('tr');
                    empty.id = 'pboEmptyRow';
                    empty.innerHTML = '<td colspan="6" class="vd-table-empty">No results found.</td>';
                    pboTableBody.appendChild(empty);
                }
                toastr.success(result.data.message, 'Success');
            } else {
                toastr.error(result.data.message || 'Something went wrong.', 'Error');
            }
        })
        .catch(function () { toastr.error('Network error.', 'Error'); });
    });

    /* ══════════════════════════════════
       VDP INCENTIVES — HIDE TOGGLE
    ══════════════════════════════════ */
    var urlIncHideBase   = vdPage ? vdPage.dataset.urlIncentiveHideBase   : '';
    var urlIncUnhideBase = vdPage ? vdPage.dataset.urlIncentiveUnhideBase : '';

    document.addEventListener('change', function (e) {
        var toggle = e.target.closest('.inc-hide-toggle');
        if (!toggle) return;

        var incentiveId = toggle.dataset.incentiveId;
        var isHiding    = toggle.checked;
        var row         = document.getElementById('inc-row-' + incentiveId);

        var url = isHiding
            ? urlIncHideBase.replace('__ID__', incentiveId)
            : urlIncUnhideBase.replace('__ID__', incentiveId);

        var method = isHiding ? 'POST' : 'DELETE';

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN':     getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
        })
        .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
        .then(function (result) {
            if (result.ok && result.data.success) {
                if (row) row.style.opacity = isHiding ? '0.5' : '1';
                toastr.success(result.data.message, 'Success');
            } else {
                toggle.checked = !isHiding; // revert
                toastr.error(result.data.message || 'Something went wrong.', 'Error');
            }
        })
        .catch(function () {
            toggle.checked = !isHiding; // revert
            toastr.error('Network error.', 'Error');
        });
    });

    /* ══════════════════════════════════
       TOOLTIP POSITION FIX
    ══════════════════════════════════ */
    document.addEventListener('mouseover', function (e) {
        var wrap = e.target.closest('.vd-inc-tooltip');
        if (!wrap) return;
        var tip = wrap.querySelector('.vd-inc-tooltip-text');
        if (!tip) return;

        var rect = wrap.getBoundingClientRect();
        var tipW  = 220;

        var left = rect.left + (rect.width / 2);
        var top  = rect.bottom + 8;

        if (left - tipW / 2 < 8) left = tipW / 2 + 8;
        if (left + tipW / 2 > window.innerWidth - 8) left = window.innerWidth - tipW / 2 - 8;

        tip.style.left      = left + 'px';
        tip.style.top       = top + 'px';
        tip.style.transform = 'translateX(-50%)';
    });


    /* ══════════════════════════════════
       PRINTABLES
    ══════════════════════════════════ */

    var svPrintables         = document.getElementById('svPrintables');
    var printablesTableBody  = document.getElementById('printablesTableBody');
    var btnAddPrintable      = document.getElementById('btnAddPrintable');
    var btnCreatePrintable   = document.getElementById('btnCreatePrintable');
    var printableNameSelect  = document.getElementById('printableNameSelect');
    var printableCtaSelect   = document.getElementById('printableCtaSelect');
    var printableDescription = document.getElementById('printableDescription');
    var printableLayout      = document.getElementById('printableLayout');

    var urlPrintablesIndex       = svPrintables ? svPrintables.dataset.urlPrintablesIndex      : '';
    var urlPrintablesStore       = svPrintables ? svPrintables.dataset.urlPrintablesStore      : '';
    var urlPrintablesUpdateBase  = svPrintables ? svPrintables.dataset.urlPrintablesUpdateBase : '';
    var urlPrintablesDestroyBase = svPrintables ? svPrintables.dataset.urlPrintablesDestroyBase: '';
    var urlPrintablesRenderBase  = svPrintables ? svPrintables.dataset.urlPrintablesRenderBase : '';

    var printablesLoaded = false;

    // ── Load printables when tab becomes active ────────────────────────────────
    // Hook into showNav — printables loads lazily on first visit
    var _originalShowNav = showNav;
    showNav = function (key) {
        _originalShowNav(key);
        if (key === 'printables' && !printablesLoaded) {
            loadPrintables();
        }
    };

    function loadPrintables() {
        if (!urlPrintablesIndex) return;

        fetch(urlPrintablesIndex, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
        })
        .then(function (res) { return res.json(); })
        .then(function (result) {
            if (!result.success) return;

            printablesLoaded = true;
            renderPrintablesTable(result.printables);
            updateAddButton(result.available);
        })
        .catch(function () {
            if (printablesTableBody) {
                printablesTableBody.innerHTML =
                    '<tr><td colspan="3" class="vd-table-empty" style="color:#c0392b;">Failed to load printables.</td></tr>';
            }
        });
    }

    // ── Render table ──────────────────────────────────────────────────────────

    function renderPrintablesTable(printables) {
        if (!printablesTableBody) return;

        if (!printables || !printables.length) {
            printablesTableBody.innerHTML =
                '<tr id="printablesEmptyRow"><td colspan="3" class="vd-table-empty">No printables found.</td></tr>';
            return;
        }

        printablesTableBody.innerHTML = '';
        printables.forEach(function (p) {
            printablesTableBody.appendChild(buildPrintableRow(p));
        });
    }

    function buildPrintableRow(p) {
        var tr = document.createElement('tr');
        tr.dataset.printableId = p.id;

        var renderUrl = urlPrintablesRenderBase.replace('__ID__', p.id);

        tr.innerHTML =
            '<td style="font-weight:500;">' + escHtml(p.name) + '</td>' +
            '<td style="color:#888;">' + escHtml(p.description || '') + '</td>' +
            '<td style="text-align:right;white-space:nowrap;">' +
                // Delete btn (only for non-Buyer's Guide — that's system-seeded)
                '<button type="button" class="vd-btn-remove printable-delete" ' +
                'data-id="' + p.id + '" ' +
                'style="padding:4px 10px;font-size:12px;margin-right:6px;">' +
                    '<i class="bi bi-trash3"></i>' +
                '</button>' +
                // Open/Print btn
                '<button type="button" class="vd-btn-view-listing printable-open" ' +
                'data-url="' + renderUrl + '" ' +
                'style="padding:4px 12px;font-size:12px;">' +
                    '<i class="bi bi-arrow-right-short"></i>' +
                '</button>' +
            '</td>';

        return tr;
    }

    // ── Update "Add" button — disable when all types used ─────────────────────

    function updateAddButton(available) {
        if (!btnAddPrintable) return;

        if (!available || !available.length) {
            btnAddPrintable.disabled  = true;
            btnAddPrintable.style.opacity = '0.5';
            btnAddPrintable.style.cursor  = 'not-allowed';
            btnAddPrintable.title = 'All printable types already exist.';
        } else {
            btnAddPrintable.disabled  = false;
            btnAddPrintable.style.opacity = '1';
            btnAddPrintable.style.cursor  = 'pointer';
            btnAddPrintable.title = '';
        }

        // Populate Name dropdown with only unused types
        if (printableNameSelect) {
            printableNameSelect.innerHTML = '<option value="">Select...</option>';
            (available || []).forEach(function (type) {
                var opt       = document.createElement('option');
                opt.value     = type;
                opt.textContent = type;
                printableNameSelect.appendChild(opt);
            });
        }
    }

    // ── Open Add modal ────────────────────────────────────────────────────────

    if (btnAddPrintable) {
        // Remove existing listener set earlier in the file (for openModal)
        // and replace with one that also refreshes available types
        btnAddPrintable.addEventListener('click', function () {
            if (this.disabled) return;
            // Reset modal fields
            if (printableNameSelect)  printableNameSelect.value  = '';
            if (printableCtaSelect)   printableCtaSelect.value   = '';
            if (printableDescription) printableDescription.value = '';
            if (printableLayout)      printableLayout.value      = 'portrait';
            openModal('modalAddPrintable');
        });
    }

    // ── Create printable ──────────────────────────────────────────────────────

    if (btnCreatePrintable) {

        // ── name select change → clear error ──
        if (printableNameSelect) {
            printableNameSelect.addEventListener('change', function () {
                this.classList.remove('is-invalid');
            });
        }

        btnCreatePrintable.addEventListener('click', function () {
            var name = printableNameSelect  ? printableNameSelect.value.trim()  : '';
            var cta  = printableCtaSelect   ? printableCtaSelect.value.trim()   : '';
            var desc = printableDescription ? printableDescription.value.trim() : '';
            var lay  = printableLayout      ? printableLayout.value             : 'portrait';

            // ── Validation ────────────────────────────────────────────────
            var hasError = false;

            if (!name) {
                if (printableNameSelect) {
                    printableNameSelect.classList.add('is-invalid');
                    printableNameSelect.focus();
                }
                toastr.error('Please select a printable name.', 'Required');
                hasError = true;
            }

            if (hasError) return;

            // ── Save button loading state ─────────────────────────────────
            var origHtml              = btnCreatePrintable.innerHTML;
            btnCreatePrintable.disabled  = true;
            btnCreatePrintable.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" ' +
                'style="width:12px;height:12px;border-width:2px;"></span> Saving...';

            fetch(urlPrintablesStore, {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify({ name: name, cta: cta, description: desc, layout: lay }),
            })
            .then(function (res) {
                return res.json().then(function (d) {
                    return { ok: res.ok, status: res.status, data: d };
                });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toastr.success(result.data.message, 'Success');
                    closeModal('modalAddPrintable');

                    var emptyRow = document.getElementById('printablesEmptyRow');
                    if (emptyRow) emptyRow.remove();

                    if (printablesTableBody) {
                        printablesTableBody.appendChild(buildPrintableRow(result.data.printable));
                    }

                    printablesLoaded = false;
                    loadPrintables();

                } else if (result.status === 422 && result.data.errors) {
                    Object.values(result.data.errors).forEach(function (msgs) {
                        msgs.forEach(function (msg) { toastr.error(msg, 'Error'); });
                    });
                    // Re-enable on validation error — user needs to fix
                    btnCreatePrintable.disabled  = false;
                    btnCreatePrintable.innerHTML = origHtml;
                } else {
                    toastr.error(result.data.message || 'Something went wrong.', 'Error');
                    btnCreatePrintable.disabled  = false;
                    btnCreatePrintable.innerHTML = origHtml;
                }
            })
            .catch(function () {
                toastr.error('Network error.', 'Error');
                btnCreatePrintable.disabled  = false;
                btnCreatePrintable.innerHTML = origHtml;
            });
            // Note: .finally() removed — re-enable handled per-case above
            // (success case: modal closes, no re-enable needed)
        });
    }

    // ── Open printable (new tab) ──────────────────────────────────────────────

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.printable-open');
        if (!btn) return;
        var url = btn.dataset.url;
        if (url) window.open(url, '_blank');
    });

    // ── Delete printable ──────────────────────────────────────────────────────

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.printable-delete');
        if (!btn) return;

        if (!confirm('Delete this printable?')) return;

        var id  = btn.dataset.id;
        var url = urlPrintablesDestroyBase.replace('__ID__', id);
        var row = printablesTableBody
            ? printablesTableBody.querySelector('[data-printable-id="' + id + '"]')
            : null;

        fetch(url, {
            method:  'DELETE',
            headers: {
                'X-CSRF-TOKEN':     getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
        })
        .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
        .then(function (result) {
            if (result.ok && result.data.success) {
                if (row) row.remove();

                if (printablesTableBody && !printablesTableBody.querySelector('tr[data-printable-id]')) {
                    printablesTableBody.innerHTML =
                        '<tr id="printablesEmptyRow"><td colspan="3" class="vd-table-empty">No printables found.</td></tr>';
                }

                toastr.success(result.data.message, 'Success');

                // Refresh to re-enable Add button
                printablesLoaded = false;
                loadPrintables();
            } else {
                toastr.error(result.data.message || 'Something went wrong.', 'Error');
            }
        })
        .catch(function () { toastr.error('Network error.', 'Error'); });
    });


})();
