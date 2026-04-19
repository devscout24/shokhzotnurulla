@extends('layouts.frontend.app')

@section('title', __('Schedule Service') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/frontend/pages/service.css',
    ])
@endpush

@section('page-content')
    <div class="d-block h-63 d-xl-none" id="mobile-nav-spacer"></div>

    <div class="page-template-schedule-service" role="main">
        <header class="sc-5a5d3415-0 jHTnHg" id="interior-page-header"
            title="Schedule service at {{ $dealerName ?? config('app.name') }} in Smyrna, TN">
            <div class="position-relative container">
                <div>
                    <h1 class="m-0 text-white py-3 text-center" id="page_h1">Schedule service at {{ $dealerName ?? config('app.name') }} in
                        Smyrna, TN</h1>
                </div>
            </div>
        </header>

        <div class="bg-white pt-3 pt-lg-5">
            <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                <div class="cElement cColumnLayout  row">
                    <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-2">
                        <h2 class="text-start" id="bskH7Gsbd5">Book your service appointment at {{ $dealerName ?? config('app.name') }}
                        </h2>
                        <div class="sc-24764b04-0 kdvwXJ"></div>
                        <p>
                            Staying on top of vehicle care is simple with {{ $dealerName ?? config('app.name') }} Our online scheduler lets
                            you reserve a convenient time for oil changes, tire rotations, brake checks, fluid
                            services, or any other maintenance or repair your car needs, no phone call required.
                        </p>
                        <p>Choose the service you want, enter your vehicle details, and pick an open slot that works
                            with your schedule. Our experienced technicians handle every job with attention to
                            detail, use quality parts, and provide clear explanations, so you always know what was
                            done and why. You will receive instant confirmation and a friendly reminder to keep
                            everything on track.</p>
                        <p>Whether it is routine upkeep or addressing a small issue before it grows, we make the
                            process fast, transparent, and hassle-free. Schedule your appointment online today and
                            count on {{ $dealerName ?? config('app.name') }} in Smyrna to keep your vehicle safe and reliable on Middle
                            Tennessee roads.</p>
                        <div class="sc-24764b04-0 kdvwXJ"></div>
                        <img width="800" height="534" src="{{ asset('assets/frontend/img/Angel_Motors_Inc_service.webp') }}" alt=""
                            loading="lazy" fetchpriority="auto" class="cElement rounded-12 cImage mb-2   img-fluid">
                        <div class="sc-24764b04-0 kdvwXJ"></div>
                        <div class="cElement cColumnLayout  d-flex align-items-center row">
                            <div class="cElement cColumn col-sm-6 col-6 order-sm-0 order-1">
                                <img s width="600" height="300" src="{{ asset('assets/frontend/img/Service_center.webp') }}" alt=""
                                    loading="lazy" fetchpriority="auto"
                                    class="cElement rounded-12 cImage mb-2   img-fluid">
                            </div>
                            <div class="cElement cColumn col-sm-6 col-6  rounded-12 order-sm-0 order-1"><img
                                    width="600" height="300" src="{{ asset('assets/frontend/img/ceramic-coating-buffing.webp') }}" alt=""
                                    loading="lazy" fetchpriority="auto" class="cElement cImage mb-2   img-fluid">
                            </div>
                        </div>
                    </div>

                    <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
                        <div class="sc-1a7ba87f-0 d-sm-none cElement cContainer  container ">
                            <div class="sc-24764b04-0 edIJuL"></div>
                            <h2 class="text-start" id="S_Dd-1zubD">Schedule service form</h2>
                        </div>
                        <div class="sc-1a7ba87f-0 cElement cContainer  container " id="connect">
                            <div class="cElement cForm p-4 border rounded bg-white h-500">
                                <div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-3 mb-md-4">
                                                <label for="firstname" class="form-label mb-1">
                                                    First Name <strong class="text-danger ps-1">*</strong>
                                                </label>
                                                <input type="text" id="firstname" name="firstname"
                                                    class="form-control" placeholder="First" minlength="2"
                                                    maxlength="100" tabindex="1"
                                                    data-cy="formcontrol-text-firstname">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3 mb-md-4">
                                                <label for="lastname" class="form-label mb-1">
                                                    Last Name <strong class="text-danger ps-1">*</strong>
                                                </label>
                                                <input type="text" id="lastname" name="lastname"
                                                    class="form-control" placeholder="Last" minlength="2"
                                                    maxlength="100" tabindex="2"
                                                    data-cy="formcontrol-text-lastname">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3 mb-md-4">
                                                <label for="email" class="form-label mb-1">
                                                    Email Address <strong class="text-danger ps-1">*</strong>
                                                </label>
                                                <input type="email" id="email" name="email" class="form-control"
                                                    placeholder="you@email.com" tabindex="3" aria-label="Your email"
                                                    autocomplete="off" data-cy="formcontrol-email">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3 mb-md-4">
                                                <label for="phone" class="form-label mb-1">
                                                    Phone Number <strong class="text-danger ps-1">*</strong>
                                                </label>
                                                <input type="tel" id="phone" name="phone" class="form-control"
                                                    placeholder="(###) ###-####" tabindex="4" inputmode="numeric"
                                                    data-cy="formcontrol-phone">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3 mb-md-4">
                                                <label for="commpref" class="form-label mb-1">
                                                    What is the best way to contact you?
                                                    <strong class="text-danger ps-1">*</strong>
                                                </label>

                                                <div>
                                                    <label class="custom-control custom-radio p-0">
                                                        <input type="radio" name="commpref" value="email"
                                                            class="form-check-input"
                                                            data-cy="formcontrol-radio-commpref" checked>
                                                        <span class="custom-control-label">Email</span>
                                                    </label>
                                                </div>

                                                <div>
                                                    <label class="custom-control custom-radio p-0">
                                                        <input type="radio" name="commpref" value="text"
                                                            class="form-check-input"
                                                            data-cy="formcontrol-radio-commpref">
                                                        <span class="custom-control-label">Text Message</span>
                                                    </label>
                                                </div>

                                                <div>
                                                    <label class="custom-control custom-radio p-0">
                                                        <input type="radio" name="commpref" value="phone"
                                                            class="form-check-input"
                                                            data-cy="formcontrol-radio-commpref">
                                                        <span class="custom-control-label">Phone Call</span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3 mb-md-4 border-top"></div>
                                        </div>
                                        <div class=" col-sm-12">
                                            <div
                                                class="mb-3 mb-md-4 pb-3 text-muted border-bottom text-small text-uppercase font-weight-bold">
                                                Vehicle Info</div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="mb-3 mb-md-4">
                                                <label for="year" class="form-label mb-1">
                                                    Year <strong class="text-danger ps-1">*</strong>
                                                </label>
                                                <select id="year" name="year" class="custom-select form-select"
                                                    tabindex="8" data-cy="formcontrol-yearselect">
                                                    <option value="">Select...</option>
                                                    <option value="2027">2027</option>
                                                    <option value="2026">2026</option>
                                                    <option value="2025">2025</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2022">2022</option>
                                                    <option value="2021">2021</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2019">2019</option>
                                                    <option value="2018">2018</option>
                                                    <option value="2017">2017</option>
                                                    <option value="2016">2016</option>
                                                    <option value="2015">2015</option>
                                                    <option value="2014">2014</option>
                                                    <option value="2013">2013</option>
                                                    <option value="2012">2012</option>
                                                    <option value="2011">2011</option>
                                                    <option value="2010">2010</option>
                                                    <option value="2009">2009</option>
                                                    <option value="2008">2008</option>
                                                    <option value="2007">2007</option>
                                                    <option value="2006">2006</option>
                                                    <option value="2005">2005</option>
                                                    <option value="2004">2004</option>
                                                    <option value="2003">2003</option>
                                                    <option value="2002">2002</option>
                                                    <option value="2001">2001</option>
                                                    <option value="2000">2000</option>
                                                    <option value="1999">1999</option>
                                                    <option value="1998">1998</option>
                                                    <option value="1997">1997</option>
                                                    <option value="1996">1996</option>
                                                    <option value="1995">1995</option>
                                                    <option value="1994">1994</option>
                                                    <option value="1993">1993</option>
                                                    <option value="1992">1992</option>
                                                    <option value="1991">1991</option>
                                                    <option value="1990">1990</option>
                                                    <option value="1989">1989</option>
                                                    <option value="1988">1988</option>
                                                    <option value="1987">1987</option>
                                                    <option value="1986">1986</option>
                                                    <option value="1985">1985</option>
                                                    <option value="1984">1984</option>
                                                    <option value="1983">1983</option>
                                                    <option value="1982">1982</option>
                                                    <option value="1981">1981</option>
                                                    <option value="1980">1980</option>
                                                    <option value="1979">1979</option>
                                                    <option value="1978">1978</option>
                                                    <option value="1977">1977</option>
                                                    <option value="1976">1976</option>
                                                    <option value="1975">1975</option>
                                                    <option value="1974">1974</option>
                                                    <option value="1973">1973</option>
                                                    <option value="1972">1972</option>
                                                    <option value="1971">1971</option>
                                                    <option value="1970">1970</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class=" col-sm-3">
                                            <div class="mb-3 mb-md-4"><label class="m-0 form-label"><label
                                                        class="mb-1" for="make">Make <strong
                                                            class="text-danger ps-1">*</strong></label></label><input
                                                    data-cy="formcontrol-text-make" tabindex="9" placeholder="Ford"
                                                    id="make" class="form-control  form-control" type="text"
                                                    value="" name="make" fdprocessedid="53n4jc"></div>
                                        </div>
                                        <div class=" col-sm-3">
                                            <div class="mb-3 mb-md-4"><label class="m-0 form-label"><label
                                                        class="mb-1" for="model">Model <strong
                                                            class="text-danger ps-1">*</strong></label></label><input
                                                    data-cy="formcontrol-text-model" tabindex="10"
                                                    placeholder="Focus" id="model"
                                                    class="form-control  form-control" type="text" value=""
                                                    name="model" fdprocessedid="r56fq"></div>
                                        </div>
                                        <div class=" col-sm-3">
                                            <div class="mb-3 mb-md-4"><label class="m-0 form-label"><label
                                                        class="mb-1" for="mileage">Mileage <strong
                                                            class="text-danger ps-1">*</strong></label></label><input
                                                    data-cy="formcontrol-number-mileage" tabindex="11"
                                                    placeholder="0" min="0" id="mileage"
                                                    class="form-control  form-control" type="number" value=""
                                                    name="mileage" fdprocessedid="u2ua4v"></div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3 mb-md-4">
                                                <label for="f18text" class="m-0 form-label mb-1">
                                                    VIN #
                                                </label>
                                                <input type="text" id="f18text" name="f18text"
                                                    class="form-control form-control" tabindex="12"
                                                    data-cy="formcontrol-text-f18text">
                                            </div>
                                        </div>

                                        <div class=" col-sm-12">
                                            <div class="mb-3 mb-md-4"><label class="m-0 form-label"><label
                                                        class="mb-1" for="warranty">Is your vehicle under warranty?
                                                        <strong class="text-danger ps-1">*</strong></label></label>
                                                <div role="group" class="d-flex w-50 pe-3 btn-group">
                                                    <button type="button" data-cy="formcontrol-btntab" value="yes"
                                                        class="active btn btn-default" fdprocessedid="559f3i">Yes
                                                    </button>

                                                    <button type="button" data-cy="formcontrol-btntab" value="no"
                                                        class="btn btn-default" fdprocessedid="ffo04">No</button>

                                                </div>
                                                <input tabindex="-1" type="hidden" value="yes" name="warranty">
                                            </div>
                                        </div>
                                        <div class=" col-sm-12">
                                            <div class="mb-3 mb-md-4 border-top"></div>
                                        </div>
                                        <div class=" col-sm-12">
                                            <div
                                                class="mb-3 mb-md-4 pb-3 text-muted border-bottom text-small text-uppercase font-weight-bold">
                                                Requested Service(s)</div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3 mb-md-4">
                                                <label for="services[]" class="m-0 form-label mb-1">
                                                    Service(s) Needed:
                                                    <strong class="text-danger ps-1">*</strong>
                                                </label>

                                                <div class="custom-control p-0 custom-checkbox">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="checkDefault">
                                                    <label class="custom-control-label" for="make_audi">
                                                        Oil Change
                                                    </label>
                                                </div>

                                                <div class="custom-control p-0 custom-checkbox">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="checkDefault">
                                                    <label class="custom-control-label" for="make_audi">
                                                        Tire Rotation
                                                    </label>
                                                </div>

                                                <div class="custom-control p-0 custom-checkbox">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="checkDefault">
                                                    <label class="custom-control-label" for="make_audi">
                                                        Coolant Flush
                                                    </label>
                                                </div>

                                                <div class="custom-control p-0 custom-checkbox">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="checkDefault">
                                                    <label class="custom-control-label" for="make_audi">
                                                        Transmission Flush
                                                    </label>
                                                </div>

                                                <div class="custom-control p-0 custom-checkbox">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="checkDefault">
                                                    <label class="custom-control-label" for="make_audi">
                                                        Tire Balance
                                                    </label>
                                                </div>

                                                <div class="custom-control p-0 custom-checkbox">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="checkDefault">
                                                    <label class="custom-control-label" for="make_audi">
                                                        Other (please describe below)
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                        <div class=" col-sm-12">
                                            <div class="d-block">
                                                <div class="mb-3 mb-md-4"><label class="m-0 form-label"><label
                                                            class="mb-1" for="comment">Further Description /
                                                            Requests </label></label><textarea
                                                        data-cy="formcontrol-textarea" name="comment"
                                                        placeholder="I'd like to schedule service" tabindex="17"
                                                        type="text" id="comment"
                                                        class="form-control h-100 form-control"></textarea></div>
                                            </div>
                                        </div>
                                        <div class=" col-sm-12">
                                            <div class="d-block">
                                                <div class="mb-3 mb-md-4"><label class="m-0 form-label"><label
                                                            class="mb-1" for="preferreddate">Do you have a preferred
                                                            date and time? </label></label><textarea
                                                        data-cy="formcontrol-textarea" name="preferreddate"
                                                        placeholder="I'd like to schedule service" tabindex="18"
                                                        type="text" id="preferreddate"
                                                        class="form-control h-100 form-control"></textarea></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="pt-4 border-top"></div>
                                        </div>

                                        <div class="col-sm-6 col-12">
                                            <button type="submit" data-cy="btn-submit-form-wrapper"
                                                class="text-start w-100 btn btn-primary btn-md">
                                                Continue
                                                <span class="d-inline-block float-end">
                                                    <!-- Perfect chevron right SVG -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd"
                                                            d="M6.146 4.146a.5.5 0 0 1 .708 0L10.207 8l-3.353 3.854a.5.5 0 0 1-.708-.708L8.793 8 6.146 5.354a.5.5 0 0 1 0-.708z" />
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sc-24764b04-0 kduiyY"></div>
            </div>

            <div class="sc-1a7ba87f-0 cjNa-Do cElement cContainer  w-100 ">
                <div class="sc-24764b04-0 kduiyY"></div>
                <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                    <div class="cElement cColumnLayout d-flex align-items-center row">

                        <!-- Text column -->
                        <div class="cElement cColumn col-sm-9 col-12 order-sm-0 order-1">
                            <h2 id="i4YLMPfJIZ" class="text-start">
                                Trust the skilled technicians at {{ $dealerName ?? config('app.name') }}
                            </h2>
                            <p>
                                Whether your car is due for routine care or you’ve noticed something that needs
                                attention,
                                trust {{ $dealerName ?? config('app.name') }} in Smyrna for honest, professional service you can count on.
                                Book your appointment online now or give us a call. We’re here to keep you moving.
                            </p>
                        </div>

                        <!-- Button column -->
                        <div class="cElement cColumn col-sm-3 col-12 order-sm-0 order-1">
                            <div class="text-left">
                                <a href="#connect" target="_self" class="btn btn-primary btn-lg"
                                    title="Schedule service" aria-label="Schedule service">
                                    Schedule service
                                    <span class="d-inline-block text-white ms-2">
                                        <!-- Inline chevron right SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M6.146 4.146a.5.5 0 0 1 .708 0L10.207 8l-3.353 3.854a.5.5 0 0 1-.708-.708L8.793 8 6.146 5.354a.5.5 0 0 1 0-.708z" />
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="sc-24764b04-0 kdvVWg"></div>
                    <div class="cElement cColumnLayout  row">
                        <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">

                            <img src="{{ asset('assets/frontend/img/Service_centers.webp') }}" alt="" width="600" height="300"
                                loading="lazy" fetchpriority="auto"
                                class="cElement rounded-12 cImage mb-2 img-fluid">

                            <div class="sc-24764b04-0 kdvwXJ"></div>

                            <h3 id="B3pumXZP5x" class="text-start">
                                Precision diagnostics that uncover the real story
                            </h3>

                            <p>
                                When your check engine light appears, or something just doesn’t feel right, our
                                advanced diagnostic service pinpoints the issue quickly and accurately. Using
                                state-of-the-art scanning tools and manufacturer-level software, our technicians
                                read trouble codes, test live data, and perform targeted inspections so we identify
                                the root cause, not just the symptom. You receive a clear, easy-to-understand
                                explanation along with honest recommendations, helping you make informed decisions
                                without unnecessary repairs or guesswork.
                            </p>

                        </div>

                        <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">

                            <img src="{{ asset('assets/frontend/img/Car_servicing.webp') }}" alt="" width="600" height="300"
                                loading="lazy" fetchpriority="auto"
                                class="cElement rounded-12 cImage mb-2 img-fluid">

                            <div class="sc-24764b04-0 kdvwXJ"></div>

                            <h3 id="u54nVd5SDe" class="text-start">
                                Cooling system care built for Tennessee summers
                            </h3>

                            <p>
                                Tennessee heat can push any vehicle’s cooling system to the limit. Our comprehensive
                                cooling service includes pressure testing the radiator and hoses, checking the
                                thermostat and water pump, flushing old coolant, and refilling with the correct
                                mixture to prevent overheating and corrosion. We pay special attention to AC
                                performance too, recharging refrigerant and inspecting components so your cabin
                                stays comfortable on long commutes to Nashville or summer drives with the family.
                            </p>

                        </div>

                        <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">

                            <img src="{{ asset('assets/frontend/img/Car_tires_(4).webp') }}" alt="" width="600" height="300"
                                loading="lazy" fetchpriority="auto"
                                class="cElement rounded-12 cImage mb-2 img-fluid">

                            <div class="sc-24764b04-0 kdvwXJ"></div>

                            <h3 id="iQ_CM24Ucc" class="text-start">
                                Brake &amp; tire safety check that gives you peace of mind
                            </h3>

                            <p>
                                Safe stops start with strong brakes and reliable tires. Our brake and tire service
                                thoroughly inspects pads, rotors, calipers, and lines for wear, measures rotor
                                thickness, checks fluid condition, and evaluates tire tread depth, pressure, and
                                alignment. We provide straightforward recommendations, whether it’s a simple pad
                                replacement, rotor resurfacing, tire rotation, or a full set, so you drive
                                confidently knowing your vehicle is ready for Middle Tennessee’s highways, rain, and
                                everyday demands.
                            </p>

                        </div>
                    </div>

                    <div class="sc-24764b04-0 kdtJAz"></div>
                </div>
            </div>

            <div class="sc-1a7ba87f-0 dbDRLt cElement cContainer  w-100 ">
                <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                    <div class="sc-24764b04-0 kduiyY"></div>
                    <div class="cElement cColumnLayout  d-flex align-items-center row">
                        <div class="cElement cColumn col-sm-9 col-12 order-sm-0 order-1">
                            <h2 class="text-start" id="lekCyOT9nu">{{ $dealerName ?? config('app.name') }} services and
                                maintains all makes and models
                                including foreign imports and luxury brands</h2>
                            <p>When it comes to exotic, European, or high-performance vehicles, not every shop has
                                the expertise to keep them at peak performance. At Angle Motors Inc in Smyrna, TN,
                                our factory-trained technicians bring specialized knowledge and precision tools to
                                every job. We treat your luxury or import as it deserves, delivering meticulous care
                                so every drive feels just as thrilling as day one.</p>
                        </div>
                        <div class="cElement cColumn col-sm-3 col-12 order-sm-0 order-1"></div>
                    </div>
                    <div class="sc-24764b04-0 kdvVWg"></div>
                    <div class="cElement cColumnLayout row">

                        <!-- RAM Card -->
                        <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                            <img src="{{ asset('assets/frontend/img/RAM_truck_(1).webp') }}" alt="" width="600" height="300"
                                loading="lazy" fetchpriority="auto"
                                class="cElement rounded-12 cImage mb-2 img-fluid">
                            <div class="sc-24764b04-0 kdvwXJ"></div>
                            <h3 id="86FJuG7xMN" class="text-start">RAM</h3>
                            <p>
                                RAM trucks are built for serious work, towing, and everyday toughness. Our services
                                cover heavy-duty suspension inspections, Cummins or HEMI engine maintenance,
                                transmission fluid &amp; filter services, Uconnect system diagnostics, and full
                                brake overhauls with performance-grade pads and rotors when needed. We keep your RAM
                                hauling, trailering, and conquering Middle Tennessee roads with the same strength
                                and reliability it was engineered for.
                            </p>
                        </div>

                        <!-- Cadillac Card -->
                        <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                            <img src="{{ asset('assets/frontend/img/Cadillac_(1).webp') }}" alt="" width="600" height="300" loading="lazy"
                                fetchpriority="auto" class="cElement cImage rounded-12 mb-2 img-fluid">
                            <div class="sc-24764b04-0 kdvwXJ"></div>
                            <h3 id="a-65SPz4Te" class="text-start">Cadillac</h3>
                            <p>
                                Cadillac stands for refined luxury, cutting-edge technology, and effortless power.
                                We specialize in Magnetic Ride Control calibrations, Northstar or Blackwing engine
                                care, Super Cruise system checks, AWD transfer case service, and comprehensive
                                OBD-II diagnostics with Cadillac-specific software. Our technicians preserve that
                                signature blend of comfort, performance, and sophistication so your Cadillac
                                continues to turn heads and deliver a world-class experience mile after mile.
                            </p>
                        </div>

                        <!-- Ford Card -->
                        <div class="cElement cColumn col-sm-4 col-12 order-sm-0 order-1">
                            <img src="{{ asset('assets/frontend/img/Ford_SUV.webp') }}" alt="" width="600" height="300" loading="lazy"
                                fetchpriority="auto" class="cElement rounded-12 cImage mb-2 img-fluid">
                            <div class="sc-24764b04-0 kdvwXJ"></div>
                            <h3 id="vCbHZGeGw2" class="text-start">Ford</h3>
                            <p>
                                From F-150s and Super Duty trucks to Mustang, Explorer, and Bronco, Ford vehicles
                                are icons of American capability and innovation. Our services include EcoBoost turbo
                                maintenance, 10-speed transmission fluid exchanges, SYNC &amp; FordPass
                                troubleshooting, trailer brake controller checks, and precision alignments for
                                lifted or lowered setups. We treat every Ford with the respect it deserves, ensuring
                                it stays dependable whether you're commuting, off-roading, or pulling heavy loads
                                across Tennessee.
                            </p>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Dealership Info -->
    @include('frontend.partials.dealership-info')
@endsection
