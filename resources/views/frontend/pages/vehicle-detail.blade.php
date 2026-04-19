@extends('layouts.frontend.app')

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/vehicle-detail.css',
        'resources/js/frontend/pages/vehicle-detail.js',
        'resources/js/frontend/pages/managers-special.js',
        'resources/js/frontend/pages/ask-question.js',
        'resources/js/frontend/pages/schedule-test-drive.js',
        'resources/js/frontend/pages/trade-in.js',
        'resources/js/frontend/pages/get-approved-offcanvas.js',
        'resources/js/frontend/pages/unlock-eprice.js',
        'resources/js/frontend/pages/nps.js',
    ])
@endpush

@php
    $loc        = $locationMenuData[0] ?? null;
    $phones     = $loc['phones'] ?? [];

    $mainPhone = collect($phones)->firstWhere('type', 'sales')
        ?? collect($phones)->firstWhere('type', 'main')
        ?? collect($phones)->first();

    $mainPhoneNumber = $mainPhone ? $mainPhone['number'] : '(615) 267-0590';
    $mainPhoneRaw    = preg_replace('/\D/', '', $mainPhoneNumber);

    $street1    = $loc['street1']    ?? '1339 South Lowry Street';
    $city       = $loc['city']       ?? 'Smyrna';
    $state      = $loc['state']      ?? 'TN';
    $postalcode = $loc['postalcode'] ?? '37167';
    $mapUrl = !empty($loc['map_override'])
            ? $loc['map_override']
            : 'https://www.google.com/maps/search/' . urlencode(
                implode(', ', array_filter([
                    $loc['street1'] ?? '',
                    $loc['city']    ?? '',
                    $loc['state']   ?? '',
                ]))
              );
@endphp

@php
    $dealer      = $vehicle->dealer;
    $spec        = $vehicle->specs;
    $photosCount = $vehicle->photos_count ?? $allPhotos->count();
    $extraCount  = max(0, $photosCount - 5);

    // Main vehicle — loop se protect karo
    $mainVehicleId    = $vehicle->id;
    $mainVehicleTitle = $vehicleTitle;

    $horsepower = null;
    if ($spec?->max_horsepower) {
        $horsepower = $spec->max_horsepower . ' hp';
        if ($spec->max_horsepower_at) {
            $horsepower .= ' @ ' . number_format($spec->max_horsepower_at) . ' RPM';
        }
    }

    $torque = null;
    if ($spec?->max_torque) {
        $torque = $spec->max_torque . ' lb-ft';
        if ($spec->max_torque_at) {
            $torque .= ' @ ' . number_format($spec->max_torque_at) . ' RPM';
        }
    }

    $dimensions = null;
    if ($spec?->dimension_width && $spec?->dimension_length && $spec?->dimension_height) {
        $dimensions = "{$spec->dimension_width}\" w x {$spec->dimension_length}\" l x {$spec->dimension_height}\" h";
    }
@endphp

