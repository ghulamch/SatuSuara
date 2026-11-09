@extends('layouts.admin')

@section('title', 'Data Pemilih (DPT)')

@section('actions')
    <a href="{{ route('admin.voters.template') }}" class="btn btn-secondary btn-sm" title="Unduh format template Excel/CSV">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        <span>Unduh Template</span>
    </a>

    <button type="button" class="btn btn-secondary btn-sm" onclick="openImportModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        <span>Import Excel / CSV</span>
    </button>

    <a href="{{ route('admin.voters.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>Tambah Pemilih</span>
    </a>
@endsection

@section('content')
    <!-- Import Warnings Alert -->
    @if (session('import_warnings') && count(session('import_warnings')) > 0)
        <div class="alert alert--err" style="background:var(--warn-bg); border-color:var(--warn-bd); color:var(--warn);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <div style="flex:1;">
                <div style="font-weight:600; margin-bottom:4px;">Catatan Import Berkas:</div>
                <ul style="padding-left:18px; font-size:12.5px; line-height:1.5;">
                    @foreach (array_slice(session('import_warnings'), 0, 5) as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                    @if (count(session('import_warnings')) > 5)
                        <li>...dan {{ count(session('import_warnings')) - 5 }} catatan lainnya.</li>
                    @endif
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Daftar Akun Pemilih Terdaftar (DPT)</h2>
                <div style="font-size:12.5px; color:var(--text-sec); margin-top:2px;">
                    Total: <strong>{{ $voters->count() }} pemilih</strong> &nbsp;•&nbsp;
                    Sudah Memilih: <strong style="color:var(--ok);">{{ $voters->whereNotNull('vote')->count() }}</strong> &nbsp;•&nbsp;
                    Belum Memilih: <strong style="color:var(--warn);">{{ $voters->whereNull('vote')->count() }}</strong>
                </div>
            </div>
            <span class="badge badge-neutral">DPT Resmi</span>
        </div>

        @if ($voters->isEmpty())
            <x-empty-state
                title="Belum Ada Pemilih Terdaftar"
                description="Tambahkan akun pemilih secara manual atau gunakan fitur import Excel."
            >
                <x-slot:icon>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <polyline points="16 11 18 13 22 9"/>
                    </svg>
                </x-slot:icon>
                <button type="button" class="btn btn-secondary btn-sm" onclick="openImportModal()">
                    Import dari Excel
                </button>
                <a href="{{ route('admin.voters.create') }}" class="btn btn-primary btn-sm">
                    + Tambah Manual
                </a>
            </x-empty-state>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pemilih</th>
                            <th>Token Presensi</th>
                            <th>Email Login</th>
                            <th>Status TPS</th>
                            <th>Status Hak Pilih</th>
                            <th>Sesi Perangkat</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($voters as $index => $voter)
                            <tr>
                                <td style="color:var(--text-sec); width:50px;">{{ $index + 1 }}</td>
                                <td style="font-weight:600; color:var(--text);">{{ $voter->name }}</td>
                                <td>
                                    <span class="badge badge-neutral" style="font-family:monospace; font-size:12px; font-weight:700; color:var(--accent); background:#EEF1F9; padding:4px 9px; border-radius:6px; border:1px solid #C7D3F0; white-space:nowrap; letter-spacing:0.02em;">
                                        {{ $voter->attendance_token ?? '-' }}
                                    </span>
                                </td>
                                <td style="color:var(--text-sec); font-family:monospace; font-size:13px;">{{ $voter->email }}</td>
                                <td>
                                    @if ($voter->isCheckedIn())
                                        <span class="badge badge-success" title="Waktu: {{ $voter->checked_in_at }}">
                                            ✓ Hadir
                                        </span>
                                    @else
                                        <span class="badge badge-neutral">Belum Hadir</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($voter->vote)
                                        <span class="badge badge-success">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                             Sudah Memilih
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/>
                                            </svg>
                                             Belum Memilih
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($voter->hasActiveSession())
                                        <span class="badge badge-success" style="background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; font-weight:600;" title="IP: {{ $voter->session_ip ?? 'N/A' }} | Terakhir: {{ $voter->session_last_active_at ? $voter->session_last_active_at->diffForHumans() : 'baru saja' }}">
                                            <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#10B981; margin-right:4px;"></span>
                                            Terkunci di Bilik
                                        </span>
                                        @if ($voter->session_ip)
                                            <div style="font-size:11px; color:var(--text-sec); margin-top:2px; font-family:monospace;">{{ $voter->session_ip }}</div>
                                        @endif
                                    @else
                                        <span class="badge badge-neutral" style="color:var(--text-sec); background:#F3F4F6;">
                                            Tidak Ada Sesi
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:6px;">
                                        @if ($voter->current_session_id || $voter->hasActiveSession())
                                            <form method="POST" action="{{ route('admin.voters.reset-session', $voter) }}" onsubmit="return confirm('Buka kunci / reset sesi perangkat pemilih {{ $voter->name }}? Sesi di perangkat sebelumnya akan dihentikan dan pemilih dapat login kembali.');">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-sm" title="Reset sesi perangkat pemilih (Buka Kunci)" style="color:var(--accent); border-color:#C7D3F0; background:#EEF1F9;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                        <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                                                    </svg>
                                                    <span>Reset Sesi</span>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($voter->vote)
                                            <form method="POST" action="{{ route('admin.voters.reset-vote', $voter) }}" onsubmit="return confirm('Reset suara pemilih ini? Hak pilih pemilih ini akan dikembalikan dan suaranya akan dihapus.');">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-sm" title="Reset status suara pemilih ini" style="color:var(--warn);">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                                                    </svg>
                                                    <span>Reset Suara</span>
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.voters.destroy', $voter) }}" onsubmit="return confirm('Hapus akun pemilih ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Akun Pemilih">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- MODAL IMPORT EXCEL -->
    <div id="importModal" class="modal-overlay" style="position:fixed; inset:0; background:rgba(28,31,46,0.6); backdrop-filter:blur(4px); z-index:100; display:none; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#FFFFFF; border-radius:14px; max-width:480px; width:100%; padding:28px; box-shadow:0 10px 30px rgba(0,0,0,0.2);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
                <div>
                    <h3 style="font-size:18px; font-weight:600; color:var(--text);">Import Data Pemilih</h3>
                    <p style="font-size:12.5px; color:var(--text-sec); margin-top:2px;">Unggah berkas Excel (.xlsx, .xls) atau CSV.</p>
                </div>
                <button type="button" onclick="closeImportModal()" style="background:none; border:none; color:var(--text-sec); cursor:pointer; font-size:20px; line-height:1;">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.voters.import') }}" enctype="multipart/form-data">
                @csrf

                <div style="background:#FAFBFC; border:1px dashed var(--border-hi); border-radius:10px; padding:20px; text-align:center; margin-bottom:18px;">
                    <div style="width:42px; height:42px; border-radius:50%; background:var(--accent-bg); color:var(--accent); display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <label for="excelFile" class="btn btn-secondary btn-sm" style="cursor:pointer; display:inline-flex; align-items:center; gap:6px; font-weight:600; margin-bottom:4px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Pilih Berkas Spreadsheet</span>
                    </label>
                    <div id="excelFileName" style="font-size:12px; color:var(--text-sec); margin-top:4px;">Belum ada berkas dipilih</div>
                    <input
                        type="file"
                        id="excelFile"
                        name="file"
                        accept=".xlsx,.xls,.csv"
                        required
                        style="display:none;"
                        onchange="if(this.files[0]) { const el = document.getElementById('excelFileName'); el.textContent = this.files[0].name; el.style.color = 'var(--text)'; el.style.fontWeight = '600'; }"
                    >
                    <div style="font-size:11px; color:var(--text-ph); margin-top:6px;">Ukuran berkas maksimal 5MB</div>
                </div>

                <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:8px; padding:12px; font-size:12px; color:#166534; margin-bottom:20px; line-height:1.5;">
                    <div style="font-weight:600; margin-bottom:2px;">Format Kolom yang Didukung:</div>
                    <div>1. <code>nama</code> (Nama lengkap pemilih)</div>
                    <div>2. <code>email</code> (Email login unik)</div>
                    <div>3. <code>password</code> (Opsional, bawaan: <em>password123</em>)</div>
                    <div style="margin-top:8px;">
                        <a href="{{ route('admin.voters.template') }}" style="color:var(--accent); font-weight:600; text-decoration:underline;">
                            Unduh Contoh Template Excel
                        </a>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn btn-secondary" onclick="closeImportModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Mulai Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importModal').style.display = 'flex';
    }

    function closeImportModal() {
        document.getElementById('importModal').style.display = 'none';
    }

    // Close when clicking overlay
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImportModal();
        }
    });
</script>
@endpush
