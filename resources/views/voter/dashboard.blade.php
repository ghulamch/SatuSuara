<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bilik Suara Digital — SatuSuara</title>
    <x-favicon />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:        #EDEEF1;
            --card:      #FFFFFF;
            --border:    #E0E2E8;
            --border-hi: #B8BCC8;
            --text:      #1C1F2E;
            --text-sec:  #6B7080;
            --text-ph:   #A8ACBA;
            --accent:    #3D5A99;
            --accent-h:  #334D84;
            --accent-bg: #EEF1F9;
            --accent-lt: #6079B2;
            --err:       #B91C1C;
            --err-bg:    #FEF2F2;
            --err-bd:    #FECACA;
            --ok:        #166534;
            --ok-bg:     #F0FDF4;
            --ok-bd:     #BBF7D0;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100vh;
            font-family: 'Jost', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        ::selection { background: #C7D3F0; color: var(--text); }

        /* Top Navbar */
        .topbar {
            background: #FFFFFF;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        .topbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--text);
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            background: var(--accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid var(--border);
        }

        .brand-name {
            font-size: 18.5px;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
            color: var(--text);
        }

        .brand-sub {
            font-size: 13px;
            color: var(--text-sec);
            letter-spacing: 0.01em;
            margin-top: 3px;
        }

        .user-panel {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-info {
            text-align: right;
            display: none;
        }

        @media (min-width: 640px) {
            .user-info { display: block; }
        }

        .user-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.2;
        }

        .user-role {
            font-size: 11px;
            color: var(--ok);
            background: var(--ok-bg);
            border: 1px solid var(--ok-bd);
            padding: 1px 8px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 2px;
        }

        .btn-logout {
            background: #FAFBFC;
            border: 1px solid var(--border);
            color: var(--text-sec);
            font-family: inherit;
            font-size: 12.5px;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-logout:hover {
            background: var(--err-bg);
            border-color: var(--err-bd);
            color: var(--err);
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 36px 20px 60px;
        }

        /* Intro Section */
        .intro {
            text-align: center;
            max-width: 640px;
            margin: 0 auto 36px;
        }

        .intro-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--accent);
            background: var(--accent-bg);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .intro-title {
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.025em;
            color: var(--text);
            margin-bottom: 8px;
        }

        .intro-desc {
            font-size: 14.5px;
            color: var(--text-sec);
            line-height: 1.55;
        }

        /* Alerts */
        .alert {
            max-width: 680px;
            margin: 0 auto 24px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert--ok {
            background: var(--ok-bg);
            border: 1px solid var(--ok-bd);
            color: var(--ok);
        }

        .alert--err {
            background: var(--err-bg);
            border: 1px solid var(--err-bd);
            color: var(--err);
        }

        /* Already Voted Success State */
        .voted-card {
            max-width: 540px;
            margin: 0 auto;
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            animation: fadeIn 0.4s ease;
        }

        .voted-badge-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--ok-bg);
            border: 2px solid var(--ok-bd);
            color: var(--ok);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .voted-title {
            font-size: 22px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .voted-desc {
            font-size: 14px;
            color: var(--text-sec);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .voted-meta {
            background: #FAFBFC;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
        }

        .voted-meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 6px 0;
            border-bottom: 1px dashed var(--border);
        }

        .voted-meta-row:last-child {
            border-bottom: none;
        }

        .voted-meta-lbl {
            color: var(--text-sec);
        }

        .voted-meta-val {
            font-weight: 600;
            color: var(--text);
        }

        /* 3D Flip Card Candidate Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 28px;
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Card 3D Setup */
        .card-container {
            perspective: 1200px;
            height: 520px;
        }

        .card-flipper {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
            transition: transform 0.65s cubic-bezier(0.2, 0.85, 0.35, 1.2);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }

        .card-container:hover .card-flipper,
        .card-container.is-flipped .card-flipper {
            transform: rotateY(180deg);
        }

        /* Faces */
        .card-face {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            border-radius: 16px;
            background: #FFFFFF;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* FRONT FACE */
        .card-front {
            z-index: 2;
            transform: rotateY(0deg);
        }

        .card-photo-box {
            position: relative;
            height: 260px;
            background: #E8EBF2;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .candidate-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
        }

        .photo-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--text-ph);
        }

        .nomor-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: var(--accent);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 30px;
            box-shadow: 0 2px 8px rgba(61,90,153,0.35);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .wakil-mini-avatar {
            position: absolute;
            bottom: -20px;
            right: 18px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid #FFFFFF;
            background: var(--accent-bg);
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wakil-mini-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-front-body {
            padding: 28px 22px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .paslon-names {
            margin-bottom: 12px;
        }

        .nama-ketua {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.25;
            margin-bottom: 4px;
        }

        .nama-wakil {
            font-size: 14px;
            color: var(--text-sec);
            font-weight: 400;
        }

        .visi-snippet {
            font-size: 13px;
            color: var(--text-sec);
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-top: 8px;
            background: #FAFBFC;
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 3px solid var(--accent);
        }

        .hover-hint {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            color: var(--accent);
            background: var(--accent-bg);
            padding: 8px;
            border-radius: 8px;
            font-weight: 500;
            margin-top: 14px;
            cursor: pointer;
            border: 1px dashed #C7D3F0;
        }

        /* BACK FACE */
        .card-back {
            transform: rotateY(180deg);
            padding: 24px;
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
        }

        .card-back-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }

        .back-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
        }

        .back-nomor {
            font-size: 12px;
            font-weight: 700;
            background: var(--accent-bg);
            color: var(--accent);
            padding: 3px 10px;
            border-radius: 12px;
        }

        .card-back-content {
            flex: 1;
            overflow-y: auto;
            padding-right: 6px;
            margin-bottom: 16px;
        }

        .card-back-content::-webkit-scrollbar {
            width: 4px;
        }
        .card-back-content::-webkit-scrollbar-thumb {
            background: var(--border-hi);
            border-radius: 4px;
        }

        .section-label {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--accent);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .visi-text {
            font-size: 13px;
            line-height: 1.55;
            color: var(--text);
            margin-bottom: 16px;
            background: #FAFBFC;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .misi-text {
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-sec);
            white-space: pre-line;
            background: #FAFBFC;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .btn-vote {
            width: 100%;
            height: 44px;
            background: var(--accent);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s;
            box-shadow: 0 2px 8px rgba(61,90,153,0.3);
        }

        .btn-vote:hover {
            background: var(--accent-h);
            transform: translateY(-1px);
        }

        .btn-flip-back {
            background: none;
            border: none;
            color: var(--text-sec);
            font-size: 12px;
            font-family: inherit;
            margin-top: 8px;
            cursor: pointer;
            text-align: center;
            width: 100%;
            padding: 4px;
        }

        .btn-flip-back:hover {
            color: var(--text);
            text-decoration: underline;
        }

        /* Modal Confirmation */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(28, 31, 46, 0.6);
            backdrop-filter: blur(4px);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
            animation: fadeIn 0.2s ease;
        }

        .modal-box {
            background: #FFFFFF;
            border-radius: 14px;
            max-width: 420px;
            width: 100%;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }

        .modal-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--accent-bg);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .modal-desc {
            font-size: 13.5px;
            color: var(--text-sec);
            line-height: 1.5;
            margin-bottom: 22px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
        }

        .btn-cancel {
            flex: 1;
            height: 40px;
            background: #FAFBFC;
            border: 1px solid var(--border);
            color: var(--text-sec);
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-confirm {
            flex: 1;
            height: 40px;
            background: var(--accent);
            border: none;
            color: #FFFFFF;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
        }

        /* Countdown Styles */
        .countdown-shell {
            max-width: 680px;
            margin: 0 auto;
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 44px 32px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(61,90,153,0.07);
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.4s ease;
        }

        .countdown-pulse-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--accent-bg);
            border: 2px solid #C7D3F0;
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulseGlow 2.4s infinite ease-in-out;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(61,90,153,0.3); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(61,90,153,0); }
        }

        .countdown-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--accent);
            background: var(--accent-bg);
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
        }

        .countdown-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .countdown-desc {
            font-size: 14.5px;
            color: var(--text-sec);
            max-width: 480px;
            margin: 0 auto 28px;
            line-height: 1.55;
        }

        .countdown-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            max-width: 540px;
            margin: 0 auto 32px;
        }

        .countdown-box {
            background: #FAFBFC;
            border: 1px solid var(--border-hi);
            border-radius: 12px;
            padding: 18px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 1px 0 #FFFFFF, 0 2px 6px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }

        .countdown-box:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            background: #FFFFFF;
        }

        .countdown-val {
            font-size: clamp(28px, 4.5vw, 40px);
            font-weight: 700;
            color: var(--accent);
            line-height: 1;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
        }

        .countdown-lbl {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-sec);
            margin-top: 8px;
        }

        .countdown-info-box {
            background: #F8FAFC;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px 20px;
            max-width: 540px;
            margin: 0 auto;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .countdown-info-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 2px;
        }

        .countdown-info-sub {
            font-size: 12px;
            color: var(--text-sec);
            line-height: 1.45;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes queueMove {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }

        @keyframes queuePulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(180, 83, 9, 0.35); }
            50% { transform: scale(1.04); box-shadow: 0 0 0 8px rgba(180, 83, 9, 0); }
        }
    </style>
