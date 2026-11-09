@extends('install.layout')

@section('title', 'Langkah 3: Konfigurasi Instansi & Akun Admin')

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
        <div class="step-item active">
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
            <h2 class="card-title">3. Informasi Instansi &amp; Akun Administrator</h2>
            <p class="card-desc">Lengkapi nama lembaga penyelenggara serta kredensial akun administrator utama.</p>
        </div>

        <form method="POST" action="{{ route('install.process') }}" id="installForm" onsubmit="return syncHiddenDbFields();">
            @csrf

            <!-- Hidden Database Values from SessionStorage -->
            <input type="hidden" name="db_host" id="hidden_db_host" value="{{ env('DB_HOST', '127.0.0.1') }}">
            <input type="hidden" name="db_port" id="hidden_db_port" value="{{ env('DB_PORT', '3306') }}">
            <input type="hidden" name="db_name" id="hidden_db_name" value="{{ env('DB_DATABASE', 'satusuara') }}">
            <input type="hidden" name="db_user" id="hidden_db_user" value="{{ env('DB_USERNAME', 'root') }}">
            <input type="hidden" name="db_password" id="hidden_db_password" value="{{ env('DB_PASSWORD', '') }}">

            <!-- BAGIAN 1: IDENTITAS LEMBAGA & PEMILIHAN -->
            <div style="margin-bottom:24px;">
                <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:12px; text-transform:uppercase; letter-spacing:0.04em;">A. Agenda Pemilihan</div>

                <div class="form-group">
                    <label for="institution_name" class="form-label">Nama Lembaga / Instansi Penyelenggara *</label>
                    <input
                        type="text"
                        id="institution_name"
                        name="institution_name"
                        value="{{ old('institution_name') }}"
                        required
                        class="form-control"
                        placeholder="Contoh: SMA Negeri 1 Indonesia / BEM Universitas"
                    >
                    <div class="form-text">Nama ini akan tercantum di kop Berita Acara, Bilik Suara, dan Header aplikasi.</div>
                </div>

                <div class="form-group">
                    <label for="election_name" class="form-label">Nama Agenda Pemilihan *</label>
                    <input
                        type="text"
                        id="election_name"
                        name="election_name"
                        value="{{ old('election_name') }}"
                        required
                        class="form-control"
                        placeholder="Contoh: Pemilihan Raya Ketua OSIS 2026/2027"
                    >
                </div>
            </div>

            <!-- BAGIAN 2: AKUN ADMINISTRATOR UTAMA -->
            <div style="margin-bottom:24px; padding-top:16px; border-top:1px solid #F2F4F7;">
                <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:12px; text-transform:uppercase; letter-spacing:0.04em;">B. Akun Administrator Sistem (Superadmin)</div>

                <div class="form-group">
                    <label for="admin_name" class="form-label">Nama Lengkap Administrator *</label>
                    <input
                        type="text"
                        id="admin_name"
                        name="admin_name"
                        value="{{ old('admin_name') }}"
                        required
                        class="form-control"
                        placeholder="Contoh: Muhammad Ihsan"
                    >
                </div>

                <div class="form-group">
                    <label for="admin_email" class="form-label">Alamat Email Login *</label>
                    <input
                        type="email"
                        id="admin_email"
                        name="admin_email"
                        value="{{ old('admin_email') }}"
                        required
                        class="form-control"
                        placeholder="Contoh: admin@instansi.sch.id"
                    >
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label for="admin_password" class="form-label">Password *</label>
                        <input
                            type="password"
                            id="admin_password"
                            name="admin_password"
                            required
                            minlength="8"
                            class="form-control"
                            placeholder="Minimal 8 karakter"
                        >
                    </div>

                    <div class="form-group">
                        <label for="admin_password_confirmation" class="form-label">Ulangi Password *</label>
                        <input
                            type="password"
                            id="admin_password_confirmation"
                            name="admin_password_confirmation"
                            required
                            minlength="8"
                            class="form-control"
                            placeholder="Ketik ulang password"
                        >
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding-top:18px; border-top:1px solid #F2F4F7;">
                <a href="{{ route('install.database') }}" class="btn btn-secondary">
                    &larr; Kembali
                </a>

                <button type="submit" id="btnSubmitInstall" class="btn btn-primary" style="padding:0 28px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span id="btnSubmitText">Mulai Proses Instalasi &rarr;</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    // Hydrate hidden DB fields from sessionStorage if available
    document.addEventListener('DOMContentLoaded', function() {
        const h = sessionStorage.getItem('satusuara_db_host');
        const p = sessionStorage.getItem('satusuara_db_port');
        const n = sessionStorage.getItem('satusuara_db_name');
        const u = sessionStorage.getItem('satusuara_db_user');
        const pass = sessionStorage.getItem('satusuara_db_password');

        if (h) document.getElementById('hidden_db_host').value = h;
        if (p) document.getElementById('hidden_db_port').value = p;
        if (n) document.getElementById('hidden_db_name').value = n;
        if (u) document.getElementById('hidden_db_user').value = u;
        if (pass !== null) document.getElementById('hidden_db_password').value = pass;
    });

    function syncHiddenDbFields() {
        const pass1 = document.getElementById('admin_password').value;
        const pass2 = document.getElementById('admin_password_confirmation').value;

        if (pass1 !== pass2) {
            alert('Konfirmasi kata sandi tidak cocok. Silakan periksa kembali.');
            return false;
        }

        const btn = document.getElementById('btnSubmitInstall');
        const text = document.getElementById('btnSubmitText');
        if (text) text.innerText = 'Menginstal Sistem & Menjalankan Migrasi...';
        setTimeout(function() {
            if (btn) btn.disabled = true;
        }, 10);
        return true;
    }
</script>
@endpush

