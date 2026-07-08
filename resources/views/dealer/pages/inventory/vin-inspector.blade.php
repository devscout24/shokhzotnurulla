@extends('layouts.dealer.app')
@section('title', 'VIN Inspector | ' . __(config('app.name')))

@push('page-assets')
    @vite(['resources/css/dealer/pages/inventory-details.css'])
    <style>
        .vi-layout { display:flex; gap:0; height:calc(100vh - 134px); }
        .vi-left { width:40%; overflow-y:auto; border-right:1px solid #2d2d3a; padding:20px; }
        .vi-right { width:60%; overflow-y:auto; padding:20px; display:flex; flex-direction:column; }
        .vi-search { position:sticky; top:0; z-index:10; padding-bottom:12px; background:#1a1a27; }
        .vi-search input { width:100%; padding:8px 12px; border:1px solid #2d2d3a; border-radius:6px; background:#12121c; color:#e0e0e0; font-size:13px; }
        .vi-search input:focus { outline:none; border-color:#4f8cff; }
        .vi-field { display:flex; padding:4px 0; font-size:13px; border-bottom:1px solid #1e1e2e; align-items:center; }
        .vi-field .vi-label { width:38%; color:#8a8a9a; flex-shrink:0; }
        .vi-field .vi-input { width:62%; }
        .vi-field .vi-input input,
        .vi-field .vi-input select,
        .vi-field .vi-input textarea { width:100%; padding:3px 6px; border:1px solid #2d2d3a; border-radius:4px; background:#12121c; color:#e0e0e0; font-size:12px; font-family:monospace; box-sizing:border-box; }
        .vi-field .vi-input input:focus,
        .vi-field .vi-input select:focus { outline:none; border-color:#4f8cff; }
        .vi-field .vi-input textarea { resize:vertical; min-height:28px; }
        .vi-field .vi-input input[type="checkbox"] { width:auto; }
        .vi-section-title { font-size:13px; font-weight:600; color:#6b9aff; margin:16px 0 8px; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; justify-content:space-between; }
        .vi-json-block { margin-bottom:16px; }
        .vi-json-block h4 { font-size:13px; font-weight:600; color:#6b9aff; margin:0 0 6px; text-transform:uppercase; letter-spacing:0.5px; }
        .vi-json-block pre { background:#12121c; border:1px solid #2d2d3a; border-radius:6px; padding:12px; font-size:11px; line-height:1.5; color:#c9d1d9; overflow:auto; max-height:400px; margin:0; white-space:pre-wrap; word-break:break-word; cursor:default; }
        .vi-json-block pre .json-value { cursor:pointer; border-bottom:1px dashed #4f8cff; }
        .vi-json-block pre .json-value:hover { background:#1a3a5c; }
        .vi-empty { color:#5a5a6a; font-style:italic; font-size:13px; padding:20px 0; }
        .vi-highlight { background:#2d5a1e; border-radius:2px; padding:0 2px; }
        .vi-highlight-click { background:#2d5a1e; border-radius:2px; padding:0 2px; cursor:pointer; }
        .vi-highlight-click:hover { background:#3d7a2e; }
        .vi-match-count { font-size:12px; color:#6b9aff; margin-top:6px; }
        .vi-back { display:inline-flex; align-items:center; gap:6px; color:#8a8a9a; text-decoration:none; font-size:13px; margin-bottom:16px; }
        .vi-back:hover { color:#e0e0e0; }
        .vi-save-bar { position:sticky; top:0; z-index:20; background:#1a1a27; padding:10px 0 14px; display:flex; align-items:center; gap:12px; border-bottom:1px solid #2d2d3a; margin-bottom:12px; }
        .vi-save-bar .btn-save { padding:6px 20px; background:#4f8cff; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer; }
        .vi-save-bar .btn-save:hover { background:#3b7ae8; }
        .vi-save-bar .btn-save:disabled { opacity:0.5; cursor:not-allowed; }
        .vi-toast { position:fixed; top:20px; right:20px; z-index:9999; padding:12px 20px; border-radius:8px; font-size:14px; color:#fff; opacity:0; transition:opacity 0.3s; }
        .vi-toast.success { background:#1a6b3c; }
        .vi-toast.error { background:#6b1a1a; }
        .vi-toast.show { opacity:1; }
        .vi-copy-hint { font-size:11px; color:#5a5a6a; font-style:italic; margin-bottom:8px; }
    </style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent" style="padding:0;overflow:hidden;">
    <div class="view-content inventory-view" data-view="inventory" style="padding:0;">

        @include('dealer.partials.inventory-topbar')

        <div class="vi-layout">

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
                            <i class="bi bi-check-lg"></i> Save Changes
                        </button>
                        <span id="viSaveStatus" style="font-size:12px;color:#5a5a6a;"></span>
                    </div>

                    @php
                        $relationalLabels = [
                            'make_id' => 'Make',
                            'make_model_id' => 'Model',
                            'location_id' => 'Location',
                            'body_type_id' => 'Body Type',
                            'body_style_id' => 'Body Style',
                            'fuel_type_id' => 'Fuel Type',
                            'transmission_type_id' => 'Transmission',
                            'drivetrain_type_id' => 'Drivetrain',
                            'exterior_color_id' => 'Exterior Color',
                            'interior_color_id' => 'Interior Color',
                        ];
                    @endphp

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
                    @php
                        $priceValue = fn($key) => old($key, $vehicle->prices?->{$key});
                    @endphp
                    <div class="vi-field">
                        <span class="vi-label">MSRP</span>
                        <div class="vi-input"><input type="number" step="0.01" name="msrp" value="{{ $priceValue('msrp') }}" data-field="msrp"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Dealer Cost</span>
                        <div class="vi-input"><input type="number" step="0.01" name="dealer_cost" value="{{ $priceValue('dealer_cost') }}" data-field="dealer_cost"></div>
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
                        <div class="vi-input"><input type="number" step="0.01" name="internet_price" value="{{ $priceValue('internet_price') }}" data-field="internet_price"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Special Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="special_price" value="{{ $priceValue('special_price') }}" data-field="special_price"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Asking Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="asking_price" value="{{ $priceValue('asking_price') }}" data-field="asking_price"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Sold Price</span>
                        <div class="vi-input"><input type="number" step="0.01" name="sold_price" value="{{ $priceValue('sold_price') }}" data-field="sold_price"></div>
                    </div>

                    {{-- ── Specs ── --}}
                    @if ($vehicle->specs)
                    <div class="vi-section-title">Specs</div>
                    @php $specVal = fn($k) => old($k, $vehicle->specs?->{$k}); @endphp
                    <div class="vi-field">
                        <span class="vi-label">Cylinders</span>
                        <div class="vi-input"><input type="number" name="cylinders" value="{{ $specVal('cylinders') }}" data-field="cylinders"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Displacement (L)</span>
                        <div class="vi-input"><input type="number" step="0.1" name="displacement" value="{{ $specVal('displacement') }}" data-field="displacement"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Max Horsepower</span>
                        <div class="vi-input"><input type="number" name="max_horsepower" value="{{ $specVal('max_horsepower') }}" data-field="max_horsepower"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">HP @ RPM</span>
                        <div class="vi-input"><input type="number" name="max_horsepower_at" value="{{ $specVal('max_horsepower_at') }}" data-field="max_horsepower_at"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Max Torque</span>
                        <div class="vi-input"><input type="number" name="max_torque" value="{{ $specVal('max_torque') }}" data-field="max_torque"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Torque @ RPM</span>
                        <div class="vi-input"><input type="number" name="max_torque_at" value="{{ $specVal('max_torque_at') }}" data-field="max_torque_at"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Block Type</span>
                        <div class="vi-input"><input type="text" name="block_type" value="{{ $specVal('block_type') }}" data-field="block_type"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Trans Std</span>
                        <div class="vi-input"><input type="text" name="transmission_standard" value="{{ $specVal('transmission_standard') }}" data-field="transmission_standard"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Drivetrain Std</span>
                        <div class="vi-input"><input type="text" name="drivetrain_standard" value="{{ $specVal('drivetrain_standard') }}" data-field="drivetrain_standard"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">GVWR</span>
                        <div class="vi-input"><input type="number" name="gvwr" value="{{ $specVal('gvwr') }}" data-field="gvwr"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Empty Weight</span>
                        <div class="vi-input"><input type="number" name="empty_weight" value="{{ $specVal('empty_weight') }}" data-field="empty_weight"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Fuel Tank</span>
                        <div class="vi-input"><input type="number" step="0.1" name="fuel_tank" value="{{ $specVal('fuel_tank') }}" data-field="fuel_tank"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">MPG City</span>
                        <div class="vi-input"><input type="number" step="0.1" name="mpg_city" value="{{ $specVal('mpg_city') }}" data-field="mpg_city"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">MPG Highway</span>
                        <div class="vi-input"><input type="number" step="0.1" name="mpg_highway" value="{{ $specVal('mpg_highway') }}" data-field="mpg_highway"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Width</span>
                        <div class="vi-input"><input type="number" step="0.1" name="dimension_width" value="{{ $specVal('dimension_width') }}" data-field="dimension_width"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Length</span>
                        <div class="vi-input"><input type="number" step="0.1" name="dimension_length" value="{{ $specVal('dimension_length') }}" data-field="dimension_length"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Height</span>
                        <div class="vi-input"><input type="number" step="0.1" name="dimension_height" value="{{ $specVal('dimension_height') }}" data-field="dimension_height"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Wheelbase</span>
                        <div class="vi-input"><input type="number" step="0.1" name="wheelbase" value="{{ $specVal('wheelbase') }}" data-field="wheelbase"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Compression</span>
                        <div class="vi-input"><input type="number" step="0.1" name="compression" value="{{ $specVal('compression') }}" data-field="compression"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Engine Valves</span>
                        <div class="vi-input"><input type="number" name="engine_valves" value="{{ $specVal('engine_valves') }}" data-field="engine_valves"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Engine Model</span>
                        <div class="vi-input"><input type="text" name="engine_model" value="{{ $specVal('engine_model') }}" data-field="engine_model"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Front Tire</span>
                        <div class="vi-input"><input type="text" name="front_tire" value="{{ $specVal('front_tire') }}" data-field="front_tire"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Rear Tire</span>
                        <div class="vi-input"><input type="text" name="rear_tire" value="{{ $specVal('rear_tire') }}" data-field="rear_tire"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Front Wheel</span>
                        <div class="vi-input"><input type="text" name="front_wheel" value="{{ $specVal('front_wheel') }}" data-field="front_wheel"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Rear Wheel</span>
                        <div class="vi-input"><input type="text" name="rear_wheel" value="{{ $specVal('rear_wheel') }}" data-field="rear_wheel"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Towing Capacity</span>
                        <div class="vi-input"><input type="number" name="towing_capacity" value="{{ $specVal('towing_capacity') }}" data-field="towing_capacity"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Payload Capacity</span>
                        <div class="vi-input"><input type="number" name="payload_capacity" value="{{ $specVal('payload_capacity') }}" data-field="payload_capacity"></div>
                    </div>
                    <div class="vi-field">
                        <span class="vi-label">Axle Ratio</span>
                        <div class="vi-input"><input type="number" step="0.01" name="axle_ratio" value="{{ $specVal('axle_ratio') }}" data-field="axle_ratio"></div>
                    </div>
                    @endif

                </form>
            </div>

            {{-- ═══════ RIGHT: VIN Raw Data (click values to fill) ═══════ --}}
            <div class="vi-right">

                <div class="vi-search">
                    <input type="text" id="viSearchInput" placeholder="Search JSON — click a value to fill the matching field…" oninput="filterJson()">
                    <div id="viMatchCount" class="vi-match-count"></div>
                </div>

                <div class="vi-copy-hint">
                    <i class="bi bi-hand-index-thumb"></i> Click any <strong>"value"</strong> in the JSON to fill the corresponding field on the left. Or click a highlighted search match.
                </div>

                @php
                    $vinData = $vehicle->vinData;
                    $jsonColumns = ['vehicle_databases', 'default', 'data_one', 'custom'];
                @endphp

                @if (! $vinData)
                    <div class="vi-empty">No VIN decode data found for this vehicle.</div>
                @else
                    @foreach ($jsonColumns as $col)
                        @php $colData = $vinData->{$col}; @endphp
                        @if (! is_null($colData))
                            <div class="vi-json-block" data-json-col="{{ $col }}">
                                <h4>{{ $col }}</h4>
                                <pre class="vi-raw-json">{{ json_encode($colData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    @endforeach
                @endif

            </div>

        </div>
    </div>
</main>

<div id="viToast" class="vi-toast"></div>

<script>
(function() {
    var form = document.getElementById('viForm');
    var btn = document.getElementById('viSaveBtn');
    var status = document.getElementById('viSaveStatus');

    if (form) {
        form.addEventListener('submit', function() {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:13px;height:13px;border-width:2px;"></span> Saving...';
            status.textContent = 'Saving…';
        });

        @if (session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
    }

    function showToast(msg, type) {
        var t = document.getElementById('viToast');
        if (!t) return;
        t.textContent = msg;
        t.className = 'vi-toast ' + type + ' show';
        setTimeout(function() { t.classList.remove('show'); }, 3500);
    }

    // ── JSON click-to-fill ─────────────────────────────────────────────────────
    function makeJsonClickable() {
        var pres = document.querySelectorAll('.vi-raw-json');
        pres.forEach(function(pre) {
            var original = pre.getAttribute('data-original');
            if (!original) {
                original = pre.textContent;
                pre.setAttribute('data-original', original);
            }

            // parse once to get value mappings
            var parsed = null;
            try {
                var text = pre.textContent;
                // find the matching json and parse it
                var blocks = document.querySelectorAll('.vi-json-block');
                for (var i = 0; i < blocks.length; i++) {
                    var p = blocks[i].querySelector('.vi-raw-json');
                    if (p === pre) {
                        var col = blocks[i].getAttribute('data-json-col');
                        // We can't easily re-parse from the text, so we'll use a different approach
                        break;
                    }
                }
            } catch(e) {}

            // Instead of re-parsing, we'll add click handlers to individual key-value patterns
            // by wrapping values in clickable spans after rendering
            wrapJsonValues(pre);
        });
    }

    function wrapJsonValues(pre) {
        var html = pre.innerHTML;
        // Match JSON string values: "key": "value" or "key": number
        // We wrap the value part in a clickable span
        html = html.replace(/:(\s*)"([^"]+)"/g, function(match, space, val) {
            return ':<span class="json-value" data-val="' + escapeAttr(val) + '">"' + val + '"</span>';
        });
        html = html.replace(/:(\s*)(\d+(?:\.\d+)?)/g, function(match, space, val) {
            return ':<span class="json-value" data-val="' + val + '">' + val + '</span>';
        });
        pre.innerHTML = html;
    }

    function escapeAttr(str) {
        return str.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // Click handler for JSON values
    document.addEventListener('click', function(e) {
        var target = e.target.closest('.json-value, .vi-highlight, .vi-highlight-click');
        if (!target) return;

        var val = target.getAttribute('data-val');
        if (!val) {
            // try to extract from the match highlight
            val = target.textContent.trim();
        }

        // Guess the field name from context: look at the key name in the JSON line
        var pre = target.closest('pre');
        if (!pre) return;

        var lineEl = target.closest('span');
        if (!lineEl) return;

        // Walk back to find the key name
        var lineContent = pre.textContent;
        // Find what line this is on
        var allLines = pre.querySelectorAll('span, div');
        // Simpler: just use the text content and let user click on highlighted matches too

        // Try to find the field by data-field attribute matching the JSON key
        // The user can just click and we'll try to fill the best match
        fillBestMatch(val);
    });

    // For highlighted search matches, make them clickable too
    function makeHighlightsClickable() {
        document.querySelectorAll('.vi-highlight').forEach(function(el) {
            el.classList.add('vi-highlight-click');
            el.setAttribute('data-val', el.textContent.trim());
        });
    }

    function fillBestMatch(val) {
        if (!val || val === 'null' || val === 'undefined') return;

        // Find all input fields
        var inputs = document.querySelectorAll('#viForm input[data-field], #viForm select[data-field]');
        var bestInput = null;
        var bestScore = 0;

        var lowerVal = val.toLowerCase();

        inputs.forEach(function(inp) {
            var field = inp.getAttribute('data-field');
            if (!field) return;

            // Score: exact match on field name, or partial match
            var lowerField = field.toLowerCase().replace(/_/g, '');
            var searchVal = lowerVal.replace(/[^a-z0-9]/g, '');

            if (lowerField === searchVal) {
                bestScore = 100;
                bestInput = inp;
                return;
            }

            // Check if the field name contains the value or vice versa
            if (lowerField.includes(searchVal) || searchVal.includes(lowerField)) {
                var score = Math.min(lowerField.length, searchVal.length) / Math.max(lowerField.length, searchVal.length);
                if (score > bestScore) {
                    bestScore = score;
                    bestInput = inp;
                }
            }
        });

        if (bestInput && bestScore >= 0.5) {
            bestInput.value = val;
            bestInput.style.borderColor = '#4f8cff';
            setTimeout(function() { bestInput.style.borderColor = ''; }, 2000);
            showToast('Filled "' + bestInput.getAttribute('data-field') + '" with ' + val, 'success');
        } else {
            showToast('No matching field found for "' + val + '"', 'error');
        }
    }

    // Patch filterJson to re-apply clickable values after search highlighting
    var origFilter = window.filterJson;
    window.filterJson = function() {
        if (typeof origFilter === 'function') {
            // We need to override the behavior
        }
        _filterJson();
    };

    function _filterJson() {
        var query = document.getElementById('viSearchInput').value.toLowerCase().trim();
        var blocks = document.querySelectorAll('.vi-json-block');
        var totalMatches = 0;

        blocks.forEach(function(block) {
            var pre = block.querySelector('.vi-raw-json');
            if (!pre) return;

            var original = pre.getAttribute('data-original');
            if (!original) {
                // get inner text without HTML
                var temp = document.createElement('div');
                temp.appendChild(document.createTextNode(pre.textContent));
                original = temp.innerHTML;
                pre.setAttribute('data-original', original);
            }

            if (!query) {
                // restore with clickable values
                pre.innerHTML = original;
                wrapJsonValues(pre);
                block.style.display = '';
                return;
            }

            var lines = original.split('\n');
            var matchCount = 0;
            var htmlLines = lines.map(function(line) {
                var lower = line.toLowerCase();
                var idx = lower.indexOf(query);
                if (idx === -1) return line;

                matchCount++;
                var result = '';
                var last = 0;
                var lowerLine = lower;
                while (idx !== -1) {
                    result += line.substring(last, idx);
                    result += '<span class="vi-highlight-click" data-val="' + escapeAttr(line.substring(idx, idx + query.length)) + '">' + line.substring(idx, idx + query.length) + '</span>';
                    last = idx + query.length;
                    idx = lowerLine.indexOf(query, last);
                }
                result += line.substring(last);
                return result;
            });

            pre.innerHTML = htmlLines.join('\n');
            totalMatches += matchCount;

            var colLabel = block.querySelector('h4').textContent;
            var matchInfo = block.querySelector('.vi-match-info');
            if (!matchInfo) {
                matchInfo = document.createElement('div');
                matchInfo.className = 'vi-match-info';
                matchInfo.style.cssText = 'font-size:11px;color:#5a5a6a;margin-top:4px;';
                block.querySelector('h4').after(matchInfo);
            }
            matchInfo.textContent = matchCount + ' match' + (matchCount !== 1 ? 'es' : '');
        });

        document.getElementById('viMatchCount').textContent = query
            ? totalMatches + ' match' + (totalMatches !== 1 ? 'es' : '') + ' found'
            : '';
    }

    // Initialize clickable values on page load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(makeJsonClickable, 100);
    });

    // Also re-apply after search clears
    var searchInput = document.getElementById('viSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            if (!this.value.trim()) {
                setTimeout(makeJsonClickable, 50);
            }
        });
    }
})();
</script>
@endsection