</head>
<body>

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-inner">
            <a href="{{ url('/') }}" class="brand">
                <div class="brand-icon" style="overflow:hidden; background: {{ config('election.logo_instansi') ? '#FFFFFF' : 'var(--accent)' }};">
                    @if (config('election.logo_instansi') && file_exists(public_path(config('election.logo_instansi'))))
                        <img src="{{ asset(config('election.logo_instansi')) }}" alt="Logo" style="width:100%; height:100%; object-fit:contain; padding:2px;">
                    @else
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <div class="brand-name">{{ config('election.institution') ?: 'SatuSuara' }}</div>
                    <div class="brand-sub">{{ config('election.election_name') ?: 'Bilik Pemungutan Suara Digital' }}</div>
                </div>
            </a>

            <div class="user-panel">
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Pemilih Terverifikasi</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout" title="Keluar dari akun">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container">

        <!-- Flash Status Message -->
        @if (session('status'))
            <div class="alert alert--ok">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert--err">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if ($sudahVoting)
            <!-- State: Already Voted -->
            <div class="voted-card">
                <div class="voted-badge-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h1 class="voted-title">Suara Anda Telah Tercatat Sah!</h1>
                <p class="voted-desc">
                    Hak pilih Anda telah berhasil disalurkan secara aman dan rahasia. Suara Anda telah masuk ke dalam kotak suara audit sistem SatuSuara.
                </p>

                <div class="voted-meta">
                    <div class="voted-meta-row">
                        <span class="voted-meta-lbl">Nama Pemilih</span>
                        <span class="voted-meta-val">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="voted-meta-row">
                        <span class="voted-meta-lbl">Status Hak Pilih</span>
                        <span class="voted-meta-val" style="color:var(--ok);">Selesai Memilih</span>
                    </div>
                    <div class="voted-meta-row">
                        <span class="voted-meta-lbl">Struk Kriptografi (Hash)</span>
                        <span class="voted-meta-val" style="font-family:monospace; font-size:13px; font-weight:700; color:var(--accent);">
                            {{ $voteKu->receipt_hash ?? session('receipt_code', 'VOTE-' . strtoupper(substr(md5(auth()->id()), 0, 12))) }}
                        </span>
                    </div>
                </div>

                <!-- Jaminan Struk & Link Verifikasi -->
                <div style="background:#EEF1F9; border:1px solid #C7D3F0; border-radius:10px; padding:12px 14px; margin-bottom:20px; font-size:12px; color:var(--text-sec); display:flex; align-items:center; justify-content:space-between; gap:12px; text-align:left;">
                    <div>
                        <strong style="color:var(--text);">Simpan Struk Digital Anda:</strong> Gunakan kode di atas untuk memverifikasi bahwa suara Anda sah tercatat tanpa membocorkan pilihan Anda.
                    </div>
                    <a href="{{ route('verify-receipt', ['code' => $voteKu->receipt_hash ?? session('receipt_code', '')]) }}" target="_blank" class="btn btn-secondary btn-sm" style="white-space:nowrap; font-size:11.5px;">
                        Verifikasi ↗
                    </a>
                </div>

                @if (config('election.kiosk_mode', false))
                    <!-- Kiosk Auto-Standby Countdown -->
                    <div style="background:#FEF3C7; border:1px solid #FCD34D; border-radius:10px; padding:12px 14px; margin-bottom:20px; font-size:13px; color:#92400E; display:flex; align-items:center; justify-content:center; gap:8px;">
                        <span>⏱</span>
                        <span><strong>Mode Kiosk TPS:</strong> Bilik suara akan otomatis logout dalam <strong id="kiosk-timer" style="font-size:16px; font-family:monospace; color:#B45309;">{{ config('election.kiosk_timeout_seconds', 5) }}</strong> detik untuk pemilih berikutnya...</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}" id="logout-kiosk-form">
                    @csrf
                    <button type="submit" class="btn-vote" style="max-width:240px; margin:0 auto;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span>Selesai &amp; Keluar</span>
                    </button>
                </form>
            </div>
        @elseif ($votingStatus === 'BELUM_DIBUKA')
            <!-- State: COUNTDOWN ANIMASI KEREN -->
            <div class="countdown-shell">
                <div class="countdown-pulse-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>

                <div class="countdown-badge">
                    <span style="width:6px; height:6px; border-radius:50%; background:var(--accent); animation: pulseGlow 1.8s infinite;"></span>
                    <span id="countdown-status-text">Bilik Suara Belum Dibuka</span>
                </div>

                <h1 class="countdown-title">Pemilihan Akan Segera Dimulai</h1>
                <p class="countdown-desc">
                    Bilik suara digital masih terkunci. Sistem menghitung mundur hingga waktu pembukaan resmi dan akan otomatis membuka bilik suara secara langsung begitu waktu tiba.
                </p>

                <!-- GRID 4 DIGIT COUNTDOWN -->
                <div class="countdown-grid">
                    <div class="countdown-box">
                        <div class="countdown-val" id="cd-days">00</div>
                        <div class="countdown-lbl">Hari</div>
                    </div>
                    <div class="countdown-box">
                        <div class="countdown-val" id="cd-hours">00</div>
                        <div class="countdown-lbl">Jam</div>
                    </div>
                    <div class="countdown-box">
                        <div class="countdown-val" id="cd-minutes">00</div>
                        <div class="countdown-lbl">Menit</div>
                    </div>
                    <div class="countdown-box">
                        <div class="countdown-val" id="cd-seconds">00</div>
                        <div class="countdown-lbl">Detik</div>
                    </div>
                </div>

                <div class="countdown-info-box">
                    <div style="color:var(--accent); flex-shrink:0; margin-top:2px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <div class="countdown-info-title">
                            Jadwal Pembukaan Resmi:
                            @if ($startTimeTarget)
                                {{ \Carbon\Carbon::parse($startTimeTarget)->translatedFormat('l, d F Y — [Pukul] H:i T') }}
                            @else
                                Menunggu Pengumuman Panitia
                            @endif
                        </div>
                        <div class="countdown-info-sub">
                            Hitung mundur disinkronkan secara ketat dengan jam server resmi pemilihan. Mengubah waktu atau tanggal di perangkat tidak akan memengaruhi hitungan mundur. Bilik suara akan otomatis terbuka secara live.
                        </div>
                    </div>
                </div>

                <div style="margin-top: 16px; display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid var(--border); padding:6px 14px; border-radius:20px; font-size:12px; color:var(--text-sec); box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10B981;"></span>
                    <span>Waktu Server Resmi: <strong id="server-clock-preview" style="color:var(--text); font-family:monospace; font-size:12.5px;">{{ \Carbon\Carbon::now()->format('H:i:s') }}</strong> ({{ config('app.timezone') }})</span>
                </div>
            </div>

        @elseif ($votingStatus === 'DITUTUP')
            <!-- State: Voting Closed -->
            <div class="voted-card" style="border-color:var(--border-hi);">
                <div class="voted-badge-icon" style="background:#F1F5F9; border-color:var(--border-hi); color:var(--text-sec);">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h1 class="voted-title">Waktu Pemilihan Telah Berakhir</h1>
                <p class="voted-desc">
                    Bilik pemungutan suara digital telah resmi ditutup oleh panitia pemilihan. Terima kasih atas partisipasi seluruh warga pemilih dalam agenda ini.
                </p>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-vote" style="max-width:220px; margin:0 auto; background:var(--text); color:#fff;">
                        <span>Keluar dari Sesi</span>
                    </button>
                </form>
            </div>

        @elseif ($queueInfo && !$queueInfo['can_enter'])
            <!-- State: VIRTUAL WAITING ROOM (RUANG ANTRIAN BILIK SUARA) -->
            <div class="countdown-shell" style="max-width:620px;">
                <div class="countdown-pulse-icon" style="background:#FFFBEB; border-color:#FDE68A; color:#B45309; animation: queuePulse 2s infinite ease-in-out;">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>

                <div class="countdown-badge" style="background:#FFFBEB; color:#92400E; border:1px solid #FDE68A;">
                    <span style="width:6px; height:6px; border-radius:50%; background:#B45309; animation: pulseGlow 1.5s infinite;"></span>
                    <span id="queueBadgeStatus">Dalam Antrian Pemilihan</span>
                </div>

                <h1 class="countdown-title">Ruang Antrian Bilik Suara</h1>
                <p class="countdown-desc">
                    Saat ini bilik suara sedang penuh. Untuk menjaga kelancaran dan mencegah lag pada server, Anda dialokasikan ke dalam antrian otomatis.
                </p>

                <!-- QUEUE METRICS -->
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; margin-bottom:28px;">
                    <div class="countdown-box" style="padding:16px 8px;">
                        <div class="countdown-val" style="color:var(--text); font-size:28px;">
                            #{{ $queueInfo['queue_number'] ?? 1 }}
                        </div>
                        <div class="countdown-lbl">Nomor Anda</div>
                    </div>

                    <div class="countdown-box" style="padding:16px 8px; border-color:var(--accent);">
                        <div class="countdown-val" id="queueAheadCount" style="color:var(--accent); font-size:28px;">
                            {{ $queueInfo['ahead_count'] ?? 0 }}
                        </div>
                        <div class="countdown-lbl">Di Depan Anda</div>
                    </div>

                    <div class="countdown-box" style="padding:16px 8px;">
                        <div class="countdown-val" id="queueEstTime" style="color:var(--ok); font-size:20px; padding-top:4px;">
                            {{ $queueInfo['estimated_wait_text'] ?? '~1 Menit' }}
                        </div>
                        <div class="countdown-lbl">Estimasi Tunggu</div>
                    </div>
                </div>

                <!-- Live Progress indicator bar -->
                <div style="width:100%; height:6px; background:#F1F5F9; border-radius:10px; overflow:hidden; margin-bottom:24px;">
                    <div style="width:100%; height:100%; background:linear-gradient(90deg, var(--accent), #38BDF8, var(--accent)); background-size:200% 100%; animation: queueMove 1.8s linear infinite;"></div>
                </div>

                <div class="countdown-info-box" style="background:#FAFBFC;">
                    <div style="color:#B45309; flex-shrink:0; margin-top:2px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        <div class="countdown-info-title">Mohon Menunggu Sejenak</div>
                        <div class="countdown-info-sub">
                            Sistem melakukan sinkronisasi otomatis tiap 2,5 detik. Begitu giliran Anda tiba, bilik suara akan terbuka secara live tanpa perlu menekan refresh.
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- State: Ready to Vote -->
            <div class="intro">
                <div class="intro-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Pemilihan Terenkripsi &amp; Langsung</span>
                </div>
                <h1 class="intro-title">Tentukan Pilihan Anda</h1>
                <p class="intro-desc">
                    Arahkan kursor atau sentuh kartu untuk membaca <strong>Visi &amp; Misi</strong> setiap pasangan calon sebelum menjatuhkan suara Anda.
                </p>
            </div>

            @if ($candidates->isEmpty())
                <div class="voted-card" style="padding: 40px 20px;">
                    <p style="color:var(--text-sec); font-size:14px;">Belum ada pasangan calon yang aktif pada sistem.</p>
                </div>
            @else
                <div class="cards-grid">
                    @foreach ($candidates as $candidate)
                        <div class="card-container" id="card-wrap-{{ $candidate->id }}">
                            <div class="card-flipper">

                                <!-- CARD FRONT -->
                                <div class="card-face card-front">
                                    <div class="card-photo-box">
                                        <div class="nomor-badge">
                                            <span>#{{ str_pad($candidate->nomor_urut, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>

                                        @if ($candidate->foto_ketua)
                                            <img src="{{ $candidate->foto_ketua_url }}" alt="{{ $candidate->nama_ketua }}" class="candidate-photo">
                                        @else
                                            <div class="photo-placeholder">
                                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                                <span style="font-size:11px;">Foto Paslon</span>
                                            </div>
                                        @endif

                                        @if ($candidate->foto_wakil)
                                            <div class="wakil-mini-avatar" title="Wakil: {{ $candidate->nama_wakil }}">
                                                <img src="{{ $candidate->foto_wakil_url }}" alt="{{ $candidate->nama_wakil }}">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-front-body">
                                        <div class="paslon-names">
                                            <div class="nama-ketua">{{ $candidate->nama_ketua }}</div>
                                            <div class="nama-wakil">
                                                {{ $candidate->nama_wakil ? '& ' . $candidate->nama_wakil : 'Calon Tunggal' }}
                                            </div>

                                            @if ($candidate->visi)
                                                <div class="visi-snippet">
                                                    &ldquo;{{ $candidate->visi }}&rdquo;
                                                </div>
                                            @endif
                                        </div>

                                        <div class="hover-hint" onclick="toggleCardFlip({{ $candidate->id }})">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                                            </svg>
                                            <span>Buka Visi Misi &amp; Beri Suara ➔</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- CARD BACK -->
                                <div class="card-face card-back">
                                    <div class="card-back-header">
                                        <div>
                                            <div class="back-title">Paslon No. {{ $candidate->nomor_urut }}</div>
                                            <div style="font-size:12px; color:var(--text-sec);">{{ $candidate->nama_ketua }}</div>
                                        </div>
                                        <div class="back-nomor">Pilihan #{{ $candidate->nomor_urut }}</div>
                                    </div>

                                    <div class="card-back-content">
                                        <div class="section-label">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="16" x2="12" y2="12"/>
                                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                                            </svg>
                                            <span>Visi Utama</span>
                                        </div>
                                        <div class="visi-text">
                                            {{ $candidate->visi ?: 'Visi belum dicantumkan.' }}
                                        </div>

                                        <div class="section-label">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="8" y1="6" x2="21" y2="6"/>
                                                <line x1="8" y1="12" x2="21" y2="12"/>
                                                <line x1="8" y1="18" x2="21" y2="18"/>
                                                <line x1="3" y1="6" x2="3.01" y2="6"/>
                                                <line x1="3" y1="12" x2="3.01" y2="12"/>
                                                <line x1="3" y1="18" x2="3.01" y2="18"/>
                                            </svg>
                                            <span>Misi &amp; Program</span>
                                        </div>
                                        <div class="misi-text">{{ $candidate->misi ?: 'Misi belum dicantumkan.' }}</div>
                                    </div>

                                    <form id="vote-form-{{ $candidate->id }}" method="POST" action="{{ route('voter.vote') }}">
                                        @csrf
                                        <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                                        <button type="button" class="btn-vote" onclick="confirmVote({{ $candidate->id }}, '{{ addslashes($candidate->nama_ketua) }}', '{{ $candidate->nomor_urut }}')">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            <span>Pilih Paslon No. {{ $candidate->nomor_urut }}</span>
                                        </button>
                                    </form>

                                    <button type="button" class="btn-flip-back" onclick="toggleCardFlip({{ $candidate->id }})">
                                        ⟵ Kembali ke Tampilan Depan
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endforeach

                    @if ($isSingleCandidate)
                        <!-- KARTU KOTAK KOSONG (CALON TUNGGAL) -->
                        <div class="card-container" id="card-wrap-kotak-kosong">
                            <div class="card-flipper" id="flipper-kotak-kosong">

                                <!-- KOTAK KOSONG FRONT -->
                                <div class="card-face card-front" style="border: 2px dashed #94A3B8;">
                                    <div class="card-photo-box" style="background: linear-gradient(180deg, #F8FAFC 0%, #E2E8F0 100%);">
                                        <div class="nomor-badge" style="background: #475569; box-shadow: 0 2px 8px rgba(71,85,105,0.35); font-size:12px; letter-spacing:0.04em; padding:6px 12px;">
                                            <span>KOTAK KOSONG</span>
                                        </div>

                                        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px;">
                                            <div style="width:76px; height:76px; border-radius:20px; background:#FFFFFF; border:2px dashed #94A3B8; display:flex; align-items:center; justify-content:center; color:#64748B; box-shadow: 0 4px 14px rgba(0,0,0,0.06);">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                                </svg>
                                            </div>
                                            <span style="font-size:11.5px; font-weight:700; color:#475569; background:rgba(255,255,255,0.85); padding:3px 12px; border-radius:20px; border:1px solid #CBD5E1; letter-spacing:0.02em;">
                                                KOLOM KOSONG
                                            </span>
                                        </div>
                                    </div>

                                    <div class="card-front-body">
                                        <div class="paslon-names">
                                            <div class="nama-ketua" style="color:#334155;">Kotak Kosong</div>
                                            <div class="nama-wakil">Pilihan Resmi Tidak Setuju Calon Tunggal</div>

                                            <div class="visi-snippet" style="border-left-color: #64748B; background:#F8FAFC; color:#475569;">
                                                &ldquo;Hak konstitusional pemilih untuk menyatakan ketidaksetujuan terhadap calon tunggal sesuai regulasi pemilu.&rdquo;
                                            </div>
                                        </div>

                                        <div class="hover-hint" onclick="toggleCardFlip('kotak-kosong')" style="background:#F1F5F9; color:#475569; border:1px dashed #CBD5E1;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                                            </svg>
                                            <span>Buka Ketentuan &amp; Beri Suara ➔</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- KOTAK KOSONG BACK -->
                                <div class="card-face card-back" style="border: 2px dashed #94A3B8;">
                                    <div class="card-back-header">
                                        <div>
                                            <div class="back-title" style="color:#334155;">Kotak Kosong</div>
                                            <div style="font-size:12px; color:var(--text-sec);">Opsi Regulasi Pemilu Resmi</div>
                                        </div>
                                        <div class="back-nomor" style="background:#F1F5F9; color:#475569;">Kotak Kosong</div>
                                    </div>

                                    <div class="card-back-content">
                                        <div class="section-label" style="color:#475569;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="16" x2="12" y2="12"/>
                                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                                            </svg>
                                            <span>Dasar Regulasi Pemilihan</span>
                                        </div>
                                        <div class="visi-text" style="color:#334155;">
                                            Dalam pemilihan dengan calon tunggal, pemilih berhak menyatakan persetujuan atau ketidaksetujuan terhadap calon yang ada.
                                        </div>

                                        <div class="section-label" style="margin-top:14px; color:#475569;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                            <span>Mekanisme Hasil Suara</span>
                                        </div>
                                        <div class="misi-text" style="color:#475569;">
                                            Jika Kotak Kosong memperoleh suara terbanyak (&gt; 50%), maka calon tunggal dinyatakan tidak terpilih dan pemilihan akan diulang sesuai ketentuan panitia penyelenggara.
                                        </div>
                                    </div>

                                    <form id="vote-form-kotak_kosong" method="POST" action="{{ route('voter.vote') }}">
                                        @csrf
                                        <input type="hidden" name="kotak_kosong" value="1">
                                        <button type="button" class="btn-vote" onclick="confirmVote('kotak_kosong', 'Kotak Kosong (Opsi Tidak Setuju Calon Tunggal)', 'KOTAK KOSONG')" style="background:#475569;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            <span>Pilih Kotak Kosong</span>
                                        </button>
                                    </form>

                                    <button type="button" class="btn-flip-back" onclick="toggleCardFlip('kotak-kosong')">
                                        ⟵ Kembali ke Tampilan Depan
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endif

    </main>

    <!-- Modal Konfirmasi Voting -->
    <div id="voteModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h3 class="modal-title">Konfirmasi Pilihan Anda</h3>
            <p class="modal-desc" id="modalPromptText">
                Apakah Anda yakin ingin memberikan suara kepada Pasangan Calon ini? Pilihan Anda tidak dapat diubah setelah dikonfirmasi.
            </p>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="button" class="btn-confirm" id="btnSubmitVote">Ya, Beri Suara</button>
            </div>
        </div>
    </div>

    <script>
        let selectedCandidateId = null;

        function toggleCardFlip(id) {
            const cardWrap = document.getElementById('card-wrap-1' + id) || document.getElementById('card-wrap-' + id);
            if (cardWrap) {
                cardWrap.classList.toggle('is-flipped');
            }
        }

        function confirmVote(id, name, nomor) {
            selectedCandidateId = id;
            const promptText = document.getElementById('modalPromptText');
            if (id === 'kotak_kosong') {
                promptText.innerHTML = `Anda akan memberikan suara kepada <strong>${name}</strong>.<br>Pilihan bersifat final dan langsung terenkripsi ke dalam kotak suara digital.`;
            } else {
                promptText.innerHTML = `Anda akan memberikan suara kepada <strong>Paslon No. ${nomor} (${name})</strong>.<br>Pilihan bersifat final dan langsung terenkripsi ke dalam kotak suara digital.`;
            }
            document.getElementById('voteModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('voteModal').classList.remove('active');
            selectedCandidateId = null;
        }

        document.getElementById('btnSubmitVote').addEventListener('click', function() {
            if (selectedCandidateId) {
                const form = document.getElementById('vote-form-' + selectedCandidateId);
                if (form) {
                    form.submit();
                }
            }
        });

        @if ($sudahVoting && config('election.kiosk_mode', false))
        (function() {
            let timeLeft = {{ (int) config('election.kiosk_timeout_seconds', 5) }};
            const timerEl = document.getElementById('kiosk-timer');

            const countdownInterval = setInterval(function() {
                timeLeft--;
                if (timerEl) timerEl.innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    const logoutForm = document.getElementById('logout-kiosk-form') || document.querySelector('form[action="{{ route('logout') }}"]');
                    if (logoutForm) {
                        logoutForm.submit();
                    } else {
                        window.location.href = "{{ route('login') }}";
                    }
                }
            }, 1000);
        })();
        @endif

        @if (!$sudahVoting && $votingStatus === 'BELUM_DIBUKA' && $startTimeTarget)
        (function() {
            // Sisa waktu awal dihitung langsung oleh server resmi (ms)
            let serverRemainingMs = {{ (int) ($serverRemainingMs ?? 0) }};
            // Monotonic hardware clock - kebal 100% terhadap pengubahan jam OS perangkat pemilih
            let monotonicStart = performance.now();
            // Epoch server awal (ms)
            let serverEpochNow = {{ (int) ($serverTimeNowMs ?? (now()->getPreciseTimestamp(3))) }};

            const daysEl = document.getElementById('cd-days');
            const hoursEl = document.getElementById('cd-hours');
            const minsEl = document.getElementById('cd-minutes');
            const secsEl = document.getElementById('cd-seconds');
            const statusEl = document.getElementById('countdown-status-text');
            const clockEl = document.getElementById('server-clock-preview');

            let isReloading = false;

            function tickCountdown() {
                if (isReloading) return;

                // Menggunakan performance.now() (Monotonic Time)
                // Berapapun user memajukan/memundurkan jam perangkat, performance.now() tidak akan berubah!
                const elapsedMs = performance.now() - monotonicStart;
                const diff = Math.max(0, serverRemainingMs - elapsedMs);

                // Sinkronkan display jam server berjalan secara akurat
                if (clockEl) {
                    const currentServerDate = new Date(serverEpochNow + elapsedMs);
                    const hours = String(currentServerDate.getHours()).padStart(2, '0');
                    const mins = String(currentServerDate.getMinutes()).padStart(2, '0');
                    const secs = String(currentServerDate.getSeconds()).padStart(2, '0');
                    clockEl.innerText = hours + ':' + mins + ':' + secs;
                }

                if (diff <= 0) {
                    isReloading = true;
                    if (daysEl) daysEl.innerText = '00';
                    if (hoursEl) hoursEl.innerText = '00';
                    if (minsEl) minsEl.innerText = '00';
                    if (secsEl) secsEl.innerText = '00';
                    if (statusEl) statusEl.innerText = 'Bilik Suara Telah Dibuka! Memuat...';

                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                if (daysEl) daysEl.innerText = String(days).padStart(2, '0');
                if (hoursEl) hoursEl.innerText = String(hours).padStart(2, '0');
                if (minsEl) minsEl.innerText = String(minutes).padStart(2, '0');
                if (secsEl) secsEl.innerText = String(seconds).padStart(2, '0');
            }

            // Background synchronization setiap 15 detik dengan server
            // Menjaga presisi tinggi & mendeteksi jika admin membuka bilik lebih awal
            function syncWithServer() {
                if (isReloading) return;

                fetch("{{ route('voter.server-time') }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'DIBUKA' || data.remaining_ms <= 0) {
                            isReloading = true;
                            if (statusEl) statusEl.innerText = 'Bilik Suara Dibuka! Memuat...';
                            window.location.reload();
                            return;
                        }
                        // Re-kalibrasi referensi waktu server
                        serverRemainingMs = data.remaining_ms;
                        serverEpochNow = data.server_time_ms;
                        monotonicStart = performance.now();
                    })
                    .catch(() => {});
            }

            tickCountdown();
            setInterval(tickCountdown, 1000);
            setInterval(syncWithServer, 15000);
        })();
        @endif

        @if (!$sudahVoting && $votingStatus === 'DIBUKA' && $queueInfo && !$queueInfo['can_enter'])
        (function() {
            let queuePolling = true;

            function checkQueue() {
                if (!queuePolling) return;

                fetch("{{ route('voter.queue-status') }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data.can_enter) {
                            queuePolling = false;
                            const badge = document.getElementById('queueBadgeStatus');
                            if (badge) badge.innerText = 'Giliran Anda Tiba! Membuka...';
                            setTimeout(function() {
                                window.location.reload();
                            }, 800);
                        } else {
                            const aheadEl = document.getElementById('queueAheadCount');
                            const estEl = document.getElementById('queueEstTime');
                            if (aheadEl && data.ahead_count !== undefined) aheadEl.innerText = data.ahead_count;
                            if (estEl && data.estimated_wait_text) estEl.innerText = data.estimated_wait_text;
                        }
                    })
                    .catch(err => console.error("Queue poll error:", err));
            }

            setInterval(checkQueue, 2500);
        })();
        @endif

        @if (!$sudahVoting && $votingStatus === 'DIBUKA' && (!$queueInfo || $queueInfo['can_enter']))
        // Heartbeat keeping booth session active while voting
        setInterval(function() {
            fetch("{{ route('voter.queue-status') }}").catch(function() {});
        }, 20000);
        @endif
    </script>
</body>
</html>
