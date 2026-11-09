@extends('layouts.admin')

@section('title', 'Edit Pengguna: ' . $user->name)

@section('content')
    <div style="max-width: 680px; margin: 0 auto;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" style="padding:6px 12px;">
                &larr; Kembali
            </a>
            <div>
                <p style="font-size:13px; color:var(--text-sec); margin-top:2px;">
                    Perbarui identitas, alamat email, peran (role), atau atur ulang password akun.
                </p>
            </div>
        </div>

        <div class="card">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <!-- Nama Lengkap -->
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap Pengguna *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
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
                        value="{{ old('email', $user->email) }}"
                        class="form-control"
                        required
                    >
                    @error('email')
                        <div style="color:var(--err); font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Role Selection -->
                @php
                    $currentRole = $user->roles->first()?->name;
                @endphp
                <div class="form-group">
                    <label for="role" class="form-label">Peran (Role &amp; Hak Akses) *</label>
                    <select id="role" name="role" class="form-control" style="background:#fff;" required>
                        <option value="">-- Pilih Peran Pengguna --</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->name }}" {{ old('role', $currentRole) === $r->name ? 'selected' : '' }}>
                                {{ \App\Enums\UserRole::getLabel($r->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div style="color:var(--err); font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Section Ubah Password (Opsional) -->
                <div style="background:#FAFBFC; border:1px solid var(--border); border-radius:10px; padding:18px; margin:20px 0 16px;">
                    <div style="font-size:13.5px; font-weight:600; color:var(--text); margin-bottom:4px;">
                        Ganti Password (Opsional)
                    </div>
                    <div style="font-size:12px; color:var(--text-sec); margin-bottom:14px;">
                        Biarkan kedua kolom password di bawah ini kosong jika Anda tidak ingin mengubah password akun ini.
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label for="password" class="form-label">Password Baru</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Kosongkan jika tetap"
                                class="form-control"
                            >
                            @error('password')
                                <div style="color:var(--err); font-size:12px; margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password baru"
                                class="form-control"
                            >
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:16px; padding-top:16px; border-top:1px solid var(--border);">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding:8px 24px;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

