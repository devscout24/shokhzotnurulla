@extends('layouts.frontend.app')

@section('title', __('Home') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/home.css',
        'resources/js/frontend/pages/trade-in.js',
        'resources/js/frontend/pages/get-approved-offcanvas.js',
        'resources/js/frontend/pages/unlock-eprice.js',
        'resources/js/frontend/pages/nps.js',
    ])
@endpush

@section('page-content')
    <div class="d-block d-xl-none h-63"></div>

    <!-- hero section  -->
    <div class="position-relative">
        <div class="hero-video-container" id="hero-video">
            <div class="hero-video-overlay"></div>

            <div class="hero-video">
                @if($videoUrl ?? null)
                    <video src="{{ asset($videoUrl) }}" muted playsinline autoplay loop></video>
                @else
                    <video src="{{ asset('assets/frontend/img/angel-motors-hero-video.mp4') }}" muted playsinline autoplay loop></video>
                @endif
            </div>

            <div class="hero-caption text-center text-white">
                <div class="py-4">
                    @if($bannerTitle ?? null)
                        <h1 class="font-weight-bold">{{ $bannerTitle }}</h1>
                    @endif

                    @if($bannerSubtitle ?? null)
                        <h2 class="font-weight-bold">{{ $bannerSubtitle }}</h2>
                    @endif

                    <div class="bg-light p-md-3 p-2 rounded border mt-md-0 mt-3 d-none d-md-block">
                        <div class="position-relative">
                            <span class="d-inline-block position-absolute search-icon iconPostion">
                                <i class="fa-solid fa-magnifying-glass greyIcon"></i>
                            </span>
                            <input data-bs-toggle="modal" data-bs-target="#modalSearch"
                                placeholder="Search by make, model, feature" autocomplete="off" tabindex="-1"
                                class="ps-md-5 ps-5 pe-5 py-md-3 py-2 searchbox form-control form-control-lg" name="search"
                                value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Browse by Style  -->
    <div class="position-relative pt-0 mb-5">
        <div class="container">
            <h3 class="h4 border-bottom border-theme border-thick pb-3 mb-4 mt-5 d-inline-block">
                Browse by Style
            </h3>

            <div class="mx-n1 mx-lg-0 my-3 row">
                <div class="p-1 notranslate type-cars col-lg-2 col-md-3 col-4">
                    <a href="{{ route('frontend.inventory.type', 'cars') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center h-100"
                        title="Cars at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/sedan.webp') }}" class="my-1 img-fluid carss" alt="Cars" style="height: 48px; object-fit: contain;">
                        <br>
                        <span class="small font-weight-bold">Cars</span>
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-lg-2 col-md-3 col-4">
                    <a href="{{ route('frontend.inventory.type', 'suvs') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center h-100"
                        title="SUVs at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/suv.webp') }}" class="my-1 img-fluid carss" alt="SUVs" style="height: 48px; object-fit: contain;">
                        <br>
                        <span class="small font-weight-bold">SUVs</span>
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-lg-2 col-md-3 col-4">
                    <a href="{{ route('frontend.inventory.type', 'trucks') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center h-100"
                        title="Trucks at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/truck.webp') }}" class="my-1 img-fluid carss" alt="Trucks" style="height: 48px; object-fit: contain;">
                        <br>
                        <span class="small font-weight-bold">Trucks</span>
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-lg-2 col-md-3 col-4">
                    <a href="{{ route('frontend.inventory.type', 'vans') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center h-100"
                        title="Cargo Vans at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/cargovan.webp') }}" class="my-1 img-fluid carss" alt="Cargo Vans" style="height: 48px; object-fit: contain;">
                        <br>
                        <span class="small font-weight-bold">Cargo Vans</span>
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-lg-2 col-md-3 col-4">
                    <a href="{{ route('frontend.inventory.type', 'hatchbacks') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center h-100"
                        title="Hatchbacks at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/wagon.webp') }}" class="my-1 img-fluid carss" alt="Hatchbacks" style="height: 48px; object-fit: contain;">
                        <br>
                        <span class="small font-weight-bold">Hatchback</span>
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-lg-2 col-md-3 col-4">
                    <a href="{{ route('frontend.inventory') }}?fuel_type[]=Hybrid"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center h-100"
                        title="Hybrid vehicles at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/hybrid.webp') }}" class="my-1 img-fluid carss" alt="Hybrid" style="height: 48px; object-fit: contain;">
                        <br>
                        <span class="small font-weight-bold">Hybrid</span>
                    </a>
                </div>
            </div>

            <div class="d-flex d-sm-none border-top mx-n3">
                <a class="btn btn-primary text-start font-weight-semibold w-100 my-3"
                    href="{{ route('frontend.inventory') }}">
                    All Inventory
                    <span class="float-end text-white">
                        <i class="fa-solid fa-angle-right font-base"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>


    <div class='sc-1a7ba87f-0 erNtBH cElement cContainer w-100'>
        <div class="sc-1a7ba87f-0 cElement cContainer container px-md-3 px-0">
            <div class="cElement cColumnLayout row g-0">
                <div class="cElement cColumn col-sm-6 col-12">
                    <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer container py-5">
                        <div class="sc-24764b04-0 kduiyY d-none d-sm-block"></div>

                        <img width="100" height=""
                            src="{{ asset('assets/frontend/img/streamlinehq-car-tool-keys-transportation-white-200.png') }}" alt=""
                            loading="lazy" fetchpriority="auto"
                            class="cElement rounded-0 cImage mb-3 mx-auto d-block opacity-75 img-fluid">

                        <h3 class="text-center h4 font-weight-bold mb-3">
                            Trading in? Find out your car's trade-in value today.
                        </h3>
                        
                        <div class="text-center">
                            <a href="javascript:void(0)" class="btn btn-outline-light px-4 py-2" title="Get your trade-in value" data-bs-toggle="offcanvas" data-bs-target="#getTrade" aria-controls="offcanvasRight">
                                Get your trade-in value
                                <span class="d-inline-block ms-2">
                                    <i class="fa-solid fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                        <div class="sc-24764b04-0 kduiyY d-none d-sm-block"></div>
                    </div>
                </div>


                <div class="cElement cColumn col-sm-6 col-12">
                    <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer container py-5">
                        <div class="sc-24764b04-0 kduiyY d-none d-sm-block"></div>

                        <img width="100" height=""
                            src="{{ asset('assets/frontend/img/streamlinehq-monetization-touch-browser-business-products-white-200.png') }}"
                            alt="" loading="lazy" fetchpriority="auto"
                            class="cElement rounded-0 cImage mb-3 mx-auto d-block opacity-75 img-fluid">

                        <h3 class="text-center h4 font-weight-bold mb-3">
                            Save an hour at the dealership with an online credit approval.
                        </h3>
                        
                        <div class="text-center">
                            <a href="{{ route('frontend.get-approved') }}" class="btn btn-outline-light px-4 py-2" title="Get approved">
                                Get approved
                                <span class="d-inline-block ms-2">
                                    <i class="fa-solid fa-angle-right"></i>
                                </span>
                            </a>
                        </div>
                        <div class="sc-24764b04-0 kduiyY d-none d-sm-block"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- {!! ($homeAboutCardCtaContent ?? null) ?: ($homeAboutCardCtaFallback ?? view('frontend.partials.home-about-card-cta')->render()) !!} --}}
    {!! view('frontend.partials.home-about-card-cta')->render() !!}

    <div class="min-height-570">
        <section class="border-top" id="home-new-arrivals">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col">
                        <h3 class="h4 border-bottom border-theme border-thick pb-3 d-inline-block mb-0">
                            New Arrivals
                        </h3>
                    </div>

                    <div class="text-end d-none d-md-block col">
                        <a href="{{ route('frontend.inventory') }}" class="btn btn-pill-all">
                            All Inventory
                            <span class="ms-1 small-chevron">
                                <i class="fa-solid fa-angle-right"></i>
                            </span>
                        </a>
                    </div>
                </div>

                {{-- Mobile Button --}}
                <div class="text-end mt-3 d-md-none d-block w-100">
                    <a href="{{ route('frontend.inventory') }}" class="btn w-100 btn-pill-all">
                        All Inventory
                        <span class="ms-1 small-chevron">
                            <i class="fa-solid fa-angle-right"></i>
                        </span>
                    </a>
                </div>

                {{-- Carousel wrapper --}}
                <div class="mt-3 d-flex align-items-center new-arrivals-carousel-wrapper">
                   
                    <div class="new-arrivals-arrow new-arrivals-prev">
                        <i class="fa-solid fa-angle-left"></i>
                    </div>

                  
                    <div class="new-arrivals-track-outer">
                        <div class="new-arrivals-track">
                            @forelse($newArrivals as $vehicle)
                                @include('frontend.partials.vehicle-card')
                            @empty
                                <div class="col-12 text-center text-muted py-5">
                                    No new arrivals at this time.
                                </div>
                            @endforelse
                        </div>
                    </div>

                   
                    <div class="new-arrivals-arrow new-arrivals-next">
                        <i class="fa-solid fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Browse Inventory -->
    <section class="bg-white border-top border-bottom">
        <div class="container">
            <h3 class="h4 border-bottom border-theme border-thick pb-3 d-inline-block mx-auto">Browse Inventory</h3>

            @php
                $makes = [
                    'Audi', 'BMW', 'Cadillac', 'Chevrolet', 'Chrysler', 'Dodge',
                    'Ford', 'Genesis', 'GMC', 'Honda', 'INFINITI', 'Jeep',
                    'Land Rover', 'Lexus', 'Lincoln', 'Mazda', 'Mercedes-Benz',
                    'Nissan', 'Ram', 'Subaru', 'Toyota', 'Volkswagen',
                ];
            @endphp

            <div class="mt-3 row">
                @foreach($makes as $make)
                    <div class="pb-3 col-lg-3 col-md-4 col-sm-6 col-6">
                        <a title="{{ $make }} for sale in Smyrna, TN"
                           href="{{ route('frontend.inventory') }}?make[]={{ urlencode($make) }}">
                            {{ $make }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Dealership Info -->
    @include('frontend.partials.dealership-info')
@endsection

@push('page-modals')
    @include('frontend.offcanvas.get-estimate')
    @include('frontend.offcanvas.get-trade-in')
    @include('frontend.offcanvas.get-approved')
    @include('frontend.offcanvas.unlock-eprice')
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

    <script>
        (function () {
            var track   = document.querySelector('.new-arrivals-track');
            var outer   = document.querySelector('.new-arrivals-track-outer');
            var btnPrev = document.querySelector('.new-arrivals-prev');
            var btnNext = document.querySelector('.new-arrivals-next');

            if (!track || !outer || !btnPrev || !btnNext) return;

            var cards      = track.querySelectorAll('.srp-cardcontainer');
            var total      = cards.length;
            var currentIdx = 0;

            function visibleCount() {
                var w = outer.offsetWidth;
                if (w < 576)  return 1;
                if (w < 992)  return 2;
                if (w < 1200) return 3;
                return 4;
            }

            function cardWidth() {
                return outer.offsetWidth / visibleCount();
            }

            function maxIdx() {
                return Math.max(0, total - visibleCount());
            }

            function update() {
                var offset = currentIdx * cardWidth();
                track.style.transform = 'translateX(-' + offset + 'px)';
                btnPrev.style.opacity = currentIdx <= 0        ? '0.3' : '1';
                btnNext.style.opacity = currentIdx >= maxIdx() ? '0.3' : '1';
            }

            btnPrev.addEventListener('click', function () {
                if (currentIdx > 0) { currentIdx--; update(); }
            });

            btnNext.addEventListener('click', function () {
                if (currentIdx < maxIdx()) { currentIdx++; update(); }
            });

            // Recalculate on resize (debounced)
            var resizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function () {
                    currentIdx = Math.min(currentIdx, maxIdx());
                    update();
                }, 150);
            });

            update();
        })();
    </script>
@endpush
