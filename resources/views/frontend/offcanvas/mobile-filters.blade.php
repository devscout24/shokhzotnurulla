{{-- Mobile Filters Offcanvas --}}
<div class="offcanvas offcanvas-end w-100" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="m-0 border-bottom offcanvas-header" bis_skin_checked="1">
        <div class="w-100 d-flex align-items-center offcanvas-title h5" bis_skin_checked="1">Filters
            <span type="button" data-bs-dismiss="offcanvas" aria-label="Close"
                class="text-large text-muted ms-auto mb-0">
                <i class="fa-solid fa-xmark"></i>
            </span>
        </div>
    </div>

    <div class="pt-0 pb-0 bg-lighter offcanvas-body">
        <div class="mb-5 mt-3 mt-md-0 notranslate filterCard card">
            <div class="pt-3 pb-2 bg-white card-header">
                <div class="card-title h6 font-weight-bold mb-2" bis_skin_checked="1">32<!-- -->
                    matches <br>
                    <a data-cy="btn-reset-filters" title="Clear inventory filters"
                        class="float-end font-weight-normal text-14 cursor-pointer text-primary" href="#">Clear
                        Filters</a>
                </div>
                <div class="d-inline-block badge-default px-2 py-0 me-2 rounded border my-1 cursor-pointer">
                    <span class="small">Sedan</span>
                    <span class="d-inline-block ms-2 float-end text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 16 16"
                            fill="#166B87">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                        </svg>
                    </span>
                </div>

                <div class="d-inline-block badge-default px-2 py-0 me-2 rounded border my-1 cursor-pointer">
                    <span class="small">Couple</span>
                    <span class="d-inline-block ms-2 float-end text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 16 16"
                            fill="#166B87">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                        </svg>
                    </span>
                </div>
                <div class="d-inline-block badge-default px-2 py-0 me-2 rounded border my-1 cursor-pointer">
                    <span class="small">Hatchback</span>
                    <span class="d-inline-block ms-2 float-end text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 16 16"
                            fill="#166B87">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                        </svg>
                    </span>
                </div>
                <div class="d-inline-block badge-default px-2 py-0 me-2 rounded border my-1 cursor-pointer">
                    <span class="small">Wagon</span>
                    <span class="d-inline-block ms-2 float-end text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 16 16"
                            fill="#166B87">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                        </svg>
                    </span>
                </div>
                <div class="d-inline-block badge-default px-2 py-0 me-2 rounded border my-1 cursor-pointer">
                    <span class="small">Convertible</span>
                    <span class="d-inline-block ms-2 float-end text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 16 16"
                            fill="#166B87">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                        </svg>
                    </span>
                </div>
            </div>

            <form data-cy='filter-section' class="pb-0 mt-sm-0">
                <div class="price-financing card-footer">
                    <div class="cursor-pointer" bis_skin_checked="1">Price Financing
                    </div>
                    <div class="opacity-100">

                        <div class="slider-wrapper">
                            <div class="slider-track price-financing-slider">

                                <div class="slider-handle" id="handle-min" tabindex="0" aria-valuemax="32000"
                                    aria-valuemin="5000" aria-valuenow="5000" draggable="false" role="slider">
                                    <div class="slider-handle-bar"></div>
                                </div>
                                <div class="slider-handle slider-handle-top" id="handle-max" tabindex="0"
                                    aria-valuemax="32000" aria-valuemin="5000" aria-valuenow="32000"
                                    draggable="false" role="slider">
                                    <div class="slider-handle-bar"></div>
                                </div>
                            </div>
                        </div>

                        <div class="no-gutters my-3 row">
                            <div class="pe-1 col-6" bis_skin_checked="1">
                                <small><strong>Min</strong></small><input type="text" class="form-control"
                                    name="price-display-min" value="$5,000" inputmode="numeric"
                                    fdprocessedid="tm5j2t">
                            </div>
                            <div class="ps-1 col-6" bis_skin_checked="1">
                                <small><strong>Max</strong></small><input type="text" class="form-control"
                                    name="price-display-max" value="$32,000" inputmode="numeric"
                                    fdprocessedid="ryekgtq">
                            </div>
                        </div>
                    </div>
                    <div class="rounded text-sm border my-2 py-2 px-3 text-center" bis_skin_checked="1">75<!-- -->
                        months @ <br> <span class="notranslate">6.79<!-- -->%</span> APR<div
                            class="text-primary cursor-pointer border-top mt-2 pt-2" bis_skin_checked="1">Adjust
                            Terms</div>
                    </div>
                    <input type="hidden" tabindex="-1" id="minprice" name="price[gt]" value="5000">
                    <input type="hidden" tabindex="-1" id="maxprice" name="price[lt]" value="32000">
                </div>
                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Make & Model
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content max-280">

                        <!-- AUDI -->
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    AUDI (1)
                                </label>
                            </div>

                            <div class="model-list mb-3">
                                <div class="text-muted pb-1 ps-4">Model</div>
                                <div class="my-1 ps-4">
                                    <div class="custom-control custom-checkbox">
                                        <input id="model_audi_a4" type="checkbox" class="checkbox-round" value="A4"
                                            name="model[AUDI][]">
                                        <label class="custom-control-label" for="model_audi_a4">A4
                                            (1)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BMW -->
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_bmw" class="make-checkbox checkbox-round" type="checkbox"
                                    value="BMW" name="make[]">
                                <label class="custom-control-label" for="make_bmw">
                                    BMW (1)
                                </label>
                            </div>

                            <div class="model-list mb-3">
                                <div class="text-muted pb-1 ps-4">Model</div>
                                <div class="my-1 ps-4">
                                    <div class="custom-control custom-checkbox">
                                        <input id="model_bmw_330xi" type="checkbox" class="checkbox-round"
                                            value="330XI" name="model[BMW][]">
                                        <label class="custom-control-label" for="model_bmw_330xi">330XI (1)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CHEVROLET -->
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_chevrolet" class="make-checkbox checkbox-round" type="checkbox"
                                    value="CHEVROLET" name="make[]">
                                <label class="custom-control-label" for="make_chevrolet">CHEVROLET
                                    (4)</label>
                            </div>

                            <div class="model-list mb-3">
                                <div class="text-muted pb-1 ps-4">Model</div>

                                <div class="my-1 ps-4">
                                    <div class="custom-control custom-checkbox">
                                        <input id="model_chevrolet_camaro" type="checkbox" class="checkbox-round"
                                            value="Camaro" name="model[CHEVROLET][]">
                                        <label class="custom-control-label" for="model_chevrolet_camaro">Camaro
                                            (1)</label>
                                    </div>
                                </div>
                                <div class="my-1 ps-4">
                                    <div class="custom-control custom-checkbox">
                                        <input id="model_chevrolet_express" type="checkbox" class="checkbox-round"
                                            value="EXPRESS G2500" name="model[CHEVROLET][]">
                                        <label class="custom-control-label" for="model_chevrolet_express">EXPRESS
                                            G2500 (1)</label>
                                    </div>
                                </div>
                                <div class="my-1 ps-4">
                                    <div class="custom-control custom-checkbox">
                                        <input id="model_chevrolet_malibu" type="checkbox" class="checkbox-round"
                                            value="Malibu" name="model[CHEVROLET][]">
                                        <label class="custom-control-label" for="model_chevrolet_malibu">MALIBU
                                            (1)</label>
                                    </div>
                                </div>
                                <div class="my-1 ps-4">
                                    <div class="custom-control custom-checkbox">
                                        <input id="model_chevrolet_spark" type="checkbox" class="checkbox-round"
                                            value="Spark" name="model[CHEVROLET][]">
                                        <label class="custom-control-label" for="model_chevrolet_spark">SPARK
                                            (1)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DODGE -->
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_dodge" class="make-checkbox checkbox-round" type="checkbox"
                                    value="DODGE" name="make[]">
                                <label class="custom-control-label" for="make_dodge">DODGE
                                    (1)</label>
                            </div>

                            <div class="model-list mb-3">
                                <div class="text-muted pb-1 ps-4">Model</div>
                                <div class="my-1 ps-4">
                                    <div class="custom-control custom-checkbox">
                                        <input id="model_dodge_charger" type="checkbox" class="checkbox-round"
                                            value="Charger" name="model[DODGE][]">
                                        <label class="custom-control-label" for="model_dodge_charger">CHARGER
                                            (1)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Years & Mileage
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content  ">

                        <div class="mt-3 filter-select filter-mileage">
                            <label class="text-small text-muted form-label">Mileage</label>
                            <select name="mileage[lt]" class="custom-select pt-4 form-select"
                                fdprocessedid="moeh1o">
                                <option value="">Any</option>
                                <option value="20000">20,000 or less (1)</option>
                                <option value="30000">30,000 or less (2)</option>
                                <option value="40000">40,000 or less (3)</option>
                                <option value="50000">50,000 or less (5)</option>
                                <option value="60000">60,000 or less (8)</option>
                                <option value="70000">70,000 or less (12)</option>
                                <option value="80000">80,000 or less (14)</option>
                                <option value="90000">90,000 or less (17)</option>
                                <option value="100000">100,000 or less (22)</option>
                                <option value="Over 100000">Over 100,000 (9)</option>
                            </select>
                        </div>

                        <div class="my-3 row flex-nowrap" bis_skin_checked="1">
                            <div class="col" bis_skin_checked="1">
                                <div class="m-0 filter-select" bis_skin_checked="1"><label
                                        class="text-small text-muted form-label">Min
                                        Year</label><select data-cy="formcontrol-minyear" name="year[gt]"
                                        class="custom-select form-select" fdprocessedid="c9i6z">
                                        <option value="0">Oldest</option>
                                        <option value="2012">2012</option>
                                        <option value="2013">2013</option>
                                        <option value="2014">2014</option>
                                        <option value="2015">2015</option>
                                        <option value="2016">2016</option>
                                        <option value="2017">2017</option>
                                        <option value="2018">2018</option>
                                        <option value="2019">2019</option>
                                        <option value="2020">2020</option>
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                    </select></div>
                            </div>
                            <div class="px-0 text-muted text-small text-center pt-3 col-1" bis_skin_checked="1">to
                            </div>
                            <div class="col" bis_skin_checked="1">
                                <div class="m-0 filter-select" bis_skin_checked="1"><label
                                        class="text-small text-muted form-label">Max
                                        Year</label><select data-cy="formcontrol-maxyear" name="year[lt]"
                                        class="custom-select form-select" fdprocessedid="fcl1v7">
                                        <option value="5000">Newest</option>
                                        <option value="2012">2012</option>
                                        <option value="2013">2013</option>
                                        <option value="2014">2014</option>
                                        <option value="2015">2015</option>
                                        <option value="2016">2016</option>
                                        <option value="2017">2017</option>
                                        <option value="2018">2018</option>
                                        <option value="2019">2019</option>
                                        <option value="2020">2020</option>
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                    </select></div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Body Style
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Cargo Van (3)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Convertible (2)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Coupe (1)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Hatchback (3)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Pickup Truck (12)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Sedan (24)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    SUV (22)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Van (2)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Wagon (1)
                                </label>
                            </div>
                        </div>



                    </div>
                </div>


                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Features
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Adaptive Cruise Control (24)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Android Auto (31)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Apple CarPlay (31)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Automatic Climate Control (40)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Backup Camera (47)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Blind Spot Monitor (28)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Bluetooth (52)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Captain Seats (1)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Collision Warning (37)
                                </label>
                            </div>
                        </div>


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Cooled Seats (3)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Cross Traffic Alert (24)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Fog Lights (17)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Hands-free Liftgate (3)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Head Up Display (1)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Heated Seats (26)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Heated Steering Wheel (7)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Keyless Entry (32)
                                </label>
                            </div>
                        </div>


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Lane Departure Warning (34)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Lane Keep Assist (26)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Leather Seats (14)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Memory Seats (9)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Navigation (12)
                                </label>
                            </div>
                        </div>


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Parking Sensors/assist (1)
                                </label>
                            </div>
                        </div>


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Power Seats (18)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Premium Audio (7)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Push Start (37)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Rain Sensing Wipers (16)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Rear A/C (6)
                                </label>
                            </div>
                        </div>


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Rear Heated Seats (2)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Remote Engine Start (20)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Satellite Radio Ready (44)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Side Impact Airbags (52)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Smart Device Remote Start (1)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Sunroof/moonroof (18)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Third Row Seat (2)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Tow Package (2)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Wifi Hotspot (16)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Wireless Phone Charging (10)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Xenon Headlights (3)
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Seating Capacity
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    4 (4)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    5 (46)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    7 (1)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    8 (1)
                                </label>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Exterior Color
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    <span class="colorIndicator text-large bg-blue"></span>
                                    Blue (1)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    <span class="colorIndicator text-large bg-red"></span>
                                    Red (1)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    <span class="colorIndicator text-large bg-silver"></span>
                                    Silver (2)
                                </label>
                            </div>
                        </div>




                    </div>
                </div>


                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Interior Color
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    <span class="colorIndicator text-large bg-black"></span>
                                    Black (1)
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Fuel Type
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Gasoline (4)
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Transmission
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Automatic (3)
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Drivetrain
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    FWD (1)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    RWD (3)
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer filter-dropdown">

                    <div class="dropdown-toggle-btn cursor-pointer py-1">
                        Engine
                        <span class="dropdown-icon float-end text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="currentColor">
                                <path
                                    d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z" />
                            </svg>
                        </span>
                    </div>

                    <div class="dropdown-content ">


                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    3.6L V6 323hp 278ft. Lbs. (1)
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    EcoBoost 2.3L Turbo I4 315hp 350ft. Lbs. (2)
                                </label>
                            </div>
                        </div>

                        <div class="mt-2 make-item">
                            <div class="custom-control custom-checkbox">
                                <input id="make_audi" class="make-checkbox checkbox-round" type="checkbox"
                                    value="AUDI" name="make[]">
                                <label class="custom-control-label" for="make_audi">
                                    Ecotec 1.4L I4 98hp 94ft. Lbs. (1)
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>