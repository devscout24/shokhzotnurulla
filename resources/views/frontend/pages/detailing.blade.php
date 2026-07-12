@extends('layouts.frontend.app')

@section('title', __('Detailing') . ' | ' . __(config('app.name')))


@push('page-assets')
    @vite([

        'resources/css/frontend/pages/service.css',
    ])
@endpush


@section('page-content')

    <div class="d-block h-63 d-xl-none" id="mobile-nav-spacer"></div>
    <main id="main-content" tabindex="-1">
        <main role="main" class="page-template-detailing">
            <header class="sc-5a5d3415-0 jHTnHg" id="interior-page-header"
                title="Schedule service at {{ $dealerName ?? config('app.name') }} in Smyrna, TN">
                <div class="position-relative container">
                    <div>
                        <h1 class="m-0 text-white py-3 text-center" id="page_h1">Schedule service at
                            {{ $dealerName ?? config('app.name') }} in
                            Smyrna, TN
                        </h1>
                    </div>
                </div>
            </header>
            <div class="bg-white pt-3 pt-lg-5" id="interior-padding">
                <div class="sc-1a7ba87f-0 kGvcPS cElement cContainer  w-100 ">
                    <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                        <div class="cElement cColumnLayout  d-flex align-items-center row">
                            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                                <img width="999" height="537" alt="" loading="lazy" fetchpriority="auto"
                                    class="cElement cImage mb-2   img-fluid" {{--
                                    src="https://static.overfuel.com/dealers/angel-motors-inc/image/detailing-service.webp"
                                    --}} src="{{ asset('assets/frontend/img/detailing/detailing-service.png') }}"
                                    style="border-radius: 12px;">
                                <div class="sc-24764b04-0 heCyzu"></div>
                            </div>
                            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                                <div class="sc-1a7ba87f-0 kGvcPS cElement cContainer  container ">
                                    <h2 class="" id="0-l16eOkFi" style="text-align: left;">Give your vehicle a complete
                                        refresh with our expert interior and exterior treatments</h2>
                                    <p>At Angel Motors Inc., we believe every car deserves to look its absolute best. Our
                                        detailing team doesn't just clean vehicles; we restore them using professional-grade
                                        techniques tailored to the unique needs of Tennessee drivers. Whether you're looking
                                        to preserve your current vehicle's value or simply want to enjoy a spotless interior
                                        again, we provide a transparent, high-quality service without the dealership
                                        runaround. As a family-owned business, we treat your car with the same care we give
                                        to our own hand-selected inventory.</p>
                                    <div class="sc-24764b04-0 heCyzu"></div>
                                    <div class="text-left">
                                        <a target="_self" class="btn btn-primary" title="Schedule your appointment"
                                            aria-label="Schedule your appointment" href="/detailing#contact">
                                            Schedule your appointment
                                            <span aria-hidden="true"
                                                class="d-inline-block faIcon ofa-regular ofa-angle-right text-white ms-2">
                                                <svg aria-hidden="true" focusable="false" height="16" width="16"
                                                    fill="white">
                                                    <use xlink:href="/regular.svg#angle-right"></use>
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="sc-24764b04-0 dRafiy"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="cElement cDivider my-5">
                    <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                        <div class="cElement cColumnLayout  d-flex align-items-center row">
                            <div class="cElement cColumn col-sm-9 col-12 order-sm-0 order-1">
                                <h2 class="" id="uMZba1p-Vy" style="text-align: left;">Elevate your driving experience with
                                    our specialized detailing treatments</h2>
                                <div class="sc-24764b04-0 hImejv"></div>
                            </div>
                            <div class="cElement cColumn col-sm-3 col-12 order-sm-0 order-1">
                                <div class="sc-24764b04-0 dRafiy"></div>
                            </div>
                        </div>
                        <div class="cElement cColumnLayout  row">
                            <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                                <img width="1163" height="581" alt="" loading="lazy" fetchpriority="auto"
                                    class="cElement cImage mb-2   img-fluid"
                                    src="{{ asset('assets/frontend/img/detailing/wheels-service.png') }}"
                                    style="border-radius: 12px;">
                                <div class="sc-24764b04-0 heCyzu"></div>
                                <h3 class="h4" id="MBQqEIjtLw" style="text-align: left;">Comprehensive Care</h3>
                                <p>We offer specialized services designed to protect your investment. From professional
                                    headlight restoration for safer night driving to deep-cleaning wheels and tires to
                                    remove stubborn road grime, our team ensures every detail is addressed with precision.
                                </p>
                                <div class="sc-24764b04-0 heCyzu"></div>
                            </div>
                            <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                                <div class="sc-24764b04-0 heCyzu"></div>
                                <img width="640" height="480" alt="" loading="lazy" fetchpriority="auto"
                                    class="cElement cImage mb-2   img-fluid"
                                    src="{{ asset('assets/frontend/img/detailing/IMG_3226_(1).png') }}"
                                    style="border-radius: 20px;">
                            </div>
                            <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                                <img width="1163" height="581" alt="" loading="lazy" fetchpriority="auto"
                                    class="cElement cImage mb-2   img-fluid"
                                    src="{{ asset('assets/frontend/img/detailing/deep-cleaning-service.png') }}"
                                    style="border-radius: 12px;">
                                <h3 class="h4" id="4_ha6Mxm86" style="text-align: left;">Precision interior refresh</h3>
                                <p>A clean cabin changes your entire driving experience. Our advanced treatments focus on
                                    deep-cleaning carpets, sanitizing surfaces, and specialized leather conditioning to keep
                                    your upholstery supple, durable, and free from environmental damage.</p>
                            </div>
                        </div>
                        <div class="sc-24764b04-0 Pszmn"></div>
                    </div>
                </div>
                <div class="sc-1a7ba87f-0 cahomj cElement cContainer  w-100 ">
                    <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                        <div class="sc-24764b04-0 dISRze"></div>
                        <div class="sc-1a7ba87f-0 bRDyIy cElement cContainer  container ">
                            <h2 class="text-white" id="WZNGMXTpZb" style="text-align: center;">Bring back that brand-new car
                                feeling with Angel Motors Inc</h2>
                        </div>
                        <div class="sc-24764b04-0 heCyzu"></div>
                        <div class="text-center">
                            <a target="_self" class="btn btn-outline-default btn-lg" title="Get a personalized quote"
                                aria-label="Get a personalized quote" href="/detailing#contact">
                                Get a personalized quote
                                <span aria-hidden="true"
                                    class="d-inline-block faIcon ofa-regular ofa-angle-right text-white ms-2">
                                    <svg aria-hidden="true" focusable="false" height="22" width="22" fill="white">
                                        <use xlink:href="/regular.svg#angle-right"></use>
                                    </svg>
                                </span>
                            </a>
                        </div>
                        <div class="sc-24764b04-0 dISRze"></div>
                    </div>
                </div>
                <div></div>
                <div class="sc-1a7ba87f-0 kGvcPS cElement cContainer  w-100 ">
                    <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                        <div class="sc-24764b04-0 bypa-DG"></div>
                        <div class="cElement cColumnLayout  row">
                            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                                <div class="cElement cIcon text-left">
                                    <span aria-hidden="true" class="d-inline-block faIcon ofa-regular ofa-cars fa-fw h1 "
                                        style="color: rgb(22, 107, 135);">
                                        <svg aria-hidden="true" focusable="false" height="55" width="40" fill="#166B87">
                                            <use xlink:href="/regular.svg#cars"></use>
                                        </svg>
                                    </span>
                                </div>
                                <h2 class="" id="zaNhV4f-1y" style="text-align: left;">Protect your investment and your
                                    drive with the meticulous care of Angel Motors Inc</h2>
                                <div class="sc-24764b04-0 heCyzu"></div>
                                <p>At Angel Motors Inc, we treat your vehicle with the same high standards we apply to our
                                    own premium inventory. Our team provides more than just a wash, we offer specialized
                                    reconditioning designed to protect your car from the Tennessee elements. From deep
                                    interior sanitization to meticulous exterior polishing, we use professional-grade
                                    techniques to maintain your vehicle's beauty and long-term value. Trust our family-owned
                                    team in Smyrna to deliver the quality and transparency you deserve.</p>
                                <hr class="cElement cDivider my-5">
                                <div class="sc-24764b04-0 hImejv"></div>
                            </div>
                            <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                                <div>
                                    <div>
                                        <div id="contact"></div>
                                    </div>
                                </div>
                                <div class="cElement cForm p-4 border rounded bg-white" style="min-height: 500px;">
                                    <div data-cy="confirmation" class="py-4 px-0 text-center px-sm-4 border-bottom">
                                        <div class="my-1">
                                            <span aria-hidden="true"
                                                class="d-inline-block faIcon ofa-regular ofa-circle-check h1">
                                                <svg aria-hidden="true" focusable="false" height="55" width="40"
                                                    fill="inherit">
                                                    <use xlink:href="/regular.svg#circle-check"></use>
                                                </svg>
                                            </span>
                                        </div>
                                        <br>
                                        <div class="h2 text-primary">Question Received!</div>
                                        <div>
                                            <p>Thank you for contacting Angel Motors Inc! We will get back to you as soon as
                                                possible.</p>
                                        </div>
                                    </div>
                                    <div class="pt-4 pb-3 px-0 text-center" id="npsSurvey"> </div>
                                </div>
                            </div>
                        </div>
                        <div class="sc-24764b04-0 Pszmn"></div>
                    </div>
                </div>
                <div class="sc-1a7ba87f-0 gyakut cElement cContainer  w-100 ">
                    <div class="sc-1a7ba87f-0 cElement cContainer  w-100 ">
                        <div class="sc-24764b04-0 Pszmn"></div>
                        <h2 class="" id="4tlMYaZHGR" style="text-align: center;">Frequently asked questions about car
                            detailing</h2>
                        <div class="sc-24764b04-0 dRafiy"></div>
                        <div class="sc-1a7ba87f-0 eBFsoO cElement cContainer  container ">
                            <div class="accordion">
                                <div class="accordion-item">
                                    <div class="d-flex px-4 py-3 cursor-pointer border-bottom">
                                        <h4 class="h5 m-0">How often should I schedule a professional detail?</h4>
                                        <span aria-hidden="true"
                                            class="d-inline-block faIcon ofa-solid ofa-square-minus ms-auto h5 mb-0 text-primary">
                                            <svg aria-hidden="true" focusable="false" height="16" width="16" fill="#166B87">
                                                <use xlink:href="/solid.svg#square-minus"></use>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <div>
                                                <p>For optimal protection against local weather and road conditions, we
                                                    recommend a full detail every 4 to 6 months.<em><span
                                                            class="ql-cursor">﻿</span></em></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="d-flex px-4 py-3 cursor-pointer border-bottom">
                                        <h4 class="h5 m-0">How long will the process take?</h4>
                                        <span aria-hidden="true"
                                            class="d-inline-block faIcon ofa-solid ofa-square-plus ms-auto h5 mb-0 ">
                                            <svg aria-hidden="true" focusable="false" height="16" width="16" fill="inherit">
                                                <use xlink:href="/solid.svg#square-plus"></use>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <div>
                                                <p>Quality takes time. Depending on the vehicle size and condition, a
                                                    professional detail typically ranges from 3 to 6 hours to ensure no
                                                    corner is overlooked.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="d-flex px-4 py-3 cursor-pointer border-bottom">
                                        <h4 class="h5 m-0">Can detailing help with paint imperfections?</h4>
                                        <span aria-hidden="true"
                                            class="d-inline-block faIcon ofa-solid ofa-square-plus ms-auto h5 mb-0 ">
                                            <svg aria-hidden="true" focusable="false" height="16" width="16" fill="inherit">
                                                <use xlink:href="/solid.svg#square-plus"></use>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <div>
                                                <p>Yes, our exterior enhancement process helps remove light swirl marks and
                                                    surface oxidation, significantly improving the depth and clarity of your
                                                    paint’s finish.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <div class="d-flex px-4 py-3 cursor-pointer border-bottom">
                                        <h4 class="h5 m-0">What is included in the interior detailing package?</h4>
                                        <span aria-hidden="true"
                                            class="d-inline-block faIcon ofa-solid ofa-square-plus ms-auto h5 mb-0 ">
                                            <svg aria-hidden="true" focusable="false" height="16" width="16" fill="inherit">
                                                <use xlink:href="/solid.svg#square-plus"></use>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <div>
                                                <p>We provide a complete cabin refresh that covers everything from carpet
                                                    shampooing to leather protection and surface sanitization. Every vehicle
                                                    is different, so we tailor our process to your car's specific needs.
                                                    Contact our team today to discuss your vehicle’s condition and let us
                                                    recommend the best treatment for you.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sc-24764b04-0 Pszmn"></div>
                    </div>
                </div>

            </div>
        </main>
    </main>

    @include('frontend.partials.dealership-info')
@endsection