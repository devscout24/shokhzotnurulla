@extends('layouts.dealer.app')
@section('title', 'VIN Inspector | ' . __(config('app.name')))

@push('page-assets')
    @vite(['resources/css/dealer/pages/inventory-details.css'])
    <style>
        * { box-sizing:border-box; }
        .vi-layout { display:flex; gap:0; height:calc(100vh - 134px); }
        .vi-left { width:40%; overflow-y:auto; border-right:1px solid #2d2d3a; padding:20px; }
        .vi-right { width:60%; display:flex; flex-direction:column; overflow:hidden; }
        .vi-right-body { flex:1; overflow-y:auto; padding:20px; padding-top:0; }

        /* ── left form fields ── */
        .vi-field { display:flex; padding:4px 0; font-size:13px; border-bottom:1px solid #1e1e2e; align-items:center; }
        .vi-field .vi-label { width:38%; color:#8a8a9a; flex-shrink:0; }
        .vi-field .vi-input { width:62%; }
        .vi-field .vi-input input,
        .vi-field .vi-input select,
        .vi-field .vi-input textarea { width:100%; padding:3px 6px; border:1px solid #2d2d3a; border-radius:4px; background:#12121c; color:#e0e0e0; font-size:12px; font-family:monospace; }
        .vi-field .vi-input input:focus,
        .vi-field .vi-input select:focus { outline:none; border-color:#4f8cff; }
        .vi-field .vi-input textarea { resize:vertical; min-height:28px; }
        .vi-section-title { font-size:13px; font-weight:600; color:#6b9aff; margin:16px 0 8px; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; justify-content:space-between; }
        .vi-back { display:inline-flex; align-items:center; gap:6px; color:#8a8a9a; text-decoration:none; font-size:13px; margin-bottom:16px; }
        .vi-back:hover { color:#e0e0e0; }
        .vi-save-bar { position:sticky; top:0; z-index:20; background:#1a1a27; padding:10px 0 14px; display:flex; align-items:center; gap:12px; border-bottom:1px solid #2d2d3a; margin-bottom:12px; }
        .vi-save-bar .btn-save { padding:6px 20px; background:#4f8cff; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer; }
        .vi-save-bar .btn-save:hover { background:#3b7ae8; }
        .vi-save-bar .btn-save:disabled { opacity:0.5; cursor:not-allowed; }

        /* ── tabs ── */
        .vi-tabs { display:flex; gap:0; border-bottom:1px solid #2d2d3a; background:#1a1a27; position:sticky; top:0; z-index:15; flex-shrink:0; }
        .vi-tab { padding:10px 18px; font-size:12px; font-weight:500; color:#5a5a6a; cursor:pointer; border-bottom:2px solid transparent; text-transform:uppercase; letter-spacing:0.3px; transition:all 0.15s; }
        .vi-tab:hover { color:#8a8a9a; background:#12121c; }
        .vi-tab.active { color:#6b9aff; border-bottom-color:#6b9aff; background:transparent; }

        /* ── search bar (inside right, below tabs) ── */
        .vi-search { padding:12px 20px; background:#1a1a27; border-bottom:1px solid #2d2d3a; flex-shrink:0; }
        .vi-search-row { display:flex; align-items:center; gap:10px; }
        .vi-search input { flex:1; padding:8px 12px; border:1px solid #2d2d3a; border-radius:6px; background:#12121c; color:#e0e0e0; font-size:13px; }
        .vi-search input:focus { outline:none; border-color:#4f8cff; }
        .vi-match-badge { font-size:11px; color:#8a8a9a; white-space:nowrap; min-width:60px; text-align:right; }
        .vi-hint { font-size:11px; color:#5a5a6a; margin-top:4px; }
        .vi-hint i { margin-right:4px; }

        /* ── JSON viewer ── */
        .vi-json-pane { display:none; flex-direction:column; height:100%; }
        .vi-json-pane.active { display:flex; }
        .vi-json-pane pre { background:#0d0d17; border:1px solid #2d2d3a; border-radius:6px; padding:16px; font-size:11px; line-height:1.6; overflow:auto; flex:1; margin:0; white-space:pre; word-break:normal; cursor:default; min-height:300px; }
        .vi-json-pane pre .jv-key { color:#79c0ff; }
        .vi-json-pane pre .jv-str { color:#a5d6ff; }
        .vi-json-pane pre .jv-num { color:#79c0ff; }
        .vi-json-pane pre .jv-bool { color:#ff7b72; }
        .vi-json-pane pre .jv-null { color:#8b949e; }
        .vi-json-pane pre .jv-punc { color:#e0e0e0; }
        .vi-json-pane pre .jv-clickable { cursor:pointer; border-bottom:1px dashed #4f8cff; }
        .vi-json-pane pre .jv-clickable:hover { background:#1a3a5c; }
        .vi-json-pane pre .jv-highlight { background:#2d5a1e; border-radius:2px; cursor:pointer; }
        .vi-json-pane pre .jv-highlight:hover { background:#3d7a2e; }
        .vi-empty { color:#5a5a6a; font-style:italic; font-size:13px; padding:20px; }

        /* ── toast ── */
        .vi-toast { position:fixed; top:20px; right:20px; z-index:9999; padding:12px 20px; border-radius:8px; font-size:14px; color:#fff; opacity:0; transition:opacity 0.3s; pointer-events:none; }
        .vi-toast.success { background:#1a6b3c; }
        .vi-toast.error { background:#6b1a1a; }
        .vi-toast.show { opacity:1; }

        /* ── line numbers ── */
        .vi-line-num { display:inline-block; width:32px; color:#48484f; text-align:right; margin-right:14px; user-select:none; }

        /* ── factory options ── */
        .vi-fo-category { margin:6px 0; border:1px solid #2d2d3a; border-radius:4px; overflow:hidden; }
        .vi-fo-category[open] { background:#12121e; }
        .vi-fo-cat-title { padding:6px 10px; font-size:12px; font-weight:600; color:#8a8a9a; cursor:pointer; display:flex; justify-content:space-between; align-items:center; background:#1a1a27; user-select:none; }
        .vi-fo-cat-title:hover { color:#e0e0e0; }
        .vi-fo-category[open] .vi-fo-cat-title { color:#6b9aff; border-bottom:1px solid #2d2d3a; }
        .vi-fo-count { font-size:10px; color:#5a5a6a; font-weight:400; }
        .vi-fo-group { padding:4px 10px 6px; }
        .vi-fo-group + .vi-fo-group { border-top:1px dashed #1e1e2e; }
        .vi-fo-group-title { font-size:11px; font-weight:500; color:#6b9aff; margin:4px 0 2px; }
        .vi-fo-item { display:flex; align-items:center; gap:4px; padding:2px 0; font-size:12px; color:#c0c0d0; }
        .vi-fo-item:hover { color:#e0e0e0; }
        .vi-fo-item-label { display:flex; align-items:center; gap:6px; flex:1; cursor:pointer; min-width:0; }
        .vi-fo-item-label input[type="checkbox"] { accent-color:#4f8cff; width:13px; height:13px; cursor:pointer; flex-shrink:0; }
        .vi-fo-item-label span { line-height:1.3; }
        .vi-fo-search-btn { background:none; border:none; color:#4a4a5a; cursor:pointer; padding:2px 4px; font-size:11px; border-radius:3px; flex-shrink:0; line-height:1; }
        .vi-fo-search-btn:hover { color:#6b9aff; background:#1a1a2e; }

        /* ── loader overlay ── */
        .vi-loader { position:absolute; inset:0; z-index:100; display:none; align-items:center; justify-content:center; background:rgba(18,18,28,0.85); backdrop-filter:blur(2px); }
        .vi-loader.show { display:flex; }
        .vi-loader-inner { text-align:center; }
        .vi-loader-spinner { width:28px; height:28px; border:3px solid #2d2d3a; border-top-color:#4f8cff; border-radius:50%; animation:vi-spin 0.6s linear infinite; margin:0 auto 8px; }
        .vi-loader-label { font-size:11px; color:#8a8a9a; text-transform:uppercase; letter-spacing:0.5px; }
        @keyframes vi-spin { to { transform:rotate(360deg); } }
    </style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent" style="padding:0;overflow:hidden;">
    <div class="view-content inventory-view" data-view="inventory" style="padding:0;">

        @include('dealer.partials.inventory-topbar')

        <div class="vi-layout" style="position:relative;">

            <div class="vi-loader" id="viLoader">
                <div class="vi-loader-inner">
                    <div class="vi-loader-spinner"></div>
                    <div class="vi-loader-label">Loading…</div>
                </div>
            </div>

            {{-- ═══════ LEFT: Editable Vehicle Fields ═══════ --}}
            <div class="vi-left">
                <form id="viForm" method="POST" action="{{ route('dealer.inventory.vdp.vin-inspector.save', $vehicle) }}">
                    @csrf

                    <a href="{{ route('dealer.inventory.vdp.show', $vehicle) }}" class="vi-back">
                        <i class="bi bi-arrow-left"></i> Back to VDP
                    </a>

                    <div class="vi-save-bar">
                        <span style="font-size:15px;font-weight:600;color:#e0e0e0;">Vehicle Fields</span>
                        <button type="submit" class="btn-save" id="viSaveBtn">
                            <i class="bi bi-check-lg"></i> Save
                        </button>
                        <span id="viSaveStatus" style="font-size:12px;color:#5a5a6a;"></span>
                    </div>

                    {{-- ── Vehicle ── --}}
                    <div class="vi-section-title">Vehicle</div>

                    <div class="vi-field">
                        <span class="vi-label">ID</span>
                        <span class="vi-input" style="color:#5a5a6a;padding:3px 6px;font-size:12px;font-family:monospace;">{{ $vehicle->id }}</span>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Stock #</span>
                        <div class="vi-input"><input type="text" name="stock_number" value="{{ old('stock_number', $vehicle->stock_number) }}" data-field="stock_number"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">VIN</span>
                        <div class="vi-input"><input type="text" name="vin" value="{{ old('vin', $vehicle->vin) }}" data-field="vin"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Year</span>
                        <div class="vi-input"><input type="number" name="year" value="{{ old('year', $vehicle->year) }}" data-field="year"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Model #</span>
                        <div class="vi-input"><input type="text" name="model_number" value="{{ old('model_number', $vehicle->model_number) }}" data-field="model_number"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Trim</span>
                        <div class="vi-input"><input type="text" name="trim" value="{{ old('trim', $vehicle->trim) }}" data-field="trim"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Engine</span>
                        <div class="vi-input"><input type="text" name="engine" value="{{ old('engine', $vehicle->engine) }}" data-field="engine"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Mileage</span>
                        <div class="vi-input"><input type="number" name="mileage" value="{{ old('mileage', $vehicle->mileage) }}" data-field="mileage"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Condition</span>
                        <div class="vi-input">
                            <select name="vehicle_condition" data-field="vehicle_condition">
                                <option value="Used" {{ $vehicle->vehicle_condition === 'Used' ? 'selected' : '' }}>Used</option>
                                <option value="New" {{ $vehicle->vehicle_condition === 'New' ? 'selected' : '' }}>New</option>
                                <option value="Certified Pre-Owned" {{ $vehicle->vehicle_condition === 'Certified Pre-Owned' ? 'selected' : '' }}>Certified Pre-Owned</option>
                            </select>
                        </div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Doors</span>
                        <div class="vi-input"><input type="number" name="doors" value="{{ old('doors', $vehicle->doors) }}" data-field="doors"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Seating</span>
                        <div class="vi-input"><input type="number" name="seating_capacity" value="{{ old('seating_capacity', $vehicle->seating_capacity) }}" data-field="seating_capacity"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Status</span>
                        <div class="vi-input">
                            <select name="status" data-field="status">
                                <option value="draft" {{ $vehicle->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ $vehicle->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="sold" {{ $vehicle->status === 'sold' ? 'selected' : '' }}>Sold</option>
                            </select>
                        </div>
                    </div>

                    {{-- ── Prices ── --}}
                    <div class="vi-section-title">Prices</div>
                    @php $pv = fn($k) => old($k, $vehicle->prices?->{$k}); @endphp
                    <div class="vi-field">
                        <span class="vi-label">MSRP</span>
                        <div class="vi-input"><input type="number" step="0.01" name="msrp" value="{{ $pv('msrp') }}" data-field="msrp"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Dealer Cost</span>
                        <div class="vi-input"><input type="number" step="0.01" name="dealer_cost" value="{{ $pv('dealer_cost') }}" data-field="dealer_cost"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">List Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="list_price" value="{{ old('list_price', $vehicle->list_price) }}" data-field="list_price"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Original Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="original_price" value="{{ old('original_price', $vehicle->original_price) }}" data-field="original_price"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Internet Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="internet_price" value="{{ $pv('internet_price') }}" data-field="internet_price"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Special Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="special_price" value="{{ $pv('special_price') }}" data-field="special_price"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Asking Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="asking_price" value="{{ $pv('asking_price') }}" data-field="asking_price"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Sold Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="sold_price" value="{{ $pv('sold_price') }}" data-field="sold_price"></div>
                    </div>

                    {{-- ── Specs ── --}}
                    @if ($vehicle->specs)
                    <div class="vi-section-title">Specs</div>
                    @php $sv = fn($k) => old($k, $vehicle->specs?->{$k}); @endphp
                    <div class="vi-field">
                        <span class="vi-label">Cylinders</span>
                        <div class="vi-input"><input type="number" name="cylinders" value="{{ $sv('cylinders') }}" data-field="cylinders"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Displacement (L)</span>
                        <div class="vi-input"><input type="number" step="0.1" name="displacement" value="{{ $sv('displacement') }}" data-field="displacement"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Max Horsepower</span>
                        <div class="vi-input"><input type="number" name="max_horsepower" value="{{ $sv('max_horsepower') }}" data-field="max_horsepower"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">HP @ RPM</span>
                        <div class="vi-input"><input type="number" name="max_horsepower_at" value="{{ $sv('max_horsepower_at') }}" data-field="max_horsepower_at"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Max Torque</span>
                        <div class="vi-input"><input type="number" name="max_torque" value="{{ $sv('max_torque') }}" data-field="max_torque"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Torque @ RPM</span>
                        <div class="vi-input"><input type="number" name="max_torque_at" value="{{ $sv('max_torque_at') }}" data-field="max_torque_at"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Block Type</span>
                        <div class="vi-input"><input type="text" name="block_type" value="{{ $sv('block_type') }}" data-field="block_type"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Trans Std</span>
                        <div class="vi-input"><input type="text" name="transmission_standard" value="{{ $sv('transmission_standard') }}" data-field="transmission_standard"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Drivetrain Std</span>
                        <div class="vi-input"><input type="text" name="drivetrain_standard" value="{{ $sv('drivetrain_standard') }}" data-field="drivetrain_standard"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">GVWR</span>
                        <div class="vi-input"><input type="number" name="gvwr" value="{{ $sv('gvwr') }}" data-field="gvwr"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Empty Weight</span>
                        <div class="vi-input"><input type="number" name="empty_weight" value="{{ $sv('empty_weight') }}" data-field="empty_weight"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Fuel Tank</span>
                        <div class="vi-input"><input type="number" step="0.1" name="fuel_tank" value="{{ $sv('fuel_tank') }}" data-field="fuel_tank"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">MPG City</span>
                        <div class="vi-input"><input type="number" step="0.1" name="mpg_city" value="{{ $sv('mpg_city') }}" data-field="mpg_city"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">MPG Highway</span>
                        <div class="vi-input"><input type="number" step="0.1" name="mpg_highway" value="{{ $sv('mpg_highway') }}" data-field="mpg_highway"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Width</span>
                        <div class="vi-input"><input type="number" step="0.1" name="dimension_width" value="{{ $sv('dimension_width') }}" data-field="dimension_width"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Length</span>
                        <div class="vi-input"><input type="number" step="0.1" name="dimension_length" value="{{ $sv('dimension_length') }}" data-field="dimension_length"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Height</span>
                        <div class="vi-input"><input type="number" step="0.1" name="dimension_height" value="{{ $sv('dimension_height') }}" data-field="dimension_height"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Wheelbase</span>
                        <div class="vi-input"><input type="number" step="0.1" name="wheelbase" value="{{ $sv('wheelbase') }}" data-field="wheelbase"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Compression</span>
                        <div class="vi-input"><input type="number" step="0.1" name="compression" value="{{ $sv('compression') }}" data-field="compression"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Engine Valves</span>
                        <div class="vi-input"><input type="number" name="engine_valves" value="{{ $sv('engine_valves') }}" data-field="engine_valves"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Engine Model</span>
                        <div class="vi-input"><input type="text" name="engine_model" value="{{ $sv('engine_model') }}" data-field="engine_model"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Front Tire</span>
                        <div class="vi-input"><input type="text" name="front_tire" value="{{ $sv('front_tire') }}" data-field="front_tire"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Rear Tire</span>
                        <div class="vi-input"><input type="text" name="rear_tire" value="{{ $sv('rear_tire') }}" data-field="rear_tire"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Front Wheel</span>
                        <div class="vi-input"><input type="text" name="front_wheel" value="{{ $sv('front_wheel') }}" data-field="front_wheel"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Rear Wheel</span>
                        <div class="vi-input"><input type="text" name="rear_wheel" value="{{ $sv('rear_wheel') }}" data-field="rear_wheel"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Towing Capacity</span>
                        <div class="vi-input"><input type="number" name="towing_capacity" value="{{ $sv('towing_capacity') }}" data-field="towing_capacity"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Payload Capacity</span>
                        <div class="vi-input"><input type="number" name="payload_capacity" value="{{ $sv('payload_capacity') }}" data-field="payload_capacity"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Axle Ratio</span>
                        <div class="vi-input"><input type="number" step="0.01" name="axle_ratio" value="{{ $sv('axle_ratio') }}" data-field="axle_ratio"></div>
                    </div>
                    @endif

                    {{-- ── Factory Options ── --}}
                    <div class="vi-section-title" style="margin-top:20px;">Factory Options</div>

                    @foreach($factoryOptionCategories as $category)
                        <details class="vi-fo-category" {{ $loop->first ? 'open' : '' }}>
                            <summary class="vi-fo-cat-title">
                                <span>{{ $category->name }}</span>
                                <span class="vi-fo-count">{{ $category->groups->sum(fn($g) => $g->options->count()) }}</span>
                            </summary>
                            @foreach($category->groups as $group)
                                <div class="vi-fo-group">
                                    <div class="vi-fo-group-title">{{ $group->name }}</div>
                                    @foreach($group->options as $option)
                                        <div class="vi-fo-item">
                                            <label class="vi-fo-item-label">
                                                <input type="checkbox"
                                                       name="selected_ids[]"
                                                       value="{{ $option->id }}"
                                                       {{ in_array($option->id, $selectedOptionIds) ? 'checked' : '' }}>
                                                <span>{{ $option->label }}</span>
                                            </label>
                                            <button type="button"
                                                     class="vi-fo-search-btn"
                                                     title="Search this option in JSON"
                                                     onclick="try{var q=this.getAttribute('data-query'),i=document.getElementById('viSearchInput');if(i){i.value=q;window.filterActiveTab();i.focus();}}catch(e){}"
                                                     data-query="{{ $option->label }}">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </details>
                    @endforeach
                </form>
            </div>

            {{-- ═══════ RIGHT: Tabbed JSON Viewer ═══════ --}}
            <div class="vi-right">

                @php
                    $vinData = $vehicle->vinData;
                    $jsonColumns = ['vehicle_databases', 'premium_vehicle_databases', 'default', 'data_one', 'custom'];
                    $availableCols = [];
                    if ($vinData) {
                        foreach ($jsonColumns as $col) {
                            if (!is_null($vinData->{$col})) {
                                $availableCols[] = $col;
                            }
                        }
                    }
                @endphp

                @if (empty($availableCols))
                    <div class="vi-empty" style="padding:40px 20px;">No VIN decode data found for this vehicle.</div>
                @else
                    {{-- Tabs --}}
                    <div class="vi-tabs" id="viTabs">
                        @foreach ($availableCols as $i => $col)
                            <div class="vi-tab {{ $i === 0 ? 'active' : '' }}" data-tab="{{ $col }}" onclick="try{switchTab(this.getAttribute('data-tab'))}catch(e){}">{{ $col }}</div>
                        @endforeach
                    </div>

                    {{-- Search bar --}}
                    <div class="vi-search">
                        <div class="vi-search-row">
                            <input type="text" id="viSearchInput" placeholder="Search…" oninput="try{clearTimeout(window._searchTimer);window._searchTimer=setTimeout(filterActiveTab,200)}catch(e){}" onkeydown="try{return searchKeydown(event)}catch(e){}">
                            <span class="vi-match-badge" id="viMatchBadge"></span>
                            <button class="vi-nav-btn" id="viPrevBtn" onclick="navMatch(-1)" title="Previous match (Shift+Enter)" style="display:none;background:none;border:1px solid #2d2d3a;border-radius:4px;color:#8a8a9a;padding:4px 8px;cursor:pointer;">&#x25B2;</button>
                            <button class="vi-nav-btn" id="viNextBtn" onclick="navMatch(1)" title="Next match (Enter)" style="display:none;background:none;border:1px solid #2d2d3a;border-radius:4px;color:#8a8a9a;padding:4px 8px;cursor:pointer;">&#x25BC;</button>
                        </div>
                        <div class="vi-hint"><i class="bi bi-hand-index-thumb"></i> Click a value to fill a field &middot; Enter/Shift+Enter to jump between matches</div>
                    </div>

                    {{-- JSON panes (rendered server-side, no JS dependency for basic display) --}}
                    <div class="vi-right-body">
                        @foreach ($availableCols as $i => $col)
                            <div class="vi-json-pane {{ $i === 0 ? 'active' : '' }}" id="viPane{{ $col }}">
                                <pre class="vi-pre" id="viPre{{ $col }}">{{ json_encode($vinData->{$col}, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>
    </div>
</main>

<div id="viToast" class="vi-toast"></div>

<script>
(function() {
    var activeTab = '{{ $availableCols[0] ?? '' }}';
    var viMatches = [];
    var viMatchIdx = -1;

    // ── JSON syntax highlighting ──────────────────────────────────────────────
    function highlightJson(raw) {
        var lines = raw.split('\n');
        return lines.map(function(line, idx) {
            var num = '<span class="vi-line-num">' + (idx + 1) + '</span>';
            return num + syntaxLine(line) + '\n';
        }).join('');
    }

    var RE_TOKEN = /("[^"]*"\s*:)|("(?:[^"\\]|\\.)*")|(-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?)|(true|false)|(null)|(.)/g;

    function syntaxLine(line) {
        return line.replace(RE_TOKEN, function(m, key, str, num, bool, nil, punc) {
            if (key) return '<span class="jv-key">' + esc(key) + '</span>';
            if (str) {
                var d = str.slice(1, -1).replace(/\\(.)/g, '$1');
                return '<span class="jv-str jv-clickable" data-val="' + escAttr(d) + '">' + esc(str) + '</span>';
            }
            if (num) return '<span class="jv-num jv-clickable" data-val="' + num + '">' + num + '</span>';
            if (bool) return '<span class="jv-bool jv-clickable" data-val="' + bool + '">' + bool + '</span>';
            if (nil) return '<span class="jv-null">' + nil + '</span>';
            return '<span class="jv-punc">' + esc(punc) + '</span>';
        });
    }

    var escMap = {'&':'&amp;','"':'&quot;',"'":'&#39;','<':'&lt;','>':'&gt;'};
    function esc(s) { return s.replace(/[&"'<>]/g, function(c) { return escMap[c]; }); }
    function escAttr(s) { return String(s).replace(/[&"'<>]/g, function(c) { return escMap[c]; }); }

    var rawCache = {};
    var hlCache = {};

    function getRawJson(col) {
        if (rawCache[col]) return rawCache[col];
        var pre = document.getElementById('viPre' + col);
        if (!pre) return '';
        rawCache[col] = pre.textContent;
        return rawCache[col];
    }

    function getHighlighted(col) {
        if (hlCache[col]) return hlCache[col];
        hlCache[col] = highlightJson(getRawJson(col));
        return hlCache[col];
    }

    // ── Loader helpers ────────────────────────────────────────────────────────
    function showLoader(label) {
        var el = document.getElementById('viLoader');
        if (!el) return;
        var lbl = el.querySelector('.vi-loader-label');
        if (lbl) lbl.textContent = label || 'Loading…';
        el.classList.add('show');
    }
    function hideLoader() {
        var el = document.getElementById('viLoader');
        if (el) el.classList.remove('show');
    }

    // ── Tab switching ─────────────────────────────────────────────────────────
    window.switchTab = function(col) {
        activeTab = col;
        document.querySelectorAll('.vi-tab').forEach(function(t) {
            t.classList.toggle('active', t.getAttribute('data-tab') === col);
        });
        document.querySelectorAll('.vi-json-pane').forEach(function(p) {
            p.classList.toggle('active', p.id === 'viPane' + col);
        });
        var input = document.getElementById('viSearchInput');
        if (input) { input.value = ''; input.focus(); }
        resetMatchNav();

        showLoader('Switching tab…');
        requestAnimationFrame(function() {
            setTimeout(function() {
                var pre = document.getElementById('viPre' + col);
                if (pre) pre.innerHTML = getHighlighted(col);
                hideLoader();
            }, 0);
        });
    };

    var _searchTimer = null;

    // ── Search + match navigation ─────────────────────────────────────────────
    window.filterActiveTab = function() {
        var q = document.getElementById('viSearchInput').value.trim();
        var activePane = document.querySelector('.vi-json-pane.active');
        if (!activePane) return;
        var pre = activePane.querySelector('pre');
        if (!pre) return;
        var col = activeTab;
        var raw = getRawJson(col);
        if (!raw) return;

        if (!q) {
            showLoader('Loading…');
            requestAnimationFrame(function() {
                setTimeout(function() {
                    pre.innerHTML = getHighlighted(col);
                    resetMatchNav();
                    hideLoader();
                }, 0);
            });
            return;
        }

        showLoader('Searching…');
        requestAnimationFrame(function() {
            setTimeout(function() {
                var lowerQ = q.toLowerCase();
                var lines = raw.split('\n');
                var matchElements = [];
                var total = 0;

                var html = lines.map(function(line, idx) {
                    var num = '<span class="vi-line-num">' + (idx + 1) + '</span>';
                    var lower = line.toLowerCase();
                    var ci = lower.indexOf(lowerQ);
                    if (ci === -1) return num + syntaxLine(line) + '\n';

                    total++;
                    var result = '';
                    var last = 0;
                    while (ci !== -1) {
                        result += syntaxLine(line.slice(last, ci));
                        var mid = line.slice(ci, ci + q.length);
                        var placeholder = '%%MATCH_' + (matchElements.length) + '%%';
                        result += placeholder;
                        matchElements.push({ matchText: mid, encoded: esc(mid), col: activeTab });
                        last = ci + q.length;
                        ci = lower.indexOf(lowerQ, last);
                    }
                    result += syntaxLine(line.slice(last));
                    return num + result + '\n';
                }).join('');

                // replace placeholders with actual spans
                matchElements.forEach(function(m, i) {
                    html = html.replace('%%MATCH_' + i + '%%',
                        '<span class="jv-highlight" data-match-idx="' + i + '" data-val="' + escAttr(m.matchText) + '">' + m.encoded + '</span>');
                });

                pre.innerHTML = html;

                viMatches = pre.querySelectorAll('.jv-highlight');
                viMatchIdx = viMatches.length > 0 ? 0 : -1;
                updateMatchUI(total);

                // scroll to first match
                if (viMatches.length > 0) {
                    scrollToMatch(viMatches[0]);
                }

                hideLoader();
            }, 0);
        });
    };

    function resetMatchNav() {
        viMatches = [];
        viMatchIdx = -1;
        var badge = document.getElementById('viMatchBadge');
        if (badge) badge.textContent = '';
        var prev = document.getElementById('viPrevBtn');
        var next = document.getElementById('viNextBtn');
        if (prev) prev.style.display = 'none';
        if (next) next.style.display = 'none';
    }

    function updateMatchUI(total) {
        var badge = document.getElementById('viMatchBadge');
        var prev = document.getElementById('viPrevBtn');
        var next = document.getElementById('viNextBtn');
        if (!badge) return;
        if (total === 0) {
            badge.textContent = 'No matches';
            if (prev) prev.style.display = 'none';
            if (next) next.style.display = 'none';
        } else {
            badge.textContent = (viMatchIdx + 1) + '/' + total;
            if (prev) prev.style.display = '';
            if (next) next.style.display = '';
        }
    }

    function scrollToMatch(el) {
        if (!el) return;
        el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        // brief focus glow
        el.style.outline = '2px solid #4f8cff';
        el.style.outlineOffset = '2px';
        setTimeout(function() {
            if (el) { el.style.outline = ''; el.style.outlineOffset = ''; }
        }, 1500);
        updateMatchUI(viMatches.length);
    }

    window.navMatch = function(dir) {
        if (!viMatches || viMatches.length === 0) return;
        viMatchIdx += dir;
        if (viMatchIdx < 0) viMatchIdx = viMatches.length - 1;
        if (viMatchIdx >= viMatches.length) viMatchIdx = 0;
        scrollToMatch(viMatches[viMatchIdx]);
    };

    window.searchKeydown = function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (e.shiftKey) {
                navMatch(-1);
            } else {
                navMatch(1);
            }
            return false;
        }
        return true;
    };

    // ── Click-to-fill ────────────────────────────────────────────────────────
    document.addEventListener('click', function(e) {
        var target = e.target.closest('.jv-clickable, .jv-highlight');
        if (!target) return;
        var val = target.getAttribute('data-val');
        if (val === null || val === undefined) return;
        fillBestMatch(val);
    });

    function fillBestMatch(val) {
        if (val === 'null' || val === 'undefined') return;
        var inputs = document.querySelectorAll('#viForm input[data-field], #viForm select[data-field]');
        var best = null, bestScore = 0;
        var lowerVal = val.toLowerCase().replace(/[^a-z0-9]/g, '');

        inputs.forEach(function(inp) {
            var field = inp.getAttribute('data-field');
            if (!field) return;
            var f = field.toLowerCase().replace(/_/g, '');
            if (f === lowerVal) { best = inp; bestScore = 100; return; }
            if (f.includes(lowerVal) || lowerVal.includes(f)) {
                var score = Math.min(f.length, lowerVal.length) / Math.max(f.length, lowerVal.length);
                if (score > bestScore) { bestScore = score; best = inp; }
            }
        });

        if (best && bestScore >= 0.5) {
            best.value = val;
            best.style.borderColor = '#4f8cff';
            best.scrollIntoView({ block: 'center', behavior: 'smooth' });
            setTimeout(function() { best.style.borderColor = ''; }, 2000);
            showToast('Filled "' + best.getAttribute('data-field') + '"', 'success');
        } else {
            showToast('No matching field for "' + val + '"', 'error');
        }
    }

    // ── Toast ─────────────────────────────────────────────────────────────────
    function showToast(msg, type) {
        var t = document.getElementById('viToast');
        if (!t) return;
        t.textContent = msg;
        t.className = 'vi-toast ' + type + ' show';
        setTimeout(function() { t.classList.remove('show'); }, 2500);
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        if (activeTab) {
            showLoader('Loading…');
            requestAnimationFrame(function() {
                setTimeout(function() {
                    var pre = document.getElementById('viPre' + activeTab);
                    if (pre) pre.innerHTML = getHighlighted(activeTab);
                    hideLoader();
                }, 0);
            });
        }

        var form = document.getElementById('viForm');
        var btn = document.getElementById('viSaveBtn');
        if (form) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:13px;height:13px;border-width:2px;"></span> Saving...';
            });
            @if (session('success'))
                showToast('{{ session('success') }}', 'success');
            @endif
        }
    });
})();
</script>
@endsection
