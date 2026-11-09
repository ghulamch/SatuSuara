@extends('install.layout')

@section('title', 'Langkah 1: Prasyarat Server')

@section('content')
    <!-- Step Bar -->
    <div class="step-bar">
        <div class="step-item active">
            <span class="step-num">1</span>
            <span class="step-title">Prasyarat</span>
        </div>
        <span class="step-arrow">&rarr;</span>
        <div class="step-item">
            <span class="step-num">2</span>
            <span class="step-title">Database</span>
        </div>
        <span class="step-arrow">&rarr;</span>
        <div class="step-item">
            <span class="step-num">3</span>
            <span class="step-title">Akun Admin</span>
        </div>
        <span class="step-arrow">&rarr;</span>
        <div class="step-item">
            <span class="step-num">4</span>
            <span class="step-title">Selesai</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">1. Pemeriksaan Lingkungan Server</h2>
            <p class="card-desc">Memastikan versi PHP, ekstensi penting, dan hak akses direktori penyimpanan terpenuhi.</p>
        </div>

        <!-- PHP Version Status -->
        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 14px; background:#FAFBFC; border:1px solid var(--border); border-radius:8px; margin-bottom:18px;">
            <div>
                <strong style="font-size:14px; color:var(--text);">Versi PHP Server</strong>
                <div style="font-size:12px; color:var(--text-sec);">Diperlukan: ≥ {{ $check['php']['required'] }} &bull; Terdeteksi: {{ $check['php']['current'] }}</div>
            </div>
            @if ($check['php']['passed'])
                <span style="display:inline-flex; align-items:center; gap:4px; font-size:12.5px; font-weight:700; color:var(--ok); background:var(--ok-bg); padding:4px 10px; border-radius:20px; border:1px solid var(--ok-bd);">
                    ✓ Memenuhi
                </span>
            @else
                <span style="display:inline-flex; align-items:center; gap:4px; font-size:12.5px; font-weight:700; color:var(--err); background:var(--err-bg); padding:4px 10px; border-radius:20px; border:1px solid var(--err-bd);">
                    ✕ Tidak Memenuhi
                </span>
            @endif
        </div>

        <!-- PHP Extensions List -->
        <div style="margin-bottom:20px;">
            <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.04em;">Ekstensi PHP Wajib</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                @foreach ($check['extensions'] as $ext)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 12px; background:#FFFFFF; border:1px solid {{ $ext['passed'] ? 'var(--border)' : 'var(--err-bd)' }}; border-radius:6px; font-size:12.5px;">
                        <span style="color:var(--text); font-weight:500;">{{ $ext['name'] }}</span>
                        @if ($ext['passed'])
                            <span style="color:var(--ok); font-weight:700;">✓ Aktif</span>
                        @else
                            <span style="color:var(--err); font-weight:700;">✕ Belum Ada</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Directory Permissions -->
        <div style="margin-bottom:24px;">
            <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.04em;">Hak Akses Direktori (Writable)</div>
            <div style="display:flex; flex-direction:column; gap:6px;">
                @foreach ($check['directories'] as $dir)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 12px; background:#FFFFFF; border:1px solid {{ $dir['writable'] ? 'var(--border)' : 'var(--err-bd)' }}; border-radius:6px; font-size:12px;">
                        <code style="color:var(--text); font-weight:600;">{{ $dir['path'] }}</code>
                        @if ($dir['writable'])
                            <span style="color:var(--ok); font-weight:700;">✓ Dapat Ditulis</span>
                        @else
                            <span style="color:var(--err); font-weight:700;">✕ Permission Denied</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        @if ($check['allPassed'])
            <div style="display:flex; justify-content:flex-end;">
                <a href="{{ route('install.database') }}" class="btn btn-primary" style="width:100%;">
                    <span>Lanjutkan ke Konfigurasi Database</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
        @else
            <div class="alert alert--err" style="margin-bottom:0;">
                <div>
                    <strong>Sebagian prasyarat belum terpenuhi.</strong><br>
                    Silakan aktifkan ekstensi PHP atau periksa izin direktori di atas sebelum melanjutkan.
                </div>
            </div>
        @endif
    </div>
@endsection

