@extends('layouts.frontend.app')

@push('page-assets')
    @vite(['resources/css/frontend/pages/inventory-listing.css', 'resources/js/frontend/pages/inventory-listing.js', 'resources/js/frontend/pages/trade-in.js', 'resources/js/frontend/pages/get-approved-offcanvas.js', 'resources/js/frontend/pages/unlock-eprice.js', 'resources/js/frontend/pages/nps.js'])
@endpush

@section('page-content')
    <div class="d-block h-104 d-xl-none" id="mobile-nav-spacer" bis_skin_checked="1"></div>

    <main class="position-relative path-inventory" id="inventory-index">
        <div class="bg-secondary py-3" id="top-banner">
            <div class="container">
                <div class="d-flex align-items-center justify-content-center position-relative">
                    <div class="d-flex align-items-center justify-content-center flex-wrap">
                        <img alt="Get your trade-in value" loading="lazy" width="30" height="30" decoding="async"
                            class="me-3"
                            src="{{ asset('assets/frontend/img/streamlinehq-car-tool-keys-transportation-white-200.png') }}">
                        <span class="text-white me-3 mb-2 mb-sm-0">What's your car worth?</span>
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-bold" style="font-size: 13px;"
                            data-bs-toggle="offcanvas" data-bs-target="#getTrade">Get your trade-in value</button>
                    </div>

                    <button type="button" id="close-banner" class="btn btn-link text-white p-0 position-absolute end-0">
                        <i class="fa-solid fa-xmark h4 mb-0"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- mobile filter  -->
        <div class="sc-1ee57aef-0 eiTTSf w-100 border-bottom ToolbarMobile bg-white d-block d-xl-none searchHidden">
            <div class="no-gutters row-bordered text-start text-nowrap sticky-top bg-white border-bottom border-top row">
                <div data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"
                    class="py-2 pe-2 ps-3 cursor-pointer col-sm-6 col-5">
                    <span class="d-inline-block text-primary me-2">
                        <i class="fa-solid fa-filter"></i>
                    </span>
                    Filters
                    <span class="ps-1 text-primary">(1)</span>
                </div>
                <div class="py-2 px-3 col-sm-5 col-5" data-bs-toggle="modal" data-bs-target="#modalGallery">
                    <span class="d-inline-block text-primary me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                            fill="#166B87">
                            <path d="M2 3h10v1H2V3zm0 3h8v1H2V6zm0 3h6v1H2V9zm0 3h4v1H2v-1z" />
                            <path d="M12 5l3 3-3 3V9H9V7h3V5z" />
                        </svg>
                    </span>
                    Best Match
                </div>
                <div class="py-2 pe-3 text-end col-sm-1 col-2" data-bs-toggle="modal" data-bs-target="#modalSearch"
                    aria-label="Search">
                    <span class="d-inline-block text-primary me-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="py-4 bg-lighter d-md-none border-bottom text-nowrap overflow-auto" data-cy="srp-pills">
            <div class="px-2 mx-0 container">

                <button class="me-3 rounded border bg-white p-2 border-dark">Side impact airbags</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Collision warning</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Bluetooth</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Backup camera</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Lane departure warning</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Satellite radio ready</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Lane keep assist</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Heated seats</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Automatic climate control</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Adaptive cruise control</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Push start</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Apple CarPlay</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Android Auto</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Navigation</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Keyless entry</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Blind spot monitor</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Cross traffic alert</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Wifi hotspot</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Fog lights</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Remote engine start</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Power seats</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Sunroof / moonroof</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Rain sensing wipers</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Tow package</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Leather seats</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Rear A/C</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Memory seats</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Wireless phone charging</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Heated steering wheel</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Premium audio</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Cooled seats</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Xenon headlights</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Hands-free liftgate</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Third row seat</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Smart device remote start</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Rear heated seats</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Parking sensors / assist</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Captain seats</button>
                <button class="me-3 rounded border bg-white p-2 border-dark">Head up display</button>

            </div>
        </div>
        <div class="mb-5 container-fluid px-lg-5">
            <div class="row mt-3">

                <!-- filter  -->
                <div class="col-xl-3 col-lg-4 d-none d-lg-block filter-container">
                    <div class="pe-0">
                        @include('frontend.partials.filter-sidebar')
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8 col-12 ps-lg-4">
                    <div class="position-relative mt-2 mb-4">
                        <div class="position-relative search-wrapper w-100">
                            <span class="search-icon text-primary" style="position: absolute; left: 15px; top: 12px;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>

                            <input data-bs-toggle="modal" data-bs-target="#modalSearch" data-cy="input-search"
                                placeholder="Search by make, model, feature" autocomplete="off" tabindex="-1"
                                type="text" class="search-input w-100 form-control py-2 ps-5"
                                style="border-radius: 8px;" name="search" value="">
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 id="inventory-heading" class="inventoryheading m-0 h5">
                            {{ $total }} used
                            {{ $type ? config("vehicle_types.{$type}.heading_label") : 'vehicles' }} for sale in Smyrna, TN
                        </h1>
                        <div class="dropdown">
                            <button type="button" id="sortby" aria-expanded="false" data-cy="sortby"
                                class="dropdown-toggle btn btn-sm d-flex align-items-center border rounded-pill px-3">
                                <span class="me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16" fill="#166B87">
                                        <path d="M2 12h12v-1H2v1zm0-5h8V6H2v1zm0-5h4V1H2v1z" />
                                    </svg>
                                </span>
                                <strong>Sort by Best Match</strong>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <ul class="p-0 mb-0 list-unstyled">
                                    <li class="dropdown-item py-2 px-3 border-bottom">Best Match</li>
                                    <li class="dropdown-item py-2 px-3 border-bottom">Price: Lowest</li>
                                    <li class="dropdown-item py-2 px-3 border-bottom">Newest Arrivals</li>
                                    <li class="dropdown-item py-2 px-3 border-bottom">Miles: Low to High</li>
                                    <li class="dropdown-item py-2 px-3 border-bottom">Miles: High to Low</li>
                                    <li class="dropdown-item py-2 px-3 border-bottom">Year: Newest</li>
                                    <li class="dropdown-item py-2 px-3 border-bottom">Year: Oldest</li>
                                    <li class="dropdown-item py-2 px-3">Make: A-Z</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- details  -->

                    <div class="row g-3" id="inventory-grid">
                        @include('frontend.partials.inventory-grid')
                    </div>

                    {{-- Pagination --}}
                    <div id="inventory-pagination" class="mt-5">
                        {{ $vehicles->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="container" bis_skin_checked="1">
            <div class="mb-5" id="srp-content" bis_skin_checked="1">
                <h2><strong>Used cars for sale in Smyrna, TN</strong></h2>
                <p>If you’re looking for used cars for sale in Smyrna, TN, Angel Motors Inc offers a strong
                    selection of dependable pre-owned sedans and passenger vehicles designed for comfort,
                    efficiency, and everyday reliability. Conveniently located in Smyrna, Tennessee, we proudly
                    serve drivers throughout Rutherford County and the greater Nashville area who want quality
                    vehicles at competitive prices.</p>
                <p>Whether you’re commuting to work, heading into Nashville, or running errands around town, our
                    cars inventory in Smyrna includes a variety of models built to fit different lifestyles and
                    budgets.</p>
                <p>⸻</p>
                <h3><strong>Popular car makes &amp; models at Angel Motors Inc</strong></h3>
                <p>Our used cars inventory frequently includes vehicles from trusted manufacturers known for
                    performance and long-term value, such as:</p>
                <p> • Toyota Camry &amp; Corolla – Fuel-efficient sedans with proven reliability</p>
                <p> • Honda Civic &amp; Accord – Comfortable, well-rounded cars ideal for daily driving</p>
                <p> • Nissan Altima &amp; Sentra – Smooth-riding vehicles with modern features</p>
                <p> • Chevrolet Malibu &amp; Impala – Spacious sedans offering practical performance</p>
                <p> • Hyundai Elantra &amp; Sonata – Stylish options delivering strong value</p>
                <p> • Kia Forte &amp; Optima – Affordable cars with impressive features</p>
                <p> • Ford Fusion – A balanced midsize sedan with comfort and capability</p>
                <p>Inventory is updated regularly, giving you access to a variety of trims, features, and price
                    points.</p>
                <p>⸻</p>
                <h3><strong>Why buy your next car from Angel Motors Inc?</strong></h3>
                <p>Drivers throughout Smyrna choose Angel Motors Inc because we offer:</p>
                <p> • Competitive pricing on quality pre-owned cars</p>
                <p> • Flexible financing options for a range of credit situations</p>
                <p> • A wide selection of reliable sedans and passenger vehicles</p>
                <p> • A knowledgeable, customer-focused team</p>
                <p> • A convenient Smyrna location near major roadways</p>
                <p>We’re here to help you compare models, explore available features, and find the right car for
                    your needs.</p>
                <p>⸻</p>
                <h3><strong>Proudly serving Smyrna &amp; surrounding communities</strong></h3>
                <p>We proudly serve customers from:</p>
                <p>Smyrna • Murfreesboro • La Vergne • Nashville • Antioch • Nolensville • Christiana • Rockvale •
                    Lebanon • and surrounding Middle Tennessee communities</p>
                <p>Our Smyrna location makes it easy for drivers across the region to shop locally for dependable
                    used cars.</p>
                <p>⸻</p>
                <h3><strong>Shop used cars in Smyrna, TN today</strong></h3>
                <p>Browse our current cars inventory online to compare models, pricing, and features. Then visit
                    Angel Motors Inc in Smyrna, TN to schedule your test drive and drive home in a car you’ll feel
                    confident about every mile of the way.</p>
            </div>
        </div>

        <div class="py-3 text-small opacity-75 disclaimers container" bis_skin_checked="1">
            <div bis_skin_checked="1">
                @php
                    $cleanDisclaimer = trim(strip_tags($inventoryDisclaimer));
                @endphp

                @if (!empty($cleanDisclaimer))
                    <p>{!! $inventoryDisclaimer !!}</p>
                @else
                    <p>
                        It is the customer's sole responsibility to verify the existence and condition of any equipment
                        listed. Neither the dealership nor eBizAutos is responsible for misprints on prices or equipment. It
                        is the customer's sole responsibility to verify the accuracy of the prices with the dealer,
                        including the pricing for all added accessories. Financing Fees apply for all vehicles not purchased
                        with cash. See Dealer for details.
                    </p>
                @endif
            </div>
        </div>
    </main>
@endsection

@push('page-modals')
    @include('frontend.offcanvas.mobile-filters')
    @include('frontend.offcanvas.get-trade-in')
    @include('frontend.offcanvas.get-approved')
    @include('frontend.offcanvas.unlock-eprice')
    @include('frontend.modals.sort-inventory')
@endpush

@push('page-scripts')
    <script>
        window.srpConfig = {
            filterUrl: '{{ $type ? route('frontend.inventory.type.filter', $type) : route('frontend.inventory.filter') }}',
            csrf: '{{ csrf_token() }}',
        };

        window.tiRoutes = {
            tradeIn: '{{ route('frontend.forms.trade-in') }}',
            tradeInPhotos: '{{ route('frontend.forms.trade-in.photos') }}',
            makeModels: '{{ route('frontend.data.make-models', ['make' => '__make__']) }}',
        };

        window.gaRoutes = {
            submit: '{{ route('frontend.forms.get-approved') }}',
        };

        window.npsRouteTemplate = '{{ route('frontend.forms.nps', ['formEntry' => '__id__']) }}';
    </script>

    {{-- Close banner --}}
    <script>
        document.getElementById('close-banner')?.addEventListener('click', function() {
            document.getElementById('top-banner').style.display = 'none';
        });
    </script>
@endpush
