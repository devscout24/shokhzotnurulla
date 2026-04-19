@extends('layouts.dealer.app')
@section('title', __('Inventory Details') . ' | ' . __(config('app.name')))

@push('page-assets')
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    @vite([
        'resources/css/dealer/pages/inventory-details.css',
        'resources/js/dealer/pages/inventory-details.js',
    ])
@endpush

@php
    $vehicleImgSrc = $vehicle->primaryPhoto
        ? $vehicle->primaryPhoto->url
        : 'https://placehold.co/400x250/e8e8e8/999?text=' . urlencode($vehicle->year . ' ' . $vehicle->makeModel->name);

    $vehicleTitle = strtoupper(
        $vehicle->year . ' ' .
        $vehicle->make->name . ' ' .
        $vehicle->makeModel->name .
        ($vehicle->trim ? ' ' . $vehicle->trim : '')
    );
@endphp

@section('page-content')
<main class="main-content" id="mainContent" style="padding:0;overflow:hidden;">
    <div class="view-content inventory-view" data-view="inventory" style="padding:0;">

        @include('dealer.partials.inventory-topbar')

        <div class="subview vd-page"
            data-vehicle-id="{{ $vehicle->id }}"
            data-url-models="{{ route('dealer.inventory.models') }}"
            data-url-pricing="{{ route('dealer.inventory.vdp.pricing', $vehicle) }}"
            data-url-details="{{ route('dealer.inventory.vdp.details', $vehicle) }}"
            data-url-tags="{{ route('dealer.inventory.vdp.tags', $vehicle) }}"
            data-url-notes="{{ route('dealer.inventory.vdp.notes', $vehicle) }}"
            data-url-status="{{ route('dealer.inventory.vdp.status', $vehicle) }}"
            data-url-destroy="{{ route('dealer.inventory.vdp.destroy', $vehicle) }}"
            data-url-video="{{ route('dealer.inventory.vdp.video', $vehicle) }}"
            data-url-premium-options="{{ route('dealer.inventory.vdp.premium-options.store', $vehicle) }}"
            data-url-premium-option-base="{{ route('dealer.inventory.vdp.premium-options.destroy', [$vehicle, '__ID__']) }}"
            data-url-incentive-hide-base="{{ route('dealer.inventory.vdp.incentive.hide', [$vehicle, '__ID__']) }}"
            data-url-incentive-unhide-base="{{ route('dealer.inventory.vdp.incentive.unhide', [$vehicle, '__ID__']) }}">

            {{-- ═══════════════════
                 TOP HEADER
            ═══════════════════ --}}
            <div class="vd-header">
                <div class="vd-header-title">
                    {{ $vehicleTitle }}
                    <span>({{ $vehicle->stock_number }})</span>
                </div>
                <div class="vd-header-actions">
                    <button type="button" id="view-listing-btn" class="vd-btn-view-listing" data-listing-url="{{ route('frontend.inventory.show', $vehicle->slug) }}">
                        <i class="bi bi-box-arrow-up-right"></i> View Listing
                    </button>
                    <button type="button" class="vd-btn-remove-listing" id="btnRemoveListing">
                        <i class="bi bi-trash3"></i> Remove Listing
                    </button>
                </div>
            </div>

            {{-- ═══════════════════
                 3-COLUMN BODY
            ═══════════════════ --}}
            <div class="vd-body">

                {{-- ════════════
                     LEFT PANEL
                ════════════ --}}
                @include('dealer.partials.inventory-details-sidebar')

                {{-- ════════════════
                     MIDDLE PANEL
                ════════════════ --}}
                <div class="vd-middle">

                    {{-- Tab bar --}}
                    <div class="vd-tabs">
                        <button class="vd-tab active" data-tab="pricing">
                            <i class="bi bi-tag"></i> Pricing
                        </button>
                        <button class="vd-tab" data-tab="edit-details">
                            <i class="bi bi-pencil-square"></i> Edit Details
                        </button>
                        <button class="vd-tab" data-tab="tags">
                            <i class="bi bi-bookmark"></i> Tags
                        </button>
                        <button class="vd-tab" data-tab="internal-notes">
                            <i class="bi bi-chat-left-text"></i> Internal Notes
                        </button>
                        <div class="vd-tab-nav">
                            <button class="vd-tab-arrow"><i class="bi bi-chevron-left"></i></button>
                            <button class="vd-tab-arrow"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                    <hr>
                    {{-- Tab content --}}
                    <div class="vd-tab-content">

                        {{-- ══════════════════════════
                             PRICING TAB
                        ══════════════════════════ --}}
                        <div class="vd-tab-pane active" id="tab-pricing">
                            <form id="formPricing" data-ajax-url="{{ route('dealer.inventory.vdp.pricing', $vehicle) }}" autocomplete="off">
                                @csrf
                                <input type="hidden" name="_method" value="PATCH">
                                <input type="hidden" name="pricing_disclaimer" id="hiddenPricingDisclaimer"
                                       value="{{ $vehicle->prices->pricing_disclaimer ?? '' }}">

                                {{-- List / MSRP / Dealer Cost --}}
                                <div class="vd-price-row">
                                    <div class="vd-price-field">
                                        <div class="vd-field-label">List Price <span class="req">*</span></div>
                                        <div class="vd-price-input-wrap">
                                            <span class="vd-price-sym">$</span>
                                            <input type="text" name="list_price"
                                                   value="{{ $vehicle->list_price ? number_format($vehicle->list_price, 2) : '' }}"
                                                   class="vd-numeric"
                                                   placeholder="__,___">
                                        </div>
                                    </div>
                                    <div class="vd-price-field">
                                        <div class="vd-field-label">MSRP</div>
                                        <div class="vd-price-input-wrap">
                                            <span class="vd-price-sym">$</span>
                                            <input type="text" name="msrp"
                                                   value="{{ $vehicle->prices->msrp ? number_format($vehicle->prices->msrp, 2) : '' }}"
                                                   class="vd-numeric"
                                                   placeholder="__,___">
                                        </div>
                                    </div>
                                    <div class="vd-price-field">
                                        <div class="vd-field-label">Dealer Cost</div>
                                        <div class="vd-price-input-wrap">
                                            <span class="vd-price-sym">$</span>
                                            <input type="text" name="dealer_cost"
                                                   value="{{ $vehicle->prices->dealer_cost ? number_format($vehicle->prices->dealer_cost, 2) : '' }}"
                                                   class="vd-numeric"
                                                   placeholder="__,___">
                                        </div>
                                    </div>
                                </div>

                                {{-- Pricing Disclaimer --}}
                                <div class="vd-field-label" style="margin-bottom:8px;">Pricing Disclaimer</div>
                                <div class="vd-rte-wrap" id="rteWrapPricing">
                                    <div id="rtePricing"
                                         data-quill-hidden="hiddenPricingDisclaimer"
                                         style="min-height:80px;">{!! $vehicle->prices->pricing_disclaimer ?? '' !!}</div>
                                </div>

                                <hr class="vd-divider">

                                {{-- Sold Information --}}
                                <div class="vd-section-title">Sold Information</div>
                                <p class="vd-section-desc">Special pricing can be used to apply custom labels like "Ford employee discount" to override the listing price. Add-on pricing can be used to show premium upgrades like wheels or tow packages, lift kits, etc.</p>

                                <div class="vd-sold-row">
                                    <div class="vd-sold-field">
                                        <div class="vd-field-label">Sold Price</div>
                                        <div class="vd-price-input-wrap">
                                            <span class="vd-price-sym">$</span>
                                            <input type="text" name="sold_price"
                                                   value="{{ $vehicle->prices->sold_price ? number_format($vehicle->prices->sold_price, 2) : '' }}"
                                                   class="vd-numeric"
                                                   placeholder="__,___">
                                        </div>
                                    </div>
                                    <div class="vd-sold-field">
                                        <div class="vd-field-label">Sold Date</div>
                                        <div class="vd-date-wrap">
                                            <span class="vd-date-icon"><i class="bi bi-calendar3"></i></span>
                                            <input type="date" name="sold_date"
                                                   value="{{ $vehicle->prices->sold_date ? \Carbon\Carbon::parse($vehicle->prices->sold_date)->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>
                                    <div class="vd-sold-field">
                                        <div class="vd-field-label">Customer Sold To</div>
                                        <input type="text" name="sold_to" class="vd-input"
                                               id="inputSoldTo"
                                               value="{{ $vehicle->prices->sold_to ?? '' }}"
                                               placeholder="Enter customer name">
                                        <button type="button" class="vd-customer-btn" id="btnSelectCustomer" style="margin-top:4px;">
                                            <i class="bi bi-person-plus" style="font-size:14px;"></i>
                                            {{ $vehicle->prices->sold_to ? $vehicle->prices->sold_to : 'Select Customer' }}
                                        </button>
                                    </div>
                                </div>

                                <hr class="vd-divider">

                                {{-- Add-ons --}}
                                <div class="vd-section-title">Add-ons &amp; employee/special discounts</div>
                                <p class="vd-section-desc">Special pricing can be used to apply custom labels like "Ford employee discount" to override the listing price. Add-on pricing can be used to show premium upgrades like wheels or tow packages, lift kits, etc.</p>

                                <div class="vd-addon-row">
                                    <div>
                                        <div class="vd-field-label">Special Price</div>
                                        <div class="vd-price-input-wrap">
                                            <span class="vd-price-sym">$</span>
                                            <input type="text" name="special_price"
                                                   value="{{ $vehicle->prices->special_price ? number_format($vehicle->prices->special_price, 2) : '' }}"
                                                   class="vd-numeric"
                                                   placeholder="__,___">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="vd-field-label">Special Price Label</div>
                                        <input type="text" name="special_price_label" class="vd-input"
                                               value="{{ $vehicle->prices->special_price_label ?? '' }}"
                                               placeholder="Enter some text">
                                    </div>
                                    <div>
                                        <div class="vd-field-label">Add-on Price</div>
                                        <div class="vd-price-input-wrap">
                                            <span class="vd-price-sym">$</span>
                                            <input type="text" name="addon_price"
                                                   value="{{ $vehicle->prices->addon_price ? number_format($vehicle->prices->addon_price, 2) : '' }}"
                                                   class="vd-numeric"
                                                   placeholder="__,___">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="vd-field-label">Add-on Price Label</div>
                                        <input type="text" name="addon_price_label" class="vd-input"
                                               value="{{ $vehicle->prices->addon_price_label ?? '' }}"
                                               placeholder="Enter some text">
                                    </div>
                                </div>

                                <p class="vd-section-desc">Adjustment Label can be used to change the label of the line item on pricing that appears when the <strong>MSRP</strong> is lower than the <strong>List Price</strong>.</p>

                                <div style="max-width:220px;margin-bottom:20px;">
                                    <div class="vd-field-label">Adjustment Label</div>
                                    <input type="text" name="adjustment_label" class="vd-input"
                                           value="{{ $vehicle->prices->adjustment_label ?? '' }}"
                                           placeholder="Enter some text">
                                </div>

                                <hr class="vd-divider">

                                {{-- Third-party pricing --}}
                                <div class="vd-section-title">Third-party / syndication pricing</div>
                                <p class="vd-section-desc">For syndication purposes, internet price can be set for feeds we syndicate to Cars, Autotrader, etc. Internet price will not override the <strong>List Price</strong> displayed on your website.</p>

                                <div class="vd-price-row" style="max-width:540px;">
                                    <div class="vd-price-field">
                                        <div class="vd-field-label">Internet Price</div>
                                        <div class="vd-price-input-wrap">
                                            <span class="vd-price-sym">$</span>
                                            <input type="text" name="internet_price"
                                                   value="{{ $vehicle->prices->internet_price ? number_format($vehicle->prices->internet_price, 2) : '' }}"
                                                   class="vd-numeric"
                                                   placeholder="__,___">
                                        </div>
                                    </div>
                                    <div class="vd-price-field">
                                        <div class="vd-field-label">Asking Price</div>
                                        <div class="vd-price-input-wrap">
                                            <span class="vd-price-sym">$</span>
                                            <input type="text" name="asking_price"
                                                   value="{{ $vehicle->prices->asking_price ? number_format($vehicle->prices->asking_price, 2) : '' }}"
                                                   class="vd-numeric"
                                                   placeholder="__,___">
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="vd-btn-save" data-form="formPricing">
                                    <i class="bi bi-check-lg"></i> Save
                                </button>
                            </form>
                        </div>
                        {{-- end pricing --}}

                        {{-- ══════════════════════════
                             EDIT DETAILS TAB
                        ══════════════════════════ --}}
                        <div class="vd-tab-pane" id="tab-edit-details">
                            <form id="formDetails" data-ajax-url="{{ route('dealer.inventory.vdp.details', $vehicle) }}" autocomplete="off">
                                @csrf
                                <input type="hidden" name="_method" value="PATCH">

                                {{-- Jump-to sub-nav --}}
                                <div class="vd-jumpto">
                                    <span class="vd-jumpto-label">Jump to:</span>
                                    <a href="#det-basics" class="vd-jumpto-item">
                                        <i class="bi bi-car-front"></i> Basics
                                    </a>
                                    <a href="#det-powertrain" class="vd-jumpto-item">
                                        <i class="bi bi-cpu"></i> Powertrain
                                    </a>
                                    <a href="#det-fuel" class="vd-jumpto-item">
                                        <i class="bi bi-fuel-pump"></i> Fuel / Battery
                                    </a>
                                    <a href="#det-dimensions" class="vd-jumpto-item">
                                        <i class="bi bi-rulers"></i> Dimensions &amp; sizes
                                    </a>
                                </div>

                                {{-- ── Basics ── --}}
                                <div id="det-basics" class="vd-det-section">
                                    <i class="bi bi-car-front-fill"></i> Basics
                                </div>
                                <div class="vd-det-grid">
                                    <div class="vd-det-field vd-det-field-span2">
                                        <label class="vd-field-label">VIN</label>
                                        <input type="text" name="vin" class="vd-input"
                                               value="{{ $vehicle->vin }}" maxlength="17">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Stock # <span class="req">*</span></label>
                                        <input type="text" name="stock_number" class="vd-input"
                                               value="{{ $vehicle->stock_number }}" required>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Certified Pre-owned?</label>
                                        <select name="is_certified" class="vd-select">
                                            <option value="0" {{ !$vehicle->is_certified ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ $vehicle->is_certified ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Condition <span class="req">*</span></label>
                                        <select name="vehicle_condition" class="vd-select" required>
                                            <option value="Used" {{ $vehicle->vehicle_condition === 'Used' ? 'selected' : '' }}>Used</option>
                                            <option value="New" {{ $vehicle->vehicle_condition === 'New' ? 'selected' : '' }}>New</option>
                                            <option value="Certified Pre-Owned" {{ $vehicle->vehicle_condition === 'Certified Pre-Owned' ? 'selected' : '' }}>Certified Pre-Owned</option>
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">On lot / In-transit</label>
                                        <select name="location_status" class="vd-select">
                                            <option value="lot" {{ $vehicle->location_status === 'lot' ? 'selected' : '' }}>On lot</option>
                                            <option value="transit" {{ $vehicle->location_status === 'transit' ? 'selected' : '' }}>In-transit</option>
                                            <option value="order" {{ $vehicle->location_status === 'order' ? 'selected' : '' }}>Order</option>
                                            <option value="preorder" {{ $vehicle->location_status === 'preorder' ? 'selected' : '' }}>Pre-order</option>
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Year <span class="req">*</span></label>
                                        <input type="number" name="year" class="vd-input"
                                               value="{{ $vehicle->year }}"
                                               min="1900" max="{{ date('Y') + 2 }}" required>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Make <span class="req">*</span></label>
                                        <select name="make_id" class="vd-select" id="detMakeId" required>
                                            @foreach($makes as $make)
                                                <option value="{{ $make->id }}" {{ $vehicle->make_id == $make->id ? 'selected' : '' }}>
                                                    {{ $make->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Model <span class="req">*</span></label>
                                        <select name="make_model_id" class="vd-select" id="detModelId" required>
                                            <option value="{{ $vehicle->make_model_id }}" selected>
                                                {{ $vehicle->makeModel->name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Trim</label>
                                        <input type="text" name="trim" class="vd-input"
                                               value="{{ $vehicle->trim }}"
                                               placeholder="e.g. LE, Sport, Limited">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Model number</label>
                                        <input type="text" name="model_number" class="vd-input"
                                               value="{{ $vehicle->model_number }}"
                                               placeholder="Enter model number">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Mileage</label>
                                        <input type="text" name="mileage" class="vd-input vd-numeric"
                                               value="{{ $vehicle->mileage ? number_format($vehicle->mileage) : '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Body type <span class="req">*</span></label>
                                        <select name="body_type_id" class="vd-select" required>
                                            <option value="">Select...</option>
                                            @foreach($bodyTypeGroups as $group)
                                                <optgroup label="{{ $group->name }}">
                                                    @foreach($group->bodyTypes as $bodyType)
                                                        <option value="{{ $bodyType->id }}" {{ $vehicle->body_type_id == $bodyType->id ? 'selected' : '' }}>
                                                            {{ $bodyType->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Body sub-type</label>
                                        <select name="body_style_id" class="vd-select">
                                            <option value="">Select...</option>
                                            @foreach($bodyStyleGroups as $group)
                                                <optgroup label="{{ $group->name }}">
                                                    @foreach($group->bodyStyles as $bodyStyle)
                                                        <option value="{{ $bodyStyle->id }}" {{ $vehicle->body_style_id == $bodyStyle->id ? 'selected' : '' }}>
                                                            {{ $bodyStyle->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Commercial</label>
                                        <select name="is_commercial" class="vd-select">
                                            <option value="0" {{ !$vehicle->is_commercial ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ $vehicle->is_commercial ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Exterior color</label>
                                        <select name="exterior_color_id" class="vd-select">
                                            <option value="">Select...</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->id }}" {{ $vehicle->exterior_color_id == $color->id ? 'selected' : '' }}>
                                                    {{ $color->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Interior color</label>
                                        <select name="interior_color_id" class="vd-select">
                                            <option value="">Select...</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->id }}" {{ $vehicle->interior_color_id == $color->id ? 'selected' : '' }}>
                                                    {{ $color->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label"># Doors</label>
                                        <input type="text" name="doors" class="vd-input vd-numeric"
                                               value="{{ $vehicle->doors ?? '' }}"
                                               placeholder="Enter some value">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Seating capacity</label>
                                        <input type="text" name="seating_capacity" class="vd-input vd-numeric"
                                               value="{{ $vehicle->seating_capacity ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                </div>

                                <hr class="vd-divider">

                                {{-- ── Powertrain ── --}}
                                <div id="det-powertrain" class="vd-det-section">
                                    <i class="bi bi-cpu-fill" style="color:#c0392b;"></i> Powertrain
                                </div>
                                <div class="vd-det-grid">
                                    <div class="vd-det-field vd-det-field-span2">
                                        <label class="vd-field-label">Engine</label>
                                        <input type="text" name="engine" class="vd-input"
                                               value="{{ $vehicle->engine ?? '' }}">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Aspiration</label>
                                        <input type="text" name="aspiration" class="vd-input"
                                               value="{{ $vehicle->specs->aspiration ?? '' }}">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Block type</label>
                                        <input type="text" name="block_type" class="vd-input"
                                               value="{{ $vehicle->specs->block_type ?? '' }}">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Cylinders</label>
                                        <input type="text" name="cylinders" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->cylinders ?? '' }}"
                                               placeholder="0">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Displacement</label>
                                        <input type="text" name="displacement" class="vd-input"
                                               value="{{ $vehicle->specs->displacement ?? '' }}"
                                               placeholder="e.g. 2.7">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Transmission</label>
                                        <select name="transmission_type_id" class="vd-select">
                                            <option value="">Select...</option>
                                            @foreach($transmissionTypes as $t)
                                                <option value="{{ $t->id }}" {{ $vehicle->transmission_type_id == $t->id ? 'selected' : '' }}>
                                                    {{ $t->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Drivetrain</label>
                                        <select name="drivetrain_type_id" class="vd-select">
                                            <option value="">Select...</option>
                                            @foreach($drivetrainTypes as $d)
                                                <option value="{{ $d->id }}" {{ $vehicle->drivetrain_type_id == $d->id ? 'selected' : '' }}>
                                                    {{ $d->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Max horsepower</label>
                                        <input type="text" name="max_horsepower" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->max_horsepower ? number_format($vehicle->specs->max_horsepower) : '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Max horsepower @ RPM</label>
                                        <input type="text" name="max_horsepower_at" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->max_horsepower_at ? number_format($vehicle->specs->max_horsepower_at) : '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Max torque</label>
                                        <input type="text" name="max_torque" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->max_torque ? number_format($vehicle->specs->max_torque) : '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Max torque @ RPM</label>
                                        <input type="text" name="max_torque_at" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->max_torque_at ? number_format($vehicle->specs->max_torque_at) : '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Max towing capacity</label>
                                        <input type="text" name="towing_capacity" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->towing_capacity ? number_format($vehicle->specs->towing_capacity) : '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Max payload capacity</label>
                                        <input type="text" name="payload_capacity" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->payload_capacity ? number_format($vehicle->specs->payload_capacity) : '' }}"
                                               placeholder="__,___">
                                    </div>
                                </div>

                                <hr class="vd-divider">

                                {{-- ── Fuel / Battery ── --}}
                                <div id="det-fuel" class="vd-det-section">
                                    <i class="bi bi-fuel-pump-fill" style="color:#c0392b;"></i> Fuel / Battery
                                </div>
                                <div class="vd-det-grid">
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Fuel</label>
                                        <select name="fuel_type_id" class="vd-select">
                                            <option value="">Select...</option>
                                            @foreach($fuelTypes as $f)
                                                <option value="{{ $f->id }}" {{ $vehicle->fuel_type_id == $f->id ? 'selected' : '' }}>
                                                    {{ $f->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Fuel tank capacity</label>
                                        <input type="text" name="fuel_tank" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->fuel_tank ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">MPG: City</label>
                                        <input type="text" name="mpg_city" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->mpg_city ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">MPG: Highway</label>
                                        <input type="text" name="mpg_highway" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->mpg_highway ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                </div>

                                <hr class="vd-divider">

                                {{-- ── Dimensions & sizes ── --}}
                                <div id="det-dimensions" class="vd-det-section">
                                    <i class="bi bi-pencil-fill" style="color:#c0392b;"></i> Dimensions &amp; sizes
                                </div>
                                <div class="vd-det-grid">
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Vehicle width</label>
                                        <input type="text" name="dimension_width" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->dimension_width ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Vehicle length</label>
                                        <input type="text" name="dimension_length" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->dimension_length ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Vehicle height</label>
                                        <input type="text" name="dimension_height" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->dimension_height ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Wheelbase</label>
                                        <input type="text" name="wheelbase" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->wheelbase ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Bed length</label>
                                        <input type="text" name="bed_length" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->bed_length ?? '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">GVWR</label>
                                        <input type="text" name="gvwr" class="vd-input vd-numeric"
                                               value="{{ $vehicle->specs->gvwr ? number_format($vehicle->specs->gvwr) : '' }}"
                                               placeholder="__,___">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Front wheel size</label>
                                        <input type="text" name="front_wheel" class="vd-input"
                                               value="{{ $vehicle->specs->front_wheel ?? '' }}">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Rear wheel size</label>
                                        <input type="text" name="rear_wheel" class="vd-input"
                                               value="{{ $vehicle->specs->rear_wheel ?? '' }}">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Front tire size</label>
                                        <input type="text" name="front_tire" class="vd-input"
                                               value="{{ $vehicle->specs->front_tire ?? '' }}">
                                    </div>
                                    <div class="vd-det-field">
                                        <label class="vd-field-label">Rear tire size</label>
                                        <input type="text" name="rear_tire" class="vd-input"
                                               value="{{ $vehicle->specs->rear_tire ?? '' }}">
                                    </div>
                                </div>

                                <button type="button" class="vd-btn-save" data-form="formDetails">
                                    <i class="bi bi-check-lg"></i> Save
                                </button>
                            </form>
                        </div>
                        {{-- end edit details --}}

                        {{-- ══════════════════════════
                             TAGS TAB
                        ══════════════════════════ --}}
                        <div class="vd-tab-pane" id="tab-tags">
                            <form id="formTags" data-ajax-url="{{ route('dealer.inventory.vdp.tags', $vehicle) }}" autocomplete="off">
                                @csrf
                                <input type="hidden" name="_method" value="PATCH">

                                <div class="vd-tag-input-wrap" id="tagInputWrap">
                                    @foreach($vehicle->tags as $tag)
                                        <span class="vd-tag-chip">
                                            {{ $tag->tag }}
                                            <button type="button" title="Remove">&times;</button>
                                            <input type="hidden" name="tags[]" value="{{ $tag->tag }}">
                                        </span>
                                    @endforeach
                                    <input type="text" class="vd-tag-input" id="tagInput" placeholder="Add a tag...">
                                </div>
                                <div class="vd-tag-hint">
                                    <i class="bi bi-arrow-return-left"></i>
                                    After typing, hit enter to add a tag. Click "Save" below to save your changes.
                                </div>
                                <br>
                                <button type="button" class="vd-btn-save" data-form="formTags" style="margin-top:8px;">
                                    <i class="bi bi-check-lg"></i> Save
                                </button>
                            </form>
                        </div>

                        {{-- ══════════════════════════
                             INTERNAL NOTES TAB
                        ══════════════════════════ --}}
                        <div class="vd-tab-pane" id="tab-internal-notes">
                            <form id="formNotes" data-ajax-url="{{ route('dealer.inventory.vdp.notes', $vehicle) }}" autocomplete="off">
                                @csrf
                                <input type="hidden" name="_method" value="PATCH">
                                <input type="hidden" name="internal_notes" id="hiddenInternalNotes"
                                       value="{{ $vehicle->notes->internal_notes ?? '' }}">

                                <div class="vd-notes-label">
                                    <i class="bi bi-circle-fill" style="font-size:10px;color:#f39c12;"></i>
                                    Internal Notes
                                </div>
                                <div class="vd-notes-sublabel">Visible only to dealership staff</div>

                                <div class="vd-rte-wrap" id="rteWrapNotes">
                                    <div id="rteNotes"
                                         data-quill-hidden="hiddenInternalNotes"
                                         style="min-height:200px;">{!! $vehicle->notes->internal_notes ?? '' !!}</div>
                                </div>

                                <button type="button" class="vd-btn-save" data-form="formNotes" style="margin-top:4px;">
                                    <i class="bi bi-check-lg"></i> Save
                                </button>
                            </form>
                        </div>

                    </div>{{-- end .vd-tab-content --}}
                </div>{{-- end .vd-middle --}}

                {{-- ════════════
                     RIGHT PANEL
                ════════════ --}}
                <aside class="vd-right">

                    <form id="formStatus" data-ajax-url="{{ route('dealer.inventory.vdp.status', $vehicle) }}" autocomplete="off">
                        @csrf
                        <input type="hidden" name="_method" value="PATCH">

                        <div class="vd-right-section">
                            <div class="vd-right-section-title">Vehicle Status</div>
                            <select name="status" class="vd-select" id="vehicleStatusSelect">
                                <option value="draft" {{ $vehicle->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ $vehicle->status === 'active' ? 'selected' : '' }}>Active / For Sale</option>
                                <option value="sold" {{ $vehicle->status === 'sold' ? 'selected' : '' }}>Sold</option>
                            </select>
                        </div>

                        <div class="vd-right-section">
                            <div class="vd-right-section-title">Temporary Statuses</div>
                            <div class="vd-toggle-row">
                                <span class="vd-toggle-row-label">
                                    On Hold
                                    <i class="bi bi-question-circle" title="Vehicle will be shown as 'On Hold'"></i>
                                </span>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <label class="vd-toggle">
                                        <input type="checkbox" name="is_on_hold" value="1"
                                               {{ $vehicle->is_on_hold ? 'checked' : '' }}>
                                        <span class="vd-toggle-slider"></span>
                                    </label>
                                    <span class="vd-toggle-no">{{ $vehicle->is_on_hold ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                            <div class="vd-toggle-row">
                                <span class="vd-toggle-row-label">
                                    Spotlight featured
                                    <i class="bi bi-question-circle" title="Feature this vehicle in spotlight"></i>
                                </span>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <label class="vd-toggle">
                                        <input type="checkbox" name="is_spotlight" value="1"
                                               {{ $vehicle->is_spotlight ? 'checked' : '' }}>
                                        <span class="vd-toggle-slider"></span>
                                    </label>
                                    <span class="vd-toggle-no">{{ $vehicle->is_spotlight ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="vd-right-section">
                            <div class="vd-right-section-title">Ignore Feed Updates</div>
                            <p style="font-size:12px;color:#666;line-height:1.6;margin-bottom:10px;">
                                Setting this to yes will ignore updates from your inventory feed going forward.
                            </p>
                            <select name="ignore_feed_updates" class="vd-select">
                                <option value="0" {{ !$vehicle->ignore_feed_updates ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $vehicle->ignore_feed_updates ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </form>

                    <div class="vd-right-section">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <div class="vd-right-section-title" style="margin-bottom:0;">Analytics</div>
                            <a href="#" class="vd-analytics-link" id="btnViewAnalytics">
                                View <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="vd-stat">
                            <span class="vd-stat-icon"><i class="bi bi-eye"></i></span>
                            <div>
                                <div class="vd-stat-label">Total Views</div>
                                <div class="vd-stat-value">{{ $vehicle->total_views }}</div>
                            </div>
                        </div>
                        <div class="vd-stat">
                            <span class="vd-stat-icon"><i class="bi bi-clock"></i></span>
                            <div>
                                <div class="vd-stat-label">Days on Lot</div>
                                <div class="vd-stat-value">{{ $vehicle->days_on_lot }}</div>
                            </div>
                        </div>
                        <div class="vd-stat">
                            <span class="vd-stat-icon"><i class="bi bi-funnel"></i></span>
                            <div>
                                <div class="vd-stat-label">Leads</div>
                                <div class="vd-stat-value">{{ $vehicle->total_leads }}</div>
                            </div>
                        </div>
                    </div>

                </aside>

            </div>{{-- end .vd-body --}}

            {{-- ════════════════════════════════════════════
                 SUBVIEW PANELS
            ════════════════════════════════════════════ --}}

            {{-- ── VIDEOS ── --}}
            <div class="vd-subview-panel" id="svVideos">
                <div class="vd-sv-wrap">
                    {{-- ════════════
                         LEFT PANEL
                    ════════════ --}}
                    @include('dealer.partials.inventory-details-sidebar')

                    <div class="vd-sv-main">
                        <div class="vd-video-layout">
                            <div class="vd-video-form vd-video-card">
                                <div class="vd-section-title" style="margin-bottom:16px;">Vehicle Video</div>
                                <div class="vd-modal-field">
                                    <div class="vd-modal-label">URL</div>
                                    <input type="text" class="vd-input" id="videoUrl"
                                           value="{{ $vehicle->video->url ?? '' }}"
                                           placeholder="Enter the video URL">
                                </div>
                                <div class="vd-modal-field">
                                    <div class="vd-modal-label">Source</div>
                                    <select class="vd-select" id="videoSource">
                                        <option value="">Select a video Source</option>
                                        <option value="youtube" {{ ($vehicle->video->source ?? '') === 'youtube' ? 'selected' : '' }}>YouTube</option>
                                        <option value="glo3d" {{ ($vehicle->video->source ?? '') === 'glo3d' ? 'selected' : '' }}>Glo3D</option>
                                        <option value="lesautomotive" {{ ($vehicle->video->source ?? '') === 'lesautomotive' ? 'selected' : '' }}>LESA Automotive</option>
                                        <option value="dealerimagepro" {{ ($vehicle->video->source ?? '') === 'dealerimagepro' ? 'selected' : '' }}>Dealer Image Pro</option>
                                        <option value="dealervideopro" {{ ($vehicle->video->source ?? '') === 'dealervideopro' ? 'selected' : '' }}>Dealer Video Pro</option>
                                        <option value="spincar" {{ ($vehicle->video->source ?? '') === 'spincar' ? 'selected' : '' }}>SpinCar</option>
                                        <option value="unityworks" {{ ($vehicle->video->source ?? '') === 'unityworks' ? 'selected' : '' }}>UnityWorks</option>
                                        <option value="flickfusion" {{ ($vehicle->video->source ?? '') === 'flickfusion' ? 'selected' : '' }}>FlickFusion</option>
                                    </select>
                                </div>
                                <div class="vd-video-actions">
                                    <button type="button" class="vd-btn-remove" id="btnVideoRemove">
                                        <i class="bi bi-trash3"></i> Remove
                                    </button>
                                    <button type="button" class="vd-btn-save" id="btnVideoSave"
                                            {{ !$vehicle->video ? 'disabled style="opacity:0.45;cursor:not-allowed;"' : '' }}>
                                        <i class="bi bi-check-lg"></i> Save
                                    </button>
                                </div>
                            </div>
                            <div class="vd-video-preview">
                                <div class="vd-video-preview-title">Video Preview</div>
                                <div class="vd-video-preview-body" id="videoPreviewBody"
                                     {{ $vehicle->video ? 'style="display:none;"' : '' }}>
                                    <i class="bi bi-camera-video-off"></i>
                                    To preview the video, please enter both the Video URL and Source.
                                </div>
                                <div id="videoPreviewEmbed"
                                     style="display:{{ $vehicle->video ? 'block' : 'none' }};width:100%;aspect-ratio:16/9;border-radius:5px;overflow:hidden;background:#000;">
                                    <iframe id="videoIframe"
                                            src="{{ $vehicle->video ? $vehicle->video->url : '' }}"
                                            width="100%" height="100%" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen style="display:block;width:100%;height:100%;border:none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── NOTES ── --}}
            <div class="vd-subview-panel" id="svNotes">
                <div class="vd-sv-wrap">
                    {{-- ════════════
                         LEFT PANEL
                    ════════════ --}}
                    @include('dealer.partials.inventory-details-sidebar')

                    <div class="vd-sv-main">

                        <div class="vd-notes-card">
                            <div class="vd-notes-card-title">Dealer Notes</div>
                            <div class="vd-notes-card-sub">Appears on website VDP</div>
                            <input type="hidden" name="dealer_notes" id="hiddenDealerNotes"
                                   value="{{ $vehicle->notes->dealer_notes ?? '' }}">
                            <div class="vd-rte-wrap">
                                <div id="rteDealerNotes"
                                     data-quill-hidden="hiddenDealerNotes"
                                     style="min-height:120px;">{!! $vehicle->notes->dealer_notes ?? '' !!}</div>
                            </div>
                            <button type="button" class="vd-btn-save" id="btnSaveDealerNotes">
                                <i class="bi bi-check-lg"></i> Save
                            </button>
                        </div>

                        <div class="vd-notes-card">
                            <div class="vd-notes-ai-header">
                                <div class="vd-notes-ai-badge">AI</div>
                                <div class="vd-notes-ai-info">
                                    <div class="vd-notes-ai-title">Extended Vehicle Description</div>
                                    <div class="vd-notes-ai-sub">Optional - appears on website VDP above Dealer Notes</div>
                                </div>
                            </div>
                            <input type="hidden" name="ai_description" id="hiddenAiDescription"
                                   value="{{ $vehicle->notes->ai_description ?? '' }}">
                            <div class="vd-rte-wrap vd-notes-ai-rte">
                                <div id="rteExtDescription"
                                     data-quill-hidden="hiddenAiDescription"
                                     style="min-height:120px;">{!! $vehicle->notes->ai_description ?? '' !!}</div>
                            </div>
                            <div class="vd-notes-ai-actions">
                                <button type="button" class="vd-btn-ai">
                                    <i class="bi bi-stars"></i> Generate with AI
                                </button>
                                <button type="button" class="vd-btn-save" id="btnSaveAiDescription">
                                    <i class="bi bi-check-lg"></i> Save
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── INSTALLED FACTORY OPTIONS ── --}}
            <div class="vd-subview-panel" id="svFactoryOptions">
                <div class="vd-sv-wrap">
                    {{-- ════════════
                         LEFT PANEL
                    ════════════ --}}
                    @include('dealer.partials.inventory-details-sidebar')

                    <div class="vd-sv-main"
                        data-url-factory-options="{{ route('dealer.inventory.vdp.factory-options', $vehicle) }}">

                        <div class="vd-ifo-header">
                            <div class="vd-ifo-title">Installed Factory Options</div>
                        </div>

                        <div class="vd-accordion" id="factoryOptionsAccordion">
                            @foreach($factoryOptionCategories as $category)
                                <div class="vd-accordion-item">
                                    <button class="vd-accordion-trigger"
                                            data-accordion="cat-{{ $category->id }}">
                                        {{ $category->name }}
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                    <div class="vd-accordion-body {{ $loop->first ? 'open' : '' }}" id="acc-cat-{{ $category->id }}">
                                        <div class="vd-feat-grid">
                                            @foreach($category->groups as $group)
                                                <div class="vd-feat-group">
                                                    <div class="vd-feat-group-title">{{ $group->name }}</div>
                                                    @php $grouped = $group->options->groupBy('sub_label'); @endphp
                                                    @foreach($grouped as $subLabel => $options)
                                                        @if($subLabel)
                                                            <div class="vd-feat-sub-label">{{ $subLabel }}</div>
                                                            @foreach($options as $option)
                                                                <div class="vd-feat-item vd-feat-item-indented">
                                                                    <label class="vd-feat-check-label">
                                                                        <input type="checkbox" class="vd-factory-option-cb"
                                                                               data-option-id="{{ $option->id }}"
                                                                               {{ in_array($option->id, $selectedOptionIds) ? 'checked' : '' }}>
                                                                        <span class="vd-feat-checkbox-icon">
                                                                            <i class="bi {{ in_array($option->id, $selectedOptionIds) ? 'bi-check-square-fill' : 'bi-square' }}"></i>
                                                                        </span>
                                                                        <span class="vd-feat-label">{{ $option->label }}</span>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            @foreach($options as $option)
                                                                <div class="vd-feat-item">
                                                                    <label class="vd-feat-check-label">
                                                                        <input type="checkbox" class="vd-factory-option-cb"
                                                                               data-option-id="{{ $option->id }}"
                                                                               {{ in_array($option->id, $selectedOptionIds) ? 'checked' : '' }}>
                                                                        <span class="vd-feat-checkbox-icon">
                                                                            <i class="bi {{ in_array($option->id, $selectedOptionIds) ? 'bi-check-square-fill' : 'bi-square' }}"></i>
                                                                        </span>
                                                                        <span class="vd-feat-label">{{ $option->label }}</span>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── PREMIUM BUILD OPTIONS ── --}}
            <div class="vd-subview-panel" id="svPremiumBuild">
                <div class="vd-sv-wrap">
                    {{-- ════════════
                         LEFT PANEL
                    ════════════ --}}
                    @include('dealer.partials.inventory-details-sidebar')

                    <div class="vd-sv-main">
                        <div class="vd-pbo-header">
                            <div class="vd-pbo-title">Premium Build Options</div>
                            <button type="button" class="vd-btn-add-option" id="btnAddOption">
                                <i class="bi bi-plus-lg"></i> Add Option
                            </button>
                        </div>
                        <table class="vd-table">
                            <thead>
                                <tr>
                                    <th>Factory Code</th>
                                    <th>Category</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>MSRP</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="pboTableBody">
                                @forelse($vehicle->premiumOptions as $opt)
                                    <tr data-option-id="{{ $opt->id }}">
                                        <td>{{ $opt->factory_code }}</td>
                                        <td>{{ $opt->category }}</td>
                                        <td>{{ $opt->name }}</td>
                                        <td>{{ $opt->description ?? '—' }}</td>
                                        <td>{{ $opt->msrp ? '$'.number_format($opt->msrp, 2) : '—' }}</td>
                                        <td>
                                            <button type="button" class="vd-btn-remove pbo-delete"
                                                    data-id="{{ $opt->id }}" style="padding:4px 10px;font-size:12px;">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="pboEmptyRow">
                                        <td colspan="6" class="vd-table-empty">No results found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── INCENTIVES ── --}}
            <div class="vd-subview-panel" id="svIncentives">
                <div class="vd-sv-wrap">
                    @include('dealer.partials.inventory-details-sidebar')

                    <div class="vd-sv-main">
                        <table class="vd-table">
                            <thead>
                                <tr>
                                    <th colspan="6" style="font-size:13.5px;font-weight:700;color:#222;padding-bottom:10px;">
                                        Incentives
                                    </th>
                                </tr>
                                <tr>
                                    <th>Program</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Expires</th>
                                    <th>Details</th>
                                    <th>Hide</th>
                                </tr>
                            </thead>
                            <tbody id="incentivesTableBody">
                                @forelse($applicableIncentives as $incentive)
                                    @php $isHidden = in_array($incentive->id, $hiddenIncentiveIds); @endphp
                                    <tr id="inc-row-{{ $incentive->id }}"
                                        class="{{ $isHidden ? 'opacity-50' : '' }}"
                                        style="{{ $isHidden ? 'opacity:0.5;' : '' }}">
                                        <td class="px-3">
                                            {{ $incentive->program_code ?: '—' }}
                                        </td>
                                        <td class="px-3">
                                            <span class="badge inc-type-badge inc-type-{{ $incentive->type }}">
                                                {{ $incentive->type_label }}
                                            </span>
                                        </td>
                                        <td class="px-3">{{ $incentive->category_label }}</td>
                                        <td class="px-3 text-muted">
                                            {{ $incentive->expires_at ? $incentive->expires_at->format('M d, Y') : '—' }}
                                        </td>
                                        <td class="px-3">
                                            @if($incentive->description)
                                                <span class="vd-inc-tooltip">
                                                    <i class="bi bi-info-circle" style="color:#888;cursor:pointer;"></i>
                                                    <span class="vd-inc-tooltip-text">{{ $incentive->description }}</span>
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-3">
                                            <label class="vd-toggle" title="{{ $isHidden ? 'Show' : 'Hide' }}">
                                                <input type="checkbox"
                                                       class="inc-hide-toggle"
                                                       data-incentive-id="{{ $incentive->id }}"
                                                       {{ $isHidden ? 'checked' : '' }}>
                                                <span class="vd-toggle-slider"></span>
                                            </label>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="vd-table-empty">No incentives found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── PRINTABLES ── --}}
            <div class="vd-subview-panel" id="svPrintables"
                 data-url-printables-index="{{ route('dealer.inventory.vdp.printables.index', $vehicle) }}"
                 data-url-printables-store="{{ route('dealer.inventory.vdp.printables.store', $vehicle) }}"
                 data-url-printables-update-base="{{ route('dealer.inventory.vdp.printables.update', [$vehicle, '__ID__']) }}"
                 data-url-printables-destroy-base="{{ route('dealer.inventory.vdp.printables.destroy', [$vehicle, '__ID__']) }}"
                 data-url-printables-render-base="{{ route('dealer.inventory.vdp.printables.render', [$vehicle, '__ID__']) }}">

                <div class="vd-sv-wrap">
                    @include('dealer.partials.inventory-details-sidebar')

                    <div class="vd-sv-main">

                        {{-- Header row --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                            <div style="font-size:16px;font-weight:700;color:#222;">Printables</div>
                            <button type="button" class="vd-btn-add-option" id="btnAddPrintable"
                                    style="opacity:0.5;cursor:not-allowed;" disabled>
                                <i class="bi bi-plus-lg"></i> Add Printable
                            </button>
                        </div>

                        {{-- Table --}}
                        <table class="vd-table" id="printablesTable">
                            <thead>
                                <tr>
                                    <th>Document</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="printablesTableBody">
                                <tr id="printablesLoadingRow">
                                    <td colspan="3" class="vd-table-empty">
                                        <span class="spinner-border spinner-border-sm me-2"
                                              style="width:13px;height:13px;border-width:2px;"></span>
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            {{-- ── ANALYTICS ── --}}
            <div class="vd-subview-panel" id="svAnalytics">
                <div class="vd-sv-wrap">
                    {{-- ════════════
                         LEFT PANEL
                    ════════════ --}}
                    @include('dealer.partials.inventory-details-sidebar')

                    <div class="vd-sv-main">
                        <div class="vd-analytics-stats-row">
                            <div class="vd-analytics-stat-card">
                                <i class="bi bi-eye vd-analytics-stat-icon"></i>
                                <div>
                                    <div class="vd-analytics-stat-label">Total Views</div>
                                    <div class="vd-analytics-stat-value">{{ $vehicle->total_views }}</div>
                                </div>
                            </div>
                            <div class="vd-analytics-stat-card">
                                <i class="bi bi-clock vd-analytics-stat-icon"></i>
                                <div>
                                    <div class="vd-analytics-stat-label">Days on Lot</div>
                                    <div class="vd-analytics-stat-value">{{ $vehicle->days_on_lot }}</div>
                                </div>
                            </div>
                            <div class="vd-analytics-stat-card">
                                <i class="bi bi-funnel vd-analytics-stat-icon"></i>
                                <div>
                                    <div class="vd-analytics-stat-label">Leads</div>
                                    <div class="vd-analytics-stat-value">{{ $vehicle->total_leads }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="vd-analytics-card">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                                <div class="vd-analytics-card-title" style="margin-bottom:0;">Views</div>
                                <div class="vd-analytics-period-btns">
                                    <button class="vd-period-btn" data-days="7">7 days</button>
                                    <button class="vd-period-btn active" data-days="30">30 days</button>
                                    <button class="vd-period-btn" data-days="90">90 days</button>
                                </div>
                            </div>
                            <div class="vd-chart-wrap">
                                <canvas id="analyticsChart" height="80"></canvas>
                            </div>
                        </div>

                        <div class="vd-analytics-card vd-price-history-table">
                            <div class="vd-analytics-card-title">Price Change History</div>
                            <table class="vd-table" style="margin:-4px -2px;">
                                <thead>
                                    <tr>
                                        <th>Date</th><th>Days ago</th>
                                        <th>New price</th><th>$ change</th><th>% change</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="5" class="vd-table-empty">No price changes logged yet.</td></tr>
                                    <tr>
                                        <td colspan="3"><strong>Total</strong></td>
                                        <td>--</td><td>--</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── SYNDICATION ── --}}
            <div class="vd-subview-panel" id="svSyndication">
                <div class="vd-sv-wrap">
                    {{-- ════════════
                         LEFT PANEL
                    ════════════ --}}
                    @include('dealer.partials.inventory-details-sidebar')

                    <div class="vd-sv-main">
                        <div class="vd-sync-notice">
                            Status indicators for each individual syndication provider are coming soon!
                        </div>
                        <div class="vd-sync-card">
                            <div class="vd-sync-title">Syndication</div>
                            <div class="vd-sync-row vd-sync-header">
                                <div>Provider</div>
                                <div>Feed Name</div>
                                <div>Date Enabled</div>
                                <div>Status</div>
                            </div>
                            @php
                            $syndicationFeeds = [
                                ['provider'=>'Auto Dealers Digital','feed'=>'Auto Dealers Digital - Angel Motors Inc','date'=>'Friday, February 26, 2026','status'=>'Success'],
                                ['provider'=>'Carfax','feed'=>'Carfax - Angel Motors Inc','date'=>'Friday, February 26, 2026','status'=>'Success'],
                                ['provider'=>'CarGurus','feed'=>'CarGurus - Angel Motors Inc','date'=>'Friday, February 26, 2026','status'=>'Success'],
                                ['provider'=>'Cars For Sale','feed'=>'Cars For Sale - Angel Motors Inc','date'=>'Friday, February 26, 2026','status'=>'Success'],
                                ['provider'=>'Cars.com','feed'=>'Cars.com - Angel Motors Inc','date'=>'Friday, February 26, 2026','status'=>'Success'],
                                ['provider'=>'Facebook v2','feed'=>'Facebook v2 - Angel Motors Inc','date'=>'Saturday, February 27, 2026','status'=>'Success'],
                                ['provider'=>'HomeNet','feed'=>'HomeNet - Angel Motors Inc','date'=>'Friday, February 26, 2026','status'=>'Success'],
                                ['provider'=>'TrueCar','feed'=>'TrueCar - Angel Motors Inc','date'=>'Friday, February 26, 2026','status'=>'Success'],
                            ];
                            @endphp
                            @foreach($syndicationFeeds as $feed)
                            <div class="vd-sync-row">
                                <div>{{ $feed['provider'] }}</div>
                                <div>{{ $feed['feed'] }}</div>
                                <div>{{ $feed['date'] }}</div>
                                <div class="vd-sync-status-success">{{ $feed['status'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end .subview.vd-page --}}
    </div>{{-- end .view-content --}}
</main>
@endsection

@push('page-modals')
    @include('dealer.modals.inventory-add-premium-options')
    @include('dealer.modals.inventory-add-printable')
    @include('dealer.modals.inventory-find-contacts-canvas')
@endpush