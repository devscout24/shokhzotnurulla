<!DOCTYPE html>
<html lang="en">
@php
    $dealerLogo = $vehicle->dealer->logo ?? null;
    $dealerLogoUrl    = $dealerLogo ? asset('assets/frontend/img/logos/' . $dealerLogo) : null;
    $bbbLogoUrl       = asset('assets/Images/Logos/bbb.png');
    $carfaxLogoUrl    = asset('assets/Images/Logos/carfax-logo.png');
    $cargurusLogoUrl  = asset('assets/Images/Logos/car-gurus-2020.png');
    $footerBgImg      = asset('assets/Images/Backgrounds/cars-footer.png');
@endphp
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
            padding: 20px;
        }

        /* ── Outer wrapper (Acts as main row flex to let sidebar go full height) ── */
        .ws-wrap {
            width: 100%;
            border: 8px solid #4a4a4a;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            display: flex;
            min-height: 950px;
        }

        /* ── Left sidebar (Now covers 100% full height) ── */
        .ws-sidebar {
            width: 160px;
            min-width: 160px;
            background: linear-gradient(to bottom, #f5f5f5, #cfcfcf);
            border-right: 6px solid #4a4a4a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-around;
            padding: 40px 10px;
            gap: 40px;
        }

        .ws-badge-img {
            width: 120px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .ws-badge {
            width: 135px;
            background: #fff;
            border: 2px solid #666;
            border-radius: 8px;
            padding: 12px 6px;
            text-align: center;
        }

        /* ── Right Container (Header + Body + Footer stacked) ── */
        .ws-container-right {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* ── Header band ── */
        .ws-header {
            border-bottom: 6px solid #4a4a4a;
            padding: 20px;
            text-align: center;
            min-height: 125px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom, #e3e3e3, #b8b8b8);
            background-size: contain;
            background-position: center center;

            background-repeat: no-repeat;
            background-size: 100% 100%;

        }
        .ws-header {
            /* background-size: auto 85%; */
        }

        .ws-dealer-name {
            font-size: 28px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #111;
            font-family: 'Arial Black', sans-serif;
        }

        .ws-dealer-tagline {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* ── Vehicle title block ── */
        .ws-vehicle-title {
            padding: 20px 25px;
            text-align: center;
            border-bottom: 2px solid #4a4a4a;
        }

        .ws-vehicle-title h2 {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.4;
            color: #000;
            letter-spacing: 0.5px;
        }

        .ws-vehicle-title h3 {
            font-size: 15px;
            font-weight: 800;
            color: #222;
            margin-top: 5px;
            text-transform: uppercase;
        }

        /* ── Specs grid ── */
        .ws-specs {
            padding: 15px 35px;
            border-bottom: 4px solid #4a4a4a;
        }

        .ws-specs table {
            width: 100%;
            font-size: 14px;
            border-collapse: collapse;
        }

        .ws-specs td {
            padding: 6px 10px;
            vertical-align: middle;
            color: #111;
            width: 50%;
        }

        /* ── Features section ── */
        .ws-features {
            padding: 25px 35px;
            flex: 1;
            background: #fff;
        }

        .ws-features-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #000;
            letter-spacing: 0.5px;
        }

        .ws-features-grid {
            columns: 2;
            column-gap: 50px;
            padding: 0 10px;
        }

        .ws-features-grid li {
            font-size: 13px;
            color: #111;
            line-height: 1.9;
            break-inside: avoid;
            list-style: none;
            position: relative;
        }

        /* ── Disclaimer ── */
        .ws-disclaimer {
            padding: 15px 35px;
            font-size: 10px;
            color: #444;
            line-height: 1.5;
            border-top: 2px solid #e0e0e0;
            text-align: justify;
        }

        /* ── Footer ── */
        .ws-footer {
            border-top: 6px solid #4a4a4a;
            padding: 20px;
            text-align: center;
            background: linear-gradient(to bottom, #e3e3e3, #b8b8b8);
        }

        .ws-footer-title {
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            color: #000;
            letter-spacing: 0.5px;
        }

        .ws-footer-hours {
            font-size: 13px;
            font-weight: bold;
            color: #222;
            margin-top: 6px;
            line-height: 1.6;
        }

        .ws-footer-phone {
            font-size: 16px;
            font-weight: 900;
            margin-top: 6px;
            color: #000;
            letter-spacing: 0.5px;
        }

        /* ── Print button (screen only) ── */
        .ws-print-bar {
            background: #1a1a1a;
            padding: 12px 20px;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .ws-print-bar-title {
            color: #fff;
            font-size: 14px;
        }

        .ws-btn-print {
            background: #7b2cbf;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .ws-btn-print:hover {
            background: #6a1b9a;
        }

        @media print {
            .ws-print-bar { display: none !important; }
            body { padding: 0; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
    <style>
        .ws-wrap {
            border-radius: 16px;
            overflow: hidden;
        }
        .ws-header {
            border-top-right-radius: 8px;
        }

        .ws-sidebar {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .ws-footer {
            border-bottom-right-radius: 8px;
        }
    </style>
    <style>
        @media print {
            .ws-print-bar { display: none !important; }
            body { padding: 0; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
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

        {{-- ── Left Sidebar (Runs 100% full-height of the frame) ── --}}
        <div class="ws-sidebar">
            <div class="ws-badge">
                <img src="{{ $bbbLogoUrl }}" alt="BBB Accredited Business" class="ws-badge-img">
            </div>

            <div class="ws-badge">
                <img src="{{ $carfaxLogoUrl }}" alt="CARFAX Advantage Dealer" class="ws-badge-img">
            </div>

            <div class="ws-badge">
                <img src="{{ $cargurusLogoUrl }}" alt="CarGurus Top Rated Dealer" class="ws-badge-img">
            </div>
        </div>

        {{-- ── Right Container ── --}}
        <div class="ws-container-right">

            {{-- Header --}}
            <div class="ws-header" style="@if($dealerLogoUrl) background-image: url('{{ $dealerLogoUrl }}'); @endif">
                @if(!$dealerLogoUrl)
                    <div class="ws-dealer-name">{{ $vehicle->dealer->name ?? config('app.name') }}</div>
                    @if($vehicle->dealer->tagline ?? null)
                        <div class="ws-dealer-tagline">{{ $vehicle->dealer->tagline }}</div>
                    @endif
                @endif
            </div>

            {{-- Vehicle Title --}}
            <div class="ws-vehicle-title">
                <h2>
                    {{ $vehicle->year }} {{ strtoupper($vehicle->make->name) }} {{ strtoupper($vehicle->makeModel->name) }}
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
                        <td><strong>Mileage:</strong> {{ $vehicle->mileage ? number_format($vehicle->mileage) : '' }}</td>
                        <td><strong>Transmission:</strong> {{ $vehicle->transmissionType->name ?? 'Automatic' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Color:</strong> {{ $vehicle->exteriorColor->name ?? '' }}</td>
                        <td><strong>V.I.N.:</strong> {{ $vehicle->vin ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Stock No.:</strong> {{ $vehicle->stock_number ?? '' }}</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>

            {{-- Features ── --}}
            @php
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

            <div class="ws-features">
                <div class="ws-features-title">Comfort Equipment and Accessories</div>
                @if($allFeatures->isNotEmpty())
                    <ul class="ws-features-grid">
                        @foreach($allFeatures as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                @else
                    <p style="font-size:12px;color:#aaa;text-align:center;padding:20px 0;">
                        No features listed. Add factory options on the VDP to populate this section.
                    </p>
                @endif
            </div>

            {{-- Disclaimer ── --}}
            <div class="ws-disclaimer">
                It is the customer's sole responsibility to verify the existence and condition of any equipment listed.
                The dealership is not responsible for misprints on prices or equipment. It is the customer's sole
                responsibility to verify the accuracy of the prices with the dealer. Pricing subject to change without notice.
            </div>

            {{-- Footer ── --}}
            <div
    class="ws-footer"
    style="background-image:linear-gradient(rgba(255,255,255,0.8), rgba(255,255,255,0.8)),url('{{ $footerBgImg }}');background-size: cover;background-position: center;">
                <div class="ws-footer-title">Hours of Operation</div>
                <div class="ws-footer-hours">
                    Monday–Saturday &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 10 a.m. – 7 p.m.<br>
                    Sunday &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; By Appointment Only.
                </div>
                @if($vehicle->dealer->phone ?? null)
                    <div class="ws-footer-phone">{{ $vehicle->dealer->phone }}</div>
                @endif
            </div>

        </div>{{-- end .ws-container-right --}}
    </div>{{-- end .ws-wrap --}}

</body>
</html>
