<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary:   #1a1f2e;
            --accent:    #e8b94f;
            --warning:   #f59e0b;
            --subtle:    #f5f6fa;
            --muted:     #8a94a6;
            --border:    #e2e6f0;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--subtle);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(26,31,46,.10);
            max-width: 520px;
            width: 100%;
            padding: 56px 48px 48px;
            text-align: center;
            border-top: 5px solid var(--warning);
        }

        .error-code {
            font-size: 96px;
            font-weight: 800;
            line-height: 1;
            color: var(--primary);
            letter-spacing: -4px;
        }

        .error-code span {
            color: var(--warning);
        }

        .error-icon {
            width: 72px;
            height: 72px;
            background: #fffbeb;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 32px;
            color: var(--warning);
        }

        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .error-desc {
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .btn-home {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 32px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .2s, transform .15s;
        }

        .btn-home:hover {
            background: var(--accent);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-back {
            color: var(--muted);
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 16px;
            transition: color .2s;
        }

        .btn-back:hover { color: var(--primary); }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 32px;
        }

        .brand-dot {
            width: 10px;
            height: 10px;
            background: var(--accent);
            border-radius: 50%;
        }

        .brand-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 28px 0;
        }

        .alert-info-box {
            background: #fffbeb;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: var(--warning);
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ref-id {
            font-size: 11px;
            color: var(--muted);
            margin-top: 24px;
            letter-spacing: .5px;
        }
    </style>
</head>
<body>
    <div class="error-card">

        {{-- Brand --}}
        <div class="brand">
            <div class="brand-dot"></div>
            <span class="brand-name">{{ config('app.name') }}</span>
        </div>

        {{-- Error Code --}}
        <div class="error-code">5<span>0</span>0</div>

        <hr class="divider">

        {{-- Icon --}}
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        {{-- Message --}}
        <div class="error-title">Something Went Wrong</div>
        <p class="error-desc">
            We're experiencing a technical issue on our end.<br>
            Our team has been notified. Please try again in a moment.
        </p>

        {{-- Info Box --}}
        <div class="alert-info-box">
            <i class="bi bi-tools"></i>
            This issue has been logged and our team is working on it.
        </div>

        {{-- Actions --}}
        <a href="{{ url('/') }}" class="btn-home">
            <i class="bi bi-house-door-fill"></i>
            Back to Dashboard
        </a>

        <br>

        <a href="javascript:history.back()" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            Try again
        </a>

        {{-- Reference ID for support --}}
        <p class="ref-id">
            Reference: {{ strtoupper(substr(md5(request()->url() . now()), 0, 8)) }}
        </p>

    </div>
</body>
</html>
