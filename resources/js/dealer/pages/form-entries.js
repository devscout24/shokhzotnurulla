(function () {

    // ── Config ────────────────────────────────────────────────────────────────
    var routes = window.fsRoutes || {};
    var csrf   = window.fsCsrf  || '';

    // ── Elements ──────────────────────────────────────────────────────────────
    var btnDelete   = document.getElementById('btnDelete');
    var btnUnread   = document.getElementById('btnMarkUnread');
    var btnExport   = document.getElementById('btnExport');
    var selectAll   = document.getElementById('fsSelectAll');
    var tableBody   = document.getElementById('fsTableBody');
    var fsCanvas        = document.getElementById('fsCanvas');
    var fsCanvasOverlay = document.getElementById('fsCanvasOverlay');

    // ── Helpers ───────────────────────────────────────────────────────────────
    function getChecked()    { return document.querySelectorAll('.fs-row-cb:checked'); }
    function getVisibleCbs() { return document.querySelectorAll('.fs-row .fs-row-cb'); }
    function getCheckedIds() { return Array.from(getChecked()).map(function (cb) { return cb.value; }); }

    function escHtml(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function displayVal(v) {
        return (!v || v === 'null' || String(v).trim() === '') ? null : v;
    }

    // ── Readable value helpers ────────────────────────────────────────────────
    function yesNo(v) {
        if (!v) return null;
        return v === 'yes' ? 'Yes' : v === 'no' ? 'No' : v;
    }

    function conditionLabel(v) {
        if (!v) return null;
        var map = {
            'new':     'Like-new condition with little signs of wear or tear',
            'average': 'Average condition with normal wear and tear (dents, dings, scratches)',
            'poor':    'Visible rust, cosmetic damage, or signs of vehicle repainting',
        };
        return map[v] || v;
    }

    // ── Button state ──────────────────────────────────────────────────────────
    function updateBtns() {
        var n   = getChecked().length;
        var vis = getVisibleCbs().length;
        btnDelete.classList.toggle('enabled', n > 0);
        btnUnread.classList.toggle('enabled', n > 0);
        selectAll.indeterminate = n > 0 && n < vis;
        selectAll.checked       = vis > 0 && n === vis;
    }

    // ── Checkboxes ────────────────────────────────────────────────────────────
    tableBody.addEventListener('change', function (e) {
        if (!e.target.classList.contains('fs-row-cb')) return;
        e.target.closest('tr').classList.toggle('fs-row-selected', e.target.checked);
        updateBtns();
    });

    selectAll.addEventListener('change', function () {
        getVisibleCbs().forEach(function (cb) {
            cb.checked = selectAll.checked;
            cb.closest('tr').classList.toggle('fs-row-selected', selectAll.checked);
        });
        updateBtns();
    });

    // ── Tabs → server reload ──────────────────────────────────────────────────
    document.querySelectorAll('.fs-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var url = new URL(window.location.href);
            url.searchParams.set('tab', tab.dataset.tab);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    });

    // ── Search → debounced server reload ──────────────────────────────────────
    var searchTimer;
    document.getElementById('fsSearch').addEventListener('input', function () {
        var val = this.value;
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            var url = new URL(window.location.href);
            val.trim() ? url.searchParams.set('search', val.trim())
                       : url.searchParams.delete('search');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }, 500);
    });

    // ── Filter by Form → server reload ───────────────────────────────────────
    var fsFilterBtn      = document.getElementById('fsFilterBtn');
    var fsFilterDropdown = document.getElementById('fsFilterDropdown');
    var fsFilterChevron  = document.getElementById('fsFilterChevron');

    fsFilterBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = fsFilterDropdown.classList.toggle('open');
        fsFilterBtn.classList.toggle('open', isOpen);
        fsFilterChevron.style.transform = isOpen ? 'rotate(180deg)' : '';
    });

    document.querySelectorAll('.fs-filter-item').forEach(function (item) {
        item.addEventListener('click', function () {
            var url = new URL(window.location.href);
            item.dataset.form ? url.searchParams.set('form_type', item.dataset.form)
                              : url.searchParams.delete('form_type');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    });

    document.addEventListener('click', function (e) {
        if (!fsFilterBtn.contains(e.target)) {
            fsFilterDropdown.classList.remove('open');
            fsFilterBtn.classList.remove('open');
            fsFilterChevron.style.transform = '';
        }
    });

    // ── Export ────────────────────────────────────────────────────────────────
    btnExport.addEventListener('click', function () {
        window.location.href = routes.export;
    });

    // ── Mark as Unread → AJAX ─────────────────────────────────────────────────
    btnUnread.addEventListener('click', function () {
        var ids = getCheckedIds();
        if (!ids.length) return;

        fetch(routes.bulkRead, {
            method:  'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body:    JSON.stringify({ ids: ids }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) { if (data.success) window.location.reload(); });
    });

    // ── Delete → AJAX ─────────────────────────────────────────────────────────
    btnDelete.addEventListener('click', function () {
        var ids = getCheckedIds();
        if (!ids.length) return;
        if (!confirm('Delete ' + ids.length + ' submission(s)? This cannot be undone.')) return;

        fetch(routes.bulkDestroy, {
            method:  'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body:    JSON.stringify({ ids: ids }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) { if (data.success) window.location.reload(); });
    });

    // ── Canvas: build sections from API data ──────────────────────────────────
    function buildSections(entry) {
        var html     = '';
        var data     = entry.data     || {};
        var formType = entry.form_type;
        var sections = [];

        if (formType === 'trade_in') {
            var v  = data.vehicle   || {};
            var c  = data.condition || {};
            var ct = data.contact   || {};

            // ── Vehicle Information ───────────────────────────────────────────
            var vehicleFields = [
                { label: 'Input Method',     value: v.input_method === 'vin' ? 'VIN Number' : 'Make & Model' },
                { label: 'Year',             value: v.year },
                { label: 'Make',             value: v.make },
                { label: 'Model',            value: v.model },
                { label: 'Trim',             value: v.trim },
                { label: 'Body Style',       value: v.body },
                { label: 'Engine',           value: v.engine },
                { label: 'Drivetrain',       value: v.drivetrain },
                { label: 'VIN',              value: v.vin },
                { label: 'Mileage',          value: c.mileage },
                { label: 'Vehicle zip code', value: c.postal_code },
                { label: 'Exterior Color',   value: c.exterior_color },
                { label: 'Interior Color',   value: c.interior_color },
                { label: 'Number of keys',   value: c.keys },
                { label: 'Loan or lease',    value: c.ownership },
            ];

            // Conditional fields — only if value exists
            if (displayVal(c.lienholder)) {
                vehicleFields.push({ label: 'Lienholder', value: c.lienholder });
            }
            if (displayVal(c.remaining_balance)) {
                vehicleFields.push({ label: 'Remaining balance', value: c.remaining_balance });
            }

            sections.push({ title: 'Vehicle Information', fields: vehicleFields });

            // ── History & Condition ───────────────────────────────────────────
            var conditionFields = [
                { label: 'How would you rate your vehicle overall?',       value: conditionLabel(c.overall_condition) },
                { label: 'Does the vehicle have a clean title?',           value: yesNo(c.clean_title) },
                { label: 'Does your vehicle run and drive?',               value: yesNo(c.run_drive) },
                { label: 'Has your vehicle been in an accident?',          value: yesNo(c.accident) },
                { label: 'Are there any active warning or service lights?',value: yesNo(c.warning_lights) },
                { label: 'Has the vehicle been smoked in?',                value: yesNo(c.smoked_in) },
                { label: 'Does your vehicle have any damage?',             value: Array.isArray(c.damage) ? c.damage.join(', ') : c.damage },
            ];

            if (displayVal(c.tire_condition)) {
                conditionFields.push({ label: 'What condition are your tires?', value: c.tire_condition });
            }

            sections.push({ title: 'History & Condition', fields: conditionFields });

            // ── Contact Information ───────────────────────────────────────────
            var contactFields = [
                { label: 'First Name',                           value: entry.first_name },
                { label: 'Last Name',                            value: entry.last_name },
                { label: 'Email Address',                        value: entry.email },
                { label: 'Phone Number',                         value: entry.phone },
                { label: 'What is the best way to contact you?', value: ct.commpref },
            ];

            if (displayVal(ct.comment)) {
                contactFields.push({ label: 'What questions can we answer for you?', value: ct.comment });
            }

            sections.push({ title: 'Contact Information', fields: contactFields });

        } else if (formType === 'get_approved') {
            var b  = data.borrower   || {};
            var co = data.coborrower || null;
            var isJoint = co !== null;

            // ── Housing label helper ──────────────────────────────────────────
            function housingLabel(v) {
                if (!v) return null;
                var map = {
                    'Rent':             'Rented',
                    'Own':              'Mortgaged',
                    'Own_Freeandclear': 'Owned (free & clear)',
                    'Other':            'Other',
                };
                return map[v] || v;
            }

            // ── Section title helper ──────────────────────────────────────────
            // var typeLabel = isJoint ? 'Joint Borrower' : 'Single Borrower';
            var typeLabel = 'Single Borrower';

            // 1 — Contact
            sections.push({ title: 'Hard Credit App (' + typeLabel + ')', fields: [
                { label: 'First Name',                           value: entry.first_name },
                { label: 'Last Name',                            value: entry.last_name },
                { label: 'Email Address',                        value: entry.email },
                { label: 'Phone Number',                         value: entry.phone },
                { label: 'What is the best way to contact you?', value: b.commpref },
            ]});

            // 2 — Address
            sections.push({ title: 'Hard Credit App (Single - Address)', fields: [
                { label: 'Street Address',                  value: b.address },
                { label: 'City',                            value: b.city },
                { label: 'State',                           value: b.state },
                { label: 'Zip Code',                        value: b.postalcode },
                { label: 'Type of Residence',               value: housingLabel(b.housing) },
                { label: 'How many years have you lived here?', value: b.currentaddressperiod },
                { label: 'Monthly rent/mortgage payment',   value: b.housingpay ? b.housingpay : null },
            ]});

            // 3 — Employment
            var empFields = [
                { label: 'Employer Name',         value: b.employer },
                { label: 'Job Title',             value: b.position },
                { label: 'Employer Phone Number', value: b.wphone },
                { label: 'Monthly income',        value: b.mincome },
                { label: 'Years worked here',     value: b.years },
                { label: 'Months worked here',    value: b.months },
                { label: 'Do you have another source of income', value: b.other_income },
            ];
            if (b.other_income === 'Y') {
                empFields.push({ label: 'Type of income',  value: b.other_income_type });
                empFields.push({ label: 'Gross income',    value: b.other_income_amount });
            }
            sections.push({ title: 'Hard Credit App (Single - Employment)', fields: empFields });

            // 4/6 — Co-borrower sections (joint only)
            if (isJoint) {
                sections.push({ title: 'Hard Credit App (Joint - Info / Address)', fields: [
                    { label: 'Co-borrower First Name',      value: co.first_name },
                    { label: 'Co-borrower Last Name',       value: co.last_name },
                    { label: 'Co-borrower Phone Number',    value: co.phone },
                    { label: 'Co-borrower Street Address',  value: co.address },
                    { label: 'City',                        value: co.city },
                    { label: 'State',                       value: co.state },
                    { label: 'Zip Code',                    value: co.postalcode },
                    { label: 'Type of Residence',           value: housingLabel(co.housing) },
                    { label: 'How many years have you lived here?', value: co.addressperiod },
                    { label: 'Monthly rent/mortgage payment', value: co.housingpay ? co.housingpay : null },
                ]});

                var coEmpFields = [
                    { label: 'Co-borrower Employer Name',         value: co.employer },
                    { label: 'Co-borrower Job Title',             value: co.position },
                    { label: 'Employer Phone Number',             value: co.wphone },
                    { label: 'Monthly income',                    value: co.mincome },
                    { label: 'Years worked here',                 value: co.years },
                    { label: 'Do you have another source of income', value: co.other_income },
                ];
                if (co.other_income === 'Y') {
                    coEmpFields.push({ label: 'Type of income', value: co.other_income_type });
                    coEmpFields.push({ label: 'Gross income',   value: co.other_income_amount });
                }
                sections.push({ title: 'Hard Credit App (Joint - Employment)', fields: coEmpFields });
            }

            // Last — Consent + SSN/DOB
            var consentTitle = isJoint
                ? 'Hard Credit App (Joint - Consent + SSN/DOB)'
                : 'Hard Credit App (Single - Consent + SSN/DOB)';

            var consentFields = [
                { label: 'Electronic Signature',              value: b.signature },
                { label: 'Social Security Number',            value: b.ssn },
                { label: 'Date of Birth',                     value: b.dob },
                { label: 'Authorization / consent checkbox',  value: '1' },
            ];

            if (isJoint) {
                consentFields.push({ label: 'Regulation B checkbox',                    value: '1' });
                consentFields.push({ label: 'Electronic Signature',                     value: co.signature });
                consentFields.push({ label: 'Co-borrower authorization / consent checkbox', value: '1' });
                consentFields.push({ label: 'Co-borrower Regulation B checkbox',        value: '1' });
                consentFields.push({ label: 'Co-borrower Date of Birth',               value: co.dob });
                consentFields.push({ label: 'Co-borrower Social Security Number',      value: co.ssn });
            }

            sections.push({ title: consentTitle, fields: consentFields });

        } else if (formType === 'schedule_test_drive') {
            var stdFields = [
                { label: 'Vehicle',                              value: entry.vehicle ? (entry.vehicle.stock_number + ': ' + entry.vehicle.display_title) : null },
                { label: 'First Name',                           value: entry.first_name },
                { label: 'Last Name',                            value: entry.last_name },
                { label: 'Email Address',                        value: entry.email },
                { label: 'Phone Number',                         value: entry.phone },
                { label: 'What is the best way to contact you?', value: data.commpref },
                { label: 'Select a day',                         value: data.preferred_day_label || data.preferred_date },
                { label: 'Select a time of day',                 value: data.preferred_time },
            ];

            if (displayVal(data.comment)) {
                stdFields.push({ label: 'What questions can we answer for you?', value: data.comment });
            }

            sections.push({ title: 'Schedule a Test Drive', fields: stdFields });

        } else {
            // managers_special / ask_question / contact_us / e_price

            var fields = [];

            if (entry.vehicle) {
                var vehicleLabel = entry.vehicle.stock_number
                    ? entry.vehicle.stock_number + ': ' + entry.vehicle.display_title
                    : entry.vehicle.display_title;
                fields.push({ label: 'Vehicle', value: vehicleLabel });
            }

            fields.push(
                { label: 'First Name',                           value: entry.first_name },
                { label: 'Last Name',                            value: entry.last_name },
                { label: 'Email Address',                        value: entry.email },
                { label: 'Phone Number',                         value: entry.phone },
                { label: 'What is the best way to contact you?', value: data.commpref }
            );

            if (formType === 'contact_us' || formType === 'ask_question') {
                fields.push({ label: 'What questions can we answer for you?', value: data.comment });
            } else if (formType === 'managers_special' && displayVal(data.comment)) {
                fields.push({ label: 'What questions can we answer for you?', value: data.comment });
            }

            sections.push({ title: entry.form_type_label || 'Form Submission', fields: fields });
        }

        // ── Render ────────────────────────────────────────────────────────────
        sections.forEach(function (sec, idx) {
            html += '<div class="fs-form-card">';
            html += '<div class="fs-form-type-row">' + escHtml(sec.title) + '</div>';
            sec.fields.forEach(function (f) {
                var dv = displayVal(f.value);
                var rendered = dv
                    ? '<span class="fs-form-field-value">' + escHtml(String(dv)) + '</span>'
                    : '<span class="fs-form-field-value" style="color:#bbb;">—</span>';
                html += '<div class="fs-form-field-row">'
                      + '<span class="fs-form-field-label">' + escHtml(f.label) + '</span>'
                      + rendered
                      + '</div>';
            });
            html += '</div>';
            if (idx < sections.length - 1) {
                html += '<div class="fs-section-arrow"><i class="bi bi-arrow-down"></i></div>';
            }
        });

        html += '<div class="fs-section-arrow"><i class="bi bi-arrow-down"></i></div>';
        html += '<div class="fs-form-ended">Form submission ended.</div>';

        return html;
    }

    // ── Canvas: set detail value ──────────────────────────────────────────────
    function setDetail(id, value) {
        var el = document.getElementById(id);
        if (!el) return;
        var dv = displayVal(value);
        el.textContent = dv || '--';
        el.className   = 'fs-detail-value' + (dv ? '' : ' muted');
    }

    // ── Canvas: open via AJAX ─────────────────────────────────────────────────
    function openCanvas(showUrl, entryName) {
        document.getElementById('fsFormSections').innerHTML = '<div style="padding:24px;color:#aaa;text-align:center;">Loading...</div>';

        fsCanvasOverlay.classList.add('open');
        fsCanvas.classList.add('open');
        document.body.style.overflow = 'hidden';
        document.querySelector('.fs-canvas-body').scrollTop = 0;

        fetch(showUrl, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (!res.success) return;
            var entry = res.entry;
            var vd    = entry.visitor_data || {};
            var tr    = vd.traffic  || {};
            var br    = vd.browser  || {};
            var dv    = vd.device   || {};
            var lo    = vd.location || {};

            document.getElementById('fsFormSections').innerHTML = buildSections(entry);

            setDetail('fsDetIp',             vd.ip);
            setDetail('fsDetLocation',       lo.city ? lo.city + ', ' + lo.state : null);
            setDetail('fsDetDevice',         dv.type);
            setDetail('fsDetBrand',          dv.brand);
            setDetail('fsDetModel',          dv.model);
            setDetail('fsDetClient',         br.client);
            setDetail('fsDetOs',             br.os);
            setDetail('fsDetOsVersion',      br.os_version);
            setDetail('fsDetReferer',        tr.referer);
            setDetail('fsDetClassification', tr.classification);
            setDetail('fsDetUtmSource',      tr.utm_source);
            setDetail('fsDetUtmCampaign',    tr.utm_campaign);
            setDetail('fsDetUtmTerm',        tr.utm_term);
            setDetail('fsDetUtmContent',     tr.utm_content);
            setDetail('fsDetVisitDuration',  vd.visit_duration_seconds
                ? Math.round(vd.visit_duration_seconds / 60 * 10) / 10 + ' min'
                : null);

            var row = tableBody.querySelector('[data-show-url="' + showUrl + '"]');
            if (row) {
                row.classList.remove('fs-row-unread');
                var dot = row.querySelector('.fs-unread-dot');
                if (dot) dot.remove();
            }
        });
    }

    // ── Canvas: close ─────────────────────────────────────────────────────────
    function closeCanvas() {
        fsCanvas.classList.remove('open');
        fsCanvasOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.getElementById('fsCanvasClose').addEventListener('click', closeCanvas);
    fsCanvasOverlay.addEventListener('click', closeCanvas);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeCanvas();
    });

    // ── Row click → open canvas ───────────────────────────────────────────────
    tableBody.addEventListener('click', function (e) {
        if (e.target.closest('.fs-row-cb'))       return;
        if (e.target.closest('.fs-vehicle-link')) return;
        var row = e.target.closest('.fs-row');
        if (!row || !row.dataset.showUrl)          return;
        var name = row.querySelector('td:nth-child(2)').textContent.trim();
        openCanvas(row.dataset.showUrl, name);
    });

    // ── Init ──────────────────────────────────────────────────────────────────
    updateBtns();

})();
