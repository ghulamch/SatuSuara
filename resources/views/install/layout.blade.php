<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Instalasi Awal') — SatuSuara Setup Wizard</title>
    <x-favicon />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg:        #F0F2F5;
            --card:      #FFFFFF;
            --border:    #D0D5DD;
            --border-hi: #98A2B3;
            --text:      #181D27;
            --text-sec:  #475467;
            --text-ph:   #667085;
            --accent:    #1E3A8A;
            --accent-h:  #172554;
            --accent-bg: #EFF6FF;
            --accent-bd: #BFDBFE;
            --ok:        #166534;
            --ok-bg:     #F0FDF4;
            --ok-bd:     #BBF7D0;
            --err:       #991B1B;
            --err-bg:    #FEF2F2;
            --err-bd:    #FCA5A5;
            --warn:      #854D0E;
            --warn-bg:   #FEFCE8;
            --warn-bd:   #FEF08A;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100vh;
            font-family: 'Jost', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        .installer-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        .installer-container {
            width: 100%;
            max-width: 640px;
        }

        /* Brand Header */
        .brand-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
            box-shadow: 0 1px 3px rgba(16,24,40,0.04);
            margin-bottom: 12px;
        }

        .brand-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .brand-sub {
            font-size: 14px;
            color: var(--text-sec);
            margin-top: 4px;
        }

        /* Step Indicator */
        .step-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px 18px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(16,24,40,0.03);
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-ph);
        }

        .step-item.active {
            color: var(--accent);
        }

        .step-item.passed {
            color: var(--ok);
        }

        .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11.5px;
            background: #F2F4F7;
            color: var(--text-ph);
        }

        .step-item.active .step-num {
            background: var(--accent);
            color: #FFFFFF;
        }

        .step-item.passed .step-num {
            background: var(--ok-bg);
            color: var(--ok);
            border: 1px solid var(--ok-bd);
        }

        .step-arrow {
            color: var(--border-hi);
            font-size: 12px;
        }

        /* Wizard Card */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px 28px;
            box-shadow: 0 4px 16px rgba(16,24,40,0.06);
        }

        .card-header {
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #F2F4F7;
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .card-desc {
            font-size: 13.5px;
            color: var(--text-sec);
            margin-top: 4px;
            line-height: 1.45;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(30,58,138,0.12);
        }

        .form-control.is-invalid {
            border-color: var(--err);
            background: #FFFDFD;
        }

        .form-text {
            font-size: 12px;
            color: var(--text-sec);
            margin-top: 4px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 20px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--accent);
            color: #FFFFFF;
        }
        .btn-primary:hover {
            background: var(--accent-h);
        }

        .btn-secondary {
            background: #FFFFFF;
            color: var(--text);
            border-color: var(--border);
        }
        .btn-secondary:hover {
            background: #F9FAFB;
            border-color: var(--border-hi);
        }

        .btn-success {
            background: var(--ok);
            color: #FFFFFF;
        }
        .btn-success:hover {
            background: #14532D;
        }

        /* Alert Box */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
            border: 1px solid;
        }
        .alert--ok { background: var(--ok-bg); border-color: var(--ok-bd); color: var(--ok); }
        .alert--err { background: var(--err-bg); border-color: var(--err-bd); color: var(--err); }
        .alert--warn { background: var(--warn-bg); border-color: var(--warn-bd); color: var(--warn); }

        /* Footer */
        .installer-footer {
            text-align: center;
            font-size: 12px;
            color: var(--text-ph);
            margin-top: 24px;
        }

        @media (max-width: 480px) {
            .card { padding: 24px 18px; }
            .brand-title { font-size: 22px; }
            .step-bar { font-size: 12px; padding: 10px 12px; }
            .step-title { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="installer-page">
    <div class="installer-container">
        
        <!-- Header Brand -->
        <div class="brand-header">
            <div class="brand-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span>SatuSuara Setup Wizard</span>
            </div>
            <h1 class="brand-title">Instalasi Sistem E-Voting</h1>
            <p class="brand-sub">Konfigurasi otomatis server, database, dan akun administrator.</p>
        </div>

        @if (session('error'))
            <div class="alert alert--err" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:2px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')

        <div class="installer-footer">
            SatuSuara E-Voting &bull; Open Source
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>

