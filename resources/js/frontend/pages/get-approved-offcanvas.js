/**
 * Get Approved — Offcanvas version
 * All element IDs prefixed with gao- to avoid conflict with page version
 * Radio names prefixed with gao_ for same reason
 *
 * Lives in: resources/js/frontend/pages/get-approved-offcanvas.js
 * Load on:  vehicle-detail.blade.php, home.blade.php, inventory-listing.blade.php
 *           (any page that includes the get-approved offcanvas)
 */

(function () {

    'use strict';

    var offcanvasEl = document.getElementById('getApproved');
    if (!offcanvasEl) return;

    // ── Routes ────────────────────────────────────────────────────────────────
    var submitRoute = (window.gaRoutes && window.gaRoutes.submit) ? window.gaRoutes.submit : null;
    var csrf        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── State ─────────────────────────────────────────────────────────────────
    var isJoint     = false;
    var formEntryId = null;

    // ── Elements ──────────────────────────────────────────────────────────────
    var borrowerTypeInput = document.getElementById('gao-borrower-type');

    var step1     = document.getElementById('gao-step-1');
    var step2     = document.getElementById('gao-step-2');
    var step3     = document.getElementById('gao-step-3');
    var step4j    = document.getElementById('gao-step-4-joint');
    var step5j    = document.getElementById('gao-step-5-joint');
    var stepLast  = document.getElementById('gao-step-last');
    var successDiv = document.getElementById('gao-success');

    var header1    = document.getElementById('gao-header-1');
    var header2    = document.getElementById('gao-header-2');
    var header3    = document.getElementById('gao-header-3');
    var header4j   = document.getElementById('gao-header-4-joint');
    var header5j   = document.getElementById('gao-header-5-joint');
    var headerLast = document.getElementById('gao-header-last');

    var ind1    = document.getElementById('gao-ind-1');
    var ind2    = document.getElementById('gao-ind-2');
    var ind3    = document.getElementById('gao-ind-3');
    var ind4j   = document.getElementById('gao-ind-4-joint');
    var ind5j   = document.getElementById('gao-ind-5-joint');
    var indLast = document.getElementById('gao-ind-last');

    var choiceDiv       = document.getElementById('gao-choice');
    var contactForm     = document.getElementById('gao-contact-form');
    var btnMyself       = document.getElementById('gao-btn-myself');
    var btnCoborrower   = document.getElementById('gao-btn-coborrower');
    var btnChangeType   = document.getElementById('gao-btn-change-type');
    var changeTypeLabel = document.getElementById('gao-change-type-label');

    var continue1  = document.getElementById('gao-continue-1');
    var continue2  = document.getElementById('gao-continue-2');
    var continue3  = document.getElementById('gao-continue-3');
    var continue4j = document.getElementById('gao-continue-4-joint');
    var continue5j = document.getElementById('gao-continue-5-joint');
    var submitBtn  = document.getElementById('gao-submit-btn');

    var coborrowerConsentSection = document.getElementById('gao-coborrower-consent-section');
    var regulationbPrimaryRow    = document.getElementById('gao-regulationb-primary-row');
    var consentBorrowerTitle     = document.getElementById('gao-consent-borrower-title');

    var errorSummary = document.getElementById('gao-error-summary');
    var errorList    = document.getElementById('gao-error-list');

    // ── Helpers ───────────────────────────────────────────────────────────────

    function show(el) { if (el) el.classList.remove('hidden'); }
    function hide(el) { if (el) el.classList.add('hidden'); }

    function getTotalSteps() { return isJoint ? 6 : 4; }

    function getStepNumber(key) {
        if (!isJoint) {
            return { 1: 1, 2: 2, 3: 3, 'last': 4 }[key] || key;
        }
        return { 1: 1, 2: 2, 3: 3, '4j': 4, '5j': 5, 'last': 6 }[key] || key;
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
            if (item.ind) item.ind.textContent = getStepNumber(item.key) + ' / ' + total;
        });
    }

    function allHeaders() {
        return [header1, header2, header3, header4j, header5j, headerLast];
    }

    function setHeaderActive(headerEl) {
        allHeaders().forEach(function (h) {
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
        var ind   = headerEl.querySelector('.gao-indicator');
        if (!ind)  return;
        var total = getTotalSteps();
        var num   = getStepNumber(stepKey);
        ind.innerHTML = '<u class="cursor-pointer gao-edit-step" data-step="' + stepKey + '">'
            + num + ' / ' + total + ' &nbsp;<small>Edit</small></u>';
    }

    // ── Validation ────────────────────────────────────────────────────────────

    function clearFieldError(input) {
        if (!input) return;
        input.classList.remove('is-invalid');
        var container = input.closest('.mb-3');
        var fb = container?.querySelector('.invalid-feedback')
            || input.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
        if (fb) fb.textContent = '';
    }

    function setFieldError(input, message) {
        if (!input) return;
        input.classList.add('is-invalid');
        var container = input.closest('.mb-3');
        var fb = container?.querySelector('.invalid-feedback')
            || input.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
        if (fb) fb.textContent = message;
    }

    function clearAllErrors() {
        offcanvasEl.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        offcanvasEl.querySelectorAll('.invalid-feedback').forEach(function (el) {
            el.textContent = '';
        });
        ['gao-commpref-error','gao-other-error','gao-spouseother-error',
         'gao-singleconsent-error','gao-regulationbprimary-error',
         'gao-jointconsent-error','gao-regulationbjoint-error'].forEach(function (id) {
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
        errorSummary.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideErrorSummary() {
        hide(errorSummary);
        errorList.innerHTML = '';
    }

    // ── Server error display ──────────────────────────────────────────────────

    var fieldIdMap = {
        'first_name': 'gao-first-name', 'last_name': 'gao-last-name',
        'email': 'gao-email', 'phone': 'gao-phone',
        'address': 'gao-address', 'city': 'gao-city', 'state': 'gao-state',
        'postalcode': 'gao-postalcode', 'housing': 'gao-housing',
        'currentaddressperiod': 'gao-currentaddressperiod', 'housingpay': 'gao-housingpay',
        'employer': 'gao-employer', 'position': 'gao-position', 'wphone': 'gao-wphone',
        'mincome': 'gao-mincome', 'years': 'gao-years', 'months': 'gao-months',
        'otherincomeexpln': 'gao-otherincomeexpln', 'otherincome': 'gao-otherincome',
        'ssn': 'gao-ssn', 'month': 'gao-month', 'day': 'gao-day', 'year': 'gao-year',
        'singlesignature': 'gao-singlesignature',
        'spousefirstname': 'gao-spousefirstname', 'spouselastname': 'gao-spouselastname',
        'spousephone': 'gao-spousephone', 'spouseaddress': 'gao-spouseaddress',
        'spousecity': 'gao-spousecity', 'spousestate': 'gao-spousestate',
        'spousepostalcode': 'gao-spousepostalcode', 'spousehousing': 'gao-spousehousing',
        'spouseaddressperiod': 'gao-spouseaddressperiod', 'spousehousingpay': 'gao-spousehousingpay',
        'spouseemployer': 'gao-spouseemployer', 'spouseposition': 'gao-spouseposition',
        'spouseworkphone': 'gao-spouseworkphone', 'spouseincome': 'gao-spouseincome',
        'spouseyears': 'gao-spouseyears',
        'spousessn': 'gao-spousessn', 'jointsignature': 'gao-jointsignature',
    };

    var specialErrors = {
        'commpref':           'gao-commpref-error',
        'other':              'gao-other-error',
        'spouseother':        'gao-spouseother-error',
        'singleconsent':      'gao-singleconsent-error',
        'regulationbprimary': 'gao-regulationbprimary-error',
        'jointconsent':       'gao-jointconsent-error',
        'regulationbjoint':   'gao-regulationbjoint-error',
    };

    function showServerErrors(errors) {
        var summaryMessages = [];
        Object.entries(errors).forEach(function (_ref) {
            var field = _ref[0]; var messages = _ref[1]; var msg = messages[0];
            summaryMessages.push(msg);
            if (specialErrors[field]) {
                var errEl = document.getElementById(specialErrors[field]);
                if (errEl) errEl.textContent = msg;
                return;
            }
            var inputId = fieldIdMap[field];
            if (inputId) {
                var input = document.getElementById(inputId);
                if (input) setFieldError(input, msg);
            }
        });
        if (summaryMessages.length) showErrorSummary(summaryMessages);
    }

    // ── Borrower Type ─────────────────────────────────────────────────────────

    function selectBorrowerType(joint) {
        isJoint = joint;
        borrowerTypeInput.value = joint ? 'joint' : 'single';
        hide(choiceDiv);
        show(contactForm);
        changeTypeLabel.textContent = joint
            ? 'Change to individual application'
            : 'Change to joint application';
        if (joint) { show(header4j); show(header5j); }
        else        { hide(header4j); hide(header5j); }
        updateIndicators();
        setHeaderActive(header1);
    }

    btnMyself.addEventListener('click',     function () { selectBorrowerType(false); });
    btnCoborrower.addEventListener('click', function () { selectBorrowerType(true); });

    btnChangeType.addEventListener('click', function () {
        isJoint = false;
        borrowerTypeInput.value = '';
        hide(header4j); hide(header5j);
        updateIndicators();
        hide(contactForm);
        show(choiceDiv);
        setHeaderActive(header1);
    });

    // ── Other Income Toggles ──────────────────────────────────────────────────

    offcanvasEl.addEventListener('change', function (e) {
        if (e.target.name === 'gao_other') {
            var fields = document.getElementById('gao-other-income-fields');
            e.target.value === 'Y' ? show(fields) : hide(fields);
        }
        if (e.target.name === 'gao_spouseother') {
            var fields = document.getElementById('gao-spouse-other-income-fields');
            e.target.value === 'Y' ? show(fields) : hide(fields);
        }
    });

    // ── Step Validation Helpers ───────────────────────────────────────────────

    function validateFields(fieldDefs) {
        var errors = [];
        fieldDefs.forEach(function (f) {
            var el = document.getElementById(f.id);
            if (el && !el.value.toString().trim()) {
                setFieldError(el, f.msg);
                errors.push(f.msg);
            }
        });
        return errors;
    }

    // ── Step 1 → 2 ───────────────────────────────────────────────────────────

    continue1.addEventListener('click', function () {
        clearAllErrors(); hideErrorSummary();
        var errors = [];
        var fn = document.getElementById('gao-first-name');
        var ln = document.getElementById('gao-last-name');
        var em = document.getElementById('gao-email');
        var ph = document.getElementById('gao-phone');
        var cp = offcanvasEl.querySelector('input[name="gao_commpref"]:checked');

        if (!fn.value.trim()) { setFieldError(fn, 'First name is required.'); errors.push('First name is required.'); }
        if (!ln.value.trim()) { setFieldError(ln, 'Last name is required.'); errors.push('Last name is required.'); }
        if (!em.value.trim()) { setFieldError(em, 'Email address is required.'); errors.push('Email is required.'); }
        if (!ph.value.trim()) { setFieldError(ph, 'Phone number is required.'); errors.push('Phone is required.'); }
        if (!cp) {
            var cpErr = document.getElementById('gao-commpref-error');
            if (cpErr) cpErr.textContent = 'Please select a contact preference.';
            errors.push('Contact preference is required.');
        }
        if (errors.length) return;

        hide(step1); show(step2);
        setHeaderActive(header2);
        setHeaderEdit(header1, 1);
        step2.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // ── Step 2 → 3 ───────────────────────────────────────────────────────────

    continue2.addEventListener('click', function () {
        clearAllErrors(); hideErrorSummary();
        var errors = validateFields([
            { id: 'gao-address',              msg: 'Street address is required.' },
            { id: 'gao-city',                 msg: 'City is required.' },
            { id: 'gao-state',                msg: 'State is required.' },
            { id: 'gao-postalcode',           msg: 'Zip code is required.' },
            { id: 'gao-housing',              msg: 'Type of residence is required.' },
            { id: 'gao-currentaddressperiod', msg: 'Years at residence is required.' },
            { id: 'gao-housingpay',           msg: 'Monthly payment is required.' },
        ]);
        if (errors.length) return;

        hide(step2); show(step3);
        setHeaderActive(header3);
        setHeaderEdit(header2, 2);
        step3.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // ── Step 3 → next ────────────────────────────────────────────────────────

    continue3.addEventListener('click', function () {
        clearAllErrors(); hideErrorSummary();
        var errors = validateFields([
            { id: 'gao-employer', msg: 'Employer name is required.' },
            { id: 'gao-position', msg: 'Job title is required.' },
            { id: 'gao-wphone',   msg: 'Employer phone is required.' },
            { id: 'gao-mincome',  msg: 'Monthly income is required.' },
            { id: 'gao-years',    msg: 'Years worked is required.' },
            { id: 'gao-months',   msg: 'Months worked is required.' },
        ]);
        var otherRadio = offcanvasEl.querySelector('input[name="gao_other"]:checked');
        if (!otherRadio) {
            var errEl = document.getElementById('gao-other-error');
            if (errEl) errEl.textContent = 'Please answer this question.';
            errors.push('Other income answer is required.');
        }
        if (otherRadio && otherRadio.value === 'Y') {
            var incomeType = document.getElementById('gao-otherincomeexpln');
            if (incomeType && !incomeType.value.trim()) {
                setFieldError(incomeType, 'Type of income is required.');
                errors.push('Type of income is required.');
            }
        }
        if (errors.length) return;

        hide(step3);
        setHeaderEdit(header3, 3);
        if (isJoint) {
            show(step4j); setHeaderActive(header4j);
            step4j.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            goToLastStep();
        }
    });

    // ── Step 4j → 5j ─────────────────────────────────────────────────────────

    continue4j.addEventListener('click', function () {
        clearAllErrors(); hideErrorSummary();
        var errors = validateFields([
            { id: 'gao-spousefirstname',     msg: 'Co-borrower first name is required.' },
            { id: 'gao-spouselastname',      msg: 'Co-borrower last name is required.' },
            { id: 'gao-spousephone',         msg: 'Co-borrower phone is required.' },
            { id: 'gao-spouseaddress',       msg: 'Co-borrower address is required.' },
            { id: 'gao-spousecity',          msg: 'Co-borrower city is required.' },
            { id: 'gao-spousestate',         msg: 'Co-borrower state is required.' },
            { id: 'gao-spousepostalcode',    msg: 'Co-borrower zip code is required.' },
            { id: 'gao-spousehousing',       msg: 'Co-borrower residence type is required.' },
            { id: 'gao-spouseaddressperiod', msg: 'Co-borrower years at address is required.' },
            { id: 'gao-spousehousingpay',    msg: 'Co-borrower monthly payment is required.' },
        ]);
        if (errors.length) return;

        hide(step4j); show(step5j);
        setHeaderActive(header5j);
        setHeaderEdit(header4j, '4j');
        step5j.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // ── Step 5j → last ────────────────────────────────────────────────────────

    continue5j.addEventListener('click', function () {
        clearAllErrors(); hideErrorSummary();
        var errors = validateFields([
            { id: 'gao-spouseemployer',  msg: 'Co-borrower employer is required.' },
            { id: 'gao-spouseposition',  msg: 'Co-borrower job title is required.' },
            { id: 'gao-spouseworkphone', msg: 'Co-borrower employer phone is required.' },
            { id: 'gao-spouseincome',    msg: 'Co-borrower monthly income is required.' },
            { id: 'gao-spouseyears',     msg: 'Co-borrower years worked is required.' },
        ]);
        var spouseOther = offcanvasEl.querySelector('input[name="gao_spouseother"]:checked');
        if (!spouseOther) {
            var errEl = document.getElementById('gao-spouseother-error');
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
        show(regulationbPrimaryRow); // always shown
        if (isJoint) {
            show(coborrowerConsentSection);
            if (consentBorrowerTitle) consentBorrowerTitle.textContent = 'Your Information (Primary Borrower)';
        } else {
            hide(coborrowerConsentSection);
            if (consentBorrowerTitle) consentBorrowerTitle.textContent = 'Your Information';
        }
        stepLast.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ── Edit Step ─────────────────────────────────────────────────────────────

    offcanvasEl.addEventListener('click', function (e) {
        var editEl = e.target.closest('.gao-edit-step');
        if (!editEl) return;
        var step = editEl.dataset.step;
        [step1, step2, step3, step4j, step5j, stepLast, successDiv].forEach(hide);

        if (step === '1')   { show(step1); show(contactForm); hide(choiceDiv); setHeaderActive(header1); updateIndicators(); }
        else if (step === '2')  { show(step2);  setHeaderActive(header2); }
        else if (step === '3')  { show(step3);  setHeaderActive(header3); }
        else if (step === '4j') { show(step4j); setHeaderActive(header4j); }
        else if (step === '5j') { show(step5j); setHeaderActive(header5j); }
    });

    // ── Submit ────────────────────────────────────────────────────────────────

    submitBtn.addEventListener('click', function () {
        clearAllErrors(); hideErrorSummary();

        if (!submitRoute) { console.error('gao: submitRoute not set'); return; }

        var fd = new FormData();

        function a(name, id) {
            var el = document.getElementById(id);
            if (el) fd.append(name, el.value);
        }

        fd.append('borrower_type', isJoint ? 'joint' : 'single');

        a('first_name', 'gao-first-name');
        a('last_name',  'gao-last-name');
        a('suffix',     'gao-suffix');
        a('email',      'gao-email');
        a('phone',      'gao-phone');

        var cp = offcanvasEl.querySelector('input[name="gao_commpref"]:checked');
        fd.append('commpref', cp ? cp.value : '');

        a('address',              'gao-address');
        a('city',                 'gao-city');
        a('state',                'gao-state');
        a('postalcode',           'gao-postalcode');
        a('housing',              'gao-housing');
        a('currentaddressperiod', 'gao-currentaddressperiod');
        a('housingpay',           'gao-housingpay');

        a('employer', 'gao-employer');
        a('position', 'gao-position');
        a('wphone',   'gao-wphone');
        a('mincome',  'gao-mincome');
        a('years',    'gao-years');
        a('months',   'gao-months');

        var otherRadio = offcanvasEl.querySelector('input[name="gao_other"]:checked');
        fd.append('other', otherRadio ? otherRadio.value : '');
        a('otherincomeexpln', 'gao-otherincomeexpln');
        a('otherincome',      'gao-otherincome');

        a('ssn',             'gao-ssn');
        a('month',           'gao-month');
        a('day',             'gao-day');
        a('year',            'gao-year');
        a('singlesignature', 'gao-singlesignature');

        var singleConsent = document.getElementById('gao-singleconsent');
        fd.append('singleconsent', singleConsent && singleConsent.checked ? '1' : '');

        var regBPrimary = document.getElementById('gao-regulationbprimary');
        fd.append('regulationbprimary', regBPrimary && regBPrimary.checked ? '1' : '');

        if (isJoint) {
            a('spousefirstname',     'gao-spousefirstname');
            a('spouselastname',      'gao-spouselastname');
            a('spousesuffix',        'gao-spousesuffix');
            a('spousephone',         'gao-spousephone');
            a('spouseaddress',       'gao-spouseaddress');
            a('spousecity',          'gao-spousecity');
            a('spousestate',         'gao-spousestate');
            a('spousepostalcode',    'gao-spousepostalcode');
            a('spousehousing',       'gao-spousehousing');
            a('spouseaddressperiod', 'gao-spouseaddressperiod');
            a('spousehousingpay',    'gao-spousehousingpay');
            a('spouseemployer',      'gao-spouseemployer');
            a('spouseposition',      'gao-spouseposition');
            a('spouseworkphone',     'gao-spouseworkphone');
            a('spouseincome',        'gao-spouseincome');
            a('spouseyears',         'gao-spouseyears');

            var spouseOther = offcanvasEl.querySelector('input[name="gao_spouseother"]:checked');
            fd.append('spouseother', spouseOther ? spouseOther.value : '');

            a('spousessn',      'gao-spousessn');
            a('jointsignature', 'gao-jointsignature');
            a('spousemonth',    'gao-spousemonth');
            a('spouseday',      'gao-spouseday');
            a('spouseyear',     'gao-spouseyear');

            var jointConsent = document.getElementById('gao-jointconsent');
            fd.append('jointconsent', jointConsent && jointConsent.checked ? '1' : '');

            var regBJoint = document.getElementById('gao-regulationbjoint');
            fd.append('regulationbjoint', regBJoint && regBJoint.checked ? '1' : '');
        }

        // Loading state
        submitBtn.disabled = true;
        submitBtn.querySelector('.gao-btn-label').textContent = 'Submitting...';
        submitBtn.querySelector('.gao-btn-icon').classList.add('d-none');

        fetch(submitRoute, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: fd,
        })
        .then(function (r) { return r.json().then(function (j) { return { status: r.status, json: j }; }); })
        .then(function (_ref) {
            var status = _ref.status; var json = _ref.json;

            if (status === 200 && json.success) {
                formEntryId = json.form_entry_id ?? null;
                showSuccess();
                return;
            }
            if (status === 422 && json.errors) {
                showServerErrors(json.errors);
            }
            resetSubmitBtn();
        })
        .catch(function (err) {
            resetSubmitBtn();
            console.error('gao submit error:', err);
        });
    });

    function resetSubmitBtn() {
        submitBtn.disabled = false;
        submitBtn.querySelector('.gao-btn-label').textContent = 'Finish Application';
        submitBtn.querySelector('.gao-btn-icon').classList.remove('d-none');
    }

    // ── Success ───────────────────────────────────────────────────────────────

    function showSuccess() {
        // Hide all step headers + content
        allHeaders().forEach(function (h) { if (h) hide(h); });
        [step1, step2, step3, step4j, step5j, stepLast].forEach(hide);
        hideErrorSummary();

        show(successDiv);
        successDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Init NPS — same approach as ask-question
        if (window.NpsModule) {
            window.NpsModule.init({
                starRatingId: 'gao-star-rating',
                npsOptionsId: 'gaoNpsOptions',
                submitBtnSel: '.gao-nps-submit-btn',
                getNpsRoute:  function () {
                    return (window.npsRouteTemplate || '').replace('__id__', formEntryId);
                },
            });
        }
    }

    // ── Reset on offcanvas close ──────────────────────────────────────────────

    offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
        resetForm();
    });

    function resetForm() {
        isJoint     = false;
        formEntryId = null;

        borrowerTypeInput.value = '';

        // Reset all inputs
        offcanvasEl.querySelectorAll('input:not([type="radio"]):not([type="checkbox"])').forEach(function (el) {
            el.value = '';
        });
        offcanvasEl.querySelectorAll('select').forEach(function (el) {
            el.selectedIndex = 0;
        });
        offcanvasEl.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(function (el) {
            el.checked = false;
        });

        // Clear all errors
        clearAllErrors();
        hideErrorSummary();

        // Reset headers
        allHeaders().forEach(function (h) {
            if (!h) return;
            show(h);
            h.querySelector('b').classList.remove('text-primary');
            h.querySelector('b').classList.add('text-muted');
            h.classList.remove('wizardstep-active');
            var ind = h.querySelector('.gao-indicator');
            if (ind) ind.textContent = ''; // will be re-set by updateIndicators
        });
        hide(header4j); hide(header5j);

        // Reset step visibility
        [step2, step3, step4j, step5j, stepLast, successDiv].forEach(hide);
        [contactForm].forEach(hide);
        [document.getElementById('gao-other-income-fields'),
         document.getElementById('gao-spouse-other-income-fields'),
         coborrowerConsentSection].forEach(hide);

        show(step1);
        show(choiceDiv);
        show(header1);
        show(header2);
        show(header3);
        show(headerLast);

        // Re-init phone/SSN formatting on freshly shown inputs
        if (window.InputFormat) window.InputFormat.init();

        updateIndicators();
        setHeaderActive(header1);
        resetSubmitBtn();
        show(npsSection); // will be conditionally hidden on next success
    }

    // ── Initial State ─────────────────────────────────────────────────────────

    [step2, step3, step4j, step5j, stepLast, successDiv, contactForm].forEach(hide);
    hide(header4j); hide(header5j);
    hide(coborrowerConsentSection);
    hide(document.getElementById('gao-other-income-fields'));
    hide(document.getElementById('gao-spouse-other-income-fields'));
    hideErrorSummary();

    setHeaderActive(header1);
    updateIndicators();

})();
