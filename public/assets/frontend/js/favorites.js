/**
 * Favorites Manager
 * Persists favorite vehicles in localStorage and updates the header dropdown.
 */

(function () {
    "use strict";

    const STORAGE_KEY = "dealer_favorites";

    function getFavorites() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            return stored ? JSON.parse(stored) : [];
        } catch (e) {
            console.error("Error reading favorites from localStorage", e);
            return [];
        }
    }

    function saveFavorites(favorites) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(favorites));
        updateDropdown();
        updateButtonStates();
        updateBadge(favorites.length);
        
        window.dispatchEvent(new CustomEvent("favorites:updated", { detail: { count: favorites.length } }));
    }

    function updateBadge(count) {
        const badges = document.querySelectorAll("#fav-count, #fav-count-mobile");
        badges.forEach(badge => {
            badge.textContent = count;
            if (count > 0) {
                badge.classList.remove("d-none");
            } else {
                badge.classList.add("d-none");
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
        const dropdownLists = document.querySelectorAll("[data-favorites-menu]");
        const favorites = getFavorites();

        if (dropdownLists.length === 0) return;

        let content = "";
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
            content += "<div class=\"favorites-scroll\" style=\"max-height: 400px; overflow-y: auto;\">";
            favorites.forEach(v => {
                const img = v.image || "/assets/frontend/img/no-photo.webp";
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
            content += "</div>";
            content += `<div class="p-2 border-top bg-light">
                        <a href="/inventory" class="btn btn-primary btn-sm w-100">View All Inventory</a>
                     </div>`;
        }

        dropdownLists.forEach(list => {
            list.innerHTML = content;
            list.querySelectorAll(".remove-favorite").forEach(btn => {
                btn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = btn.dataset.id;
                    let favs = getFavorites();
                    favs = favs.filter(f => String(f.id) !== String(id));
                    saveFavorites(favs);
                    if (window.toastr) toastr.info("Removed from favorites");
                };
            });
        });
    }

    function updateButtonStates() {
        const favorites = getFavorites();
        const ids = favorites.map(f => String(f.id));
        document.querySelectorAll("[data-cy=\"btn-favorite\"]").forEach(btn => {
            const id = String(btn.dataset.vehicleId);
            const icon = btn.querySelector(".fa-heart");
            if (!icon) return;
            if (ids.includes(id)) {
                btn.classList.add("active");
                icon.classList.remove("greyIcon", "fa-regular");
                icon.classList.add("text-danger", "fa-solid");
            } else {
                btn.classList.remove("active");
                icon.classList.add("greyIcon");
                icon.classList.remove("text-danger", "fa-solid");
                icon.classList.add("fa-regular");
            }
        });
    }

    function init() {
        document.addEventListener("click", (e) => {
            const favBtn = e.target.closest("[data-cy=\"btn-favorite\"]");
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
                if (vehicle.id && vehicle.id !== "undefined") {
                    const added = toggleFavorite(vehicle);
                    const icon = favBtn.querySelector(".fa-heart");
                    if (icon) {
                        icon.style.transform = "scale(1.4)";
                        setTimeout(() => icon.style.transform = "scale(1)", 200);
                    }
                    if (window.toastr) {
                        if (added) toastr.success("Saved to favorites");
                        else toastr.info("Removed from favorites");
                    }
                }
            }
        });
        updateDropdown();
        updateButtonStates();
        updateBadge(getFavorites().length);
        const observer = new MutationObserver(() => {
            updateButtonStates();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    window.refreshFavoritesDropdown = updateDropdown;

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
