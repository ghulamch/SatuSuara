@extends('layouts.admin')

@section('title', 'Ubah Pasangan Calon')

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
            <h2 class="card-title">Edit Paslon #{{ $candidate->nomor_urut }} ({{ $candidate->nama_ketua }})</h2>
        </div>

        <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.candidates._form', ['candidate' => $candidate])
        </form>
    </div>
@endsection
