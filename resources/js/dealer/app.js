import flatpickr from "flatpickr";

window.flatpickr = flatpickr;

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
window.Chart = Chart;


// Nav active switch & view switching
// Primary nav only (top bar links)
// var navItems = document.querySelectorAll('.topbar-inner .nav-links > .nav-item');
// navItems.forEach(function (item) {
//     item.addEventListener('click', function () {
//         var view = this.getAttribute('data-view') || 'dashboard';

//         navItems.forEach(function (i) {
//             if (i.getAttribute('data-view') === view) i.classList.add('active');
//             else i.classList.remove('active');
//         });

//         document.querySelectorAll('.view-content').forEach(function (v) { v.hidden = true; });
//         var el = document.querySelector('.view-content[data-view="' + view + '"]');
//         if (el) el.hidden = false;

//         var sidebar = document.querySelector('.sidebar');
//         var pageHeader = document.querySelector('.page-header');
//         if (sidebar && pageHeader) {
//             if (view === 'inventory') {
//                 sidebar.style.display = 'none';
//                 pageHeader.style.display = 'none';
//             } else {
//                 sidebar.style.display = 'block';
//                 pageHeader.style.display = 'block';
//             }
//         }

//         var secondary = document.querySelector('.topbar-secondary');
//         if (secondary) {
//             secondary.style.display = (view === 'inventory' ? 'flex' : 'none');
//         }

//         if (view === 'inventory') {
//             var secondaryItems = document.querySelectorAll('.topbar-secondary .nav-item');
//             secondaryItems.forEach(function (i) { i.classList.remove('active'); });
//             var defaultItem = document.querySelector('.topbar-secondary .nav-item[data-view="dashboard"]');
//             if (defaultItem) defaultItem.classList.add('active');

//             document.querySelectorAll('.subview').forEach(function (v) { v.hidden = true; });
//             var dashSubview = document.querySelector('.subview[data-subview="dashboard"]');
//             if (dashSubview) dashSubview.hidden = false;
//         }

//         var title = document.querySelector('.view-title');
//         if (title) {
//             var titleMap = {
//                 dashboard: 'Dashboard',
//                 inventory: 'Inventory',
//                 connections: 'Connections',
//                 whatsnew: "What's New",
//                 incentives: 'Incentives',
//                 pricing: 'Pricing Specials',
//                 settings: 'Settings',
//                 reports: 'Reports'
//             };
//             title.innerText = titleMap[view] || title.innerText || 'Dashboard';
//         }
//     });
// });

// Handle secondary nav subview switching (inside Inventory view)
// var secondaryNavItems = document.querySelectorAll('.topbar-secondary .nav-item');
// secondaryNavItems.forEach(function (item) {
//     item.addEventListener('click', function () {
//         var subview = this.getAttribute('data-view') || 'dashboard';

//         secondaryNavItems.forEach(function (i) {
//             if (i.getAttribute('data-view') === subview) i.classList.add('active');
//             else i.classList.remove('active');
//         });

//         document.querySelectorAll('.subview').forEach(function (v) { v.hidden = true; });
//         var subviewEl = document.querySelector('.subview[data-subview="' + subview + '"]');
//         if (subviewEl) subviewEl.hidden = false;

//         var invSidebar = document.querySelector('.inv-sidebar');
//         if (invSidebar) {
//             invSidebar.style.display = (subview === 'inventory' ? 'none' : 'block');
//         }
//     });
// });

// Initialize inventory date picker
if (document.querySelector('#inventoryDateRange')) {
    flatpickr('#inventoryDateRange', {
        mode: 'range',
        dateFormat: 'm/d/Y',
        defaultDate: ['01/31/2026', '02/27/2026']
    });
}

// Collapsible filter sections (event delegation keeps it resilient)
(function () {
    document.addEventListener('click', function (e) {
        var header = e.target.closest('.inv-filters .filter-block h4');
        if (!header) return;
        var block = header.closest('.filter-block');
        if (!block) return;
        block.classList.toggle('collapsed');
    });
})();

document.querySelectorAll('.sidebar-item').forEach(function (item) {
    item.addEventListener('click', function () {
        document.querySelectorAll('.sidebar-item').forEach(function (i) { i.classList.remove('active'); });
        this.classList.add('active');
    });
});

