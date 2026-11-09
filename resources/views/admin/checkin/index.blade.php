@extends('layouts.admin')

@section('title', 'Presensi TPS')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <p style="font-size:13px; color:var(--text-sec); margin-top:2px;">
                Verifikasi kehadiran pemilih di meja TPS menggunakan token kartu, NISN, nama, atau email pemilih.
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="badge badge-success" style="font-size:12px; padding:6px 14px;">
                <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:#10B981; margin-right:6px;"></span>
                Meja Registrasi TPS Aktif
            </span>
        </div>
    </div>

    <!-- STATS ROW -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:18px 20px; margin-bottom:0;">
            <div style="font-size:12px; font-weight:600; color:var(--text-sec); text-transform:uppercase; letter-spacing:0.04em;">Total Hak Pilih (DPT)</div>
            <div style="font-size:32px; font-weight:800; color:var(--text); line-height:1.2; margin-top:4px;">{{ $totalVoters }}</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">Pemilih terdaftar resmi</div>
        </div>

        <div class="card" style="padding:18px 20px; margin-bottom:0; border-color:var(--ok-bd);">
            <div style="font-size:12px; font-weight:600; color:var(--ok); text-transform:uppercase; letter-spacing:0.04em;">Hadir di TPS</div>
            <div style="font-size:32px; font-weight:800; color:var(--ok); line-height:1.2; margin-top:4px;" id="statCheckedIn">{{ $checkedInCount }}</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">Telah terverifikasi masuk</div>
        </div>

        <div class="card" style="padding:18px 20px; margin-bottom:0;">
            <div style="font-size:12px; font-weight:600; color:var(--text-sec); text-transform:uppercase; letter-spacing:0.04em;">Belum Hadir</div>
            <div style="font-size:32px; font-weight:800; color:var(--text); line-height:1.2; margin-top:4px;" id="statNotCheckedIn">{{ $notCheckedInCount }}</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">Belum tiba di meja TPS</div>
        </div>

        <div class="card" style="padding:18px 20px; margin-bottom:0; border-color:#C7D3F0;">
            <div style="font-size:12px; font-weight:600; color:var(--accent); text-transform:uppercase; letter-spacing:0.04em;">Tingkat Partisipasi TPS</div>
            <div style="font-size:32px; font-weight:800; color:var(--accent); line-height:1.2; margin-top:4px;" id="statPct">{{ $attendancePercentage }}%</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">Persentase kehadiran fisik</div>
        </div>
    </div>

    <!-- MAIN INTERACTIVE GRID -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap:24px; margin-bottom:32px;">
        
        <!-- KOLOM KIRI: FORM PRESENSI KILAT -->
        <div class="card" style="margin-bottom:0; display:flex; flex-direction:column;">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent);">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <polyline points="17 11 19 13 23 9"/>
                    </svg>
                    <h2 class="card-title">Registrasi Presensi Kilat</h2>
                </div>
                <span class="badge badge-primary" style="font-size:11px;">Input &amp; Enter</span>
            </div>

            <div style="padding:20px; display:flex; flex-direction:column; gap:16px;">
                <div style="background:#F8FAFC; border:1px solid var(--border); border-radius:10px; padding:18px;">
                    <label for="manualTokenInput" class="form-label" style="font-weight:600; margin-bottom:6px; font-size:13.5px; color:var(--text);">
                        Ketik Token / NISN / Email Pemilih
                    </label>
                    <p style="font-size:12px; color:var(--text-sec); margin-bottom:14px; line-height:1.4;">
                        Petugas cukup mengetik token presensi (misal: <code style="font-family:monospace; color:var(--accent); font-weight:700;">SS-XXXX-XXXX</code>) atau alamat email pemilih, lalu tekan <strong>Enter</strong>.
                    </p>
                    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:stretch;">
                        <input
                            type="text"
                            id="manualTokenInput"
                            placeholder="Contoh: SS-8F2A-99B1 atau pemilih@sekolah.sch.id"
                            class="form-control"
                            style="flex: 1 1 200px; min-width: 0; height: 46px; font-size: 14.5px; padding: 10px 16px; border-radius: 8px; font-family:inherit; background:#FFFFFF;"
                            autocomplete="off"
                            autofocus
                        >
                        <button type="button" class="btn btn-primary" onclick="submitManualCheckIn()" style="white-space:nowrap; height: 46px; padding: 0 20px; font-weight: 600; font-size: 14px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Presensi (Enter)</span>
                        </button>
                    </div>
                </div>

                <!-- Petunjuk Petugas TPS -->
                <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:14px; display:flex; gap:12px; align-items:flex-start;">
                    <div style="font-size:18px; line-height:1; color:#16A34A; flex-shrink:0;">💡</div>
                    <div style="font-size:12.5px; color:#166534; line-height:1.5;">
                        <strong>Alur Registrasi Meja TPS:</strong><br>
                        1. Pemilih menyebutkan nama, NISN, atau menunjukkan kartu pemilih.<br>
                        2. Masukkan ke kolom di atas atau cari langsung pada daftar di bawah dan klik <strong>"Tandai Hadir"</strong>.<br>
                        3. Pemilih yang sudah hadir dapat langsung masuk bilik suara untuk mencoblos.
                    </div>
                </div>

                <!-- FEEDBACK LIVE NOTIFICATION BOX -->
                <div id="scanFeedbackBox" style="display:none; padding:16px 18px; border-radius:10px; border:1px solid transparent; animation:fadeIn 0.25s ease;">
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div id="scanFeedbackIcon" style="font-size:24px; line-height:1; flex-shrink:0;"></div>
                        <div style="flex:1;">
                            <div id="scanFeedbackTitle" style="font-weight:700; font-size:15px;"></div>
                            <div id="scanFeedbackBody" style="font-size:13px; margin-top:3px; line-height:1.5;"></div>
                            <div id="scanFeedbackMeta" style="margin-top:8px; font-size:12px; font-family:monospace; color:var(--text-sec);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: RECENT CHECK-INS -->
        <div class="card" style="margin-bottom:0; display:flex; flex-direction:column;">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--ok);">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <h2 class="card-title">Aktivitas Presensi Terbaru</h2>
                </div>
                <span class="badge badge-neutral" style="font-size:11px;">Realtime TPS Stream</span>
            </div>

            <div style="flex:1; overflow-y:auto; max-height:480px; padding:16px;">
                <div id="recentCheckinsList" style="display:flex; flex-direction:column; gap:10px;">
                    @forelse ($recentCheckins as $rc)
                        <div class="recent-checkin-item" style="display:flex; justify-content:space-between; align-items:center; background:#FAFBFC; border:1px solid var(--border); border-radius:8px; padding:12px 14px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; border-radius:50%; background:var(--ok-bg); border:1px solid var(--ok-bd); color:var(--ok); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">
                                    ✓
                                </div>
                                <div>
                                    <strong style="font-size:13.5px; color:var(--text);">{{ $rc->name }}</strong>
                                    <div style="font-size:11.5px; color:var(--text-sec); font-family:monospace;">{{ $rc->attendance_token ?? $rc->email }}</div>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <span class="badge badge-success" style="font-size:11px;">Hadir</span>
                                <div style="font-size:11px; color:var(--text-sec); margin-top:2px; font-family:monospace;">
                                    {{ \Carbon\Carbon::parse($rc->checked_in_at)->format('H:i:s') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="text-align:center; padding:32px 16px; color:var(--text-sec); font-size:13px;" id="emptyRecentText">
                            Belum ada pemilih yang melakukan presensi TPS hari ini.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- TABEL DAFTAR PEMILIH & STATUS PRESENSI -->
    <div class="card">
        <div class="card-header" style="flex-wrap:wrap; gap:12px;">
            <div>
                <h2 class="card-title">Daftar Kehadiran Seluruh Pemilih (DPT)</h2>
                <div style="font-size:12.5px; color:var(--text-sec); margin-top:2px;">
                    Status kehadiran seluruh pemilih terdaftar untuk verifikasi manual meja panitia.
                </div>
            </div>

            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('admin.checkin.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, NISN, token..."
                    class="form-control"
                    style="width:200px;"
                >
                <select name="status" class="form-control" style="width:auto; background:#fff;">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Hadir</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.checkin.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Pemilih</th>
                        <th>Token Presensi</th>
                        <th>Email / Akun</th>
                        <th>Status TPS</th>
                        <th>Waktu Presensi</th>
                        <th style="width:140px; text-align:center;">Aksi Manual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($votersList as $v)
                        <tr>
                            <td>
                                <strong style="font-size:13.5px; color:var(--text); white-space:nowrap;">{{ $v->name }}</strong>
                                @if ($v->sudahVoting())
                                    <span class="badge badge-primary" style="font-size:10px; margin-left:6px; white-space:nowrap;">Sudah Memilih</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-neutral" style="font-family:monospace; font-size:12px; font-weight:700; color:var(--accent); background:#EEF1F9; padding:4px 9px; border-radius:6px; border:1px solid #C7D3F0; white-space:nowrap; letter-spacing:0.02em;">
                                    {{ $v->attendance_token ?? '-' }}
                                </span>
                            </td>
                            <td style="font-size:12.5px; color:var(--text-sec);">{{ $v->email }}</td>
                            <td>
                                @if ($v->isCheckedIn())
                                    <span class="badge badge-success">✓ Hadir di TPS</span>
                                @else
                                    <span class="badge badge-neutral">Belum Hadir</span>
                                @endif
                            </td>
                            <td style="font-size:12px; color:var(--text-sec); font-family:monospace;">
                                {{ $v->checked_in_at ? \Carbon\Carbon::parse($v->checked_in_at)->translatedFormat('d M Y, H:i') : '-' }}
                            </td>
                            <td style="text-align:center;">
                                <form method="POST" action="{{ route('admin.checkin.toggle', $v->id) }}" style="display:inline;">
                                    @csrf
                                    @if ($v->isCheckedIn())
                                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size:11px; padding:3px 8px;" title="Batalkan presensi jika salah klik">
                                            Batal Hadir
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-primary btn-sm" style="font-size:11px; padding:3px 10px;">
                                            Tandai Hadir
                                        </button>
                                    @endif
                                </form>

                                @if ($v->hasActiveSession())
                                    <form method="POST" action="{{ route('admin.voters.reset-session', $v->id) }}" style="display:inline; margin-left:4px;" onsubmit="return confirm('Reset kunci sesi perangkat pemilih {{ $v->name }}? Sesi aktif di perangkat sebelumnya akan dihentikan.');">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size:11px; padding:3px 8px; color:var(--accent); background:#EEF1F9; border-color:#C7D3F0;" title="Buka kunci sesi perangkat (Bilik / HP)">
                                            Buka Sesi
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:32px; color:var(--text-sec);">
                                Tidak ada data pemilih yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($votersList->hasPages())
            <div style="padding:16px 20px; border-top:1px solid var(--border);">
                {{ $votersList->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    const csrfToken = "{{ csrf_token() }}";
    const scanUrl = "{{ route('admin.checkin.scan') }}";

    // Input Manual
    const manualInput = document.getElementById('manualTokenInput');
    if (manualInput) {
        manualInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitManualCheckIn();
            }
        });
    }

    function submitManualCheckIn() {
        const input = document.getElementById('manualTokenInput');
        const token = input.value.trim();
        if (!token) return;

        sendCheckInRequest(token);
        input.value = '';
    }

    function sendCheckInRequest(token) {
        fetch(scanUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ token: token })
        })
        .then(res => res.json())
        .then(data => {
            displayScanFeedback(data);
        })
        .catch(err => {
            displayScanFeedback({
                success: false,
                message: 'Terjadi gangguan jaringan saat memverifikasi token presensi.'
            });
        });
    }

    function displayScanFeedback(data) {
        const box = document.getElementById('scanFeedbackBox');
        const icon = document.getElementById('scanFeedbackIcon');
        const title = document.getElementById('scanFeedbackTitle');
        const body = document.getElementById('scanFeedbackBody');
        const meta = document.getElementById('scanFeedbackMeta');

        box.style.display = 'block';

        if (data.success && !data.already_checked_in) {
            // SUKSES BARU
            box.style.background = 'var(--ok-bg)';
            box.style.borderColor = 'var(--ok-bd)';
            box.style.color = 'var(--ok)';
            icon.innerText = '✓';
            title.innerText = 'Presensi Berhasil Diverifikasi!';
            title.style.color = 'var(--ok)';
            body.innerText = `${data.voter.name} (${data.voter.attendance_token}) — Dipersilakan menuju Bilik Suara.`;
            body.style.color = '#1E3A2B';
            meta.innerText = `Waktu Presensi: ${data.checked_in_at}`;

            prependRecentCheckIn(data.voter);
            updateStats(1);
        } else if (data.success && data.already_checked_in) {
            // SUDAH HADIR SEBELUMNYA
            box.style.background = '#FEF9C3';
            box.style.borderColor = '#FDE047';
            box.style.color = '#854D0E';
            icon.innerText = '⚠️';
            title.innerText = 'Pemilih Sudah Presensi Sebelumnya';
            title.style.color = '#854D0E';
            body.innerText = data.message;
            body.style.color = '#713F12';
            meta.innerText = `Tercatat pada: ${data.checked_in_at}`;
        } else {
            // GAGAL
            box.style.background = 'var(--err-bg)';
            box.style.borderColor = 'var(--err-bd)';
            box.style.color = 'var(--err)';
            icon.innerText = '✕';
            title.innerText = 'Gagal Verifikasi Pemilih';
            title.style.color = 'var(--err)';
            body.innerText = data.message || 'Token / pemilih tidak ditemukan dalam DPT.';
            body.style.color = '#7F1D1D';
            meta.innerText = '';
        }
    }

    function prependRecentCheckIn(voter) {
        const emptyText = document.getElementById('emptyRecentText');
        if (emptyText) emptyText.remove();

        const list = document.getElementById('recentCheckinsList');
        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':' + String(now.getSeconds()).padStart(2, '0');

        const item = document.createElement('div');
        item.className = 'recent-checkin-item';
        item.style.cssText = 'display:flex; justify-content:space-between; align-items:center; background:#F0FDF4; border:1px solid #BBF7D0; border-radius:8px; padding:12px 14px; animation:slideDown 0.3s ease;';
        item.innerHTML = `
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:50%; background:var(--ok-bg); border:1px solid var(--ok-bd); color:var(--ok); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">✓</div>
                <div>
                    <strong style="font-size:13.5px; color:var(--text);">${voter.name}</strong>
                    <div style="font-size:11.5px; color:var(--text-sec); font-family:monospace;">${voter.attendance_token || voter.email}</div>
                </div>
            </div>
            <div style="text-align:right;">
                <span class="badge badge-success" style="font-size:11px;">Baru Saja</span>
                <div style="font-size:11px; color:var(--text-sec); margin-top:2px; font-family:monospace;">${timeStr}</div>
            </div>
        `;

        list.insertBefore(item, list.firstChild);
    }

    function updateStats(increment) {
        const checkedEl = document.getElementById('statCheckedIn');
        const notCheckedEl = document.getElementById('statNotCheckedIn');
        if (checkedEl && notCheckedEl) {
            let c = parseInt(checkedEl.innerText) + increment;
            let nc = Math.max(0, parseInt(notCheckedEl.innerText) - increment);
            checkedEl.innerText = c;
            notCheckedEl.innerText = nc;
        }
    }
</script>
@endpush
