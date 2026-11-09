@extends('layouts.admin')

@section('title', 'Tambah Akun Pemilih Baru')

@section('actions')
    <a href="{{ route('admin.voters.index') }}" class="btn btn-secondary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        <span>Kembali ke Daftar</span>
    </a>
@endsection

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title">Registrasi Akun Pemilih (DPT)</h2>
            <span style="font-size:12px; color:var(--text-sec);">Peran Otomatis: Voters</span>
        </div>

        <form method="POST" action="{{ route('admin.voters.store') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap Pemilih *</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    placeholder="Contoh: Ahmad Fauzan"
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email / ID Login *</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    required
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    placeholder="nama@instansi.ac.id"
                >
                <div class="form-text">Digunakan oleh pemilih saat masuk ke bilik suara e-voting.</div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi Default *</label>
                <input
                    type="text"
                    name="password"
                    id="password"
                    value="{{ old('password', 'password123') }}"
                    required
                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Minimal 6 karakter"
                >
                <div class="form-text">Berikan kata sandi ini kepada pemilih untuk login pertama kali.</div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:28px; padding-top:18px; border-top:1px solid var(--border);">
                <a href="{{ route('admin.voters.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Simpan &amp; Terbitkan Akun</span>
                </button>
            </div>
        </form>
    </div>
@endsection

