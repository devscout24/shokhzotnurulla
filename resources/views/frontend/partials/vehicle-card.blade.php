@php
    $primary = $vehicle->relationLoaded('primaryPhoto') ? $vehicle->primaryPhoto : null;
    $rest = $vehicle->relationLoaded('photos') ? $vehicle->photos : collect();

    $displayPhotos = $primary
        ? collect([$primary])->concat($rest->where('id', '!=', $primary->id))
        : $rest;

    $totalCount = $vehicle->photos_count ?? $displayPhotos->count();

    // Pricing — use attached pricing or fallback to list_price
    $p = $vehicle->pricing ?? null;
    $finalPrice = $p ? $p['final_price'] : (float) $vehicle->list_price;
    $origPrice = $p ? $p['display_original'] : (float) ($vehicle->original_price ?? 0);
    $savings = $p ? $p['savings'] : 0.0;
    $monthly = $p ? $p['monthly'] : 0.0;
    $hasDiscount = $p ? $p['has_discount'] : ($origPrice > $finalPrice);
    $isFormfill = $p ? $p['is_formfill'] : false;
    $isSpecialFin = $p ? $p['is_special_finance'] : false;
    $btnText = $p ? $p['button_text'] : null;

    if (!$monthly && $finalPrice > 0) {
        $r = (6.79 / 100) / 12;
        $monthly = ($finalPrice * $r) / (1 - pow(1 + $r, -60));
    }
@endphp

