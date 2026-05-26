(function () {
    'use strict';

    const STORAGE_KEY = 'dealer_favorites';

    function getFavorites() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            return stored ? JSON.parse(stored) : [];
        } catch (e) {
            console.error('Error reading favorites from localStorage', e);
            return [];
        }
    }

    function saveFavorites(favorites) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(favorites));
        updateDropdown();
        updateButtonStates();
        updateBadge(favorites.length);
        
        // Dispatch custom event for any other listeners
        window.dispatchEvent(new CustomEvent('favorites:updated', { detail: { count: favorites.length } }));
    }

    function updateBadge(count) {
        const badges = document.querySelectorAll('#fav-count, #fav-count-mobile');
        badges.forEach(badge => {
            badge.textContent = count;
            if (count > 0) {
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        });
    }

    function toggleFavorite(vehicle) {
        let favorites = getFavorites();
        const vehicleId = String(vehicle.id);
        const index = favorites.findIndex(f => String(f.id) === vehicleId);
        let added = false;

        if (index > -1) {
            favorites.splice(index, 1);
            added = false;
        } else {
            favorites.push(vehicle);
            added = true;
        }

        saveFavorites(favorites);
        return added;
    }

    function updateDropdown() {
        // Find all potential dropdown menus for favorites
        const dropdownLists = document.querySelectorAll('#favoritesDropdown + .dropdown-menu, #favorites + .dropdown-menu, [aria-labelledby="favoritesDropdown"]');
        const favorites = getFavorites();

        if (dropdownLists.length === 0) return;

        let content = '';
        if (favorites.length === 0) {
            content = `
                <div class="p-4 text-center">
                    <i class="fa-solid fa-heart-crack d-block mb-2 h4 opacity-25"></i>
                    <div class="text-muted small mb-0">No items saved yet.</div>
                </div>
                <div class="p-2 border-top bg-light">
                    <a href="/inventory" class="btn btn-primary btn-sm w-100">Browse Inventory</a>
                </div>`;
        } else {
            content += '<div class="favorites-scroll" style="max-height: 400px; overflow-y: auto;">';
            favorites.forEach(v => {
                const img = v.image || '/assets/frontend/img/no-photo.webp';
                content += `
                    <div class="d-flex align-items-center p-2 border-bottom position-relative favorite-item" style="transition: background 0.2s;">
                        <img src="${img}" alt="${v.name}" class="rounded me-2" style="width: 70px; height: 50px; object-fit: cover; background: #f8f9fa;">
                        <div class="text-start overflow-hidden flex-grow-1">
                            <a href="${v.url}" class="d-block text-dark text-decoration-none fw-bold text-truncate small mb-0" title="${v.name}">${v.name}</a>
                            <div class="text-primary small fw-bold">${v.price}</div>
                        </div>
                        <button class="btn btn-link btn-sm text-danger p-1 ms-2 remove-favorite" data-id="${v.id}" title="Remove">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>`;
            });
            content += '</div>';
            content += `<div class="p-2 border-top bg-light">
                        <a href="/inventory" class="btn btn-primary btn-sm w-100">View All Inventory</a>
                     </div>`;
        }

        dropdownLists.forEach(list => {
            list.innerHTML = content;

            // Re-bind remove buttons
            list.querySelectorAll('.remove-favorite').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = btn.dataset.id;
                    let favs = getFavorites();
                    favs = favs.filter(f => String(f.id) !== String(id));
                    saveFavorites(favs);
                    if (window.toastr) toastr.info('Removed from favorites');
                });
            });
        });
    }

    function updateButtonStates() {
        const favorites = getFavorites();
        const ids = favorites.map(f => String(f.id));

        document.querySelectorAll('[data-cy="btn-favorite"]').forEach(btn => {
            const id = String(btn.dataset.vehicleId);
            const icon = btn.querySelector('.fa-heart');
            if (!icon) return;
            
            if (ids.includes(id)) {
                btn.classList.add('active');
                icon.classList.remove('greyIcon', 'fa-regular');
                icon.classList.add('text-danger', 'fa-solid');
                
                // If it's the detail page, handling potential muted classes
                const parentMuted = icon.closest('.text-muted');
                if (parentMuted && parentMuted.contains(icon)) {
                    parentMuted.style.color = '#dc3545'; // Force danger color
                }
            } else {
                btn.classList.remove('active');
                icon.classList.add('greyIcon');
                icon.classList.remove('text-danger', 'fa-solid');
                icon.classList.add('fa-regular');
                
                const parentMuted = icon.closest('.text-muted');
                if (parentMuted && parentMuted.contains(icon)) {
                    parentMuted.style.color = ''; // Reset
                }
            }
        });
    }

    function init() {
        // Toggle Dropdowns Manually (Backup for Bootstrap)
        document.addEventListener('click', function(e) {
            const toggle = e.target.closest('#favoritesDropdown, #favorites, #hoursDropdown');
            if (toggle) {
                // If Bootstrap is not working, we toggle it ourselves
                const menu = toggle.nextElementSibling;
                if (menu && menu.classList.contains('dropdown-menu')) {
                    const isShown = menu.classList.contains('show');
                    
                    // Close all other open menus first
                    document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                        if (m !== menu) {
                            m.classList.remove('show');
                            const prev = m.previousElementSibling;
                            if (prev && prev.classList.contains('dropdown-toggle')) {
                                prev.classList.remove('show');
                                prev.setAttribute('aria-expanded', 'false');
                            }
                        }
                    });
                    
                    if (!isShown) {
                        menu.classList.add('show');
                        toggle.classList.add('show');
                        toggle.setAttribute('aria-expanded', 'true');
                        // If it's the favorites dropdown, refresh data on open
                        if (toggle.id && toggle.id.includes('favorites')) {
                            updateDropdown();
                        }
                    } else {
                        menu.classList.remove('show');
                        toggle.classList.remove('show');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                    
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
            }

            // Close dropdowns when clicking outside
            if (!e.target.closest('.dropdown-menu')) {
                const openMenus = document.querySelectorAll('.dropdown-menu.show');
                if (openMenus.length > 0) {
                    openMenus.forEach(m => {
                        m.classList.remove('show');
                        const t = m.previousElementSibling;
                        if (t && t.classList.contains('dropdown-toggle')) {
                            t.classList.remove('show');
                            t.setAttribute('aria-expanded', 'false');
                        }
                    });
                }
            }

            // Delegate click event for favorite buttons (heart icons)
            const favBtn = e.target.closest('[data-cy="btn-favorite"]');
            if (favBtn) {
                e.preventDefault();
                e.stopPropagation();

                const vehicle = {
                    id: String(favBtn.dataset.vehicleId),
                    name: favBtn.dataset.vehicleName,
                    price: favBtn.dataset.vehiclePrice,
                    image: favBtn.dataset.vehicleImage,
                    url: favBtn.dataset.vehicleUrl
                };

                if (vehicle.id && vehicle.id !== 'undefined') {
                    const added = toggleFavorite(vehicle);
                    
                    // Animation
                    const icon = favBtn.querySelector('.fa-heart');
                    if (icon) {
                        icon.style.transform = 'scale(1.4)';
                        setTimeout(() => icon.style.transform = 'scale(1)', 200);
                    }

                    // Notification
                    if (window.toastr) {
                        if (added) toastr.success('Saved to favorites');
                        else toastr.info('Removed from favorites');
                    }
                }
            }
        });

        // Initialize UI
        updateDropdown();
        updateButtonStates();
        updateBadge(getFavorites().length);

        // Robust MutationObserver to handle dynamically loaded content
        const observer = new MutationObserver((mutations) => {
            let shouldUpdate = false;
            for (const mutation of mutations) {
                if (mutation.addedNodes.length > 0) {
                    shouldUpdate = true;
                    break;
                }
            }
            if (shouldUpdate) updateButtonStates();
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

                icon.classList.add('greyIcon');
                icon.classList.remove('text-danger');

                // If it's the detail page, restore text-muted to parent span if it was there
                // Actually, let's just make it simpler:
                if (btn.classList.contains('col-4')) {
                     const iconBox = icon.parentElement;
                     if (iconBox && !iconBox.classList.contains('text-muted')) {
                        iconBox.classList.add('text-muted');
                     }
                }
            }
        });
    }

    function init() {
        // Delegate click event for favorite buttons
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-cy="btn-favorite"]');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();

                const vehicle = {
                    id: btn.dataset.vehicleId,
                    name: btn.dataset.vehicleName,
                    price: btn.dataset.vehiclePrice,
                    image: btn.dataset.vehicleImage,
                    url: btn.dataset.vehicleUrl
                };

                if (vehicle.id) {
                    toggleFavorite(vehicle);
                }
            }
        });

        // Initialize UI
        updateDropdown();
        updateButtonStates();

        // Also update when dynamic content is loaded (inventory AJAX)
        const gridEl = document.getElementById('inventory-grid');
        if (gridEl) {
            const observer = new MutationObserver(() => {
                updateButtonStates();
            });
            observer.observe(gridEl, { childList: true });
        }
    }

    // Run on DOM load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