// Mobile toggle
var mobileToggle = document.getElementById('mobileToggle');
var sidebarEl = document.getElementById('sidebar');
if (mobileToggle && sidebarEl) {
    mobileToggle.addEventListener('click', function () {
        sidebarEl.classList.toggle('active');
    });
}

// Date picker
if (document.querySelector('#dateRange')) {
    flatpickr('#dateRange', {
        mode: 'range',
        dateFormat: 'm/d/Y',
        defaultDate: ['01/31/2026', '02/27/2026']
    });
}

function initSimpleDropdown(toggleId, menuId, syncAriaOnToggle) {
    var toggle = document.getElementById(toggleId);
    var menu = document.getElementById(menuId);
    var wrapper = toggle ? toggle.closest('.settings-dropdown, .mobile-row-2') : null;

    if (!toggle || !menu) return;

    function openMenu() {
        menu.classList.add('show');
        menu.setAttribute('aria-hidden', 'false');
        if (wrapper) wrapper.classList.add('is-open');
        if (syncAriaOnToggle) toggle.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
        menu.classList.remove('show');
        menu.setAttribute('aria-hidden', 'true');
        if (wrapper) wrapper.classList.remove('is-open');
        if (syncAriaOnToggle) toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (menu.classList.contains('show')) closeMenu();
        else openMenu();
    });

    document.addEventListener('click', function (e) {
        if (!menu.classList.contains('show')) return;
        var target = e.target;
        if (!menu.contains(target) && !toggle.contains(target)) closeMenu();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
}

// Top and mobile dropdowns
initSimpleDropdown('settingsToggle', 'settingsMenu', true);
initSimpleDropdown('mobileAdminToggle', 'mobileAdminMenu', false);
initSimpleDropdown('mobileSettingsToggle', 'mobileSettingsMenu', false);

// Hard fallback: ensure top Settings + inventory filter headers always toggle.
(function () {
    if (document.documentElement.getAttribute('data-dashboard-fallback-bound') === '1') return;
    document.documentElement.setAttribute('data-dashboard-fallback-bound', '1');

    document.addEventListener('click', function (e) {
        var settingsBtn = e.target.closest('#settingsToggle');
        var settingsMenu = document.getElementById('settingsMenu');
        if (settingsBtn && settingsMenu) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            var isOpen = settingsMenu.classList.contains('show');
            if (isOpen) {
                settingsMenu.classList.remove('show');
                settingsMenu.setAttribute('aria-hidden', 'true');
                settingsBtn.setAttribute('aria-expanded', 'false');
            } else {
                settingsMenu.classList.add('show');
                settingsMenu.setAttribute('aria-hidden', 'false');
                settingsBtn.setAttribute('aria-expanded', 'true');
            }
            return;
        }

        var filterHeader = e.target.closest('.inv-filters .filter-block h4');
        if (filterHeader) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            var filterBlock = filterHeader.closest('.filter-block');
            if (filterBlock) filterBlock.classList.toggle('collapsed');
            return;
        }

        if (settingsMenu && settingsMenu.classList.contains('show')) {
            if (!settingsMenu.contains(e.target)) {
                settingsMenu.classList.remove('show');
                settingsMenu.setAttribute('aria-hidden', 'true');
                if (settingsBtn) settingsBtn.setAttribute('aria-expanded', 'false');
            }
        }
    }, true);

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        var settingsBtn = document.getElementById('settingsToggle');
        var settingsMenu = document.getElementById('settingsMenu');
        if (!settingsMenu) return;
        settingsMenu.classList.remove('show');
        settingsMenu.setAttribute('aria-hidden', 'true');
        if (settingsBtn) settingsBtn.setAttribute('aria-expanded', 'false');
    });
})();

