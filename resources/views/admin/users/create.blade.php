@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')

@section('content')
    <div style="max-width: 680px; margin: 0 auto;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" style="padding:6px 12px;">
                &larr; Kembali
            </a>
            <div>
                <p style="font-size:13px; color:var(--text-sec); margin-top:2px;">
                    Buat akun login baru dan tentukan perannya (Administrator, Petugas TPS, Pemilih, dll).
                </p>
            </div>
        </div>

        <div class="card">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <!-- Nama Lengkap -->
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap Pengguna *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Ahmad Baihaqi, S.Kom."
                        class="form-control"
                        required
                    >
                    @error('name')
                        <div style="color:var(--err); font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Login -->
                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email (Digunakan untuk Login) *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Contoh: ahmad@instansi.sch.id"
                        class="form-control"
                        required
                    >
                    @error('email')
                        <div style="color:var(--err); font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Role Selection -->
                <div class="form-group">
                    <label for="role" class="form-label">Peran (Role &amp; Hak Akses) *</label>
                    <select id="role" name="role" class="form-control" style="background:#fff;" required>
                        <option value="">-- Pilih Peran Pengguna --</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->name }}" {{ old('role') === $r->name ? 'selected' : '' }}>
                                {{ \App\Enums\UserRole::getLabel($r->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div style="color:var(--err); font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                    <div style="font-size:12px; color:var(--text-sec); margin-top:6px;">
                        Pilih <strong>Petugas Presensi TPS</strong> untuk panitia yang bertugas di meja registrasi mencatat kehadiran pemilih.
                    </div>
                </div>

                <!-- Grid Password -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label for="password" class="form-label">Password *</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 6 karakter"
                            class="form-control"
                            required
                        >
                        @error('password')
                            <div style="color:var(--err); font-size:12px; margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password *</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password di atas"
                            class="form-control"
                            required
                        >
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:16px; padding-top:16px; border-top:1px solid var(--border);">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding:8px 24px;">
                        Simpan Akun Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

