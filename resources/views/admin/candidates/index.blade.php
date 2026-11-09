@extends('layouts.admin')

@section('title', 'Kelola Pasangan Calon')

@section('actions')
    <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>Tambah Paslon Baru</span>
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Daftar Pasangan Calon (Kandidat)</h2>
            <span class="badge badge-neutral">{{ $candidates->total() }} Terdaftar</span>
        </div>

        @if ($candidates->isEmpty())
            <x-empty-state
                title="Belum Ada Pasangan Calon"
                description="Silakan daftarkan pasangan calon ketua & wakil untuk pemilihan."
            >
                <x-slot:icon>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                    </svg>
                </x-slot:icon>
                <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary btn-sm">
                    + Tambah Paslon Pertama
                </a>
            </x-empty-state>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">No. Urut</th>
                            <th style="width: 70px;">Foto</th>
                            <th>Nama Pasangan Calon</th>
                            <th>Visi Utama</th>
                            <th>Status</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $c)
                            <tr>
                                <td>
                                    <span style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:var(--accent); color:#fff; font-weight:700; font-size:14px;">
                                        #{{ $c->nomor_urut }}
                                    </span>
                                </td>
                                <td>
                                    @if ($c->foto_ketua)
                                        <img src="{{ $c->foto_ketua_url }}" alt="{{ $c->nama_ketua }}" style="width:42px; height:42px; border-radius:8px; object-fit:cover; border:1px solid var(--border);">
                                    @else
                                        <div style="width:42px; height:42px; border-radius:8px; background:var(--accent-bg); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600;">
                                            Paslon
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight:600; font-size:14.5px; color:var(--text);">{{ $c->nama_ketua }}</div>
                                    <div style="font-size:12.5px; color:var(--text-sec);">
                                        {{ $c->nama_wakil ? 'Wakil: ' . $c->nama_wakil : 'Calon Tunggal' }}
                                    </div>
                                </td>
                                <td style="max-width:260px;">
                                    <div style="font-size:12.5px; color:var(--text-sec); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                        {{ $c->visi ?: '-' }}
                                    </div>
                                </td>
                                <td>
                                    @if ($c->aktif)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-neutral">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <a href="{{ route('admin.candidates.edit', $c) }}" class="btn btn-secondary btn-sm" title="Ubah Paslon">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            <span>Edit</span>
                                        </a>

                                        <form method="POST" action="{{ route('admin.candidates.destroy', $c) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paslon ini? Semua suara yang telah masuk untuk paslon ini juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Paslon">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

            <div style="margin-top: 20px;">
                {{ $candidates->links() }}
            </div>
        @endif
    </div>
@endsection

