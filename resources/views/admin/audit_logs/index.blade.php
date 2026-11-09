@extends('layouts.admin')

@section('title', 'Audit Trail & Log Aktivitas')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <p style="font-size:13px; color:var(--text-sec); margin-top:2px;">
                Catatan mutlak dan tidak dapat diubah atas seluruh aksi perubahan sistem, jadwal, paslon, DPT, dan perolehan suara.
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="badge badge-neutral" style="font-size:12px; padding:6px 12px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Riwayat Aktivitas Resmi
            </span>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card" style="margin-bottom:20px; padding:16px 20px;">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
            <div style="display:flex; gap:10px; flex-wrap:wrap; flex:1; max-width:600px;">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari deskripsi aksi, nama admin, atau IP..."
                    class="form-control"
                    style="flex:1; min-width:220px;"
                >
                <select name="category" class="form-control" style="width:auto; min-width:160px; background:#fff;">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span>Filter</span>
                </button>
                @if (request()->hasAny(['search', 'category']))
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABEL LOG -->
    <div class="card" style="padding:0; overflow:hidden;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:180px;">Waktu &amp; Tanggal</th>
                        <th style="width:130px;">Kategori</th>
                        <th style="width:160px;">Pelaksana (Admin)</th>
                        <th>Deskripsi Aktivitas</th>
                        <th style="width:140px;">Alamat IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        @php
                            $ip = $log->properties['ip'] ?? '-';
                            $agent = $log->properties['user_agent'] ?? '-';
                            $badgeColor = match($log->log_name) {
                                'paslon'      => 'badge-primary',
                                'dpt'         => 'badge-neutral',
                                'pengaturan'  => 'badge-warning',
                                'reset'       => 'badge-danger',
                                'presensi'    => 'badge-success',
                                'laporan'     => 'badge-neutral',
                                'user'        => 'badge-primary',
                                'role'        => 'badge-warning',
                                default       => 'badge-neutral'
                            };
                        @endphp
                        <tr>
                            <td style="font-size:12px; color:var(--text-sec); white-space:nowrap;">
                                <strong style="color:var(--text); font-family:monospace; font-size:12.5px;">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}
                                </strong>
                                <div>{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $badgeColor }}" style="text-transform:uppercase; font-size:11px;">
                                    {{ $log->log_name ?? 'Umum' }}
                                </span>
                            </td>
                            <td>
                                @if ($log->causer)
                                    <strong style="font-size:13px; color:var(--text);">{{ $log->causer->name }}</strong>
                                    <div style="font-size:11px; color:var(--text-sec);">{{ $log->causer->email }}</div>
                                @else
                                    <span style="font-size:12px; color:var(--text-sec); font-style:italic;">Sistem / Otomatis</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:13.5px; font-weight:600; color:var(--text);">
                                    {{ $log->description }}
                                </div>
                                @if (!empty($log->properties) && count($log->properties) > 3)
                                    <div style="font-size:11px; color:var(--text-sec); margin-top:2px; font-family:monospace;">
                                        URL: {{ Str::limit($log->properties['url'] ?? '', 45) }}
                                    </div>
                                @endif
                            </td>
                            <td style="font-size:12px; font-family:monospace; color:var(--text-sec); white-space:nowrap;">
                                <span title="{{ $agent }}">{{ $ip }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:36px; color:var(--text-sec);">
                                Belum ada riwayat aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div style="padding:16px 20px; border-top:1px solid var(--border);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection

