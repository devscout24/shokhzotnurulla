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
            font-size: 14px;
            margin-bottom: 20px;
            text-transform: uppercase;
            line-height: 1.4;
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
        .warranty-option:last-child {
            border-bottom: none;
        }
        .warranty-sub-option {
            display: flex;
            align-items: flex-start;
            margin-top: 10px;
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
        .checkbox.small {
            width: 22px;
            height: 22px;
            font-size: 16px;
            margin-right: 10px;
            border-width: 2px;
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
            margin-top: 20px;
            border: 2px solid #000;
            padding: 15px;
        }
        .dealer-title {
            font-weight: 900;
            margin-bottom: 5px;
        }
        .two-col-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 5px;
        }
        .complaints-box {
            margin-top: 15px;
            font-size: 11px;
            line-height: 1.3;
        }
        .service-contract-container {
            display: flex;
            align-items: flex-start;
            margin-top: 15px;
        }
        .signature-section {
            margin-top: 30px;
            padding-top: 10px;
        }
        .signature-title {
            font-size: 12px;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .sig-block {
            width: 45%;
        }
        .line {
            border-bottom: 2px solid #000;
            height: 30px;
            margin-bottom: 5px;
        }
        .labels {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 900;
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
            z-index: 9999;
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

        <!-- DEALER WARRANTIES SECTION -->
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

                    <div class="warranty-sub-option">
                        <div class="checkbox small"></div>
                        <div><strong>FULL WARRANTY</strong></div>
                    </div>

                    <div class="warranty-sub-option">
                        <div class="checkbox small"></div>
                        <div>
                            <strong>LIMITED WARRANTY.</strong> The dealer will pay ____% of the labor and ____% of the parts for the covered systems that fail during the warranty period. Ask the dealer for a copy of the warranty document and for an explanation of warranty coverage, exclusions, and the dealer's repair obligations.
                        </div>
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

        <!-- NON-DEALER WARRANTIES SECTION -->
        <div class="warranty-section" style="margin-top: 20px;">
            <div class="warranty-header">NON-DEALER WARRANTIES FOR THIS VEHICLE:</div>
            <div style="padding: 10px;">
                <div class="warranty-sub-option" style="margin-top: 5px;">
                    <div class="checkbox small"></div>
                    <div><strong>MANUFACTURER'S WARRANTY STILL APPLIES.</strong> The manufacturer's original warranty has not expired on some components of the vehicle.</div>
                </div>
                <div class="warranty-sub-option">
                    <div class="checkbox small"></div>
                    <div><strong>MANUFACTURER'S USED VEHICLE WARRANTY APPLIES.</strong></div>
                </div>
                <div class="warranty-sub-option">
                    <div class="checkbox small"></div>
                    <div><strong>OTHER USED VEHICLE WARRANTY APPLIES.</strong></div>
                </div>
            </div>
        </div>

        <!-- SERVICE CONTRACT SECTION WITH REAL CHECKBOX -->
        <div class="service-contract-container">
            <div class="checkbox small" style="margin-top: 3px;"></div>
            <div class="complaints-box" style="margin-top: 0; font-size: 13px;">
                <strong>SERVICE CONTRACT.</strong> A service contract is available at an extra charge on this vehicle.
                Ask for details as to coverage, deductible, price,
                and exclusions. If you buy a service contract within 90 days of your purchase of this vehicle,
                implied warranties under your state's laws may give you additional rights.
            </div>
        </div>

       <div class="dealer-info">
    <div class="dealer-title">DEALER: {{ $vehicle->dealer->name ?? config('app.name') }}</div>

    <div style="display: flex; gap: 5px; margin-bottom: 10px;">
        <span>ADDRESS:</span>
        <span style="flex: 1; border-bottom: 1px solid #000; padding-bottom: 2px;">{{ $vehicle->dealer->address ?? '' }}</span>
    </div>

    <div class="two-col-grid" style="margin-bottom: 15px;">
        <div style="display: flex; gap: 5px;">
            <span>TELEPHONE:</span>
            <span style="flex: 1; border-bottom: 1px solid #000; padding-bottom: 2px;">{{ $vehicle->dealer->phone ?? '' }}</span>
        </div>
        <div style="display: flex; gap: 5px;">
            <span>EMAIL:</span>
            <span style="flex: 1; border-bottom: 1px solid #000; padding-bottom: 2px;"></span>
        </div>
    </div>

    <div style="margin-top: 20px;">
        FOR COMPLAINTS OR AFTER SALE SERVICE, CONTACT:
        <div style="border-bottom: 1px solid #000; height: 30px; margin-top: 5px;"></div>
    </div>
</div>

        <!-- ACKNOWLEDGEMENT AND SIGNATURE BLOCKS -->
        <div class="signature-section">
            <div class="signature-title"><strong>I hereby acknowledge receipt of the Buyers Guide at the closing of this sale.</strong></div>

            <div class="signature-row">
                <div class="sig-block">
                    <div class="line"></div>
                    <div class="labels">
                        <span>BUYER SIGNATURE</span>
                        <span>DATE</span>
                    </div>
                </div>
                <div class="sig-block">
                    <div class="line"></div>
                    <div class="labels">
                        <span>CO-BUYER SIGNATURE</span>
                        <span>DATE</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="complaints-box" style="margin-top: 20px; text-align: center; font-size: 10px;">
            SEE THE BACK OF THIS FORM for important additional information, including a list of some major defects that may occur in used motor vehicles.
        </div>
    </div>
</body>
</html>
