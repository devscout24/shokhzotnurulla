@extends('layouts.base')

@push('panel-assets')
    @vite([
        'resources/css/frontend/app.css',
        'resources/js/frontend/app.js'
    ])
    <script src="{{ asset('assets/frontend/js/favorites.js') }}"></script>
@endpush


@section('panel-content')
    {{-- Header (Desktop & Mobile) --}}
    @include('frontend.partials.header')

    {{-- @if ($bannerText ?? null)
    <div class="persistent-banner py-2 text-center"
        style="background: {{ $bannerBgColor ?? 'purple' }}; color: {{ $bannerTextColor ?? '#ffffff' }};"
        title="{{ $bannerHoverTitle ?? '' }}">
        <div class="container">
            <span class="banner-message font-weight-bold">{{ $bannerText }}</span>
        </div>
    </div>
    @endif --}}

    {{-- ── Page Content ─────────────────────────────────────────────────── --}}
    @yield('page-content')

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    @include('frontend.partials.footer')

    {{-- ── Favorites Floating Button ──────────────────────────────────────────────────────── --}}
    <div class="userback-button-container" loadtype="web" id="userback_button_container" data-html2canvas-ignore="true"
        nextgen="1" data-ub-colour-scheme="light">
        <div class="userback-button userback-button-e" wstyle="text" wicon="7">
            <div class="userback-button-content" style="background-color: purple !important;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#ffffff" viewBox="0 0 24 24"
                    style="transform: rotate(90deg) !important;">
                    <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z">
                    </path>
                </svg>Favorites
                <span class="badge bg-white text-danger rounded-pill d-none ms-1" id="fav-count-floating"
                    style="font-size: 0.75rem; vertical-align: middle; position: relative; top: -1px;">0</span>
            </div>
        </div>
    </div>


    @push('page-scripts')
        <script>
            (function () {
                function closeFavoritesDropdowns() {
                    ['favoritesDropdown', 'favoritesHeader', 'favoritesMobile'].forEach(function (id) {
                        const el = document.getElementById(id);
                        if (!el) return;

                        // Close the dropdown menu sibling
                        const menu = el.nextElementSibling || el.closest('.dropdown')?.querySelector('.dropdown-menu');
                        if (menu) menu.classList.remove('show');

                        el.classList.remove('show');
                        el.setAttribute('aria-expanded', 'false');
                    });

                    // Catch any other open dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(function (m) { m.classList.remove('show'); });
                    document.querySelectorAll('.dropdown-toggle.show').forEach(function (t) {
                        t.classList.remove('show');
                        t.setAttribute('aria-expanded', 'false');
                    });
                }

                function isFeedbackClick(e) {
                    return !!(e.target && e.target.closest('#userback_button_container'));
                }

                // Block ALL three early events so Bootstrap never sees the pointerdown/mousedown
                ['pointerdown', 'mousedown', 'click'].forEach(function (type) {
                    document.addEventListener(type, function (e) {
                        if (isFeedbackClick(e)) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            if (type === 'click') e.preventDefault();

                            closeFavoritesDropdowns();

                            // Clean up any accidentally opened modals
                            document.querySelectorAll('.modal.show').forEach(function (modal) {
                                modal.classList.remove('show');
                                modal.style.display = 'none';
                                modal.setAttribute('aria-hidden', 'true');
                            });
                            document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
                            document.body.classList.remove('modal-open');
                            document.body.style.removeProperty('padding-right');

                            if (type === 'click') {
                                const modalEl = document.getElementById('modalFavorites');
                                if (modalEl && window.bootstrap) {
                                    if (typeof window.refreshFavoritesDropdown === 'function') {
                                        window.refreshFavoritesDropdown();
                                    }
                                    const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                                    modal.show();
                                }
                            }
                        }
                    }, true); // capture phase — fires before Bootstrap
                });

            })();
        </script>
    @endpush



    @push('panel-modals')
        @include('frontend.offcanvas.mobile-drawer-header-menu')
        @include('frontend.offcanvas.location-menu')
        @include('frontend.modals.main-search')
        @include('frontend.modals.favorites')
    @endpush


@endsection