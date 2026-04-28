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
            @if(array_filter($filters ?? []))
                <a href="javascript:void(0)" onclick="window.location.href = window.location.pathname"
                    class="font-weight-normal text-12 cursor-pointer text-primary clear-filters-btn">
                    Clear Filters
                </a>
            @endif
        </div>

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

            {{-- Histogram Mockup --}}
            <div class="histogram-container mb-2">
                <div class="d-flex align-items-end justify-content-between h-40px px-1">
                    <div class="bg-primary opacity-25" style="height: 20%; width: 8%;"></div>
                    <div class="bg-primary opacity-25" style="height: 40%; width: 8%;"></div>
                    <div class="bg-primary opacity-75" style="height: 80%; width: 8%;"></div>
                    <div class="bg-primary opacity-75" style="height: 100%; width: 8%;"></div>
                    <div class="bg-primary opacity-75" style="height: 70%; width: 8%;"></div>
                    <div class="bg-primary opacity-75" style="height: 90%; width: 8%;"></div>
                    <div class="bg-primary opacity-25" style="height: 30%; width: 8%;"></div>
                    <div class="bg-primary opacity-25" style="height: 15%; width: 8%;"></div>
                    <div class="bg-primary opacity-25" style="height: 25%; width: 8%;"></div>
                    <div class="bg-primary opacity-25" style="height: 10%; width: 8%;"></div>
                </div>
            </div>

            <div class="opacity-100">
                @php
                    $minPrice  = (int)($filterData['priceRange']->min_price ?? 0);
                    $maxPrice  = (int)($filterData['priceRange']->max_price ?? 100000);
                    $activeMin = request()->input('price.gt', $minPrice);
                    $activeMax = request()->input('price.lt', $maxPrice);
                @endphp
                <div class="slider-wrapper mt-0 mb-4">
                    <div class="slider-track price-financing-slider">
                        <div class="slider-handle" id="handle-min" tabindex="0"
                            aria-valuemax="{{ $maxPrice }}" aria-valuemin="{{ $minPrice }}"
                            aria-valuenow="{{ $activeMin }}" draggable="false" role="slider">
                        </div>
                        <div class="slider-handle slider-handle-top" id="handle-max" tabindex="0"
                            aria-valuemax="{{ $maxPrice }}" aria-valuemin="{{ $minPrice }}"
                            aria-valuenow="{{ $activeMax }}" draggable="false" role="slider">
                        </div>
                    </div>
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

            <div class="bg-light rounded p-3 mb-3 text-center border">
                <div class="small fw-bold mb-1">60 months @ 7.99% APR</div>
                <a href="javascript:void(0)" class="text-primary small text-decoration-none border-top d-block mt-2 pt-2">Adjust Terms</a>
            </div>

            <input type="hidden" id="minprice" name="price[gt]" value="{{ $activeMin }}">
            <input type="hidden" id="maxprice" name="price[lt]" value="{{ $activeMax }}">
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
        @if($filterData['bodyStyles']->count())
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
        @endif

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
