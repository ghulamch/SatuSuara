@extends('install.layout')

@section('title', 'Aplikasi Sudah Terpasang')

@section('content')
    <div class="card" style="text-align:center;">
        <div style="width:60px; height:60px; border-radius:50%; background:var(--accent-bg); color:var(--accent); display:flex; align-items:center; justify-content:center; margin:0 auto 18px; border:1px solid var(--accent-bd);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>

        <h2 style="font-size:22px; font-weight:700; color:var(--text); margin-bottom:8px;">
            SatuSuara Sudah Terinstal
        </h2>
        <p style="font-size:14px; color:var(--text-sec); max-width:460px; margin:0 auto 22px; line-height:1.5;">
            Sistem pemilihan digital SatuSuara telah terpasang dan beroperasi secara normal pada server ini. Demi keamanan integritas data, wizard instalasi telah dikunci.
        </p>

        <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:8px; padding:14px; margin-bottom:24px; text-align:left; font-size:12.5px; color:#92400E; line-height:1.5;">
            <strong>Catatan Administrator:</strong> Jika Anda sengaja ingin melakukan instalasi ulang dari awal, hapus berkas penanda <code>storage/installed</code> pada server Anda terlebih dahulu.
        </div>

        <div style="display:flex; justify-content:center; gap:12px;">
            <a href="{{ route('login') }}" class="btn btn-primary" style="padding:0 24px;">
                <span>Buka Halaman Login &rarr;</span>
            </a>
        </div>
    </div>
@endsection

