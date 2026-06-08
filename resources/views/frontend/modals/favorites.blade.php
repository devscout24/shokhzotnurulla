{{-- Favorites Modal --}}
<div class="modal fade" id="modalFavorites" tabindex="-1" aria-labelledby="modalFavoritesLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
            {{-- Modal Header --}}
            <div class="modal-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: #212121; color: #ffffff;">
                <h5 class="modal-title d-flex align-items-center m-0" id="modalFavoritesLabel" style="font-size: 1.15rem; font-weight: 600; letter-spacing: 0.3px;">
                    <i class="fa-solid fa-heart me-2 text-danger animate-pulse" style="font-size: 1.25rem;"></i>
                    <span>Favorite Items</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.85rem; opacity: 0.8; transition: opacity 0.2s;"></button>
            </div>
            
            {{-- Modal Body --}}
            <div class="modal-body p-0" id="modalFavoritesBody" data-favorites-menu style="min-height: 120px;">
                {{-- Dynamically populated via favorites.js --}}
                <div class="p-4 text-center">
                    <div class="spinner-border text-primary text-opacity-50" role="status" style="width: 1.5rem; height: 1.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}
.animate-pulse {
    animation: pulse 2s infinite ease-in-out;
}
#modalFavorites .btn-close-white:hover {
    opacity: 1;
}
#modalFavorites .favorite-item {
    transition: background-color 0.2s ease, transform 0.2s ease;
}
#modalFavorites .favorite-item:hover {
    background-color: rgba(22, 107, 135, 0.04) !important;
}
#modalFavorites .remove-favorite {
    transition: color 0.15s ease, transform 0.15s ease;
}
#modalFavorites .remove-favorite:hover {
    transform: scale(1.15);
}
</style>
