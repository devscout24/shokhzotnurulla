{{--
    Dynamic Filter Sidebar
    File: resources/views/frontend/partials/filter-sidebar.blade.php
    Variables: $filterData, $filters (active filters), $total
    Note: No inline <script> — all JS handled by inventory-listing.js
--}}
<div class="mb-5 mt-3 mt-md-0 notranslate filterCard card">
    <div class="pt-3 pb-2 bg-white card-header border-bottom">
        <div class="card-title h6 font-weight-bold mb-3 d-flex justify-content-between align-items-center">
            <span>{{ $total }} matches</span>
        </div>
        @if(array_filter($filters ?? []))
            <a href="{{ request()->url() }}"
                class="btn btn-outline-danger btn-sm w-100 clear-filters-btn">
                <i class="fa-solid fa-xmark me-1"></i> Clear All Filters
            </a>
        @endif

        {{-- Active filter badges --}}
        <div class="filter-badges d-flex flex-wrap" id="filter-badges">
            @foreach($filters ?? [] as $key => $value)
                @if(!empty($value))
                    @foreach((array)$value as $v)
                        <div class="filter-chip d-flex align-items-center bg-light border rounded-pill px-2 py-1 me-2 mb-2 cursor-pointer"
                             data-filter-key="{{ $key }}" data-filter-val="{{ $v }}">
                            <span class="text-xs text-muted me-2">{{ $v }}</span>
                            <i class="fa-solid fa-circle-xmark text-primary" style="font-size: 14px;"></i>
                        </div>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>

    <form method="GET" action="{{ request()->url() }}" id="inventory-filter-form" class="pb-0 mt-sm-0">

        {{-- ── Price / Financing ── --}}
        <div class="price-financing card-footer border-0 pb-0">
            <div class="h6 font-weight-bold mb-3">Price & Financing</div>

            <div class="mb-3">
                <small class="text-muted mb-2 d-block">Shop by</small>
                <div class="btn-group w-100 price-payment-toggle" role="group">
                    <input type="radio" class="btn-check" name="shop_by" id="shop_price" checked>
                    <label class="btn btn-outline-primary btn-sm py-2" for="shop_price">Price</label>
                    <input type="radio" class="btn-check" name="shop_by" id="shop_payment">
                    <label class="btn btn-outline-primary btn-sm py-2" for="shop_payment">Payment</label>
                </div>
            </div>

            @php
                $minPrice  = (int)($filterData['priceRange']->min_price ?? 0);
                $maxPrice  = (int)($filterData['priceRange']->max_price ?? 100000);
                $activeMin = request()->input('price.gt', $minPrice);
                $activeMax = request()->input('price.lt', $maxPrice);

                $sidebarDefaultRate = ($interestRates ?? collect())->first(function ($rate) {
                    $termMatches = ($rate->min_term === null || 60 >= $rate->min_term)
                        && ($rate->max_term === null || 60 <= $rate->max_term);
                    $creditMatches = ($rate->min_credit_score === null || 740 >= $rate->min_credit_score)
                        && ($rate->max_credit_score === null || 740 <= $rate->max_credit_score);
                    return $termMatches && $creditMatches;
                });
                $sidebarRate = $sidebarDefaultRate ? (float) $sidebarDefaultRate->rate : 7.99;

                $monthlyRate = ($sidebarRate / 100) / 12;
                $minMonthly = $monthlyRate > 0
                    ? round($minPrice * $monthlyRate / (1 - pow(1 + $monthlyRate, -60)), 0)
                    : round($minPrice / 60, 0);
                $maxMonthly = $monthlyRate > 0
                    ? round($maxPrice * $monthlyRate / (1 - pow(1 + $monthlyRate, -60)), 0)
                    : round($maxPrice / 60, 0);
                $activeMinPayment = (int) request()->input('payment.gt', $minMonthly);
                $activeMaxPayment = (int) request()->input('payment.lt', $maxMonthly);
            @endphp

            {{-- Histogram ─ bars update via JS based on slider position --}}
            <div class="histogram-container mb-2" id="price-histogram">
                <div class="d-flex align-items-end justify-content-between h-40px px-1" id="histogram-bars">
                    <div class="histogram-bar" data-bar-idx="0" style="height: 20%;"></div>
                    <div class="histogram-bar" data-bar-idx="1" style="height: 40%;"></div>
                    <div class="histogram-bar" data-bar-idx="2" style="height: 80%;"></div>
                    <div class="histogram-bar" data-bar-idx="3" style="height: 100%;"></div>
                    <div class="histogram-bar" data-bar-idx="4" style="height: 70%;"></div>
                    <div class="histogram-bar" data-bar-idx="5" style="height: 90%;"></div>
                    <div class="histogram-bar" data-bar-idx="6" style="height: 30%;"></div>
                    <div class="histogram-bar" data-bar-idx="7" style="height: 15%;"></div>
                    <div class="histogram-bar" data-bar-idx="8" style="height: 25%;"></div>
                    <div class="histogram-bar" data-bar-idx="9" style="height: 10%;"></div>
                </div>
            </div>

            {{-- ── Price tab ── --}}
            <div class="price-tab-panel" id="tab-price">
                <div class="dual-range-wrapper mt-0 mb-3">
                    <div class="dual-range-track" id="dual-range-track"></div>
                    <input type="range" class="dual-range-input" id="price-range-min"
                        min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $activeMin }}" step="100">
                    <input type="range" class="dual-range-input" id="price-range-max"
                        min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $activeMax }}" step="100">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Min</small>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0">$</span>
                            <input type="text" class="form-control border-start-0 ps-0" name="price-display-min"
                                value="{{ number_format($activeMin) }}" inputmode="numeric">
                        </div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Max</small>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0">$</span>
                            <input type="text" class="form-control border-start-0 ps-0" name="price-display-max"
                                value="{{ number_format($activeMax) }}" inputmode="numeric">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Payment tab ── --}}
            <div class="price-tab-panel d-none" id="tab-payment">
                <div class="dual-range-wrapper mt-0 mb-3">
                    <div class="dual-range-track" id="payment-range-track"></div>
                    <input type="range" class="dual-range-input" id="payment-range-min"
                        min="{{ $minMonthly }}" max="{{ $maxMonthly }}" value="{{ $activeMinPayment }}" step="10">
                    <input type="range" class="dual-range-input" id="payment-range-max"
                        min="{{ $minMonthly }}" max="{{ $maxMonthly }}" value="{{ $activeMaxPayment }}" step="10">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Min /mo</small>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0">$</span>
                            <input type="text" class="form-control border-start-0 ps-0" name="payment-display-min"
                                value="{{ number_format($activeMinPayment) }}" inputmode="numeric">
                        </div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Max /mo</small>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0">$</span>
                            <input type="text" class="form-control border-start-0 ps-0" name="payment-display-max"
                                value="{{ number_format($activeMaxPayment) }}" inputmode="numeric">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-light rounded p-3 mb-3 text-center border">
                <div class="small fw-bold mb-1">60 months @ {{ number_format($sidebarRate, 2) }}% APR</div>
                <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#getEstimate"
                    data-vehicle-title="Estimated Payment" data-vehicle-price="{{ $activeMax }}" data-vehicle-monthly="0"
                    data-vehicle-rate="{{ $sidebarRate }}" data-vehicle-term="60" data-sidebar-link="1"
                    class="text-primary small text-decoration-none border-top d-block mt-2 pt-2">Adjust Terms</a>
            </div>

            <input type="hidden" id="minprice" name="price[gt]" value="{{ $activeMin }}">
            <input type="hidden" id="maxprice" name="price[lt]" value="{{ $activeMax }}">
            <input type="hidden" id="sidebar-rate" value="{{ $sidebarRate }}">
        </div>

        {{-- ── Make & Model ── --}}
        @if($filterData['makes']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Make & Model
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content max-280">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search make…">
                </div>
                @foreach($filterData['makes'] as $make)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="make_{{ Str::slug($make->make_name) }}"
                                class="make-checkbox checkbox-round"
                                type="checkbox" name="make[]"
                                value="{{ $make->make_name }}"
                                {{ in_array($make->make_name, (array)request()->input('make', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="make_{{ Str::slug($make->make_name) }}">
                                {{ $make->make_name }} ({{ $make->cnt }})
                            </label>
                        </div>

                        @if($filterData['models']->has($make->make_name))
                            <div class="model-list mb-3" style="display: none;">
                                <div class="text-muted pb-1 ps-4">Model</div>
                                @foreach($filterData['models'][$make->make_name] as $model)
                                    <div class="my-1 ps-4">
                                        <div class="custom-control custom-checkbox">
                                            <input id="model_{{ Str::slug($make->make_name . '_' . $model->model_name) }}"
                                                type="checkbox" class="checkbox-round"
                                                name="model[]" value="{{ $model->model_name }}"
                                                {{ in_array($model->model_name, (array)request()->input('model', [])) ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="model_{{ Str::slug($make->make_name . '_' . $model->model_name) }}">
                                                {{ $model->model_name }} ({{ $model->cnt }})
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Years & Mileage ── --}}
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Years & Mileage
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="mt-3 filter-select">
                    <label class="text-small text-muted form-label">Mileage</label>
                    <select name="mileage[lt]" class="custom-select pt-4 form-select">
                        <option value="">Any</option>
                        @foreach([20000,30000,40000,50000,60000,70000,80000,90000,100000] as $mi)
                            <option value="{{ $mi }}" {{ request()->input('mileage.lt') == $mi ? 'selected' : '' }}>
                                {{ number_format($mi) }} or less
                            </option>
                        @endforeach
                        <option value="Over 100000" {{ request()->input('mileage.lt') == 'Over 100000' ? 'selected' : '' }}>
                            Over 100,000
                        </option>
                    </select>
                </div>
                <div class="my-3 row flex-nowrap">
                    <div class="col">
                        <div class="m-0 filter-select">
                            <label class="text-small text-muted form-label">Min Year</label>
                            <select name="year[gt]" class="custom-select form-select">
                                <option value="0">Oldest</option>
                                @for($y = ($filterData['yearRange']->min_year ?? 2000); $y <= date('Y'); $y++)
                                    <option value="{{ $y }}" {{ request()->input('year.gt') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="px-0 text-muted text-small text-center pt-3 col-1">to</div>
                    <div class="col">
                        <div class="m-0 filter-select">
                            <label class="text-small text-muted form-label">Max Year</label>
                            <select name="year[lt]" class="custom-select form-select">
                                <option value="5000">Newest</option>
                                @for($y = ($filterData['yearRange']->max_year ?? date('Y')); $y >= ($filterData['yearRange']->min_year ?? 2000); $y--)
                                    <option value="{{ $y }}" {{ request()->input('year.lt') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Body Style ── --}}
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Body Style
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search body style…">
                </div>
                @foreach($filterData['bodyStyles'] as $style)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="body_{{ Str::slug($style->style_name) }}"
                                class="checkbox-round" type="checkbox"
                                name="body_style[]" value="{{ $style->style_name }}"
                                {{ in_array($style->style_name, (array)request()->input('body_style', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="body_{{ Str::slug($style->style_name) }}">
                                {{ $style->style_name }} ({{ $style->cnt }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Features ── --}}
        @if($filterData['features']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Features
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content max-280">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search features…">
                </div>
                @foreach($filterData['features'] as $feature)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="feature_{{ $feature->id }}"
                                class="checkbox-round" type="checkbox"
                                name="feature[]" value="{{ $feature->id }}"
                                {{ in_array($feature->id, (array)request()->input('feature', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="feature_{{ $feature->id }}">
                                {{ $feature->name }} ({{ $feature->vehicles_count }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Seating Capacity ── --}}
        @if($filterData['seating']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Seating Capacity
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search seating…">
                </div>
                @foreach($filterData['seating'] as $s)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="seat_{{ $s->seating_capacity }}"
                                class="checkbox-round" type="checkbox"
                                name="seating[]" value="{{ $s->seating_capacity }}"
                                {{ in_array($s->seating_capacity, (array)request()->input('seating', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="seat_{{ $s->seating_capacity }}">
                                {{ $s->seating_capacity }} seats ({{ $s->cnt }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Exterior Color ── --}}
        @if($filterData['colors']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Exterior Color
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search exterior color…">
                </div>
                @foreach($filterData['colors'] as $color)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="color_{{ Str::slug($color->color_name) }}"
                                class="checkbox-round" type="checkbox"
                                name="exterior_color[]" value="{{ $color->color_name }}"
                                {{ in_array($color->color_name, (array)request()->input('exterior_color', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="color_{{ Str::slug($color->color_name) }}">
                                <span class="colorIndicator" style="background-color: {{ strtolower($color->color_name) }};"></span>
                                {{ $color->color_name }} ({{ $color->cnt }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Interior Color ── --}}
        @if($filterData['interiorColors']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Interior Color
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search interior color…">
                </div>
                @foreach($filterData['interiorColors'] as $color)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="icolor_{{ Str::slug($color->color_name) }}"
                                class="checkbox-round" type="checkbox"
                                name="interior_color[]" value="{{ $color->color_name }}"
                                {{ in_array($color->color_name, (array)request()->input('interior_color', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="icolor_{{ Str::slug($color->color_name) }}">
                                <span class="colorIndicator" style="background-color: {{ strtolower($color->color_name) }};"></span>
                                {{ $color->color_name }} ({{ $color->cnt }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Fuel Type ── --}}
        @if($filterData['fuelTypes']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Fuel Type
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search fuel type…">
                </div>
                @foreach($filterData['fuelTypes'] as $f)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="fuel_{{ Str::slug($f->name) }}"
                                class="checkbox-round" type="checkbox"
                                name="fuel_type[]" value="{{ $f->name }}"
                                {{ in_array($f->name, (array)request()->input('fuel_type', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="fuel_{{ Str::slug($f->name) }}">
                                {{ $f->name }} ({{ $f->cnt }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Transmission ── --}}
        @if($filterData['transmissions']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Transmission
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search transmission…">
                </div>
                @foreach($filterData['transmissions'] as $t)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="trans_{{ Str::slug($t->name) }}"
                                class="checkbox-round" type="checkbox"
                                name="transmission[]" value="{{ $t->name }}"
                                {{ in_array($t->name, (array)request()->input('transmission', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="trans_{{ Str::slug($t->name) }}">
                                {{ $t->name }} ({{ $t->cnt }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Drivetrain ── --}}
        @if($filterData['drivetrains']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Drivetrain
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search drivetrain…">
                </div>
                @foreach($filterData['drivetrains'] as $d)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="drive_{{ Str::slug($d->name) }}"
                                class="checkbox-round" type="checkbox"
                                name="drivetrain[]" value="{{ $d->name }}"
                                {{ in_array($d->name, (array)request()->input('drivetrain', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="drive_{{ Str::slug($d->name) }}">
                                {{ $d->name }} ({{ $d->cnt }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Engine ── --}}
        @if($filterData['engines']->count())
        <div class="card-footer filter-dropdown">
            <div class="dropdown-toggle-btn cursor-pointer py-1">
                Engine
                <span class="dropdown-icon float-end text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M3.204 5.5a.5.5 0 0 1 .708 0L8 9.586 12.088 5.5a.5.5 0 1 1 .707.707l-4.442 4.442a.5.5 0 0 1-.707 0L3.204 6.207a.5.5 0 0 1 0-.707z"/>
                    </svg>
                </span>
            </div>
            <div class="dropdown-content">
                <div class="filter-search-wrap px-2 pt-2 pb-1">
                    <input type="text" class="filter-search form-control form-control-sm" placeholder="Search engine…">
                </div>
                @foreach($filterData['engines'] as $engine)
                    <div class="mt-2 make-item">
                        <div class="custom-control custom-checkbox">
                            <input id="engine_{{ Str::slug($engine->engine) }}"
                                class="checkbox-round" type="checkbox"
                                name="engine[]" value="{{ $engine->engine }}"
                                {{ in_array($engine->engine, (array)request()->input('engine', [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="engine_{{ Str::slug($engine->engine) }}">
                                {{ $engine->engine }} ({{ $engine->cnt }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- NO inline <script> here — all handled by inventory-listing.js --}}

    </form>
</div>
