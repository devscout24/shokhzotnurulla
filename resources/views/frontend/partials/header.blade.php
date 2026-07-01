@php
    $activeLocationId = app(\App\Services\Location\LocationContext::class)->getResolvedLocationId(
        $_resolvedDealerId ?? null,
    );
    $loc = null;
    if ($activeLocationId) {
        $loc = collect($locationMenuData)->firstWhere('id', $activeLocationId);
    }
    if (!$loc) {
        $loc = $locationMenuData[0] ?? null;
    }

    $salesHours = $loc['hours_by_department']['sales'] ?? [];
    $phones = $loc['phones'] ?? [];

    $salesPhone =
        collect($phones)->firstWhere('type', 'sales') ??
        (collect($phones)->firstWhere('type', 'main') ?? collect($phones)->first());

    $street1 = $loc['street1'] ?? '1339 South Lowry Street';
    $city = $loc['city'] ?? 'Smyrna';
    $state = $loc['state'] ?? 'TN';
    $postalcode = $loc['postalcode'] ?? '37167';

    $salesPhoneNumber = $salesPhone ? $salesPhone['number'] : '(615) 267-0590';
    $salesPhoneRaw = preg_replace('/\D/', '', $salesPhoneNumber);

    $fullAddress = implode(', ', array_filter([$street1, $city, $state . ' ' . $postalcode]));

    // fallback hours
    $fallbackSalesHours = [
        [
            'day_name' => 'Monday',
            'is_closed' => false,
            'appointment_only' => false,
            'open_time' => '9:00 AM',
            'close_time' => '6:00 PM',
        ],
        [
            'day_name' => 'Tuesday',
            'is_closed' => false,
            'appointment_only' => false,
            'open_time' => '9:00 AM',
            'close_time' => '6:00 PM',
        ],
        [
            'day_name' => 'Wednesday',
            'is_closed' => false,
            'appointment_only' => false,
            'open_time' => '9:00 AM',
            'close_time' => '6:00 PM',
        ],
        [
            'day_name' => 'Thursday',
            'is_closed' => false,
            'appointment_only' => false,
            'open_time' => '9:00 AM',
            'close_time' => '6:00 PM',
        ],
        [
            'day_name' => 'Friday',
            'is_closed' => false,
            'appointment_only' => false,
            'open_time' => '9:00 AM',
            'close_time' => '6:00 PM',
        ],
        [
            'day_name' => 'Saturday',
            'is_closed' => false,
            'appointment_only' => false,
            'open_time' => '10:00 AM',
            'close_time' => '6:00 PM',
        ],
        [
            'day_name' => 'Sunday',
            'is_closed' => true,
            'appointment_only' => false,
            'open_time' => '',
            'close_time' => '',
        ],
    ];

    $resolvedSalesHours = !empty($salesHours) ? $salesHours : $fallbackSalesHours;

    // current day
    $today = now()->format('l');

    $todayHours = collect($resolvedSalesHours)->firstWhere('day_name', $today);

    if ($todayHours) {
        if ($todayHours['is_closed']) {
            $todayDisplay = 'Closed Today';
        } elseif ($todayHours['appointment_only']) {
            if (!empty($todayHours['open_time']) && !empty($todayHours['close_time'])) {
                $todayDisplay = 'By Appointment ' . $todayHours['open_time'] . ' to ' . $todayHours['close_time'];
            } else {
                $todayDisplay = 'By Appointment';
            }
        } else {
            $todayDisplay = 'Open ' . $todayHours['open_time'] . ' to ' . $todayHours['close_time'];
        }
    } else {
        $todayDisplay = 'Open 9:00 AM to 6:00 PM';
    }

    $faviconUrl = null;
    $mimeType = null;
    $dealerForFavicon = app('currentDealer');
    if ($dealerForFavicon && $dealerForFavicon->favicon) {
        $faviconPath = public_path('assets/frontend/img/favicons/' . $dealerForFavicon->favicon);
        if (file_exists($faviconPath)) {
            $faviconUrl = asset('assets/frontend/img/favicons/' . $dealerForFavicon->favicon);
            $mimeType = mime_content_type($faviconPath);
        }
    }