<div class="srp-cardcontainer mb-3 {{ $isFormfill ? 'ePrice-locked' : '' }} px-2
    make_{{ Str::slug($vehicle->make?->name ?? '') }}
    {{ $hasDiscount ? 'has-discount' : 'no-discount' }}
    col-lg-3 col-md-4 col-sm-4 col-12">

    <div class="srp-card overflow-hidden h-100 conditionUsed card">
        <div class="px-0 pt-0 pb-0 card-body">
            <div class="new-arrival position-relative border-bottom">

                @if($vehicle->is_spotlight || $vehicle->featured)
                    <div class="bg-danger text-white small py-1 px-3 pill-badge font-weight-bold vc-popular">
                        <span class="d-inline-block me-1">
                            <i class="fa-solid fa-fire-flame-curved text-white" style="font-size:14px;"></i>
                        </span>
                        Hot
                    </div>
                @endif
                <div class="position-relative cursor-pointer">
                    <div class="img-srp-container" onclick="window.location.href='{{ route('frontend.inventory.show', $vehicle->slug) }}'">

                        <div class="sc-dba2d435-2 deAfgc">
                            <div class="toggle left-toggle" onclick="event.stopPropagation()">
                                <span class="d-inline-block h2 m-0">
                                    <i class="fa-solid fa-angle-left white-text-29"></i>
                                </span>
                            </div>
                            <div class="toggle right-toggle" onclick="event.stopPropagation()">
                                <span class="d-inline-block h2 m-0">
                                    <i class="fa-solid fa-angle-right white-text-29"></i>
                                </span>
                            </div>
                        </div>

                        <div class="sc-dba2d435-1 jcwltT top-15">
                            @foreach($displayPhotos as $photo)
                                <span class="d-inline-block me-1 circle-dot-icon {{ $loop->first ? 'active' : '' }}">
                                    <i class="fa-solid fa-circle-dot"></i>
                                </span>
                            @endforeach
                        </div>

                        @forelse($displayPhotos as $photo)
                            <img src="{{ $photo->url }}" class="img-srp {{ $loop->first ? 'd-block' : 'd-none' }}"
                                alt="{{ $vehicle->display_title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                        @empty
                            <img src="{{ asset('assets/frontend/img/no-photo.webp') }}" class="img-srp d-block"
                                alt="{{ $vehicle->display_title }}" loading="lazy">
                        @endforelse

                        <div id="has-more-photos" class="sc-dba2d435-0 QeHlJ" bis_skin_checked="1" onclick="event.stopPropagation()">
                                <div class="w-75 mx-auto mt-n4" bis_skin_checked="1">
                                    <button type="button" class="w-100 text-white border-white btn btn-default btn-lg">
                                        View all {{ $totalCount }} photos
                                    </button>
                                    <a role="button" tabindex="0" target="_self" title="Apply online"
                                        class="mt-4 w-100 btn btn-primary btn-lg"
                                        href="{{ route('frontend.inventory.show', $vehicle->slug) }}">
                                        Apply online
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- Card info --}}
            <div class="px-3 pt-3 pb-0">
                <div class="d-flex justify-content-between">
                    <small class="opacity-75 srp-stocknum">Stock # {{ $vehicle->stock_number }}</small>
                </div>

                <div class="no-gutters mt-1 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-truncate me-2">
                            <a href="{{ route('frontend.inventory.show', $vehicle->slug) }}">
                                <h2 class="h5 m-0 font-weight-bold text-truncate notranslate">
                                    {{ $vehicle->year }} {{ $vehicle->make?->name }} {{ $vehicle->makeModel?->name }}
                                </h2>
                            </a>
                        </div>
                        <div class="flex-shrink-0">
                            <span data-cy="btn-favorite" class="d-inline-block h4 cursor-pointer mb-0 favorite-icon">
                                <i class="fa-solid fa-heart greyIcon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-1 align-items-center srp-miles opacity-75">
                        <div class="text-truncate h-6">{{ $vehicle->trim }}</div>
                        <div class="ps-2 text-nowrap ms-auto text-end">
                            {{ $vehicle->mileage ? number_format($vehicle->mileage) . ' miles' : '—' }}
                        </div>
                    </div>
                </div>

                {{-- Price section --}}
                <div class="d-flex align-items-center mb-3 border-top pt-2 srpPriceContainer">
                    <div class="font-weight-bold w-100">

                        {{-- Final price or e-Price button --}}
                        @if($isFormfill)
                            <span class="h5 font-weight-bold label-price text-muted"
                                style="filter:blur(4px);user-select:none;">
                                ${{ number_format($finalPrice) }}
                            </span>
                            <div class="mt-1">
                                <button type="button" class="btn btn-sm btn-pill-all w-100 btn-unlock-price"
                                    data-bs-toggle="offcanvas" data-bs-target="#unlockEPrice"
                                    data-vehicle-id="{{ $vehicle->id }}"
                                    data-vehicle-title="{{ $vehicle->year }} {{ $vehicle->make?->name }} {{ $vehicle->makeModel?->name }}"
                                    style="font-size:11px;">
                                    <i class="fa-solid fa-lock me-1"></i>
                                    {{ $btnText ?: 'Unlock e-Price' }}
                                </button>
                            </div>
                        @elseif($isSpecialFin)
                            <span class="h4 font-weight-bold mt-1 label-price">
                                ${{ number_format($finalPrice) }}
                            </span>
                            @if($p && $p['applied_special']?->finance_rate)
                                <div class="mt-1">
                                    <span class="badge bg-success text-white" style="font-size:11px;">
                                        {{ $p['applied_special']->finance_rate }}% APR
                                        @if($p['applied_special']->finance_term)
                                            / {{ $p['applied_special']->finance_term }} mo.
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    @if($hasDiscount && $origPrice > $finalPrice)
                                        <div class="text-muted small mb-n1">
                                            was ${{ number_format($origPrice) }}
                                            <span class="text-success ms-1">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </span>
                                        </div>
                                    @endif
                                    <span class="h4 font-weight-bold mt-1 label-price">
                                        ${{ number_format($finalPrice) }}
                                    </span>
                                </div>
                                
                                {{-- Monthly payment --}}
                                @if(!$isFormfill)
                                    <div class="text-end text-nowrap ms-auto my-1">
                                        <span class="cursor-pointer" role="button">
                                            <small class="opacity-75">Est. Payment</small><br>
                                            <span class="fw-bold">${{ number_format($monthly) }}/mo</span>
                                            <span class="d-inline-block faIcon ms-1 text-primary">
                                                <i class="fa-solid fa-pen-to-square" style="font-size: 14px;"></i>
                                            </span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>

                </div>
            </div>

            <div class="px-2 pb-2 mt-n2">
                <button class="btn btn-estimate-payment w-100 py-2 font-weight-bold">
                    Estimate payment
                    <div style="font-size: 10px; font-weight: normal; opacity: 0.9;">No impact to your credit score</div>
                </button>
            </div>
        </div>
    </div>
</div>