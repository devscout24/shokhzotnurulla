/**
 * Contact Us — AJAX Form Submission
 * Route: POST /forms/contact-us  →  frontend.forms.contact-us
 * Returns: { success: true }  |  422 { errors: {...} }
 */

document.addEventListener('DOMContentLoaded', () => {

    const form        = document.getElementById('contact-us-form');
    const submitBtn   = document.getElementById('contact-submit-btn');
    const formWrapper = document.getElementById('contact-form-wrapper');
    const successBox  = document.getElementById('contact-success-box');

    if (!form) return;

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Clear all previously shown field errors
     */
    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    }

    /**
     * Render Laravel validation errors under each field
     * @param {Object} errors  — { first_name: ['...'], email: ['...'], ... }
     */
    function showErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;

            input.classList.add('is-invalid');

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = messages[0];

            // Radio groups — append after parent .mb-3 div, not after input itself
            if (input.type === 'radio') {
                const container = input.closest('.mb-3, .mb-md-4');
                if (container) container.appendChild(feedback);
            } else {
                input.insertAdjacentElement('afterend', feedback);
            }
        });
    }

    /**
     * Set button to loading state
     */
    function setLoading() {
        submitBtn.disabled = true;
        submitBtn.dataset.originalText = submitBtn.querySelector('.btn-label').textContent;
        submitBtn.querySelector('.btn-label').textContent = 'Sending...';
        submitBtn.querySelector('.btn-icon')?.classList.add('d-none');
    }

    /**
     * Restore button to original state
     */
    function resetButton() {
        submitBtn.disabled = false;
        submitBtn.querySelector('.btn-label').textContent = submitBtn.dataset.originalText ?? 'Continue';
        submitBtn.querySelector('.btn-icon')?.classList.remove('d-none');
    }

    /**
     * Show the success state — hide form, show success box
     */
    function showSuccess() {
        formWrapper.classList.add('d-none');
        successBox.classList.remove('d-none');
    }

    // ── Submit Handler ──────────────────────────────────────────────────────────

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Native HTML5 validation check karo pehle
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        clearErrors();
        setLoading();

        const formData = new FormData(form);

        try {
            const response = await fetch(form.dataset.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const json = await response.json();

            if (response.ok && json.success) {
                showSuccess();
                return;
            }

            // 422 Unprocessable — validation errors from Laravel
            if (response.status === 422 && json.errors) {
                showErrors(json.errors);
                resetButton();
                return;
            }

            // Any other unexpected error
            resetButton();
            console.error('Contact form error:', json);

        } catch (err) {
            resetButton();
            console.error('Contact form network error:', err);
        }
    });

});