@endphp
<!-- header desktop  -->
<style>
    .dropdown-menu.show {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        transform: none !important;
    }
</style>
<header class="d-flex d-none d-xl-block ">
    <div class="bg-preheader text-preheader theme-dark">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <!-- location  -->
                    <div
                        class="py-2 ps-2 pe-3 d-inline-block cursor-pointer border-end position-relative locationDropdown">
                        <span class="d-inline-block faIcon ofa-solid ofa-location-dot me-2">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                        <span data-bs-toggle="offcanvas" data-bs-target="#locationMenu"
                            class="d-inline-block me-1 notranslate">{{ $fullAddress }}
                            <span class="d-inline-block faIcon ofa-solid ofa-caret-down ms-2">
                                <i class="fa-solid fa-caret-down"></i>
                            </span>
                        </span>
                    </div>
                    <!-- phone -->
                    <div class="py-2 px-3 d-inline-block">
                        <a href="tel:{{ $salesPhoneRaw }}" class="text-preheader text-decoration-none">
                            <span class="d-inline-block faIcon ofa-solid ofa-phone me-2">
                                <i class="fa-solid fa-phone"></i>
                            </span>{{ $salesPhoneNumber }}
                        </a>
                    </div>

                    {{-- <div class="float-end py-2 text-end border-end">
                        <div class="dropdown">
                            <button class="py-0 text-15 text-decoration-none text-white dropdown-toggle btn btn-link">
                                <span class="d-inline-block me-2 float-start mt-0">
                                    <i class="fa-solid fa-clock"></i>
                                </span>
                                Recently Viewed
                            </button>
                            <div class="py-0 recent absolute-offset dropdown-menu">
                                <a href="#" class="border-bottom dropdown-item">
                                    <em class="text-muted">No items viewed yet.</em>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="float-end py-2 border-end text-end">
                        <div class="dropdown">
                            <button type="button" id="favoritesHeader" aria-expanded="false" data-bs-toggle="dropdown"
                                class="py-0 text-decoration-none text-15 text-white dropdown-toggle btn btn-link">
                                <span class="d-inline-block faIcon ofa-solid ofa-heart me-2">
                                    <i class="fa-solid fa-heart"></i>
                                </span>Favorites
                            </button>
                            <div class="py-0 absolute-offset recent dropdown-menu" data-favorites-menu>
                                <a href="#" class="border-bottom dropdown-item">
                                    <em class="text-muted">No items viewed yet.</em>
                                </a>
                            </div>
                        </div>
                    </div> --}}



                    <div class="float-end py-2 px-3 border-end">
                        <div class="dropdown h-100 d-flex align-items-center">
                            <a href="javascript:void(0)"
                                class="text-decoration-none text-white dropdown-toggle d-flex align-items-center"
                                id="hoursDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                style="font-size: 15px; font-weight: 500;">
                                <i class="fa-solid fa-calendar-days me-2 text-info"></i>
                                <span>{{ $todayDisplay }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-0"
                                aria-labelledby="hoursDropdown"
                                style="min-width: 320px; z-index: 9999; border-radius: 8px; overflow: hidden;">
                                <li class="px-3 py-2 bg-light border-bottom">
                                    <div class="d-flex justify-content-between w-100 fw-bold text-dark"
                                        style="font-size: 14px;">
                                        <span>Day</span>
                                        <span>Sales Hours</span>
                                    </div>
                                </li>
                                @foreach ($resolvedSalesHours as $item)
                                    <li class="px-3 py-2 d-flex justify-content-between align-items-center {{ $today === $item['day_name'] ? 'bg-primary bg-opacity-10 fw-bold' : '' }}"
                                        style="font-size: 14px; border-bottom: 1px solid #f1f1f1;">
                                        <span class="text-secondary">{{ $item['day_name'] }}</span>
                                        <span class="text-dark">
                                            @if ($item['is_closed'])
                                                <span class="text-danger">Closed</span>
                                            @elseif($item['appointment_only'])
                                                <span class="text-warning">By Appointment</span>
                                            @else
                                                {{ $item['open_time'] }} - {{ $item['close_time'] }}
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="float-end py-2 px-3 border-end dropdown">
                        <a href="javascript:void(0)"
                            class="text-white text-decoration-none d-flex align-items-center dropdown-toggle"
                            id="favoritesDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="font-size: 15px;">
                            <i class="fa-solid fa-heart me-2 text-danger"></i>
                            Favorites
                            <span class="badge bg-danger rounded-pill ms-2 d-none" id="fav-count">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-0 text-center"
                            aria-labelledby="favoritesDropdown" data-favorites-menu
                            style="min-width: 280px; overflow: hidden; z-index: 9999;">
                            <div class="p-3">
                                <i class="fa-solid fa-heart-crack d-block mb-2 h4 opacity-50"></i>
                                <span class="text-muted small">No items saved yet.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Location Pill Switcher (desktop preheader) --}}
                    @if (count($locationMenuData ?? []) > 1)
                        <div class="py-1 px-3 float-end border-end d-flex align-items-center gap-2">
                            @foreach ($locationMenuData as $locationItem)
                                @php $isActiveLoc = $activeLocationId === (int) $locationItem['id']; @endphp
                                <form action="{{ route('frontend.switch-location') }}" method="POST"
                                    class="d-inline m-0">
                                    @csrf
                                    <input type="hidden" name="location_id" value="{{ $locationItem['id'] }}">
                                    <button type="submit"
                                        class="location-pill {{ $isActiveLoc ? 'location-pill--active' : '' }}">
                                        {{ $locationItem['name'] }}{{ !empty($locationItem['city']) ? ' · ' . $locationItem['city'] : '' }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @elseif(!empty($locationMenuData))
                        <div class="py-1 px-3 float-end border-end d-flex align-items-center">
                            <span class="location-pill location-pill--active">
                                {{ $locationMenuData[0]['name'] }}{{ !empty($locationMenuData[0]['city']) ? ' · ' . $locationMenuData[0]['city'] : '' }}
                            </span>
                        </div>
                    @endif

                    {{-- <div class="float-end py-2 border-end text-end">
                        <div translate="no" class="notranslate dropdown">
                            <button type="button" id="lang-switcher" aria-expanded="false"
                                class="py-0 text-decoration-none text-15 text-white dropdown-toggle btn btn-link">
                                <img width="24" height="16" alt="Select language" class="me-2"
                                    src="{{ asset('assets/frontend/img/en.png') }}">EN
                            </button>
                            <div x-placement="bottom-start" aria-labelledby="lang-switcher"
                                class="py-0 absolute-offset2 dropdown-menu">
                                <a class="border-bottom dropdown-item" role="button" tabindex="0" href="#">
                                    <img width="24" height="16" alt="Select English" class="me-2"
                                        src="{{ asset('assets/frontend/img/en.png') }}">English
                                </a>
                                <a class="border-bottom dropdown-item" role="button" tabindex="0" href="#">
                                    <img width="24" height="16" alt="Select Spanish" class="me-2"
                                        src="{{ asset('assets/frontend/img/es.png') }}">Spanish
                                </a>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="bg-header position-relative py-2 px-3">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <a href="{{ route('frontend.home') }}" title="Angel Motors Inc" id="logo-link">
                    @php
                        $dealer = app('currentDealer') ?? null;
                        $logoPath =
                            $dealer && $dealer->logo
                                ? asset('assets/frontend/img/logos/' . $dealer->logo)
                                : asset('assets/frontend/img/angel-motors-logo-top-dealer-logo.webp');
                    @endphp
                    <img src="{{ $logoPath }}"
                        style="max-width: 325px; max-height: 65px; width: auto; height: auto;"
                        alt="{{ $dealer->company_name ?? 'Angel Motors Inc' }}">
                </a>
                <div class="ms-auto" id="header-nav">
                    <ul>

                        <li class="searchInventory">
                            <button id="search_desktop" data-bs-toggle="modal" data-bs-target="#modalSearch"
                                class="text-decoration-none text-dark bg-light btn btn-link">
                                <span class="d-inline-block me-2">
                                    <i class="fa-solid fa-magnifying-glass primaryy"></i>
                                </span>
                                Search Inventory
                            </button>
                        </li>

                        @foreach ($mainMenu as $menu)
                            <li class="cursor-pointer">
                                @if ($menu->children->count())
                                    {{ $menu->label }}
                                    <span class="d-inline-block ms-2">
                                        <i class="fa-solid fa-angle-down iconStyle"></i>
                                    </span>

                                    <ul>
                                        @foreach ($menu->children as $child)
                                            <li>
                                                <a href="{{ url($child->url) }}" target="{{ $child->target }}">
                                                    {{ $child->label }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <a href="{{ url($menu->url) }}" target="{{ $menu->target }}" style="color: #fff;">
                                        {{ $menu->label }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupDropdown(id) {
            const toggle = document.getElementById(id);
            if (!toggle) return;

            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const menu = toggle.nextElementSibling;
                if (!menu) return;

                const isShown = menu.classList.contains('show');

                // Close others
                document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                    if (m !== menu) m.classList.remove('show');
                });

                if (!isShown) {
                    menu.classList.add('show');
                    toggle.classList.add('show');
                    if (id.toLowerCase().includes('favorite') && typeof window
                        .refreshFavoritesDropdown === 'function') {
                        window.refreshFavoritesDropdown();
                    }
                } else {
                    menu.classList.remove('show');
                    toggle.classList.remove('show');
                }
            });
        }

        setupDropdown('favoritesDropdown');
        setupDropdown('hoursDropdown');
        setupDropdown('favoritesHeader');

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                    m.classList.remove('show');
                });
            }
        });
    });
