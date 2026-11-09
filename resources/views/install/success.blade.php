@extends('install.layout')

@section('title', 'Instalasi Selesai')

@section('content')
    <!-- Step Bar -->
    <div class="step-bar">
        <div class="step-item passed">
            <span class="step-num">✓</span>
            <span class="step-title">Prasyarat</span>
        </div>
        <span class="step-arrow">&rarr;</span>
        <div class="step-item passed">
            <span class="step-num">✓</span>
            <span class="step-title">Database</span>
        </div>
        <span class="step-arrow">&rarr;</span>
        <div class="step-item passed">
            <span class="step-num">✓</span>
            <span class="step-title">Akun Admin</span>
        </div>
        <span class="step-arrow">&rarr;</span>
        <div class="step-item active passed">
            <span class="step-num">✓</span>
            <span class="step-title">Selesai</span>
        </div>
    </div>

    <div class="card" style="text-align:center;">
        <div style="width:64px; height:64px; border-radius:50%; background:var(--ok-bg); color:var(--ok); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; border:2px solid var(--ok-bd);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>

        <h2 style="font-size:24px; font-weight:800; color:var(--text); letter-spacing:-0.02em; margin-bottom:8px;">
            Instalasi Berhasil Diselesaikan!
        </h2>
        <p style="font-size:14px; color:var(--text-sec); max-width:480px; margin:0 auto 24px; line-height:1.5;">
            Selamat! Basis data, struktur tabel, hak akses peran panitia, dan akun administrator utama telah berhasil dipasang dan siap digunakan.
        </p>

        @if ($summary)
            <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:20px; text-align:left; margin-bottom:28px;">
                <div style="font-size:12px; font-weight:700; color:var(--text-ph); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px;">
                    Ringkasan Informasi Sistem
                </div>
                
                <div style="display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-sec);">Nama Lembaga:</span>
                        <strong style="color:var(--text);">{{ $summary['institution'] ?? '-' }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-sec);">Agenda Pemilihan:</span>
                        <strong style="color:var(--text);">{{ $summary['election'] ?? '-' }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-top:8px; border-top:1px dashed var(--border);">
                        <span style="color:var(--text-sec);">Email Login Admin:</span>
                        <code style="font-size:13px; font-weight:700; color:var(--accent);">{{ $summary['admin_email'] ?? 'admin@satusuara.id' }}</code>
                    </div>
                </div>
            </div>
        @endif

        <div style="background:var(--accent-bg); border:1px solid var(--accent-bd); border-radius:8px; padding:14px; margin-bottom:28px; text-align:left; display:flex; gap:10px; align-items:flex-start;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:2px;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <div style="font-size:12.5px; color:#1E3A8A; line-height:1.5;">
                <strong>Keamanan Terjaga:</strong> Berkas kunci instalasi telah dibuat di <code>storage/installed</code>. Akses menuju wizard instalasi ini otomatis dikunci untuk mencegah penimpaan sistem oleh pihak lain.
            </div>
        </div>

        <div>
            <a href="{{ route('login') }}" class="btn btn-primary" style="width:100%; font-size:15px; height:48px;">
                <span>Masuk ke Halaman Login &rarr;</span>
            </a>
        </div>
    </div>
@endsection

