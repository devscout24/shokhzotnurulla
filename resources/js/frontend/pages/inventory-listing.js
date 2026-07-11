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
    // 9. PRICE / PAYMENT SLIDERS ─────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────

    // --- helpers ---
    function fillTrack(trackId, minId, maxId) {
        const track    = document.getElementById(trackId);
        const inputMin = document.getElementById(minId);
        const inputMax = document.getElementById(maxId);
        if (! track || ! inputMin || ! inputMax) return null;

        const min = Number(inputMin.min);
        const max = Number(inputMin.max);
        const range = max - min;
        if (range <= 0) return null;

        const low  = Math.min(Number(inputMin.value), Number(inputMax.value));
        const high = Math.max(Number(inputMin.value), Number(inputMax.value));
        const lowPct  = ((low - min) / range) * 100;
        const highPct = ((high - min) / range) * 100;

        track.style.background = `linear-gradient(to right,
            #ccc 0%, #ccc ${lowPct}%,
            #166B87 ${lowPct}%, #166B87 ${highPct}%,
            #ccc ${highPct}%, #ccc 100%)`;

        return { low, high, min, max, range };
    }

    function clampPair(inputMin, inputMax) {
        const low  = Math.min(Number(inputMin.value), Number(inputMax.value));
        const high = Math.max(Number(inputMin.value), Number(inputMax.value));
        inputMin.value = low;
        inputMax.value = high;
    }

    // --- price track fill + histogram ---
    function updateTrackFill() {
        const info = fillTrack('dual-range-track', 'price-range-min', 'price-range-max');
        if (! info) return;

        // Update histogram — each of 10 bars covers 10% of the range
        const barCount = 10;
        const barStep  = info.range / barCount;
        document.querySelectorAll('.histogram-bar').forEach(bar => {
            const idx      = parseInt(bar.dataset.barIdx, 10);
            const barMin   = info.min + idx * barStep;
            const barMax   = barMin + barStep;
            const isActive = barMax > info.low && barMin < info.high;
            bar.classList.toggle('in-range', isActive);
        });
    }

    // --- payment track fill ---
    function updatePaymentTrackFill() {
        fillTrack('payment-range-track', 'payment-range-min', 'payment-range-max');
    }

    // --- sync hidden/display inputs ---
    function syncPriceInputs() {
        const inputMin  = document.getElementById('price-range-min');
        const inputMax  = document.getElementById('price-range-max');
        const minHidden = document.getElementById('minprice');
        const maxHidden = document.getElementById('maxprice');
        const minDisp   = document.querySelector('[name="price-display-min"]');
        const maxDisp   = document.querySelector('[name="price-display-max"]');
        if (! inputMin || ! inputMax) return;

        const low  = Math.min(Number(inputMin.value), Number(inputMax.value));
        const high = Math.max(Number(inputMin.value), Number(inputMax.value));

        if (minHidden) minHidden.value = low;
        if (maxHidden) maxHidden.value = high;
        if (minDisp)   minDisp.value   = '$' + low.toLocaleString();
        if (maxDisp)   maxDisp.value   = '$' + high.toLocaleString();
    }

    function syncPaymentInputs() {
        const inputMin = document.getElementById('payment-range-min');
        const inputMax = document.getElementById('payment-range-max');
        const minDisp  = document.querySelector('[name="payment-display-min"]');
        const maxDisp  = document.querySelector('[name="payment-display-max"]');
        if (! inputMin || ! inputMax) return;

        const low  = Math.min(Number(inputMin.value), Number(inputMax.value));
        const high = Math.max(Number(inputMin.value), Number(inputMax.value));

        if (minDisp) minDisp.value = '$' + low.toLocaleString();
        if (maxDisp) maxDisp.value = '$' + high.toLocaleString();
    }

    // --- payment ↔ price conversion ---
    function getSidebarRate() {
        const el = document.getElementById('sidebar-rate');
        return el ? Number(el.value) : 6.79;
    }

    function paymentToPrice(payment) {
        const rate = getSidebarRate();
        const monthlyRate = (rate / 100) / 12;
        if (monthlyRate <= 0) return Math.round(payment * 60);
        return Math.round(payment * (1 - Math.pow(1 + monthlyRate, -60)) / monthlyRate);
    }

    function priceToPayment(price) {
        const rate = getSidebarRate();
        const monthlyRate = (rate / 100) / 12;
        if (monthlyRate <= 0) return Math.round(price / 60);
        return Math.round(price * monthlyRate / (1 - Math.pow(1 + monthlyRate, -60)));
    }

    // --- payment slider → feed hidden price inputs + AJAX ---
    function syncPaymentToPrice() {
        const inputMin = document.getElementById('payment-range-min');
        const inputMax = document.getElementById('payment-range-max');
        const minHidden = document.getElementById('minprice');
        const maxHidden = document.getElementById('maxprice');
        if (! inputMin || ! inputMax) return;

        const low  = Math.min(Number(inputMin.value), Number(inputMax.value));
        const high = Math.max(Number(inputMin.value), Number(inputMax.value));

        const priceLow  = paymentToPrice(low);
        const priceHigh = paymentToPrice(high);

        if (minHidden) minHidden.value = priceLow;
        if (maxHidden) maxHidden.value = priceHigh;
    }

    function initPaymentSlider() {
        const inputMin = document.getElementById('payment-range-min');
        const inputMax = document.getElementById('payment-range-max');
        if (! inputMin || ! inputMax) return;

        function onInput() {
            clampPair(inputMin, inputMax);
            updatePaymentTrackFill();
            syncPaymentInputs();
        }

        function onChange() {
            clampPair(inputMin, inputMax);
            updatePaymentTrackFill();
            syncPaymentInputs();
            syncPaymentToPrice();
            fetchResults(collectParams());
        }

        inputMin.addEventListener('input', onInput);
        inputMax.addEventListener('input', onInput);
        inputMin.addEventListener('change', onChange);
        inputMax.addEventListener('change', onChange);
    }

    // --- price slider (original) ---
    function initPriceSlider() {
        const inputMin = document.getElementById('price-range-min');
        const inputMax = document.getElementById('price-range-max');
        if (! inputMin || ! inputMax) return;

        function onInput() {
            clampPair(inputMin, inputMax);
            updateTrackFill();
            syncPriceInputs();
        }

        function onChange() {
            clampPair(inputMin, inputMax);
            updateTrackFill();
            syncPriceInputs();
            fetchResults(collectParams());
        }

        inputMin.addEventListener('input', onInput);
        inputMax.addEventListener('input', onInput);
        inputMin.addEventListener('change', onChange);
        inputMax.addEventListener('change', onChange);
    }

    // --- display input manual edit (price tab) ---
    function bindPriceInputs() {
        const inputMin  = document.getElementById('price-range-min');
        const inputMax  = document.getElementById('price-range-max');
        const minDisp   = document.querySelector('[name="price-display-min"]');
        const maxDisp   = document.querySelector('[name="price-display-max"]');
        if (! inputMin || ! inputMax) return;

        const parsePrice = str => parseInt(str.replace(/[$,]/g, ''), 10);

        minDisp?.addEventListener('change', () => {
            const val = parsePrice(minDisp.value);
            if (! isNaN(val)) { inputMin.value = val; clampAndFire(); }
        });
        maxDisp?.addEventListener('change', () => {
            const val = parsePrice(maxDisp.value);
            if (! isNaN(val)) { inputMax.value = val; clampAndFire(); }
        });

        function clampAndFire() {
            clampPair(inputMin, inputMax);
            updateTrackFill();
            syncPriceInputs();
            fetchResults(collectParams());
        }
    }

    // --- display input manual edit (payment tab) ---
    function bindPaymentInputs() {
        const inputMin = document.getElementById('payment-range-min');
        const inputMax = document.getElementById('payment-range-max');
        const minDisp  = document.querySelector('[name="payment-display-min"]');
        const maxDisp  = document.querySelector('[name="payment-display-max"]');
        if (! inputMin || ! inputMax) return;

        const parseVal = str => parseInt(str.replace(/[$,]/g, ''), 10);

        minDisp?.addEventListener('change', () => {
            const val = parseVal(minDisp.value);
            if (! isNaN(val)) { inputMin.value = val; clampAndFire(); }
        });
        maxDisp?.addEventListener('change', () => {
            const val = parseVal(maxDisp.value);
            if (! isNaN(val)) { inputMax.value = val; clampAndFire(); }
        });

        function clampAndFire() {
            clampPair(inputMin, inputMax);
            updatePaymentTrackFill();
            syncPaymentInputs();
            syncPaymentToPrice();
            fetchResults(collectParams());
        }
    }

    // --- Price / Payment tab toggle ---
    function initPricePaymentToggle() {
        const priceRadio   = document.getElementById('shop_price');
        const paymentRadio = document.getElementById('shop_payment');
        const pricePanel   = document.getElementById('tab-price');
        const paymentPanel = document.getElementById('tab-payment');
        if (! priceRadio || ! paymentRadio) return;

        priceRadio.addEventListener('change', () => {
            if (! priceRadio.checked) return;
            pricePanel?.classList.remove('d-none');
            paymentPanel?.classList.add('d-none');
            updateTrackFill();
            syncPriceInputs();
        });

        paymentRadio.addEventListener('change', () => {
            if (! paymentRadio.checked) return;
            paymentPanel?.classList.remove('d-none');
            pricePanel?.classList.add('d-none');
            updatePaymentTrackFill();
            syncPaymentInputs();
        });
    }

    // --- "Adjust Terms" link: feed correct price depending on active tab ---
    function initSidebarGetEstimateLink() {
        const link = document.querySelector('[data-sidebar-link]');
        if (! link) return;
        link.addEventListener('click', () => {
            const priceRadio = document.getElementById('shop_price');
            if (priceRadio?.checked) {
                const maxInput = document.getElementById('price-range-max');
                const minInput = document.getElementById('price-range-min');
                if (maxInput) link.dataset.vehiclePrice = Math.max(Number(maxInput.value), Number(minInput?.value || 0));
            } else {
                const maxInput = document.getElementById('payment-range-max');
                const minInput = document.getElementById('payment-range-min');
                if (maxInput) {
                    const maxPayment = Math.max(Number(maxInput.value), Number(minInput?.value || 0));
                    link.dataset.vehiclePrice = paymentToPrice(maxPayment);
                }
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 9b. GVWR (WEIGHT) SLIDER ──────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────

    function updateGvwrTrackFill() {
        fillTrack('gvwr-range-track', 'gvwr-range-min', 'gvwr-range-max');
    }

    function syncGvwrInputs() {
        const inputMin = document.getElementById('gvwr-range-min');
        const inputMax = document.getElementById('gvwr-range-max');
        const minHidden = document.getElementById('mingvwr');
        const maxHidden = document.getElementById('maxgvwr');
        const minDisp   = document.querySelector('[name="gvwr-display-min"]');
        const maxDisp   = document.querySelector('[name="gvwr-display-max"]');
        if (! inputMin || ! inputMax) return;

        const low  = Math.min(Number(inputMin.value), Number(inputMax.value));
        const high = Math.max(Number(inputMin.value), Number(inputMax.value));

        // Only send hidden params when range is narrowed from full range
        const isFullRange = (low === Number(inputMin.min) && high === Number(inputMin.max));
        if (minHidden) minHidden.value = isFullRange ? '' : low;
        if (maxHidden) maxHidden.value = isFullRange ? '' : high;
        if (minDisp)   minDisp.value   = low.toLocaleString();
        if (maxDisp)   maxDisp.value   = high.toLocaleString();
    }

    function initGvwrSlider() {
        const inputMin = document.getElementById('gvwr-range-min');
        const inputMax = document.getElementById('gvwr-range-max');
        if (! inputMin || ! inputMax) return;

        function onInput() {
            clampPair(inputMin, inputMax);
            updateGvwrTrackFill();
            syncGvwrInputs();
        }

        function onChange() {
            clampPair(inputMin, inputMax);
            updateGvwrTrackFill();
            syncGvwrInputs();
            fetchResults(collectParams());
        }

        inputMin.addEventListener('input', onInput);
        inputMax.addEventListener('input', onInput);
        inputMin.addEventListener('change', onChange);
        inputMax.addEventListener('change', onChange);
    }

    function bindGvwrInputs() {
        const inputMin = document.getElementById('gvwr-range-min');
        const inputMax = document.getElementById('gvwr-range-max');
        const minDisp  = document.querySelector('[name="gvwr-display-min"]');
        const maxDisp  = document.querySelector('[name="gvwr-display-max"]');
        if (! inputMin || ! inputMax) return;

        const parseVal = str => parseInt(str.replace(/,/g, ''), 10);

        minDisp?.addEventListener('change', () => {
            const val = parseVal(minDisp.value);
            if (! isNaN(val)) { inputMin.value = val; clampAndFire(); }
        });
        maxDisp?.addEventListener('change', () => {
            const val = parseVal(maxDisp.value);
            if (! isNaN(val)) { inputMax.value = val; clampAndFire(); }
        });

        function clampAndFire() {
            clampPair(inputMin, inputMax);
            updateGvwrTrackFill();
            syncGvwrInputs();
            fetchResults(collectParams());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 10. SORT DROPDOWN ───────────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────
    function initSortDropdown() {
        const sortBtn    = document.getElementById('sortby');
        const sortByText = document.querySelector('[data-cy="sortby-selected"]') || sortBtn?.querySelector('strong');
        if (! sortBtn) return;

        const dropdown = sortBtn.closest('.dropdown');
        if (! dropdown) return;

        const iconSpan = sortBtn.querySelector('span');
        sortBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('show');
            // Close all other open dropdowns first
            document.querySelectorAll('.dropdown.show').forEach(d => d.classList.remove('show'));
            if (! isOpen) dropdown.classList.add('show');
            iconSpan?.classList.toggle('text-primary', !isOpen);
            iconSpan?.classList.toggle('text-white', isOpen);
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (! dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
                iconSpan?.classList.add('text-primary');
                iconSpan?.classList.remove('text-white');
            }
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
            'Make: A-Z':        'make_asc',
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

        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach((item, index) => {
            const check     = createCheckmark();
            const itemLabel = item.textContent.trim();
            const itemValue = sortMap[itemLabel] || 'best_match';
            item.prepend(check);
            item.style.cursor = 'pointer';

            // Mark current active sort
            if (itemValue === currentSort || (index === 0 && currentSort === 'best_match')) {
                check.style.display    = 'inline-block';
                item.classList.add('fw-bold', 'bg-lighter');
                if (sortByText) sortByText.textContent = 'Sort by ' + itemLabel;
            }

            item.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const url = new URL(window.location.href);
                url.searchParams.set('sort', itemValue);
                url.searchParams.delete('page');
                window.location.href = url.toString();
            });
        });
    }


    // ─────────────────────────────────────────────────────────────────────────
    // 11. BADGE REMOVAL — click active filter badge → uncheck + re-fetch
    //     No jQuery needed — pure vanilla JS
    // ─────────────────────────────────────────────────────────────────────────
    function bindBadgeRemoval() {
        document.addEventListener('click', function (e) {
            const badge = e.target.closest('.badge-default, .filter-chip');
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
    // 14. FILTER SEARCH — type to filter checkbox options within a dropdown ───
    // ─────────────────────────────────────────────────────────────────────────
    function bindFilterSearch() {
        document.querySelectorAll('.filter-search').forEach(function (input) {
            input.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                const content = this.closest('.dropdown-content');
                if (! content) return;

                content.querySelectorAll('.make-item').forEach(function (item) {
                    const label = item.querySelector('label');
                    const text = label ? label.textContent.toLowerCase() : '';
                    item.style.display = (! q || text.includes(q)) ? '' : 'none';
                });
            });

            // Prevent dropdown toggle when clicking inside search input
            input.addEventListener('click', function (e) { e.stopPropagation(); });
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
        syncPriceInputs();
        updateTrackFill();
        bindPriceInputs();
        initPaymentSlider();
        syncPaymentInputs();
        updatePaymentTrackFill();
        bindPaymentInputs();
        initPricePaymentToggle();
        initSidebarGetEstimateLink();
        initGvwrSlider();
        syncGvwrInputs();
        updateGvwrTrackFill();
        bindGvwrInputs();
        initSortDropdown();
        bindBadgeRemoval();
        updateBadges();
        initImageSlider();
        bindFilterSearch();

        // Prevent full form submit — always use AJAX
        form?.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchResults(collectParams());
        });
    });

}());
