<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk ke Akun — SatuSuara</title>
    <x-favicon />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            --err:       #991B1B;
            --err-bg:    #FEF2F2;
            --err-bd:    #FCA5A5;
            --err-txt:   #7F1D1D;
            --ok:        #166534;
            --ok-bg:     #F0FDF4;
            --ok-bd:     #BBF7D0;
            --ok-txt:    #14532D;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100%;
            width: 100%;
            overflow-x: hidden;
            font-family: 'Jost', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        ::selection { background: #BFDBFE; color: var(--text); }
        input { caret-color: var(--accent); }

        @media (prefers-reduced-motion: reduce) {
            * { animation: none !important; transition: none !important; }
        }

        /* ── Page Layout ── */
        .page {
            min-height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 16px 48px;
            gap: 22px;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        /* ── Institution Header Brand ── */
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            color: var(--text);
            border-radius: var(--radius-sm);
            max-width: 420px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            padding: 0;
            box-sizing: border-box;
            transition: opacity 0.15s;
        }
        .brand:hover { opacity: 0.95; }
        .brand:focus-visible { outline: 2px solid var(--accent); outline-offset: 4px; }

        .brand-icon {
            width: 72px;
            height: 72px;
            background: #FFFFFF;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(16,24,40,0.06), 0 1px 2px rgba(16,24,40,0.04);
            border: 1px solid var(--border);
            overflow: hidden;
            padding: 4px;
        }

        .brand-name {
            font-size: 19px;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.25;
            color: var(--text);
        }
        .brand-sub {
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text-sec);
            margin-top: 3px;
            line-height: 1.35;
        }

        /* ── Login Container Card ── */
        .card {
            width: 100%;
            max-width: 420px;
            min-width: 0;
            margin-left: auto;
            margin-right: auto;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px 28px 26px;
            box-sizing: border-box;
            box-shadow:
                0 1px 3px rgba(16,24,40,0.04),
                0 4px 16px rgba(16,24,40,0.06);
        }

        .card-header {
            margin-bottom: 22px;
        }

        .card-title {
            font-size: 21px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--text);
            line-height: 1.25;
            margin-bottom: 6px;
        }

        .card-sub {
            font-size: 13.5px;
            color: var(--text-sec);
            line-height: 1.45;
        }

        /* ── Feedback & Alerts ── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
            border-width: 1px;
            border-style: solid;
        }
        .alert svg { flex-shrink: 0; margin-top: 2px; }
        .alert--ok  { background: var(--ok-bg);  border-color: var(--ok-bd);  color: var(--ok-txt); }
        .alert--err { background: var(--err-bg); border-color: var(--err-bd); color: var(--err-txt); }
        .alert--err strong { font-weight: 600; color: var(--err); }

        /* ── Form Controls & Fields ── */
        .field {
            margin-bottom: 16px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
            letter-spacing: -0.01em;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            color: var(--text-ph);
        }

        .text-input {
            width: 100%;
            height: 44px;
            padding: 0 14px 0 42px;
            background: #FAFAFB;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
            appearance: none;
        }
        .text-input::placeholder { color: var(--text-ph); }
        .text-input:hover { border-color: var(--border-hi); }
        .text-input:focus {
            background: #FFFFFF;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12);
        }
        .text-input.err {
            border-color: var(--err);
            background: #FFFDFD;
        }
        .text-input.err:focus {
            box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.12);
        }
        .text-input--pr { padding-right: 48px; }

        /* Accessible 44px eye toggle */
        .input-eye {
            position: absolute;
            right: 0;
            top: 0;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-ph);
            border-radius: var(--radius-sm);
            transition: color .15s ease;
        }
        .input-eye:hover { color: var(--text-sec); }
        .input-eye:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: -2px;
        }

        /* ── Accessible Remember Option ── */
        .row-opt {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 2px;
            margin-bottom: 22px;
        }

        .check-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            cursor: pointer;
            user-select: none;
        }
        .check-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid var(--border-hi);
            accent-color: var(--accent);
            cursor: pointer;
            flex-shrink: 0;
        }
        .check-label:focus-within input[type="checkbox"] {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }
        .check-text {
            font-size: 13px;
            color: var(--text-sec);
            font-weight: 500;
        }

        /* ── Primary Submit Button ── */
        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 44px;
            background: var(--accent);
            border: none;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 600;
            color: #FFFFFF;
            cursor: pointer;
            letter-spacing: -0.01em;
            box-shadow: 0 1px 2px rgba(16,24,40,0.08);
            transition: background-color .15s ease, transform .1s ease, box-shadow .15s ease;
        }
        .btn-submit:hover {
            background: var(--accent-h);
            box-shadow: 0 2px 4px rgba(16,24,40,0.12);
        }
        .btn-submit:active { transform: scale(0.99); }
        .btn-submit:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
        }

        /* ── Public Cryptographic Receipt Link ── */
        .receipt-action {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #F2F4F7;
            text-align: center;
        }
        .receipt-link {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-sec);
            text-decoration: none;
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            max-width: 100%;
            min-width: 0;
            white-space: normal;
            text-align: center;
            line-height: 1.35;
            transition: color .15s ease, background-color .15s ease;
        }
        .receipt-link span {
            word-break: break-word;
        }
        .receipt-link:hover {
            color: var(--accent);
            background-color: var(--accent-bg);
        }
        .receipt-link:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        /* ── Institutional Footer ── */
        .foot {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-ph);
            text-align: center;
            max-width: 420px;
            min-width: 0;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
            line-height: 1.4;
        }
        .foot svg { flex-shrink: 0; opacity: 0.8; }
        .foot-sep { color: var(--border-hi); }

        /* ── Mobile Optimization (<540px) ── */
        @media (max-width: 540px) {
            .page {
                padding: 16px 12px 28px;
                gap: 14px;
                width: 100%;
                max-width: 100vw;
                box-sizing: border-box;
                overflow-x: hidden;
            }
            .brand {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 10px;
                max-width: 420px;
                min-width: 0;
                width: 100%;
                margin-left: auto;
                margin-right: auto;
                padding: 0;
                box-sizing: border-box;
            }
            .brand-icon {
                width: 60px;
                height: 60px;
                margin: 0 auto;
                border-radius: 12px;
            }
            .brand-name {
                font-size: 16px;
                line-height: 1.3;
                word-break: break-word;
            }
            .brand-sub {
                font-size: 11.5px;
                margin-top: 2px;
                line-height: 1.35;
                word-break: break-word;
            }
            .card {
                padding: 20px 14px 18px;
                border-radius: 14px;
                width: 100%;
                max-width: 420px;
                min-width: 0;
                margin-left: auto;
                margin-right: auto;
                box-sizing: border-box;
            }
            .card-title {
                font-size: 18px;
            }
            .card-sub {
                font-size: 12.5px;
            }
            .text-input {
                font-size: 13.5px;
            }
            .foot {
                flex-direction: column;
                gap: 4px;
                font-size: 11px;
                width: 100%;
                max-width: 420px;
                min-width: 0;
                margin-left: auto;
                margin-right: auto;
                padding: 0 4px;
                box-sizing: border-box;
            }
            .foot-sep {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- Top Institution Brand Header -->
    <a href="{{ url('/') }}" class="brand" aria-label="Beranda Portal E-Voting">
        <div class="brand-icon">
            @if (config('election.logo_instansi') && file_exists(public_path(config('election.logo_instansi'))))
                <img src="{{ asset(config('election.logo_instansi')) }}" alt="Logo Instansi" style="width:100%; height:100%; object-fit:contain;">
            @else
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            @endif
        </div>
        <div>
            <div class="brand-name">{{ config('election.institution') ?: 'SatuSuara E-Voting' }}</div>
            <div class="brand-sub">{{ config('election.election_name') ?: 'Sistem Pemilihan Umum Digital Resmi' }}</div>
        </div>
    </a>

    <!-- Main Authentication Card -->
    <main class="card">
        <div class="card-header">
            <h1 class="card-title">Masuk ke Akun</h1>
            <p class="card-sub">Masukkan alamat email dan kata sandi yang terdaftar pada DPT atau akun panitia.</p>
        </div>

        @if (session('status'))
            <div class="alert alert--ok" role="status">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert--err" role="alert" aria-live="assertive">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p style="margin:0 0 3px;">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" novalidate>
            @csrf

            <!-- Email / Identity -->
            <div class="field">
                <label class="field-label" for="email">Alamat Email Terdaftar</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                    </span>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="nama@instansi.sch.id"
                        class="text-input {{ $errors->has('email') ? 'err' : '' }}"
                    >
                </div>
            </div>

            <!-- Password -->
            <div class="field">
                <label class="field-label" for="password">Kata Sandi</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi Anda"
                        class="text-input text-input--pr {{ $errors->has('password') ? 'err' : '' }}"
                    >
                    <button type="button" id="togglePwd" class="input-eye" aria-label="Tampilkan kata sandi">
                        <svg id="eyeOn" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eyeOff" style="display:none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/>
                            <path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/>
                            <path d="m9 18 .722-3.25"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="row-opt">
                <label class="check-label">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="check-text">Ingat sesi di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                <span>Masuk ke Sistem</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                </svg>
            </button>
        </form>

        <!-- Public Receipt Verification Link -->
        <div class="receipt-action">
            <a href="{{ route('verify-receipt') }}" class="receipt-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
                <span>Cek Validitas Struk Kriptografi Suara</span>
            </a>
        </div>
    </main>

    <!-- Institutional Footer (Zero Tech Jargon) -->
    <footer class="foot">
        <span>&copy; {{ date('Y') }} {{ config('election.institution') ?: 'SatuSuara' }}</span>
    </footer>

</div>

<script>
(function () {
    var btn = document.getElementById('togglePwd');
    var inp = document.getElementById('password');
    var on  = document.getElementById('eyeOn');
    var off = document.getElementById('eyeOff');
    if (!btn || !inp) return;
    btn.addEventListener('click', function () {
        var isPassword = inp.type === 'password';
        inp.type = isPassword ? 'text' : 'password';
        on.style.display  = isPassword ? 'none' : '';
        off.style.display = isPassword ? ''     : 'none';
        btn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
})();
</script>
</body>
</html>
