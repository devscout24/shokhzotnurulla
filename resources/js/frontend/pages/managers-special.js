/**
 * Unlock Manager's Special — AJAX Form Submission
 * Route: POST /forms/managers-special  →  frontend.forms.managers-special
 * Returns: { success: true }  |  422 { errors: {...} }
 *
 * Lives in: resources/js/frontend/pages/managers-special.js
 * Loaded via: vehicle-detail.blade.php @push('page-scripts')
 */

(function () {

    const form        = document.getElementById('manager-special-form');
    const submitBtn   = document.getElementById('ms-submit-btn');
    const formWrapper = document.getElementById('managerFormWrapper');
    const successBox  = document.getElementById('managerSuccessBox');

    if (!form) return;

    // ── State ──────────────────────────────────────────────────────────────────
    var formEntryId = null;

    // ── Helpers ────────────────────────────────────────────────────────────────

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const input = form.querySelector('[name="' + field + '"]');
            if (!input) return;
            input.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = messages[0];
            if (input.type === 'radio') {
                const container = input.closest('.mb-3, .mb-md-4');
                if (container) container.appendChild(feedback);
            } else {
                input.insertAdjacentElement('afterend', feedback);
            }
        });
    }

    function setLoading() {
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-label').textContent = 'Sending...';
        submitBtn.querySelector('.btn-icon')?.classList.add('d-none');
    }

    function resetButton() {
        submitBtn.disabled = false;
        submitBtn.querySelector('.btn-label').textContent = 'Continue';
        submitBtn.querySelector('.btn-icon')?.classList.remove('d-none');
    }

    function showSuccess() {
        formWrapper.classList.add('hidden');
        successBox.classList.remove('hidden');

        if (window.NpsModule) {
            window.NpsModule.init({
                starRatingId: 'ms-star-rating',
                npsOptionsId: 'msNpsOptions',
                submitBtnSel: '.ms-nps-submit-btn',
                getNpsRoute:  function () {
                    return (window.npsRouteTemplate || '').replace('__id__', formEntryId);
                },
            });
        }
    }

    // ── Submit Handler ──────────────────────────────────────────────────────────

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        clearErrors();
        setLoading();

        try {
            const response = await fetch(form.dataset.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            const json = await response.json();

            if (response.ok && json.success) {
                formEntryId = json.form_entry_id ?? null;
                showSuccess();
                return;
            }

            if (response.status === 422 && json.errors) {
                showErrors(json.errors);
                resetButton();
                return;
            }

            resetButton();
            console.error('Manager special form error:', json);

        } catch (err) {
            resetButton();
            console.error('Manager special form network error:', err);
        }
    });

    // ── Get Approved CTA ───────────────────────────────────────────────────────

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.ms-get-approved-btn')) return;

        const managerOffcanvas = document.getElementById('unlockManager');
        if (managerOffcanvas) bootstrap.Offcanvas.getInstance(managerOffcanvas)?.hide();

        const getApprovedOffcanvas = document.getElementById('getApproved');
        if (getApprovedOffcanvas) new bootstrap.Offcanvas(getApprovedOffcanvas).show();
    });

    // ── Reset on offcanvas close ───────────────────────────────────────────────

    const offcanvasEl = document.getElementById('unlockManager');
    if (offcanvasEl) {
        offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
            form.reset();
            clearErrors();
            formWrapper.classList.remove('hidden');
            successBox.classList.add('hidden');
            resetButton();
            formEntryId = null;
            document.querySelectorAll('#ms-star-rating .star').forEach(s => s.classList.remove('filled'));
            const npsOptions = document.getElementById('msNpsOptions');
            if (npsOptions) npsOptions.classList.add('hidden');
        });
    }

})();
