@php
    $activeLocationId = app(\App\Services\Location\LocationContext::class)->getResolvedLocationId($_resolvedDealerId ?? null);
    $loc = null;
    if ($activeLocationId) {
        $loc = collect($locationMenuData)->firstWhere('id', $activeLocationId);
    }
    if (!$loc) {
        $loc = $locationMenuData[0] ?? null;
    }

    $salesHours = $loc['hours_by_department']['sales'] ?? [];
    $phones     = $loc['phones'] ?? [];

    $salesPhone = collect($phones)->firstWhere('type', 'sales')
                ?? collect($phones)->firstWhere('type', 'main')
                ?? collect($phones)->first();

    $street1    = $loc['street1']    ?? '1339 South Lowry Street';
    $city       = $loc['city']       ?? 'Smyrna';
    $state      = $loc['state']      ?? 'TN';
    $postalcode = $loc['postalcode'] ?? '37167';

    $salesPhoneNumber = $salesPhone ? $salesPhone['number'] : '(615) 267-0590';
    $salesPhoneRaw    = preg_replace('/\D/', '', $salesPhoneNumber);

    $fullAddress = implode(', ', array_filter([
        $street1,
        $city,
        $state . ' ' . $postalcode
    ]));

    // fallback hours
    $fallbackSalesHours = [
        ['day_name' => 'Monday', 'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM', 'close_time' => '6:00 PM'],
        ['day_name' => 'Tuesday', 'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM', 'close_time' => '6:00 PM'],
        ['day_name' => 'Wednesday', 'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM', 'close_time' => '6:00 PM'],
        ['day_name' => 'Thursday', 'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM', 'close_time' => '6:00 PM'],
        ['day_name' => 'Friday', 'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM', 'close_time' => '6:00 PM'],
        ['day_name' => 'Saturday', 'is_closed' => false, 'appointment_only' => false, 'open_time' => '10:00 AM', 'close_time' => '6:00 PM'],
        ['day_name' => 'Sunday', 'is_closed' => true, 'appointment_only' => false, 'open_time' => '', 'close_time' => ''],
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
@endphp
<!-- header desktop  -->
<header class="d-flex d-none d-xl-block ">
    <div class="bg-preheader text-preheader theme-dark">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <!-- location  -->
                    <div class="py-2 ps-2 pe-3 d-inline-block cursor-pointer border-end position-relative locationDropdown">
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
                            <button type="button" id="favorites" aria-expanded="false"
                                class="py-0 text-decoration-none text-15 text-white dropdown-toggle btn btn-link">
                                <span class="d-inline-block faIcon ofa-solid ofa-heart me-2">
                                    <i class="fa-solid fa-heart"></i>
                                </span>Favorites
                            </button>
                            <div class="py-0 absolute-offset recent dropdown-menu">
                                <a href="#" class="border-bottom dropdown-item">
                                    <em class="text-muted">No items viewed yet.</em>
                                </a>
                            </div>
                        </div>
                    </div> --}}

                    <div class="py-2 px-3 float-end text-end border-end">
                        <span>{{ $todayDisplay }}</span>
                    </div>

                    {{-- Location Pill Switcher (desktop preheader) --}}
                    @if(count($locationMenuData ?? []) > 1)
                        <div class="py-1 px-3 float-end border-end d-flex align-items-center gap-2">
                            @foreach($locationMenuData as $locationItem)
                                @php $isActiveLoc = $activeLocationId === (int) $locationItem['id']; @endphp
                                <form action="{{ route('frontend.switch-location') }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <input type="hidden" name="location_id" value="{{ $locationItem['id'] }}">
                                    <button type="submit" class="location-pill {{ $isActiveLoc ? 'location-pill--active' : '' }}">
                                        {{ $locationItem['name'] }}{{ !empty($locationItem['city']) ? ' · '.$locationItem['city'] : '' }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @elseif(!empty($locationMenuData))
                        <div class="py-1 px-3 float-end border-end d-flex align-items-center">
                            <span class="location-pill location-pill--active">
                                {{ $locationMenuData[0]['name'] }}{{ !empty($locationMenuData[0]['city']) ? ' · '.$locationMenuData[0]['city'] : '' }}
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
                    <img src="{{ asset('assets/frontend/img/angel-motors-logo-top-dealer-logo.webp') }}"
                        width="325" height="65" alt="Angel Motors Inc">
                </a>
                <div class="ms-auto" id="header-nav">
                    <ul>
                        {{-- Search --}}
                        <li class="searchInventory">
                            <button id="search_desktop" data-bs-toggle="modal" data-bs-target="#modalSearch"
                                class="text-decoration-none text-dark bg-light btn btn-link">
                                <span class="d-inline-block me-2">
                                    <i class="fa-solid fa-magnifying-glass primaryy"></i>
                                </span>
                                Search Inventory
                            </button>
                        </li>

                        @foreach($mainMenu as $menu)
                            <li class="cursor-pointer">
                                @if($menu->children->count())
                                    {{ $menu->label }}
                                    <span class="d-inline-block ms-2">
                                        <i class="fa-solid fa-angle-down iconStyle"></i>
                                    </span>

                                    <ul>
                                        @foreach($menu->children as $child)
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

<!-- Header mobile -->
<div class="d-xl-none position-fixed w-100" id="mobile-nav" style="z-index:25;top:0;left:0;">
    {{-- Main mobile nav row --}}
    <div class="py-1 d-flex w-100 align-items-center justify-content-between" id="mobile-header" style="background:#212121;">
        <div class="text-left w-100 ps-2" id="mobile-logo">
            <a href="{{ route('frontend.home') }}">
                <img alt="Angel Motors Inc" fetchpriority="high" loading="eager" width="145" height="50"
                    decoding="async" src="{{ asset('assets/frontend/img/angel-motors-logo-top-dealer-logo.webp') }}">
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
    @if(count($locationMenuData ?? []) > 0)
        <div class="mobile-location-strip px-3 py-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-location-dot me-1" style="font-size:0.7rem;"></i>
            @if(count($locationMenuData) > 1)
                @foreach($locationMenuData as $locationItem)
                    @php $isActiveLoc = $activeLocationId === (int) $locationItem['id']; @endphp
                    <form action="{{ route('frontend.switch-location') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <input type="hidden" name="location_id" value="{{ $locationItem['id'] }}">
                        <button type="submit" class="location-pill location-pill--sm {{ $isActiveLoc ? 'location-pill--active' : '' }}">
                            {{ $locationItem['name'] }}{{ !empty($locationItem['city']) ? ' · '.$locationItem['city'] : '' }}
                        </button>
                    </form>
                @endforeach
            @else
                <span class="location-pill location-pill--sm location-pill--active">
                    {{ $locationMenuData[0]['name'] }}{{ !empty($locationMenuData[0]['city']) ? ' · '.$locationMenuData[0]['city'] : '' }}
                </span>
            @endif
        </div>
    @endif
</div>