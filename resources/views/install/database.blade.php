@extends('install.layout')

@section('title', 'Langkah 2: Konfigurasi Database')

@section('content')
    <!-- Step Bar -->
    <div class="step-bar">
        <div class="step-item passed">
            <span class="step-num">✓</span>
            <span class="step-title">Prasyarat</span>
        </div>
        <span class="step-arrow">&rarr;</span>
        <div class="step-item active">
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
            <h2 class="card-title">2. Sambungan Database MySQL / MariaDB</h2>
            <p class="card-desc">Masukkan informasi kredensial basis data Anda. Wizard akan memvalidasi koneksi secara langsung.</p>
        </div>

        <form id="dbForm" onsubmit="event.preventDefault(); goToStepAdmin();">
            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:12px;">
                <div class="form-group">
                    <label for="db_host" class="form-label">Host Server Database *</label>
                    <input
                        type="text"
                        id="db_host"
                        name="db_host"
                        value="{{ old('db_host', $currentDb['host']) }}"
                        required
                        class="form-control"
                        placeholder="127.0.0.1 atau localhost"
                    >
                </div>

                <div class="form-group">
                    <label for="db_port" class="form-label">Port *</label>
                    <input
                        type="number"
                        id="db_port"
                        name="db_port"
                        value="{{ old('db_port', $currentDb['port']) }}"
                        required
                        class="form-control"
                        placeholder="3306"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="db_name" class="form-label">Nama Database *</label>
                <input
                    type="text"
                    id="db_name"
                    name="db_name"
                    value="{{ old('db_name', $currentDb['database']) }}"
                    required
                    class="form-control"
                    placeholder="Contoh: satusuara"
                >
                <div class="form-text">Jika database belum ada di server MySQL Anda, sistem akan berusaha membuatnya secara otomatis.</div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label for="db_user" class="form-label">Username Database *</label>
                    <input
                        type="text"
                        id="db_user"
                        name="db_user"
                        value="{{ old('db_user', $currentDb['username']) }}"
                        required
                        class="form-control"
                        placeholder="root"
                    >
                </div>

                <div class="form-group">
                    <label for="db_password" class="form-label">Password Database</label>
                    <input
                        type="password"
                        id="db_password"
                        name="db_password"
                        value="{{ old('db_password', $currentDb['password']) }}"
                        class="form-control"
                        placeholder="Kosongkan jika tanpa sandi"
                    >
                </div>
            </div>

            <!-- AJAX Test Status Box -->
            <div id="testStatusBox" style="display:none; margin-bottom:18px;"></div>

            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:24px; padding-top:18px; border-top:1px solid #F2F4F7;">
                <a href="{{ route('install.index') }}" class="btn btn-secondary">
                    &larr; Kembali
                </a>

                <div style="display:flex; gap:10px;">
                    <button type="button" id="btnTestDb" class="btn btn-secondary" onclick="testConnection()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                            <path d="m12 12 4 4"/>
                            <path d="m16 12-4 4"/>
                        </svg>
                        <span id="btnTestText">Uji Koneksi</span>
                    </button>

                    <button type="button" id="btnContinue" class="btn btn-primary" onclick="goToStepAdmin()">
                        <span>Lanjut ke Akun Admin &rarr;</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    let isTested = false;

    async function testConnection() {
        const btn = document.getElementById('btnTestDb');
        const text = document.getElementById('btnTestText');
        const box = document.getElementById('testStatusBox');

        const host = document.getElementById('db_host').value.trim();
        const port = document.getElementById('db_port').value.trim();
        const database = document.getElementById('db_name').value.trim();
        const username = document.getElementById('db_user').value.trim();
        const password = document.getElementById('db_password').value;

        if (!host || !database || !username) {
            alert('Silakan isi Host, Nama Database, dan Username terlebih dahulu.');
            return;
        }

        btn.disabled = true;
        text.innerText = 'Menguji...';
        box.style.display = 'none';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch("{{ route('install.test-database') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ host, port, database, username, password })
            });

            const data = await res.json();
            box.style.display = 'block';

            if (data.success) {
                isTested = true;
                box.className = 'alert alert--ok';
                box.innerHTML = `<strong>✓ Berhasil!</strong> ${data.message}`;
            } else {
                isTested = false;
                box.className = 'alert alert--err';
                box.innerHTML = `<strong>✕ Gagal:</strong> ${data.message}`;
            }
        } catch (err) {
            box.style.display = 'block';
            box.className = 'alert alert--err';
            box.innerHTML = `<strong>✕ Gagal menghubungi server:</strong> ${err.message}`;
        } finally {
            btn.disabled = false;
            text.innerText = 'Uji Koneksi';
        }
    }

    function goToStepAdmin() {
        const host = document.getElementById('db_host').value.trim();
        const port = document.getElementById('db_port').value.trim();
        const database = document.getElementById('db_name').value.trim();
        const username = document.getElementById('db_user').value.trim();
        const password = document.getElementById('db_password').value;

        if (!host || !database || !username) {
            alert('Silakan lengkapi data konfigurasi database.');
            return;
        }

        // Store temporarily in sessionStorage
        sessionStorage.setItem('satusuara_db_host', host);
        sessionStorage.setItem('satusuara_db_port', port);
        sessionStorage.setItem('satusuara_db_name', database);
        sessionStorage.setItem('satusuara_db_user', username);
        sessionStorage.setItem('satusuara_db_password', password);

        window.location.href = "{{ route('install.admin') }}";
    }
</script>
@endpush

