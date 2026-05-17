<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer's Guide — {{ $vehicle->year }} {{ $vehicle->make->name }} {{ $vehicle->makeModel->name }}</title>
    <style>
        @page { margin: 0.5cm; size: portrait; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Arial Black', Gadget, sans-serif;
            font-size: 14px;
            color: #000;
            background: #fff;
            padding: 10px;
        }
        .bg-container {
            width: 100%;
            border: 5px solid #000;
            padding: 20px;
            min-height: 1000px;
            position: relative;
        }
        .main-title {
            text-align: center;
            font-size: 50px;
            line-height: 1;
            margin-bottom: 20px;
            border-bottom: 5px solid #000;
            padding-bottom: 10px;
        }
        .sub-title {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .warranty-section {
            border: 2px solid #000;
            margin-bottom: 15px;
        }
        .warranty-header {
            background: #000;
            color: #fff;
            padding: 5px 10px;
            font-weight: 900;
            font-size: 20px;
        }
        .warranty-option {
            padding: 10px;
            border-bottom: 1px solid #000;
            display: flex;
            align-items: flex-start;
        }
        .checkbox {
            width: 30px;
            height: 30px;
            border: 2px solid #000;
            margin-right: 15px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 900;
        }
        .option-text {
            flex: 1;
        }
        .option-title {
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 5px;
        }
        .systems-covered {
            padding: 10px;
            font-size: 12px;
        }
        .info-section {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 15px;
        }
        .vehicle-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .dealer-info {
            margin-top: 30px;
            border: 2px solid #000;
            padding: 15px;
        }
        .dealer-title {
            font-weight: 900;
            margin-bottom: 5px;
        }
        .complaints-box {
            margin-top: 20px;
            font-size: 11px;
            line-height: 1.3;
        }
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-weight: 900;
        }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .bg-container { border-width: 8px; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">PRINT BUYER'S GUIDE</button>

    <div class="bg-container">
        <div class="main-title">BUYERS GUIDE</div>
        <div class="sub-title">IMPORTANT: Spoken promises are difficult to enforce. Ask the dealer to put all promises in writing. Keep this form.</div>

        <div class="vehicle-info">
            <div>{{ $vehicle->make->name }} {{ $vehicle->makeModel->name }}</div>
            <div>{{ $vehicle->year }}</div>
            <div>VIN: {{ $vehicle->vin }}</div>
        </div>

        <div class="warranty-section">
            <div class="warranty-header">WARRANTIES FOR THIS VEHICLE:</div>
            
            <div class="warranty-option">
                <div class="checkbox">X</div>
                <div class="option-text">
                    <div class="option-title">AS IS - NO DEALER WARRANTY</div>
                    <div>THE DEALER DOES NOT PROVIDE A WARRANTY FOR ANY REPAIRS AFTER SALE.</div>
                </div>
            </div>

            <div class="warranty-option">
                <div class="checkbox"></div>
                <div class="option-text">
                    <div class="option-title">DEALER WARRANTY</div>
                    <div style="margin-top: 5px;">
                        [ ] FULL WARRANTY<br>
                        [ ] LIMITED WARRANTY. The dealer will pay ____% of the labor and ____% of the parts for the covered systems that fail during the warranty period. Ask the dealer for a copy of the warranty document and for an explanation of warranty coverage, exclusions, and the dealer's repair obligations.
                    </div>
                </div>
            </div>
        </div>

        <div class="systems-covered">
            <strong>SYSTEMS COVERED:</strong><br>
            ____________________________________________________________________________________________________<br>
            ____________________________________________________________________________________________________<br><br>
            <strong>DURATION:</strong><br>
            ____________________________________________________________________________________________________
        </div>

        <div class="warranty-section" style="margin-top: 20px;">
            <div class="warranty-header">NON-DEALER WARRANTIES FOR THIS VEHICLE:</div>
            <div style="padding: 10px; font-size: 13px;">
                [ ] MANUFACTURER'S WARRANTY STILL APPLIES. The manufacturer's original warranty has not expired on some components of the vehicle.<br>
                [ ] OTHER USED VEHICLE WARRANTY APPLIES.
            </div>
        </div>

        <div class="complaints-box">
            SERVICE CONTRACT. A service contract is available at an extra charge on this vehicle. Ask for details as to coverage, deductible, price, and exclusions. If you buy a service contract within 90 days of your purchase of this vehicle, implied warranties under your state's laws may give you additional rights.
        </div>

        <div class="dealer-info">
            <div class="dealer-title">DEALER: {{ $vehicle->dealer->name ?? config('app.name') }}</div>
            <div>ADDRESS: {{ $vehicle->dealer->address ?? '________________________________________' }}</div>
            <div style="margin-top: 10px;">FOR COMPLAINTS OR AFTER SALE SERVICE, CONTACT:</div>
            <div>________________________________________________________________________</div>
        </div>

        <div class="complaints-box" style="margin-top: 30px; text-align: center; font-size: 10px;">
            SEE THE BACK OF THIS FORM for important additional information, including a list of some major defects that may occur in used motor vehicles.
        </div>
    </div>
</body>
</html>
