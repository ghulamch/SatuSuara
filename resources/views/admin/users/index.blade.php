@extends('layouts.admin')

@section('title', 'Kelola User & Peran (Role)')

@section('actions')
    <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>+ Tambah Pengguna Baru</span>
        </a>
    </div>
@endsection

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <p style="font-size:13px; color:var(--text-sec); margin-top:2px;">
                Kelola akun administrator, petugas meja presensi TPS, panitia, serta konfigurasi hak akses peran sistem. (Akun pemilih dikelola terpisah di menu Data Pemilih).
            </p>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:18px 20px; margin-bottom:0;">
            <div style="font-size:12px; font-weight:600; color:var(--text-sec); text-transform:uppercase; letter-spacing:0.04em;">Pengguna Staf &amp; Panitia</div>
            <div style="font-size:32px; font-weight:800; color:var(--text); line-height:1.2; margin-top:4px;">{{ $totalStaff }}</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">Akun operasional non-voters</div>
        </div>

        <div class="card" style="padding:18px 20px; margin-bottom:0; border-color:#C7D3F0;">
            <div style="font-size:12px; font-weight:600; color:var(--accent); text-transform:uppercase; letter-spacing:0.04em;">Administrator</div>
            <div style="font-size:32px; font-weight:800; color:var(--accent); line-height:1.2; margin-top:4px;">{{ $adminCount }}</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">Hak kendali penuh sistem</div>
        </div>

        <div class="card" style="padding:18px 20px; margin-bottom:0; border-color:var(--ok-bd);">
            <div style="font-size:12px; font-weight:600; color:var(--ok); text-transform:uppercase; letter-spacing:0.04em;">Petugas Presensi TPS</div>
            <div style="font-size:32px; font-weight:800; color:var(--ok); line-height:1.2; margin-top:4px;">{{ $petugasCount }}</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">Operator meja registrasi &amp; presensi</div>
        </div>

        <div class="card" style="padding:18px 20px; margin-bottom:0;">
            <div style="font-size:12px; font-weight:600; color:var(--text-sec); text-transform:uppercase; letter-spacing:0.04em;">Pemilih (DPT)</div>
            <div style="font-size:32px; font-weight:800; color:var(--text); line-height:1.2; margin-top:4px;">{{ $votersCount }}</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                <a href="{{ route('admin.voters.index') }}" style="color:var(--accent); text-decoration:none; font-weight:600;">Lihat di menu DPT &rarr;</a>
            </div>
        </div>

        <div class="card" style="padding:18px 20px; margin-bottom:0; border-color:var(--warn-bd);">
            <div style="font-size:12px; font-weight:600; color:var(--warn); text-transform:uppercase; letter-spacing:0.04em;">Peran Dinamis</div>
            <div style="font-size:32px; font-weight:800; color:var(--warn); line-height:1.2; margin-top:4px;">{{ $allRoles->count() }}</div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">Tipe peran di database</div>
        </div>
    </div>

    <!-- TABS NAVIGATION -->
    @php
        $activeTab = request('tab', 'users');
    @endphp
    <div style="display:flex; gap:8px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:12px;">
        <a href="{{ route('admin.users.index', ['tab' => 'users']) }}" class="btn {{ $activeTab === 'users' ? 'btn-primary' : 'btn-secondary' }}" style="padding:8px 18px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <span>Daftar Pengguna Sistem</span>
        </a>

        <a href="{{ route('admin.users.index', ['tab' => 'roles']) }}" class="btn {{ $activeTab === 'roles' ? 'btn-primary' : 'btn-secondary' }}" style="padding:8px 18px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <polyline points="9 12 11 14 15 10"/>
            </svg>
            <span>Kelola Peran Dinamis (Roles)</span>
        </a>
    </div>

    @if ($activeTab === 'users')
        <!-- TAB 1: USERS LIST -->
        <div class="card">
            <div class="card-header" style="flex-wrap:wrap; gap:12px;">
                <div>
                    <h2 class="card-title">Daftar Akun Pengguna (Staf &amp; Panitia)</h2>
                    <div style="font-size:12.5px; color:var(--text-sec); margin-top:2px;">
                        Menampilkan seluruh akun staf operasional, admin, dan petugas TPS. (Akun pemilih DPT dipisahkan di menu Data Pemilih).
                    </div>
                </div>

                <!-- Search & Role Filter -->
                <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <input type="hidden" name="tab" value="users">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama atau email staf..."
                        class="form-control"
                        style="width:220px;"
                    >
                    <select name="role" class="form-control" style="width:auto; background:#fff;">
                        <option value="">Semua Peran Staf</option>
                        @foreach ($staffRoles as $r)
                            <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if (request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th>Pengguna</th>
                            <th>Email Login</th>
                            <th>Peran (Role)</th>
                            <th>Tanggal Terdaftar</th>
                            <th style="text-align:right; width:150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $u)
                            @php
                                $primaryRole = $u->roles->first()?->name ?? 'none';
                                $badgeClass = match($primaryRole) {
                                    'admin'            => 'badge-primary',
                                    'petugas_presensi' => 'badge-success',
                                    'voters'           => 'badge-neutral',
                                    default            => 'badge-warning'
                                };
                                $roleLabel = match($primaryRole) {
                                    'admin'            => 'Administrator',
                                    'petugas_presensi' => 'Petugas Presensi TPS',
                                    'voters'           => 'Pemilih (Voter)',
                                    default            => ucfirst(str_replace('_', ' ', $primaryRole))
                                };
                            @endphp
                            <tr>
                                <td style="color:var(--text-sec); font-size:12.5px;">{{ $users->firstItem() + $index }}</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:34px; height:34px; border-radius:50%; background:#EEF1F9; border:1px solid #C7D3F0; color:var(--accent); font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:center;">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:var(--text); font-size:13.5px;">
                                                {{ $u->name }}
                                                @if (auth()->id() === $u->id)
                                                    <span class="badge badge-neutral" style="font-size:10px; margin-left:4px;">Anda</span>
                                                @endif
                                            </div>
                                            @if ($u->attendance_token)
                                                <div style="font-size:11px; color:var(--text-sec); font-family:monospace;">
                                                    Token: {{ $u->attendance_token }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="font-family:monospace; font-size:12.5px; color:var(--text);">
                                    {{ $u->email }}
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }}" style="font-size:11px;">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td style="font-size:12px; color:var(--text-sec); font-family:monospace;">
                                    {{ $u->created_at ? $u->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:6px; align-items:center;">
                                        <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-secondary btn-sm" title="Edit Akun Pengguna">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            <span>Edit</span>
                                        </a>

                                        @if (auth()->id() !== $u->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Pengguna">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-secondary btn-sm" disabled style="opacity:0.4; cursor:not-allowed;" title="Tidak dapat menghapus akun sendiri">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding:36px; color:var(--text-sec);">
                                    Tidak ada data pengguna yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div style="padding:16px 20px; border-top:1px solid var(--border);">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    @else
        <!-- TAB 2: DYNAMIC ROLES MANAGEMENT -->
        <div style="display:grid; grid-template-columns: 1fr 340px; gap:24px; align-items:start;">
            <!-- ROLE LIST TABLE -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Daftar Peran Dinamis (Role)</h2>
                        <div style="font-size:12.5px; color:var(--text-sec); margin-top:2px;">
                            Daftar peran yang terdaftar di basis data dan digunakan untuk membatasi hak akses sistem.
                        </div>
                    </div>
                    <span class="badge badge-neutral">{{ $allRoles->count() }} Peran Aktif</span>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Peran (Slug)</th>
                                <th>Tipe Peran</th>
                                <th>Hak Akses (RBAC)</th>
                                <th>Pengguna Terkait</th>
                                <th style="text-align:right; width:180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allRoles as $role)
                                @php
                                    $isCore = in_array(strtolower($role->name), ['admin', 'voters', 'petugas_presensi'], true);
                                    $roleBadge = match($role->name) {
                                        'admin'            => 'badge-primary',
                                        'petugas_presensi' => 'badge-success',
                                        'voters'           => 'badge-neutral',
                                        default            => 'badge-warning'
                                    };
                                    $roleTitle = match($role->name) {
                                        'admin'            => 'Administrator Sistem (Akses Penuh)',
                                        'petugas_presensi' => 'Petugas Presensi TPS (Registrasi Meja TPS)',
                                        'voters'           => 'Pemilih Terdaftar (Bilik Suara)',
                                        default            => 'Peran Kustom Dinamis'
                                    };
                                    $permCount = $role->permissions->count();
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <span class="badge {{ $roleBadge }}" style="font-family:monospace; font-size:12px;">
                                                {{ $role->name }}
                                            </span>
                                        </div>
                                        <div style="font-size:11.5px; color:var(--text-sec); margin-top:3px;">
                                            {{ $roleTitle }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($isCore)
                                            <span class="badge badge-neutral" style="font-size:10.5px;">Sistem Inti</span>
                                        @else
                                            <span class="badge badge-warning" style="font-size:10.5px;">Kustom Dinamis</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($role->name === 'admin')
                                            <span class="badge badge-primary" style="font-size:11px;">
                                                Akses Penuh (Superadmin)
                                            </span>
                                        @elseif ($role->name === 'voters')
                                            <span class="badge badge-neutral" style="font-size:11px;">
                                                Bilik Suara Sah
                                            </span>
                                        @else
                                            <span class="badge {{ $permCount > 0 ? 'badge-success' : 'badge-neutral' }}" style="font-size:11px;">
                                                {{ $permCount }} Izin Modul Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" style="color:var(--accent); font-weight:600; text-decoration:none; font-size:13px;">
                                            {{ $role->users_count }} Pengguna &rarr;
                                        </a>
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:inline-flex; gap:6px; align-items:center;">
                                            @if ($role->name !== 'voters')
                                                <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-secondary btn-sm" title="Konfigurasi Hak Akses Modul (RBAC)">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                                    </svg>
                                                    <span>Hak Akses</span>
                                                </a>
                                            @endif

                                            @if ($isCore)
                                                <span style="font-size:11px; color:var(--text-sec); font-style:italic; padding:0 4px;">Dilindungi</span>
                                            @elseif ($role->users_count > 0)
                                                <button type="button" class="btn btn-secondary btn-sm" disabled style="opacity:0.4; cursor:not-allowed;" title="Tidak dapat dihapus karena masih digunakan">
                                                    Hapus
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran dinamis [{{ $role->name }}]?');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Peran">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FORM TAMBAH ROLE DINAMIS BARU -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Buat Peran Dinamis Baru</h2>
                        <div style="font-size:12px; color:var(--text-sec); margin-top:2px;">
                            Tambahkan nama peran baru untuk grup pengguna kustom.
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="roleNameInput" class="form-label">Nama Peran Baru *</label>
                        <input
                            type="text"
                            id="roleNameInput"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: saksi_tps atau pengawas"
                            class="form-control"
                            required
                        >
                        <div style="font-size:11.5px; color:var(--text-sec); margin-top:5px; line-height:1.4;">
                            Spasi akan otomatis diubah menjadi garis bawah (underscore) huruf kecil.
                        </div>
                    </div>

                    <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:8px; padding:12px 14px; margin-bottom:18px; font-size:12px; color:var(--text-sec); line-height:1.5;">
                        <strong style="color:var(--text);">Catatan Hak Akses:</strong><br>
                        Peran baru akan langsung tersedia pada formulir pembuatan akun panitia. Kewenangan akses menu dapat diatur pada tab Hak Akses.
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                        + Simpan Peran Dinamis
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection

