@extends('layouts.admin')

@section('title', 'Dashboard Ringkasan')

@section('actions')
    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
        <span>Lihat Live Hasil</span>
    </a>
@endsection

@section('content')
    <!-- Stat Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 28px;">
        <div class="card" style="margin-bottom:0; padding: 20px 22px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size: 12px; font-weight:600; color:var(--text-sec); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:6px;">Total Pemilih (DPT)</div>
                    <div style="font-size: 32px; font-weight:700; color:var(--text); line-height:1;">{{ $totalVoters }}</div>
                </div>
                <div style="width:40px; height:40px; border-radius:8px; background:var(--accent-bg); color:var(--accent); display:flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                    </svg>
                </div>
            </div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:12px;">Akun pemilih terdaftar</div>
        </div>

        <div class="card" style="margin-bottom:0; padding: 20px 22px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size: 12px; font-weight:600; color:var(--text-sec); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:6px;">Suara Masuk</div>
                    <div style="font-size: 32px; font-weight:700; color:var(--ok); line-height:1;">{{ $totalVotes }}</div>
                </div>
                <div style="width:40px; height:40px; border-radius:8px; background:var(--ok-bg); color:var(--ok); display:flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:12px;">
                Partisipasi: <strong>{{ $totalVoters > 0 ? round(($totalVotes / $totalVoters) * 100, 1) : 0 }}%</strong>
            </div>
        </div>

        <div class="card" style="margin-bottom:0; padding: 20px 22px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size: 12px; font-weight:600; color:var(--text-sec); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:6px;">Belum Memilih</div>
                    <div style="font-size: 32px; font-weight:700; color:var(--warn); line-height:1;">{{ $haventVoted }}</div>
                </div>
                <div style="width:40px; height:40px; border-radius:8px; background:var(--warn-bg); color:var(--warn); display:flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:12px;">Sisa hak suara aktif</div>
        </div>

        <div class="card" style="margin-bottom:0; padding: 20px 22px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size: 12px; font-weight:600; color:var(--text-sec); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:6px;">Paslon Terdaftar</div>
                    <div style="font-size: 32px; font-weight:700; color:var(--accent); line-height:1;">{{ $totalCandidates }}</div>
                </div>
                <div style="width:40px; height:40px; border-radius:8px; background:var(--accent-bg); color:var(--accent); display:flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <polyline points="16 11 18 13 22 9"/>
                    </svg>
                </div>
            </div>
            <div style="font-size:12px; color:var(--text-sec); margin-top:12px;">Kandidat pemilihan</div>
        </div>
    </div>

    <!-- Perolehan Suara Sementara -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Perolehan Suara Sementara</h2>
            <span class="badge badge-neutral">Realtime Snapshot</span>
        </div>

        @if ($results->isEmpty())
            <p style="color:var(--text-sec); font-size:13.5px; text-align:center; padding:20px;">Belum ada data kandidat pada sistem.</p>
        @else
            <div style="display:flex; flex-direction:column; gap:20px;">
                @foreach ($results as $candidate)
                    @php
                        $percentage = $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="background:var(--accent); color:#fff; font-size:12px; font-weight:700; padding:3px 9px; border-radius:12px;">
                                    #{{ $candidate->nomor_urut }}
                                </span>
                                <span style="font-weight:600; font-size:14.5px; color:var(--text);">
                                    {{ $candidate->nama_ketua }}
                                    @if ($candidate->nama_wakil)
                                        <span style="font-weight:400; color:var(--text-sec); font-size:13px;">&amp; {{ $candidate->nama_wakil }}</span>
                                    @endif
                                </span>
                            </div>
                            <div style="text-align:right;">
                                <span style="font-weight:700; font-size:15px; color:var(--text);">{{ $candidate->votes_count }} suara</span>
                                <span style="font-size:13px; color:var(--text-sec); margin-left:6px;">({{ $percentage }}%)</span>
                            </div>
                        </div>

                        <!-- Progress Bar Container -->
                        <div style="width:100%; height:12px; background:var(--bg); border-radius:6px; overflow:hidden; border:1px solid var(--border);">
                            <div style="width: {{ $percentage }}%; height:100%; background:var(--accent); border-radius:6px; transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1);"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Aktivitas Suara Terakhir -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Pemilih Yang Telah Memberikan Suara</h2>
            <span class="badge badge-success">{{ $hasVoted->count() }} Terverifikasi</span>
        </div>

        @if ($hasVoted->isEmpty())
            <p style="color:var(--text-sec); font-size:13.5px; text-align:center; padding:20px;">Belum ada pemilih yang memberikan suara.</p>
        @else
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Pemilih</th>
                            <th>Email Akun</th>
                            <th>Status Hak Pilih</th>
                            <th>Pilihan Audit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hasVoted->take(8) as $voter)
                            <tr>
                                <td style="font-weight:600;">{{ $voter->name }}</td>
                                <td style="color:var(--text-sec);">{{ $voter->email }}</td>
                                <td>
                                    <span class="badge badge-success">Sudah Memilih</span>
                                </td>
                                <td>
                                    @if ($voter->vote && $voter->vote->candidate)
                                        <span class="badge badge-neutral">
                                            Paslon #{{ $voter->vote->candidate->nomor_urut }} ({{ $voter->vote->candidate->nama_ketua }})
                                        </span>
                                    @else
                                        <span style="color:var(--text-sec); font-size:12px;">Terekam</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
