/**
 * Ask a Question — AJAX Form Submission
 * Route: POST /forms/ask-question  →  frontend.forms.ask-question
 * Returns: { success: true }  |  422 { errors: {...} }
 *
 * Lives in: resources/js/frontend/pages/ask-question.js
 * Loaded via: vehicle-detail.blade.php @push('page-scripts')
 */

(function () {

    const form        = document.getElementById('ask-question-form');
    const submitBtn   = document.getElementById('aq-submit-btn');
    const formWrapper = document.getElementById('aqFormWrapper');
    const successBox  = document.getElementById('aqSuccessBox');

    if (!form) return;

    // ── State ──────────────────────────────────────────────────────────────────
    var formEntryId = null;

    // ── Populate vehicle context on offcanvas open ─────────────────────────────
    const offcanvasEl = document.getElementById('askQuestion');

    if (offcanvasEl) {
        offcanvasEl.addEventListener('show.bs.offcanvas', () => {
            const year  = document.getElementById('td_year')?.textContent?.trim()  ?? '';
            const make  = document.getElementById('td_make')?.textContent?.trim()  ?? '';
            const model = document.getElementById('td_model')?.textContent?.trim() ?? '';
            const vehicleId = document.querySelector('[data-vehicle-id]')?.dataset?.vehicleId ?? '';

            const title = [year, make, model].filter(Boolean).join(' ');

            const titleEl = document.getElementById('aq-vehicle-title');
            if (titleEl) titleEl.textContent = title;

            document.querySelectorAll('.aq-vehicle-title-success').forEach(el => {
                el.textContent = title;
            });

            const vehicleIdInput = document.getElementById('aq-vehicle-id');
            if (vehicleIdInput) vehicleIdInput.value = vehicleId;
        });

        // Reset on close
        offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
            form.reset();
            clearErrors();
            formWrapper.classList.remove('hidden');
            successBox.classList.add('hidden');
            resetButton();
            formEntryId = null;
            document.querySelectorAll('#aq-star-rating .star').forEach(s => s.classList.remove('filled'));
            const npsOptions = document.getElementById('aqNpsOptions');
            if (npsOptions) {
                npsOptions.classList.add('hidden');
                npsOptions.innerHTML = npsOptions.innerHTML; // reset inner content
            }
        });
    }

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

        // Init NPS after success
        if (window.NpsModule) {
            window.NpsModule.init({
                starRatingId: 'aq-star-rating',
                npsOptionsId: 'aqNpsOptions',
                submitBtnSel: '.aq-nps-submit-btn',
                getNpsRoute:  function () {
                    return (window.npsRouteTemplate || '').replace('__id__', formEntryId);
                },
            });
        }
    }

    // ── Submit Handler ──────────────────────────────────────────────────────────

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

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
            console.error('Ask question form error:', json);

        } catch (err) {
            resetButton();
            console.error('Ask question form network error:', err);
        }
    });

    // ── Get Approved CTA ───────────────────────────────────────────────────────

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.aq-get-approved-btn')) return;

        const aqOffcanvas = document.getElementById('askQuestion');
        if (aqOffcanvas) bootstrap.Offcanvas.getInstance(aqOffcanvas)?.hide();

        const getApprovedOffcanvas = document.getElementById('getApproved');
        if (getApprovedOffcanvas) new bootstrap.Offcanvas(getApprovedOffcanvas).show();
    });

})();
