<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Quote — {{ $vehicle->year }} {{ $vehicle->make->name }} {{ $vehicle->makeModel->name }}</title>
    <style>
        @page { margin: 0.5cm; size: portrait; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            background: #fff;
            padding: 20px;
            line-height: 1.5;
        }
        .quote-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #e5e7eb;
            padding: 40px;
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .dealer-info h1 {
            font-size: 24px;
            font-weight: 800;
            color: #111;
            text-transform: uppercase;
        }
        .dealer-info p {
            color: #6b7280;
            font-size: 12px;
        }
        .quote-title {
            text-align: right;
        }
        .quote-title h2 {
            font-size: 20px;
            font-weight: 700;
            color: #c0392b;
            text-transform: uppercase;
        }
        .quote-date {
            font-size: 12px;
            color: #6b7280;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            color: #374151;
            background: #f9fafb;
            padding: 8px 12px;
            margin: 20px 0 15px;
            border-left: 4px solid #c0392b;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .info-group {
            margin-bottom: 10px;
        }
        .info-label {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 14px;
            font-weight: 500;
            color: #111;
        }
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .pricing-table td {
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .pricing-table .label {
            font-weight: 500;
            color: #4b5563;
        }
        .pricing-table .value {
            text-align: right;
            font-weight: 600;
            font-size: 15px;
        }
        .pricing-table .total-row td {
            border-top: 2px solid #111;
            border-bottom: none;
            padding-top: 15px;
        }
        .pricing-table .total-label {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .pricing-table .total-value {
            font-size: 20px;
            font-weight: 800;
            color: #c0392b;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
        }
        .signature-lines {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .sig-box {
            width: 45%;
            border-top: 1px solid #111;
            padding-top: 10px;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #c0392b;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .quote-container { border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print Quote</button>

    <div class="quote-container">
        <div class="header">
            <div class="dealer-info">
                <h1>{{ $vehicle->dealer->name ?? config('app.name') }}</h1>
                <p>{{ $vehicle->dealer->address ?? 'Dealer Address Not Set' }}</p>
                <p>{{ $vehicle->dealer->phone ?? 'Phone Not Set' }} | {{ $vehicle->dealer->website ?? 'Website Not Set' }}</p>
            </div>
            <div class="quote-title">
                <h2>Purchase Quote</h2>
                <p class="quote-date">Date: {{ now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="section-title">Vehicle Information</div>
        <div class="grid">
            <div class="info-group">
                <div class="info-label">Vehicle</div>
                <div class="info-value">{{ $vehicle->year }} {{ $vehicle->make->name }} {{ $vehicle->makeModel->name }} {{ $vehicle->trim }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">VIN</div>
                <div class="info-value">{{ $vehicle->vin }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Stock Number</div>
                <div class="info-value">{{ $vehicle->stock_number }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Mileage</div>
                <div class="info-value">{{ number_format($vehicle->mileage) }} miles</div>
            </div>
            <div class="info-group">
                <div class="info-label">Exterior Color</div>
                <div class="info-value">{{ $vehicle->exteriorColor->name ?? 'N/A' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Engine</div>
                <div class="info-value">{{ $vehicle->engine ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="section-title">Pricing Breakdown</div>
        <table class="pricing-table">
            <tr>
                <td class="label">Vehicle List Price</td>
                <td class="value">${{ number_format($vehicle->list_price, 2) }}</td>
            </tr>
            @if($vehicle->prices && $vehicle->prices->special_price)
            <tr>
                <td class="label">Dealer Discount</td>
                <td class="value">-${{ number_format($vehicle->list_price - $vehicle->prices->special_price, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Documentation & Admin Fees</td>
                <td class="value">$0.00</td>
            </tr>
            <tr>
                <td class="label">Tax, Title & License (Estimated)</td>
                <td class="value">$0.00</td>
            </tr>
            <tr class="total-row">
                <td class="total-label">Total Purchase Price</td>
                <td class="total-value">${{ number_format($vehicle->prices->special_price ?? $vehicle->list_price, 2) }}</td>
            </tr>
        </table>

        <div class="section-title">Notes & Terms</div>
        <p style="font-size: 12px; color: #4b5563; margin-bottom: 10px;">
            This quote is valid for 48 hours from the date shown above. Vehicle availability is subject to change without notice. 
            All prices and specifications are subject to verification. Final price may vary based on final equipment, taxes, and fees.
        </p>

        <div class="signature-lines">
            <div class="sig-box">Customer Signature</div>
            <div class="sig-box">Dealer Representative Signature</div>
        </div>

        <div class="footer">
            Generated by {{ config('app.name') }} Management System.
        </div>
    </div>
</body>
</html>
