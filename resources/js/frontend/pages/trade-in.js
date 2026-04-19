/**
 * Trade-in Form — 4-step wizard + AJAX submit + photo upload
 * Routes:
 *   POST /forms/trade-in          → frontend.forms.trade-in       → { success, form_entry_id }
 *   POST /forms/trade-in/photos   → frontend.forms.trade-in.photos → { success, photos }
 *   GET  /data/makes/{make}/models → frontend.data.make-models     → [{ id, name }]
 *
 * Lives in: resources/js/frontend/pages/trade-in.js
 */

(function () {

    var offcanvasEl = document.getElementById('getTrade');
    if (!offcanvasEl) return;

    var routes       = window.tiRoutes || {};
    var csrf         = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    var urlVinDecode = offcanvasEl.dataset.urlVinDecode || '';

    var state = {
        inputMethod:    'ymm',
        formEntryId:    null,
        uploadedPhotos: [],
        keys:           null,
        ownership:      null,
        yesNoValues:    {},
    };

    var startOverBar   = document.getElementById('ti-start-over-bar');
    var startOverBtn   = document.getElementById('ti-start-over-btn');
    var step1El        = document.getElementById('ti-step-1');
    var step1bEl       = document.getElementById('ti-step-1b');
    var step2El        = document.getElementById('ti-step-2');
    var step3El        = document.getElementById('ti-step-3');
    var step4El        = document.getElementById('ti-step-4');
    var successEl      = document.getElementById('ti-success');
    var header1        = document.getElementById('ti-header-1');
    var header2        = document.getElementById('ti-header-2');
    var header3        = document.getElementById('ti-header-3');
    var header4        = document.getElementById('ti-header-4');
    var tabYmm         = document.getElementById('ti-tab-ymm');
    var tabVin         = document.getElementById('ti-tab-vin');
    var panelYmm       = document.getElementById('ti-panel-ymm');
    var panelVin       = document.getElementById('ti-panel-vin');
    var yearSel        = document.getElementById('ti-year');
    var makeSel        = document.getElementById('ti-make');
    var modelSel       = document.getElementById('ti-model');
    var trimRow        = document.getElementById('ti-trim-row');
    var bodyRow        = document.getElementById('ti-body-row');
    var vinInput       = document.getElementById('ti-vin');
    var continue1a     = document.getElementById('ti-continue-1a');
    var continue1b     = document.getElementById('ti-continue-1b');
    var continue2      = document.getElementById('ti-continue-2');
    var continue3      = document.getElementById('ti-continue-3');
    var skipPhotos     = document.getElementById('ti-skip-photos');
    var submitBtn      = document.getElementById('ti-submit-btn');
    var vehicleTitle   = document.getElementById('ti-vehicle-title-display');
    var ownershipHidden   = document.getElementById('ti-ownership-hidden');
    var keysHidden        = document.getElementById('ti-keys-hidden');
    var lienholderRow     = document.getElementById('ti-lienholder-row');
    var balanceRow        = document.getElementById('ti-balance-row');
    var dropzone       = document.getElementById('ti-dropzone');
    var photoInput     = document.getElementById('ti-photo-input');
    var photoPreview   = document.getElementById('ti-photo-preview');
    var photoGrid      = document.getElementById('ti-photo-grid');
    var uploadStatus   = document.getElementById('ti-upload-status');

    // ── Helpers ───────────────────────────────────────────────────────────────

    function show(el)  { if (el) el.classList.remove('hidden'); }
    function hide(el)  { if (el) el.classList.add('hidden'); }

    function scrollOffcanvasTop() {
        var body = offcanvasEl.querySelector('.offcanvas-body');
        if (body) body.scrollTop = 0;
    }

    // ── Inline validation helpers ─────────────────────────────────────────────

    function setFieldError(inputEl, message) {
        if (!inputEl) return;
        inputEl.classList.add('is-invalid');
        var fb = inputEl.nextElementSibling;
        if (fb && (fb.classList.contains('invalid-feedback') || fb.classList.contains('text-danger'))) {
            fb.textContent = message;
        }
    }

    function setError(errorId, message) {
        var el = document.getElementById(errorId);
        if (el) el.textContent = message;
    }

    function clearError(errorId) {
        var el = document.getElementById(errorId);
        if (el) el.textContent = '';
    }

    function clearFieldError(inputEl) {
        if (!inputEl) return;
        inputEl.classList.remove('is-invalid');
        var fb = inputEl.nextElementSibling;
        if (fb && (fb.classList.contains('invalid-feedback') || fb.classList.contains('text-danger'))) {
            fb.textContent = '';
        }
    }

    function clearAllStep1bErrors() {
        ['ti-mileage', 'ti-postal-code', 'ti-exterior-color', 'ti-interior-color'].forEach(function (id) {
            clearFieldError(document.getElementById(id));
        });
        clearError('ti-keys-error');
        clearError('ti-ownership-error');
    }

    function clearAllStep2Errors() {
        clearError('ti-condition-error');
        ['clean_title', 'run_drive', 'accident', 'warning_lights', 'smoked_in'].forEach(function (field) {
            clearError('ti-' + field.replace(/_/g, '-') + '-error');
        });
        clearError('ti-damage-error');
    }

    // ── Header helpers ────────────────────────────────────────────────────────

    function setHeaderActive(header) {
        [header1, header2, header3, header4].forEach(function (h) {
            if (!h) return;
            h.querySelector('b').classList.remove('text-primary');
            h.querySelector('b').classList.add('text-muted');
            h.classList.remove('wizardstep-active');
        });
        if (header) {
            header.querySelector('b').classList.remove('text-muted');
            header.querySelector('b').classList.add('text-primary');
            header.classList.add('wizardstep-active');
        }
    }

    function setHeaderEdit(header, stepNum) {
        if (!header) return;
        var indicator = header.querySelector('.wizardstep-indicator');
        if (!indicator) return;
        indicator.innerHTML = '<u class="cursor-pointer ti-edit-step" data-step="' + stepNum + '">Edit</u>';
        indicator.classList.add('text-muted');
    }

    function getVehicleLabel() {
        if (state.inputMethod === 'vin') {
            return vinInput.value.trim() ? 'VIN: ' + vinInput.value.trim() : '';
        }
        var year  = yearSel.value;
        var make  = makeSel.options[makeSel.selectedIndex]?.text ?? '';
        var model = modelSel.options[modelSel.selectedIndex]?.text ?? '';
        return [year, make, model].filter(Boolean).join(' ');
    }

    // ── Tab switching ─────────────────────────────────────────────────────────

    tabYmm.addEventListener('click', function () {
        state.inputMethod = 'ymm';
        tabYmm.classList.add('active');
        tabVin.classList.remove('active');
        show(panelYmm);
        hide(panelVin);
        checkStep1aReady();
    });

    tabVin.addEventListener('click', function () {
        state.inputMethod = 'vin';
        tabVin.classList.add('active');
        tabYmm.classList.remove('active');
        hide(panelYmm);
        show(panelVin);
        checkStep1aReady();
    });

    // ── Make → Models AJAX ────────────────────────────────────────────────────

    makeSel.addEventListener('change', function () {
        var make = makeSel.value;
        modelSel.innerHTML = '<option value="">[Select Model]</option>';
        modelSel.disabled = true;
        hide(trimRow);
        hide(bodyRow);

        if (!make) { checkStep1aReady(); return; }

        fetch(routes.makeModels.replace('__make__', encodeURIComponent(make)), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
        .then(function (r) { return r.json(); })
        .then(function (models) {
            models.forEach(function (m) {
                var opt = document.createElement('option');
                opt.value = m.name;
                opt.textContent = m.name;
                modelSel.appendChild(opt);
            });
            modelSel.disabled = false;
            checkStep1aReady();
        })
        .catch(function () { modelSel.disabled = false; });
    });

    modelSel.addEventListener('change', function () {
        if (modelSel.value) { show(trimRow); show(bodyRow); }
        else                { hide(trimRow); hide(bodyRow); }
        checkStep1aReady();
    });

    yearSel.addEventListener('change', checkStep1aReady);
    vinInput.addEventListener('input', function () {
        clearVinError();
        checkStep1aReady();
    });

    function checkStep1aReady() {
        var ready = state.inputMethod === 'ymm'
            ? (yearSel.value && makeSel.value && modelSel.value)
            : (vinInput.value.trim().length === 17);
        continue1a.disabled = !ready;
    }

    // ── Step 1a Continue ──────────────────────────────────────────────────────

    continue1a.addEventListener('click', function () {
        if (state.inputMethod === 'vin') {
            decodeVinAndContinue();
            return;
        }
        proceedToStep1b();
    });

    /* ── Step 1a → Step 1b proceed (shared by both YMM and VIN paths) ── */
    function proceedToStep1b() {
        vehicleTitle.textContent = getVehicleLabel();

        var introContent = step1El.querySelector('.py-4');
        if (introContent) hide(introContent);
        var continue1aRow = continue1a.closest('.row');
        if (continue1aRow) hide(continue1aRow);

        show(step1bEl);
        show(startOverBar);
        scrollOffcanvasTop();
    }

    /* ── VIN decode → auto-fill step 1b fields → proceed ── */
    function decodeVinAndContinue() {
        var vin = vinInput.value.trim();

        if (!urlVinDecode) {
            // Route not configured — just proceed without decode
            proceedToStep1b();
            return;
        }

        // Loading state
        var origHtml      = continue1a.innerHTML;
        continue1a.disabled  = true;
        continue1a.innerHTML = '<span class="spinner-border spinner-border-sm me-1" '
            + 'style="width:13px;height:13px;border-width:2px;"></span> Decoding...';

        fetch(urlVinDecode, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':     csrf,
                'Accept':           'application/json',
                'Content-Type':     'application/json',
            },
            body: JSON.stringify({ vin: vin }),
        })
        .then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, status: res.status, data: data };
            });
        })
        .then(function (result) {
            if (result.ok && result.data.success) {
                // Auto-fill step 1b fields
                populateVinFields(result.data.data);

                // Update vehicle title to show decoded year/make/model
                var d = result.data.data;
                var title = [d.year, d.make, d.model].filter(Boolean).join(' ');
                if (title) state.vinDecodedTitle = title;

                proceedToStep1b();
                return;
            }

            // Validation error (422)
            if (result.status === 422) {
                var msg = (result.data.errors && result.data.errors.vin)
                    ? result.data.errors.vin[0]
                    : (result.data.message || 'Invalid VIN. Please check and try again.');
                showVinError(msg);
                return;
            }

            // NHTSA returned an error (VIN not decodeable)
            if (result.data && result.data.message) {
                showVinError(result.data.message);
                return;
            }

            // Any other error — just proceed anyway, VIN still submitted as-is
            proceedToStep1b();
        })
        .catch(function () {
            // Network error — still proceed, VIN will be saved as-is
            proceedToStep1b();
        })
        .finally(function () {
            continue1a.innerHTML = origHtml;
            checkStep1aReady();
        });
    }

    /* ── Populate step 1b visible fields from VIN decode response ── */
    function populateVinFields(data) {
        if (!data) return;

        state.vinDecoded = {
            year:       data.year       || null,
            make:       data.make       || null,
            model:      data.model      || null,
            trim:       data.trim       || null,
            engine:     data.engine_string || null,
            drivetrain: null,
            body:       null,
        };

        // Engine
        var engineEl = document.getElementById('ti-engine');
        if (engineEl && data.engine_string) {
            engineEl.value = data.engine_string;
        }

        // Drivetrain select match
        var driveEl = document.getElementById('ti-drivetrain');
        if (driveEl) {
            var candidates = [data.drivetrain_standard, data.drive_type].filter(Boolean);
            var matched    = false;

            for (var c = 0; c < candidates.length && !matched; c++) {
                var needle = candidates[c].toLowerCase();
                for (var o = 0; o < driveEl.options.length && !matched; o++) {
                    var optVal = driveEl.options[o].value.toLowerCase();
                    if (optVal && (optVal.indexOf(needle) !== -1 || needle.indexOf(optVal) !== -1)) {
                        driveEl.value = driveEl.options[o].value;
                        state.vinDecoded.drivetrain = driveEl.options[o].value; // ← matched value store
                        matched = true;
                    }
                }
            }
        }

        // Body style select match (same strategy as drivetrain)
        var bodyEl = document.getElementById('ti-body');
        if (bodyEl && data.body_class) {
            var bodyNeedle = data.body_class.toLowerCase();
            for (var b = 0; b < bodyEl.options.length; b++) {
                var bodyOptVal = bodyEl.options[b].value.toLowerCase();
                if (bodyOptVal && (bodyOptVal.indexOf(bodyNeedle) !== -1 || bodyNeedle.indexOf(bodyOptVal) !== -1)) {
                    bodyEl.value = bodyEl.options[b].value;
                    state.vinDecoded.body = bodyEl.options[b].value;
                    break;
                }
            }
        }
    }

    /* ── VIN error display helpers ── */
    function showVinError(message) {
        vinInput.classList.add('is-invalid');
        var errEl = document.getElementById('ti-vin-error');
        if (errEl) {
            errEl.textContent    = message;
            errEl.style.display  = '';
        }
    }

    function clearVinError() {
        vinInput.classList.remove('is-invalid');
        var errEl = document.getElementById('ti-vin-error');
        if (errEl) {
            errEl.textContent   = '';
            errEl.style.display = 'none';
        }
    }

    // ── Back to Step 1a from Step 1b ──────────────────────────────────────────
    var backTo1aBtn = document.getElementById('ti-back-to-1a');
    if (backTo1aBtn) {
        backTo1aBtn.addEventListener('click', function () {
            hide(step1bEl);

            // step1El ka intro content wapas show karo
            var introContent = step1El.querySelector('.py-4');
            if (introContent) show(introContent);
            var continue1aRow = continue1a.closest('.row');
            if (continue1aRow) show(continue1aRow);

            scrollOffcanvasTop();
        });
    }

    // ── Keys & Ownership buttons ──────────────────────────────────────────────

    document.querySelectorAll('.ti-keys-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.ti-keys-btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            state.keys = btn.dataset.value;
            keysHidden.value = btn.dataset.value;
            clearError('ti-keys-error');
        });
    });

    document.querySelectorAll('.ti-ownership-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.ti-ownership-btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            state.ownership = btn.dataset.value;
            ownershipHidden.value = btn.dataset.value;
            clearError('ti-ownership-error');
            if (btn.dataset.value === 'Loan' || btn.dataset.value === 'Lease') {
                show(lienholderRow); show(balanceRow);
            } else {
                hide(lienholderRow); hide(balanceRow);
            }
        });
    });

    // ── Step 1b Continue ──────────────────────────────────────────────────────

    continue1b.addEventListener('click', function () {
        clearAllStep1bErrors();

        var hasErrors = false;
        var mileageEl    = document.getElementById('ti-mileage');
        var postalEl     = document.getElementById('ti-postal-code');
        var extColorEl   = document.getElementById('ti-exterior-color');
        var intColorEl   = document.getElementById('ti-interior-color');

        if (!mileageEl.value.trim()) {
            setFieldError(mileageEl, 'Mileage is required.');
            hasErrors = true;
        }
        if (!postalEl.value.trim()) {
            setFieldError(postalEl, 'Postal code is required.');
            hasErrors = true;
        }
        if (!extColorEl.value) {
            setFieldError(extColorEl, 'Exterior color is required.');
            hasErrors = true;
        }
        if (!intColorEl.value) {
            setFieldError(intColorEl, 'Interior color is required.');
            hasErrors = true;
        }
        if (!state.keys) {
            setError('ti-keys-error', 'Please select number of keys.');
            hasErrors = true;
        }
        if (!state.ownership) {
            setError('ti-ownership-error', 'Please select ownership status.');
            hasErrors = true;
        }

        if (hasErrors) return;

        hide(step1El);
        hide(step1bEl);
        show(step2El);
        setHeaderActive(header2);
        setHeaderEdit(header1, 1);
        scrollOffcanvasTop();
    });

    // ── Yes/No buttons ────────────────────────────────────────────────────────

    document.querySelectorAll('.ti-yesno-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var field = btn.dataset.field;
            document.querySelectorAll('.ti-yesno-btn[data-field="' + field + '"]').forEach(function (b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            state.yesNoValues[field] = btn.dataset.value;
            var hiddenEl = document.getElementById('ti-' + field.replace(/_/g, '-'));
            if (hiddenEl) hiddenEl.value = btn.dataset.value;
            // Clear error for this field
            clearError('ti-' + field.replace(/_/g, '-') + '-error');
        });
    });

    // ── Damage checkboxes ─────────────────────────────────────────────────────

    document.querySelectorAll('.ti-damage-cb').forEach(function (cb) {
        cb.addEventListener('change', function () {
            clearError('ti-damage-error');
            if (cb.value === 'None of the above' && cb.checked) {
                document.querySelectorAll('.ti-damage-cb').forEach(function (other) {
                    if (other.value !== 'None of the above') other.checked = false;
                });
            } else if (cb.value !== 'None of the above' && cb.checked) {
                document.querySelectorAll('.ti-damage-cb[value="None of the above"]').forEach(function (none) {
                    none.checked = false;
                });
            }
        });
    });

    // ── Step 2 Continue ───────────────────────────────────────────────────────

    continue2.addEventListener('click', function () {
        clearAllStep2Errors();
        var hasErrors = false;

        // Condition radio
        var conditionVal = document.querySelector('input[name="condition"]:checked');
        if (!conditionVal) {
            setError('ti-condition-error', 'Please rate your vehicle condition.');
            hasErrors = true;
        }

        // Yes/No fields
        var required = ['clean_title', 'run_drive', 'accident', 'warning_lights', 'smoked_in'];
        var labels = {
            'clean_title':    'Please answer: clean title.',
            'run_drive':      'Please answer: run and drive.',
            'accident':       'Please answer: accident history.',
            'warning_lights': 'Please answer: warning lights.',
            'smoked_in':      'Please answer: smoked in.',
        };
        required.forEach(function (field) {
            if (!state.yesNoValues[field]) {
                setError('ti-' + field.replace(/_/g, '-') + '-error', labels[field]);
                hasErrors = true;
            }
        });

        // Damage checkboxes
        var damageChecked = document.querySelectorAll('.ti-damage-cb:checked');
        if (damageChecked.length === 0) {
            setError('ti-damage-error', 'Please select at least one option.');
            hasErrors = true;
        }

        if (hasErrors) return;

        hide(step2El);
        show(step3El);
        setHeaderActive(header3);
        setHeaderEdit(header2, 2);
        header1.classList.remove('wizardstep-active');
        scrollOffcanvasTop();
    });

    // ── Photo Upload ──────────────────────────────────────────────────────────

    dropzone.addEventListener('click', function () { photoInput.click(); });

    dropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropzone.classList.add('border-primary');
    });
    dropzone.addEventListener('dragleave', function () { dropzone.classList.remove('border-primary'); });
    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.classList.remove('border-primary');
        handleFiles(e.dataTransfer.files);
    });

    photoInput.addEventListener('change', function () {
        handleFiles(photoInput.files);
        photoInput.value = '';
    });

    function handleFiles(files) {
        var imageFiles = Array.from(files).filter(function (f) { return f.type.startsWith('image/'); });
        if (!imageFiles.length) return;

        imageFiles.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function (e) { addPhotoPreview(e.target.result, null); };
            reader.readAsDataURL(file);
        });

        if (!state.pendingFiles) state.pendingFiles = [];
        state.pendingFiles = state.pendingFiles.concat(imageFiles);
    }

    function addPhotoPreview(src, photoId) {
        show(photoPreview);
        var col = document.createElement('div');
        col.className = 'position-relative mb-3 px-sm-2 px-1 col-sm-2 col-3';
        col.dataset.photoId = photoId || '';
        col.innerHTML = '<button type="button" style="top:0;right:0;width:22px;height:22px;padding:0;line-height:1;z-index:2;" '
            + 'class="ti-remove-photo btn btn-white border-0 bg-white position-absolute d-flex align-items-center justify-content-center shadow-sm">'
            + '<i class="fa-solid fa-circle-xmark text-primary" style="font-size:18px;"></i>'
            + '</button>'
            + '<img class="border border-light w-100 d-block" alt="Trade photo" src="' + src + '">';

        col.querySelector('.ti-remove-photo').addEventListener('click', function () {
            col.remove();
            if (!photoGrid.children.length) hide(photoPreview);
        });
        photoGrid.appendChild(col);
    }

    // ── Step 3 Continue / Skip ────────────────────────────────────────────────

    continue3.addEventListener('click', function () { goToStep4(); });
    skipPhotos.addEventListener('click', function () { state.pendingFiles = []; goToStep4(); });

    function goToStep4() {
        hide(step3El);
        show(step4El);
        setHeaderActive(header4);
        setHeaderEdit(header3, 3);
        scrollOffcanvasTop();
    }

    // ── Step 4 Submit ─────────────────────────────────────────────────────────

    submitBtn.addEventListener('click', function () { submitForm(); });

    function submitForm() {
        var fd = new FormData();
        fd.append('input_method', state.inputMethod);

        if (state.inputMethod === 'ymm') {
            fd.append('year',       yearSel.value);
            fd.append('make',       makeSel.value);
            fd.append('model',      modelSel.value);
            fd.append('trim',       document.getElementById('ti-trim').value);
            fd.append('body',       document.getElementById('ti-body').value);
            fd.append('engine',     document.getElementById('ti-engine')?.value ?? '');
            fd.append('drivetrain', document.getElementById('ti-drivetrain')?.value ?? '');
        } else {
            fd.append('vin', vinInput.value.trim());
            var vd = state.vinDecoded || {};
            fd.append('year',       vd.year       || '');
            fd.append('make',       vd.make       || '');
            fd.append('model',      vd.model      || '');
            fd.append('trim',       vd.trim       || '');
            fd.append('engine',     vd.engine     || '');
            fd.append('drivetrain', vd.drivetrain || '');
            fd.append('body',       vd.body       || '');
        }

        fd.append('mileage',        document.getElementById('ti-mileage').value);
        fd.append('postal_code',    document.getElementById('ti-postal-code').value);
        fd.append('exterior_color', document.getElementById('ti-exterior-color').value);
        fd.append('interior_color', document.getElementById('ti-interior-color').value);
        fd.append('keys',           keysHidden.value);
        fd.append('ownership',      ownershipHidden.value);

        var conditionRadio = document.querySelector('input[name="condition"]:checked');
        fd.append('condition',      conditionRadio ? conditionRadio.value : '');
        fd.append('clean_title',    document.getElementById('ti-clean-title').value);
        fd.append('run_drive',      document.getElementById('ti-run-drive').value);
        fd.append('accident',       document.getElementById('ti-accident').value);
        fd.append('warning_lights', document.getElementById('ti-warning-lights').value);
        fd.append('smoked_in',      document.getElementById('ti-smoked-in').value);
        fd.append('tire_condition', document.getElementById('ti-tire-condition')?.value ?? '');

        document.querySelectorAll('.ti-damage-cb:checked').forEach(function (cb) {
            fd.append('damage[]', cb.value);
        });

        fd.append('first_name', document.getElementById('ti-first-name').value);
        fd.append('last_name',  document.getElementById('ti-last-name').value);
        fd.append('email',      document.getElementById('ti-email').value);
        fd.append('phone',      document.getElementById('ti-phone').value);
        fd.append('comment',    document.getElementById('ti-comment').value);

        var commpref = document.querySelector('input[name="commpref"]:checked');
        fd.append('commpref', commpref ? commpref.value : 'email');

        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-label').textContent = 'Sending...';
        submitBtn.querySelector('.btn-icon')?.classList.add('d-none');

        fetch(routes.tradeIn, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: fd,
        })
        .then(function (r) { return r.json().then(function (j) { return { status: r.status, json: j }; }); })
        .then(function (_ref) {
            var status = _ref.status; var json = _ref.json;

            if (status === 200 && json.success) {
                state.formEntryId = json.form_entry_id;
                if (state.pendingFiles && state.pendingFiles.length > 0) {
                    uploadPhotos(state.formEntryId).then(showSuccess);
                } else {
                    showSuccess();
                }
                return;
            }
            if (status === 422 && json.errors) {
                showServerErrors(json.errors);
                resetSubmitBtn();
                return;
            }
            resetSubmitBtn();
            console.error('Trade-in submit error:', json);
        })
        .catch(function (err) {
            resetSubmitBtn();
            console.error('Trade-in network error:', err);
        });
    }

    function uploadPhotos(entryId) {
        show(uploadStatus);
        var fd = new FormData();
        fd.append('form_entry_id', entryId);
        state.pendingFiles.forEach(function (file) { fd.append('photos[]', file); });

        return fetch(routes.tradeInPhotos, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: fd,
        })
        .then(function (r) { return r.json(); })
        .then(function (json) { hide(uploadStatus); return json; })
        .catch(function (err) { hide(uploadStatus); console.error('Photo upload error:', err); });
    }

    function showSuccess() {
        hide(step4El);
        show(successEl);
        header4.querySelector('b').classList.remove('text-primary');
        header4.querySelector('b').classList.add('text-muted');
        header4.classList.remove('wizardstep-active');
        scrollOffcanvasTop();

        if (window.NpsModule) {
            window.NpsModule.init({
                starRatingId: 'ti-star-rating',
                npsOptionsId: 'tiNpsOptions',
                submitBtnSel: '.ti-nps-submit-btn',
                getNpsRoute:  function () {
                    return (window.npsRouteTemplate || '').replace('__id__', state.formEntryId);
                },
            });
        }
    }

    // ── Server-side errors (step 4) ───────────────────────────────────────────

    function showServerErrors(errors) {
        // Inline per-field errors
        var fieldMap = {
            'first_name': 'ti-first-name',
            'last_name':  'ti-last-name',
            'email':      'ti-email',
            'phone':      'ti-phone',
        };

        var errorList = document.getElementById('ti-error-list');
        errorList.innerHTML = '';

        Object.entries(errors).forEach(function (_ref) {
            var field = _ref[0]; var messages = _ref[1];
            var msg = messages[0];

            // Inline if we have a mapped input
            if (fieldMap[field]) {
                var inputEl = document.getElementById(fieldMap[field]);
                if (inputEl) setFieldError(inputEl, msg);
            }

            // Always add to summary list
            var div = document.createElement('div');
            div.textContent = msg;
            errorList.appendChild(div);
        });

        show(document.getElementById('ti-form-errors'));
    }

    function resetSubmitBtn() {
        submitBtn.disabled = false;
        submitBtn.querySelector('.btn-label').textContent = 'Continue';
        submitBtn.querySelector('.btn-icon')?.classList.remove('d-none');
    }

    // ── Edit Step ─────────────────────────────────────────────────────────────

    document.addEventListener('click', function (e) {
        var editEl = e.target.closest('.ti-edit-step');
        if (!editEl) return;

        var step = parseInt(editEl.dataset.step);
        hide(step2El); hide(step3El); hide(step4El); hide(successEl);

        if (step === 1) {
            show(step1El);
            hide(step1bEl);     // wapas step 1a pe — 1b hide
            // step1El ka intro content restore karo
            var introContent = step1El.querySelector('.py-4');
            if (introContent) show(introContent);
            var continue1aRow = continue1a.closest('.row');
            if (continue1aRow) show(continue1aRow);
            setHeaderActive(header1);
            header1.querySelector('.wizardstep-indicator').textContent = '1 / 4';
        } else if (step === 2) {
            show(step2El);
            setHeaderActive(header2);
            header2.querySelector('.wizardstep-indicator').textContent = '2 / 4';
        } else if (step === 3) {
            show(step3El);
            setHeaderActive(header3);
            header3.querySelector('.wizardstep-indicator').textContent = '3 / 4';
        }
        scrollOffcanvasTop();
    });

    // ── Start Over ────────────────────────────────────────────────────────────

    startOverBtn.addEventListener('click', function () { resetForm(); });

    function resetForm() {
        state.inputMethod    = 'ymm';
        state.formEntryId    = null;
        state.uploadedPhotos = [];
        state.pendingFiles   = [];
        state.keys           = null;
        state.ownership      = null;
        state.yesNoValues    = {};
        state.vinDecoded     = null;
        state.vinDecodedTitle = null;

        yearSel.value = yearSel.options[0].value;
        makeSel.value = '';
        modelSel.innerHTML = '<option value="">[Select Model]</option>';
        modelSel.disabled = true;
        document.getElementById('ti-trim').value = '';
        document.getElementById('ti-body').value = '';
        vinInput.value = '';

        document.getElementById('ti-engine').value         = '';    // ← ADD
        document.getElementById('ti-drivetrain').value     = '';    // ← ADD
        document.getElementById('ti-tire-condition').value = '';    // ← ADD

        document.getElementById('ti-mileage').value        = '';
        document.getElementById('ti-postal-code').value    = '';
        document.getElementById('ti-exterior-color').value = '';
        document.getElementById('ti-interior-color').value = '';
        keysHidden.value     = '';
        ownershipHidden.value = '';

        document.querySelectorAll('.ti-keys-btn, .ti-ownership-btn, .ti-yesno-btn').forEach(function (b) {
            b.classList.remove('active');
        });
        document.querySelectorAll('input[name="condition"]').forEach(function (r) { r.checked = false; });
        document.querySelectorAll('.ti-damage-cb').forEach(function (c) { c.checked = false; });
        ['ti-clean-title','ti-run-drive','ti-accident','ti-warning-lights','ti-smoked-in'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });

        document.getElementById('ti-first-name').value = '';
        document.getElementById('ti-last-name').value  = '';
        document.getElementById('ti-email').value      = '';
        document.getElementById('ti-phone').value      = '';
        document.getElementById('ti-comment').value    = '';

        photoGrid.innerHTML = '';
        hide(photoPreview);
        hide(uploadStatus);

        // Clear all inline errors
        clearVinError();
        clearAllStep1bErrors();
        clearAllStep2Errors();
        offcanvasEl.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
        offcanvasEl.querySelectorAll('.invalid-feedback').forEach(function (el) { el.textContent = ''; });
        hide(document.getElementById('ti-form-errors'));

        hide(step1bEl); hide(step2El); hide(step3El); hide(step4El); hide(successEl);
        hide(lienholderRow); hide(balanceRow); hide(trimRow); hide(bodyRow);
        hide(startOverBar);
        show(step1El);

        // step1El ka intro content bhi restore karo
        var introContent = step1El.querySelector('.py-4');
        if (introContent) show(introContent);
        var continue1aRow = continue1a.closest('.row');
        if (continue1aRow) show(continue1aRow);

        tabYmm.classList.add('active');
        tabVin.classList.remove('active');
        show(panelYmm);
        hide(panelVin);

        [header1, header2, header3, header4].forEach(function (h, i) {
            if (!h) return;
            h.querySelector('b').classList.remove('text-primary');
            h.querySelector('b').classList.add('text-muted');
            h.classList.remove('wizardstep-active');
            var ind = h.querySelector('.wizardstep-indicator');
            if (ind) ind.innerHTML = (i + 1) + ' / 4';
        });
        header1.querySelector('b').classList.remove('text-muted');
        header1.querySelector('b').classList.add('text-primary');
        header1.classList.add('wizardstep-active');

        continue1a.disabled = true;
        resetSubmitBtn();
        scrollOffcanvasTop();
    }

    // ── Get Approved CTA ──────────────────────────────────────────────────────

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.ti-get-approved-btn')) return;
        var tiOffcanvas = document.getElementById('getTrade');
        if (tiOffcanvas) bootstrap.Offcanvas.getInstance(tiOffcanvas)?.hide();
        var getApproved = document.getElementById('getApproved');
        if (getApproved) new bootstrap.Offcanvas(getApproved).show();
    });

    // ── NPS Stars ─────────────────────────────────────────────────────────────

    var stars      = document.querySelectorAll('#ti-star-rating .star');
    var npsOptions = document.getElementById('tiNpsOptions');

    stars.forEach(function (star, idx) {
        star.addEventListener('click', function () {
            stars.forEach(function (s, i) { s.classList.toggle('filled', i <= idx); });
            if (npsOptions) npsOptions.classList.remove('hidden');
        });
    });

    // ── Reset on offcanvas close ──────────────────────────────────────────────

    offcanvasEl.addEventListener('hidden.bs.offcanvas', function () { resetForm(); });

    // ── Initial state ─────────────────────────────────────────────────────────

    hide(step1bEl); hide(step2El); hide(step3El); hide(step4El); hide(successEl);
    hide(lienholderRow); hide(balanceRow); hide(trimRow); hide(bodyRow);
    hide(panelVin); hide(startOverBar);
    continue1a.disabled = true;

})();
