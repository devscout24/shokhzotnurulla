/**
 * Unlock e-Price — AJAX Form Submission
 * Route: POST /forms/unlock-price  →  frontend.forms.unlock-price
 * Returns: { success: true }  |  422 { errors: {...} }
 *
 * Lives in: resources/js/frontend/pages/unlock-eprice.js
 * Loaded via: vehicle-detail.blade.php @push('page-assets')
 */

(function () {

    const form        = document.getElementById('unlock-price-form');
    const submitBtn   = document.getElementById('up-submit-btn');
    const formWrapper = document.getElementById('upFormWrapper');
    const successBox  = document.getElementById('upSuccessBox');

    if (! form) return;

    // ── State ─────────────────────────────────────────────────────────────────
    var formEntryId      = null;
    var _activeVehicleId = null;   // jo abhi offcanvas mein open hai
    var _activeTriggerBtn = null;  // jis card ka button click hua

    const STORAGE_PREFIX = 'up_';

    function storageKey(vehicleId) {
        return STORAGE_PREFIX + vehicleId;
    }

    function isUnlocked(vehicleId) {
        try {
            return localStorage.getItem(storageKey(vehicleId)) === '1';
        } catch (e) {
            return false;
        }
    }

    function markUnlocked(vehicleId) {
        try {
            localStorage.setItem(storageKey(vehicleId), '1');
        } catch (e) {}
    }

    // ── Vehicle ID resolve karo ───────────────────────────────────────────────
    // VDP par: window.vdpConfig
    // Listing/home par: triggering button ka data attribute
    function resolveVehicleId() {
        if (window.vdpConfig && window.vdpConfig.vehicleId) {
            return window.vdpConfig.vehicleId;
        }
        return _activeVehicleId || '';
    }

    function resolveVehicleTitle() {
        if (window.vdpConfig && window.vdpConfig.vehicleTitle) {
            return window.vdpConfig.vehicleTitle;
        }
        return _activeTriggerBtn
            ? (_activeTriggerBtn.dataset.vehicleTitle || '')
            : '';
    }

    // ── Reveal price ──────────────────────────────────────────────────────────
    // VDP par: saari .label-price (page par ek hi vehicle)
    // Listing par: sirf us card ki .label-price jis ka button click hua
    function revealPrice(vehicleId) {
        var isVdpMainBtn = _activeTriggerBtn &&
                           !_activeTriggerBtn.closest('.srp-cardcontainer');

        if (isVdpMainBtn || (!_activeTriggerBtn && !!document.getElementById('vdp_top_price'))) {
            // VDP main price area — page-wide reveal
            document.querySelectorAll('.label-price').forEach(function (el) {
                el.style.filter     = '';
                el.style.userSelect = '';
            });
            document.querySelectorAll('.btn-unlock-price:not([data-vehicle-id="' + vehicleId + '"], .srp-cardcontainer *)').forEach(function (btn) {
                if (!btn.closest('.srp-cardcontainer')) {
                    btn.disabled  = true;
                    btn.innerHTML = '<i class="fa-solid fa-lock-open me-1"></i> Price Unlocked';
                    btn.classList.remove('btn-outline-secondary');
                    btn.classList.add('btn-success');
                }
            });
        } else if (_activeTriggerBtn) {
            // Listing/home card — sirf us card ko update karo
            var card = _activeTriggerBtn.closest('.srp-cardcontainer');
            if (card) {
                card.querySelectorAll('.label-price').forEach(function (el) {
                    el.style.filter     = '';
                    el.style.userSelect = '';
                });
            }
            _activeTriggerBtn.disabled  = true;
            _activeTriggerBtn.innerHTML = '<i class="fa-solid fa-lock-open me-1"></i> Price Unlocked';
            _activeTriggerBtn.classList.remove('btn-outline-secondary');
            _activeTriggerBtn.classList.add('btn-success');
        }
    }

    // ── Page load — already unlocked cards auto-reveal ────────────────────────
    function autoRevealUnlockedCards() {
        document.querySelectorAll('.btn-unlock-price').forEach(function (btn) {
            var vid = btn.dataset.vehicleId ? String(btn.dataset.vehicleId) : '';
            if (vid && isUnlocked(vid)) {
                _activeTriggerBtn = btn;
                revealPrice(vid);
                // null baad mein — revealPrice ke andar use hota hai
            }
        });
        _activeTriggerBtn = null; // loop khatam hone ke baad reset
    }

    // ── Offcanvas — show ──────────────────────────────────────────────────────
    const offcanvasEl = document.getElementById('unlockEPrice');

    if (offcanvasEl) {

        // Triggering button capture karo — Bootstrap 5 mein relatedTarget milta hai
        offcanvasEl.addEventListener('show.bs.offcanvas', function (e) {
            // e.relatedTarget = jis element ne offcanvas trigger kiya
            if (e.relatedTarget && e.relatedTarget.dataset.vehicleId) {
                _activeTriggerBtn = e.relatedTarget;
                _activeVehicleId  = e.relatedTarget.dataset.vehicleId;
            } else {
                // VDP case — vdpConfig se
                _activeTriggerBtn = null;
                _activeVehicleId  = null;
            }

            var vehicleId    = resolveVehicleId();
            var vehicleTitle = resolveVehicleTitle();

            var titleEl = document.getElementById('up-vehicle-title');
            if (titleEl) titleEl.textContent = vehicleTitle;

            var vehicleIdInput = document.getElementById('up-vehicle-id');
            if (vehicleIdInput) vehicleIdInput.value = vehicleId;

            // Already unlocked? seedha success show karo
            if (vehicleId && isUnlocked(vehicleId)) {
                formWrapper.classList.add('hidden');
                successBox.classList.remove('hidden');
            } else {
                formWrapper.classList.remove('hidden');
                successBox.classList.add('hidden');
            }
        });

        // Reset on close
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
            form.reset();
            clearErrors();
            formWrapper.classList.remove('hidden');
            successBox.classList.add('hidden');
            resetButton();
            formEntryId      = null;
            _activeVehicleId = null;
            // _activeTriggerBtn ko reset mat karo — revealPrice baad mein bhi use ho sakta hai
            document.querySelectorAll('#up-star-rating .star').forEach(function (s) {
                s.classList.remove('filled');
            });
            var npsOptions = document.getElementById('upNpsOptions');
            if (npsOptions) {
                npsOptions.classList.add('hidden');
                npsOptions.innerHTML = npsOptions.innerHTML;
            }
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(function (el) {
            el.remove();
        });
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(function ([field, messages]) {
            var input = form.querySelector('[name="' + field + '"]');
            if (! input) return;
            input.classList.add('is-invalid');
            var feedback = document.createElement('div');
            feedback.className   = 'invalid-feedback';
            feedback.textContent = messages[0];
            input.insertAdjacentElement('afterend', feedback);
        });
    }

    function setLoading() {
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-label').textContent = 'Unlocking...';
        submitBtn.querySelector('.btn-icon')?.classList.add('d-none');
    }

    function resetButton() {
        submitBtn.disabled = false;
        submitBtn.querySelector('.btn-label').textContent = 'Continue';
        submitBtn.querySelector('.btn-icon')?.classList.remove('d-none');
    }

    function showSuccess() {
        var vehicleId = resolveVehicleId();

        if (vehicleId) markUnlocked(vehicleId);
        revealPrice(vehicleId);

        formWrapper.classList.add('hidden');
        successBox.classList.remove('hidden');

        if (window.NpsModule) {
            window.NpsModule.init({
                starRatingId: 'up-star-rating',
                npsOptionsId: 'upNpsOptions',
                submitBtnSel: '.up-nps-submit-btn',
                getNpsRoute: function () {
                    return (window.npsRouteTemplate || '').replace('__id__', formEntryId);
                },
            });
        }
    }

    // ── Submit Handler ────────────────────────────────────────────────────────

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (! form.checkValidity()) {
            form.reportValidity();
            return;
        }

        clearErrors();
        setLoading();

        try {
            var response = await fetch(form.dataset.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept':       'application/json',
                },
                body: new FormData(form),
            });

            var json = await response.json();

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
            console.error('Unlock price form error:', json);

        } catch (err) {
            resetButton();
            console.error('Unlock price form network error:', err);
        }
    });

    // ── Page load ─────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        autoRevealUnlockedCards();
    });

})();