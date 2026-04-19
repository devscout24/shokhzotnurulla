/**
 * Schedule a Test Drive — Calendar + AJAX Form Submission
 * Route: POST /forms/schedule-test-drive  →  frontend.forms.schedule-test-drive
 *
 * Lives in: resources/js/frontend/pages/schedule-test-drive.js
 * Loaded via: vehicle-detail.blade.php @push('page-scripts')
 */

(function () {

    // ── Elements ──────────────────────────────────────────────────────────────
    var offcanvasEl    = document.getElementById('scheduleTest');
    var daysContainer  = document.getElementById('std-days-container');
    var prevWeekBtn    = document.getElementById('std-prev-week');
    var nextWeekBtn    = document.getElementById('std-next-week');
    var selectDateDiv  = document.getElementById('std-select-date');
    var selectTimeDiv  = document.getElementById('std-select-time');
    var selectedLabel  = document.getElementById('std-selected-day-label');
    var timeSelect     = document.getElementById('std-time-select');
    var backToDateBtn  = document.getElementById('std-back-to-date');
    var continueStep1  = document.getElementById('std-continue-step1');

    var step1Div       = document.getElementById('std-step-1');
    var step2Div       = document.getElementById('std-step-2');
    var successDiv     = document.getElementById('std-success');
    var header1        = document.getElementById('std-header-1');
    var header2        = document.getElementById('std-header-2');

    var form           = document.getElementById('std-form');
    var submitBtn      = document.getElementById('std-submit-btn');

    var hiddenDate     = document.getElementById('std-hidden-date');
    var hiddenDayLabel = document.getElementById('std-hidden-day-label');
    var hiddenTime     = document.getElementById('std-hidden-time');
    var hiddenVehicle  = document.getElementById('std-hidden-vehicle-id');

    if (!offcanvasEl || !form) return;

    // ── State ─────────────────────────────────────────────────────────────────
    var currentStartDate  = new Date();
    currentStartDate.setHours(0, 0, 0, 0);

    var selectedDate     = null;
    var selectedDayLabel = '';
    var selectedDateStr  = '';
    var formEntryId      = null;

    // ── Calendar: get 6 days from start ──────────────────────────────────────
    function getWeekDates(startDate) {
        var dates = [];
        var d = new Date(startDate);
        for (var i = 0; i < 6; i++) {
            dates.push(new Date(d));
            d.setDate(d.getDate() + 1);
        }
        return dates;
    }

    // ── Calendar: format date to YYYY-MM-DD ──────────────────────────────────
    function formatDate(date) {
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    // ── Calendar: render days ────────────────────────────────────────────────
    function renderDays() {
        daysContainer.innerHTML = '';
        var dates = getWeekDates(currentStartDate);

        dates.forEach(function (date) {
            var isSelected = selectedDate && formatDate(date) === formatDate(selectedDate);
            var dayName    = date.toLocaleDateString('en-US', { weekday: 'long' });
            var dayNum     = date.getDate();
            var monthName  = date.toLocaleDateString('en-US', { month: 'long' });

            var dayDiv = document.createElement('div');
            dayDiv.className = 'p-3 hover-light font-weight-bold border-bottom border-theme border-thick text-center allowed col-sm-2 col-4'
                + (isSelected ? ' std-day-selected' : '');
            dayDiv.dataset.date     = formatDate(date);
            dayDiv.dataset.dayLabel = dayName + ', ' + monthName + ' ' + dayNum + ', ' + date.getFullYear();

            dayDiv.innerHTML = '<div class="day-name">' + dayName + '</div>'
                + '<div class="h1 my-2 day-number' + (isSelected ? ' selected' : '') + '">' + dayNum + '</div>'
                + '<div class="month">' + (isSelected ? '' : monthName) + '</div>'
                + (isSelected
                    ? '<span class="d-inline-block check-icon text-primary">'
                        + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#166B87" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
                        + '<circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg></span>'
                    : '');

            dayDiv.addEventListener('click', function () {
                // Save selected date
                selectedDate     = date;
                selectedDayLabel = dayDiv.dataset.dayLabel;
                selectedDateStr  = dayDiv.dataset.date;

                // Update hidden fields
                hiddenDate.value     = selectedDateStr;
                hiddenDayLabel.value = selectedDayLabel;

                // Update label in time section
                selectedLabel.textContent = selectedDayLabel;

                // Reset time selection
                timeSelect.value = '';
                hiddenTime.value = '';

                // Render days again to show selected state properly (fixes multiple tick bug)
                renderDays();

                // Show time selection
                selectDateDiv.classList.add('hidden');
                selectTimeDiv.classList.remove('hidden');
            });

            daysContainer.appendChild(dayDiv);
        });

        // Disable prev if at current week
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        prevWeekBtn.disabled = currentStartDate <= today;
    }

    // ── Week navigation ───────────────────────────────────────────────────────
    nextWeekBtn.addEventListener('click', function () {
        currentStartDate.setDate(currentStartDate.getDate() + 6);
        renderDays();
    });

    prevWeekBtn.addEventListener('click', function () {
        currentStartDate.setDate(currentStartDate.getDate() - 6);
        renderDays();
    });

    // ── Back to date selection ────────────────────────────────────────────────
    backToDateBtn.addEventListener('click', function () {
        selectTimeDiv.classList.add('hidden');
        selectDateDiv.classList.remove('hidden');
    });

    // ── Continue Step 1 → Step 2 ─────────────────────────────────────────────
    continueStep1.addEventListener('click', function () {
        if (!timeSelect.value) {
            timeSelect.classList.add('is-invalid');
            return;
        }

        timeSelect.classList.remove('is-invalid');
        hiddenTime.value = timeSelect.value;

        // Set vehicle ID
        hiddenVehicle.value = document.getElementById('td_vehicle_id')?.textContent?.trim() ?? '';

        // Show step 2, hide step 1
        step1Div.classList.add('hidden');
        step2Div.classList.remove('hidden');

        // Update wizard headers
        header1.querySelector('b').classList.remove('text-primary');
        header1.querySelector('b').classList.add('text-muted');
        header1.querySelector('.wizardstep-indicator').innerHTML = '<button class="btn btn-sm btn-link text-muted p-0 std-edit-step1">Edit</button>';

        header2.querySelector('b').classList.remove('text-muted');
        header2.querySelector('b').classList.add('text-primary');
        header2.classList.add('wizardstep-active');
    });

    // ── Edit Step 1 ───────────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('std-edit-step1')) return;

        step2Div.classList.add('hidden');
        step1Div.classList.remove('hidden');

        // If day was selected, show time selection directly
        if (selectedDate) {
            selectDateDiv.classList.add('hidden');
            selectTimeDiv.classList.remove('hidden');
        }

        // Restore wizard header 1
        header1.querySelector('b').classList.remove('text-muted');
        header1.querySelector('b').classList.add('text-primary');
        header1.querySelector('.wizardstep-indicator').textContent = '1 / 2';
        header1.classList.add('wizardstep-active');

        header2.querySelector('b').classList.remove('text-primary');
        header2.querySelector('b').classList.add('text-muted');
        header2.classList.remove('wizardstep-active');
    });

    // ── AJAX Helpers ──────────────────────────────────────────────────────────
    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
        form.querySelectorAll('.invalid-feedback').forEach(function (el) { el.remove(); });
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(function (_ref) {
            var field    = _ref[0];
            var messages = _ref[1];
            var input = form.querySelector('[name="' + field + '"]');
            if (!input) return;

            input.classList.add('is-invalid');
            var feedback = document.createElement('div');
            feedback.className   = 'invalid-feedback';
            feedback.textContent = messages[0];

            if (input.type === 'radio') {
                var container = input.closest('.mb-3, .mb-md-4');
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
        step2Div.classList.add('hidden');
        successDiv.classList.remove('hidden');

        header2.querySelector('b').classList.remove('text-primary');
        header2.querySelector('b').classList.add('text-muted');
        header2.classList.remove('wizardstep-active');

        if (window.NpsModule) {
            window.NpsModule.init({
                starRatingId: 'std-star-rating',
                npsOptionsId: 'stdNpsOptions',
                submitBtnSel: '.std-nps-submit-btn',
                getNpsRoute:  function () {
                    return (window.npsRouteTemplate || '').replace('__id__', formEntryId);
                },
            });
        }
    }

    // ── Submit Handler ────────────────────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        clearErrors();
        setLoading();

        fetch(form.dataset.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        })
        .then(function (r) { return r.json().then(function (json) { return { status: r.status, json: json }; }); })
        .then(function (_ref) {
            var status = _ref.status;
            var json   = _ref.json;

            if (status === 200 && json.success) {
                formEntryId = json.form_entry_id ?? null;
                showSuccess();
                return;
            }

            if (status === 422 && json.errors) {
                showErrors(json.errors);
                resetButton();
                return;
            }

            resetButton();
            console.error('Schedule test drive error:', json);
        })
        .catch(function (err) {
            resetButton();
            console.error('Schedule test drive network error:', err);
        });
    });

    // ── NPS Stars ─────────────────────────────────────────────────────────────
    var stars      = document.querySelectorAll('#std-star-rating .star');
    var npsOptions = document.getElementById('stdNpsOptions');

    stars.forEach(function (star, idx) {
        star.addEventListener('click', function () {
            stars.forEach(function (s, i) { s.classList.toggle('filled', i <= idx); });
            if (npsOptions) npsOptions.classList.remove('hidden');
        });
    });

    // ── Get Approved CTA ──────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.std-get-approved-btn')) return;

        var stdOffcanvas = document.getElementById('scheduleTest');
        if (stdOffcanvas) bootstrap.Offcanvas.getInstance(stdOffcanvas)?.hide();

        var getApproved = document.getElementById('getApproved');
        if (getApproved) new bootstrap.Offcanvas(getApproved).show();
    });

    // ── Reset on offcanvas close ──────────────────────────────────────────────
    offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
        // Reset state
        selectedDate     = null;
        selectedDayLabel = '';
        selectedDateStr  = '';

        // Reset calendar to today
        currentStartDate = new Date();
        currentStartDate.setHours(0, 0, 0, 0);
        renderDays();

        // Reset date/time view
        selectDateDiv.classList.remove('hidden');
        selectTimeDiv.classList.add('hidden');
        timeSelect.value = '';

        // Reset steps visibility
        step1Div.classList.remove('hidden');
        step2Div.classList.add('hidden');
        successDiv.classList.add('hidden');

        // Reset wizard headers
        header1.querySelector('b').classList.add('text-primary');
        header1.querySelector('b').classList.remove('text-muted');
        header1.querySelector('.wizardstep-indicator').textContent = '1 / 2';
        header1.classList.add('wizardstep-active');

        header2.querySelector('b').classList.add('text-muted');
        header2.querySelector('b').classList.remove('text-primary');
        header2.classList.remove('wizardstep-active');

        // Reset form
        form.reset();
        clearErrors();
        resetButton();

        // Reset NPS
        stars.forEach(function (s) { s.classList.remove('filled'); });
        if (npsOptions) npsOptions.classList.add('hidden');
    });

    // ── Initial render ────────────────────────────────────────────────────────
    selectTimeDiv.classList.add('hidden');
    renderDays();

})();