// Column visibility toggles (persisted to localStorage)
(function () {
    var table = document.querySelector('.vehicle-table');
    if (!table) return;

    var headers = table.querySelectorAll('thead th');
    headers.forEach(function (th, idx) { th.setAttribute('data-col', idx); });

    table.querySelectorAll('tbody tr').forEach(function (tr) {
        tr.querySelectorAll('td').forEach(function (td, idx) { td.setAttribute('data-col', idx); });
    });

    var menu = document.getElementById('columnsMenu');
    var toggle = document.getElementById('columnsToggle');
    var storageKey = 'vehicleHiddenCols';

    function getHidden() {
        try { return JSON.parse(localStorage.getItem(storageKey)) || []; }
        catch (e) { return []; }
    }

    function setHidden(arr) {
        localStorage.setItem(storageKey, JSON.stringify(arr));
    }

    function applyHidden() {
        var hidden = getHidden();
        headers.forEach(function (th, idx) {
            th.style.display = (hidden.indexOf(idx) !== -1) ? 'none' : '';
        });
        table.querySelectorAll('tbody tr').forEach(function (tr) {
            tr.querySelectorAll('td').forEach(function (td) {
                var idx = Number(td.getAttribute('data-col'));
                td.style.display = (hidden.indexOf(idx) !== -1) ? 'none' : '';
            });
        });
        if (menu) {
            menu.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
                var idx = Number(cb.getAttribute('data-col'));
                cb.checked = getHidden().indexOf(idx) === -1;
            });
        }

        var info = document.querySelector('.columns-info');
        if (info) {
            var visible = headers.length - hidden.length;
            info.textContent = '/ ' + visible + ' Columns Displayed';
        }
    }

    if (toggle && menu) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (menu.hasAttribute('hidden')) menu.removeAttribute('hidden');
            else menu.setAttribute('hidden', '');
        });

        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target) && e.target !== toggle) menu.setAttribute('hidden', '');
        });
    }

    if (!localStorage.getItem(storageKey)) setHidden([]);
    applyHidden();
})();

// ========================================
// INVENTORY LISTING DROPDOWNS
// ========================================

document.addEventListener('DOMContentLoaded', function () {
    function initDropdown(dropdown) {
        var toggle = dropdown.querySelector('.dropdown-toggle');
        var menu = dropdown.querySelector('.dropdown-menu');

        if (!toggle || !menu) return;

        function showMenu() {
            menu.classList.add('show');
            toggle.classList.add('active');
            toggle.setAttribute('aria-expanded', 'true');
        }

        function hideMenu() {
            menu.classList.remove('show');
            toggle.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (menu.classList.contains('show')) {
                hideMenu();
                return;
            }

            document.querySelectorAll('.listing-dropdown .dropdown-menu, .columns-dropdown .dropdown-menu').forEach(function (m) {
                m.classList.remove('show');
            });
            document.querySelectorAll('.listing-dropdown .dropdown-toggle, .columns-dropdown .dropdown-toggle').forEach(function (t) {
                t.classList.remove('active');
                t.setAttribute('aria-expanded', 'false');
            });

            showMenu();
        });

        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target) && !toggle.contains(e.target)) {
                hideMenu();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') hideMenu();
        });
    }

    document.querySelectorAll('.listing-dropdown, .columns-dropdown').forEach(function (dropdown) {
        initDropdown(dropdown);
    });

    // Keep one chevron icon (same family as filter icon) on listing controls.
    document.querySelectorAll('.listing-dropdown .dropdown-toggle, .columns-dropdown .dropdown-toggle').forEach(function (btn) {
        if (!btn.querySelector('.bi-chevron-down')) {
            btn.insertAdjacentHTML('beforeend', ' <i class="bi bi-chevron-down"></i>');
        }
    });

    function bindLabelUpdate(menuSelector, transformLabel) {
        var menu = document.querySelector(menuSelector);
        if (!menu) return;

        var wrapper = menu.closest('.listing-dropdown, .columns-dropdown');
        if (!wrapper) return;

        var btn = wrapper.querySelector('.dropdown-toggle');
        if (!btn) return;

        menu.querySelectorAll('.dropdown-item').forEach(function (item) {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                btn.innerHTML = transformLabel(this.textContent.trim()) + ' <i class="bi bi-chevron-down"></i>';
                btn.setAttribute('aria-expanded', 'false');
                menu.classList.remove('show');
                btn.classList.remove('active');
            });
        });
    }

    bindLabelUpdate('.limit-dropdown-menu', function (value) { return 'Limit ' + value; });
    bindLabelUpdate('.display-dropdown-menu', function (value) { return value; });
    bindLabelUpdate('.list-dropdown-menu', function (value) { return value; });
    bindLabelUpdate('.columns-dropdown-menu', function (value) { return value + ' Columns Displayed'; });
});