@section('page-content')

    {{-- ══ Breadcrumb ══ --}}
    <div class="my-3 d-none d-xl-block container">
        <ol class="breadcrumb my-3 d-none d-xl-flex"
            itemscope itemtype="https://schema.org/BreadcrumbList">

            <li class="breadcrumb-item notranslate"
                itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="{{ route('frontend.home') }}">
                    <span itemprop="name">Angel Motors Inc</span>
                </a>
                <meta itemprop="position" content="1">
            </li>

            <li class="breadcrumb-item"
                itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="{{ route('frontend.inventory') }}">
                    <span itemprop="name">Inventory</span>
                </a>
                <meta itemprop="position" content="2">
            </li>

            <li class="breadcrumb-item active"
                itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name">Used {{ $vehicleTitle }}</span>
                <meta itemprop="position" content="3">
            </li>
        </ol>
    </div>

    <div class="d-block d-xl-none h-63" id="mobile-nav-spacer"></div>

    <main class="mt-3 mb-3 mb-sm-5 vdp_pre-owned
        make_{{ Str::slug($vehicle->make?->name ?? '') }}
        {{ $vehicle->bodyType?->name ? 'body_' . Str::slug($vehicle->bodyType->name) : '' }}
        {{ ($vehicle->original_price && $vehicle->list_price < $vehicle->original_price) ? 'has-discount' : 'no-discount' }}
        container">

        <div class="row">

            {{-- ══════ LEFT COLUMN ══════ --}}
            <div class="col-xl-8">
                <div class="bg-white card">
                    <div class="py-0 px-0 py-sm-1 px-sm-1 card-body">
                        <div>
                            <div class="sc-5176998-0 jHXOJF {{ $photosCount > 0 ? 'hasPhotos' : '' }} noVideo">

                                {{-- Main Photo --}}
                                <div class="position-relative cursor-pointer"
                                     data-bs-toggle="modal"
                                     data-bs-target="#modalGallery"
                                     id="vdp_photo_main">
                                    @if($mainPhoto)
                                        <img id="vdpMainPhoto"
                                             alt="{{ $vehicleTitle }} for sale in Smyrna, TN"
                                             fetchpriority="high"
                                             loading="eager"
                                             width="926"
                                             height="694"
                                             decoding="async"
                                             class="main-photo"
                                             src="{{ $mainPhoto->url }}">
                                    @else
                                        <img id="vdpMainPhoto"
                                             alt="{{ $vehicleTitle }}"
                                             fetchpriority="high"
                                             loading="eager"
                                             width="926"
                                             height="694"
                                             class="main-photo"
                                             src="{{ asset('assets/frontend/img/no-photo.webp') }}">
                                    @endif
                                </div>

                                {{-- Thumbnails --}}
                                <div class="thumbnail-container position-relative">

                                    @forelse($thumbnails as $thumb)
                                        <div class="thumbnail-wrapper">
                                            <img src="{{ $thumb->url }}"
                                                 class="thumbnail vdp-thumb"
                                                 data-full="{{ $thumb->url }}"
                                                 alt="{{ $vehicleTitle }}"
                                                 loading="lazy">
                                        </div>
                                    @empty
                                        {{-- No thumbnails — keep grid layout intact --}}
                                    @endforelse

                                    @if($extraCount > 0)
                                        <div class="has-more-photos gallery-thumb-d"
                                             data-bs-toggle="modal"
                                             data-bs-target="#modalGallery"
                                             style="cursor:pointer;">
                                            <div class="more-photos-overlay cursor-pointer">
                                                +{{ $extraCount }} more
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Vehicle Title + Mileage ── --}}
                    <div class="border-top py-2 card-footer">
                        <div>
                            <div class="text-start">

                                <h1 data-cy="vehicle-title"
                                    class="h3 mt-0 mb-2 font-weight-bold notranslate">
                                    {{ $vehicleTitle }}
                                </h1>

                                <div class="vdp-miles">
                                    {{ $vehicle->mileage ? number_format($vehicle->mileage) . ' miles' : '—' }}
                                </div>

                                {{-- Hidden fields for form JS --}}
                                <div class="d-none" id="td_year">{{ $vehicle->year }}</div>
                                <div class="d-none" id="td_make">{{ strtoupper($vehicle->make?->name ?? '') }}</div>
                                <div class="d-none" id="td_model">{{ strtoupper($vehicle->makeModel?->name ?? '') }}</div>
                                <div class="d-none" id="td_vehicle_id">{{ $vehicle->id }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Mobile Price ── --}}
                    <div class="d-block bg-white d-xl-none pb-2 card-footer">
                        <div class="vdp-costpayment ePrice-locked">
                            <div class="clearfix"></div>
                            <div class="d-flex align-items-center row">
                                <div class="col-12"></div>
                                <div class="text-start col-7">
                                    <div class="text-large" id="vdp_top_price">

                                        <div class="text-medium mb-1">{{ $pricing['price_label'] }}</div>

                                        @if($pricing['is_formfill'])
                                            <strong class="label-price mb-2 mb-md-1 text-muted"
                                                    style="filter:blur(5px);user-select:none;">
                                                ${{ number_format($pricing['final_price']) }}
                                            </strong>
                                            <div class="mt-1">
                                                {{-- Formfill button --}}
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-sm w-100 btn-unlock-price"
                                                    data-bs-toggle="offcanvas"
                                                    data-bs-target="#unlockEPrice"
                                                    data-vehicle-id="{{ $mainVehicleId }}"
                                                    data-vehicle-title="{{ $mainVehicleTitle }}"
                                                    style="font-size:12px;">
                                                <i class="fa-solid fa-lock me-1"></i>
                                                {{ $pricing['button_text'] ?: 'Get e-Price' }}
                                            </button>
                                            </div>
                                        @elseif($pricing['is_special_finance'])
                                            <strong class="label-price mb-2 mb-md-1">
                                                ${{ number_format($pricing['final_price']) }}
                                            </strong>
                                            @if($pricing['applied_special']?->finance_rate)
                                                <div class="mt-1">
                                                    <span class="badge bg-success text-white" style="font-size:11px;">
                                                        {{ $pricing['applied_special']->finance_rate }}% APR
                                                        @if($pricing['applied_special']->finance_term)
                                                            / {{ $pricing['applied_special']->finance_term }} mo.
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        @else
                                            <strong class="label-price mb-2 mb-md-1">
                                                ${{ number_format($pricing['final_price']) }}
                                            </strong>
                                        @endif

                                    </div>
                                </div>
                                <div class="text-end d-block d-xl-none col-5"><br></div>
                                <div class="col-12">
                                    <span class="rounded bg-light p-2 d-block text-end border mt-1">
                                        <span class="badge badge-secondary text-uppercase me-2 float-start">
                                            Estimated Payment
                                        </span>
                                        ${{ number_format($pricing['monthly']) }} / mo.
                                        <span class="ms-2 text-primary"
                                              data-bs-toggle="offcanvas"
                                              data-bs-target="#offcanvasRight"
                                              aria-controls="offcanvasRight">
                                            <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>

                            {{-- ── On sale now! section ── --}}
                            @if($pricing['has_discount'] && ! $pricing['is_formfill'])
                                @php
                                    $isOffsetType    = $pricing['applied_special'] &&
                                                       in_array($pricing['applied_special']->discount_type, ['offsetdollar', 'offsetincrease']);
                                    $originalDisplay = $isOffsetType && $pricing['msrp'] > 0
                                                       ? $pricing['msrp']
                                                       : $vehicle->list_price;
                                    $discountAmt     = $pricing['savings'];
                                    $isIncrease      = $pricing['applied_special'] &&
                                                       in_array($pricing['applied_special']->discount_type, ['increase', 'offsetincrease']);
                                    $sign            = $isIncrease ? '+' : '-';
                                @endphp
                                <div id="vdp-onsale-section-mobile" class="border rounded py-2 px-2 mt-3 mb-2">
                                    <div id="vdp-onsale-toggle-mobile" class="cursor-pointer d-flex">
                                        <div class="text-truncate pe-2 w-100 notranslate d-flex align-items-center">
                                            <span class="pt-1" style="font-size:13px;font-weight:600;color:#333;">On sale now!</span>
                                        </div>
                                        <span id="vdp-onsale-icon-mobile" class="d-inline-block text-primary mt-1 float-end">
                                            <i class="fa-solid fa-square-minus fa-sm"></i>
                                        </span>
                                    </div>
                                    <div id="vdp-onsale-body-mobile" class="border-top mt-2 pt-2" style="font-size:13px;">
                                        @php
                                            $isOffsetType = $pricing['applied_special'] &&
                                                            in_array($pricing['applied_special']->discount_type, ['offsetdollar', 'offsetincrease']);
                                            $originalDisplay = $isOffsetType && $pricing['msrp'] > 0
                                                               ? $pricing['msrp']
                                                               : $vehicle->list_price;
                                        @endphp
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Original price</span>
                                            <span>${{ number_format($originalDisplay) }}</span>
                                        </div>
                                        @if($pricing['applied_special'])
                                            <div class="d-flex justify-content-between py-1 border-bottom">
                                                <span class="text-muted">{{ $pricing['applied_special']->title }}</span>
                                                <span class="{{ $isIncrease ? 'text-danger' : 'text-success' }}">
                                                    {{ $sign }}${{ number_format(abs($discountAmt)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-between py-1 border-bottom fw-semibold">
                                            <span>Cash price</span>
                                            <span>${{ number_format($pricing['final_price']) }}</span>
                                        </div>
                                        {{-- ── Applicable Fees ── --}}
                                        @foreach($applicableFees as $fee)
                                            @php
                                                $feeAmount = $fee->type === 'amount'
                                                    ? '$' . number_format($fee->value, 2)
                                                    : number_format($fee->value, 2) . '%';
                                            @endphp
                                            <div class="d-flex justify-content-between py-1 {{ ! $loop->last ? 'border-bottom' : '' }}">
                                                <span class="text-muted d-flex align-items-center gap-1">
                                                    <i class="fa-solid fa-circle-info" style="font-size:11px;"></i>
                                                    {{ $fee->name }}
                                                    @if($fee->is_optional)
                                                        <small class="text-muted">(optional)</small>
                                                    @endif
                                                </span>
                                                <span class="text-muted">{{ $feeAmount }}</span>
                                            </div>
                                        @endforeach

                                        @if($applicableFees->isEmpty())
                                            <div class="d-flex justify-content-between py-1">
                                                <span class="text-muted d-flex align-items-center gap-1">
                                                    <i class="fa-solid fa-circle-info" style="font-size:11px;"></i>
                                                    Documentation Fee
                                                </span>
                                                <span class="text-muted">—</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Mobile Location Box --}}
                            <div id="vdp_location_box_mobile" class="border rounded py-2 px-2 mt-3 mb-2">
                                <div id="toggleLocationMobile" class="cursor-pointer d-flex">
                                    <div class="text-truncate pe-2 w-100 notranslate d-flex align-items-center">
                                        <span class="d-inline-block text-primary me-2">
                                            <i class="fa-solid fa-location-dot fa-sm"></i>
                                        </span>
                                        <span class="pt-1">{{ $dealer?->name ?? config('app.name') }}</span>
                                    </div>
                                    <span id="toggleIconMobile" class="d-inline-block text-primary mt-1 float-end">
                                        <i class="fa-solid fa-square-plus fa-sm"></i>
                                    </span>
                                </div>
                                <div id="locationboxMobile" class="border-top mt-2 pt-2">
                                    {{ $street1 }}<br>
                                    {{ $city }}, {{ $state }} {{ $postalcode }}
                                    <div class="g-2 mt-2 pb-1 row">
                                        <div class="col-6">
                                            <a href="{{ $mapUrl }}"
                                               target="_blank"
                                               class="btn btn-primary bg-white w-100 text-start d-flex align-items-center">
                                                <i class="fa-solid fa-location-arrow me-2 text-white"></i>
                                                Directions
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="tel:{{ $mainPhoneRaw }}"
                                               class="btn btn-primary bg-white w-100 text-start d-flex align-items-center">
                                                <i class="fa-solid fa-phone me-2 text-white"></i>
                                                Call
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Mobile CTAs ── --}}
                    <div class="d-block d-xl-none bg-white text-center p-0 card-footer">
                        <div class="pt-0 pt-sm-1 pb-3 pb-sm-4 px-3 px-sm-4 bg-lighter card-footer" id="vdpctas">

                            <button data-bs-toggle="offcanvas" data-bs-target="#unlockManager"
                                    type="button"
                                    class="text-nowrap text-start w-100 d-flex align-items-center mt-3 btn btn-primary">
                                <span class="me-2 text-white">
                                    <i class="fa-solid fa-unlock-keyhole fa-lg"></i>
                                </span>
                                Unlock Manager's Special
                            </button>

                            <button type="button" data-bs-toggle="offcanvas" data-bs-target="#getApproved"
                                    class="text-nowrap text-start w-100 d-flex align-items-center mt-3 bg-white btn btn-default">
                                <span class="d-inline-block me-2 fa-fw text-primary">
                                    <i class="fa-solid fa-percent fa-lg"></i>
                                </span>
                                Get approved
                            </button>

                            <button type="button" data-bs-toggle="offcanvas" data-bs-target="#scheduleTest"
                                    class="text-nowrap text-start w-100 d-flex align-items-center mt-3 bg-white btn btn-default">
                                <span class="me-2 text-primary">
                                    <i class="fa-solid fa-car fa-lg"></i>
                                </span>
                                Schedule a test drive
                            </button>
                        </div>

                        <div class="border-top py-0 card-body" id="cta-buyersactions">
                            <div class="bg-white row-bordered mx-n4 text-center row">
                                <div class="py-3 cursor-pointer col-4" data-bs-toggle="modal" data-bs-target="#modalShare">
                                    <span class="text-muted d-block">
                                        <i class="fa-solid fa-share-nodes fa-lg"></i>
                                    </span>
                                    Share
                                </div>
                                <div class="py-3 cursor-pointer col-4"
                                     data-bs-toggle="offcanvas" data-bs-target="#askQuestion">
                                    <span class="text-muted d-block">
                                        <i class="fa-regular fa-message fa-lg"></i>
                                    </span>
                                    Questions?
                                </div>
                                <div class="py-3 cursor-pointer col-4" data-cy="btn-favorite">
                                    <span class="text-muted d-block">
                                        <i class="fa-solid fa-heart fa-lg"></i>
                                    </span>
                                    Save
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Details Section Header ── --}}
                    <div class="px-4 py-3 bg-lighter font-weight-bold card-footer">
                        <div class="d-flex align-items-center row">
                            <div class="text-center text-md-start col-md-8 col-12">
                                <h2 class="h5 my-0 font-weight-bold">
                                    {{ $vehicleTitle }} Details
                                </h2>
                            </div>
                        </div>
                    </div>

                    {{-- ── Details Tables ── --}}
                    <div class="px-4 py-3 bg-white card-footer">
                        <div class="row">

                            {{-- ── Left column ── --}}
                            <div class="col-sm-6">
                                <table class="table card-table table-borderless inventory-table vehicledetails">
                                    <tbody>

                                        <tr class="tr_condition">
                                            <td width="50%"><b>Condition</b></td>
                                            <td class="pe-0">Pre-owned</td>
                                        </tr>

                                        @if($vehicle->bodyType?->name)
                                            <tr class="notranslate tr_body">
                                                <td width="50%"><b>Body Type</b></td>
                                                <td class="pe-0">{{ $vehicle->bodyType->name }}</td>
                                            </tr>
                                        @endif

                                        @if($vehicle->trim)
                                            <tr class="tr_trim">
                                                <td width="50%"><b>Trim</b></td>
                                                <td class="pe-0">{{ $vehicle->trim }}</td>
                                            </tr>
                                        @endif

                                        <tr class="tr_stocknumber">
                                            <td width="50%"><b>Stock #</b></td>
                                            <td class="pe-0">{{ $vehicle->stock_number }}</td>
                                        </tr>

                                        <tr class="notranslate tr_vin">
                                            <td width="50%"><b>VIN</b></td>
                                            <td class="pe-0">
                                                <span data-vin="{{ $vehicle->vin }}">{{ $vehicle->vin }}</span>
                                            </td>
                                        </tr>

                                        @if($vehicle->exteriorColor?->name)
                                            <tr class="tr_exteriorcolor">
                                                <td width="50%"><b>Exterior Color</b></td>
                                                <td class="pe-0">{{ $vehicle->exteriorColor->name }}</td>
                                            </tr>
                                        @endif

                                        @if($vehicle->seating_capacity)
                                            <tr class="tr_seatingcapacity">
                                                <td width="50%"><b>Passengers</b></td>
                                                <td class="pe-0">{{ $vehicle->seating_capacity }}</td>
                                            </tr>
                                        @endif

                                        @if($vehicle->drivetrainType?->name)
                                            <tr class="tr_drivetrain">
                                                <td width="50%"><b>Drivetrain</b></td>
                                                <td class="pe-0">{{ $vehicle->drivetrainType->name }}</td>
                                            </tr>
                                        @endif

                                        @if($horsepower)
                                            <tr class="tr_maxhorsepower">
                                                <td width="50%"><b>Horsepower</b></td>
                                                <td class="pe-0">{{ $horsepower }}</td>
                                            </tr>
                                        @endif

                                        @if($torque)
                                            <tr class="tr_maxtorque">
                                                <td width="50%"><b>Torque</b></td>
                                                <td class="pe-0">{{ $torque }}</td>
                                            </tr>
                                        @endif

                                        @if($vehicle->fuelType?->name)
                                            <tr class="tr_fuel">
                                                <td width="50%"><b>Fuel Type</b></td>
                                                <td class="pe-0">{{ $vehicle->fuelType->name }}</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>

                            {{-- ── Right column ── --}}
                            <div class="col-sm-6">
                                <table class="table card-table table-borderless inventory-table vehicledetails">
                                    <tbody>

                                        @if($spec?->fuel_tank)
                                            <tr class="tr_fueltank">
                                                <td width="50%"><b>Fuel Capacity</b></td>
                                                <td class="pe-0">{{ $spec->fuel_tank }} gallons</td>
                                            </tr>
                                        @endif

                                        @if($spec?->mpg_city && $spec?->mpg_highway)
                                            <tr class="tr_mpgcity">
                                                <td width="50%"><b>Fuel Economy</b></td>
                                                <td class="pe-0">{{ $spec->mpg_city }} City / {{ $spec->mpg_highway }} Hwy</td>
                                            </tr>
                                        @endif

                                        @if($vehicle->transmissionType?->name)
                                            <tr class="tr_transmission">
                                                <td width="50%"><b>Transmission</b></td>
                                                <td class="pe-0">{{ $vehicle->transmissionType->name }}</td>
                                            </tr>
                                        @endif

                                        @if($vehicle->engine)
                                            <tr class="tr_engine">
                                                <td width="50%"><b>Engine</b></td>
                                                <td class="pe-0">{{ $vehicle->engine }}</td>
                                            </tr>
                                        @endif

                                        @if($spec?->gvwr)
                                            <tr class="tr_gvwr">
                                                <td width="50%"><b>Gross Vehicle Weight Rating</b></td>
                                                <td class="pe-0">{{ number_format($spec->gvwr) }} lbs.</td>
                                            </tr>
                                        @endif

                                        @if($dimensions)
                                            <tr class="tr_dimension_length">
                                                <td width="50%"><b>Dimensions</b></td>
                                                <td class="pe-0">{{ $dimensions }}</td>
                                            </tr>
                                        @endif

                                        @if($spec?->wheelbase)
                                            <tr class="tr_wheelbase">
                                                <td width="50%"><b>Wheelbase</b></td>
                                                <td class="pe-0">{{ $spec->wheelbase }}"</td>
                                            </tr>
                                        @endif

                                        @if($spec?->front_wheel)
                                            <tr class="tr_frontwheel">
                                                <td width="50%"><b>Front Wheel</b></td>
                                                <td class="pe-0">{{ $spec->front_wheel }}</td>
                                            </tr>
                                        @endif

                                        @if($spec?->rear_wheel)
                                            <tr class="tr_rearwheel">
                                                <td width="50%"><b>Rear Wheel</b></td>
                                                <td class="pe-0">{{ $spec->rear_wheel }}</td>
                                            </tr>
                                        @endif

                                        @if($spec?->front_tire)
                                            <tr class="tr_fronttire">
                                                <td width="50%"><b>Front Tire</b></td>
                                                <td class="pe-0">{{ $spec->front_tire }}</td>
                                            </tr>
                                        @endif

                                        @if($spec?->rear_tire)
                                            <tr class="tr_reartire">
                                                <td width="50%"><b>Rear Tire</b></td>
                                                <td class="pe-0">{{ $spec->rear_tire }}</td>
                                            </tr>
                                        @endif

                                        {{-- Window Sticker — static --}}
                                        <tr>
                                            <td colspan="2" class="p-0">
                                                @if($windowSticker ?? null)
                                                    <a href="{{ route('frontend.inventory.vehicle.printable', [$vehicle, $windowSticker]) }}"
                                                       target="_blank"
                                                       title="View Window Sticker"
                                                       class="bg-white d-flex align-items-center p-2 w-100 border rounded mt-1">
                                                        <span class="px-2 py-1 bg-primary me-2 rounded text-white">
                                                            <svg width="16" height="16" viewBox="0 0 448 512" fill="currentColor">
                                                                <path d="M64 64h320v48H64zM64 160h320v48H64zM64 256h320v48H64z"/>
                                                            </svg>
                                                        </span>
                                                        View Window Sticker
                                                        <span class="ms-auto text-primary">
                                                            <svg width="16" height="16" viewBox="0 0 512 512" fill="currentColor">
                                                                <path d="M320 0h192v192h-64V109.3L256 301.3l-45.3-45.3L402.7 64H320V0zM64 64h192v64H128v256h256V256h64v192c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128c0-35.3 28.7-64 64-64z"/>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    {{-- ── Carfax — static ── --}}
                    <div class="py-1 px-4 container-carfax card-footer">
                        <a href="#" target="_blank"
                           class="d-flex align-items-center w-100 text-decoration-none">
                            <div class="carfax-badge py-1 px-2 rounded">
                                <img src="{{ asset('assets/frontend/img/fair.svg') }}"
                                     alt="Free Carfax Report"
                                     width="105" height="70"
                                     loading="lazy">
                            </div>
                            <div class="ms-auto text-md-end">
                                <del class="text-muted">$44.99</del><br>
                                <span class="text-primary d-inline-flex align-items-center gap-1">
                                    <svg width="16" height="16" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M320 0h192v192h-64V109.3L256 301.3l-45.3-45.3L402.7 64H320V0zM64 64h192v64H128v256h256V256h64v192c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128c0-35.3 28.7-64 64-64z"/>
                                    </svg>
                                    View CARFAX Report
                                </span>
                            </div>
                        </a>
                    </div>

                    {{-- ── Key Features ── --}}
                    @if($vehicle->features->isNotEmpty())
                        <div class="px-4 py-3 bg-lighter font-weight-bold card-footer">
                            <h2 class="text-small my-0 font-weight-bold">
                                {{ $vehicleTitle }} Key Features
                            </h2>
                        </div>

                        <div class="text-start p-0 bg-lighter card-footer">
                            <div class="row no-gutters px-3 pt-3 pt-sm-0 pb-0 pb-sm-3">
                                @foreach($vehicle->features->take(9) as $vf)
                                    <div class="col-sm-4 col-12 pt-0 px-0 px-sm-2 pt-sm-3">
                                        <div class="p-3 mb-2 mb-sm-0 bg-white border rounded text-nowrap">
                                            <div class="text-truncate d-flex align-items-center">
                                                {{ $vf->name }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── Factory Option Sections (dynamic, grouped by category) ── --}}
                    @foreach($groupedOptions as $categoryName => $options)
                        <div class="px-4 py-3 card-footer">
                            <div class="collapse-header cursor-pointer d-flex align-items-center justify-content-between">
                                <h3 class="h5 my-0 fw-bold mb-0">{{ $categoryName }}</h3>
                                <span class="text-primary d-inline-block collapse-icon">
                                    <svg width="16" height="16" viewBox="0 0 448 512" fill="currentColor">
                                        <path d="M207.029 381.476l-184-184c-9.373-9.373-9.373-24.569 0-33.941l22.627-22.627c9.373-9.373 24.569-9.373 33.941 0L224 284.118l144.402-144.21c9.373-9.373 24.569-9.373 33.941 0l22.627 22.627c9.373 9.373 9.373 24.569 0 33.941l-184 184c-9.373 9.372-24.568 9.372-33.941-.001z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="p-4 collapse-content bg-lighter border-top">
                            <div class="border-top border-start">
                                <div class="no-gutters row">
                                    @foreach($options as $fo)
                                        <div class="px-3 py-2 bg-white border-bottom border-end col-sm-6 col-12">
                                            {{ $fo->label }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- ── FAQs ── --}}
                    @if(count($faqs) > 0)
                        <div class="px-4 py-3 card-footer">
                            <div class="collapse-header cursor-pointer d-flex align-items-center justify-content-between">
                                <h3 class="h5 my-0 fw-bold mb-0">
                                    Frequently asked questions about {{ $vehicleTitle }}
                                </h3>
                                <span class="text-primary d-inline-block collapse-icon">
                                    <svg width="16" height="16" viewBox="0 0 448 512" fill="currentColor">
                                        <path d="M207.029 381.476l-184-184c-9.373-9.373-9.373-24.569 0-33.941l22.627-22.627c9.373-9.373 24.569-9.373 33.941 0L224 284.118l144.402-144.21c9.373-9.373 24.569-9.373 33.941 0l22.627 22.627c9.373 9.373 9.373 24.569 0 33.941l-184 184c-9.373 9.372-24.568 9.372-33.941-.001z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="p-4 collapse-content bg-lighter border-rounded">
                            <div class="bg-white border overflow-hidden rounded">
                                @foreach($faqs as $faq)
                                    <div class="accordion-item bg-white border border-bottom px-3 py-2">
                                        <div class="d-flex align-items-center justify-content-between cursor-pointer accordion-header">
                                            <h3 class="h5 my-0 fw-bold mb-0">{{ $faq['question'] }}</h3>
                                            <span class="text-primary d-inline-block collapse-icon">
                                                <svg width="16" height="16" viewBox="0 0 448 512" fill="currentColor">
                                                    <path d="M207.029 381.476l-184-184c-9.373-9.373-9.373-24.569 0-33.941l22.627-22.627c9.373-9.373 24.569-9.373 33.941 0L224 284.118l144.402-144.21c9.373-9.373 24.569-9.373 33.941 0l22.627 22.627c9.373 9.373 9.373 24.569 0 33.941l-184 184c-9.373 9.372-24.568 9.372-33.941-.001z"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="accordion-content mt-2">
                                            <div class="p-2 bg-light">{{ $faq['answer'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>{{-- end .card --}}
            </div>{{-- end col-xl-8 --}}

            {{-- ══════ RIGHT SIDEBAR (desktop) ══════ --}}
            <div class="col-xl-4">
                <div class="d-none d-xl-block" id="vdp-sidebar">
                    <div class="mb-4 card">
                        <div class="card-body">
                            <div class="vdp-costpayment ePrice-locked">
                                <div class="clearfix"></div>
                                <div class="d-flex align-items-center row">
                                    <div class="col-12"></div>
                                    <div class="text-start col-7">
                                        <div class="text-large" id="vdp_top_price">

                                            <div class="text-medium mb-1">{{ $pricing['price_label'] }}</div>

                                            @if($pricing['is_formfill'])
                                                <strong class="label-price mb-2 mb-md-1 text-muted"
                                                        style="filter:blur(5px);user-select:none;">
                                                    ${{ number_format($pricing['final_price']) }}
                                                </strong>
                                                <div class="mt-1">
                                                    {{-- Formfill button --}}
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-sm w-100 btn-unlock-price"
                                                            data-bs-toggle="offcanvas"
                                                            data-bs-target="#unlockEPrice"
                                                            data-vehicle-id="{{ $mainVehicleId }}"
                                                            data-vehicle-title="{{ $mainVehicleTitle }}"
                                                            style="font-size:12px;">
                                                        <i class="fa-solid fa-lock me-1"></i>
                                                        {{ $pricing['button_text'] ?: 'Get e-Price' }}
                                                    </button>
                                                </div>
                                            @elseif($pricing['is_special_finance'])
                                                <strong class="label-price mb-2 mb-md-1">
                                                    ${{ number_format($pricing['final_price']) }}
                                                </strong>
                                                @if($pricing['applied_special']?->finance_rate)
                                                    <div class="mt-1">
                                                        <span class="badge bg-success text-white" style="font-size:11px;">
                                                            {{ $pricing['applied_special']->finance_rate }}% APR
                                                            @if($pricing['applied_special']->finance_term)
                                                                / {{ $pricing['applied_special']->finance_term }} mo.
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            @else
                                                <strong class="label-price mb-2 mb-md-1">
                                                    ${{ number_format($pricing['final_price']) }}
                                                </strong>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="text-end d-block d-xl-none col-5"><br></div>
                                    <div class="col-12">
                                        <div class="bg-lighter border rounded py-2 px-3 mt-2 align-items-center text-center cursor-pointer">
                                            <span class="badge badge-secondary text-uppercase mb-2">Estimated Payment</span>
                                            <div class="text-large">
                                                ${{ number_format($pricing['monthly']) }} / month
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── On sale now! section ── --}}
                                @if($pricing['has_discount'] && ! $pricing['is_formfill'])
                                    @php
                                        $isOffsetType    = $pricing['applied_special'] &&
                                                           in_array($pricing['applied_special']->discount_type, ['offsetdollar', 'offsetincrease']);
                                        $originalDisplay = $isOffsetType && $pricing['msrp'] > 0
                                                           ? $pricing['msrp']
                                                           : $vehicle->list_price;
                                        $discountAmt     = $pricing['savings'];
                                        $isIncrease      = $pricing['applied_special'] &&
                                                           in_array($pricing['applied_special']->discount_type, ['increase', 'offsetincrease']);
                                        $sign            = $isIncrease ? '+' : '-';
                                    @endphp
                                    <div id="vdp-onsale-section" class="border rounded py-2 px-2 mt-3 mb-2">
                                        <div id="vdp-onsale-toggle" class="cursor-pointer d-flex">
                                            <div class="text-truncate pe-2 w-100 notranslate d-flex align-items-center">
                                                <span class="pt-1" style="font-size:13px;font-weight:600;color:#333;">On sale now!</span>
                                            </div>
                                            <span id="vdp-onsale-icon" class="d-inline-block text-primary mt-1 float-end">
                                                <i class="fa-solid fa-square-minus fa-sm"></i>
                                            </span>
                                        </div>
                                        <div id="vdp-onsale-body" class="border-top mt-2 pt-2" style="display:block; font-size:13px;">
                                            @php
                                                $isOffsetType = $pricing['applied_special'] &&
                                                                in_array($pricing['applied_special']->discount_type, ['offsetdollar', 'offsetincrease']);
                                                $originalDisplay = $isOffsetType && $pricing['msrp'] > 0
                                                                   ? $pricing['msrp']
                                                                   : $vehicle->list_price;
                                            @endphp
                                            <div class="d-flex justify-content-between py-1 border-bottom">
                                                <span class="text-muted">Original price</span>
                                                <span>${{ number_format($originalDisplay) }}</span>
                                            </div>
                                            @if($pricing['applied_special'])
                                                <div class="d-flex justify-content-between py-1 border-bottom">
                                                    <span class="text-muted">{{ $pricing['applied_special']->title }}</span>
                                                    <span class="{{ $isIncrease ? 'text-danger' : 'text-success' }}">
                                                        {{ $sign }}${{ number_format(abs($discountAmt)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between py-1 border-bottom fw-semibold">
                                                <span>Cash price</span>
                                                <span>${{ number_format($pricing['final_price']) }}</span>
                                            </div>
                                            {{-- ── Applicable Fees ── --}}
                                            @foreach($applicableFees as $fee)
                                                @php
                                                    $feeAmount = $fee->type === 'amount'
                                                        ? '$' . number_format($fee->value, 2)
                                                        : number_format($fee->value, 2) . '%';
                                                @endphp
                                                <div class="d-flex justify-content-between py-1 {{ ! $loop->last ? 'border-bottom' : '' }}">
                                                    <span class="text-muted d-flex align-items-center gap-1">
                                                        <i class="fa-solid fa-circle-info" style="font-size:11px;"></i>
                                                        {{ $fee->name }}
                                                        @if($fee->is_optional)
                                                            <small class="text-muted">(optional)</small>
                                                        @endif
                                                    </span>
                                                    <span class="text-muted">{{ $feeAmount }}</span>
                                                </div>
                                            @endforeach

                                            @if($applicableFees->isEmpty())
                                                <div class="d-flex justify-content-between py-1">
                                                    <span class="text-muted d-flex align-items-center gap-1">
                                                        <i class="fa-solid fa-circle-info" style="font-size:11px;"></i>
                                                        Documentation Fee
                                                    </span>
                                                    <span class="text-muted">—</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Desktop Location Box --}}
                                <div id="vdp_location_box" class="border rounded py-2 px-2 mt-3 mb-2">
                                    <div id="toggleLocation" class="cursor-pointer d-flex">
                                        <div class="text-truncate pe-2 w-100 notranslate d-flex align-items-center">
                                            <span class="d-inline-block text-primary me-2">
                                                <i class="fa-solid fa-location-dot fa-sm"></i>
                                            </span>
                                            <span class="pt-1">{{ $dealer?->name ?? config('app.name') }}</span>
                                        </div>
                                        <span id="toggleIcon" class="d-inline-block text-primary mt-1 float-end">
                                            <i class="fa-solid fa-square-minus fa-sm"></i>
                                        </span>
                                    </div>
                                    <div id="locationbox" class="border-top mt-2 pt-2">
                                        {{ $street1 }}<br>
                                        {{ $city }}, {{ $state }} {{ $postalcode }}
                                        <div class="g-2 mt-2 pb-1 row">
                                            <div class="col-6">
                                                <a href="{{ $mapUrl }}"
                                               target="_blank"
                                               class="btn btn-primary bg-white w-100 text-start d-flex align-items-center">
                                                <i class="fa-solid fa-location-arrow me-2 text-white"></i>
                                                Directions
                                            </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="tel:{{ $mainPhoneRaw }}"
                                                   class="btn btn-primary bg-white w-100 text-start d-flex align-items-center">
                                                    <i class="fa-solid fa-phone me-2 text-white"></i>
                                                    Call
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-0 pt-sm-1 pb-3 pb-sm-4 px-3 px-sm-4 bg-lighter card-footer" id="vdpctas">
                            <button type="button" data-bs-toggle="offcanvas" data-bs-target="#unlockManager"
                                    class="text-nowrap text-start w-100 d-flex align-items-center mt-3 btn btn-primary">
                                <span class="d-inline-block me-2 fa-fw text-white">
                                    <i class="fa-solid fa-unlock-keyhole fa-lg"></i>
                                </span>
                                Unlock Manager's Special
                            </button>

                            <button type="button" data-bs-toggle="offcanvas" data-bs-target="#getApproved"
                                    class="text-nowrap text-start w-100 d-flex align-items-center mt-3 bg-white btn btn-default">
                                <span class="d-inline-block me-2 fa-fw text-primary">
                                    <i class="fa-solid fa-percent fa-lg"></i>
                                </span>
                                Get approved
                            </button>

                            <button type="button" data-bs-toggle="offcanvas" data-bs-target="#scheduleTest"
                                    class="text-nowrap text-start w-100 d-flex align-items-center mt-3 bg-white btn btn-default">
                                <span class="d-inline-block me-2 fa-fw text-primary">
                                    <i class="fa-solid fa-car fa-lg"></i>
                                </span>
                                Schedule a test drive
                            </button>
                        </div>

                        <div class="border-top py-0 card-body" id="cta-buyersactions">
                            <div class="bg-white row-bordered mx-n4 text-center row">
                                <div class="py-3 cursor-pointer col-4"
                                     data-bs-toggle="modal" data-bs-target="#modalShare">
                                    <span class="d-inline-block text-muted h4 w-100 d-block text-center">
                                        <i class="fa-solid fa-arrow-up-from-bracket fa-lg"></i>
                                    </span>
                                    Share
                                </div>
                                <div data-bs-toggle="offcanvas" data-bs-target="#askQuestion"
                                     class="py-3 cursor-pointer col-4">
                                    <span class="d-inline-block text-muted h4 w-100 d-block text-center">
                                        <i class="fa-regular fa-message fa-lg"></i>
                                    </span>
                                    Questions?
                                </div>
                                <div data-cy="btn-favorite" class="py-3 cursor-pointer col-4">
                                    <span class="d-inline-block text-muted h4 w-100 d-block text-center">
                                        <i class="fa-solid fa-heart fa-lg"></i>
                                    </span>
                                    Save
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Trade-in CTA --}}
                    <div id="vdp-trade-cta"
                         data-bs-toggle="offcanvas"
                         data-bs-target="#getTrade"
                         class="text-center cursor-pointer bg-primary rounded text-white p-4 my-4 d-flex align-items-center">
                        <img alt="Get your trade-in value" loading="lazy"
                             width="50" height="50" decoding="async"
                             src="https://static.overfuel.com/images/icons/streamlinehq-car-tool-keys-transportation-white-200.PNG?w=128&q=80">
                        <div class="px-3 mb-0 ms-1 text-start">
                            <div class="h4 mb-0">What's your car worth?</div>
                            Get your trade-in value
                        </div>
                        <span class="d-inline-block ms-auto text-white">
                            <i class="fa-solid fa-angle-right fa-lg"></i>
                        </span>
                    </div>
                </div>
            </div>{{-- end col-xl-4 --}}

        </div>{{-- end .row --}}
    </main>

    {{-- ══ Related Vehicles ══ --}}
    @if($related->isNotEmpty())
        <div class="container" id="relatedvehicles">
            <h4 class="text-center mb-4">We think you might like these...</h4>
            <div class="mt-3 d-flex align-items-center">

                <div id="arrow-left" class="pe-4 d-none d-md-block">
                    <span class="d-inline-block h1 m-0 text-muted cursor-pointer">
                        <i class="fa-solid fa-circle-chevron-left text-50"></i>
                    </span>
                </div>

                <div class="w-100 overflow-hidden">
                    <div class="cards-wrapper">
                        @foreach($related as $vehicle)
                            @include('frontend.partials.vehicle-card')
                        @endforeach
                    </div>
                </div>

                <div id="arrow-right" class="ps-4 d-none d-md-block">
                    <span class="d-inline-block h1 m-0 text-muted cursor-pointer">
                        <i class="fa-solid fa-circle-chevron-right text-50"></i>
                    </span>
                </div>

            </div>
        </div>
    @endif

    {{-- ══ Disclaimers ══ --}}
    <div class="py-3 text-small opacity-75 disclaimers container">
        @php
            $cleanDisclaimer = trim(strip_tags($inventoryDisclaimer));
        @endphp

        @if (!empty($cleanDisclaimer))
            <p>{!! $inventoryDisclaimer !!}</p>
        @else
            <p>
                It is the customer's sole responsibility to verify the existence and condition of any equipment listed. Neither the dealership nor eBizAutos is responsible for misprints on prices or equipment. It is the customer's sole responsibility to verify the accuracy of the prices with the dealer, including the pricing for all added accessories. Financing Fees apply for all vehicles not purchased with cash. See Dealer for details.
            </p>
        @endif
    </div>
@endsection

@push('page-modals')
    @include('frontend.offcanvas.personalize-payment')
    @include('frontend.offcanvas.unlock-manager-special')
    @include('frontend.offcanvas.schedule-test')
    @include('frontend.offcanvas.ask-question')
    @include('frontend.offcanvas.get-trade-in')
    @include('frontend.offcanvas.get-approved')
    @include('frontend.offcanvas.unlock-eprice')
    @include('frontend.modals.photo-gallery', ['photos' => $allPhotos])
    @include('frontend.modals.share')
@endpush

@push('page-scripts')
    <script>
        window.tiRoutes = {
            tradeIn:       '{{ route('frontend.forms.trade-in') }}',
            tradeInPhotos: '{{ route('frontend.forms.trade-in.photos') }}',
            makeModels:    '{{ route('frontend.data.make-models', ['make' => '__make__']) }}',
        };

        window.gaRoutes = {
            submit: '{{ route('frontend.forms.get-approved') }}',
        };

        window.npsRouteTemplate = '{{ route('frontend.forms.nps', ['formEntry' => '__id__']) }}';
    </script>
@endpush