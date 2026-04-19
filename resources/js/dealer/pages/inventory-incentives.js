(function () {
    'use strict';

    /* ── Helpers ──────────────────────────────────── */
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

    /* ── Modal helpers ────────────────────────────── */
    function getModalInstance() {
        var el = document.getElementById('incentiveModal');
        if (!el) return null;
        return bootstrap.Modal.getOrCreateInstance(el);
    }

    function showModal() {
        var trigger = document.getElementById('incentiveModalTrigger');
        if (trigger) trigger.click();
    }

    function hideModal() {
        var el = document.getElementById('incentiveModal');
        if (!el || !window.bootstrap) return;
        window.bootstrap.Modal.getOrCreateInstance(el).hide();
    }

    /* ── Field refs ───────────────────────────────── */
    var form          = qs('#incentiveForm');
    var submitBtn     = qs('#incSubmitBtn');
    var modalTitle    = qs('#incentiveModalLabel');
    var incentiveId   = qs('#incentiveId');

    var fTitle        = qs('#incTitle');
    var fType         = qs('#incType');
    var fCategory     = qs('#incCategory');
    var fDescription  = qs('#incDescription');
    var fAmount       = qs('#incAmount');
    var fAmountType   = qs('#incAmountType');
    var fProgramCode  = qs('#incProgramCode');
    var fGuaranteed   = qs('#incIsGuaranteed');
    var fFeatured     = qs('#incIsFeatured');
    var fEnabled      = qs('#incIsEnabled');
    var fExpiresAt    = qs('#incExpiresAt');

    var amountSign    = qs('#incAmountSign');
    var amountLabel   = qs('#incAmountLabel');
    var amountTypeLabel = qs('#incAmountTypeLabel');

    var currentUpdateUrl = '';

    /* ── Amount Type → dynamic label + sign ──────── */
    var typeLabels = {
        cash:           { typeLabel: 'Cash Amount',        amtLabel: 'Amount ($)',    sign: '$' },
        finance:        { typeLabel: 'Finance Rate',       amtLabel: 'APR (%)',       sign: '%' },
        ivc_dvc:        { typeLabel: 'IVC / DVC Amount',   amtLabel: 'Amount ($)',    sign: '$' },
        lease:          { typeLabel: 'Monthly Payment',    amtLabel: 'Amount ($)',    sign: '$' },
        percentage_off: { typeLabel: 'Percentage Off',     amtLabel: 'Percentage (%)', sign: '%' },
    };

    function syncAmountUI() {
        var type    = fType        ? fType.value       : '';
        var amtType = fAmountType  ? fAmountType.value : '';

        var sign = '#';
        var aLabel = 'Amount';
        var atLabel = 'Amount Type';

        // Type-based smart defaults
        if (typeLabels[type]) {
            atLabel = typeLabels[type].typeLabel;
            aLabel  = typeLabels[type].amtLabel;
            sign    = typeLabels[type].sign;
        }

        // If user manually picked amount type — override sign
        if (amtType === 'fixed')   sign = '$';
        if (amtType === 'percent') sign = '%';

        if (amountSign)      amountSign.textContent      = sign;
        if (amountLabel)     amountLabel.textContent      = aLabel;
        if (amountTypeLabel) amountTypeLabel.textContent  = atLabel;

        // Auto-select amount_type based on incentive type if not set
        if (fAmountType && !fAmountType.value && type) {
            if (type === 'finance' || type === 'percentage_off') {
                fAmountType.value = 'percent';
            } else if (type === 'cash' || type === 'ivc_dvc' || type === 'lease') {
                fAmountType.value = 'fixed';
            }
        }
    }

    if (fType)       fType.addEventListener('change',      syncAmountUI);
    if (fAmountType) fAmountType.addEventListener('change', syncAmountUI);

    /* ── Validation helpers ───────────────────────── */
    function clearErrors() {
        document.querySelectorAll('.invalid-feedback').forEach(function (el) {
            el.textContent = '';
        });
        document.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
    }

    function showErrors(errors) {
        var map = {
            title:       { field: fTitle,       err: qs('#errTitle')    },
            type:        { field: fType,         err: qs('#errType')     },
            category:    { field: fCategory,     err: qs('#errCategory') },
            amount:      { field: fAmount,       err: qs('#errAmount')   },
            description: { field: fDescription,  err: qs('#errDesc')     },
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

    /* ── Button state helpers ─────────────────────── */
    function setBtnLoading() {
        if (!submitBtn) return;
        submitBtn.disabled  = true;
        submitBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1" ' +
            'style="width:12px;height:12px;border-width:2px;"></span> Saving...';
    }

    function setBtnReady(label) {
        if (!submitBtn) return;
        submitBtn.disabled  = false;
        submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> ' + (label || 'Save Incentive');
    }

    /* ── Reset modal ─────────────────────────────── */
    function resetModal() {
        if (form) form.reset();
        clearErrors();
        incentiveId.value = '';
        currentUpdateUrl  = '';
        if (fEnabled)    fEnabled.checked    = true;
        if (fGuaranteed) fGuaranteed.checked = false;
        if (fFeatured)   fFeatured.checked   = false;
        if (modalTitle)  modalTitle.textContent = 'Add Incentive';
        setBtnReady('Save Incentive');
        syncAmountUI();
    }

    /* ── Add button ──────────────────────────────── */
    var btnAdd = qs('#btnAddIncentive');
    if (btnAdd) {
        btnAdd.addEventListener('click', function () {
            resetModal();
            showModal();  // ← showModal() explicitly
        });
    }

    /* ── Edit button ─────────────────────────────── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit-incentive');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        resetModal();

        incentiveId.value    = btn.dataset.id          || '';
        currentUpdateUrl     = btn.dataset.updateUrl   || '';

        if (fTitle)       fTitle.value        = btn.dataset.title       || '';
        if (fType)        fType.value         = btn.dataset.type        || '';
        if (fCategory)    fCategory.value     = btn.dataset.category    || '';
        if (fDescription) fDescription.value  = btn.dataset.description || '';
        if (fAmount)      fAmount.value       = btn.dataset.amount      || '';
        if (fAmountType)  fAmountType.value   = btn.dataset.amountType  || '';
        if (fProgramCode) fProgramCode.value  = btn.dataset.programCode || '';
        if (fGuaranteed)  fGuaranteed.checked  = btn.dataset.isGuaranteed === '1';
        if (fFeatured)    fFeatured.checked    = btn.dataset.isFeatured   === '1';
        if (fEnabled)     fEnabled.checked     = btn.dataset.isEnabled    === '1';
        if (fExpiresAt)   fExpiresAt.value     = btn.dataset.expiresAt   || '';

        if (modalTitle) modalTitle.textContent = 'Edit Incentive';
        setBtnReady('Update Incentive');

        // Sync amount UI after values set
        syncAmountUI();

        // Show modal
        showModal();
    });

    /* ── Form submit ─────────────────────────────── */
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            clearErrors();

            var isEdit = incentiveId && incentiveId.value !== '';
            var url    = isEdit
                ? currentUpdateUrl
                : (window.incentiveRoutes ? window.incentiveRoutes.store : '');
            var method = isEdit ? 'PATCH' : 'POST';

            if (!url) { toast('error', 'Route not set.'); return; }

            var payload = {
                title:         fTitle       ? fTitle.value.trim()                 : '',
                type:          fType        ? fType.value                          : '',
                category:      fCategory    ? fCategory.value                      : '',
                description:   fDescription ? (fDescription.value.trim() || null) : null,
                amount:        fAmount && fAmount.value !== ''
                                   ? parseFloat(fAmount.value) : null,
                amount_type:   fAmountType  ? (fAmountType.value  || null) : null,
                program_code:  fProgramCode ? (fProgramCode.value.trim() || null) : null,
                is_guaranteed: fGuaranteed && fGuaranteed.checked ? 1 : 0,
                is_featured:   fFeatured   && fFeatured.checked   ? 1 : 0,
                is_enabled:    fEnabled    && fEnabled.checked    ? 1 : 0,
                expires_at:    fExpiresAt  ? (fExpiresAt.value || null) : null,
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
                        hideModal();
                        toast('success', result.data.message);
                        setTimeout(function () { window.location.reload(); }, 800);

                    } else if (result.status === 422 && result.data.errors) {
                        showErrors(result.data.errors);
                        setBtnReady(isEdit ? 'Update Incentive' : 'Save Incentive');

                    } else {
                        toast('error', result.data.message || 'Something went wrong.');
                        setBtnReady(isEdit ? 'Update Incentive' : 'Save Incentive');
                    }
                })
                .catch(function (err) {
                    console.error('Incentive fetch error:', err);
                    toast('error', 'Network error. Please try again.');
                    setBtnReady(isEdit ? 'Update Incentive' : 'Save Incentive');
                });
        });
    }

    /* ── Delete ───────────────────────────────────── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete-incentive');
        if (!btn) return;

        if (!confirm('Delete "' + btn.dataset.title + '"? This cannot be undone.')) return;

        var deleteUrl = btn.dataset.deleteUrl;
        var rowId     = 'incentive-row-' + btn.dataset.id;

        fetchJson(deleteUrl, { method: 'DELETE' })
            .then(function (res) {
                return res.json().then(function (d) { return { ok: res.ok, data: d }; });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    toast('success', result.data.message);
                    var row = document.getElementById(rowId);
                    if (row) row.remove();
                } else {
                    toast('error', result.data.message || 'Something went wrong.');
                }
            })
            .catch(function () { toast('error', 'Network error.'); });
    });

    /* ── Auto-submit filters ──────────────────────── */
    var filterForm = qs('#incentivesFilterForm');
    if (filterForm) {
        filterForm.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () { filterForm.submit(); });
        });

        var searchInput = filterForm.querySelector('input[name="search"]');
        if (searchInput) {
            var debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () { filterForm.submit(); }, 500);
            });
        }
    }

    /* ── Init ─────────────────────────────────────── */
    syncAmountUI();

})();