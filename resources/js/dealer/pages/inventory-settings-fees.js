// resources/js/dealer/pages/inventory-settings-fees.js

(function () {
    'use strict';

    const csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const cfg        = window.feesConfig ?? {};
    const storeUrl   = cfg.storeUrl   ?? '';
    const reorderUrl = cfg.reorderUrl ?? '';
    const dealerName = cfg.dealerName ?? '';
    const feesBody   = document.getElementById('feesBody');

    // ── Current edit state ────────────────────────────────────────────
    let editingFeeId  = null;
    let editUpdateUrl = null;

    // ── API Helper ────────────────────────────────────────────────────

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

    // ── Modal ─────────────────────────────────────────────────────────

    const aifOverlay = document.getElementById('addFeeModal');

    function openModal(fee = null) {
        editingFeeId  = null;
        editUpdateUrl = null;

        // Reset form
        document.getElementById('feeName').value        = '';
        document.getElementById('feeDescription').value = '';
        document.getElementById('feeType').value        = '';
        document.getElementById('feeValue').value       = '';
        document.getElementById('feeTax').value         = '';
        document.getElementById('feeOptional').value    = '';
        document.getElementById('feeCondition').value   = 'any';
        document.getElementById('feeModalTitle').textContent = 'Add Inventory Fee';
        updateAmountSymbol('');

        if (fee) {
            // Edit mode — prefill
            editingFeeId  = fee.id;
            editUpdateUrl = fee.updateUrl;
            document.getElementById('feeModalTitle').textContent = 'Edit Inventory Fee';
            document.getElementById('feeName').value        = fee.name;
            document.getElementById('feeDescription').value = fee.description;
            document.getElementById('feeType').value        = fee.type;
            document.getElementById('feeValue').value       = fee.value;
            document.getElementById('feeTax').value         = fee.tax;
            document.getElementById('feeOptional').value    = fee.isOptional;
            document.getElementById('feeCondition').value   = fee.condition;
            updateAmountSymbol(fee.type);
        }

        aifOverlay.classList.add('active');
    }

    function closeModal() {
        aifOverlay.classList.remove('active');
        editingFeeId  = null;
        editUpdateUrl = null;
    }

    document.getElementById('btnAddFee').addEventListener('click', () => openModal());
    document.getElementById('btnCloseFeeModal').addEventListener('click', closeModal);
    aifOverlay.addEventListener('click', e => { if (e.target === aifOverlay) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    // ── Dynamic Amount Symbol ─────────────────────────────────────────

    function updateAmountSymbol(type) {
        const prefix = document.getElementById('feeAmountPrefix');
        const suffix = document.getElementById('feeAmountSuffix');
        const input  = document.getElementById('feeValue');

        if (type === 'percentage') {
            prefix.style.display = 'none';
            suffix.style.display = '';
            input.style.paddingLeft  = '10px';
            input.style.paddingRight = '26px';
        } else {
            prefix.style.display = '';
            suffix.style.display = 'none';
            input.style.paddingLeft  = '22px';
            input.style.paddingRight = '10px';
        }
    }

    document.getElementById('feeType').addEventListener('change', function () {
        updateAmountSymbol(this.value);
    });

    // ── Save Fee ──────────────────────────────────────────────────────

    document.getElementById('btnSaveFee').addEventListener('click', async () => {
        const name      = document.getElementById('feeName').value.trim();
        const type      = document.getElementById('feeType').value;
        const value     = document.getElementById('feeValue').value;
        const tax       = document.getElementById('feeTax').value;
        const isOptional = document.getElementById('feeOptional').value;

        // Basic validation
        if (! name)       { toast('Name is required.',      'error'); return; }
        if (! type)       { toast('Type is required.',      'error'); return; }
        if (value === '') { toast('Amount is required.',    'error'); return; }
        if (! tax)        { toast('Tax type is required.',  'error'); return; }
        if (isOptional === '') { toast('Optional/Guaranteed is required.', 'error'); return; }

        const payload = {
            name,
            description: document.getElementById('feeDescription').value.trim() || null,
            type,
            value:       parseFloat(value),
            tax,
            is_optional: Boolean(parseInt(isOptional)),
            condition:   document.getElementById('feeCondition').value || 'any',
        };

        const btn      = document.getElementById('btnSaveFee');
        btn.disabled   = true;
        btn.innerHTML  = '<i class="bi bi-hourglass-split me-1"></i> Saving…';

        try {
            let data;

            if (editingFeeId) {
                // Update existing
                data = await apiFetch(editUpdateUrl, 'PATCH', payload);
                // Replace existing row
                const existingRow = feesBody.querySelector(`tr[data-id="${editingFeeId}"]`);
                if (existingRow) {
                    existingRow.outerHTML = data.row_html;
                }
                toast('Fee updated successfully.');
            } else {
                // Create new
                data = await apiFetch(storeUrl, 'POST', payload);
                document.getElementById('emptyRow')?.remove();
                feesBody.insertAdjacentHTML('beforeend', data.row_html);
                toast('Fee added successfully.');
            }

            closeModal();
            initSortable();
        } catch (e) {
            toast(e.message || 'Failed to save fee.', 'error');
        } finally {
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save';
        }
    });

    // ── Delegated events (Edit + Delete) ──────────────────────────────

    feesBody.addEventListener('click', async e => {

        // ── Edit ──────────────────────────────────────────────────────
        const editBtn = e.target.closest('.btn-edit-fee');
        if (editBtn) {
            const tr = editBtn.closest('tr.if-row');
            openModal({
                id:         parseInt(tr.dataset.id),
                updateUrl:  tr.dataset.updateUrl,
                name:       tr.dataset.name,
                description: tr.dataset.description,
                type:       tr.dataset.type,
                value:      tr.dataset.value,
                tax:        tr.dataset.tax,
                isOptional: tr.dataset.isOptional,
                condition:  tr.dataset.condition,
            });
            return;
        }

        // ── Delete ────────────────────────────────────────────────────
        const deleteBtn = e.target.closest('.btn-delete-fee');
        if (deleteBtn) {
            if (! confirm('Delete this inventory fee?')) return;

            const url = deleteBtn.dataset.url;
            const tr  = deleteBtn.closest('tr.if-row');

            try {
                await apiFetch(url, 'DELETE');
                tr.remove();

                if (! feesBody.querySelector('tr.if-row')) {
                    feesBody.innerHTML = `
                        <tr id="emptyRow">
                            <td colspan="8" class="no-results">No fees found.</td>
                        </tr>`;
                }
                toast('Fee deleted.');
            } catch (err) {
                toast(err.message || 'Failed to delete fee.', 'error');
            }
            return;
        }
    });

    // ── Drag to Reorder (SortableJS CDN) ─────────────────────────────

    function initSortable() {
        if (typeof Sortable === 'undefined') return;

        // Destroy existing instance if any
        if (feesBody._sortable) feesBody._sortable.destroy();

        feesBody._sortable = Sortable.create(feesBody, {
            handle:    '.if-drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: async () => {
                const ids = [...feesBody.querySelectorAll('tr.if-row')]
                    .map(tr => parseInt(tr.dataset.id));

                try {
                    await apiFetch(reorderUrl, 'POST', { ids });
                } catch (err) {
                    toast('Reorder failed.', 'error');
                }
            },
        });
    }

    initSortable();

})();