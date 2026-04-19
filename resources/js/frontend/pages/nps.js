/**
 * NPS (Net Promoter Score) Module
 * Shared across all frontend forms: ask-question, managers-special,
 * schedule-test-drive, trade-in, contact-us
 *
 * Usage:
 *   window.NpsModule.init({
 *       starRatingId:  'aq-star-rating',
 *       npsOptionsId:  'aqNpsOptions',
 *       submitBtnSel:  '.aq-nps-submit-btn',
 *       getNpsRoute:   function() { return window.npsRouteTemplate.replace('__id__', formEntryId); },
 *   });
 *
 * Lives in: resources/js/frontend/modules/nps.js
 */

(function (global) {

    'use strict';

    var csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    /**
     * Init NPS for a specific form's success state.
     *
     * @param {object} config
     * @param {string}   config.starRatingId  — ID of the star container element
     * @param {string}   config.npsOptionsId  — ID of the options/feedback div
     * @param {string}   config.submitBtnSel  — CSS selector for the submit button
     * @param {Function} config.getNpsRoute   — function that returns the PATCH URL with form_entry_id injected
     */
    function init(config) {
        var starContainer = document.getElementById(config.starRatingId);
        var npsOptions    = document.getElementById(config.npsOptionsId);

        if (!starContainer) return;

        var stars        = starContainer.querySelectorAll('.star');
        var selectedRating = null;
        var submitted      = false;

        // ── Star hover + click ────────────────────────────────────────────────
        stars.forEach(function (star, idx) {

            // Hover — highlight up to hovered star
            star.addEventListener('mouseenter', function () {
                if (submitted) return;
                stars.forEach(function (s, i) {
                    s.classList.toggle('filled', i <= idx);
                });
            });

            // Mouse leave — restore selected state
            starContainer.addEventListener('mouseleave', function () {
                if (submitted) return;
                stars.forEach(function (s, i) {
                    s.classList.toggle('filled', selectedRating !== null && i < selectedRating);
                });
            });

            // Click — set rating, show options
            star.addEventListener('click', function () {
                if (submitted) return;

                selectedRating = idx + 1;

                stars.forEach(function (s, i) {
                    s.classList.toggle('filled', i <= idx);
                });

                if (npsOptions) {
                    npsOptions.classList.remove('hidden');
                }

                // Auto-submit rating immediately on star click
                submitRating(selectedRating, config.getNpsRoute(), null, config.npsOptionsId);
            });
        });

        // ── Submit feedback (optional comment) ────────────────────────────────
        var submitBtn = config.submitBtnSel
            ? document.querySelector(config.submitBtnSel)
            : null;

        if (submitBtn) {
            submitBtn.addEventListener('click', function () {
                if (submitted || !selectedRating) return;
                submitRating(selectedRating, config.getNpsRoute(), submitBtn, config.npsOptionsId);
            });
        }
    }

    // ── AJAX submit ───────────────────────────────────────────────────────────
    function submitRating(rating, url, btn, npsOptionsId) {
        if (!url || !rating) return;

        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Sending...';
        }

        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ rating: rating }),
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            if (json.success) {
                // Show thank you state
                var npsOptions = document.getElementById(npsOptionsId);
                if (npsOptions) {
                    npsOptions.innerHTML = '<p class="text-muted mt-2">Thank you for your feedback!</p>';
                }
            }

            if (btn) {
                btn.disabled     = false;
                btn.textContent  = 'Feedback Sent!';
            }
        })
        .catch(function (err) {
            console.error('NPS submit error:', err);
            if (btn) {
                btn.disabled    = false;
                btn.textContent = 'Share Your Feedback';
            }
        });
    }

    // ── Expose globally ───────────────────────────────────────────────────────
    global.NpsModule = { init: init };

})(window);
