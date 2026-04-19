@extends('layouts.base')

@push('panel-assets')
    @vite([
        'resources/css/frontend/app.css',
        'resources/js/frontend/app.js',
    ])
@endpush

@section('panel-content')

    {{-- Header (Desktop & Mobile) --}}
    @include('frontend.partials.header')

    {{-- ── Page Content ─────────────────────────────────────────────────── --}}
    @yield('page-content')

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    @include('frontend.partials.footer')

    {{-- ── Feedback label ──────────────────────────────────────────────────────── --}}
    <div class="userback-button-container" loadtype="web" id="userback_button_container" data-html2canvas-ignore="true"
        nextgen="1" data-ub-colour-scheme="light">
        <div class="userback-button userback-button-e" wstyle="text" wicon="7">
            <div class="userback-button-content">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#ffff" viewBox="0 0 22 24">
                    <path
                        d="M21.9237 22.945L11.7076 0.472579C11.4211 -0.158087 10.5274 -0.157414 10.2422 0.473925L0.0757232 22.9275C-0.215476 23.5292 0.384368 24.1754 1.00367 23.927L10.7152 20.2184C10.8897 20.1484 11.0843 20.1484 11.2581 20.2184L20.9951 23.9452C21.6144 24.1942 22.2149 23.5481 21.9231 22.9457L21.9237 22.945Z">
                    </path>
                </svg>Feedback
            </div>
        </div>
    </div>
@endsection

@push('panel-modals')
    @include('frontend.offcanvas.mobile-drawer-header-menu')
    @include('frontend.offcanvas.location-menu')
    @include('frontend.modals.main-search')
@endpush