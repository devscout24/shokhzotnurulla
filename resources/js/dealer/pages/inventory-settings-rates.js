(function () {
    'use strict';

    // ── Config ────────────────────────────────────────────────────────
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const fsContent = document.querySelector('.fs-content');
    const syncUrl   = fsContent?.dataset.syncUrl ?? '';
    const ratesBody = document.getElementById('ratesBody');
    const makes     = window.ratesMakes ?? [];

    // ── State ─────────────────────────────────────────────────────────
    let pendingDeletes = [];
    let unsavedCount   = 0;
    let tempIdCounter  = 0;

    // ── Helpers ───────────────────────────────────────────────────────

    function genTempId() {
        return `new_${++tempIdCounter}_${Date.now()}`;
    }

    function isNewRow(id) {
        return typeof id === 'string' && id.startsWith('new_');
    }

    async function apiFetch(url, method, body = null) {
        const opts = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json',
            },
        };
        if (body) opts.body = JSON.stringify(body);
        const res = await fetch(url, opts);
        if (! res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message ?? `HTTP ${res.status}`);
        }
        return res.json();
    }

    function toast(message, type = 'success') {
        if (typeof toastr !== 'undefined') toastr[type](message);
    }

    // ── Unsaved Banner ────────────────────────────────────────────────

    function updateBanner(delta) {
        unsavedCount += delta;
        unsavedCount = Math.max(0, unsavedCount);

        const banner  = document.getElementById('unsavedBanner');
        const countEl = document.getElementById('unsavedCount');
        const wordEl  = document.getElementById('unsavedWord');

        if (unsavedCount > 0) {
            countEl.textContent  = unsavedCount;
            wordEl.textContent   = unsavedCount === 1 ? 'change' : 'changes';
            banner.style.display = 'flex';
        } else {
            banner.style.display = 'none';
        }
    }

    // ── Build Row HTML (pure JS — no server call) ─────────────────────

    function buildRowHtml(data = {}) {
        const tempId = genTempId();

        const makesOptions = [{ value: '', label: 'Any' }, ...makes.map(m => ({ value: m, label: m }))]
            .map(m => `<option value="${m.value}"${(data.make ?? '') === m.value ? ' selected' : ''}>${m.label}</option>`)
            .join('');

        const conditions = [
            { value: 'any',  label: 'Any'  },
            { value: 'new',  label: 'New'  },
            { value: 'used', label: 'Used' },
            { value: 'cpo',  label: 'CPO'  },
            { value: 'vpo',  label: 'VPO'  },
        ];
        const condOptions = conditions
            .map(c => `<option value="${c.value}"${(data.condition ?? 'any') === c.value ? ' selected' : ''}>${c.label}</option>`)
            .join('');

        const v = (val) => val !== null && val !== undefined ? val : '';

        return `
        <tr class="data-row" data-id="${tempId}">
            <td style="min-width:90px;">
                <select class="fs-inline-select" data-field="make">${makesOptions}</select>
            </td>
            <td style="width:90px;">
                <input type="number" class="fs-inline-input" data-field="min_model_year"
                       value="${v(data.min_model_year)}" min="2000" max="2099">
            </td>
            <td style="width:90px;">
                <input type="number" class="fs-inline-input" data-field="max_model_year"
                       value="${v(data.max_model_year)}" min="2000" max="2099">
            </td>
            <td style="width:70px;">
                <input type="number" class="fs-inline-input" data-field="min_term"
                       value="${v(data.min_term)}" min="0" max="200">
            </td>
            <td style="width:70px;">
                <input type="number" class="fs-inline-input" data-field="max_term"
                       value="${v(data.max_term)}" min="0" max="200">
            </td>
            <td style="width:80px;">
                <input type="number" class="fs-inline-input" data-field="min_credit_score"
                       value="${v(data.min_credit_score)}" min="300" max="850">
            </td>
            <td style="width:80px;">
                <input type="number" class="fs-inline-input" data-field="max_credit_score"
                       value="${v(data.max_credit_score)}" min="300" max="850">
            </td>
            <td style="min-width:90px;">
                <select class="fs-inline-select" data-field="condition">${condOptions}</select>
            </td>
            <td style="width:100px;">
                <div class="fs-rate-wrap">
                    <input type="number" step="0.01" class="fs-inline-input" data-field="rate"
                           value="${v(data.rate)}" min="0" max="99.99">
                    <span class="fs-rate-pct">%</span>
                </div>
            </td>
            <td>
                <div class="fs-actions">
                    <button class="btn-clone-rate" title="Clone" type="button">
                        <i class="bi bi-copy"></i>
                    </button>
                    <button class="btn-delete-rate" title="Delete" type="button">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }

    // ── Collect data from an existing TR ─────────────────────────────

    function collectRowData(tr) {
        const data = {};
        tr.querySelectorAll('[data-field]').forEach(el => {
            const field = el.dataset.field;
            const val   = el.value;
            if (['min_model_year', 'max_model_year', 'min_term', 'max_term'].includes(field)) {
                data[field] = val !== '' ? parseInt(val) : null;
            } else if (['min_credit_score', 'max_credit_score'].includes(field)) {
                data[field] = val !== '' ? parseInt(val) : null;
            } else if (field === 'rate') {
                data[field] = val !== '' ? parseFloat(val) : null;
            } else {
                data[field] = val || null;
            }
        });
        return data;
    }

    // ── Group management ──────────────────────────────────────────────

    function findGroupRow(yearKey) {
        return ratesBody.querySelector(`.fs-group-row[data-year-key="${yearKey}"]`);
    }

    function findLastRowInGroup(groupTr) {
        let last = groupTr;
        let next = groupTr.nextElementSibling;
        while (next && next.classList.contains('data-row')) {
            last = next;
            next = next.nextElementSibling;
        }
        return last;
    }

    function insertRowIntoGroup(yearKey, minYear, maxYear, rowHtml) {
        document.getElementById('emptyRow')?.remove();

        let groupTr = findGroupRow(yearKey);

        if (! groupTr) {
            const groupHtml = `
                <tr class="fs-group-row"
                    data-year-key="${yearKey}"
                    data-min-year="${minYear}"
                    data-max-year="${maxYear}">
                    <td colspan="10">
                        Model Years: ${minYear} - ${maxYear}
                        <button class="btn-add-group" type="button"
                                title="Add empty row to this group"
                                data-min-year="${minYear}"
                                data-max-year="${maxYear}">
                            <i class="bi bi-plus"></i>
                        </button>
                    </td>
                </tr>`;
            ratesBody.insertAdjacentHTML('afterbegin', groupHtml);
            groupTr = findGroupRow(yearKey);
        }

        const lastRow = findLastRowInGroup(groupTr);
        lastRow.insertAdjacentHTML('afterend', rowHtml);
    }

    function removeEmptyGroupIfNeeded(dataRow) {
        let prev = dataRow.previousElementSibling;
        while (prev && prev.classList.contains('data-row')) {
            prev = prev.previousElementSibling;
        }
        if (! prev?.classList.contains('fs-group-row')) return;

        const groupTr = prev;
        const hasMore = groupTr.nextElementSibling?.classList.contains('data-row');
        if (! hasMore) groupTr.remove();
    }

    // ── Active condition filter ka value ─────────────────────────────

    function getActiveCondition() {
        return document.querySelector('.fs-btn-group button.active')?.dataset.condition ?? 'any';
    }

    // ── Group headers visibility refresh ─────────────────────────────

    function refreshGroupVisibility() {
        ratesBody.querySelectorAll('.fs-group-row').forEach(groupTr => {
            let hasVisible = false;
            let next = groupTr.nextElementSibling;
            while (next && next.classList.contains('data-row')) {
                if (next.style.display !== 'none') {
                    hasVisible = true;
                    break;
                }
                next = next.nextElementSibling;
            }
            groupTr.style.display = hasVisible ? '' : 'none';
        });
    }

    // ── Combined filter — condition + term ────────────────────────────
    // Ye ek hi function dono filters apply karta hai ek saath

    function applyFilters() {
        const condition = getActiveCondition();
        const minFilter = parseInt(document.getElementById('filterMinTerm').value);
        const maxFilter = parseInt(document.getElementById('filterMaxTerm').value);

        const hasMinFilter = ! isNaN(minFilter) && document.getElementById('filterMinTerm').value !== '';
        const hasMaxFilter = ! isNaN(maxFilter) && document.getElementById('filterMaxTerm').value !== '';

        ratesBody.querySelectorAll('.data-row').forEach(tr => {
            let visible = true;

            // ── Condition check ───────────────────────────────────────
            if (condition !== 'any') {
                const condVal = tr.querySelector('[data-field="condition"]')?.value;
                if (condVal !== condition) visible = false;
            }

            // ── Term check (only if condition passed) ─────────────────
            if (visible && (hasMinFilter || hasMaxFilter)) {
                const rowMinTerm = parseInt(tr.querySelector('[data-field="min_term"]')?.value ?? '0');
                const rowMaxTerm = parseInt(tr.querySelector('[data-field="max_term"]')?.value ?? '0');

                // Min Term filter: row ka min_term >= filterMinTerm
                if (hasMinFilter && rowMinTerm < minFilter) visible = false;

                // Max Term filter: row ka max_term <= filterMaxTerm
                if (hasMaxFilter && rowMaxTerm > maxFilter) visible = false;
            }

            tr.style.display = visible ? '' : 'none';
        });

        refreshGroupVisibility();
    }

    // ── Condition Tab Filter ──────────────────────────────────────────

    document.querySelectorAll('.fs-btn-group button').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.fs-btn-group').querySelectorAll('button')
               .forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilters();
        });
    });

    // ── Term Filter inputs ────────────────────────────────────────────

    document.getElementById('filterMinTerm').addEventListener('input', applyFilters);
    document.getElementById('filterMaxTerm').addEventListener('input', applyFilters);

    // ── Delegated click handler for tbody ────────────────────────────

    ratesBody.addEventListener('click', e => {

        // ── + Group button → add empty row ────────────────────────────
        const addGroupBtn = e.target.closest('.btn-add-group');
        if (addGroupBtn) {
            const minYear = addGroupBtn.dataset.minYear;
            const maxYear = addGroupBtn.dataset.maxYear;
            const yearKey = `${minYear}_${maxYear}`;
            const rowHtml = buildRowHtml({ min_model_year: minYear, max_model_year: maxYear });
            insertRowIntoGroup(yearKey, minYear, maxYear, rowHtml);
            updateBanner(1);
            return;
        }

        // ── Clone button → duplicate row ─────────────────────────────
        const cloneBtn = e.target.closest('.btn-clone-rate');
        if (cloneBtn) {
            const tr      = cloneBtn.closest('tr.data-row');
            const data    = collectRowData(tr);
            const rowHtml = buildRowHtml(data);
            const minYear = tr.querySelector('[data-field="min_model_year"]')?.value;
            const maxYear = tr.querySelector('[data-field="max_model_year"]')?.value;
            const yearKey = `${minYear}_${maxYear}`;
            insertRowIntoGroup(yearKey, minYear, maxYear, rowHtml);
            updateBanner(1);
            return;
        }

        // ── Delete button ─────────────────────────────────────────────
        const deleteBtn = e.target.closest('.btn-delete-rate');
        if (deleteBtn) {
            if (! confirm('Delete this interest rate?')) return;

            const tr = deleteBtn.closest('tr.data-row');
            const id = tr.dataset.id;

            if (! isNewRow(id)) {
                pendingDeletes.push(parseInt(id));
            } else {
                updateBanner(-1);
            }

            removeEmptyGroupIfNeeded(tr);
            tr.remove();

            if (! ratesBody.querySelector('.data-row')) {
                ratesBody.innerHTML = `
                    <tr class="fs-empty-row" id="emptyRow">
                        <td colspan="10" class="text-center py-4 text-muted">
                            No interest rates configured yet. Click <strong>+ Add Rate</strong> to get started.
                        </td>
                    </tr>`;
            }

            if (! isNewRow(id)) updateBanner(1);
            return;
        }
    });

    // ── Modal Open / Close ────────────────────────────────────────────

    const arOverlay = document.getElementById('addRateModal');

    function openModal(minYear = null, maxYear = null) {
        if (minYear) document.getElementById('arMinYear').value = minYear;
        if (maxYear) document.getElementById('arMaxYear').value = maxYear;
        updateHelper();
        arOverlay.classList.add('active');
    }

    function closeModal() {
        arOverlay.classList.remove('active');
    }

    document.getElementById('btnAddRate').addEventListener('click', () => openModal());
    document.getElementById('btnCloseAR').addEventListener('click', closeModal);
    arOverlay.addEventListener('click', e => { if (e.target === arOverlay) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    // ── Live Helper ───────────────────────────────────────────────────

    const helperMap = {
        arMinYear:   'hMinYear',
        arMaxYear:   'hMaxYear',
        arCondition: 'hCondition',
        arMinTerm:   'hMinTerm',
        arMaxTerm:   'hMaxTerm',
        arMinCredit: 'hMinCredit',
        arMaxCredit: 'hMaxCredit',
        arRate:      'hRate',
    };

    function updateHelper() {
        for (const [inputId, spanId] of Object.entries(helperMap)) {
            const el   = document.getElementById(inputId);
            const span = document.getElementById(spanId);
            if (el && span) span.textContent = el.value || '—';
        }
    }

    Object.keys(helperMap).forEach(id => {
        document.getElementById(id)?.addEventListener('input',  updateHelper);
        document.getElementById(id)?.addEventListener('change', updateHelper);
    });

    updateHelper();

    // ── Add to Sheet → UI only, NO DB ────────────────────────────────

    document.getElementById('btnAddToSheet').addEventListener('click', () => {
        const minYear = document.getElementById('arMinYear').value;
        const maxYear = document.getElementById('arMaxYear').value;

        if (! minYear || ! maxYear) {
            toast('Please select model year range.', 'error');
            return;
        }

        const data = {
            min_model_year:   parseInt(minYear),
            max_model_year:   parseInt(maxYear),
            min_term:         parseInt(document.getElementById('arMinTerm').value)   || 0,
            max_term:         parseInt(document.getElementById('arMaxTerm').value)   || 36,
            min_credit_score: parseInt(document.getElementById('arMinCredit').value) || null,
            max_credit_score: parseInt(document.getElementById('arMaxCredit').value) || null,
            condition:        document.getElementById('arCondition').value,
            rate:             parseFloat(document.getElementById('arRate').value)    || null,
            make:             null,
        };

        if (! data.rate) {
            toast('Interest rate is required.', 'error');
            return;
        }

        const yearKey = `${minYear}_${maxYear}`;
        const rowHtml = buildRowHtml(data);
        insertRowIntoGroup(yearKey, minYear, maxYear, rowHtml);
        updateBanner(1);
        closeModal();
    });

    // ── Save → Send everything to DB ─────────────────────────────────

    document.getElementById('btnSaveRates').addEventListener('click', async () => {
        const creates = [];
        const updates = [];

        ratesBody.querySelectorAll('.data-row[data-id]').forEach((tr, index) => {
            const id   = tr.dataset.id;
            const data = collectRowData(tr);
            data.sort_order = index;

            if (isNewRow(id)) {
                creates.push(data);
            } else {
                updates.push({ id: parseInt(id), ...data });
            }
        });

        const payload = {
            creates,
            updates,
            deletes: pendingDeletes,
        };

        const btn     = document.getElementById('btnSaveRates');
        btn.disabled  = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';

        try {
            await apiFetch(syncUrl, 'POST', payload);
            toast('Interest rates saved successfully.');
            window.location.reload();
        } catch (e) {
            toast(e.message || 'Save failed. Please try again.', 'error');
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Save';
        }
    });

})();