</script>

<!-- Header mobile -->
<div class="d-xl-none position-fixed w-100" id="mobile-nav" style="z-index:25;top:0;left:0;">
    {{-- Main mobile nav row --}}
    <div class="py-1 d-flex w-100 align-items-center justify-content-between" id="mobile-header"
        style="background:#212121;">
        <div class="text-left w-100 ps-2" id="mobile-logo">
            <a href="{{ route('frontend.home') }}">
                @php
                    $mobileLogoPath =
                        $dealer && $dealer->logo
                            ? asset('assets/frontend/img/logos/' . $dealer->logo)
                            : asset('assets/frontend/img/angel-motors-logo-top-dealer-logo.webp');
                @endphp
                <img alt="{{ $dealer->company_name ?? 'Angel Motors Inc' }}" fetchpriority="high" loading="eager"
                    style="max-width: 145px; max-height: 50px; width: auto; height: auto;" decoding="async"
                    src="{{ $mobileLogoPath }}">
            </a>
        </div>
        <div class="d-flex align-items-center">
            <div class="my-0 ms-auto mobilePhone px-2">
                <a href="tel:+16152670590">
                    <span class="d-inline-block h2 my-0 text-white">
                        <i class="fa-solid fa-phone large"></i>
                    </span>
                </a>
            </div>
            <div class="text-end ps-2" id="menuBtn">
                <span class="d-inline-block me-2 mb-0 mt-n1 h2 text-white">
                    <i class="fa-solid fa-bars large"></i>
                </span>
            </div>
        </div>
    </div>
    {{-- Mobile location strip --}}
    @if (count($locationMenuData ?? []) > 0)
        <div class="mobile-location-strip px-3 py-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-location-dot me-1" style="font-size:0.7rem;"></i>
            @if (count($locationMenuData) > 1)
                @foreach ($locationMenuData as $locationItem)
                    @php $isActiveLoc = $activeLocationId === (int) $locationItem['id']; @endphp
                    <form action="{{ route('frontend.switch-location') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <input type="hidden" name="location_id" value="{{ $locationItem['id'] }}">
                        <button type="submit"
                            class="location-pill location-pill--sm {{ $isActiveLoc ? 'location-pill--active' : '' }}">
                            {{ $locationItem['name'] }}{{ !empty($locationItem['city']) ? ' · ' . $locationItem['city'] : '' }}
                        </button>
                    </form>
                @endforeach
            @else
                <span class="location-pill location-pill--sm location-pill--active">
                    {{ $locationMenuData[0]['name'] }}{{ !empty($locationMenuData[0]['city']) ? ' · ' . $locationMenuData[0]['city'] : '' }}
                </span>
            @endif
        </div>
    @endif
</div>

@php

@endphp

@push('base-assets')
    @if($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}" @if($mimeType) type="{{ $mimeType }}" @endif>
    @endif
@endpush
