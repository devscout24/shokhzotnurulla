/* ══════════════════════════════════════════════════
   INVENTORY MODULE — inventory.js
   Covers: Listing page + VDP page
   Approach: direct execution with null checks
══════════════════════════════════════════════════ */

(function () {
    'use strict';

    /* ══════════════════════════════════════════
       1. FILTER ACCORDION
    ══════════════════════════════════════════ */
    document.querySelectorAll('.inv-filter-group').forEach(function (group) {
        var head = group.querySelector('.inv-filter-head');
        if (!head) return;
        head.addEventListener('click', function () {
            group.classList.toggle('open');
        });
    });

    /* ══════════════════════════════════════════
       2. FILTER APPLY (URL-based)
    ══════════════════════════════════════════ */
    document.querySelectorAll('.inv-filter-cb').forEach(function (el) {
        el.addEventListener('change', applyFilters);
    });

    function applyFilters() {
        var params = new URLSearchParams(window.location.search);
        var knownKeys = [
            'condition', 'make_id', 'year_min', 'year_max',
            'body_type_id', 'exterior_color_id', 'interior_color_id',
            'fuel_type_id', 'transmission_type_id', 'drivetrain_type_id',
            'seating_capacity', 'location',
        ];
        knownKeys.forEach(function (k) { params.delete(k); });
        params.delete('page');

        document.querySelectorAll('.inv-filter-cb').forEach(function (el) {
            var filter = el.dataset.filter;
            if (!filter || !el.value) return;
            if (el.tagName === 'SELECT') {
                if (el.value) params.set(filter, el.value);
            } else if (el.checked) {
                params.append(filter, el.value);
            }
        });

        window.location.href = window.location.pathname + '?' + params.toString();
    }

    /* ══════════════════════════════════════════
       3. DISPLAY: LIST ↔ GRID
    ══════════════════════════════════════════ */
    var listView   = document.getElementById('invListView');
    var gridView   = document.getElementById('invGridView');
    var displaySel = document.getElementById('invDisplay');

    if (displaySel && listView && gridView) {
        var savedDisplay = localStorage.getItem('inv_display') || 'list';
        displaySel.value = savedDisplay;
        applyDisplay(savedDisplay);

        displaySel.addEventListener('change', function () {
            localStorage.setItem('inv_display', this.value);
            applyDisplay(this.value);
        });
    }

    function applyDisplay(val) {
        if (!listView || !gridView) return;
        if (val === 'grid') {
            listView.style.display = 'none';
            gridView.classList.add('active');
        } else {
            listView.style.display = '';
            gridView.classList.remove('active');
        }
    }

    /* ══════════════════════════════════════════
        GRID CARD CLICK
    ══════════════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var card = e.target.closest('.inv-grid-card');
        if (!card || !card.dataset.url) return;
        window.location.href = card.dataset.url;
    });

    /* ══════════════════════════════════════════
       4. COLUMNS DROPDOWN
    ══════════════════════════════════════════ */
    var btnColumns = document.getElementById('btnColumns');
    var colsDrop   = document.getElementById('colsDropdown');
    var colsLabel  = document.getElementById('colsLabel');

    if (btnColumns && colsDrop) {
        btnColumns.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = colsDrop.classList.toggle('open');
            btnColumns.classList.toggle('open', isOpen);
        });

        document.addEventListener('click', function (e) {
            if (!btnColumns.contains(e.target) && !colsDrop.contains(e.target)) {
                colsDrop.classList.remove('open');
                btnColumns.classList.remove('open');
            }
        });

        function updateColumnCount() {
            if (!colsLabel) return;
            var checked = document.querySelectorAll('.col-toggle:checked').length + 1;
            colsLabel.textContent = checked + ' Columns Displayed';
        }

        document.querySelectorAll('.col-toggle').forEach(function (cb) {
            cb.addEventListener('change', function () {
                var col  = cb.dataset.col;
                var show = cb.checked;
                var th   = document.querySelector('th.col-th[data-col="' + col + '"]');
                if (th) th.style.display = show ? '' : 'none';
                document.querySelectorAll('td.col-td[data-col="' + col + '"]').forEach(function (td) {
                    td.style.display = show ? '' : 'none';
                });
                updateColumnCount();
            });
        });
    }

    /* ══════════════════════════════════════════
       5. INLINE PRICE EDIT
    ══════════════════════════════════════════ */
    document.querySelectorAll('.inv-price-edit').forEach(function (icon) {
        icon.addEventListener('click', function (e) {
            e.stopPropagation();
            var td   = icon.closest('td');
            var disp = td.querySelector('.inv-price-display');
            var edit = td.querySelector('.inv-price-editor');
            disp.classList.add('hidden');
            edit.classList.add('active');
            edit.querySelector('.inv-price-input').select();
        });
    });

    document.querySelectorAll('.inv-price-confirm').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var td      = btn.closest('td');
            var disp    = td.querySelector('.inv-price-display');
            var edit    = td.querySelector('.inv-price-editor');
            var input   = edit.querySelector('.inv-price-input');
            var val     = input.value.trim().replace(/[^0-9.]/g, '');
            var url     = btn.dataset.url;

            if (!val || !url) {
                edit.classList.remove('active');
                disp.classList.remove('hidden');
                return;
            }

            // Spinner on confirm btn
            var origHtml  = btn.innerHTML;
            btn.disabled  = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;border-width:2px;"></span>';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':     getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'Content-Type':     'application/json',
                },
                body: JSON.stringify({ _method: 'PATCH', list_price: val }),
            })
            .then(function (res) { return res.json().then(function (d) { return { ok: res.ok, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    // Update display text
                    var formatted = '$' + Number(val).toLocaleString('en-US');
                    disp.firstChild.textContent = formatted + '\u00A0';
                    // Update edit input for next time
                    input.value = val;
                    edit.classList.remove('active');
                    disp.classList.remove('hidden');
                } else {
                    var msg = result.data.message || 'Could not save price.';
                    alert(msg);
                }
                btn.disabled  = false;
                btn.innerHTML = origHtml;
            })
            .catch(function () {
                alert('Network error.');
                btn.disabled  = false;
                btn.innerHTML = origHtml;
            });
        });
    });

    document.querySelectorAll('.inv-price-cancel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var td   = btn.closest('td');
            var disp = td.querySelector('.inv-price-display');
            var edit = td.querySelector('.inv-price-editor');
            edit.classList.remove('active');
            disp.classList.remove('hidden');
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.inv-price-editor.active').forEach(function (ed) {
                ed.classList.remove('active');
                ed.closest('td').querySelector('.inv-price-display').classList.remove('hidden');
            });
        }
    });

    document.querySelectorAll('.inv-price-input').forEach(function (inp) {
        inp.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                inp.closest('.inv-price-editor').querySelector('.inv-price-confirm').click();
            }
        });
    });

    /* ══════════════════════════════════════════
       6. ADD INVENTORY MODAL — STEP 1 / STEP 2
    ══════════════════════════════════════════ */
    var overlay  = document.getElementById('invModalOverlay');
    var step1    = document.getElementById('invStep1');
    var step2    = document.getElementById('invStep2');
    var vinInput = document.getElementById('invVinInput');
    var vinWrap  = document.getElementById('invVinWrap');
    var vinError = document.getElementById('invVinError');
    var btnAdd   = document.getElementById('btnAddUnit');

    if (overlay && step1 && step2 && vinInput) {

        var urlModels    = overlay.dataset.urlModels;
        var urlVinDecode = overlay.dataset.urlVinDecode;

        /* ── helpers ── */
        function showStep1() {
            step1.style.display = '';
            step2.style.display = 'none';
        }

        function showStep2() {
            step1.style.display = 'none';
            step2.style.display = '';
            var body = step2.querySelector('.inv-step2-body');
            if (body) body.scrollTop = 0;
        }

        function openModal() {
            showStep1();
            clearVinError();
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
            setTimeout(function () { vinInput.focus(); }, 120);
        }

        function closeModal() {
            overlay.classList.remove('open');
            document.body.style.overflow = '';
            vinInput.value = '';
            clearVinError();
            showStep1();
            resetStep2();
        }

        function setVinError(msg) {
            vinWrap.classList.add('error');
            vinError.textContent = msg || 'Please enter a VIN number.';
            vinError.classList.add('show');
        }

        function clearVinError() {
            vinWrap.classList.remove('error');
            vinError.textContent = '';
            vinError.classList.remove('show');
        }

        function resetStep2() {
            // ── Visible fields ────────────────────────────────────────────
            var makeSelect  = document.getElementById('invMakeId');
            var modelSelect = document.getElementById('invModelId');
            var trimInput   = document.getElementById('invTrimInput');
            var yearEl      = document.getElementById('invYear');
            var stockEl     = document.getElementById('invStock');
            var mileageEl     = document.getElementById('invMileage');
            var priceEl     = document.getElementById('invListPrice');
            var vinHid      = document.getElementById('invVinHidden');

            if (makeSelect)  makeSelect.value  = '';
            if (modelSelect) {
                modelSelect.innerHTML = '<option value="">Select Make first</option>';
                modelSelect.disabled  = true;
            }
            if (trimInput)  trimInput.value  = '';
            if (yearEl)     yearEl.value     = '';
            if (stockEl)    stockEl.value    = '';
            if (mileageEl)  mileageEl.value  = '';
            if (priceEl)    priceEl.value    = '';
            if (vinHid)     vinHid.value     = '';

            // Body selects
            var bodyTypeSelect  = document.querySelector('#invStep2Form select[name="body_type_id"]');
            var bodyStyleSelect = document.querySelector('#invStep2Form select[name="body_style_id"]');
            if (bodyTypeSelect)  bodyTypeSelect.value  = '';
            if (bodyStyleSelect) bodyStyleSelect.value = '';

            // ── Hidden VIN-decoded fields ─────────────────────────────────
            var hiddenIds = [
                'invFuelTypeId', 'invTransmissionTypeId', 'invDrivetrainTypeId',
                'invDoors', 'invEngine', 'invCylinders', 'invDisplacement',
                'invMaxHorsepower', 'invBlockType', 'invTransmissionStd',
                'invDrivetrainStd', 'invGvwr',
            ];
            hiddenIds.forEach(function (id) {
                var el = document.getElementById(id);
                if (el) el.value = '';
            });
        }

        /* ── open ── */
        if (btnAdd) btnAdd.addEventListener('click', openModal);

        /* ── close ── */
        document.getElementById('invModalClose').addEventListener('click', closeModal);
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
        });

        /* ── Step 1 → Step 2 (VIN decode AJAX) ── */
        vinInput.addEventListener('input', function () { clearVinError(); });
        vinInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') document.getElementById('invBtnStep1Save').click();
        });

        document.getElementById('invBtnStep1Save').addEventListener('click', function () {
            var vin = vinInput.value.trim();

            if (!vin) {
                setVinError('Please enter a VIN number.');
                vinInput.focus();
                return;
            }
            if (vin.length < 17) {
                setVinError('VIN should be 17 characters.');
                vinInput.focus();
                return;
            }
            clearVinError();

            var btn       = document.getElementById('invBtnStep1Save');
            btn.disabled  = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Decoding...';

            fetch(urlVinDecode, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept':       'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ vin: vin }),
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function (result) {
                if (!result.ok) {
                    if (result.status === 422 && result.data.errors && result.data.errors.vin) {
                        setVinError(result.data.errors.vin[0]);
                    } else if (result.data.message) {
                        setVinError(result.data.message);
                    } else {
                        setVinError('Something went wrong. Please try again.');
                    }
                    return;
                }

                document.getElementById('invVinHidden').value = vin;

                var decoded = result.data.data || {};

                // Year
                if (decoded.year) {
                    document.getElementById('invYear').value = decoded.year;
                }

                // Trim
                if (decoded.trim) {
                    var trimInput = document.getElementById('invTrimInput');
                    if (trimInput) trimInput.value = decoded.trim;
                }

                // All other VIN-decoded fields (body, drivetrain, fuel, specs...)
                populateVinDecodeData(decoded);

                // Make → then cascade-load models → preselect model
                if (decoded.make_id) {
                    var makeSelect = document.getElementById('invMakeId');
                    if (makeSelect) {
                        makeSelect.value = decoded.make_id;
                        loadModels(decoded.make_id, decoded.make_model_id || null);
                    }
                }

                // Show warnings if any fields couldn't be matched
                if (result.data.warnings && result.data.warnings.length) {
                    console.warn('VIN decode warnings:', result.data.warnings);
                    // Optional: surface to dealer via a notice in step2
                }

                showStep2();
            })
            .catch(function () {
                setVinError('Network error. Please check your connection.');
            })
            .finally(function () {
                btn.disabled  = false;
                btn.innerHTML = 'Next <i class="bi bi-chevron-right"></i>';
            });
        });

        /* ── Back ── */
        document.getElementById('invBtnBack').addEventListener('click', showStep1);

        /* ── Stock # error clear ── */
        var stockInput = document.getElementById('invStock');
        if (stockInput) {
            stockInput.addEventListener('input', function () {
                this.classList.remove('error');
            });
        }

        /* ── Mileage error clear ── */
        var mileageInput = document.getElementById('invMileage');
        if (mileageInput) {
            mileageInput.addEventListener('input', function () {
                this.classList.remove('error');
            });
        }

        /* ── Cascading: Make → Models ── */
        var makeSelect = document.getElementById('invMakeId');
        if (makeSelect) {
            makeSelect.addEventListener('change', function () {
                var makeId      = this.value;
                var modelSelect = document.getElementById('invModelId');

                modelSelect.innerHTML = '<option value="">Loading...</option>';
                modelSelect.disabled  = true;

                if (!makeId) {
                    modelSelect.innerHTML = '<option value="">Select Make first</option>';
                    return;
                }
                loadModels(makeId, null);
            });
        }

        /* ── Load Models helper ── */
        function loadModels(makeId, preselectModelId) {
            var modelSelect = document.getElementById('invModelId');
            if (!modelSelect) return;

            fetch(urlModels + '?make_id=' + makeId, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
            })
            .then(function (res) { return res.json(); })
            .then(function (models) {
                modelSelect.innerHTML = '<option value="">Select Model</option>';
                models.forEach(function (m) {
                    var opt         = document.createElement('option');
                    opt.value       = m.id;
                    opt.textContent = m.name;
                    if (preselectModelId && m.id == preselectModelId) opt.selected = true;
                    modelSelect.appendChild(opt);
                });
                modelSelect.disabled = false;
            })
            .catch(function () {
                modelSelect.innerHTML = '<option value="">Could not load models</option>';
                modelSelect.disabled  = false;
            });
        }

        /* ── Populate Step 2 form from VIN decode response ── */
        function populateVinDecodeData(data) {
            if (!data) return;

            // ── Core identity (already handled before this call) ──────────
            // year, trim, make_id, make_model_id → handled in .then() below

            // ── Body selects (visible dropdowns) ─────────────────────────
            if (data.body_type_id) {
                var bodyTypeSelect = document.querySelector('#invStep2Form select[name="body_type_id"]');
                if (bodyTypeSelect) bodyTypeSelect.value = data.body_type_id;
            }
            if (data.body_style_id) {
                var bodyStyleSelect = document.querySelector('#invStep2Form select[name="body_style_id"]');
                if (bodyStyleSelect) bodyStyleSelect.value = data.body_style_id;
            }

            // ── Hidden inputs — mechanical FKs ────────────────────────────
            setHiddenField('invFuelTypeId',         data.fuel_type_id);
            setHiddenField('invTransmissionTypeId', data.transmission_type_id);
            setHiddenField('invDrivetrainTypeId',   data.drivetrain_type_id);
            setHiddenField('invDoors',              data.doors);
            setHiddenField('invEngine',             data.engine_string);

            // ── Hidden inputs — spec fields ───────────────────────────────
            setHiddenField('invCylinders',       data.engine_cylinders);
            setHiddenField('invDisplacement',    data.engine_displacement_l);
            setHiddenField('invMaxHorsepower',   data.engine_hp);
            setHiddenField('invBlockType',       data.block_type);
            setHiddenField('invTransmissionStd', data.transmission_standard);
            setHiddenField('invDrivetrainStd',   data.drivetrain_standard);
            setHiddenField('invGvwr',            data.gvwr);
        }

        function setHiddenField(id, value) {
            var el = document.getElementById(id);
            if (el && value !== null && value !== undefined && value !== '') {
                el.value = value;
            }
        }

        /* ── Step 2 Form Submit — saving state ── */
        var step2Form   = document.getElementById('invStep2Form');
        var btnStep2Save = document.getElementById('invBtnStep2Save');

        if (step2Form && btnStep2Save) {
            step2Form.addEventListener('submit', function () {
                btnStep2Save.disabled  = true;
                btnStep2Save.innerHTML = '<span class="spinner-border spinner-border-sm me-1" '
                    + 'style="width:13px;height:13px;border-width:2px;"></span> Saving...';
            });

            // Re-enable on validation errors (browser-level or server 422 redirect back)
            // pageshow fires when browser navigates back to this page (bfcache)
            window.addEventListener('pageshow', function () {
                btnStep2Save.disabled  = false;
                btnStep2Save.innerHTML = '<i class="bi bi-check-lg"></i> Save';
            });
        }

    } /* end modal block */

    /* ══════════════════════════════════════════
       VDP — only runs if vdp elements exist
    ══════════════════════════════════════════ */

    /* ── VDP Tab Switching ── */
    var tabBtns  = document.querySelectorAll('.vdp-tab-btn');
    var tabPanes = document.querySelectorAll('.vdp-tab-pane');

    if (tabBtns.length) {
        var hash = window.location.hash ? window.location.hash.replace('#tab-', '') : '';
        if (hash) activateTab(hash);

        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                activateTab(btn.dataset.tab);
                history.replaceState(null, '', '#tab-' + btn.dataset.tab);
            });
        });

        function activateTab(tabId) {
            tabBtns.forEach(function (b) {
                b.classList.toggle('active', b.dataset.tab === tabId);
            });
            tabPanes.forEach(function (p) {
                p.classList.toggle('active', p.id === 'tab-' + tabId);
            });
        }
    }

    /* ── VDP Left Nav Menu ── */
    var navItems = document.querySelectorAll('.vdp-nav-item');
    if (navItems.length) {
        navItems.forEach(function (item) {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                navItems.forEach(function (i) { i.classList.remove('active'); });
                item.classList.add('active');
            });
        });
    }

    /* ── VDP Tags Input ── */
    var tagsWrap  = document.getElementById('tagsWrap');
    var tagsInput = document.getElementById('tagsInput');

    if (tagsWrap && tagsInput) {
        tagsWrap.addEventListener('click', function () { tagsInput.focus(); });

        function addTag(raw) {
            var tag = raw.trim().replace(/,+$/, '').trim();
            if (!tag) return;

            var existing = Array.from(tagsWrap.querySelectorAll('.vdp-tag-text'))
                .map(function (el) { return el.textContent.toLowerCase(); });
            if (existing.indexOf(tag.toLowerCase()) !== -1) {
                tagsInput.value = '';
                return;
            }

            var pill = document.createElement('span');
            pill.className = 'vdp-tag-pill';
            pill.innerHTML =
                '<span class="vdp-tag-text">' + escapeHtml(tag) + '</span>' +
                '<button type="button" class="vdp-tag-remove" aria-label="Remove tag">' +
                    '<i class="bi bi-x"></i>' +
                '</button>' +
                '<input type="hidden" name="tags[]" value="' + escapeHtml(tag) + '">';

            pill.querySelector('.vdp-tag-remove').addEventListener('click', function () {
                pill.remove();
            });

            tagsWrap.insertBefore(pill, tagsInput);
            tagsInput.value = '';
        }

        tagsInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag(tagsInput.value);
            }
            if (e.key === 'Backspace' && !tagsInput.value) {
                var pills = tagsWrap.querySelectorAll('.vdp-tag-pill');
                if (pills.length) pills[pills.length - 1].remove();
            }
        });

        tagsInput.addEventListener('paste', function (e) {
            e.preventDefault();
            var text = e.clipboardData.getData('text');
            text.split(',').forEach(function (t) { addTag(t); });
        });

        tagsWrap.querySelectorAll('.vdp-tag-remove').forEach(function (btn) {
            btn.addEventListener('click', function () {
                btn.closest('.vdp-tag-pill').remove();
            });
        });
    }

    /* ── VDP Quill Editors ── */
    if (typeof Quill !== 'undefined') {
        var toolbarOptions = [
            ['bold', 'italic', 'underline'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link'],
            ['clean'],
        ];

        document.querySelectorAll('.vdp-quill-editor').forEach(function (el) {
            var fieldId     = el.dataset.field;
            var hiddenInput = document.getElementById('field_' + fieldId);
            if (!hiddenInput) return;

            var initialHtml = el.innerHTML.trim();

            var quill = new Quill(el, {
                theme:   'snow',
                modules: { toolbar: toolbarOptions },
                placeholder: 'Enter text...',
            });

            if (initialHtml) {
                quill.clipboard.dangerouslyPasteHTML(initialHtml);
            }

            var form = el.closest('form');
            if (form) {
                form.addEventListener('submit', function () {
                    hiddenInput.value = quill.root.innerHTML === '<p><br></p>'
                        ? ''
                        : quill.root.innerHTML;
                });
            }
        });
    }

    /* ── VDP Delete Modal ── */
    var btnDelete   = document.getElementById('btnDeleteVehicle');
    var deleteModal = document.getElementById('modalDeleteVehicle');

    if (btnDelete && deleteModal) {
        btnDelete.addEventListener('click', function () {
            bootstrap.Modal.getOrCreateInstance(deleteModal).show();
        });
    }

    /* ── VDP Flash Auto-Dismiss ── */
    var vdpFlash   = document.getElementById('vdpFlash');
    var flashClose = document.getElementById('btnCloseFlash');

    if (vdpFlash) {
        var flashTimer = setTimeout(function () { dismissFlash(vdpFlash); }, 4000);
        if (flashClose) {
            flashClose.addEventListener('click', function () {
                clearTimeout(flashTimer);
                dismissFlash(vdpFlash);
            });
        }
    }

    function dismissFlash(el) {
        el.style.transition = 'opacity 0.3s ease';
        el.style.opacity    = '0';
        setTimeout(function () { el.remove(); }, 300);
    }

    /* ══════════════════════════════════════════
       HELPERS
    ══════════════════════════════════════════ */
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

})();