@extends('layouts.admin')

@section('title', 'Pengaturan Pemilihan')

@section('content')
    <div style="max-width: 760px; margin: 0 auto; display:flex; flex-direction:column; gap:28px;">

        <!-- CARD 1: GENERAL SETTINGS & LOGO -->
        <div class="card" style="margin-bottom:0;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Konfigurasi Agenda Pemilihan</h2>
                    <div style="font-size:12.5px; color:var(--text-sec); margin-top:2px;">
                        Informasi ini akan dicantumkan secara resmi pada Kop Berita Acara, Bilik Suara, dan Header Aplikasi.
                    </div>
                </div>
                <span class="badge badge-neutral">Sistem</span>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf

                <!-- LOGO INSTANSI SECTION -->
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:24px;">
                    <label class="form-label" style="margin-bottom:10px;">Logo Instansi / Lembaga Penyelenggara</label>
                    
                    <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                        <div style="width:72px; height:72px; border-radius:10px; border:1.5px dashed var(--border-hi); background:#FFFFFF; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                            @if (!empty($settings['logo_instansi']) && file_exists(public_path($settings['logo_instansi'])))
                                <img src="{{ asset($settings['logo_instansi']) }}" alt="Logo Instansi" style="max-width:100%; max-height:100%; object-fit:contain; padding:4px;">
                            @else
                                <div style="text-align:center; color:var(--text-ph); font-size:10px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 2px;">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                    <span>Default</span>
                                </div>
                            @endif
                        </div>

                        <div style="flex:1; min-width:200px;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('logo_instansi').click();" style="display:inline-flex; align-items:center; gap:6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span>Pilih Berkas Logo</span>
                                </button>
                                <span id="logoFileName" style="font-size:12.5px; color:var(--text-sec); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:250px;">
                                    Belum ada berkas baru dipilih
                                </span>
                            </div>
                            <input
                                type="file"
                                name="logo_instansi"
                                id="logo_instansi"
                                accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                style="display:none;"
                                onchange="if(this.files[0]) { document.getElementById('logoFileName').textContent = this.files[0].name; document.getElementById('logoFileName').style.color = 'var(--text)'; }"
                            >
                            <div class="form-text">Rasio disarankan <strong>1:1 (Persegi)</strong> atau logo proporsional horizontal (Maks. 2MB). Format: PNG, JPG, SVG, WebP (disarankan berlatar transparan).</div>

                            @if (!empty($settings['logo_instansi']))
                                <div style="margin-top:8px;">
                                    <label style="display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:var(--err); cursor:pointer;">
                                        <input type="checkbox" name="hapus_logo" value="1" style="accent-color:var(--err);">
                                        <span>Hapus logo kustom &amp; gunakan logo bawaan</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                    @error('logo_instansi')
                        <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- NAMA AGENDA & INSTANSI -->
                <div class="form-group">
                    <label for="election_name" class="form-label">Nama Agenda Pemilihan *</label>
                    <input
                        type="text"
                        name="election_name"
                        id="election_name"
                        value="{{ old('election_name', $settings['election_name'] ?? 'Pemilihan Ketua & Wakil OSIS Periode 2026/2027') }}"
                        required
                        class="form-control {{ $errors->has('election_name') ? 'is-invalid' : '' }}"
                        placeholder="Contoh: Pemilihan Raya Mahasiswa 2026"
                    >
                    <div class="form-text">Judul resmi pemilihan yang muncul pada seluruh laporan dan berita acara.</div>
                    @error('election_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="institution" class="form-label">Nama Instansi / Lembaga Penyelenggara *</label>
                    <input
                        type="text"
                        name="institution"
                        id="institution"
                        value="{{ old('institution', $settings['institution'] ?? 'Komisi Pemilihan Sekolah Indonesia') }}"
                        required
                        class="form-control {{ $errors->has('institution') ? 'is-invalid' : '' }}"
                        placeholder="Contoh: SMA Negeri 1 Jakarta / Universitas Gadjah Mada"
                    >
                    <div class="form-text">Nama instansi yang tercantum pada Kop Surat Berita Acara.</div>
                    @error('institution')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="election_date" class="form-label">Tanggal Pelaksanaan Pemilihan</label>
                    <input
                        type="date"
                        name="election_date"
                        id="election_date"
                        value="{{ old('election_date', $settings['election_date'] ?? date('Y-m-d')) }}"
                        class="form-control {{ $errors->has('election_date') ? 'is-invalid' : '' }}"
                    >
                    @error('election_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- JADWAL WAKTU & STATUS PELAKSANAAN -->
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                        <div>
                            <div style="font-size:14px; font-weight:600; color:var(--text);">Jadwal Waktu &amp; Countdown Pemilihan</div>
                            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                                Jika waktu belum tiba, pemilih otomatis disajikan countdown animasi interaktif.
                            </div>
                        </div>
                        <span class="badge badge-neutral">Auto Countdown</span>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label for="start_time" class="form-label">Waktu Buka / Mulai (Countdown Target)</label>
                            <input
                                type="datetime-local"
                                name="start_time"
                                id="start_time"
                                value="{{ old('start_time', $settings['start_time'] ?? '') }}"
                                class="form-control"
                            >
                            <div class="form-text">Bilik suara akan terkunci dan menampilkan countdown hingga waktu ini.</div>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label for="end_time" class="form-label">Waktu Tutup / Selesai</label>
                            <input
                                type="datetime-local"
                                name="end_time"
                                id="end_time"
                                value="{{ old('end_time', $settings['end_time'] ?? '') }}"
                                class="form-control"
                            >
                            <div class="form-text">Setelah waktu ini, bilik suara otomatis ditutup.</div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label for="status_pelaksanaan" class="form-label">Mode Kontrol Pembukaan Bilik Suara</label>
                        <select name="status_pelaksanaan" id="status_pelaksanaan" class="form-control" style="background:#fff;">
                            <option value="auto" {{ old('status_pelaksanaan', $settings['status_pelaksanaan'] ?? 'auto') === 'auto' ? 'selected' : '' }}>
                                Otomatis Sesuai Jadwal (Buka dan tutup otomatis mengikuti jam di atas)
                            </option>
                            <option value="buka" {{ old('status_pelaksanaan', $settings['status_pelaksanaan'] ?? '') === 'buka' ? 'selected' : '' }}>
                                Paksa Buka Sekarang (Abaikan countdown &amp; buka bilik suara langsung)
                            </option>
                            <option value="tutup" {{ old('status_pelaksanaan', $settings['status_pelaksanaan'] ?? '') === 'tutup' ? 'selected' : '' }}>
                                Kunci / Tutup Sekarang (Hentikan pemungutan suara manual)
                            </option>
                        </select>
                    </div>
                </div>

                <!-- SISTEM ANTRIAN (TRAFFIC & LOAD BALANCING) -->
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                        <div>
                            <div style="font-size:14px; font-weight:600; color:var(--text);">Sistem Antrian Bilik Suara (Virtual Waiting Room)</div>
                            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                                Mengatur antrian otomatis saat ribuan pemilih masuk bersamaan agar server tidak lag / crash.
                            </div>
                        </div>
                        <span class="badge badge-success">Anti-Lag Queue</span>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input
                                type="checkbox"
                                name="enable_queue"
                                value="1"
                                {{ old('enable_queue', $settings['enable_queue'] ?? true) ? 'checked' : '' }}
                                style="width:16px; height:16px; accent-color:var(--accent);"
                            >
                            <span style="font-size:13.5px; font-weight:600; color:var(--text);">Aktifkan Sistem Antrian Bilik Suara</span>
                        </label>
                        <div class="form-text" style="margin-left:24px;">Jika aktif, pemilih akan diarahkan ke ruang tunggu beranimasi jika kapasitas bilik suara penuh.</div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label for="max_concurrent_voters" class="form-label">Kapasitas Maksimal Bersamaan (Slot Bilik)</label>
                            <input
                                type="number"
                                name="max_concurrent_voters"
                                id="max_concurrent_voters"
                                value="{{ old('max_concurrent_voters', $settings['max_concurrent_voters'] ?? 10) }}"
                                min="1"
                                max="500"
                                class="form-control"
                            >
                            <div class="form-text">Maksimal pemilih yang dapat berada di bilik suara paslon pada saat bersamaan (Contoh: 10 pemilih).</div>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label for="booth_session_minutes" class="form-label">Batas Waktu Pemilih di Bilik (Menit)</label>
                            <input
                                type="number"
                                name="booth_session_minutes"
                                id="booth_session_minutes"
                                value="{{ old('booth_session_minutes', $settings['booth_session_minutes'] ?? 3) }}"
                                min="1"
                                max="30"
                                class="form-control"
                            >
                            <div class="form-text">Batas waktu maksimal per sesi di bilik sebelum otomatis dialihkan ke antrian berikutnya (Default: 3 menit).</div>
                        </div>
                    </div>
                </div>

                <!-- MODE BILIK SUARA MANDIRI (SIAGA OTOMATIS TPS) -->
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                        <div>
                            <div style="font-size:14px; font-weight:600; color:var(--text);">Mode Bilik Suara Mandiri (Siaga Otomatis TPS)</div>
                            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                                Mengakhiri sesi pemilih secara otomatis setelah selesai mencoblos agar bilik langsung siap untuk antrean pemilih berikutnya.
                            </div>
                        </div>
                        <span class="badge badge-primary">Bilik Mandiri</span>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input
                                type="checkbox"
                                name="kiosk_mode"
                                value="1"
                                {{ old('kiosk_mode', $settings['kiosk_mode'] ?? false) ? 'checked' : '' }}
                                style="width:16px; height:16px; accent-color:var(--accent);"
                            >
                            <span style="font-size:13.5px; font-weight:600; color:var(--text);">Aktifkan Mode Bilik Mandiri (Keluar Otomatis Setelah Memilih)</span>
                        </label>
                        <div class="form-text" style="margin-left:24px;">Sangat disarankan jika pemilihan dilakukan di komputer bilik suara fisik sekolah/kampus.</div>
                    </div>

                    <div class="form-group" style="margin-bottom:0; max-width:320px;">
                        <label for="kiosk_timeout_seconds" class="form-label">Waktu Tampil Tanda Terima Sebelum Sesi Berakhir (Detik)</label>
                        <input
                            type="number"
                            name="kiosk_timeout_seconds"
                            id="kiosk_timeout_seconds"
                            value="{{ old('kiosk_timeout_seconds', $settings['kiosk_timeout_seconds'] ?? 5) }}"
                            min="3"
                            max="60"
                            class="form-control"
                        >
                        <div class="form-text">Durasi pemilih melihat tanda terima suara sebelum bilik kembali siap untuk pemilih berikutnya (Bawaan: 5 detik).</div>
                    </div>
                </div>

                <!-- OPSI KOTAK KOSONG (CALON TUNGGAL) -->
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                        <div>
                            <div style="font-size:14px; font-weight:600; color:var(--text);">Opsi Kotak Kosong (Calon Tunggal)</div>
                            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                                Regulasi resmi e-voting jika hanya terdapat 1 pasangan calon yang maju dalam pemilihan.
                            </div>
                        </div>
                        <span class="badge badge-neutral">Regulasi Pemilu</span>
                    </div>

                    <div>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input
                                type="checkbox"
                                name="enable_kotak_kosong"
                                value="1"
                                {{ old('enable_kotak_kosong', $settings['enable_kotak_kosong'] ?? false) ? 'checked' : '' }}
                                style="width:16px; height:16px; accent-color:var(--accent);"
                            >
                            <span style="font-size:13.5px; font-weight:600; color:var(--text);">Aktifkan Opsi "Kotak Kosong" saat Pemilihan Calon Tunggal (1 Paslon)</span>
                        </label>
                        <div class="form-text" style="margin-left:24px;">
                            Sesuai regulasi pemilu, opsi Kotak Kosong otomatis tampil HANYA jika kandidat yang terdaftar berjumlah 1 (calon tunggal). Jika terdapat 2 paslon atau lebih, opsi ini otomatis disembunyikan.
                        </div>
                    </div>
                </div>

                <!-- PROTEKSI SINGLE-SESSION LOCKING (ANTI AKUN DIPAKAI BERSAMAAN) -->
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                        <div>
                            <div style="font-size:14px; font-weight:600; color:var(--text);">Proteksi Single-Session Locking (Anti Akun Dipakai Bersamaan)</div>
                            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                                Menjamin prinsip 1 orang 1 suara secara ketat dengan mencegah penggunaan akun ganda di bilik berbeda.
                            </div>
                        </div>
                        <span class="badge badge-success">🔒 Integritas Suara</span>
                    </div>

                    <div>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input
                                type="checkbox"
                                name="enable_single_session"
                                value="1"
                                {{ old('enable_single_session', $settings['enable_single_session'] ?? true) ? 'checked' : '' }}
                                style="width:16px; height:16px; accent-color:var(--accent);"
                            >
                            <span style="font-size:13.5px; font-weight:600; color:var(--text);">Kunci 1 Akun Pemilih Hanya untuk 1 Perangkat Aktif</span>
                        </label>
                        <div class="form-text" style="margin-left:24px;">
                            Jika akun pemilih sedang login di HP atau Bilik 1, akun tersebut tidak bisa dibuka bersamaan di komputer lain. Sesi otomatis dikunci dan hanya berlaku untuk 1 perangkat aktif hingga proses coblos selesai. Panitia dapat mereset sesi di menu Data Pemilih jika bilik terkendala.
                        </div>
                    </div>
                </div>

                <!-- ZONA WAKTU & BAHASA SISTEM (SINKRONISASI) -->
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                        <div>
                            <div style="font-size:14px; font-weight:600; color:var(--text);">Zona Waktu &amp; Bahasa Sistem</div>
                            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                                Sinkronkan seluruh jam server, countdown bilik suara pemilih, dan format tanggal laporan.
                            </div>
                        </div>
                        <span class="badge badge-neutral">Sinkronisasi</span>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label for="timezone" class="form-label">Zona Waktu (Timezone) *</label>
                            <select name="timezone" id="timezone" class="form-control" style="background:#fff; font-size:13px;">
                                @php
                                    $currentTimezone = old('timezone', $settings['timezone'] ?? config('app.timezone', 'Asia/Jakarta'));
                                @endphp

                                <optgroup label="🇮🇩 INDONESIA (WIB, WITA, WIT)">
                                    <option value="Asia/Jakarta" {{ $currentTimezone === 'Asia/Jakarta' ? 'selected' : '' }}>
                                        Asia/Jakarta (WIB — UTC+7: Jawa, Sumatera, Madura) [Default]
                                    </option>
                                    <option value="Asia/Pontianak" {{ $currentTimezone === 'Asia/Pontianak' ? 'selected' : '' }}>
                                        Asia/Pontianak (WIB — UTC+7: Kalimantan Barat &amp; Kalimantan Tengah)
                                    </option>
                                    <option value="Asia/Makassar" {{ $currentTimezone === 'Asia/Makassar' ? 'selected' : '' }}>
                                        Asia/Makassar (WITA — UTC+8: Bali, NTB, NTT, Kalimantan Selatan/Timur/Utara, Sulawesi)
                                    </option>
                                    <option value="Asia/Jayapura" {{ $currentTimezone === 'Asia/Jayapura' ? 'selected' : '' }}>
                                        Asia/Jayapura (WIT — UTC+9: Maluku, Maluku Utara, Seluruh Provinsi Papua)
                                    </option>
                                </optgroup>

                                <optgroup label="🌏 ASIA TENGGARA &amp; ASEAN">
                                    <option value="Asia/Singapore" {{ $currentTimezone === 'Asia/Singapore' ? 'selected' : '' }}>
                                        Asia/Singapore (SGT — UTC+8: Singapura)
                                    </option>
                                    <option value="Asia/Kuala_Lumpur" {{ $currentTimezone === 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>
                                        Asia/Kuala_Lumpur (MYT — UTC+8: Malaysia Barat &amp; Timur)
                                    </option>
                                    <option value="Asia/Bangkok" {{ $currentTimezone === 'Asia/Bangkok' ? 'selected' : '' }}>
                                        Asia/Bangkok (ICT — UTC+7: Thailand, Vietnam, Kamboja, Laos)
                                    </option>
                                    <option value="Asia/Manila" {{ $currentTimezone === 'Asia/Manila' ? 'selected' : '' }}>
                                        Asia/Manila (PHT — UTC+8: Filipina)
                                    </option>
                                    <option value="Asia/Brunei" {{ $currentTimezone === 'Asia/Brunei' ? 'selected' : '' }}>
                                        Asia/Brunei (BNT — UTC+8: Brunei Darussalam)
                                    </option>
                                    <option value="Asia/Dili" {{ $currentTimezone === 'Asia/Dili' ? 'selected' : '' }}>
                                        Asia/Dili (TLT — UTC+9: Timor Leste)
                                    </option>
                                    <option value="Asia/Yangon" {{ $currentTimezone === 'Asia/Yangon' ? 'selected' : '' }}>
                                        Asia/Yangon (MMT — UTC+6:30: Myanmar)
                                    </option>
                                </optgroup>

                                <optgroup label="🇯🇵 ASIA TIMUR &amp; PASIFIK">
                                    <option value="Asia/Tokyo" {{ $currentTimezone === 'Asia/Tokyo' ? 'selected' : '' }}>
                                        Asia/Tokyo (JST — UTC+9: Jepang)
                                    </option>
                                    <option value="Asia/Seoul" {{ $currentTimezone === 'Asia/Seoul' ? 'selected' : '' }}>
                                        Asia/Seoul (KST — UTC+9: Korea Selatan)
                                    </option>
                                    <option value="Asia/Hong_Kong" {{ $currentTimezone === 'Asia/Hong_Kong' ? 'selected' : '' }}>
                                        Asia/Hong_Kong (HKT — UTC+8: Hong Kong)
                                    </option>
                                    <option value="Asia/Taipei" {{ $currentTimezone === 'Asia/Taipei' ? 'selected' : '' }}>
                                        Asia/Taipei (CST — UTC+8: Taiwan)
                                    </option>
                                    <option value="Asia/Shanghai" {{ $currentTimezone === 'Asia/Shanghai' ? 'selected' : '' }}>
                                        Asia/Shanghai (CST — UTC+8: Beijing &amp; Tiongkok)
                                    </option>
                                    <option value="Australia/Perth" {{ $currentTimezone === 'Australia/Perth' ? 'selected' : '' }}>
                                        Australia/Perth (AWST — UTC+8: Australia Barat)
                                    </option>
                                    <option value="Australia/Darwin" {{ $currentTimezone === 'Australia/Darwin' ? 'selected' : '' }}>
                                        Australia/Darwin (ACST — UTC+9:30: Australia Tengah)
                                    </option>
                                    <option value="Australia/Sydney" {{ $currentTimezone === 'Australia/Sydney' ? 'selected' : '' }}>
                                        Australia/Sydney (AEST — UTC+10: Sydney &amp; Melbourne)
                                    </option>
                                    <option value="Pacific/Auckland" {{ $currentTimezone === 'Pacific/Auckland' ? 'selected' : '' }}>
                                        Pacific/Auckland (NZST — UTC+12: Selandia Baru)
                                    </option>
                                </optgroup>

                                <optgroup label="🕋 TIMUR TENGAH &amp; ASIA SELATAN">
                                    <option value="Asia/Riyadh" {{ $currentTimezone === 'Asia/Riyadh' ? 'selected' : '' }}>
                                        Asia/Riyadh (AST — UTC+3: Arab Saudi, Mekkah, Madinah)
                                    </option>
                                    <option value="Asia/Dubai" {{ $currentTimezone === 'Asia/Dubai' ? 'selected' : '' }}>
                                        Asia/Dubai (GST — UTC+4: Uni Emirat Arab &amp; Dubai)
                                    </option>
                                    <option value="Asia/Qatar" {{ $currentTimezone === 'Asia/Qatar' ? 'selected' : '' }}>
                                        Asia/Qatar (AST — UTC+3: Qatar &amp; Doha)
                                    </option>
                                    <option value="Asia/Kuwait" {{ $currentTimezone === 'Asia/Kuwait' ? 'selected' : '' }}>
                                        Asia/Kuwait (AST — UTC+3: Kuwait)
                                    </option>
                                    <option value="Asia/Kolkata" {{ $currentTimezone === 'Asia/Kolkata' ? 'selected' : '' }}>
                                        Asia/Kolkata (IST — UTC+5:30: India &amp; New Delhi)
                                    </option>
                                    <option value="Asia/Karachi" {{ $currentTimezone === 'Asia/Karachi' ? 'selected' : '' }}>
                                        Asia/Karachi (PKT — UTC+5: Pakistan)
                                    </option>
                                    <option value="Asia/Dhaka" {{ $currentTimezone === 'Asia/Dhaka' ? 'selected' : '' }}>
                                        Asia/Dhaka (BST — UTC+6: Bangladesh)
                                    </option>
                                </optgroup>

                                <optgroup label="🇪🇺 EROPA &amp; AFRIKA">
                                    <option value="Europe/London" {{ $currentTimezone === 'Europe/London' ? 'selected' : '' }}>
                                        Europe/London (GMT/BST — UTC+0/+1: Inggris &amp; London)
                                    </option>
                                    <option value="Europe/Paris" {{ $currentTimezone === 'Europe/Paris' ? 'selected' : '' }}>
                                        Europe/Paris (CET — UTC+1: Prancis &amp; Paris)
                                    </option>
                                    <option value="Europe/Berlin" {{ $currentTimezone === 'Europe/Berlin' ? 'selected' : '' }}>
                                        Europe/Berlin (CET — UTC+1: Jerman &amp; Berlin)
                                    </option>
                                    <option value="Europe/Amsterdam" {{ $currentTimezone === 'Europe/Amsterdam' ? 'selected' : '' }}>
                                        Europe/Amsterdam (CET — UTC+1: Belanda &amp; Amsterdam)
                                    </option>
                                    <option value="Europe/Rome" {{ $currentTimezone === 'Europe/Rome' ? 'selected' : '' }}>
                                        Europe/Rome (CET — UTC+1: Italia &amp; Roma)
                                    </option>
                                    <option value="Europe/Madrid" {{ $currentTimezone === 'Europe/Madrid' ? 'selected' : '' }}>
                                        Europe/Madrid (CET — UTC+1: Spanyol &amp; Madrid)
                                    </option>
                                    <option value="Europe/Istanbul" {{ $currentTimezone === 'Europe/Istanbul' ? 'selected' : '' }}>
                                        Europe/Istanbul (TRT — UTC+3: Turki &amp; Istanbul)
                                    </option>
                                    <option value="Africa/Cairo" {{ $currentTimezone === 'Africa/Cairo' ? 'selected' : '' }}>
                                        Africa/Cairo (EEST — UTC+2: Mesir &amp; Kairo)
                                    </option>
                                    <option value="Africa/Johannesburg" {{ $currentTimezone === 'Africa/Johannesburg' ? 'selected' : '' }}>
                                        Africa/Johannesburg (SAST — UTC+2: Afrika Selatan)
                                    </option>
                                </optgroup>

                                <optgroup label="🌐 GLOBAL &amp; AMERIKA">
                                    <option value="UTC" {{ $currentTimezone === 'UTC' ? 'selected' : '' }}>
                                        UTC (Coordinated Universal Time — Standar Internasional)
                                    </option>
                                    <option value="America/New_York" {{ $currentTimezone === 'America/New_York' ? 'selected' : '' }}>
                                        America/New_York (EST/EDT — UTC-5/-4: New York, Washington D.C., Florida)
                                    </option>
                                    <option value="America/Chicago" {{ $currentTimezone === 'America/Chicago' ? 'selected' : '' }}>
                                        America/Chicago (CST/CDT — UTC-6/-5: Chicago, Texas)
                                    </option>
                                    <option value="America/Denver" {{ $currentTimezone === 'America/Denver' ? 'selected' : '' }}>
                                        America/Denver (MST/MDT — UTC-7/-6: Denver, Colorado, Arizona)
                                    </option>
                                    <option value="America/Los_Angeles" {{ $currentTimezone === 'America/Los_Angeles' ? 'selected' : '' }}>
                                        America/Los_Angeles (PST/PDT — UTC-8/-7: California, Los Angeles, San Francisco)
                                    </option>
                                    <option value="America/Sao_Paulo" {{ $currentTimezone === 'America/Sao_Paulo' ? 'selected' : '' }}>
                                        America/Sao_Paulo (BRT — UTC-3: Brasilia &amp; São Paulo)
                                    </option>
                                    <option value="Pacific/Honolulu" {{ $currentTimezone === 'Pacific/Honolulu' ? 'selected' : '' }}>
                                        Pacific/Honolulu (HST — UTC-10: Hawaii)
                                    </option>
                                </optgroup>
                            </select>
                            <div class="form-text">Mempengaruhi target waktu countdown voter dan timestamp audit suara.</div>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label for="locale" class="form-label">Bahasa Sistem (Locale) *</label>
                            <select name="locale" id="locale" class="form-control" style="background:#fff;">
                                @php
                                    $currentLocale = old('locale', $settings['locale'] ?? config('app.locale', 'id'));
                                @endphp
                                <option value="id" {{ $currentLocale === 'id' ? 'selected' : '' }}>
                                    Bahasa Indonesia (ID) [Default]
                                </option>
                                <option value="en" {{ $currentLocale === 'en' ? 'selected' : '' }}>
                                    English (EN)
                                </option>
                            </select>
                            <div class="form-text">Format nama hari, bulan, angka statistik, dan berita acara.</div>
                        </div>
                    </div>

                    <div style="background:#fff; border:1px solid var(--border); border-radius:8px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10B981;"></span>
                            <span style="font-size:12px; color:var(--text-sec);">Waktu Server Terkini:</span>
                            <strong style="font-size:13px; font-family:monospace; color:var(--text);">
                                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} ({{ config('app.timezone') }})
                            </strong>
                        </div>
                        <div style="font-size:11.5px; color:var(--text-sec);">
                            Bahasa Aktif: <strong style="color:var(--accent);">{{ config('app.locale') === 'id' ? 'Bahasa Indonesia' : 'English' }}</strong>
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px; padding-top:16px; border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- CARD 2: DANGER ZONE (PEMBERSIHAN DATA PEMILIHAN) -->
        <div class="card" style="margin-bottom:0; border-color:var(--err-bd);">
            <div class="card-header" style="border-bottom-color:var(--err-bd);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:32px; height:32px; border-radius:8px; background:var(--err-bg); color:var(--err); display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="card-title" style="color:var(--err);">Pembersihan Data (Danger Zone)</h2>
                        <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                            Gunakan opsi ini saat ingin mengosongkan data uji coba atau mempersiapkan pemilihan baru.
                        </div>
                    </div>
                </div>
                <span class="badge" style="background:var(--err-bg); color:var(--err); border:1px solid var(--err-bd);">Kritis</span>
            </div>

            <!-- Ringkasan Data Saat Ini -->
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; margin-bottom:20px;">
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:8px; padding:12px; text-align:center;">
                    <div style="font-size:11px; color:var(--text-sec); text-transform:uppercase;">DPT Terdaftar</div>
                    <div style="font-size:22px; font-weight:700; color:var(--text); margin-top:2px;">{{ $stats['voters_count'] }}</div>
                </div>
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:8px; padding:12px; text-align:center;">
                    <div style="font-size:11px; color:var(--text-sec); text-transform:uppercase;">Paslon Aktif</div>
                    <div style="font-size:22px; font-weight:700; color:var(--text); margin-top:2px;">{{ $stats['candidates_count'] }}</div>
                </div>
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:8px; padding:12px; text-align:center;">
                    <div style="font-size:11px; color:var(--text-sec); text-transform:uppercase;">Suara Masuk</div>
                    <div style="font-size:22px; font-weight:700; color:var(--text); margin-top:2px;">{{ $stats['votes_count'] }}</div>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:14px;">
                <!-- Opsi 1: Clear Pemilih & Suara -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:#FAFBFC; border:1px solid var(--border); border-radius:8px; flex-wrap:wrap; gap:12px;">
                    <div>
                        <div style="font-size:13.5px; font-weight:600; color:var(--text);">Hapus Semua Pemilih (DPT) &amp; Suara</div>
                        <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                            Menghapus seluruh akun pemilih dan seluruh riwayat suara. Akun administrator tetap aman.
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" style="color:var(--err); border-color:var(--err-bd);" onclick="openPurgeModal('{{ route('admin.settings.clear-voters') }}', 'Kosongkan Seluruh Data Pemilih (DPT)', 'PERINGATAN: Seluruh akun pemilih (DPT) dan riwayat perolehan suara akan dihapus permanen.')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        <span>Kosongkan DPT</span>
                    </button>
                </div>

                <!-- Opsi 2: Clear Paslon -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:#FAFBFC; border:1px solid var(--border); border-radius:8px; flex-wrap:wrap; gap:12px;">
                    <div>
                        <div style="font-size:13.5px; font-weight:600; color:var(--text);">Hapus Semua Pasangan Calon (Paslon)</div>
                        <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                            Menghapus seluruh kandidat beserta berkas foto dari penyimpanan dan suara terkait.
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" style="color:var(--err); border-color:var(--err-bd);" onclick="openPurgeModal('{{ route('admin.settings.clear-candidates') }}', 'Hapus Seluruh Data Pasangan Calon', 'PERINGATAN: Seluruh pasangan calon dan foto mereka akan dihapus permanen dari server.')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        <span>Hapus Semua Paslon</span>
                    </button>
                </div>

                <!-- Opsi 3: Full Reset -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding:16px; background:var(--err-bg); border:1px solid var(--err-bd); border-radius:8px; flex-wrap:wrap; gap:12px;">
                    <div>
                        <div style="font-size:14px; font-weight:700; color:var(--err);">Reset Total Pemilihan (DPT + Paslon + Suara)</div>
                        <div style="font-size:12px; color:#7F1D1D; margin-top:2px;">
                            Mengembalikan database pemilihan ke kondisi kosong murni. Akun admin dan pengaturan agenda tetap dipertahankan.
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" style="background:var(--err); color:#fff; border:none; padding:7px 14px;" onclick="openPurgeModal('{{ route('admin.settings.clear-all') }}', 'Reset Total Pemilihan (DPT + Paslon + Suara)', 'PERINGATAN KRITIS: Seluruh data pemilihan akan dikembalikan ke kondisi kosong murni. Tindakan ini TIDAK DAPAT DIBATALKAN!')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                        </svg>
                        <span>Reset Total Pemilihan</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL KONFIRMASI KEAMANAN DANGER ZONE DENGAN KATA SANDI -->
    <div id="purgeModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.5); backdrop-filter:blur(3px); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:12px; width:100%; max-width:440px; padding:24px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); margin:16px; border:1px solid var(--border);">
            <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:16px;">
                <div style="width:40px; height:40px; border-radius:10px; background:var(--err-bg); color:var(--err); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h3 id="purgeModalTitle" style="font-size:16px; font-weight:700; color:var(--text); margin-bottom:4px;">Konfirmasi Pembersihan Data</h3>
                    <p id="purgeModalWarning" style="font-size:12.5px; color:var(--text-sec); line-height:1.5;">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                </div>
            </div>

            <form id="purgeModalForm" method="POST" action="">
                @csrf
                <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; padding:12px; margin-bottom:16px;">
                    <div style="font-size:12px; font-weight:600; color:#991B1B; margin-bottom:4px;">🔒 Verifikasi Kata Sandi Administrator</div>
                    <div style="font-size:11.5px; color:#B91C1C; margin-bottom:8px;">Masukkan kata sandi akun Anda untuk memvalidasi otorisasi tindakan ini:</div>
                    <input
                        type="password"
                        name="password"
                        id="purgePasswordInput"
                        required
                        placeholder="Kata sandi administrator Anda"
                        class="form-control"
                        style="background:#fff;"
                    >
                </div>

                <div style="display:flex; justify-content:flex-end; gap:8px;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closePurgeModal()">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm" style="background:var(--err); color:#fff; border:none; padding:6px 12px;">
                        Konfirmasi &amp; Hapus Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openPurgeModal(actionUrl, title, warningText) {
        document.getElementById('purgeModalForm').action = actionUrl;
        document.getElementById('purgeModalTitle').textContent = title;
        document.getElementById('purgeModalWarning').textContent = warningText;
        document.getElementById('purgePasswordInput').value = '';
        document.getElementById('purgeModal').style.display = 'flex';
        setTimeout(function() {
            document.getElementById('purgePasswordInput').focus();
        }, 50);
    }

    function closePurgeModal() {
        document.getElementById('purgeModal').style.display = 'none';
        document.getElementById('purgePasswordInput').value = '';
    }
    </script>
@endsection
