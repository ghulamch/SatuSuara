@extends('layouts.admin')

@section('title', 'Laporan & Berita Acara Pemilihan')

@section('actions')
    <a href="{{ route('admin.reports.pdf') }}" class="btn btn-primary btn-no-print" style="text-decoration:none;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        <span>Unduh PDF Resmi</span>
    </a>
    <button onclick="window.print()" class="btn btn-secondary btn-no-print">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 6 2 18 2 18 9"/>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
        </svg>
        <span>Cetak Langsung</span>
    </button>
@endsection

@push('styles')
<style>
    .live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--ok-bg);
        border: 1px solid var(--ok-bd);
        color: var(--ok);
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .pulsing-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--ok);
        animation: pulse 1.6s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(22, 101, 52, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(22, 101, 52, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(22, 101, 52, 0); }
    }

    /* Fullscreen Projector Mode (High-Visibility) */
    .live-section:-webkit-full-screen,
    .live-section:fullscreen {
        background: #EDEEF1 !important;
        color: #111827 !important;
        padding: 32px 48px !important;
        overflow-y: auto !important;
        border: none !important;
        border-radius: 0 !important;
        width: 100vw !important;
        min-height: 100vh !important;
        box-sizing: border-box !important;
        font-family: 'Jost', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    }

    /* Fullscreen Card Header */
    .live-section:fullscreen .card-header,
    .live-section:-webkit-full-screen .card-header {
        background: #FFFFFF !important;
        border: 1px solid #D8DCE5 !important;
        border-radius: 14px !important;
        padding: 18px 26px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
    }

    /* Fullscreen Title & Subtitle */
    .live-section:fullscreen .card-title,
    .live-section:-webkit-full-screen .card-title {
        color: #111827 !important;
        font-size: 28px !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
    }

    .live-section:fullscreen .fs-light-text,
    .live-section:-webkit-full-screen .fs-light-text {
        color: #475569 !important;
        font-size: 14px !important;
        font-weight: 500 !important;
    }

    /* Fullscreen Stats Banner */
    .live-section:fullscreen .fs-stat-banner,
    .live-section:-webkit-full-screen .fs-stat-banner {
        background: #FFFFFF !important;
        border: 1px solid #D8DCE5 !important;
        border-radius: 14px !important;
        padding: 22px 30px !important;
        margin-bottom: 26px !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
    }

    .live-section:fullscreen #totalVotesDisplay,
    .live-section:-webkit-full-screen #totalVotesDisplay {
        color: #3D5A99 !important;
        font-size: 58px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
    }

    .live-section:fullscreen .fs-stat-label,
    .live-section:-webkit-full-screen .fs-stat-label {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.03em !important;
    }

    .live-section:fullscreen .fs-stat-desc,
    .live-section:-webkit-full-screen .fs-stat-desc {
        font-size: 14px !important;
        color: #64748B !important;
    }

    /* Fullscreen Candidate Card Items */
    .live-section:fullscreen .candidate-card-item,
    .live-section:-webkit-full-screen .candidate-card-item {
        background: #FFFFFF !important;
        border: 1.5px solid #D8DCE5 !important;
        padding: 24px 28px !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
        margin-bottom: 18px !important;
    }

    /* Big Candidate Number Badges & Photos */
    .live-section:fullscreen .candidate-num-badge,
    .live-section:-webkit-full-screen .candidate-num-badge {
        width: 58px !important;
        height: 58px !important;
        font-size: 24px !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12) !important;
    }

    .live-section:fullscreen .candidate-photo-thumb,
    .live-section:-webkit-full-screen .candidate-photo-thumb {
        width: 58px !important;
        height: 58px !important;
        border-radius: 12px !important;
        object-fit: cover !important;
        border: 2px solid #E2E8F0 !important;
    }

    /* Big Candidate Names */
    .live-section:fullscreen .candidate-name-text,
    .live-section:-webkit-full-screen .candidate-name-text {
        color: #0F172A !important;
        font-size: 24px !important;
        font-weight: 700 !important;
        line-height: 1.25 !important;
    }

    .live-section:fullscreen .candidate-sub-text,
    .live-section:-webkit-full-screen .candidate-sub-text {
        color: #475569 !important;
        font-size: 16px !important;
        font-weight: 500 !important;
        margin-top: 3px !important;
    }

    /* Massive Perolehan Suara Numbers */
    .live-section:fullscreen .candidate-vote-num,
    .live-section:-webkit-full-screen .candidate-vote-num {
        font-size: 38px !important;
        font-weight: 800 !important;
        color: #0F172A !important;
    }

    .live-section:fullscreen .candidate-vote-label,
    .live-section:-webkit-full-screen .candidate-vote-label {
        font-size: 16px !important;
        font-weight: 600 !important;
        color: #64748B !important;
        margin-right: 4px !important;
    }

    .live-section:fullscreen .candidate-vote-pct,
    .live-section:-webkit-full-screen .candidate-vote-pct {
        font-size: 34px !important;
        font-weight: 800 !important;
        margin-left: 6px !important;
    }

    /* Thick High-Contrast Progress Bar */
    .live-section:fullscreen .progress-track,
    .live-section:-webkit-full-screen .progress-track {
        background: #F1F5F9 !important;
        border: 1px solid #CBD5E1 !important;
        height: 24px !important;
        border-radius: 12px !important;
        margin-top: 16px !important;
    }

    /* Fullscreen Chart Card */
    .live-section:fullscreen .fs-chart-card,
    .live-section:-webkit-full-screen .fs-chart-card {
        background: #FFFFFF !important;
        border: 1px solid #D8DCE5 !important;
        border-radius: 16px !important;
        padding: 32px 24px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
        height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
    }

    .live-section:fullscreen .fs-chart-title,
    .live-section:-webkit-full-screen .fs-chart-title {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        letter-spacing: 0.04em !important;
        margin-bottom: 20px !important;
    }

    .live-section:fullscreen .fs-chart-container,
    .live-section:-webkit-full-screen .fs-chart-container {
        width: 100% !important;
        max-width: 440px !important;
        height: 400px !important;
    }

    .live-section:fullscreen .btn-fs-exit,
    .live-section:-webkit-full-screen .btn-fs-exit {
        display: inline-flex !important;
    }

    .btn-fs-exit {
        display: none;
    }

    /* Berita Acara Formal Sheet */
    .berita-acara-sheet {
        background: #FFFFFF;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 40px;
        margin-top: 32px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    }

    .ba-header {
        text-align: center;
        border-bottom: 2px solid #1C1F2E;
        padding-bottom: 16px;
        margin-bottom: 24px;
    }

    .ba-kop-instansi {
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text);
    }

    .ba-title {
        font-size: 18px;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 6px;
        color: var(--text);
        letter-spacing: 0.02em;
    }

    .ba-subtitle {
        font-size: 13px;
        color: var(--text-sec);
        margin-top: 2px;
    }

    .ba-body {
        font-size: 13.5px;
        line-height: 1.65;
        color: var(--text);
    }

    .ba-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 13px;
    }

    .ba-table th, .ba-table td {
        border: 1px solid #C2C6D2;
        padding: 10px 14px;
        text-align: left;
    }

    .ba-table th {
        background: #F4F6F9;
        font-weight: 600;
    }

    .ba-signature-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 48px;
        text-align: center;
    }

    .signature-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 120px;
    }

    .signature-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
    }

    .signature-space {
        height: 60px;
    }

    .signature-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
    }
        color: var(--text-sec);
    }

    @media print {
        @page {
            size: A4;
            margin: 1.5cm;
        }
        .live-section { display: none !important; }
        .berita-acara-sheet {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')
    <!-- LIVE REALTIME RESULTS SECTION -->
    <div class="card live-section" id="liveSectionCard">
        <div class="card-header" style="flex-wrap:wrap; gap:14px; align-items:center;">
            <div style="display:flex; align-items:center; gap:16px;">
                @if (config('election.logo_instansi') && file_exists(public_path(config('election.logo_instansi'))))
                    <img src="{{ asset(config('election.logo_instansi')) }}" alt="Logo" style="height:44px; max-width:80px; object-fit:contain;">
                @endif
                <div>
                    <h2 class="card-title">Live Rekapitulasi Suara Realtime</h2>
                    <div class="fs-light-text" style="font-size:12.5px; color:var(--text-sec); margin-top:2px;">
                        {{ config('election.election_name', 'Pemilihan Raya Digital') }} &nbsp;•&nbsp; Pembaruan tiap 4 detik &nbsp;•&nbsp; Terakhir diperbarui: <span id="lastUpdatedTime">{{ $electionStats['last_updated'] }}</span>
                    </div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="live-indicator">
                    <span class="pulsing-dot"></span>
                    <span>LIVE DATA</span>
                </div>

                <!-- Tombol Fullscreen -->
                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleFullScreen()" title="Tampilkan di Layar Penuh / Proyektor">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                    </svg>
                    <span id="btnFsLabel">Layar Penuh (Full Screen)</span>
                </button>

                <button type="button" id="btnTogglePolling" class="btn btn-secondary btn-sm" onclick="togglePolling()">
                    Jeda Auto-Refresh
                </button>

                <!-- Tombol Keluar Fullscreen (hanya muncul saat mode layar penuh) -->
                <button type="button" class="btn btn-danger btn-sm btn-fs-exit" onclick="exitFullScreen()">
                    ✕ Keluar Layar Penuh
                </button>
            </div>
        </div>

        <!-- STAT BANNER PROYEKTOR / HIGH VISIBILITY -->
        <div class="fs-stat-banner" style="display:flex; justify-content:space-between; align-items:center; background:#FAFBFC; border:1px solid var(--border); border-radius:12px; padding:18px 24px; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
            <div style="display:flex; align-items:baseline; gap:14px; flex-wrap:wrap;">
                <div id="totalVotesDisplay" style="font-size:46px; font-weight:800; color:var(--accent); line-height:1;">{{ $totalVotes }}</div>
                <div>
                    <div class="fs-stat-label" style="font-size:15px; font-weight:700; color:var(--text); text-transform:uppercase; letter-spacing:0.02em;">Total Surat Suara Sah</div>
                    <div class="fs-stat-desc" style="font-size:13px; color:var(--text-sec); margin-top:2px;">Tercatat secara realtime dari bilik suara digital</div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <div style="text-align:right;">
                    <div style="font-size:11.5px; color:var(--text-sec); text-transform:uppercase; font-weight:600; letter-spacing:0.04em;">Status Pemilihan</div>
                    <div style="font-size:14px; font-weight:700; color:var(--ok); display:flex; align-items:center; gap:6px; justify-content:flex-end; margin-top:2px;">
                        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--ok); animation:pulse 1.6s infinite;"></span>
                        <span>Pemilihan Aktif</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($results->isEmpty())
            <p style="color:var(--text-sec); font-size:14px; text-align:center; padding:32px;">Belum ada data calon pada sistem.</p>
        @else
            <!-- DUA KOLOM: KIRI PROGRESS LIST & KANAN DIAGRAM PIE -->
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap:28px; align-items:stretch;">
                
                <!-- KOLOM KIRI: DAFTAR BAR PEROLEHAN SUARA -->
                <div id="liveCandidatesList" style="display:flex; flex-direction:column; gap:16px;">
                    @php
                        $palette = ['#3D5A99', '#0284C7', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
                    @endphp

                    @foreach ($results as $idx => $c)
                        @php
                            $cColor = $palette[$idx % count($palette)];
                        @endphp
                        <div class="candidate-card-item" id="candidate-block-{{ $c->id }}" style="background:#FAFBFC; border:1px solid var(--border); border-radius:12px; padding:18px 20px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:12px;">
                                <div style="display:flex; align-items:center; gap:14px;">
                                    <span class="candidate-num-badge" style="display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:10px; background:{{ $cColor }}; color:#fff; font-weight:800; font-size:18px; flex-shrink:0;">
                                        #{{ $c->nomor_urut }}
                                    </span>

                                    @if ($c->foto_ketua_url)
                                        <img src="{{ $c->foto_ketua_url }}" alt="{{ $c->nama_ketua }}" class="candidate-photo-thumb" style="width:44px; height:44px; border-radius:10px; object-fit:cover; border:1px solid var(--border);">
                                    @endif

                                    <div>
                                        <div class="candidate-name-text" style="font-weight:700; font-size:17px; color:var(--text);">
                                            {{ $c->nama_ketua }}
                                        </div>
                                        <div class="candidate-sub-text" style="font-size:13.5px; color:var(--text-sec);">
                                            {{ $c->nama_wakil ? '& ' . $c->nama_wakil : 'Calon Tunggal' }}
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <span class="candidate-vote-num" style="font-size:24px; font-weight:800; color:var(--text);" id="vote-count-{{ $c->id }}">
                                        {{ $c->votes_count }}
                                    </span>
                                    <span class="candidate-vote-label" style="font-size:13px; color:var(--text-sec); font-weight:500;">suara</span>
                                    <span class="candidate-vote-pct" style="font-size:18px; font-weight:800; color:{{ $cColor }};" id="vote-pct-{{ $c->id }}">
                                        ({{ $c->percentage }}%)
                                    </span>
                                </div>
                            </div>

                            <!-- Animated Progress Bar -->
                            <div class="progress-track" style="width:100%; height:16px; background:var(--bg); border-radius:10px; overflow:hidden; border:1px solid var(--border);">
                                <div
                                    id="vote-bar-{{ $c->id }}"
                                    style="width: {{ $c->percentage }}%; height:100%; background:{{ $cColor }}; border-radius:10px; transition: width 0.7s cubic-bezier(0.16, 1, 0.3, 1);"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- KOLOM KANAN: DIAGRAM PIE / DONUT REALTIME -->
                <div class="fs-chart-card" style="background:#FAFBFC; border:1px solid var(--border); border-radius:14px; padding:24px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                    <div class="fs-chart-title" style="font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:16px; color:var(--text);">
                        Diagram Persentase Suara
                    </div>
                    <div class="fs-chart-container" style="position:relative; width:100%; max-width:340px; height:340px;">
                        <canvas id="resultsPieChart"></canvas>
                    </div>
                </div>

            </div>
        @endif
    </div>

    <!-- FORMAL BERITA ACARA SECTION -->
    <div class="berita-acara-sheet">
        <div class="ba-header">
            @if (config('election.logo_instansi') && file_exists(public_path(config('election.logo_instansi'))))
                <div style="margin-bottom:12px;">
                    <img src="{{ asset(config('election.logo_instansi')) }}" alt="Logo Instansi" style="height:64px; max-width:180px; object-fit:contain;">
                </div>
            @endif
            <div class="ba-kop-instansi">{{ config('election.institution', 'PANITIA PEMILIHAN UMUM TERPADU') }}</div>
            <div class="ba-title">BERITA ACARA HASIL PEMUNGUTAN &amp; PENGHITUNGAN SUARA</div>
            <div class="ba-subtitle">{{ config('election.election_name', 'Pemilihan Raya Digital') }} — Tahun {{ date('Y') }}</div>
        </div>

        <div class="ba-body">
            <p style="margin-bottom:12px;">
                Pada hari ini, <strong>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</strong>, telah dilaksanakan pemungutan dan penghitungan suara secara elektronik melalui Portal E-Voting <strong>SatuSuara</strong> dengan ringkasan perolehan suara sah sebagai berikut:
            </p>

            <table class="ba-table">
                <thead>
                    <tr>
                        <th style="width:60px; text-align:center;">No</th>
                        <th style="width:90px; text-align:center;">No. Urut</th>
                        <th>Nama Pasangan Calon</th>
                        <th style="width:140px; text-align:right;">Perolehan Suara</th>
                        <th style="width:100px; text-align:right;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $idx => $r)
                        <tr>
                            <td style="text-align:center;">{{ $idx + 1 }}</td>
                            <td style="text-align:center; font-weight:700;">#{{ $r->nomor_urut }}</td>
                            <td>
                                <strong>{{ $r->nama_ketua }}</strong>
                                @if ($r->nama_wakil)
                                    &nbsp;&amp;&nbsp; {{ $r->nama_wakil }}
                                @endif
                            </td>
                            <td style="text-align:right; font-weight:600;">{{ $r->votes_count }} suara</td>
                            <td style="text-align:right;">{{ $r->percentage }}%</td>
                        </tr>
                    @endforeach
                    <tr style="background:#FAFBFC; font-weight:700;">
                        <td colspan="3" style="text-align:right;">TOTAL SUARA MASUK:</td>
                        <td style="text-align:right;">{{ $totalVotes }} suara</td>
                        <td style="text-align:right;">100.0%</td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top:16px;">
                Demikian Berita Acara ini dibuat dengan sebenar-benarnya serta dapat dipertanggungjawabkan menurut ketentuan hukum dan tata tertib pemilihan yang berlaku.
            </p>

            <div class="ba-signature-grid">
                <div class="signature-box">
                    <div class="signature-title">Ketua Panitia Pemilihan</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">( .................................... )</div>
                </div>

                <div class="signature-box">
                    <div class="signature-title">Saksi Paslon 01</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">( .................................... )</div>
                </div>

                <div class="signature-box">
                    <div class="signature-title">Saksi Paslon 02 / Independen</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">( .................................... )</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let isPolling = true;
    let pollInterval = null;
    let pieChart = null;

    const chartColors = ['#3D5A99', '#0284C7', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];

    // Inisialisasi Chart.js Pie / Donut
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('resultsPieChart');
        if (ctx) {
            const initialLabels = [
                @foreach ($results as $c)
                    "Paslon #{{ $c->nomor_urut }} ({{ $c->nama_ketua }})",
                @endforeach
            ];

            const initialData = [
                @foreach ($results as $c)
                    {{ $c->votes_count }},
                @endforeach
            ];

            pieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: initialLabels,
                    datasets: [{
                        data: initialData,
                        backgroundColor: chartColors.slice(0, initialLabels.length),
                        borderColor: '#FFFFFF',
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: "'Jost', sans-serif", size: 13, weight: '600' },
                                padding: 18,
                                color: '#111827',
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { family: "'Jost', sans-serif", size: 14, weight: '700' },
                            bodyFont: { family: "'Jost', sans-serif", size: 13 },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${value} suara (${pct}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 600,
                        easing: 'easeOutQuart'
                    },
                    cutout: '56%'
                }
            });
        }
    });

    // Polling Realtime Tiap 4 Detik
    function fetchLiveResults() {
        if (!isPolling) return;

        fetch("{{ route('admin.reports.live') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('totalVotesDisplay').innerText = data.total_votes;
                document.getElementById('lastUpdatedTime').innerText = new Date(data.last_updated).toLocaleTimeString('id-ID');

                if (data.candidates && data.candidates.length > 0) {
                    const newCounts = [];
                    const newLabels = [];

                    data.candidates.forEach(c => {
                        const countEl = document.getElementById('vote-count-' + c.id);
                        const pctEl = document.getElementById('vote-pct-' + c.id);
                        const barEl = document.getElementById('vote-bar-' + c.id);

                        if (countEl) countEl.innerText = c.votes_count;
                        if (pctEl) pctEl.innerText = `(${c.percentage}%)`;
                        if (barEl) barEl.style.width = `${c.percentage}%`;

                        newCounts.push(c.votes_count);
                        newLabels.push(`Paslon #${c.nomor_urut} (${c.nama_ketua})`);
                    });

                    // Update Data Diagram Pie secara Reaktif
                    if (pieChart) {
                        pieChart.data.labels = newLabels;
                        pieChart.data.datasets[0].data = newCounts;
                        pieChart.update('active');
                    }
                }
            })
            .catch(err => console.error("Error polling results:", err));
    }

    function togglePolling() {
        isPolling = !isPolling;
        const btn = document.getElementById('btnTogglePolling');
        if (isPolling) {
            btn.innerText = 'Jeda Auto-Refresh';
            fetchLiveResults();
        } else {
            btn.innerText = 'Lanjutkan Auto-Refresh';
        }
    }

    // Fullscreen Toggles
    function toggleFullScreen() {
        const card = document.getElementById('liveSectionCard');
        if (!document.fullscreenElement) {
            if (card.requestFullscreen) {
                card.requestFullscreen();
            } else if (card.webkitRequestFullscreen) {
                card.webkitRequestFullscreen();
            }
        } else {
            exitFullScreen();
        }
    }

    function exitFullScreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    }

    // Sesuaikan elemen dan legenda chart saat masuk/keluar fullscreen
    document.addEventListener('fullscreenchange', function() {
        const isFs = !!document.fullscreenElement;
        const fsBtnLabel = document.getElementById('btnFsLabel');
        if (fsBtnLabel) {
            fsBtnLabel.innerText = isFs ? 'Perkecil Layar (Esc)' : 'Layar Penuh (Full Screen)';
        }

        if (pieChart && pieChart.options.plugins.legend) {
            // Tema Terang: Teks selalu gelap & kontras tinggi di layar proyektor
            pieChart.options.plugins.legend.labels.color = '#111827';
            pieChart.options.plugins.legend.labels.font.size = isFs ? 15 : 13;
            pieChart.options.plugins.legend.labels.padding = isFs ? 22 : 16;
            pieChart.update();
        }
    });

    // Start 4s interval polling
    pollInterval = setInterval(fetchLiveResults, 4000);
</script>
@endpush
