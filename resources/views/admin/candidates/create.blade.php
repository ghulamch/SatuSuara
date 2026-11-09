@extends('layouts.admin')

@section('title', 'Tambah Pasangan Calon Baru')

@section('actions')
    <a href="{{ route('admin.candidates.index') }}" class="btn btn-secondary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        <span>Kembali ke Daftar</span>
    </a>
@endsection

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title">Formulir Pendaftaran Paslon</h2>
            <span style="font-size:12px; color:var(--text-sec);">Data wajib ditandai bintang (*)</span>
        </div>

        <form method="POST" action="{{ route('admin.candidates.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.candidates._form')
        </form>
    </div>
@endsection
