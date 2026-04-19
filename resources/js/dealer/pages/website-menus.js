!function () {

    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    const ROUTES = window.menuRoutes;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const mainList   = document.getElementById('mainMenuList');
    const footerList = document.getElementById('footerMenuList');

    // ── Helpers ───────────────────────────────────────────────────────────────
    function fetchJson(url, options) {
        return fetch(url, Object.assign({
            headers: {
                'X-CSRF-TOKEN':     CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'Content-Type':     'application/json',
            },
        }, options || {}));
    }

    function toast(type, msg) {
        if (window.toastr) window.toastr[type](msg);
        else alert(msg);
    }

    function activeLocation() {
        return footerList.style.display !== 'none' ? 'footer' : 'main';
    }

    // ── Build DOM from data ───────────────────────────────────────────────────
    function buildItem(item, isChild) {
        var li = document.createElement('li');
        li.className    = isChild ? 'mn-child-item' : 'mn-item';
        li.dataset.id   = item.id;
        li.dataset.label  = item.label;
        li.dataset.url    = item.url;
        li.dataset.target = item.target;

        var row = '<div class="mn-item-row">'
            + '<span class="mn-drag-handle">⠿</span>'
            + '<span class="mn-item-label">' + escHtml(item.label) + '</span>'
            + '<div class="mn-item-actions">'
            + '<button class="mn-btn-edit btn-edit-link" type="button"><i class="bi bi-pencil"></i> Edit</button>'
            + '<button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>'
            + '</div></div>';

        li.innerHTML = row;

        // Children (only for main menu top-level)
        if (!isChild && item.children && item.children.length) {
            var ul = document.createElement('ul');
            ul.className = 'mn-children';
            item.children.forEach(function (child) {
                ul.appendChild(buildItem(child, true));
            });
            li.appendChild(ul);
        } else if (!isChild && activeLocation() === 'main') {
            // Empty children container for drop target
            var ul = document.createElement('ul');
            ul.className = 'mn-children';
            li.appendChild(ul);
        }

        return li;
    }

    function escHtml(str) {
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // ── Render full list ──────────────────────────────────────────────────────
    function renderList(list, items, isFooter) {
        list.innerHTML = '';
        items.forEach(function (item) {
            list.appendChild(buildItem(item, false));
        });
    }

    // ── Fetch & render on load ────────────────────────────────────────────────
    function loadMenus() {
        fetch(ROUTES.data, {
            headers: { 'Accept': 'application/json' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            renderList(mainList,   data.main   || [], false);
            renderList(footerList, data.footer || [], true);
            updateParentDropdown();
        })
        .catch(function () {
            toast('error', 'Failed to load menus.');
        });
    }

    loadMenus();

    // ── Tab switching ─────────────────────────────────────────────────────────
    document.querySelectorAll('.mn-menu-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.mn-menu-tab').forEach(function (t) {
                t.classList.remove('active');
            });
            tab.classList.add('active');

            var menu = tab.dataset.menu;
            mainList.style.display   = menu === 'main'   ? '' : 'none';
            footerList.style.display = menu === 'footer' ? '' : 'none';

            var pw = document.getElementById('alParentWrap');
            if (pw) pw.style.display = menu === 'footer' ? 'none' : '';
        });
    });

    // ── Remove ────────────────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove-item');
        if (!btn) return;

        var item = btn.closest('.mn-item, .mn-child-item');
        if (!item) return;
        if (!confirm('Remove this menu item?')) return;

        var id = item.dataset.id;

        fetchJson(ROUTES.destroy.replace('__ID__', id), { method: 'DELETE' })
        .then(function (res) { return res.json(); })
        .then(function (result) {
            if (result.success) {
                item.remove();
                updateParentDropdown();
                toast('success', result.message);
            } else {
                toast('error', result.message || 'Could not remove item.');
            }
        })
        .catch(function () { toast('error', 'Network error.'); });
    });

    // ── Modal ─────────────────────────────────────────────────────────────────
    var overlay    = document.getElementById('addLinkModal');
    var alTitle    = document.getElementById('alModalTitle');
    var alLabel    = document.getElementById('alLabel');
    var alUrl      = document.getElementById('alUrl');
    var alTarget   = document.getElementById('alTarget');
    var alParent   = document.getElementById('alParent');
    var editingItem = null;

    function openModal(mode, item) {
        alTitle.textContent = mode === 'edit' ? 'Edit Link' : 'Add Link';

        alLabel.value  = item ? item.dataset.label  : '';
        alUrl.value    = item ? item.dataset.url    : '';
        alTarget.value = item ? item.dataset.target : '_self';

        if (alParent) {

            if (mode === 'edit' && item) {

                let parentLi = item.closest('.mn-children')?.closest('.mn-item');

                if (parentLi) {
                    alParent.value = parentLi.dataset.id;
                } else {
                    alParent.value = ''; // top level
                }

            } else {
                alParent.value = '';
            }
        }

        editingItem = mode === 'edit' ? item : null;
        overlay.classList.add('active');
    }

    function closeModal() {
        overlay.classList.remove('active');
        editingItem = null;

        alLabel.value  = '';
        alUrl.value    = '';
        alTarget.value = '_self';

        if (alParent) {
            alParent.value = '';
        }
    }

    // Update parent dropdown dynamically
    function updateParentDropdown() {
        if (!alParent) return;
        var current = alParent.value;
        alParent.innerHTML = '<option value="">— None (top level) —</option>';
        mainList.querySelectorAll(':scope > .mn-item').forEach(function (li) {
            var opt = document.createElement('option');
            opt.value       = li.dataset.id;
            opt.textContent = li.dataset.label;
            alParent.appendChild(opt);
        });
        alParent.value = current;
    }

    document.getElementById('btnAddLink').addEventListener('click', function () {
        openModal('add', null);
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit-link');
        if (btn) openModal('edit', btn.closest('.mn-item, .mn-child-item'));
    });

    document.getElementById('btnCloseAlModal').addEventListener('click', closeModal);
    document.getElementById('btnCancelAlModal').addEventListener('click', closeModal);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    // ── Save (Add / Edit) ─────────────────────────────────────────────────────
    document.getElementById('btnSaveAlModal').addEventListener('click', function () {
        var label    = alLabel.value.trim();
        var url      = alUrl.value.trim();
        var target   = alTarget.value;
        var parentId = alParent ? alParent.value : '';

        if (!label) { alert('Please enter a label.'); return; }
        if (!url)   { alert('Please enter a URL.');   return; }

        if (editingItem) {
            // ── EDIT ──────────────────────────────────────────────────────
            var id = editingItem.dataset.id;

            fetchJson(ROUTES.update.replace('__ID__', id), {
                method: 'PATCH',
                body: JSON.stringify({
                    label: label,
                    url: url,
                    target: target,
                    parent_id: parentId || null
                }),
            })
            .then(function (res) { return res.json(); })
            .then(function (result) {
                if (result.success) {
                    // DOM update
                    editingItem.dataset.label  = label;
                    editingItem.dataset.url    = url;
                    editingItem.dataset.target = target;
                    var labelEl = editingItem.querySelector(':scope > .mn-item-row .mn-item-label');
                    if (labelEl) labelEl.textContent = label;
                    updateParentDropdown();
                    toast('success', result.message);
                    closeModal();
                    loadMenus()
                } else {
                    toast('error', result.message || 'Could not update.');
                }
            })
            .catch(function () { toast('error', 'Network error.'); });

        } else {
            // ── ADD ───────────────────────────────────────────────────────
            var location = activeLocation();

            fetchJson(ROUTES.store, {
                method: 'POST',
                body: JSON.stringify({
                    location:  location,
                    label:     label,
                    url:       url,
                    target:    target,
                    parent_id: parentId || null,
                }),
            })
            .then(function (res) { return res.json(); })
            .then(function (result) {
                if (result.success) {
                    var item = result.item;
                    var isChild = !!item.parent_id;
                    var li = buildItem(item, isChild);

                    if (isChild) {
                        // Parent ke children ul mein add karo
                        var parentLi = mainList.querySelector('[data-id="' + item.parent_id + '"]');
                        if (parentLi) {
                            var childrenUl = parentLi.querySelector('.mn-children');
                            if (!childrenUl) {
                                childrenUl = document.createElement('ul');
                                childrenUl.className = 'mn-children';
                                parentLi.appendChild(childrenUl);
                            }
                            childrenUl.appendChild(li);
                        }
                    } else {
                        var activeList = location === 'footer' ? footerList : mainList;
                        activeList.appendChild(li);
                    }

                    updateParentDropdown();
                    toast('success', result.message);
                    closeModal();
                } else {
                    toast('error', result.message || 'Could not add item.');
                }
            })
            .catch(function () { toast('error', 'Network error.'); });
        }
    });

    // ── Reorder (after drag drop) ─────────────────────────────────────────────
    function saveReorder() {
        var activeList = activeLocation() === 'footer' ? footerList : mainList;
        var items = [];

        activeList.querySelectorAll(':scope > .mn-item').forEach(function (li, i) {
            var entry = { id: parseInt(li.dataset.id), order: i };
            entry.children = [];
            var childrenUl = li.querySelector(':scope > .mn-children');
            if (childrenUl) {
                childrenUl.querySelectorAll(':scope > .mn-child-item').forEach(function (cli, j) {
                    entry.children.push({ id: parseInt(cli.dataset.id), order: j });
                });
            }
            items.push(entry);
        });

        fetchJson(ROUTES.reorder, {
            method: 'POST',
            body:   JSON.stringify({ items: items }),
        })
        .then(function (res) {
            return res.json();
        })
        .then(function (result) {
            if (result.success) {
                toast('success', result.message);
            } else {
                toast('error', 'Reorder failed.');
            }
        })
        .catch(function () { toast('error', 'Network error.'); });
    }

    /* ══════════════════════════════════
       DRAG & DROP
    ══════════════════════════════════ */
    var dragging = null, clone = null, ph = null, isChild = false, ox = 0, oy = 0;

    function getPh() {
        if (!ph) { ph = document.createElement('li'); ph.className = 'mn-ph'; }
        return ph;
    }

    function bind(li) {
        if (li._b) return; li._b = 1;
        var h = li.querySelector(':scope > .mn-item-row > .mn-drag-handle');
        if (!h) return;

        h.addEventListener('mousedown', function (e) {
            e.preventDefault();
            e.stopPropagation();

            dragging = li;
            isChild  = li.classList.contains('mn-child-item');

            var r = li.getBoundingClientRect();
            ox = e.clientX - r.left;
            oy = e.clientY - r.top;

            clone = li.cloneNode(true);
            clone.className = li.className + ' mn-clone';
            clone.style.width = r.width + 'px';
            clone.style.left  = r.left  + 'px';
            clone.style.top   = r.top   + 'px';
            document.body.appendChild(clone);

            li.classList.add('is-dragging');

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup',   onUp);
        });
    }

    function onMove(e) {
        if (!dragging || !clone) return;
        clone.style.left = (e.clientX - ox) + 'px';
        clone.style.top  = (e.clientY - oy) + 'px';

        clone.style.display = 'none';
        var under = document.elementFromPoint(e.clientX, e.clientY);
        clone.style.display = '';
        if (!under) return;

        var p = getPh();
        var t = under.closest(isChild ? '.mn-child-item' : '.mn-item:not(.mn-child-item)');

        if (t && t !== dragging && t !== p) {
            var r   = t.getBoundingClientRect();
            var mid = r.top + r.height / 2;
            if (e.clientY < mid) t.parentNode.insertBefore(p, t);
            else                 t.parentNode.insertBefore(p, t.nextSibling);
        }
    }

    function onUp() {
        if (!dragging) return;
        var p = getPh();
        if (p.parentNode) p.parentNode.insertBefore(dragging, p);
        if (p.parentNode) p.parentNode.removeChild(p);

        dragging.classList.remove('is-dragging');
        if (clone) { clone.parentNode && clone.parentNode.removeChild(clone); clone = null; }

        saveReorder(); // ← reorder save karo
        dragging = null;

        document.removeEventListener('mousemove', onMove);
        document.removeEventListener('mouseup',   onUp);
    }

    function bindAll() {
        document.querySelectorAll('.mn-item, .mn-child-item').forEach(bind);
    }

    bindAll();
    new MutationObserver(bindAll).observe(document.body, { childList: true, subtree: true });

}();