<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Struk Kriptografi Suara — {{ config('election.institution', 'SatuSuara') }}</title>
    <x-favicon />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #EDEEF1;
            --surface: #FFFFFF;
            --border: #D8DCE5;
            --text: #1C1F2E;
            --text-sec: #5C6479;
            --accent: #3D5A99;
            --accent-hover: #324B80;
            --ok: #15803D;
            --ok-bg: #DCFCE7;
            --ok-bd: #86EFAC;
            --err: #B91C1C;
            --err-bg: #FEE2E2;
            --err-bd: #FCA5A5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Jost', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 24px 16px;
        }

        .verify-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            padding: 36px 32px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            text-align: center;
        }

        .verify-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: #EEF1F9;
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .verify-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .verify-desc {
            font-size: 13.5px;
            color: var(--text-sec);
            line-height: 1.5;
            margin-top: 6px;
            margin-bottom: 24px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-family: monospace;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-align: center;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: #FAFBFC;
            color: var(--text);
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(61,90,153,0.15);
        }

        .btn-verify {
            width: 100%;
            padding: 12px;
            background: var(--accent);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-verify:hover {
            background: var(--accent-hover);
        }

        .result-box {
            margin-top: 24px;
            padding: 20px;
            border-radius: 12px;
            text-align: left;
            animation: fadeIn 0.3s ease;
        }

        .result-valid {
            background: var(--ok-bg);
            border: 1.5px solid var(--ok-bd);
            color: var(--ok);
        }

        .result-invalid {
            background: var(--err-bg);
            border: 1.5px solid var(--err-bd);
            color: var(--err);
        }

        .audit-guarantee-box {
            margin-top: 24px;
            padding: 14px;
            background: #FAFBFC;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 12px;
            color: var(--text-sec);
            line-height: 1.5;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-size: 13px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="verify-card">
        <div class="verify-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <polyline points="9 12 11 14 15 10"/>
            </svg>
        </div>

        <h1 class="verify-title">Verifikasi Tanda Terima Suara</h1>
        <p class="verify-desc">
            Masukkan kode tanda terima unik yang Anda peroleh saat selesai mencoblos untuk memastikan suara Anda sah tercatat di kotak suara.
        </p>

        <form method="POST" action="{{ route('verify-receipt.check') }}">
            @csrf
            <div class="form-group">
                <label for="receipt_hash" class="form-label">Nomor Tanda Terima Struk Suara</label>
                <input
                    type="text"
                    id="receipt_hash"
                    name="receipt_hash"
                    value="{{ old('receipt_hash', $result['searched_code'] ?? request('code')) }}"
                    placeholder="Contoh: VOTE-8F2A-99B1-XXXX"
                    required
                    class="form-control"
                    autocomplete="off"
                >
            </div>

            <button type="submit" class="btn-verify">
                Verifikasi Validitas Suara
            </button>
        </form>

        @if (isset($result))
            @if ($result['is_valid'])
                <div class="result-box result-valid">
                    <div style="display:flex; align-items:center; gap:8px; font-weight:700; font-size:15px; margin-bottom:8px;">
                        <span>✓</span>
                        <span>SUARA SAH TERVERIFIKASI</span>
                    </div>
                    <div style="font-size:13px; line-height:1.6; color:#14532D;">
                        <div>Kode Struk: <strong style="font-family:monospace;">{{ $result['searched_code'] }}</strong></div>
                        <div>Waktu Pencoblosan: <strong>{{ $result['voted_at'] }}</strong></div>
                        <div>Status Audit: <strong style="color:var(--ok);">Tercatat Sah &amp; Dihitung dalam Kotak Suara</strong></div>
                    </div>
                </div>
            @else
                <div class="result-box result-invalid">
                    <div style="display:flex; align-items:center; gap:8px; font-weight:700; font-size:15px; margin-bottom:6px;">
                        <span>✕</span>
                        <span>KODE STRUK TIDAK DITEMUKAN</span>
                    </div>
                    <div style="font-size:13px; line-height:1.5; color:#7F1D1D;">
                        Kode struk <strong style="font-family:monospace;">{{ $result['searched_code'] }}</strong> tidak terdaftar dalam kotak suara resmi. Pastikan Anda menyalin kode dengan benar sesuai tanda terima.
                    </div>
                </div>
            @endif
        @endif

        <div class="audit-guarantee-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; color:var(--accent); margin-top:2px;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <div>
                <strong>Jaminan Asas Luber &amp; Jurdil:</strong> Verifikasi ini hanya membuktikan bahwa suara sah masuk ke sistem tanpa pernah membocorkan calon yang Anda pilih, demi menjaga kerahasiaan pilihan pemilih 100%.
            </div>
        </div>

        <div>
            <a href="{{ url('/') }}" class="back-link">← Kembali ke Halaman Utama</a>
        </div>
    </div>

</body>
</html>

