<!-- mobile drawer  -->
<div class="offcanvas offcanvas-end  " id="mobileDrawer">
    <div class="m-0 px-3 py-2 border-bottom bg-lighter d-flex align-items-center">
        <div class="w-100 d-flex offcanvas-title h5">

            <div class="w-50 d-flex">

                <div class="pe-3 border-end">
                    <span class="text-primary h2 mb-0">
                        <i class="fa-solid fa-phone blue-text-29"></i>
                    </span>
                </div>

                <div class="px-3 border-end">
                    <span class="text-primary h2 mb-0">
                        <i class="fa-solid fa-location-dot blue-text-29"></i>
                    </span>
                </div>

                <div class="px-3">
                    <span class="text-primary h2 mb-0">
                        <i class="fa-solid fa-clock blue-text-29"></i>
                    </span>
                </div>

                {{-- <div class="px-2 border-start">
                    <div translate="no" class="notranslate dropdown">
                        <button type="button" id="lang-switcher" aria-expanded="false"
                            class="py-0 text-decoration-none px-2 mt-2 dropdown-toggle btn btn-link">
                            <img width="24" height="16" alt="Select language" class="me-2"
                                src="https://static.overfuel.com/images/icons/flags/en.png">
                            EN
                        </button>
                        <div x-placement="bottom-start" aria-labelledby="lang-switcher"
                            class="py-0 absolute-offset2 dropdown-menu ">
                            <a data-rr-ui-dropdown-item="" class="border-bottom dropdown-item" role="button"
                                tabindex="0" href="#"><img width="24" height="16" alt="Select English" class="me-2"
                                   src="{{ asset('assets/frontend/img/en.png') }}">English</a><a data-rr-ui-dropdown-item=""
                                class="border-bottom dropdown-item" role="button" tabindex="0" href="#"><img
                                    width="24" height="16" alt="Select Spanish" class="me-2"
                                   src="{{ asset('assets/frontend/img/es.png') }}">Spanish</a>
                        </div>
                    </div>
                </div> --}}
            </div>

            <div class="w-50 text-end" id="closeDrawerBtn">
                <span class="text-muted h2 float-end mb-0">
                    <i class="fa-solid fa-xmark large"></i>
                </span>
            </div>

        </div>
    </div>

    <div class="pt-0 offcanvas-body pb-20">
        <div class="bg-lighter p-3 mx-n3 position-relative border-top border-bottom">
            <div class="row">
                <div class="col-12 p-0">
                    <a title="View Inventory" class="btn btn-primary w-100" href="{{ route('frontend.inventory') }}">
                        All Inventory
                        <span class="float-end text-white">
                            <i class="fa-solid fa-angle-right font-base"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        {{-- <div class="pb-3 border-bottom row">
            <div class="col">
                <div class="w-100 mt-3  dropdown">
                    <button type="button" id="recents" aria-expanded="false"
                        class="text-decoration-none bg-white ps-0 w-100 border border-dark dropdown-toggle btn btn-link">

                        <span class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#166B87"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3 1.75a.5.5 0 1 1-.5.866l-3.25-1.9A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z" />
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z" />
                            </svg>
                        </span>

                        Viewed
                    </button>

                    <div class="py-0 dropdown-menu mt-4 show absolute-offset2">
                        <a href="#" class="border-bottom w-100 text-truncate dropdown-item">
                            <img alt="2015 Mazda CX-5 Touring" loading="lazy" width="64" height="48"
                                decoding="async" data-nimg="1" class="compare-img img-fluid me-3"
                               src="{{ asset('assets/frontend/img/532d1a48-02bb-46eb-9480-f03976378b17-2 (1).webp') }}">
                            2015 Mazda CX-5 Touring
                        </a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="w-100 mt-3  dropdown">
                    <button type="button" id="favorites" aria-expanded="false"
                        class="text-decoration-none bg-white ps-0 d-block w-100 border border-dark dropdown-toggle btn btn-link">

                        <span class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#166B87"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 2.748-.717-.737C5.6.281 2.514 1.878 2.514 4.223c0 1.657 1.167 3.04 3.012 4.685L8 11.314l2.474-2.406c1.845-1.646 3.012-3.028 3.012-4.685 0-2.345-3.086-3.942-4.769-1.212L8 2.748z" />
                            </svg>
                        </span>

                        Favorites
                    </button>
                    <div class="py-0 mt-3 absolute-offset dropdown-menu ">
                        <a href="#" class="border-bottom dropdown-item">
                            <em class="text-muted">No items viewed yet.</em>
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}

        <nav>
            <div class="font-weight-bold py-3 parent-nav">
                Inventory
                <span class="float-end fillMuted toggle-arrow">
                    <i class="fa-solid fa-angle-down font-base"></i>
                </span>

                <nav role="navigation" aria-label="Primary" class="submenu">
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.inventory') }}">All inventory</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.inventory.type', 'cars') }}">Cars</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.inventory.type', 'trucks') }}">Trucks</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.inventory.type', 'suvs') }}">SUVs</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.inventory.type', 'vans') }}">Vans</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.inventory.type', 'convertibles') }}">Convertibles</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.inventory.type', 'hatchbacks') }}">Hatchbacks</a></div>
                </nav>
            </div>

            <div class="font-weight-bold py-3 parent-nav">
                Finance
                <span class="float-end text-muted toggle-arrow">
                    <i class="fa-solid fa-angle-down font-base"></i>
                </span>

                <nav role="navigation" aria-label="Primary" class="submenu">
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.get-approved') }}">Get approved</a></div>
                    {{-- <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="#">Get pre-qualified with Capital One</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="#">Car loan calculator</a></div> --}}
                </nav>
            </div>
            <div class="font-weight-bold py-3" bis_skin_checked="1">
                <a href="{{ route('frontend.service') }}">Service</a>
            </div>
            {{-- <div class="font-weight-bold py-3 parent-nav">
                Detailing
            </div> --}}
            <div class="font-weight-bold py-3 parent-nav">
                About
                <span class="float-end text-muted toggle-arrow">
                    <i class="fa-solid fa-angle-down font-base"></i>
                </span>

                <nav role="navigation" aria-label="Primary" class="submenu">
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.about') }}">About us</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="{{ route('frontend.contact') }}">Contact us</a></div>
                    {{-- <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3" href="#">3
                            Month / 3,000 mile certified warranty</a></div>
                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="#">Careers</a></div>

                    <div class="w-100 py-3 font-weight-normal border-bottom"><a class="text-primary p-3"
                            href="#">Customer reviews</a></div> --}}
                </nav>
            </div>
        </nav>
    </div>
</div>