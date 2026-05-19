@extends('layouts.frontend.app')

@section('title', __('Home') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/home.css',
        'resources/js/frontend/pages/trade-in.js',
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
                <video loop="" muted="" autoplay="" playsinline="" src="{{ asset('assets/frontend/img/angel-motors-hero-video.mp4') }}"
                    poster="./assets/img/angel-motors-hero-video.mp4_poster.webp') }}" class="video"></video>
            </div>

            <div class="hero-caption text-center text-white">
                <div class="py-4">
                    <h1 class="font-weight-bold">Angel Motors Inc used cars in Smyrna, TN</h1>
                    <h2 class="font-weight-bold">Quality pre-owned vehicles, transparent pricing, and trusted
                        service</h2>
                    <div class="bg-light p-3 rounded border">
                        <div class="position-relative">
                            <span class="d-inline-block position-absolute search-icon iconPostion">
                                <i class="fa-solid fa-magnifying-glass greyIcon"></i>
                            </span>
                            <input data-bs-toggle="modal" data-bs-target="#modalSearch"
                                placeholder="Search by make, model, feature" autocomplete="off" tabindex="-1"
                                class="ps-5 pe-5 py-3 searchbox form-control form-control-lg" name="search"
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
            <div class="d-xl-none pt-3 mx-n3 px-0">
                <button type="button" class="me-3 btn btn-primary">Body Style</button>
                <button type="button" class="btn btn-default">Make &amp; Model</button>
            </div>
            <h3 class="d-none d-xl-inline-block h4 border-bottom border-theme border-thick pb-3 mb-4 mt-5">
                Browse by Style
            </h3>

            <div class="mx-n1 mx-lg-0 my-3 row">
                <div class="p-1 notranslate type-cars col-md-2 col-4">
                    <a href="{{ route('frontend.inventory.type', 'cars') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center"
                        title="Cars at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/sedan.webp') }}" class="my-1 img-fluid carss" alt="Cars">
                        <br>
                        Cars
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-md-2 col-4">
                    <a href="{{ route('frontend.inventory.type', 'suvs') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center"
                        title="SUVs at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/suv.webp') }}" class="my-1 img-fluid carss" alt="SUVs">
                        <br>
                        SUVs
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-md-2 col-4">
                    <a href="{{ route('frontend.inventory.type', 'trucks') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center"
                        title="Trucks at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/truck.webp') }}" class="my-1 img-fluid carss" alt="Trucks">
                        <br>
                        Trucks
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-md-2 col-4">
                    <a href="{{ route('frontend.inventory.type', 'vans') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center"
                        title="Cargo Vans at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/cargovan.webp') }}" class="my-1 img-fluid carss" alt="Cargo Vans">
                        <br>
                        Cargo Vans
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-md-2 col-4">
                    <a href="{{ route('frontend.inventory.type', 'hatchbacks') }}"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center"
                        title="Hatchbacks at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/wagon.webp') }}" class="my-1 img-fluid carss" alt="Hatchbacks">
                        <br>
                        Hatchback
                    </a>
                </div>

                <div class="p-1 notranslate type-cars col-md-2 col-4">
                    <a href="{{ route('frontend.inventory') }}?fuel_type[]=Hybrid"
                        class="d-block cursor-pointer bg-white border rounded p-3 text-center"
                        title="Hybrid vehicles at Angel Motors Inc used car dealership in Smyrna, TN">
                        <img src="{{ asset('assets/frontend/img/hybrid.webp') }}" class="my-1 img-fluid carss" alt="Hybrid">
                        <br>
                        Hybrid
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


    <div class='sc-1a7ba87f-0 erNtBH cElement cContainer  w-100 '>
        <div class="sc-1a7ba87f-0 cElement cContainer  container ">
            <div class="cElement cColumnLayout  row">
                <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                    <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                        <div class="sc-24764b04-0 kduiyY"></div>

                        <img width="100" height=""
                            src="{{ asset('assets/frontend/img/streamlinehq-car-tool-keys-transportation-white-200.png') }}" alt=""
                            loading="lazy" fetchpriority="auto"
                            class="cElement rounded-0 cImage mb-2 mx-auto d-block opacity-50  img-fluid">

                        <div class="sc-24764b04-0 kdvjHq"></div>
                        <h3 class="text-center" id="SpCVhCFnHE">
                            Trading in? Find out your car's trade-in value today.
                        </h3>
                        <div class="sc-24764b04-0 kdvwXJ"></div>
                        <div class="text-center">
                            <a href="javascript:void(0)" class="btn btn-outline-default" title="Get your trade-in value" data-bs-toggle="offcanvas" data-bs-target="#getTrade" aria-controls="offcanvasRight">
                                Get your trade-in value
                                <span class="d-inline-block ms-2">
                                    <i class="fa-solid fa-angle-right white-text-16"></i>
                                </span>
                            </a>
                        </div>
                        <div class="sc-24764b04-0 kduiyY"></div>
                    </div>
                </div>


                <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                    <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                        <div class="sc-24764b04-0 kduiyY"></div>

                        <img width="100" height=""
                            src="{{ asset('assets/frontend/img/streamlinehq-monetization-touch-browser-business-products-white-200.png') }}"
                            alt="" loading="lazy" fetchpriority="auto"
                            class="cElement rounded-0 cImage mb-2 mx-auto d-block opacity-50  img-fluid">

                        <div class="sc-24764b04-0 kdvjHq"></div>
                        <h3 class="text-center" id="SpCVhCFnHE">
                            Save an hour at the dealership with an online credit approval.
                        </h3>
                        <div class="sc-24764b04-0 kdvwXJ"></div>
                        <div class="text-center">
                            <a href="{{ route('frontend.get-approved') }}" class="btn btn-outline-default" title="Get your trade-in value">
                                Get approved
                                <span class="d-inline-block ms-2">
                                    <i class="fa-solid fa-angle-right white-text-16"></i>
                                </span>
                            </a>
                        </div>
                        <div class="sc-24764b04-0 kduiyY"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- about us section  -->
    <div class="sc-1a7ba87f-0 cElement cContainer  container ">
        <div class="sc-24764b04-0 kdwMZF"> </div>
        <div class="cElement cColumnLayout  d-flex align-items-center row">
            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                <img width="1200" height="772" src="{{ asset('assets/frontend/img/angel-motors-top-rated-dealer-2.webp') }}" alt=""
                    loading="lazy" fetchpriority="auto"
                    class="cElement rounded-2xl cImage mb-2 mx-auto d-block  img-fluid">
                <div class="sc-24764b04-0 kdvwXG"></div>
            </div>

            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-2">
                <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                    <div class="cElement cText eyebrow">
                        <p>ABOUT US</p>
                    </div>
                    <h2 class="h2 text-start font-weight-bold" id="B8x2iRjmKT">Find your next quality
                        pre-owned vehicle in Smyrna, TN</h2>
                    <div class="sc-24764b04-0 kdvwXJ"></div>
                    <p>At Angel Motors Inc., we make car buying simple, transparent, and stress-free. Serving
                        Smyrna, TN and surrounding areas since 2015, we’re committed to helping you drive away with
                        confidence.</p>

                    <p>Explore our <a href="{{ route('frontend.inventory.type', 'cars') }}">wide selection of inspected cars</a>, <a href="{{ route('frontend.inventory.type', 'trucks') }}">trucks</a>, and <a
                            href="{{ route('frontend.inventory.type', 'suvs') }}">SUVs</a>. Whether you're looking for a
                        reliable daily driver or something with a little more space and power, our team is here to
                        help you find the right vehicle for your lifestyle and budget.</p>
                </div>
            </div>
        </div>

        <div class="sc-24764b04-0 kdvjHq"></div>
        <div class="cElement cColumnLayout row-bordered  row">
            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                <div class="cElement cColumnLayout row-bordered  row">
                    <div class="cElement bottomBorder border-end  cColumn col-sm-6 col-12 order-sm-0 order-1">
                        <div class="sc-24764b04-0 kdvjHq"></div>
                        <div class="cElement cIcon text-center">
                            <span class="d-inline-block fa-fw h1 mt-0 mb-3">
                                <i class="fa-solid fa-car blue-large-text"></i>
                            </span>

                        </div>
                        <div class="cElement cText h4 font-weight-bold mb-n2" bis_skin_checked="1">
                            <p class="text-center"><strong>All makes &amp; models</strong></p>
                        </div>
                        <p class="text-center">Curated selection of top automotive brands</p>
                    </div>

                    <div class="cElement bottomBorder border-end cColumn col-sm-6 col-12 order-sm-0 order-1">
                        <div class="sc-24764b04-0 kdvjHq"></div>
                        <div class="cElement cIcon text-center">
                            <span class="d-inline-block fa-fw h1 mt-0 mb-3">
                                <i class="fa-solid fa-star blue-large-text"></i>
                            </span>
                        </div>
                        <div class="cElement cText h4 font-weight-bold mb-n2" bis_skin_checked="1">
                            <p class="text-center">
                                <strong>4.4 stars
                                </strong>
                            </p>
                        </div>
                        <p class="text-center">On Google Reviews from our local community</p>
                    </div>
                </div>
            </div>

            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                <div class="cElement cColumnLayout row-bordered  row">
                    <div class="cElement bottomBorder border-end  cColumn col-sm-6 col-12 order-sm-0 order-1">
                        <div class="sc-24764b04-0 kdvjHq"></div>
                        <div class="cElement cIcon text-center">
                            <span class="d-inline-block fa-fw h1 mt-0 mb-3">
                                <i class="fa-regular fa-comment blue-large-text"></i>
                            </span>

                        </div>
                        <div class="cElement cText h4 font-weight-bold mb-n2" bis_skin_checked="1">
                            <p class="text-center"><strong>200+</strong></p>
                        </div>
                        <p class="text-center">reviews from happy customers

                        </p>
                    </div>

                    <div class="cElement border-end cColumn col-sm-6 col-12 order-sm-0 order-1">
                        <div class="sc-24764b04-0 kdvjHq"></div>
                        <div class="cElement cIcon text-center">
                            <span class="d-inline-block fa-fw h1 mt-0 mb-3">
                                <i class="fa-solid fa-shield-halved blue-large-text"></i>
                            </span>
                        </div>
                        <div class="cElement cText h4 font-weight-bold mb-n2" bis_skin_checked="1">
                            <p class="text-center">
                                <strong>Hand-selected quality


                                </strong>
                            </p>
                        </div>
                        <p class="text-center">Every vehicle rigorously inspected for reliability and value

                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="sc-24764b04-0 kdtJAz"></div>
    </div>

    <div class="sc-1a7ba87f-0 cElement cContainer  container card-section">
        <div class="cElement cColumnLayout  d-flex align-items-center row">
            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-2">
                <div class="sc-1a7ba87f-0 cElement cContainer  w-100 card2 h-100 border">
                    <div class="cElement cIcon text-center">
                        <span class="d-inline-block fa-fw h1 primaryy">
                            <i class="fa-solid fa-shield-halved font-55"></i>
                        </span>
                    </div>

                    <div class="sc-24764b04-0 kdvVWg"></div>

                    <h2 class="text-center font-medium" id="eFw6ufiigV">Driven by transparency
                        and reliability
                    </h2>
                    <div class="sc-24764b04-0 kdvVWg"></div>
                    <p class="text-center">As a Certified CARFAX Advantage Dealer, we provide a free CARFAX Vehicle
                        History Report with every vehicle. From mechanical inspections to final detailing, we make
                        sure each vehicle meets our standards before it reaches you.</p>
                </div>
            </div>

            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                <img width="100%" height="703" src="{{ asset('assets/frontend/img/car-inspection.webp') }}" alt="" loading="lazy"
                    fetchpriority="auto"
                    class="cElement cImage mb-2 mx-auto d-block rounded-2xl img-fluid mb-0 border img-fluid">
            </div>
        </div>
    </div>

    <div class="sc-1a7ba87f-0 cElement cContainer  container gradient">
        <div class="sc-24764b04-0 kdvVWh"></div>
        <div class="cElement cColumnLayout  row">
            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                <div
                    class="sc-1a7ba87f-0 bwwxTl cElement cContainer  cursor-pointer  w-100 cursor-pointer text-white rounded border overflow-hidden">
                    <div class=" cElement cColorOverlay bg-black"></div>
                    <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                        <div class="sc-24764b04-0 evgXRR"></div>
                        <h3 class="h2  text-start  extrabold " id="rln4nJ9ffJ">Financing made
                            simple</h3>
                        <div class="sc-24764b04-0 kdvwXJ"></div>

                        <p>We offer arranged financing solutions designed to help you secure the approval that fits
                            your situation. Our team works with trusted lenders to make the process smooth,
                            transparent, and straightforward from start to finish.</p>
                        <div class="sc-24764b04-0 kdvwXJ"></div>

                    </div>
                </div>
                <div class="sc-24764b04-0 kdvjHt"></div>
            </div>

            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                <div
                    class="sc-1a7ba87f-0 cbWkvM cElement cContainer  cursor-pointer  w-100 cursor-pointer text-white rounded border overflow-hidden">
                    <div class=" cElement cColorOverlay bg-black"></div>
                    <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                        <div class="sc-24764b04-0 evgXRR"></div>
                        <h3 class="h2 text-start extrabold " id="rln4nJ9ffJ">Here for you
                            beyond the
                            sale</h3>
                        <div class="sc-24764b04-0 kdvwXJ"></div>

                        <p>
                            Our commitment doesn’t end when you drive off the lot. From scheduling service to
                            answering your questions, our team is here to provide dependable support and guidance
                            you can rely on long after your purchase.
                        </p>
                        <div class="sc-24764b04-0 kdvwXJ"></div>

                    </div>
                </div>
                <div class="sc-24764b04-0 kdvjHt"></div>
            </div>
        </div>
        <div class=" kduiyY"></div>
    </div>

    <div class=""></div>

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
                    {{-- Prev arrow --}}
                    <div class="new-arrivals-arrow new-arrivals-prev">
                        <i class="fa-solid fa-angle-left"></i>
                    </div>

                    {{-- Scrollable track --}}
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

                    {{-- Next arrow --}}
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
                    <div class="pb-3 col-md-3 col-sm-2 col-6">
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
    @include('frontend.offcanvas.get-trade-in')
    @include('frontend.offcanvas.unlock-eprice')
@endpush

@push('page-scripts')
    <script>
        window.tiRoutes = {
            tradeIn:       '{{ route('frontend.forms.trade-in') }}',
            tradeInPhotos: '{{ route('frontend.forms.trade-in.photos') }}',
            makeModels:    '{{ route('frontend.data.make-models', ['make' => '__make__']) }}',
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