// ─────────────────────────────────────────────────────────────────────────────
// Inventory Listing — AJAX Filters + State Preservation + Image Slider
// File: resources/js/frontend/pages/inventory-listing.js
// ─────────────────────────────────────────────────────────────────────────────

(function () {
    'use strict';

    // ── Config — passed from blade via window.srpConfig ──────────────────────
    const cfg = window.srpConfig || {};
    const FILTER_URL  = cfg.filterUrl  || '';   // e.g. /inventory/filter
    const CSRF        = cfg.csrf       || document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const form        = document.getElementById('inventory-filter-form');
    const gridEl      = document.getElementById('inventory-grid');
    const paginEl     = document.getElementById('inventory-pagination');
    const headingEl   = document.getElementById('inventory-heading');

    // ── Track in-flight request to cancel previous ────────────────────────────
    let abortController = null;

    // ─────────────────────────────────────────────────────────────────────────
    // 1. AJAX FETCH
    // ─────────────────────────────────────────────────────────────────────────
    function fetchResults(params, pushState = true) {
        if (! form || ! gridEl) return;

        // Cancel previous in-flight request
        if (abortController) abortController.abort();
        abortController = new AbortController();

        // Loading state
        gridEl.style.opacity   = '0.4';
        gridEl.style.pointerEvents = 'none';

        const url    = FILTER_URL + '?' + params.toString();
        const pushUrl = window.location.pathname + '?' + params.toString();

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
            signal: abortController.signal,
        })
        .then(r => r.json())
        .then(data => {
            // Update grid
            gridEl.innerHTML           = data.grid;
            gridEl.style.opacity       = '1';
            gridEl.style.pointerEvents = '';

            // Re-init sliders on newly injected cards
            if (typeof window.initVehicleSliders === 'function') {
                window.initVehicleSliders(gridEl);
            }

            // update filter badges
            updateBadges();

            // Update pagination
            if (paginEl) paginEl.innerHTML = data.pagination;

            // Update heading
            if (headingEl) headingEl.textContent = data.heading;

            // Update URL without reload
            if (pushState) history.pushState({ params: params.toString() }, '', pushUrl);

            // Scroll to grid top smoothly
            gridEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Re-bind pagination links
            bindPagination();
        })
        .catch(err => {
            if (err.name === 'AbortError') return; // cancelled — ignore
            gridEl.style.opacity       = '1';
            gridEl.style.pointerEvents = '';
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. COLLECT FORM PARAMS — serialize form properly (checkboxes as arrays)
    // ─────────────────────────────────────────────────────────────────────────
    function collectParams() {
        const params = new URLSearchParams();
        const data   = new FormData(form);

        for (const [key, value] of data.entries()) {
            if (value !== '' && value !== null) {
                params.append(key, value);
            }
        }
        return params;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. FILTER DROPDOWN STATE — expand sections that have active filters
    // ─────────────────────────────────────────────────────────────────────────
    function restoreDropdownState() {
        if (! form) return;

        // Expand any section that has a checked checkbox
        form.querySelectorAll('.filter-dropdown').forEach(section => {
            const hasChecked = section.querySelector('input[type="checkbox"]:checked');
            if (hasChecked) section.classList.add('active');
        });

        // Expand Make & Model if a model-list has a checked item — show model list
        form.querySelectorAll('.make-checkbox').forEach(makeCheckbox => {
            if (makeCheckbox.checked) {
                const modelList = makeCheckbox.closest('.make-item')?.querySelector('.model-list');
                if (modelList) modelList.style.display = 'block';
            }
        });

        // Also show model list if any model under a make is checked (even if make unchecked)
        form.querySelectorAll('.model-list input[type="checkbox"]:checked').forEach(modelCb => {
            const modelList = modelCb.closest('.model-list');
            if (modelList) modelList.style.display = 'block';

            // Also check the parent make checkbox if it isn't already
            const makeItem = modelList.closest('.make-item');
            if (makeItem) {
                const makeCb = makeItem.querySelector('.make-checkbox');
                if (makeCb && ! makeCb.checked) makeCb.checked = true;
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. FILTER DROPDOWN TOGGLE — click accordion header
    // ─────────────────────────────────────────────────────────────────────────
    function bindDropdownToggles() {
        document.querySelectorAll('.dropdown-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                this.closest('.filter-dropdown')?.classList.toggle('active');
            });
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. MAKE CHECKBOX — show/hide model list WITHOUT auto-submit
    // ─────────────────────────────────────────────────────────────────────────
    function bindMakeCheckboxes() {
        document.querySelectorAll('.make-checkbox').forEach(function (cb) {
            cb.addEventListener('change', function () {
                const modelList = this.closest('.make-item')?.querySelector('.model-list');
                if (modelList) {
                    modelList.style.display = this.checked ? 'block' : 'none';

                    // Uncheck all models when make is unchecked
                    if (! this.checked) {
                        modelList.querySelectorAll('input[type="checkbox"]').forEach(m => {
                            m.checked = false;
                        });
                    }
                }
                // Do NOT auto-submit here — user may want to pick a model first
                // Submit fires from the general checkbox handler below
                fetchResults(collectParams());
            });
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. ALL OTHER CHECKBOXES + SELECTS — auto-submit via AJAX
    //    (excludes make-checkbox — handled above)
    // ─────────────────────────────────────────────────────────────────────────
    function bindFilterInputs() {
        if (! form) return;

        // Checkboxes (excluding make — it's handled separately above)
        form.querySelectorAll('input[type="checkbox"]:not(.make-checkbox)').forEach(cb => {
            cb.addEventListener('change', () => fetchResults(collectParams()));
        });

        // Selects (year, mileage)
        form.querySelectorAll('select').forEach(sel => {
            sel.addEventListener('change', () => fetchResults(collectParams()));
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 7. PAGINATION — intercept paginator link clicks
    // ─────────────────────────────────────────────────────────────────────────
    function bindPagination() {
        if (! paginEl) return;

        paginEl.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url      = new URL(this.href);
                const params   = new URLSearchParams(url.search);
                fetchResults(params);
            });
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. BROWSER BACK/FORWARD — restore state from history
    // ─────────────────────────────────────────────────────────────────────────
    window.addEventListener('popstate', function (e) {
        const params = new URLSearchParams(e.state?.params || window.location.search);
        fetchResults(params, false);
    });

    // ─────────────────────────────────────────────────────────────────────────
    // 9. PRICE SLIDER ─────────────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────
    function initPriceSlider() {
        const track     = document.querySelector('.slider-track');
        const handleMin = document.getElementById('handle-min');
        const handleMax = document.getElementById('handle-max');
        if (! track || ! handleMin || ! handleMax) return;

        const minValue = parseInt(handleMin.getAttribute('aria-valuemin'), 10);
        const maxValue = parseInt(handleMax.getAttribute('aria-valuemax'), 10);
        let trackRect  = track.getBoundingClientRect();

        function updateHandlePosition(handle, value) {
            const percent    = (value - minValue) / (maxValue - minValue);
            handle.style.left = (percent * trackRect.width) + 'px';
            handle.setAttribute('aria-valuenow', value);
        }

        updateHandlePosition(handleMin, parseInt(handleMin.getAttribute('aria-valuenow'), 10));
        updateHandlePosition(handleMax, parseInt(handleMax.getAttribute('aria-valuenow'), 10));

        function handleDrag(handle, isMin) {
            function onMouseMove(e) {
                trackRect = track.getBoundingClientRect();
                let x = Math.max(0, Math.min(e.clientX - trackRect.left, trackRect.width));
                if (isMin) {
                    const maxX = parseFloat(handleMax.style.left) || trackRect.width;
                    if (x > maxX) x = maxX;
                } else {
                    const minX = parseFloat(handleMin.style.left) || 0;
                    if (x < minX) x = minX;
                }
                const value = Math.round((x / trackRect.width) * (maxValue - minValue) + minValue);
                handle.style.left = x + 'px';
                handle.setAttribute('aria-valuenow', value);
            }
            function onMouseUp() {
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
                updatePriceValues();
                fetchResults(collectParams());
            }
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        }

        handleMin.addEventListener('mousedown', () => handleDrag(handleMin, true));
        handleMax.addEventListener('mousedown', () => handleDrag(handleMax, false));

        // Touch support
        function handleTouch(handle, isMin) {
            function onTouchMove(e) {
                const touch = e.touches[0];
                trackRect   = track.getBoundingClientRect();
                let x = Math.max(0, Math.min(touch.clientX - trackRect.left, trackRect.width));
                if (isMin) {
                    const maxX = parseFloat(handleMax.style.left) || trackRect.width;
                    if (x > maxX) x = maxX;
                } else {
                    const minX = parseFloat(handleMin.style.left) || 0;
                    if (x < minX) x = minX;
                }
                const value = Math.round((x / trackRect.width) * (maxValue - minValue) + minValue);
                handle.style.left = x + 'px';
                handle.setAttribute('aria-valuenow', value);
            }
            function onTouchEnd() {
                document.removeEventListener('touchmove', onTouchMove);
                document.removeEventListener('touchend', onTouchEnd);
                updatePriceValues();
                fetchResults(collectParams());
            }
            document.addEventListener('touchmove', onTouchMove, { passive: true });
            document.addEventListener('touchend', onTouchEnd);
        }

        handleMin.addEventListener('touchstart', () => handleTouch(handleMin, true), { passive: true });
        handleMax.addEventListener('touchstart', () => handleTouch(handleMax, false), { passive: true });
    }

    function updatePriceValues() {
        const sliderTrack = document.querySelector('.price-financing-slider');
        const handleMin   = document.getElementById('handle-min');
        const handleMax   = document.getElementById('handle-max');
        const minHidden   = document.getElementById('minprice');
        const maxHidden   = document.getElementById('maxprice');
        const minInput    = document.querySelector('[name="price-display-min"]');
        const maxInput    = document.querySelector('[name="price-display-max"]');
        if (! handleMin || ! handleMax) return;

        const minVal = parseInt(handleMin.getAttribute('aria-valuemin'), 10);
        const maxVal = parseInt(handleMin.getAttribute('aria-valuemax'), 10);
        const minNow = parseInt(handleMin.getAttribute('aria-valuenow'), 10);
        const maxNow = parseInt(handleMax.getAttribute('aria-valuenow'), 10);

        if (minHidden) minHidden.value = minNow;
        if (maxHidden) maxHidden.value = maxNow;
        if (minInput)  minInput.value  = '$' + minNow.toLocaleString();
        if (maxInput)  maxInput.value  = '$' + maxNow.toLocaleString();

        if (sliderTrack) {
            const minPct = ((minNow - minVal) / (maxVal - minVal)) * 100;
            const maxPct = ((maxNow - minVal) / (maxVal - minVal)) * 100;
            sliderTrack.style.background = `linear-gradient(to right,
                #ccc 0%, #ccc ${minPct}%,
                #166B87 ${minPct}%, #166B87 ${maxPct}%,
                #ccc ${maxPct}%, #ccc 100%)`;
        }
    }

    // Price input manual change
    function bindPriceInputs() {
        const minInput = document.querySelector('[name="price-display-min"]');
        const maxInput = document.querySelector('[name="price-display-max"]');
        const handleMin = document.getElementById('handle-min');
        const handleMax = document.getElementById('handle-max');
        const parsePrice = str => parseInt(str.replace(/[$,]/g, ''), 10);

        minInput?.addEventListener('change', () => {
            const val = parsePrice(minInput.value);
            if (! isNaN(val)) {
                handleMin?.setAttribute('aria-valuenow', val);
                updatePriceValues();
                fetchResults(collectParams());
            }
        });
        maxInput?.addEventListener('change', () => {
            const val = parsePrice(maxInput.value);
            if (! isNaN(val)) {
                handleMax?.setAttribute('aria-valuenow', val);
                updatePriceValues();
                fetchResults(collectParams());
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 10. SORT DROPDOWN ───────────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────
    function initSortDropdown() {
        const sortBtn    = document.getElementById('sortby');
        const sortByText = document.querySelector('[data-cy="sortby-selected"]');
        if (! sortBtn) return;

        const iconSpan = sortBtn.querySelector('span');
        sortBtn.addEventListener('click', () => {
            sortBtn.classList.toggle('show');
            iconSpan?.classList.toggle('text-primary');
            iconSpan?.classList.toggle('text-white');
        });

        // Sort items — map label to value
        const sortMap = {
            'Best Match':       'best_match',
            'Price: Lowest':    'price_asc',
            'Price: Highest':   'price_desc',
            'Newest Arrivals':  'newest',
            'Miles: Low to High': 'mileage_asc',
            'Miles: High to Low': 'mileage_desc',
            'Year: Newest':     'year_desc',
            'Year: Oldest':     'year_asc',
        };

        function createCheckmark() {
            const span = document.createElement('span');
            span.className     = 'me-2 text-primary checkmark';
            span.style.display = 'none';
            span.innerHTML     = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.285 6.709a1 1 0 0 0-1.414-1.418l-9.172 9.183-4.243-4.243a1 1 0 1 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l10-10z"/>
            </svg>`;
            return span;
        }

        // Get current sort from URL
        const currentSort = new URLSearchParams(window.location.search).get('sort') || 'best_match';

        document.querySelectorAll('.dropdown-item').forEach((item, index) => {
            const check     = createCheckmark();
            const itemLabel = item.textContent.trim();
            const itemValue = sortMap[itemLabel] || 'best_match';
            item.prepend(check);

            // Mark current active sort
            if (itemValue === currentSort || (index === 0 && currentSort === 'best_match')) {
                check.style.display    = 'inline-block';
                if (sortByText) sortByText.textContent = 'Sort by ' + itemLabel;
            }

            item.addEventListener('click', () => {
                document.querySelectorAll('.dropdown-item').forEach(i => {
                    const m = i.querySelector('.checkmark');
                    if (m) m.style.display = 'none';
                });
                check.style.display = 'inline-block';
                if (sortByText) sortByText.textContent = 'Sort by ' + itemLabel;
                sortBtn.classList.remove('show');
                if (iconSpan) {
                    iconSpan.classList.add('text-primary');
                    iconSpan.classList.remove('text-white');
                }

                // Add sort to form params and fetch
                const params = collectParams();
                params.set('sort', itemValue);
                // Remove page param on sort change
                params.delete('page');
                fetchResults(params);
            });
        });
    }


    // ─────────────────────────────────────────────────────────────────────────
    // 11. BADGE REMOVAL — click active filter badge → uncheck + re-fetch
    //     No jQuery needed — pure vanilla JS
    // ─────────────────────────────────────────────────────────────────────────
    function bindBadgeRemoval() {
        document.addEventListener('click', function (e) {
            const badge = e.target.closest('.badge-default');
            if (! badge || ! form) return;

            const key = badge.dataset.filterKey;
            const val = badge.dataset.filterVal;
            if (! key || ! val) return;

            // Uncheck the corresponding checkbox
            const checkbox = form.querySelector(`input[name="${key}[]"][value="${val}"]`);
            if (checkbox) {
                checkbox.checked = false;

                // If make unchecked — hide + clear its model list
                if (checkbox.classList.contains('make-checkbox')) {
                    const modelList = checkbox.closest('.make-item')?.querySelector('.model-list');
                    if (modelList) {
                        modelList.style.display = 'none';
                        modelList.querySelectorAll('input[type="checkbox"]').forEach(m => m.checked = false);
                    }
                }
            }

            fetchResults(collectParams());
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 12. IMAGE SLIDER — event delegation, works on AJAX-rendered cards
    // ─────────────────────────────────────────────────────────────────────────
    function initImageSlider() {
        document.addEventListener('click', function (e) {
            const leftBtn  = e.target.closest('.left-toggle');
            const rightBtn = e.target.closest('.right-toggle');

            if (leftBtn || rightBtn) {
                const container = (leftBtn || rightBtn).closest('.img-srp-container');
                if (! container) return;

                const images = Array.from(container.querySelectorAll('.img-srp'));
                if (images.length <= 1) return;

                const current = images.findIndex(img => img.classList.contains('d-block'));
                const total   = images.length;
                const next    = leftBtn
                    ? (current - 1 + total) % total
                    : (current + 1) % total;

                images.forEach(img => { img.classList.add('d-none'); img.classList.remove('d-block'); });
                images[next].classList.remove('d-none');
                images[next].classList.add('d-block');

                const dots = container.closest('.new-arrival')?.querySelectorAll('.circle-dot-icon');
                dots?.forEach((dot, i) => dot.classList.toggle('active', i === next));
            }
        });
    }

    function updateBadges() {
        const badgeContainerTitle = document.querySelector('.card-header .card-title');

        let clearBtn = badgeContainerTitle.querySelector('.clear-filters-btn');

        if (form.querySelectorAll('input[type="checkbox"]:checked').length > 0) {

            if (!clearBtn) {
                const btn = document.createElement('a');
                btn.href = "javascript:void(0)";
                btn.innerText = "Clear Filters";
                btn.className = "float-end font-weight-normal text-14 cursor-pointer text-primary clear-filters-btn";
                btn.onclick = () => window.location.href = window.location.pathname;

                badgeContainerTitle.appendChild(btn);
            }

        } else {
            if (clearBtn) clearBtn.remove();
        }

        const badgeContainer = document.querySelector('.filter-badges');
        if (! badgeContainer || ! form) return;

        // Remove existing badges
        badgeContainer.querySelectorAll('.badge-default').forEach(b => b.remove());

        // Build badges from currently checked checkboxes
        form.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
            const name  = cb.name.replace('[]', '');
            const value = cb.value;
            const label = cb.closest('.make-item')
                ?.querySelector('label')
                ?.textContent.trim().replace(/\s*\(\d+\)$/, ''); // remove count

            const badge = document.createElement('div');
            badge.className = 'd-inline-block badge-default px-2 py-0 me-2 rounded border my-1 cursor-pointer';
            badge.dataset.filterKey = name;
            badge.dataset.filterVal = value;
            badge.innerHTML = `
                <span class="small">${label || value}</span>
                <span class="d-inline-block ms-2 float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 16 16" fill="#166B87">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                    </svg>
                </span>`;
            badgeContainer.appendChild(badge);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 13. INIT
    // ─────────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        restoreDropdownState();
        bindDropdownToggles();
        bindMakeCheckboxes();
        bindFilterInputs();
        bindPagination();
        initPriceSlider();
        updatePriceValues();
        bindPriceInputs();
        initSortDropdown();
        bindBadgeRemoval();
        updateBadges();
        initImageSlider();

        // Prevent full form submit — always use AJAX
        form?.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchResults(collectParams());
        });
    });

}());
