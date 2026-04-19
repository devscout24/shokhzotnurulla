(function () {
    'use strict';

    /* ══════════════════════════════════
       HELPERS
    ══════════════════════════════════ */
    function qs(sel) { return document.querySelector(sel); }

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
                'Content-Type':     'application/json',
            },
        }, options || {}));
    }

    function toast(type, msg) {
        if (window.toastr) window.toastr[type](msg);
    }

    /* ══════════════════════════════════
       MODAL ELEMENTS
    ══════════════════════════════════ */
    var overlay        = qs('#addPricingSpecialModal');
    var modalTitle     = qs('#psModalTitle');
    var form           = qs('#addPricingSpecialForm');
    var saveBtn        = qs('#psSaveBtn');
    var specialId      = qs('#psSpecialId');

    var fTitle         = qs('#psTitle');
    var fType          = qs('#psType');
    var fButtonText    = qs('#psButtonText');
    var fStackable     = qs('#psStackable');
    var fPriority      = qs('#psPriority');
    var fDiscountType  = qs('#psDiscountType');
    var fAmount        = qs('#psAmount');
    var fDiscountLabel = qs('#psDiscountLabel');
    var fFinanceRate   = qs('#psFinanceRate');
    var fFinanceTerm   = qs('#psFinanceTerm');

    var fCondition     = qs('#psCondition');
    var fIsCertified   = qs('#psIsCertified');
    var fModelNumber   = qs('#psModelNumber');
    var fYear          = qs('#psYear');
    var fMakeId        = qs('#psMakeId');
    var fMakeModelId   = qs('#psMakeModelId');
    var fTrim          = qs('#psTrim');
    var fBodyStyle     = qs('#psBodyStyle');
    var fExteriorColor = qs('#psExteriorColorId');
    var fStockNumber   = qs('#psStockNumber');
    var fTag           = qs('#psTag');
    var fMinDays       = qs('#psMinDays');
    var fMaxDays       = qs('#psMaxDays');

    var fSendEmail     = qs('#psSendEmail');
    var fHidePrice     = qs('#psHidePrice');
    var fStartsAt      = qs('#psStartsAt');
    var fEndsAt        = qs('#psEndsAt');
    var fNotes         = qs('#psNotes');
    var fDisclaimer    = qs('#psDisclaimer');

    var wButtonText    = qs('#psButtonTextWrap');
    var wStackable     = qs('#psStackableWrap');
    var wAmount        = qs('#psAmountWrap');
    var wDiscountLabel = qs('#psDiscountLabelWrap');
    var wFinance       = qs('#psFinanceWrap');
    var wSendEmailRow  = qs('#psSendEmailRow');

    var amountSign     = qs('#psAmountSign');
    var amountSuffix   = qs('#psAmountSuffix');
    var amountLabel    = qs('#psAmountLabel');

    var matchBox       = qs('#psMatchBox');
    var matchCount     = qs('#psMatchCount');

    var currentUpdateUrl = '';
    var matchDebounce    = null;

    /* ══════════════════════════════════
       OPEN / CLOSE MODAL
    ══════════════════════════════════ */
    function openModal() {
        if (overlay) overlay.classList.add('active');
    }

    function closeModal() {
        if (overlay) overlay.classList.remove('active');
    }

    var btnAdd = qs('#btnAddPricingSpecial');
    if (btnAdd) {
        btnAdd.addEventListener('click', function () {
            resetModal();
            openModal();
        });
    }

    var btnClose  = qs('#btnClosePsModal');
    var btnCancel = qs('#btnCancelPsModal');
    if (btnClose)  btnClose.addEventListener('click', closeModal);
    if (btnCancel) btnCancel.addEventListener('click', closeModal);

    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    /* ══════════════════════════════════
       DYNAMIC: TYPE
    ══════════════════════════════════ */
    function syncTypeFields() {
        var val = fType ? fType.value : '';

        if (val === 'formfill') {
            if (wButtonText)    wButtonText.style.display    = '';
            if (wStackable)     wStackable.style.display     = 'none';
            if (wDiscountLabel) wDiscountLabel.style.display = 'none';
            if (wSendEmailRow)  wSendEmailRow.style.display  = '';
        } else if (val === 'override') {
            if (wButtonText)    wButtonText.style.display    = 'none';
            if (wStackable)     wStackable.style.display     = '';
            if (wDiscountLabel) wDiscountLabel.style.display = '';
            if (wSendEmailRow)  wSendEmailRow.style.display  = 'none';
        } else {
            if (wButtonText)    wButtonText.style.display    = 'none';
            if (wStackable)     wStackable.style.display     = 'none';
            if (wDiscountLabel) wDiscountLabel.style.display = 'none';
            if (wSendEmailRow)  wSendEmailRow.style.display  = '';
        }
    }

    if (fType) fType.addEventListener('change', syncTypeFields);

    /* ══════════════════════════════════
       DYNAMIC: DISCOUNT TYPE
    ══════════════════════════════════ */
    var discountConfig = {
        fixed:          { label: 'Fixed Amount',          sign: '$', suffix: false },
        percentage:     { label: 'Percentage',            sign: '',  suffix: true  },
        dollars:        { label: 'Dollars off',           sign: '$', suffix: false },
        offsetdollar:   { label: 'Offset dollars off',    sign: '$', suffix: false },
        offsetincrease: { label: 'Offset price increase', sign: '$', suffix: false },
        increase:       { label: 'Price increase',        sign: '$', suffix: false },
    };

    function syncDiscountType() {
        var val = fDiscountType ? fDiscountType.value : '';

        if (val === 'special') {
            if (wAmount)  wAmount.style.display  = 'none';
            if (wFinance) wFinance.style.display = '';
        } else if (val && discountConfig[val]) {
            if (wAmount)  wAmount.style.display  = '';
            if (wFinance) wFinance.style.display = 'none';

            var cfg = discountConfig[val];
            if (amountLabel) amountLabel.textContent = cfg.label;
            if (amountSign) {
                amountSign.textContent   = cfg.sign;
                amountSign.style.display = cfg.sign ? '' : 'none';
            }
            if (amountSuffix) {
                amountSuffix.style.display = cfg.suffix ? '' : 'none';
            }
        } else {
            if (wAmount)  wAmount.style.display  = 'none';
            if (wFinance) wFinance.style.display = 'none';
        }
    }

    if (fDiscountType) fDiscountType.addEventListener('change', syncDiscountType);

    /* ══════════════════════════════════
       MAKE → MODEL CASCADE
    ══════════════════════════════════ */
    if (fMakeId) {
        fMakeId.addEventListener('change', function () {
            var makeId = this.value;

            if (fMakeModelId) {
                fMakeModelId.innerHTML = '<option value="">Select...</option>';
                fMakeModelId.disabled  = true;
            }

            if (! makeId || ! window.psRoutes) return;

            fetch(window.psRoutes.models + '?make_id=' + makeId, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            })
            .then(function (res) { return res.json(); })
            .then(function (models) {
                if (! fMakeModelId) return;
                fMakeModelId.innerHTML = '<option value="">Select...</option>';
                models.forEach(function (m) {
                    var opt = document.createElement('option');
                    opt.value       = m.id;
                    opt.textContent = m.name;
                    fMakeModelId.appendChild(opt);
                });
                fMakeModelId.disabled = false;
            })
            .catch(function () {
                if (fMakeModelId) fMakeModelId.disabled = false;
            });

            fetchMatchCount();
        });
    }

    /* ══════════════════════════════════
       MATCH COUNT
    ══════════════════════════════════ */
    function fetchMatchCount() {
        clearTimeout(matchDebounce);
        matchDebounce = setTimeout(function () {
            if (! window.psRoutes || ! window.psRoutes.matchCount) return;

            var params = new URLSearchParams();
            var vals = {
                condition:         fCondition     ? fCondition.value         : '',
                is_certified:      fIsCertified   ? fIsCertified.value       : '',
                year:              fYear          ? fYear.value              : '',
                make_id:           fMakeId        ? fMakeId.value            : '',
                make_model_id:     fMakeModelId   ? fMakeModelId.value       : '',
                model_number:      fModelNumber   ? fModelNumber.value.trim(): '',
                trim:              fTrim          ? fTrim.value.trim()       : '',
                exterior_color_id: fExteriorColor ? fExteriorColor.value     : '',
                stock_number:      fStockNumber   ? fStockNumber.value       : '',
                tag:               fTag           ? fTag.value.trim()        : '',
                min_days:          fMinDays       ? fMinDays.value           : '',
                max_days:          fMaxDays       ? fMaxDays.value           : '',
            };

            Object.keys(vals).forEach(function (key) {
                if (vals[key] !== '') params.append(key, vals[key]);
            });

            fetch(window.psRoutes.matchCount + '?' + params.toString(), {
                headers: {
                    'X-CSRF-TOKEN':     getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                },
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (matchCount) matchCount.textContent = data.count;
                if (matchBox)   matchBox.innerHTML = '<b>' + data.count + '</b> vehicles match your criteria';
            })
            .catch(function () {});
        }, 400);
    }

    document.querySelectorAll('.ps-criteria').forEach(function (el) {
        var evt = (el.tagName === 'SELECT') ? 'change' : 'input';
        el.addEventListener(evt, fetchMatchCount);
    });

    /* ══════════════════════════════════
       VALIDATION
    ══════════════════════════════════ */
    function clearErrors() {
        document.querySelectorAll('.ps-invalid-feedback').forEach(function (el) {
            el.textContent = '';
        });
        document.querySelectorAll('.ps-form-control.is-invalid, .ps-form-select.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
    }

    function showErrors(errors) {
        var map = {
            title:         { field: fTitle,        err: qs('#errPsTitle')        },
            discount_type: { field: fDiscountType, err: qs('#errPsDiscountType') },
        };

        Object.keys(errors).forEach(function (key) {
            if (map[key]) {
                if (map[key].field) map[key].field.classList.add('is-invalid');
                if (map[key].err)   map[key].err.textContent = errors[key][0];
            } else {
                toast('error', errors[key][0]);
            }
        });
    }

    /* ══════════════════════════════════
       RESET MODAL
    ══════════════════════════════════ */
    function resetModal() {
        if (form) form.reset();
        clearErrors();

        if (specialId)   specialId.value = '';
        currentUpdateUrl = '';

        if (modalTitle) modalTitle.textContent = 'Add Pricing Special';
        setBtnReady('Save');

        syncTypeFields();
        syncDiscountType();

        if (fMakeModelId) {
            fMakeModelId.innerHTML = '<option value="">Select...</option>';
            fMakeModelId.disabled  = true;
        }

        if (matchBox)   matchBox.innerHTML      = '— vehicles match your criteria';
        if (matchCount) matchCount.textContent  = '—';

        fetchMatchCount();
    }

    /* ══════════════════════════════════
       BUTTON STATES
    ══════════════════════════════════ */
    function setBtnLoading() {
        if (! saveBtn) return;
        saveBtn.disabled  = true;
        saveBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1" ' +
            'style="width:12px;height:12px;border-width:2px;"></span> Saving...';
    }

    function setBtnReady(label) {
        if (! saveBtn) return;
        saveBtn.disabled  = false;
        saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> ' + (label || 'Save');
    }

    /* ══════════════════════════════════
       FORM SUBMIT
    ══════════════════════════════════ */
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            clearErrors();

            var isEdit = specialId && specialId.value !== '';
            var url    = isEdit ? currentUpdateUrl : (window.psRoutes ? window.psRoutes.store : '');
            var method = isEdit ? 'PATCH' : 'POST';

            if (! url) { toast('error', 'Route not configured.'); return; }

            var payload = {
                title:             fTitle         ? fTitle.value.trim()                                   : '',
                type:              fType          ? (fType.value || null)                                 : null,
                button_text:       fButtonText    ? (fButtonText.value.trim() || null)                    : null,
                discount_label:    fDiscountLabel ? (fDiscountLabel.value.trim() || null)                 : null,
                stackable:         fStackable     ? (fStackable.value === '1' ? 1 : 0)                   : 0,
                priority:          fPriority      ? (parseInt(fPriority.value) || 0)                      : 0,
                discount_type:     fDiscountType  ? (fDiscountType.value || null)                         : null,
                amount:            fAmount && fAmount.value.trim() !== ''
                                       ? parseFloat(fAmount.value.replace(/,/g, '')) : null,
                finance_rate:      fFinanceRate && fFinanceRate.value.trim() !== ''
                                       ? parseFloat(fFinanceRate.value) : null,
                finance_term:      fFinanceTerm && fFinanceTerm.value !== ''
                                       ? parseInt(fFinanceTerm.value) : null,
                condition:         fCondition     ? (fCondition.value || null)                            : null,
                is_certified:      fIsCertified   ? (fIsCertified.value !== '' ? fIsCertified.value : null) : null,
                model_number:      fModelNumber   ? (fModelNumber.value.trim() || null)                   : null,
                year:              fYear          ? (fYear.value ? parseInt(fYear.value) : null)          : null,
                make_id:           fMakeId        ? (fMakeId.value ? parseInt(fMakeId.value) : null)      : null,
                make_model_id:     fMakeModelId   ? (fMakeModelId.value ? parseInt(fMakeModelId.value) : null) : null,
                trim:              fTrim          ? (fTrim.value.trim() || null)                          : null,
                body_style:        fBodyStyle     ? (fBodyStyle.value.trim() || null)                     : null,
                exterior_color_id: fExteriorColor ? (fExteriorColor.value ? parseInt(fExteriorColor.value) : null) : null,
                stock_number:      fStockNumber   ? (fStockNumber.value || null)                          : null,
                tag:               fTag           ? (fTag.value.trim() || null)                           : null,
                min_days:          fMinDays       ? (fMinDays.value !== '' ? parseInt(fMinDays.value) : null) : null,
                max_days:          fMaxDays       ? (fMaxDays.value !== '' ? parseInt(fMaxDays.value) : null) : null,
                send_email:        fSendEmail     ? (fSendEmail.value === '1' ? 1 : 0)                    : 0,
                hide_price:        fHidePrice     ? (fHidePrice.checked ? 1 : 0)                         : 0,
                starts_at:         fStartsAt      ? (fStartsAt.value || null)                             : null,
                ends_at:           fEndsAt        ? (fEndsAt.value || null)                               : null,
                notes:             fNotes         ? (fNotes.value.trim() || null)                         : null,
                disclaimer:        fDisclaimer    ? (fDisclaimer.value.trim() || null)                    : null,
            };

            setBtnLoading();

            fetchJson(url, { method: method, body: JSON.stringify(payload) })
                .then(function (res) {
                    return res.json().then(function (d) {
                        return { ok: res.ok, status: res.status, data: d };
                    });
                })
                .then(function (result) {
                    if (result.ok && result.data.success) {
                        toast('success', result.data.message);
                        closeModal();
                        setTimeout(function () { window.location.reload(); }, 700);
                    } else if (result.status === 422 && result.data.errors) {
                        showErrors(result.data.errors);
                        setBtnReady(isEdit ? 'Update' : 'Save');
                    } else {
                        toast('error', result.data.message || 'Something went wrong.');
                        setBtnReady(isEdit ? 'Update' : 'Save');
                    }
                })
                .catch(function () {
                    toast('error', 'Network error. Please try again.');
                    setBtnReady(isEdit ? 'Update' : 'Save');
                });
        });
    }

    /* ══════════════════════════════════
       EDIT BUTTON
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit-special');
        if (! btn) return;

        e.preventDefault();
        resetModal();

        specialId.value  = btn.dataset.id        || '';
        currentUpdateUrl = btn.dataset.updateUrl  || '';

        if (modalTitle) modalTitle.textContent = 'Edit Pricing Special';
        setBtnReady('Update');

        if (fTitle)         fTitle.value         = btn.dataset.title          || '';
        if (fType)          fType.value          = btn.dataset.type           || '';
        if (fButtonText)    fButtonText.value    = btn.dataset.buttonText     || '';
        if (fStackable)     fStackable.value     = btn.dataset.stackable      || '';
        if (fPriority)      fPriority.value      = btn.dataset.priority       || '0';
        if (fDiscountType)  fDiscountType.value  = btn.dataset.discountType   || '';
        if (fAmount)        fAmount.value        = btn.dataset.amount         || '';
        if (fDiscountLabel) fDiscountLabel.value = btn.dataset.discountLabel  || '';
        if (fFinanceRate)   fFinanceRate.value   = btn.dataset.financeRate    || '';
        if (fFinanceTerm)   fFinanceTerm.value   = btn.dataset.financeTerm    || '';
        if (fCondition)     fCondition.value     = btn.dataset.condition      || '';
        if (fIsCertified)   fIsCertified.value   = btn.dataset.isCertified    || '';
        if (fModelNumber)   fModelNumber.value   = btn.dataset.modelNumber    || '';
        if (fYear)          fYear.value          = btn.dataset.year           || '';
        if (fMakeId)        fMakeId.value        = btn.dataset.makeId         || '';
        if (fTrim)          fTrim.value          = btn.dataset.trim           || '';
        if (fBodyStyle)     fBodyStyle.value     = btn.dataset.bodyStyle      || '';
        if (fExteriorColor) fExteriorColor.value = btn.dataset.exteriorColorId || '';
        if (fStockNumber)   fStockNumber.value   = btn.dataset.stockNumber    || '';
        if (fTag)           fTag.value           = btn.dataset.tag            || '';
        if (fMinDays)       fMinDays.value       = btn.dataset.minDays        || '';
        if (fMaxDays)       fMaxDays.value       = btn.dataset.maxDays        || '';
        if (fSendEmail)     fSendEmail.value     = btn.dataset.sendEmail      || '0';
        if (fHidePrice)     fHidePrice.checked   = btn.dataset.hidePrice      === '1';
        if (fStartsAt)      fStartsAt.value      = btn.dataset.startsAt       || '';
        if (fEndsAt)        fEndsAt.value        = btn.dataset.endsAt         || '';
        if (fNotes)         fNotes.value         = btn.dataset.notes          || '';
        if (fDisclaimer)    fDisclaimer.value    = btn.dataset.disclaimer     || '';

        var makeId      = btn.dataset.makeId;
        var makeModelId = btn.dataset.makeModelId;

        if (makeId && window.psRoutes) {
            fetch(window.psRoutes.models + '?make_id=' + makeId, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            })
            .then(function (res) { return res.json(); })
            .then(function (models) {
                if (! fMakeModelId) return;
                fMakeModelId.innerHTML = '<option value="">Select...</option>';
                models.forEach(function (m) {
                    var opt = document.createElement('option');
                    opt.value       = m.id;
                    opt.textContent = m.name;
                    if (m.id == makeModelId) opt.selected = true;
                    fMakeModelId.appendChild(opt);
                });
                fMakeModelId.disabled = false;
            });
        }

        syncTypeFields();
        syncDiscountType();
        fetchMatchCount();

        openModal();
    });

    /* ══════════════════════════════════
       DELETE BUTTON
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete-special');
        if (! btn) return;

        if (! confirm('Delete "' + btn.dataset.title + '"? This cannot be undone.')) return;

        fetchJson(btn.dataset.deleteUrl, { method: 'DELETE' })
            .then(function (res) {
                return res.json().then(function (d) { return { ok: res.ok, data: d }; });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toast('success', result.data.message);
                    var row = document.getElementById('ps-row-' + btn.dataset.id);
                    if (row) row.remove();
                } else {
                    toast('error', result.data.message || 'Something went wrong.');
                }
            })
            .catch(function () { toast('error', 'Network error.'); });
    });

    /* ══════════════════════════════════
       PAUSE / ACTIVATE TOGGLE
    ══════════════════════════════════ */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-toggle-special');
        if (! btn) return;

        var id        = btn.dataset.id;
        var isEnabled = btn.dataset.enabled === '1';
        var toggleUrl = btn.dataset.toggleUrl;
        var specType  = btn.dataset.type;
        var row       = document.getElementById('ps-row-' + id);

        var confirmMsg = isEnabled
            ? 'Pause "' + (row ? row.querySelector('.ps-td-title').firstChild.textContent.trim() : '') + '"? It will no longer apply to vehicles.'
            : 'Activate this pricing special?';

        if (! window.confirm(confirmMsg)) return;

        btn.disabled = true;

        fetchJson(toggleUrl, { method: 'PATCH' })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                var nowEnabled = data.is_enabled;

                // Button state
                btn.dataset.enabled = nowEnabled ? '1' : '0';
                btn.title           = nowEnabled ? 'Pause' : 'Activate';
                btn.className       = btn.className
                    .replace(/\bis-active\b/g, '')
                    .replace(/\bis-paused\b/g, '')
                    .trim();
                btn.classList.add(nowEnabled ? 'is-active' : 'is-paused');
                btn.querySelector('i').className = nowEnabled
                    ? 'bi bi-pause-fill'
                    : 'bi bi-play-fill';

                // Row paused class
                if (row) {
                    row.classList.toggle('ps-row-paused', ! nowEnabled);

                    // Draft badge toggle in title cell
                    var titleTd = row.querySelector('.ps-td-title');
                    if (titleTd) {
                        var existingDraft = titleTd.querySelector('.ps-badge-draft');
                        if (! nowEnabled && ! existingDraft) {
                            var badge = document.createElement('span');
                            badge.className   = 'ps-badge ps-badge-draft ms-1';
                            badge.textContent = 'Draft';
                            titleTd.appendChild(badge);
                        } else if (nowEnabled && existingDraft) {
                            existingDraft.remove();
                        }
                    }
                }

                toast('success', nowEnabled ? 'Pricing special activated.' : 'Pricing special paused.');
                btn.disabled = false;
            })
            .catch(function () {
                btn.disabled = false;
                toast('error', 'Something went wrong.');
            });
    });

    /* ══════════════════════════════════
       SIDEBAR FILTERS
    ══════════════════════════════════ */
    var sidebarForm = qs('#psSidebarFilterForm');
    if (sidebarForm) {
        sidebarForm.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () { sidebarForm.submit(); });
        });
    }

    /* ══════════════════════════════════
       INIT
    ══════════════════════════════════ */
    syncTypeFields();
    syncDiscountType();

    /* ── Duplicate ── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-duplicate-special');
        if (! btn) return;

        if (! confirm('Duplicate this pricing special?')) return;

        btn.disabled = true;

        fetchJson(btn.dataset.duplicateUrl, { method: 'POST' })
            .then(function (res) {
                return res.json().then(function (d) { return { ok: res.ok, data: d }; });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toast('success', result.data.message);
                    setTimeout(function () { window.location.reload(); }, 700);
                } else {
                    toast('error', result.data.message || 'Something went wrong.');
                    btn.disabled = false;
                }
            })
            .catch(function () {
                toast('error', 'Network error.');
                btn.disabled = false;
            });
    });

})();