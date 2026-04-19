import 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {

    // ── Dropdown Toggle ───────────────────────────────────────────────────────
    document.querySelectorAll('.dropdown-toggle').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const dropdown = this.closest('.dropdown');
            const menu     = dropdown.querySelector('.dropdown-menu');
            const isOpen   = menu.classList.contains('show');

            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));

            if (! isOpen) {
                menu.classList.add('show');
            }
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (! e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
        }
    });

    // ── Mobile Drawer ─────────────────────────────────────────────────────────
    const menuBtn  = document.getElementById('menuBtn');
    const closeBtn = document.getElementById('closeDrawerBtn');
    const drawer   = document.getElementById('mobileDrawer');

    menuBtn?.addEventListener('click',  () => drawer.classList.add('show'));
    closeBtn?.addEventListener('click', () => drawer.classList.remove('show'));

    // ── Mobile Nav Accordion ──────────────────────────────────────────────────
    document.querySelectorAll('.parent-nav').forEach(parentNav => {
        const submenu = parentNav.querySelector('.submenu');
        if (! submenu) return;

        parentNav.addEventListener('click', function (e) {
            if (e.target.tagName.toLowerCase() === 'a') return;
            submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
            parentNav.classList.toggle('open');
        });
    });

    // ── Vehicle Card Image Slider ─────────────────────────────────────────────
    function initVehicleSliders(scope) {
        (scope || document).querySelectorAll('.img-srp-container').forEach(container => {
            if (container.dataset.sliderInit) return; // already initialized
            container.dataset.sliderInit = '1';

            const images   = Array.from(container.querySelectorAll('.img-srp'));
            const extraDiv = container.querySelector('#has-more-photos');
            const leftBtn  = container.querySelector('.left-toggle');
            const rightBtn = container.querySelector('.right-toggle');

            if (! leftBtn || ! rightBtn) return;

            let currentIndex  = 0;
            const totalSlides = images.length + 1;

            function showSlide(index) {
                images.forEach((img, i) => {
                    const isVisible = (i === index) || (i === images.length - 1 && index === images.length);
                    img.classList.toggle('d-block', isVisible);
                    img.classList.toggle('d-none',  ! isVisible);
                });
                if (extraDiv) {
                    const showExtra = index === images.length;
                    extraDiv.classList.toggle('d-block', showExtra);
                    extraDiv.classList.toggle('d-none',  ! showExtra);
                }
                const showArrows = images.length > 1 && index !== images.length;
                leftBtn.style.display  = showArrows ? 'block' : 'none';
                rightBtn.style.display = showArrows ? 'block' : 'none';
            }

            rightBtn.addEventListener('click', e => {
                e.stopPropagation();
                currentIndex = (currentIndex + 1) % totalSlides;
                showSlide(currentIndex);
            });

            leftBtn.addEventListener('click', e => {
                e.stopPropagation();
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                showSlide(currentIndex);
            });

            showSlide(0);
        });
    }

    // Expose globally for AJAX re-init
    window.initVehicleSliders = initVehicleSliders;

    // Init on page load
    initVehicleSliders(document);

    // ── Hours Tab Toggle ──────────────────────────────────────────────────────
    document.querySelectorAll('.btn-group button[data-tab]').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;

            btn.parentElement.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const tabContainer = btn.closest('.btn-group').nextElementSibling;

            tabContainer?.querySelectorAll('.tab-content').forEach(content => {
                content.classList.toggle('d-none', content.dataset.tab !== tab);
            });
        });
    });

    // ── Phone Formatting — (###) ###-#### ─────────────────────────────────────

    function formatPhone(raw) {
        var digits = raw.replace(/\D/g, '').slice(0, 10);
        if (digits.length === 0) return '';
        if (digits.length <= 3)  return '(' + digits;
        if (digits.length <= 6)  return '(' + digits.slice(0, 3) + ') ' + digits.slice(3);
        return '(' + digits.slice(0, 3) + ') ' + digits.slice(3, 6) + '-' + digits.slice(6);
    }

    function onPhoneInput(e) {
        var input = e.target;
        var prev  = input.value;
        var next  = formatPhone(prev);
        input.value = next;
        if (input.selectionStart === prev.length) {
            input.setSelectionRange(next.length, next.length);
        }
    }

    function onPhoneKeydown(e) {
        var allowed = [8, 9, 13, 27, 35, 36, 37, 38, 39, 40, 46];
        if (allowed.indexOf(e.keyCode) !== -1) return;
        if (e.ctrlKey || e.metaKey) return;
        if (e.key && !/\d/.test(e.key)) e.preventDefault();
    }

    function applyPhone(input) {
        if (input.dataset.phoneFormatted) return;
        input.dataset.phoneFormatted = '1';
        input.setAttribute('placeholder', '(___) ___-____');
        input.setAttribute('maxlength', '14');
        input.addEventListener('input',   onPhoneInput);
        input.addEventListener('keydown', onPhoneKeydown);
    }

    // ── SSN Formatting — ###-##-#### ──────────────────────────────────────────

    function formatSSN(raw) {
        var digits = raw.replace(/\D/g, '').slice(0, 9);
        if (digits.length === 0) return '';
        if (digits.length <= 3)  return digits;
        if (digits.length <= 5)  return digits.slice(0, 3) + '-' + digits.slice(3);
        return digits.slice(0, 3) + '-' + digits.slice(3, 5) + '-' + digits.slice(5);
    }

    function onSsnInput(e) {
        var input = e.target;
        var prev  = input.value;
        var next  = formatSSN(prev);
        input.value = next;
        if (input.selectionStart === prev.length) {
            input.setSelectionRange(next.length, next.length);
        }
    }

    function onSsnKeydown(e) {
        var allowed = [8, 9, 13, 27, 35, 36, 37, 38, 39, 40, 46];
        if (allowed.indexOf(e.keyCode) !== -1) return;
        if (e.ctrlKey || e.metaKey) return;
        if (e.key && !/\d/.test(e.key)) e.preventDefault();
    }

    function applySSN(input) {
        if (input.dataset.ssnFormatted) return;
        input.dataset.ssnFormatted = '1';
        input.setAttribute('placeholder', '___-__-____');
        input.setAttribute('maxlength', '11');
        input.addEventListener('input',   onSsnInput);
        input.addEventListener('keydown', onSsnKeydown);
    }

    // ── Init & Re-init ────────────────────────────────────────────────────────

    function initInputFormats() {
        document.querySelectorAll('input[type="tel"]').forEach(applyPhone);
        document.querySelectorAll('input[name*="ssn"], input[name*="SSN"]').forEach(applySSN);
    }

    initInputFormats();

    // Re-apply when any offcanvas opens (forms inside offcanvas)
    document.addEventListener('show.bs.offcanvas', function () {
        setTimeout(initInputFormats, 50);
    });

    window.InputFormat = { init: initInputFormats, applyPhone: applyPhone, applySSN: applySSN };

});
