<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Window Sticker — {{ $vehicle->year }} {{ $vehicle->make->name }} {{ $vehicle->makeModel->name }}</title>
    <style>
        @page { margin: 0.5cm; size: {{ $printable->layout === 'landscape' ? 'landscape' : 'portrait' }}; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #111;
            background: #fff;
            padding: 12px;
        }

        /* ── Outer wrapper ── */
        .ws-wrap {
            width: 100%;
            border: 3px solid #888;
            border-radius: 4px;
            overflow: hidden;
        }

        /* ── Two-column layout ── */
        .ws-body {
            display: flex;
            min-height: 800px;
        }

        /* ── Left sidebar ── */
        .ws-sidebar {
            width: 130px;
            min-width: 130px;
            background: linear-gradient(to bottom, #e0e0e0, #c8c8c8);
            border-right: 2px solid #999;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 18px 10px;
            gap: 20px;
        }

        .ws-badge-img {
            width: 90px;
            height: auto;
            display: block;
        }

        .ws-badge-placeholder {
            width: 90px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px 6px;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            color: #333;
            line-height: 1.4;
        }

        /* ── Main content ── */
        .ws-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0;
        }

        /* ── Header band ── */
        .ws-header {
            background: linear-gradient(135deg, #b0b0b0, #d8d8d8);
            border-bottom: 2px solid #999;
            padding: 12px 18px;
            text-align: center;
        }

        .ws-dealer-name {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #111;
        }

        .ws-dealer-tagline {
            font-size: 11px;
            color: #444;
            margin-top: 2px;
        }

        /* ── Vehicle title block ── */
        .ws-vehicle-title {
            padding: 14px 18px 10px;
            border-bottom: 1px solid #ddd;
        }

        .ws-vehicle-title h2 {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.4;
            color: #111;
        }

        .ws-vehicle-title h3 {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-top: 3px;
        }

        /* ── Specs grid ── */
        .ws-specs {
            padding: 10px 18px;
            border-bottom: 1px solid #ddd;
        }

        .ws-specs table {
            width: 100%;
            font-size: 13px;
            border-collapse: collapse;
        }

        .ws-specs td {
            padding: 3px 8px 3px 0;
            vertical-align: top;
            color: #222;
            width: 50%;
        }

        .ws-specs td strong {
            font-weight: 600;
        }

        /* ── Features section ── */
        .ws-features {
            padding: 10px 18px;
            flex: 1;
        }

        .ws-features-title {
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ws-features-grid {
            columns: 2;
            column-gap: 20px;
        }

        .ws-features-grid li {
            font-size: 12px;
            color: #333;
            line-height: 1.7;
            break-inside: avoid;
            list-style: none;
            padding-left: 10px;
            position: relative;
        }

        .ws-features-grid li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #666;
        }

        /* ── Disclaimer ── */
        .ws-disclaimer {
            padding: 8px 18px 12px;
            font-size: 10px;
            color: #777;
            line-height: 1.5;
            border-top: 1px solid #eee;
        }

        /* ── Footer ── */
        .ws-footer {
            background: linear-gradient(135deg, #b0b0b0, #d8d8d8);
            border-top: 2px solid #999;
            padding: 10px 18px;
            text-align: center;
        }

        .ws-footer-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ws-footer-hours {
            font-size: 12px;
            color: #333;
            margin-top: 4px;
            line-height: 1.6;
        }

        .ws-footer-phone {
            font-size: 13px;
            font-weight: 700;
            margin-top: 4px;
            color: #111;
        }

        /* ── Print button (screen only) ── */
        .ws-print-bar {
            background: #1a1a1a;
            padding: 10px 18px;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ws-print-bar-title {
            color: #ccc;
            font-size: 13px;
        }

        .ws-btn-print {
            background: #c0392b;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .ws-btn-print:hover {
            background: #a93226;
        }

        @media print {
            .ws-print-bar { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    {{-- Print bar (screen only — hidden on print) --}}
    <div class="ws-print-bar">
        <span class="ws-print-bar-title">
            Window Sticker — {{ $vehicle->year }} {{ $vehicle->make->name }} {{ $vehicle->makeModel->name }}
        </span>
        <button type="button" class="ws-btn-print" onclick="window.print()">
            🖨️ Print / Save as PDF
        </button>
    </div>

    <div class="ws-wrap">
        <div class="ws-body">

            {{-- ── Left Sidebar ── --}}
            <div class="ws-sidebar">
                {{-- BBB placeholder --}}
                <div class="ws-badge-placeholder">
                    <div style="font-size:20px;font-weight:900;">BBB</div>
                    <div>ACCREDITED<br>BUSINESS</div>
                </div>

                {{-- CARFAX placeholder --}}
                <div class="ws-badge-placeholder">
                    <div style="font-size:10px;font-weight:900;letter-spacing:1px;">CARFAX</div>
                    <div style="font-size:9px;margin-top:3px;">ADVANTAGE<br>DEALER</div>
                </div>

                {{-- CarGurus placeholder --}}
                <div class="ws-badge-placeholder">
                    <div style="font-size:9px;font-style:italic;font-weight:700;">CarGurus</div>
                    <div style="font-size:8px;margin-top:2px;">TOP RATED<br>DEALER</div>
                </div>
            </div>

            {{-- ── Main Content ── --}}
            <div class="ws-main">

                {{-- Header --}}
                <div class="ws-header">
                    <div class="ws-dealer-name">{{ $vehicle->dealer->name ?? config('app.name') }}</div>
                    @if($vehicle->dealer->tagline ?? null)
                        <div class="ws-dealer-tagline">{{ $vehicle->dealer->tagline }}</div>
                    @endif
                </div>

                {{-- Vehicle Title --}}
                <div class="ws-vehicle-title">
                    <h2>
                        {{ $vehicle->year }}
                        {{ strtoupper($vehicle->make->name) }}
                        {{ strtoupper($vehicle->makeModel->name) }}
                        @if($vehicle->trim)
                            {{ strtoupper($vehicle->trim) }}
                        @endif
                    </h2>
                    @if($vehicle->engine)
                        <h3>{{ strtoupper($vehicle->engine) }}</h3>
                    @endif
                </div>

                {{-- Specs ── --}}
                <div class="ws-specs">
                    <table>
                        <tr>
                            @if($vehicle->mileage)
                            <td><strong>Mileage:</strong> {{ number_format($vehicle->mileage) }}</td>
                            @endif
                            @if($vehicle->transmissionType)
                            <td><strong>Transmission:</strong> {{ $vehicle->transmissionType->name }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if($vehicle->exteriorColor)
                            <td><strong>Color:</strong> {{ $vehicle->exteriorColor->name }}</td>
                            @endif
                            @if($vehicle->vin)
                            <td><strong>V.I.N.:</strong> {{ $vehicle->vin }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if($vehicle->stock_number)
                            <td><strong>Stock No.:</strong> {{ $vehicle->stock_number }}</td>
                            @endif
                            @if($vehicle->drivetrainType)
                            <td><strong>Drivetrain:</strong> {{ $vehicle->drivetrainType->name }}</td>
                            @endif
                        </tr>
                        @if($vehicle->specs && ($vehicle->specs->max_horsepower || $vehicle->specs->cylinders))
                        <tr>
                            @if($vehicle->specs->cylinders)
                            <td><strong>Cylinders:</strong> {{ $vehicle->specs->cylinders }}</td>
                            @endif
                            @if($vehicle->specs->max_horsepower)
                            <td><strong>Horsepower:</strong> {{ number_format($vehicle->specs->max_horsepower) }} hp</td>
                            @endif
                        </tr>
                        @endif
                    </table>
                </div>

                {{-- Features ── --}}
                @php
                    // Factory options — show labels as feature list
                    $features = $vehicle->factoryOptions
                        ->where('pivot.is_starred', false)
                        ->pluck('label')
                        ->filter()
                        ->values();

                    $starredFeatures = $vehicle->factoryOptions
                        ->where('pivot.is_starred', true)
                        ->pluck('label')
                        ->filter()
                        ->values();

                    $allFeatures = $starredFeatures->merge($features);
                @endphp

                @if($allFeatures->isNotEmpty())
                <div class="ws-features">
                    <div class="ws-features-title">Comfort Equipment and Accessories</div>
                    <ul class="ws-features-grid">
                        @foreach($allFeatures as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                @else
                <div class="ws-features">
                    <div class="ws-features-title">Comfort Equipment and Accessories</div>
                    <p style="font-size:12px;color:#aaa;text-align:center;padding:20px 0;">
                        No features listed. Add factory options on the VDP to populate this section.
                    </p>
                </div>
                @endif

                {{-- Disclaimer ── --}}
                <div class="ws-disclaimer">
                    It is the customer's sole responsibility to verify the existence and condition of any equipment listed.
                    The dealership is not responsible for misprints on prices or equipment. It is the customer's sole
                    responsibility to verify the accuracy of the prices with the dealer. Pricing subject to change without notice.
                </div>

            </div>{{-- end .ws-main --}}
        </div>{{-- end .ws-body --}}

        {{-- Footer ── --}}
        <div class="ws-footer">
            <div class="ws-footer-title">Hours of Operation</div>
            @php $dealer = $vehicle->dealer; @endphp
            <div class="ws-footer-hours">
                Monday–Saturday: 10 a.m. – 7 p.m. &nbsp;|&nbsp; Sunday: By Appointment Only.
            </div>
            @if($dealer->phone ?? null)
                <div class="ws-footer-phone">{{ $dealer->phone }}</div>
            @endif
        </div>

    </div>{{-- end .ws-wrap --}}

</body>
</html>
