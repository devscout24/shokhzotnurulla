@extends('layouts.frontend.app')

@section('title', __('About Us') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/about.css',
    ])
@endpush

@section('page-content')
    <div class="d-block h-63 d-xl-none" id="mobile-nav-spacer"></div>

    <div class="page-template-schedule-service" role="main">
        <header class="sc-5a5d3415-0 jHTnHg" id="interior-page-header"
            title="Schedule service at Angel Motors Inc in Smyrna, TN">
            <div class="position-relative container">
                <div>
                    <h1 class="m-0 text-white py-3 text-center" id="page_h1">About Angel Motors Inc in Smyrna, TN
                    </h1>
                </div>
            </div>
        </header>

        <div class="bg-white pt-3 pt-lg-5">
            <div class="sc-1a7ba87f-0 cElement cContainer  w-100 ">
                <div class="sc-1a7ba87f-0 dSRwKi cElement cContainer  container ">
                    <div class="sc-24764b04-0 kdwMZF"></div>
                    <div class="cElement cColumnLayout  row">
                        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-2">

                            <h2 id="zQJVBQyyh0" class="text-start">
                                Discover your next drive with Angel Motors Inc
                            </h2>

                            <div class="sc-24764b04-0 kdvjHq"></div>

                            <p>
                                Angel Motors Inc has proudly served the Smyrna community for years as a
                                family-focused dealership built on trust, value, and genuine care. Conveniently
                                located in Smyrna, TN, we welcome drivers from Murfreesboro, La Vergne, Nashville,
                                and the surrounding Middle Tennessee area who want straightforward car shopping
                                without the runaround.
                            </p>

                            <p>
                                Our lot features a thoughtfully curated selection of
                                <a href="{{ route('frontend.inventory.type', 'cars') }}">pre-owned models and quality used cars, trucks, and SUVs</a>. Every vehicle is carefully inspected, detailed, and prepared so
                                you can feel confident the moment you take the keys.
                            </p>

                            <p>
                                We support you long after the sale with expert maintenance,
                                <a href="{{ route('frontend.get-approved') }}">flexible financing</a> through a wide network of lenders,
                                and a parts department stocked with the parts for the vehicles we sell. At Angel
                                Motors Inc, our mission is simple: treat every customer like a neighbor and deliver
                                an ownership experience that keeps you smiling mile after mile.
                            </p>

                            <div class="sc-24764b04-0 kdvjHq"></div>

                            <div class="text-left">
                                <a target="_self" href="{{ route('frontend.contact') }}" class="btn btn-primary" title="Contact us"
                                    aria-label="Contact us">
                                    Contact us
                                    <span class="d-inline-block text-white ms-2">
                                        <!-- Inline chevron right SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M6.146 4.146a.5.5 0 0 1 .708 0L10.207 8l-3.353 3.854a.5.5 0 0 1-.708-.708L8.793 8 6.146 5.354a.5.5 0 0 1 0-.708z" />
                                        </svg>
                                    </span>
                                </a>
                            </div>

                        </div>
                        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1"><img width="600"
                                height="400" src="{{ asset('assets/frontend/img/Angel_Motors_Inc.webp') }}" alt="" loading="lazy"
                                fetchpriority="auto" class="cElement cImage mb-2 rounded-20   img-fluid">
                        </div>
                    </div>
                </div>


            </div>
            <div class="sc-24764b04-0 eDbZRx"></div>
            <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                <div class="cElement cColumnLayout d-flex align-items-center row">

                    <!-- Left Column: Content with check points -->
                    <div class="cElement cColumn col-sm-9 col-12 order-sm-0 order-1">

                        <div class="sc-1a7ba87f-0 cElement cContainer container">
                            <h2 id="RX3WyNIJ56" class="text-start">Why choose Angel Motors Inc?</h2>

                            <p>
                                Since 2015, Angel Motors Inc has built a strong reputation as one of the most
                                reliable dealerships in Middle Tennessee and the surrounding areas. We are proud to
                                serve Smyrna, Murfreesboro, La Vergne, Nashville, and beyond with honest deals,
                                quality vehicles, and genuine care for every customer.
                            </p>

                            <!-- Checkpoint Items -->
                            <div class="position-relative d-flex align-items-start p-2 craftElement craftCheck">
                                <div class="pe-3">
                                    <span class="d-inline-block">
                                        <!-- Inline solid check SVG -->
                                        <svg height="24" width="24" fill="#166B87"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                            <path
                                                d="M13.485 1.929a.75.75 0 0 1 0 1.06L6.03 10.444l-3.515-3.514a.75.75 0 1 1 1.06-1.06l2.455 2.454L13.485 1.93z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="w-100">
                                    <h3 id="QrNkap2mCI" class="text-large text-start">Established and trusted since
                                        2015</h3>
                                    <p>
                                        We have proudly operated as a reputable dealership in Middle Tennessee for
                                        over a decade, earning the confidence of drivers across the region through
                                        consistent honesty, fair pricing, and outstanding service.
                                    </p>
                                </div>
                            </div>

                            <div class="position-relative d-flex align-items-start p-2 craftElement craftCheck">
                                <div class="pe-3">
                                    <span class="d-inline-block">
                                        <svg height="24" width="24" fill="#166B87"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                            <path
                                                d="M13.485 1.929a.75.75 0 0 1 0 1.06L6.03 10.444l-3.515-3.514a.75.75 0 1 1 1.06-1.06l2.455 2.454L13.485 1.93z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="w-100">
                                    <h3 id="eJU76QLE5r" class="text-large text-start">Every vehicle carefully
                                        prepared</h3>
                                    <p>
                                        Before any car reaches our lot, it undergoes a thorough inspection. We
                                        verify there are no mechanical issues with the engine or transmission,
                                        ensure all fluids are at proper levels, confirm tires have good tread depth,
                                        and finish with a professional detail so it looks and feels showroom fresh.
                                    </p>
                                </div>
                            </div>

                            <div class="position-relative d-flex align-items-start p-2 craftElement craftCheck">
                                <div class="pe-3">
                                    <span class="d-inline-block">
                                        <!-- Regular check SVG -->
                                        <svg height="24" width="24" fill="#166B87"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                            <path
                                                d="M13.485 1.929a.75.75 0 0 1 0 1.06L6.03 10.444l-3.515-3.514a.75.75 0 1 1 1.06-1.06l2.455 2.454L13.485 1.93z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="w-100">
                                    <h3 id="_Ax9BaVDIm" class="text-large text-start">Flexible financing and
                                        convenient delivery</h3>
                                    <p>
                                        We arrange financing through trusted lenders to secure the best approval and
                                        terms that match your needs and budget. We also offer delivery: free within
                                        30 miles of Smyrna, or just $2 per mile beyond that, making it easy to get
                                        your new vehicle home.
                                    </p>
                                </div>
                            </div>

                            <div class="position-relative d-flex align-items-start p-2 craftElement craftCheck">
                                <div class="pe-3">
                                    <span class="d-inline-block">
                                        <svg height="24" width="24" fill="#166B87"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                            <path
                                                d="M13.485 1.929a.75.75 0 0 1 0 1.06L6.03 10.444l-3.515-3.514a.75.75 0 1 1 1.06-1.06l2.455 2.454L13.485 1.93z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="w-100">
                                    <h3 id="QlHx3I52c6" class="text-large text-start">Certified CARFAX Advantage
                                        Dealer</h3>
                                    <p>
                                        Transparency matters to us. As a Certified CARFAX Dealer, we provide a free,
                                        full CARFAX vehicle history report with every car so you can buy with
                                        complete confidence and peace of mind.
                                    </p>
                                </div>
                            </div>

                            <div class="position-relative d-flex align-items-start p-2 craftElement craftCheck">
                                <div class="pe-3">
                                    <span class="d-inline-block">
                                        <svg height="24" width="24" fill="#166B87"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                            <path
                                                d="M13.485 1.929a.75.75 0 0 1 0 1.06L6.03 10.444l-3.515-3.514a.75.75 0 1 1 1.06-1.06l2.455 2.454L13.485 1.93z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="w-100">
                                    <h3 id="ugDyabMiA6" class="text-large text-start">Friendly, knowledgeable,
                                        no-pressure staff</h3>
                                    <p>
                                        Our team is approachable, experienced, and focused on helping you find the
                                        right vehicle without any high-pressure tactics. We listen to your needs,
                                        answer your questions clearly, and guide you toward the perfect match at
                                        your own pace.
                                    </p>
                                </div>
                            </div>

                            <div class="position-relative d-flex align-items-start p-2 craftElement craftCheck">
                                <div class="pe-3">
                                    <span class="d-inline-block">
                                        <svg height="24" width="24" fill="#166B87"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                            <path
                                                d="M13.485 1.929a.75.75 0 0 1 0 1.06L6.03 10.444l-3.515-3.514a.75.75 0 1 1 1.06-1.06l2.455 2.454L13.485 1.93z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="w-100">
                                    <h3 id="O-pHglNCOt" class="text-large text-start">$100 referral bonus for you
                                    </h3>
                                    <p>
                                        Love your experience? Recommend us to friends and family. If they purchase a
                                        vehicle from us and mention your name at the time of sale, you’ll receive a
                                        $100 referral bonus as our way of saying thank you for spreading the word.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right Column: Image and CTA -->
                    <div class="cElement cColumn col-sm-3 col-12 order-sm-0 order-2">
                        <div class="sc-24764b04-0 kdvjHt"></div>
                        <img src="{{ asset('assets/frontend/img/Car-Check-Service_(2).svg') }}" alt="" width="100%" height="200"
                            loading="lazy" fetchpriority="auto"
                            class="cElement cImage mb-2 mx-auto d-block img-fluid">
                        <div class="sc-24764b04-0 kdvjHt"></div>
                        <p class="ql-align-center">
                            At Angel Motors Inc, we believe great cars deserve great relationships. Come visit us in
                            Smyrna and experience the difference for yourself.
                        </p>
                        <div class="text-center">
                            <a target="_self" href="{{ route('frontend.get-approved') }}" class="btn btn-primary" title="Get approved"
                                aria-label="Get approved">
                                Get approved
                                <span class="d-inline-block text-white ms-2">
                                    <!-- Inline chevron right SVG -->
                                    <svg height="16" width="16" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                        <path
                                            d="M6.146 4.146a.5.5 0 0 1 .708 0L10.207 8l-3.353 3.854a.5.5 0 0 1-.708-.708L8.793 8 6.146 5.354a.5.5 0 0 1 0-.708z" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                        <div class="sc-24764b04-0 kdvjHt"></div>
                    </div>

                </div>
                <div class="sc-24764b04-0 eDbZRx"></div>
                <div class="cElement cColumnLayout row">

                    <!-- Card 1: Vehicles for every Tennessee road -->
                    <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                        <div class="sc-1a7ba87f-0 cElement cContainer cursor-pointer container card3">
                            <div class="sc-24764b04-0 kdvjHt"></div>
                            <img src="{{ asset('assets/frontend/img/Car_inventory_(7).svg') }}" alt="" width="70" height="70"
                                loading="lazy" fetchpriority="auto" class="cElement cImage mb-2 img-fluid"
                                style="border-radius:0px">
                            <div class="sc-24764b04-0 kdvjHt"></div>
                            <h3 id="7k9GPnnvCj" class="text-medium text-start">
                                Vehicles for every Tennessee road
                            </h3>
                            <p>
                                Our Smyrna lot offers a wide range from fuel-sipping sedans and versatile crossovers
                                to strong trucks and
                                <a href="{{ route('frontend.inventory.type', 'suvs') }}">roomy SUVs</a>. We update our selection regularly to match commuting
                                needs, weekend trips, and family adventures across Middle Tennessee. Every vehicle
                                receives a full inspection, professional detailing, and our promise of satisfaction.
                            </p>
                            <div class="sc-24764b04-0 kdvjHt"></div>
                        </div>
                    </div>

                    <!-- Card 2: Financing that works for you -->
                    <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                        <div class="sc-1a7ba87f-0 cElement cContainer container card3">
                            <div class="sc-24764b04-0 kdvjHt"></div>
                            <img src="{{ asset('assets/frontend/img/Credit-Card-Hand.svg') }}" alt="" width="70" height="70"
                                loading="lazy" fetchpriority="auto" class="cElement cImage mb-2 img-fluid"
                                style="border-radius:0px">
                            <div class="sc-24764b04-0 kdvjHt"></div>
                            <h3 id="E5i6OMSmRW" class="text-medium text-start">
                                Financing that works for you
                            </h3>
                            <p>
                                Our finance professionals partner with numerous lenders to secure competitive rates
                                and terms tailored to your situation. We help first-time buyers, those with past
                                credit challenges, and everyone in between with honest, easy-to-understand options.
                                Fast decisions and
                                <a href="{{ route('frontend.get-approved') }}">flexible plans</a> mean you can focus on enjoying your new
                                ride.
                            </p>
                            <div class="sc-24764b04-0 kdvjHt"></div>
                        </div>
                    </div>

                    <!-- Card 3: Rooted in the community -->
                    <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                        <div class="sc-1a7ba87f-0 cElement cContainer container card3">
                            <div class="sc-24764b04-0 kdvjHt"></div>
                            <img src="{{ asset('assets/frontend/img/Community_(5).svg') }}" alt="" width="70" height="70" loading="lazy"
                                fetchpriority="auto" class="cElement cImage mb-2 img-fluid"
                                style="border-radius:0px">
                            <div class="sc-24764b04-0 kdvjHt"></div>
                            <h3 id="YQjGn7PqXx" class="text-medium text-start">
                                Rooted in the community
                            </h3>
                            <p>
                                Our skilled technicians understand Tennessee driving conditions and use quality
                                parts along with modern tools for everything from oil changes and tire service to
                                major repairs. We keep you informed every step of the way. Convenient scheduling,
                                clear explanations, and attention to detail keep your vehicle dependable and your
                                schedule uninterrupted.
                            </p>
                            <div class="sc-24764b04-0 kdvjHt"></div>
                        </div>
                    </div>

                </div>
                <div class="sc-24764b04-0 eDbZRx"></div>
            </div>

        </div>
    </div>

    <div class="sc-1a7ba87f-0 gbDIgM cElement cContainer  w-100 ">
        <div class="sc-1a7ba87f-0 cElement cContainer  container ">
            <div class="sc-24764b04-0 kduiyY"></div>
            <h2 class="text-center" id="fYyddghirh">Explore our inventory</h2>
            <div class="sc-24764b04-0 kdtJAz"></div>
            <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                <div class="container">
                    <div class="cElement cInventory row">
                        @forelse($newArrivals as $vehicle)
                            @include('frontend.partials.vehicle-card')
                        @empty
                            <div class="col-12 text-center text-muted py-5">
                                No new arrivals at this time.
                            </div>
                        @endforelse

                        <div class="text-center pt-3 col-12">
                            <a title="Browse inventory" class="btn btn-primary" href="{{ route('frontend.inventory') }}">
                                Browse inventory
                                <span class="d-inline-block ms-2">
                                    <!-- Inline chevron-right SVG -->
                                    <svg height="16" width="16" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                        <path
                                            d="M6.146 4.146a.5.5 0 0 1 .708 0L10.207 8l-3.353 3.854a.5.5 0 0 1-.708-.708L8.793 8 6.146 5.354a.5.5 0 0 1 0-.708z" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <div class="sc-24764b04-0 kduiyY"></div>
    </div>

    <!-- Dealership Info -->
    @include('frontend.partials.dealership-info')
@endsection
