@php
    $creditScore = request()->query('credit', 'N/A');
    $price = is_numeric(request()->query('price')) ? (float)request()->query('price') : 0.0;
    $term = is_numeric(request()->query('term')) ? (int)request()->query('term') : 60;
    $rate = is_numeric(request()->query('rate')) ? (float)request()->query('rate') : 6.79;
    $downPct = is_numeric(request()->query('down')) ? (float)request()->query('down') : 0.0;
    $tradein = is_numeric(request()->query('tradein')) ? (float)request()->query('tradein') : 0.0;
    $balance = is_numeric(request()->query('balance')) ? (float)request()->query('balance') : 0.0;

    $vehicleTitle = request()->query('title', 'Selected vehicle');
    $stockNumber = request()->query('stock', 'N/A');
    $vin = request()->query('vin', 'N/A');
    $dealerName = app('currentDealer')?->company_name ?? config('app.name');
    
    // Estimator Down Payment Amount
    $estimatorDownAmount = $price * $downPct / 100;
    
    // Actual Cash Down Payment (default to the estimator down payment if not provided)
    $actualDownQuery = request()->query('actual_down');
    $actualDownAmount = is_numeric($actualDownQuery) ? (float)$actualDownQuery : $estimatorDownAmount;

    // Step 1: Net Trade-in equity
    $netTradeInEquity = max(0.0, $tradein - $balance);

    // Step 2: Estimator Loan Amount
    $estimatorLoanAmount = max(0.0, $price - $netTradeInEquity - $estimatorDownAmount);

    // Step 3: Missing Down payment balance
    $missingDownPaymentBalance = $estimatorDownAmount - $actualDownAmount;

    // Step 4: Financial Impact
    $newTrueLoanAmount = max(0.0, $price - $netTradeInEquity - $actualDownAmount);

    $monthlyRate = ($rate / 100) / 12;
    if ($monthlyRate > 0) {
        $estimatorMonthly = $term > 0 ? ($estimatorLoanAmount * $monthlyRate) / (1 - pow(1 + $monthlyRate, -$term)) : 0;
        $actualMonthly = $term > 0 ? ($newTrueLoanAmount * $monthlyRate) / (1 - pow(1 + $monthlyRate, -$term)) : 0;
    } else {
        $estimatorMonthly = $term > 0 ? $estimatorLoanAmount / $term : 0;
        $actualMonthly = $term > 0 ? $newTrueLoanAmount / $term : 0;
    }

    $paymentDifference = $actualMonthly - $estimatorMonthly;
    $totalPaymentDifference = ($actualMonthly * $term) - ($estimatorMonthly * $term);

    $now = now()->format('n/j/Y');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charSet="utf-8" data-next-head="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" class="" data-next-head="" />
    <meta property="og:url" content="https://undefined/print-deal" class="" data-next-head="" />
    <meta property="og:site_name" class="" data-next-head="" />
    <meta property="og:type" content="website" class="" data-next-head="" />
    <meta property="og:image" class="" data-next-head="" />
    <meta name="format-detection" content="telephone=no,address=no" class="" data-next-head="" />
    <meta name="generator" content="Overfuel" class="" data-next-head="" />
    <style id="__">
        @font-face {
            font-display: block;
            font-family: "Roboto";
            font-style: normal;
            font-weight: 400;
            src: url("/font/roboto-v48-latin-regular.woff2")format("woff2")
        }

        @font-face {
            font-display: block;
            font-family: "Roboto";
            font-style: normal;
            font-weight: 600;
            src: url("/font/roboto-v48-latin-600.woff2")format("woff2")
        }

        @font-face {
            font-display: block;
            font-family: "Roboto";
            font-style: normal;
            font-weight: 900;
            src: url("/font/roboto-v48-latin-900.woff2")format("woff2")
        }
    </style>
    <style id="__">
        :root {
            --color-primary: #166B87;
            --color-primary-hover: #11536b;
            --color-secondary: #323232;
            --color-bg-light: #f3f4f6;
            --color-card-bg: #ffffff;
            --color-text-dark: #1f2937;
            --color-text-muted: #6b7280;
            --color-border: #e5e7eb;
        }

        body {
            font-family: "Roboto", system-ui, Arial, sans-serif !important;
            background-color: var(--color-bg-light);
            margin: 0;
            padding: 0;
            color: var(--color-text-dark);
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar styling for interactive controls */
        .controls-sidebar {
            width: 360px;
            background-color: var(--color-card-bg);
            border-right: 1px solid var(--color-border);
            padding: 2rem;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-primary);
            margin: 0;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--color-primary);
            text-align: center;
        }

        .sidebar-section-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-secondary);
            margin-top: 0.75rem;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--color-border);
            padding-bottom: 0.25rem;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .control-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--color-text-dark);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .control-group input {
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px !important;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .control-group input:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(22, 107, 135, 0.15);
        }

        .btn-action {
            background-color: var(--color-primary);
            color: #ffffff;
            padding: 0.8rem;
            border: none;
            border-radius: 8px !important;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-action:hover {
            background-color: var(--color-primary-hover);
        }

        .btn-action:active {
            transform: scale(0.98);
        }

        /* Document Viewer styling */
        .document-view {
            flex: 1;
            padding: 2.5rem 2rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow-y: auto;
        }

        .print-container {
            background-color: var(--color-card-bg);
            width: 100%;
            max-width: 820px;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--color-border);
            box-sizing: border-box;
        }

        .print-header {
            border-bottom: 3px double var(--color-border);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .dealer-brand {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--color-text-dark);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 0.5rem 0;
            text-align: center;
        }

        .deal-meta {
            font-size: 0.9rem;
            color: var(--color-text-muted);
            text-align: center;
            margin: 0;
            line-height: 1.6;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--color-border);
            padding-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        /* Comparison Table styles */
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .comparison-table th {
            background-color: var(--color-primary);
            color: #ffffff;
            padding: 0.75rem;
            font-weight: 600;
            text-align: left;
        }

        .comparison-table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--color-border);
            color: var(--color-text-dark);
        }

        .comparison-table tr:hover {
            background-color: #f9fafb;
        }

        .comparison-table .highlight-row {
            background-color: #f0f9ff;
            font-weight: 700;
        }

        .comparison-table .highlight-row td {
            border-top: 2px solid var(--color-primary);
            border-bottom: 2px solid var(--color-primary);
        }

        /* Step Card style */
        .audit-step-card {
            background-color: #fafbfc;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .audit-step-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .audit-step-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--color-text-dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .step-num {
            background-color: var(--color-primary);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .audit-step-card p {
            margin: 0 0 0.75rem 0;
            font-size: 0.9rem;
            line-height: 1.45;
            color: var(--color-text-muted);
        }

        .audit-step-math {
            font-family: "Courier New", Courier, monospace;
            background-color: #f3f4f6;
            padding: 0.85rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #2b3a4a;
            white-space: pre;
            border-left: 4px solid var(--color-primary);
            overflow-x: auto;
            margin: 0.5rem 0;
        }

        .audit-step-explain {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--color-text-dark);
            margin: 0.75rem 0 0 0 !important;
            padding-top: 0.5rem;
            border-top: 1px dashed var(--color-border);
        }

        .audit-impact-box {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px dashed var(--color-border);
        }

        .impact-badge-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .badge-pill {
            padding: 0.3rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            display: inline-block;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-info {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .disclaimer {
            font-size: 0.8rem;
            color: var(--color-text-muted);
            text-align: center;
            line-height: 1.5;
            margin-top: 3rem;
            border-top: 1px solid var(--color-border);
            padding-top: 1.5rem;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .app-container {
                flex-direction: column;
            }
            .controls-sidebar {
                width: auto;
                border-right: none;
                border-bottom: 1px solid var(--color-border);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
            .document-view {
                padding: 1.5rem 1rem;
            }
            .print-container {
                padding: 1.5rem;
            }
        }

        /* Print Media Styles */
        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .app-container {
                display: block;
            }
            .document-view {
                padding: 0;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .audit-step-card {
                page-break-inside: avoid;
                border: 1px solid #cccccc !important;
                background-color: #ffffff !important;
            }
            .audit-step-math {
                background-color: #f5f5f5 !important;
                border-left: 4px solid #333333 !important;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Interactive Controls (Hidden in Print) -->
        <aside class="controls-sidebar no-print">
            <h2 class="sidebar-title">Deal Reconciliation</h2>
            
            <div class="sidebar-section-title">Scenario Variables</div>
            
            <div class="control-group">
                <label for="price-input">Unit Price ($)</label>
                <input type="number" id="price-input" value="{{ (int)$price }}" min="0">
            </div>
            
            <div class="control-group">
                <label for="credit-input">Credit Score</label>
                <input type="number" id="credit-input" value="{{ $creditScore === 'N/A' ? 740 : (int)$creditScore }}" min="400" max="850">
            </div>
            
            <div class="control-group">
                <label for="term-input">Term (Months)</label>
                <input type="number" id="term-input" value="{{ (int)$term }}" min="12" max="120">
            </div>
            
            <div class="control-group">
                <label for="rate-input">APR (%)</label>
                <input type="number" id="rate-input" value="{{ (float)$rate }}" step="0.01" min="0" max="30">
            </div>

            <div class="sidebar-section-title">Trade-In Details</div>
            
            <div class="control-group">
                <label for="tradein-input">Trade-In Value ($)</label>
                <input type="number" id="tradein-input" value="{{ (int)$tradein }}" min="0">
            </div>
            
            <div class="control-group">
                <label for="balance-input">Loan Balance on Trade ($)</label>
                <input type="number" id="balance-input" value="{{ (int)$balance }}" min="0">
            </div>

            <div class="sidebar-section-title">Down Payment Config</div>
            
            <div class="control-group">
                <label for="down-pct-input">Estimator Down %</label>
                <input type="number" id="down-pct-input" value="{{ (float)$downPct }}" min="0" max="100" step="0.1">
            </div>
            
            <div class="control-group">
                <label for="actual-down-input">Actual Cash Down ($)</label>
                <input type="number" id="actual-down-input" value="{{ (int)$actualDownAmount }}" min="0">
            </div>

            <button class="btn-action" onclick="window.print()" style="margin-top: 1rem;">
                <svg height="16" width="16" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 7V3h12v4H6zm12 2h1a3 3 0 0 1 3 3v5h-4v4H6v-4H2v-5a3 3 0 0 1 3-3h1v2H5a1 1 0 0 0-1 1v3h16v-3a1 1 0 0 0-1-1h-1V9zM8 19h8v-4H8v4z" />
                </svg>
                Print Deal Analysis
            </button>
        </aside>

        <!-- Printable Document View -->
        <main class="document-view">
            <div class="print-container">
                <div class="print-header">
                    <h1 style="font-size:24px; font-weight:700; color: #166B87; text-align:center; margin-bottom: 0.5rem;" class="">Down Payment Discrepancy Audit</h1>
                    <p class="dealer-brand">{{ $dealerName }}</p>
                    <p class="deal-meta">
                        Date: {{ $now }} | Vehicle: {{ $vehicleTitle }}<br />
                        Stock #: {{ $stockNumber }} | VIN: {{ $vin }} | Credit Score: <span id="credit-display">{{ $creditScore }}</span>
                    </p>
                </div>

                <!-- Side-by-Side Comparison -->
                <div class="section-title">1. Side-by-Side Scenario Comparison</div>
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th class="text-center">Online Estimator</th>
                            <th class="text-center">Actual Proposal</th>
                            <th class="text-center">Discrepancy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Unit Price</strong></td>
                            <td class="text-center" data-field="price-est">${{ number_format($price) }}</td>
                            <td class="text-center" data-field="price-act">${{ number_format($price) }}</td>
                            <td class="text-center" data-field="price-diff">$0</td>
                        </tr>
                        <tr>
                            <td><strong>Trade-In Value</strong></td>
                            <td class="text-center" data-field="tradein-est">${{ number_format($tradein) }}</td>
                            <td class="text-center" data-field="tradein-act">${{ number_format($tradein) }}</td>
                            <td class="text-center" data-field="tradein-diff">$0</td>
                        </tr>
                        <tr>
                            <td><strong>Loan Balance on Trade</strong></td>
                            <td class="text-center" data-field="balance-est">${{ number_format($balance) }}</td>
                            <td class="text-center" data-field="balance-act">${{ number_format($balance) }}</td>
                            <td class="text-center" data-field="balance-diff">$0</td>
                        </tr>
                        <tr>
                            <td><strong>Net Trade-In Equity</strong></td>
                            <td class="text-center" data-field="net-tradein-est">${{ number_format($netTradeInEquity) }}</td>
                            <td class="text-center" data-field="net-tradein-act">${{ number_format($netTradeInEquity) }}</td>
                            <td class="text-center" data-field="net-tradein-diff">$0</td>
                        </tr>
                        <tr>
                            <td><strong>Down Payment</strong></td>
                            <td class="text-center" data-field="down-est">${{ number_format($estimatorDownAmount) }} ({{ $downPct }}%)</td>
                            <td class="text-center" data-field="down-act">${{ number_format($actualDownAmount) }}</td>
                            <td class="text-center" data-field="down-diff" style="color: {{ $missingDownPaymentBalance > 0 ? '#b91c1c' : ($missingDownPaymentBalance < 0 ? '#15803d' : 'inherit') }}">
                                {{ $missingDownPaymentBalance > 0 ? 'Shortfall: $' . number_format(abs($missingDownPaymentBalance)) : ($missingDownPaymentBalance < 0 ? 'Surplus: $' . number_format(abs($missingDownPaymentBalance)) : '$0') }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Implied Loan Amount</strong></td>
                            <td class="text-center" data-field="loan-est">${{ number_format($estimatorLoanAmount) }}</td>
                            <td class="text-center" data-field="loan-act">${{ number_format($newTrueLoanAmount) }}</td>
                            <td class="text-center" data-field="loan-diff" style="color: {{ $newTrueLoanAmount > $estimatorLoanAmount ? '#b91c1c' : ($newTrueLoanAmount < $estimatorLoanAmount ? '#15803d' : 'inherit') }}">
                                {{ $newTrueLoanAmount > $estimatorLoanAmount ? '+$' . number_format($newTrueLoanAmount - $estimatorLoanAmount) : ($newTrueLoanAmount < $estimatorLoanAmount ? '-$' . number_format($estimatorLoanAmount - $newTrueLoanAmount) : '$0') }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Loan Term</strong></td>
                            <td class="text-center" data-field="term-est">{{ $term }} months</td>
                            <td class="text-center" data-field="term-act">{{ $term }} months</td>
                            <td class="text-center" data-field="term-diff">-</td>
                        </tr>
                        <tr>
                            <td><strong>APR</strong></td>
                            <td class="text-center" data-field="rate-est">{{ number_format($rate, 2) }}%</td>
                            <td class="text-center" data-field="rate-act">{{ number_format($rate, 2) }}%</td>
                            <td class="text-center" data-field="rate-diff">-</td>
                        </tr>
                        <tr class="highlight-row">
                            <td><strong>Est. Monthly Payment</strong></td>
                            <td class="text-center" data-field="monthly-est">${{ number_format($estimatorMonthly) }}/mo</td>
                            <td class="text-center" data-field="monthly-act">${{ number_format($actualMonthly) }}/mo</td>
                            <td class="text-center" data-field="monthly-diff" style="color: {{ $paymentDifference > 0 ? '#b91c1c' : ($paymentDifference < 0 ? '#15803d' : 'inherit') }}">
                                {{ $paymentDifference > 0 ? '+$' . number_format($paymentDifference) : ($paymentDifference < 0 ? '-$' . number_format(abs($paymentDifference)) : '$0') }}/mo
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Step-by-Step Math Audit -->
                <div class="section-title">2. Step-by-Step Finance Audit</div>

                <!-- Step 1: Net Trade-in Equity -->
                <div class="audit-step-card">
                    <div class="audit-step-title">
                        <span class="step-num">Step 1</span> Net Trade-In Equity Calculation
                    </div>
                    <p>Subtract the "Loan Balance on Trade" from the "Trade-In Value". If the result is negative or zero, the trade-in contributes $0 toward reducing the vehicle price.</p>
                    <div class="audit-step-math" id="step1-math">
  Trade-In Value:           ${{ number_format($tradein, 0) }}
- Loan Balance on Trade:   -${{ number_format($balance, 0) }}
------------------------------------------
= Net Trade-In Equity:      ${{ number_format($netTradeInEquity, 0) }}
                    </div>
                    <p class="audit-step-explain" id="step1-explain">
                        @if($tradein - $balance <= 0)
                            Since the loan balance on trade is equal to or greater than the trade-in value, there is no positive equity. The trade-in contributes $0 toward reducing the vehicle price.
                        @else
                            The trade-in value exceeds the loan balance, contributing ${{ number_format($netTradeInEquity) }} of net equity toward the purchase.
                        @endif
                    </p>
                </div>

                <!-- Step 2: Estimator Loan Amount -->
                <div class="audit-step-card">
                    <div class="audit-step-title">
                        <span class="step-num">Step 2</span> Estimator Loan Amount Calculation
                    </div>
                    <p>Calculate the implied loan amount the online estimator is using: [Unit Price] - [Net Trade-In Equity] - [Estimator Down Payment] = [Estimator Loan Amount]</p>
                    <div class="audit-step-math" id="step2-math">
  Unit Price:               ${{ number_format($price, 0) }}
- Net Trade-In Equity:     -${{ number_format($netTradeInEquity, 0) }}
- Estimator Down Payment:  -${{ number_format($estimatorDownAmount, 0) }} ({{ $downPct }}%)
------------------------------------------
= Estimator Loan Amount:    ${{ number_format($estimatorLoanAmount, 0) }}
                    </div>
                </div>

                <!-- Step 3: Discrepancy Reconciliation -->
                <div class="audit-step-card">
                    <div class="audit-step-title">
                        <span class="step-num">Step 3</span> Down Payment Discrepancy Reconciliation
                    </div>
                    <p>Compare the configured online "Estimator Down Payment" to the customer's "Actual Cash Down" payment to find the down payment balance discrepancy:</p>
                    <div class="audit-step-math" id="step3-math">
  Estimator Down Payment:   ${{ number_format($estimatorDownAmount, 0) }}
- Actual Cash Down:        -${{ number_format($actualDownAmount, 0) }}
------------------------------------------
= Missing Down Payment:     ${{ number_format($missingDownPaymentBalance, 0) }}
                    </div>
                    <p class="audit-step-explain" id="step3-explain">
                        @if($missingDownPaymentBalance > 0)
                            There is a <strong>down payment shortfall of ${{ number_format($missingDownPaymentBalance) }}</strong>. The customer is paying less cash upfront than estimated.
                        @elseif($missingDownPaymentBalance < 0)
                            There is a <strong>down payment surplus of ${{ number_format(abs($missingDownPaymentBalance)) }}</strong>. The customer is paying more cash upfront than estimated.
                        @else
                            The cash down payment matches the estimate exactly.
                        @endif
                    </p>
                </div>

                <!-- Step 4: Financial Impact Analysis -->
                <div class="audit-step-card">
                    <div class="audit-step-title">
                        <span class="step-num">Step 4</span> Financial Impact Analysis
                    </div>
                    <p>Determine if the Missing Down Payment Balance is rolled into the actual loan, compute the new true loan amount, and analyze monthly payment impacts:</p>
                    <div class="audit-step-math" id="step4-math">
  Unit Price:               ${{ number_format($price, 0) }}
- Net Trade-In Equity:     -${{ number_format($netTradeInEquity, 0) }}
- Actual Cash Down:        -${{ number_format($actualDownAmount, 0) }}
------------------------------------------
= New True Loan Amount:     ${{ number_format($newTrueLoanAmount, 0) }}
                    </div>
                    <div class="audit-impact-box" id="step4-impact">
                        @if($missingDownPaymentBalance > 0)
                            <div class="impact-badge-container">
                                <span class="badge-pill badge-warning">Loan Increase</span>
                            </div>
                            <p>Yes, the missing down payment balance of <strong>${{ number_format($missingDownPaymentBalance) }}</strong> is rolled into the actual loan, increasing the principal.</p>
                            <p>This shift increases your monthly payment from <strong>${{ number_format($estimatorMonthly) }}/mo</strong> to <strong>${{ number_format($actualMonthly) }}/mo</strong> (an increase of <strong>${{ number_format($paymentDifference) }}/mo</strong>) over the <strong>{{ $term }}</strong> month term.</p>
                            <p>This down payment reduction will cost an additional <strong>${{ number_format($totalPaymentDifference) }}</strong> in total payments over the life of the loan due to interest compounding at <strong>{{ number_format($rate, 2) }}% APR</strong>.</p>
                        @elseif($missingDownPaymentBalance < 0)
                            <div class="impact-badge-container">
                                <span class="badge-pill badge-success">Loan Decrease</span>
                            </div>
                            <p>No, there is no missing down payment to roll. Instead, the extra cash down payment of <strong>${{ number_format(abs($missingDownPaymentBalance)) }}</strong> reduces the loan principal.</p>
                            <p>This shift decreases your monthly payment from <strong>${{ number_format($estimatorMonthly) }}/mo</strong> to <strong>${{ number_format($actualMonthly) }}/mo</strong> (a savings of <strong>${{ number_format(abs($paymentDifference)) }}/mo</strong>) over the <strong>{{ $term }}</strong> month term.</p>
                            <p>This will save you <strong>${{ number_format(abs($totalPaymentDifference)) }}</strong> in total payments over the life of the loan at <strong>{{ number_format($rate, 2) }}% APR</strong>.</p>
                        @else
                            <div class="impact-badge-container">
                                <span class="badge-pill badge-info">No Loan Shift</span>
                            </div>
                            <p>The actual cash down payment matches the estimator setup. There is no missing down payment balance to roll into the loan.</p>
                            <p>The monthly payment remains at <strong>${{ number_format($estimatorMonthly) }}/mo</strong> for the <strong>{{ $term }}</strong> month term at <strong>{{ number_format($rate, 2) }}% APR</strong>.</p>
                        @endif
                    </div>
                </div>

                <p class="disclaimer">This is an estimate. Final terms are subject to dealer and lender approval and do not include title, tax, or licensing fees. Calculations are based on amortized monthly calculations.</p>
            </div>
        </main>
    </div>

    <!-- JS Calculator Engine -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elements
            const inputs = {
                price: document.getElementById('price-input'),
                credit: document.getElementById('credit-input'),
                term: document.getElementById('term-input'),
                rate: document.getElementById('rate-input'),
                tradein: document.getElementById('tradein-input'),
                balance: document.getElementById('balance-input'),
                downPct: document.getElementById('down-pct-input'),
                actualDown: document.getElementById('actual-down-input')
            };

            const displays = {
                priceEst: document.querySelector('[data-field="price-est"]'),
                priceAct: document.querySelector('[data-field="price-act"]'),
                priceDiff: document.querySelector('[data-field="price-diff"]'),
                
                downEst: document.querySelector('[data-field="down-est"]'),
                downAct: document.querySelector('[data-field="down-act"]'),
                downDiff: document.querySelector('[data-field="down-diff"]'),
                
                tradeinEst: document.querySelector('[data-field="tradein-est"]'),
                tradeinAct: document.querySelector('[data-field="tradein-act"]'),
                tradeinDiff: document.querySelector('[data-field="tradein-diff"]'),
                
                balanceEst: document.querySelector('[data-field="balance-est"]'),
                balanceAct: document.querySelector('[data-field="balance-act"]'),
                balanceDiff: document.querySelector('[data-field="balance-diff"]'),
                
                netTradeinEst: document.querySelector('[data-field="net-tradein-est"]'),
                netTradeinAct: document.querySelector('[data-field="net-tradein-act"]'),
                netTradeinDiff: document.querySelector('[data-field="net-tradein-diff"]'),
                
                loanEst: document.querySelector('[data-field="loan-est"]'),
                loanAct: document.querySelector('[data-field="loan-act"]'),
                loanDiff: document.querySelector('[data-field="loan-diff"]'),
                
                termEst: document.querySelector('[data-field="term-est"]'),
                termAct: document.querySelector('[data-field="term-act"]'),
                termDiff: document.querySelector('[data-field="term-diff"]'),
                
                rateEst: document.querySelector('[data-field="rate-est"]'),
                rateAct: document.querySelector('[data-field="rate-act"]'),
                rateDiff: document.querySelector('[data-field="rate-diff"]'),
                
                monthlyEst: document.querySelector('[data-field="monthly-est"]'),
                monthlyAct: document.querySelector('[data-field="monthly-act"]'),
                monthlyDiff: document.querySelector('[data-field="monthly-diff"]'),
                
                creditDisplay: document.getElementById('credit-display'),
                
                step1Math: document.getElementById('step1-math'),
                step1Explain: document.getElementById('step1-explain'),
                step2Math: document.getElementById('step2-math'),
                step3Math: document.getElementById('step3-math'),
                step3Explain: document.getElementById('step3-explain'),
                step4Math: document.getElementById('step4-math'),
                step4Impact: document.getElementById('step4-impact')
            };

            const formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                maximumFractionDigits: 0
            });

            function money(value) {
                return formatter.format(Math.max(0, Math.round(value)));
            }

            function moneySigned(value) {
                const prefix = value > 0 ? '+' : (value < 0 ? '-' : '');
                return prefix + formatter.format(Math.abs(Math.round(value)));
            }

            function calculate() {
                // Read values
                const price = parseFloat(inputs.price.value) || 0;
                const credit = parseInt(inputs.credit.value) || 740;
                const term = parseInt(inputs.term.value) || 60;
                const rate = parseFloat(inputs.rate.value) || 0;
                const tradein = parseFloat(inputs.tradein.value) || 0;
                const balance = parseFloat(inputs.balance.value) || 0;
                const downPct = parseFloat(inputs.downPct.value) || 0;
                const actualDown = parseFloat(inputs.actualDown.value) || 0;

                // Calculations
                const estimatorDownAmount = price * downPct / 100;
                const netTradeInEquity = Math.max(0, tradein - balance);
                const estimatorLoanAmount = Math.max(0, price - netTradeInEquity - estimatorDownAmount);
                const missingDownPaymentBalance = estimatorDownAmount - actualDown;
                const newTrueLoanAmount = Math.max(0, price - netTradeInEquity - actualDown);

                const monthlyRate = (rate / 100) / 12;
                let estimatorMonthly = 0;
                let actualMonthly = 0;

                if (monthlyRate > 0) {
                    estimatorMonthly = term > 0 ? (estimatorLoanAmount * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -term)) : 0;
                    actualMonthly = term > 0 ? (newTrueLoanAmount * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -term)) : 0;
                } else {
                    estimatorMonthly = term > 0 ? estimatorLoanAmount / term : 0;
                    actualMonthly = term > 0 ? newTrueLoanAmount / term : 0;
                }

                const paymentDifference = actualMonthly - estimatorMonthly;
                const totalPaymentDifference = (actualMonthly * term) - (estimatorMonthly * term);

                // Update DOM Comparison Table
                displays.priceEst.textContent = money(price);
                displays.priceAct.textContent = money(price);
                displays.priceDiff.textContent = money(0);

                displays.downEst.textContent = money(estimatorDownAmount) + ` (${downPct}%)`;
                displays.downAct.textContent = money(actualDown);
                if (missingDownPaymentBalance > 0) {
                    displays.downDiff.textContent = 'Shortfall: ' + money(missingDownPaymentBalance);
                    displays.downDiff.style.color = '#b91c1c';
                } else if (missingDownPaymentBalance < 0) {
                    displays.downDiff.textContent = 'Surplus: ' + money(Math.abs(missingDownPaymentBalance));
                    displays.downDiff.style.color = '#15803d';
                } else {
                    displays.downDiff.textContent = money(0);
                    displays.downDiff.style.color = 'inherit';
                }

                displays.tradeinEst.textContent = money(tradein);
                displays.tradeinAct.textContent = money(tradein);
                displays.tradeinDiff.textContent = money(0);

                displays.balanceEst.textContent = money(balance);
                displays.balanceAct.textContent = money(balance);
                displays.balanceDiff.textContent = money(0);

                displays.netTradeinEst.textContent = money(netTradeInEquity);
                displays.netTradeinAct.textContent = money(netTradeInEquity);
                displays.netTradeinDiff.textContent = money(0);

                displays.loanEst.textContent = money(estimatorLoanAmount);
                displays.loanAct.textContent = money(newTrueLoanAmount);
                
                const loanDifference = newTrueLoanAmount - estimatorLoanAmount;
                if (loanDifference !== 0) {
                    displays.loanDiff.textContent = moneySigned(loanDifference);
                    displays.loanDiff.style.color = loanDifference > 0 ? '#b91c1c' : '#15803d';
                } else {
                    displays.loanDiff.textContent = money(0);
                    displays.loanDiff.style.color = 'inherit';
                }

                displays.termEst.textContent = term + ' months';
                displays.termAct.textContent = term + ' months';
                displays.termDiff.textContent = '-';

                displays.rateEst.textContent = rate.toFixed(2) + '%';
                displays.rateAct.textContent = rate.toFixed(2) + '%';
                displays.rateDiff.textContent = '-';

                displays.monthlyEst.textContent = money(estimatorMonthly) + '/mo';
                displays.monthlyAct.textContent = money(actualMonthly) + '/mo';
                
                if (paymentDifference !== 0) {
                    displays.monthlyDiff.textContent = moneySigned(paymentDifference) + '/mo';
                    displays.monthlyDiff.style.color = paymentDifference > 0 ? '#b91c1c' : '#15803d';
                } else {
                    displays.monthlyDiff.textContent = money(0) + '/mo';
                    displays.monthlyDiff.style.color = 'inherit';
                }

                if (displays.creditDisplay) displays.creditDisplay.textContent = credit;

                // Update DOM Step 1
                const tradeinDiffRaw = tradein - balance;
                displays.step1Math.textContent = 
`  Trade-In Value:           ${money(tradein).padStart(10)}
- Loan Balance on Trade:   -${money(balance).padStart(10)}
------------------------------------------
= Net Trade-In Equity:      ${money(netTradeInEquity).padStart(10)}`;

                displays.step1Explain.innerHTML = tradeinDiffRaw <= 0
                    ? `Since the loan balance on trade (${money(balance)}) is equal to or greater than the trade-in value (${money(tradein)}), there is no positive equity. The trade-in contributes $0 toward reducing the vehicle price.`
                    : `The trade-in value exceeds the loan balance, contributing <strong>${money(netTradeInEquity)}</strong> of net equity toward the purchase.`;

                // Update DOM Step 2
                displays.step2Math.textContent =
`  Unit Price:               ${money(price).padStart(10)}
- Net Trade-In Equity:     -${money(netTradeInEquity).padStart(10)}
- Estimator Down Payment:  -${money(estimatorDownAmount).padStart(10)} (${downPct}%)
------------------------------------------
= Estimator Loan Amount:    ${money(estimatorLoanAmount).padStart(10)}`;

                // Update DOM Step 3
                displays.step3Math.textContent =
`  Estimator Down Payment:   ${money(estimatorDownAmount).padStart(10)}
- Actual Cash Down:        -${money(actualDown).padStart(10)}
------------------------------------------
= Missing Down Payment:     ${money(missingDownPaymentBalance).padStart(10)}`;

                if (missingDownPaymentBalance > 0) {
                    displays.step3Explain.innerHTML = `There is a <strong>down payment shortfall of ${money(missingDownPaymentBalance)}</strong>. The customer is paying less cash upfront than estimated.`;
                } else if (missingDownPaymentBalance < 0) {
                    displays.step3Explain.innerHTML = `There is a <strong>down payment surplus of ${money(Math.abs(missingDownPaymentBalance))}</strong>. The customer is paying more cash upfront than estimated.`;
                } else {
                    displays.step3Explain.innerHTML = `The cash down payment matches the estimate exactly.`;
                }

                // Update DOM Step 4
                displays.step4Math.textContent =
`  Unit Price:               ${money(price).padStart(10)}
- Net Trade-In Equity:     -${money(netTradeInEquity).padStart(10)}
- Actual Cash Down:        -${money(actualDown).padStart(10)}
------------------------------------------
= New True Loan Amount:     ${money(newTrueLoanAmount).padStart(10)}`;

                let impactHTML = '';
                if (missingDownPaymentBalance > 0) {
                    impactHTML = `
                        <div class="impact-badge-container">
                            <span class="badge-pill badge-warning">Loan Increase</span>
                        </div>
                        <p>Yes, the missing down payment balance of <strong>${money(missingDownPaymentBalance)}</strong> is rolled into the actual loan, increasing the principal.</p>
                        <p>This shift increases your monthly payment from <strong>${money(estimatorMonthly)}/mo</strong> to <strong>${money(actualMonthly)}/mo</strong> (an increase of <strong>${money(paymentDifference)}/mo</strong>) over the <strong>${term}</strong> month term.</p>
                        <p>This down payment reduction will cost an additional <strong>${money(totalPaymentDifference)}</strong> in total payments over the life of the loan due to interest compounding at <strong>${rate.toFixed(2)}% APR</strong>.</p>
                    `;
                } else if (missingDownPaymentBalance < 0) {
                    impactHTML = `
                        <div class="impact-badge-container">
                            <span class="badge-pill badge-success">Loan Decrease</span>
                        </div>
                        <p>No, there is no missing down payment to roll. Instead, the extra cash down payment of <strong>${money(Math.abs(missingDownPaymentBalance))}</strong> reduces the loan principal.</p>
                        <p>This shift decreases your monthly payment from <strong>${money(estimatorMonthly)}/mo</strong> to <strong>${money(actualMonthly)}/mo</strong> (a savings of <strong>${money(Math.abs(paymentDifference))}/mo</strong>) over the <strong>${term}</strong> month term.</p>
                        <p>This will save you <strong>${money(Math.abs(totalPaymentDifference))}</strong> in total payments over the life of the loan at <strong>${rate.toFixed(2)}% APR</strong>.</p>
                    `;
                } else {
                    impactHTML = `
                        <div class="impact-badge-container">
                            <span class="badge-pill badge-info">No Loan Shift</span>
                        </div>
                        <p>The actual cash down payment matches the estimator setup. There is no missing down payment balance to roll into the loan.</p>
                        <p>The monthly payment remains at <strong>${money(estimatorMonthly)}/mo</strong> for the <strong>${term}</strong> month term at <strong>${rate.toFixed(2)}% APR</strong>.</p>
                    `;
                }
                displays.step4Impact.innerHTML = impactHTML;

                // Sync query parameters in print link / URL
                const params = new URLSearchParams(window.location.search);
                params.set('price', price);
                params.set('credit', credit);
                params.set('term', term);
                params.set('rate', rate);
                params.set('down', downPct);
                params.set('tradein', tradein);
                params.set('balance', balance);
                params.set('actual_down', actualDown);
                window.history.replaceState({}, '', window.location.pathname + '?' + params.toString());
            }

            // Attach listeners
            Object.values(inputs).forEach(input => {
                if (input) {
                    input.addEventListener('input', calculate);
                    input.addEventListener('change', calculate);
                }
            });

            // Trigger initial calculation
            calculate();

            // Print dialog trigger (only if autoprint is true or actual_down is not yet set)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === 'true' || !urlParams.has('actual_down')) {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>

</html>
