/**
 * Get Approved — 4-step (single) / 6-step (joint) wizard
 * Route: POST /forms/get-approved  →  frontend.forms.get-approved
 *
 * Lives in: resources/js/frontend/pages/get-approved.js
 */

(function () {

    'use strict';

    // ── Routes ────────────────────────────────────────────────────────────────
    var routes = window.gaRoutes || {};
    var csrf   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── State ─────────────────────────────────────────────────────────────────
    var isJoint     = false;
    var formEntryId = null;

    // ── Elements ──────────────────────────────────────────────────────────────
    var borrowerTypeInput = document.getElementById('ga-borrower-type');

    // Step divs
    var step1     = document.getElementById('ga-step-1');
    var step2     = document.getElementById('ga-step-2');
    var step3     = document.getElementById('ga-step-3');
    var step4j    = document.getElementById('ga-step-4-joint');
    var step5j    = document.getElementById('ga-step-5-joint');
    var stepLast  = document.getElementById('ga-step-last');
    var successDiv = document.getElementById('ga-success');

    // Headers
    var header1   = document.getElementById('ga-header-1');
    var header2   = document.getElementById('ga-header-2');
    var header3   = document.getElementById('ga-header-3');
    var header4j  = document.getElementById('ga-header-4-joint');
    var header5j  = document.getElementById('ga-header-5-joint');
    var headerLast = document.getElementById('ga-header-last');

    // Indicators
    var ind1      = document.getElementById('ga-ind-1');
    var ind2      = document.getElementById('ga-ind-2');
    var ind3      = document.getElementById('ga-ind-3');
    var ind4j     = document.getElementById('ga-ind-4-joint');
    var ind5j     = document.getElementById('ga-ind-5-joint');
    var indLast   = document.getElementById('ga-ind-last');

    // Step 1 elements
    var choiceDiv       = document.getElementById('ga-choice');
    var contactForm     = document.getElementById('ga-contact-form');
    var btnMyself       = document.getElementById('ga-btn-myself');
    var btnCoborrower   = document.getElementById('ga-btn-coborrower');
    var btnChangeType   = document.getElementById('ga-btn-change-type');
    var changeTypeLabel = document.getElementById('ga-change-type-label');

    // Continue buttons
    var continue1  = document.getElementById('ga-continue-1');
    var continue2  = document.getElementById('ga-continue-2');
    var continue3  = document.getElementById('ga-continue-3');
    var continue4j = document.getElementById('ga-continue-4-joint');
    var continue5j = document.getElementById('ga-continue-5-joint');
    var submitBtn  = document.getElementById('ga-submit-btn');

    // Joint-only elements
    var coborrowerConsentSection = document.getElementById('ga-coborrower-consent-section');
    var regulationbPrimaryRow    = document.getElementById('ga-regulationb-primary-row');
    var consentBorrowerTitle     = document.getElementById('ga-consent-borrower-title');

    // Error summary
    var errorSummary = document.getElementById('ga-error-summary');
    var errorList    = document.getElementById('ga-error-list');

    if (!step1) return;

    // ── Helpers ───────────────────────────────────────────────────────────────

    function show(el) { if (el) el.classList.remove('hidden'); }
    function hide(el) { if (el) el.classList.add('hidden'); }

    function getTotalSteps() { return isJoint ? 6 : 4; }

    function getStepNumber(stepKey) {
        // stepKey: 1, 2, 3, '4j', '5j', 'last'
        if (!isJoint) {
            var map = { 1: 1, 2: 2, 3: 3, 'last': 4 };
            return map[stepKey] ?? stepKey;
        } else {
            var mapJ = { 1: 1, 2: 2, 3: 3, '4j': 4, '5j': 5, 'last': 6 };
            return mapJ[stepKey] ?? stepKey;
        }
    }

    function updateIndicators() {
        var total = getTotalSteps();
        [
            { ind: ind1,    key: 1 },
            { ind: ind2,    key: 2 },
            { ind: ind3,    key: 3 },
            { ind: ind4j,   key: '4j' },
            { ind: ind5j,   key: '5j' },
            { ind: indLast, key: 'last' },
        ].forEach(function (item) {
            if (item.ind) {
                item.ind.textContent = getStepNumber(item.key) + ' / ' + total;
            }
        });
    }

    function setHeaderActive(headerEl) {
        [header1, header2, header3, header4j, header5j, headerLast].forEach(function (h) {
            if (!h) return;
            h.querySelector('b').classList.remove('text-primary');
            h.querySelector('b').classList.add('text-muted');
            h.classList.remove('wizardstep-active');
        });
        if (headerEl) {
            headerEl.querySelector('b').classList.remove('text-muted');
            headerEl.querySelector('b').classList.add('text-primary');
            headerEl.classList.add('wizardstep-active');
        }
    }

    function setHeaderEdit(headerEl, stepKey) {
        if (!headerEl) return;
        var ind = headerEl.querySelector('.ga-indicator');
        if (!ind) return;
        var total = getTotalSteps();
        var num   = getStepNumber(stepKey);
        ind.innerHTML = '<u class="cursor-pointer ga-edit-step" data-step="' + stepKey + '">'
            + num + ' / ' + total + ' &nbsp;<small>Edit</small></u>';
    }

    // ── Validation ────────────────────────────────────────────────────────────

    function clearFieldError(input) {
        if (!input) return;
        input.classList.remove('is-invalid');
        var fb = input.closest('.mb-3, .mb-md-4, .input-group')?.querySelector('.invalid-feedback')
            || input.parentElement.querySelector('.invalid-feedback');
        if (fb) fb.textContent = '';
    }

    function setFieldError(input, message) {
        if (!input) return;
        input.classList.add('is-invalid');
        // Find the nearest invalid-feedback div
        var container = input.closest('.mb-3, .mb-md-4');
        var fb = container?.querySelector('.invalid-feedback');
        // For input-group ($ prefix)
        if (!fb) {
            fb = input.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
        }
        if (fb) fb.textContent = message;
    }

    function clearStepErrors(stepEl) {
        if (!stepEl) return;
        stepEl.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        stepEl.querySelectorAll('.invalid-feedback').forEach(function (el) {
            el.textContent = '';
        });
    }

    function clearAllErrors() {
        // Clear every is-invalid and invalid-feedback across the whole form
        document.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(function (el) {
            el.textContent = '';
        });
        // Clear special error divs
        ['ga-commpref-error','ga-other-error','ga-spouseother-error',
         'ga-singleconsent-error','ga-regulationbprimary-error',
         'ga-jointconsent-error','ga-regulationbjoint-error'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.textContent = '';
        });
    }

    function showErrorSummary(errors) {
        errorList.innerHTML = '';
        errors.forEach(function (msg) {
            var li = document.createElement('li');
            li.textContent = msg;
            errorList.appendChild(li);
        });
        show(errorSummary);
        errorSummary.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function hideErrorSummary() {
        hide(errorSummary);
        errorList.innerHTML = '';
    }

    // Map backend field names → frontend input IDs
    var fieldIdMap = {
        'first_name':          'ga-first-name',
        'last_name':           'ga-last-name',
        'suffix':              'ga-suffix',
        'email':               'ga-email',
        'phone':               'ga-phone',
        'commpref':            null, // handled separately
        'address':             'ga-address',
        'city':                'ga-city',
        'state':               'ga-state',
        'postalcode':          'ga-postalcode',
        'housing':             'ga-housing',
        'currentaddressperiod':'ga-currentaddressperiod',
        'housingpay':          'ga-housingpay',
        'employer':            'ga-employer',
        'position':            'ga-position',
        'wphone':              'ga-wphone',
        'mincome':             'ga-mincome',
        'years':               'ga-years',
        'months':              'ga-months',
        'other':               null,
        'otherincomeexpln':    'ga-otherincomeexpln',
        'otherincome':         'ga-otherincome',
        'ssn':                 'ga-ssn',
        'month':               'ga-month',
        'day':                 'ga-day',
        'year':                'ga-year',
        'singlesignature':     'ga-singlesignature',
        'singleconsent':       null,
        'regulationbprimary':  null,
        'spousefirstname':     'ga-spousefirstname',
        'spouselastname':      'ga-spouselastname',
        'spousesuffix':        'ga-spousesuffix',
        'spousephone':         'ga-spousephone',
        'spouseaddress':       'ga-spouseaddress',
        'spousecity':          'ga-spousecity',
        'spousestate':         'ga-spousestate',
        'spousepostalcode':    'ga-spousepostalcode',
        'spousehousing':       'ga-spousehousing',
        'spouseaddressperiod': 'ga-spouseaddressperiod',
        'spousehousingpay':    'ga-spousehousingpay',
        'spouseemployer':      'ga-spouseemployer',
        'spouseposition':      'ga-spouseposition',
        'spouseworkphone':     'ga-spouseworkphone',
        'spouseincome':        'ga-spouseincome',
        'spouseyears':         'ga-spouseyears',
        'spouseother':         null,
        'spousessn':           'ga-spousessn',
        'jointsignature':      'ga-jointsignature',
        'jointconsent':        null,
        'regulationbjoint':    null,
    };

    function showServerErrors(errors) {
        var summaryMessages = [];

        Object.entries(errors).forEach(function (_ref) {
            var field    = _ref[0];
            var messages = _ref[1];
            var msg      = messages[0];

            summaryMessages.push(msg);

            var inputId = fieldIdMap[field];

            // Special cases (radios/checkboxes)
            if (field === 'commpref') {
                var errEl = document.getElementById('ga-commpref-error');
                if (errEl) errEl.textContent = msg;
                return;
            }
            if (field === 'other') {
                var errEl = document.getElementById('ga-other-error');
                if (errEl) errEl.textContent = msg;
                return;
            }
            if (field === 'spouseother') {
                var errEl = document.getElementById('ga-spouseother-error');
                if (errEl) errEl.textContent = msg;
                return;
            }
            if (field === 'singleconsent') {
                var errEl = document.getElementById('ga-singleconsent-error');
                if (errEl) errEl.textContent = msg;
                return;
            }
            if (field === 'regulationbprimary') {
                var errEl = document.getElementById('ga-regulationbprimary-error');
                if (errEl) errEl.textContent = msg;
                return;
            }
            if (field === 'jointconsent') {
                var errEl = document.getElementById('ga-jointconsent-error');
                if (errEl) errEl.textContent = msg;
                return;
            }
            if (field === 'regulationbjoint') {
                var errEl = document.getElementById('ga-regulationbjoint-error');
                if (errEl) errEl.textContent = msg;
                return;
            }

            if (!inputId) return;
            var input = document.getElementById(inputId);
            if (input) setFieldError(input, msg);
        });

        if (summaryMessages.length) {
            showErrorSummary(summaryMessages);
        }
    }

    // ── Borrower Type Toggle ──────────────────────────────────────────────────

    function selectBorrowerType(joint) {
        isJoint = joint;
        borrowerTypeInput.value = joint ? 'joint' : 'single';

        hide(choiceDiv);
        show(contactForm);

        if (joint) {
            changeTypeLabel.textContent = 'Change to individual application';
        } else {
            changeTypeLabel.textContent = 'Change to joint application';
        }

        // Show/hide joint headers
        if (joint) {
            show(header4j);
            show(header5j);
        } else {
            hide(header4j);
            hide(header5j);
        }

        updateIndicators();
        setHeaderActive(header1);
    }

    btnMyself.addEventListener('click', function () { selectBorrowerType(false); });
    btnCoborrower.addEventListener('click', function () { selectBorrowerType(true); });

    btnChangeType.addEventListener('click', function () {
        // Reset borrower type completely
        isJoint = false;
        borrowerTypeInput.value = '';
        hide(header4j);
        hide(header5j);
        updateIndicators();

        hide(contactForm);
        show(choiceDiv);
        setHeaderActive(header1);
    });

    // ── Other Income Toggle ───────────────────────────────────────────────────

    document.querySelectorAll('input[name="other"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            var otherFields = document.getElementById('ga-other-income-fields');
            if (this.value === 'Y') {
                show(otherFields);
            } else {
                hide(otherFields);
            }
        });
    });

    // ── Co-borrower Other Income Toggle ──────────────────────────────────────

    document.querySelectorAll('input[name="spouseother"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            var otherFields = document.getElementById('ga-spouse-other-income-fields');
            if (!otherFields) return;
            if (this.value === 'Y') {
                show(otherFields);
            } else {
                hide(otherFields);
            }
        });
    });

    // ── Step Navigation ───────────────────────────────────────────────────────

    // Step 1 → 2
    continue1.addEventListener('click', function () {
        clearStepErrors(step1);
        hideErrorSummary();

        // Simple client-side check
        var errors = [];
        var fn = document.getElementById('ga-first-name');
        var ln = document.getElementById('ga-last-name');
        var em = document.getElementById('ga-email');
        var ph = document.getElementById('ga-phone');
        var cp = document.querySelector('input[name="commpref"]:checked');

        if (!fn.value.trim()) { setFieldError(fn, 'First name is required.'); errors.push('First name is required.'); }
        if (!ln.value.trim()) { setFieldError(ln, 'Last name is required.'); errors.push('Last name is required.'); }
        if (!em.value.trim()) { setFieldError(em, 'Email address is required.'); errors.push('Email is required.'); }
        if (!ph.value.trim()) { setFieldError(ph, 'Phone number is required.'); errors.push('Phone is required.'); }
        if (!cp) {
            var cpErr = document.getElementById('ga-commpref-error');
            if (cpErr) cpErr.textContent = 'Please select a contact preference.';
            errors.push('Contact preference is required.');
        }

        if (errors.length) return;

        hide(step1);
        show(step2);
        setHeaderActive(header2);
        setHeaderEdit(header1, 1);
        step2.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Step 2 → 3
    continue2.addEventListener('click', function () {
        clearStepErrors(step2);
        hideErrorSummary();

        var errors = [];
        var fields = [
            { id: 'ga-address',              msg: 'Street address is required.' },
            { id: 'ga-city',                 msg: 'City is required.' },
            { id: 'ga-state',                msg: 'State is required.' },
            { id: 'ga-postalcode',           msg: 'Zip code is required.' },
            { id: 'ga-housing',              msg: 'Type of residence is required.' },
            { id: 'ga-currentaddressperiod', msg: 'Years at residence is required.' },
            { id: 'ga-housingpay',           msg: 'Monthly payment is required.' },
        ];

        fields.forEach(function (f) {
            var el = document.getElementById(f.id);
            if (el && !el.value.trim()) {
                setFieldError(el, f.msg);
                errors.push(f.msg);
            }
        });

        if (errors.length) return;

        hide(step2);
        show(step3);
        setHeaderActive(header3);
        setHeaderEdit(header2, 2);
        step3.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Step 3 → next (4j if joint, last if single)
    continue3.addEventListener('click', function () {
        clearStepErrors(step3);
        hideErrorSummary();

        var errors = [];
        var fields = [
            { id: 'ga-employer',  msg: 'Employer name is required.' },
            { id: 'ga-position',  msg: 'Job title is required.' },
            { id: 'ga-wphone',    msg: 'Employer phone is required.' },
            { id: 'ga-mincome',   msg: 'Monthly income is required.' },
            { id: 'ga-years',     msg: 'Years worked is required.' },
            { id: 'ga-months',    msg: 'Months worked is required.' },
        ];

        fields.forEach(function (f) {
            var el = document.getElementById(f.id);
            if (el && !el.value.toString().trim()) {
                setFieldError(el, f.msg);
                errors.push(f.msg);
            }
        });

        var otherRadio = document.querySelector('input[name="other"]:checked');
        if (!otherRadio) {
            var errEl = document.getElementById('ga-other-error');
            if (errEl) errEl.textContent = 'Please answer this question.';
            errors.push('Other income answer is required.');
        }

        if (errors.length) return;

        hide(step3);
        setHeaderEdit(header3, 3);

        if (isJoint) {
            show(step4j);
            setHeaderActive(header4j);
            step4j.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            goToLastStep();
        }
    });

    // Step 4j → 5j (joint only)
    continue4j.addEventListener('click', function () {
        clearStepErrors(step4j);
        hideErrorSummary();

        var errors = [];
        var fields = [
            { id: 'ga-spousefirstname',     msg: 'Co-borrower first name is required.' },
            { id: 'ga-spouselastname',      msg: 'Co-borrower last name is required.' },
            { id: 'ga-spousephone',         msg: 'Co-borrower phone is required.' },
            { id: 'ga-spouseaddress',       msg: 'Co-borrower street address is required.' },
            { id: 'ga-spousecity',          msg: 'Co-borrower city is required.' },
            { id: 'ga-spousestate',         msg: 'Co-borrower state is required.' },
            { id: 'ga-spousepostalcode',    msg: 'Co-borrower zip code is required.' },
            { id: 'ga-spousehousing',       msg: 'Co-borrower residence type is required.' },
            { id: 'ga-spouseaddressperiod', msg: 'Co-borrower years at address is required.' },
            { id: 'ga-spousehousingpay',    msg: 'Co-borrower monthly payment is required.' },
        ];

        fields.forEach(function (f) {
            var el = document.getElementById(f.id);
            if (el && !el.value.toString().trim()) {
                setFieldError(el, f.msg);
                errors.push(f.msg);
            }
        });

        if (errors.length) return;

        hide(step4j);
        show(step5j);
        setHeaderActive(header5j);
        setHeaderEdit(header4j, '4j');
        step5j.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Step 5j → last (joint only)
    continue5j.addEventListener('click', function () {
        clearStepErrors(step5j);
        hideErrorSummary();

        var errors = [];
        var fields = [
            { id: 'ga-spouseemployer',   msg: 'Co-borrower employer is required.' },
            { id: 'ga-spouseposition',   msg: 'Co-borrower job title is required.' },
            { id: 'ga-spouseworkphone',  msg: 'Co-borrower employer phone is required.' },
            { id: 'ga-spouseincome',     msg: 'Co-borrower monthly income is required.' },
            { id: 'ga-spouseyears',      msg: 'Co-borrower years worked is required.' },
        ];

        fields.forEach(function (f) {
            var el = document.getElementById(f.id);
            if (el && !el.value.toString().trim()) {
                setFieldError(el, f.msg);
                errors.push(f.msg);
            }
        });

        var spouseOther = document.querySelector('input[name="spouseother"]:checked');
        if (!spouseOther) {
            var errEl = document.getElementById('ga-spouseother-error');
            if (errEl) errEl.textContent = 'Please answer this question.';
            errors.push('Co-borrower other income answer is required.');
        }

        if (errors.length) return;

        hide(step5j);
        setHeaderEdit(header5j, '5j');
        goToLastStep();
    });

    function goToLastStep() {
        show(stepLast);
        setHeaderActive(headerLast);

        // Always show regulationbPrimaryRow (required for both single and joint)
        show(regulationbPrimaryRow);

        if (isJoint) {
            show(coborrowerConsentSection);
            if (consentBorrowerTitle) consentBorrowerTitle.textContent = 'Your Information (Primary Borrower)';
        } else {
            hide(coborrowerConsentSection);
            if (consentBorrowerTitle) consentBorrowerTitle.textContent = 'Your Information';
        }

        stepLast.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ── Edit Step ─────────────────────────────────────────────────────────────

    document.addEventListener('click', function (e) {
        var editEl = e.target.closest('.ga-edit-step');
        if (!editEl) return;

        var step = editEl.dataset.step;

        // Hide all step content
        [step1, step2, step3, step4j, step5j, stepLast, successDiv].forEach(hide);

        if (step === '1') {
            show(step1);
            show(contactForm);
            hide(choiceDiv);
            setHeaderActive(header1);
            updateIndicators();
        } else if (step === '2') {
            show(step2);
            setHeaderActive(header2);
        } else if (step === '3') {
            show(step3);
            setHeaderActive(header3);
        } else if (step === '4j') {
            show(step4j);
            setHeaderActive(header4j);
        } else if (step === '5j') {
            show(step5j);
            setHeaderActive(header5j);
        }
    });

    // ── Submit ────────────────────────────────────────────────────────────────

    submitBtn.addEventListener('click', function () {
        clearAllErrors();
        hideErrorSummary();

        // Collect all form data
        var fd = new FormData();

        // Helper to append
        function a(name, id) {
            var el = document.getElementById(id);
            if (el) fd.append(name, el.value);
        }

        fd.append('borrower_type', isJoint ? 'joint' : 'single');

        // Contact
        a('first_name', 'ga-first-name');
        a('last_name',  'ga-last-name');
        a('suffix',     'ga-suffix');
        a('email',      'ga-email');
        a('phone',      'ga-phone');

        var cp = document.querySelector('input[name="commpref"]:checked');
        fd.append('commpref', cp ? cp.value : '');

        // Address
        a('address',              'ga-address');
        a('city',                 'ga-city');
        a('state',                'ga-state');
        a('postalcode',           'ga-postalcode');
        a('housing',              'ga-housing');
        a('currentaddressperiod', 'ga-currentaddressperiod');
        a('housingpay',           'ga-housingpay');

        // Employment
        a('employer',  'ga-employer');
        a('position',  'ga-position');
        a('wphone',    'ga-wphone');
        a('mincome',   'ga-mincome');
        a('years',     'ga-years');
        a('months',    'ga-months');

        var otherRadio = document.querySelector('input[name="other"]:checked');
        fd.append('other', otherRadio ? otherRadio.value : '');

        a('otherincomeexpln', 'ga-otherincomeexpln');
        a('otherincome',      'ga-otherincome');

        // Consent
        a('ssn',              'ga-ssn');
        a('month',            'ga-month');
        a('day',              'ga-day');
        a('year',             'ga-year');
        a('singlesignature',  'ga-singlesignature');

        var singleConsent = document.getElementById('ga-singleconsent');
        fd.append('singleconsent', singleConsent && singleConsent.checked ? '1' : '');

        var regBPrimary = document.getElementById('ga-regulationbprimary');
        fd.append('regulationbprimary', regBPrimary && regBPrimary.checked ? '1' : '');

        // Joint fields
        if (isJoint) {
            a('spousefirstname',     'ga-spousefirstname');
            a('spouselastname',      'ga-spouselastname');
            a('spousesuffix',        'ga-spousesuffix');
            a('spousephone',         'ga-spousephone');
            a('spouseaddress',       'ga-spouseaddress');
            a('spousecity',          'ga-spousecity');
            a('spousestate',         'ga-spousestate');
            a('spousepostalcode',    'ga-spousepostalcode');
            a('spousehousing',       'ga-spousehousing');
            a('spouseaddressperiod', 'ga-spouseaddressperiod');
            a('spousehousingpay',    'ga-spousehousingpay');
            a('spouseemployer',      'ga-spouseemployer');
            a('spouseposition',      'ga-spouseposition');
            a('spouseworkphone',     'ga-spouseworkphone');
            a('spouseincome',        'ga-spouseincome');
            a('spouseyears',         'ga-spouseyears');

            var spouseOther = document.querySelector('input[name="spouseother"]:checked');
            fd.append('spouseother', spouseOther ? spouseOther.value : '');

            a('spousessn',      'ga-spousessn');
            a('jointsignature', 'ga-jointsignature');

            // Co-borrower DOB
            a('spousemonth', 'ga-spousemonth');
            a('spouseday',   'ga-spouseday');
            a('spouseyear',  'ga-spouseyear');

            var jointConsent = document.getElementById('ga-jointconsent');
            fd.append('jointconsent', jointConsent && jointConsent.checked ? '1' : '');

            var regBJoint = document.getElementById('ga-regulationbjoint');
            fd.append('regulationbjoint', regBJoint && regBJoint.checked ? '1' : '');
        }

        // Loading state
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-label').textContent = 'Submitting...';
        submitBtn.querySelector('.btn-icon').classList.add('d-none');

        fetch(routes.submit, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept':       'application/json',
            },
            body: fd,
        })
        .then(function (r) { return r.json().then(function (j) { return { status: r.status, json: j }; }); })
        .then(function (_ref) {
            var status = _ref.status;
            var json   = _ref.json;

            if (status === 200 && json.success) {
                formEntryId = json.form_entry_id ?? null;
                showSuccess();
                return;
            }

            if (status === 422 && json.errors) {
                showServerErrors(json.errors);
                resetSubmitBtn();
                return;
            }

            resetSubmitBtn();
            console.error('Get approved error:', json);
        })
        .catch(function (err) {
            resetSubmitBtn();
            console.error('Get approved network error:', err);
        });
    });

    function resetSubmitBtn() {
        submitBtn.disabled = false;
        submitBtn.querySelector('.btn-label').textContent = 'Finish Application';
        submitBtn.querySelector('.btn-icon').classList.remove('d-none');
    }

    function showSuccess() {
        hide(stepLast);

        // Hide ALL step headers — only success box should remain visible
        [header1, header2, header3, header4j, header5j, headerLast].forEach(function (h) {
            if (h) hide(h);
        });

        hideErrorSummary();
        show(successDiv);
        successDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ── Initial State ─────────────────────────────────────────────────────────

    // All steps hidden except step 1
    [step2, step3, step4j, step5j, stepLast, successDiv].forEach(hide);
    [header4j, header5j].forEach(hide);
    hide(contactForm);
    hide(coborrowerConsentSection);
    hide(regulationbPrimaryRow);
    hide(document.getElementById('ga-other-income-fields'));
    hide(document.getElementById('ga-spouse-other-income-fields'));
    hide(errorSummary);

    setHeaderActive(header1);
    updateIndicators();

})();
