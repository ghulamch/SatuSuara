<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pemilihan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 20mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        .kop-table {
            width: 100%;
            border-bottom: 2.5px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .kop-table td {
            vertical-align: middle;
        }

        .kop-instansi {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111827;
        }

        .kop-agenda {
            font-size: 14pt;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 2px;
            color: #1E3A8A;
        }

        .kop-sub {
            font-size: 9.5pt;
            color: #4B5563;
            margin-top: 2px;
        }

        .doc-title-box {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .doc-number {
            font-size: 10pt;
            color: #374151;
            margin-top: 3px;
        }

        .content-p {
            font-size: 10.5pt;
            text-align: justify;
            margin-bottom: 14px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 20px 0;
            font-size: 10pt;
        }

        .results-table th,
        .results-table td {
            border: 1px solid #374151;
            padding: 8px 10px;
        }

        .results-table th {
            background-color: #F3F4F6;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .signature-table {
            width: 100%;
            margin-top: 36px;
            text-align: center;
            font-size: 10pt;
        }

        .signature-table td {
            vertical-align: top;
            width: 33.33%;
            padding: 0 8px;
            text-align: center;
        }

        .signature-role {
            font-weight: bold;
            font-size: 10pt;
            color: #111827;
            min-height: 28px;
        }

        .signature-space {
            height: 65px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10pt;
            color: #111827;
            white-space: nowrap;
        }

        .footer-stamp {
            margin-top: 36px;
            border-top: 1px dashed #9CA3AF;
            padding-top: 10px;
            font-size: 8.5pt;
            color: #4B5563;
            display: table;
            width: 100%;
        }

        .footer-stamp-left {
            display: table-cell;
            vertical-align: middle;
        }

        .footer-stamp-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            font-family: monospace;
            font-size: 8pt;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI -->
    <table class="kop-table">
        <tr>
            @if ($logoBase64)
                <td style="width: 80px; text-align: left;">
                    <img src="{{ $logoBase64 }}" alt="Logo" style="height: 60px; max-width: 80px;">
                </td>
            @endif
            <td style="text-align: {{ $logoBase64 ? 'left' : 'center' }};">
                <div class="kop-instansi">{{ config('election.institution', 'KOMISI PEMILIHAN UMUM TERPADU') }}</div>
                <div class="kop-agenda">{{ config('election.election_name', 'Pemilihan Raya Digital') }}</div>
                <div class="kop-sub">Portal Pemungutan Suara Elektronik SatuSuara &nbsp;•&nbsp; Tahun {{ date('Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title-box">
        <div class="doc-title">BERITA ACARA REKAPITULASI HASIL PEMILIHAN</div>
        <div class="doc-number">Nomor: BA/{{ date('Y') }}/E-VOTE/{{ strtoupper(substr($verificationHash, 0, 8)) }}</div>
    </div>

    <!-- NARASI RESMI -->
    <div class="content-p">
        Pada hari ini, <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>, bertempat di Pusat Kendali Pemilihan Suara Elektronik <strong>{{ config('election.institution') }}</strong>, telah diselenggarakan penutupan serta penghitungan perolehan suara secara elektronik melalui Portal <strong>SatuSuara</strong>. Berdasarkan audit data kotak suara terenkripsi, rincian perolehan suara sah adalah sebagai berikut:
    </div>

    <!-- TABEL HASIL PEROLEHAN SUARA -->
    <table class="results-table">
        <thead>
            <tr>
                <th style="width: 35px; text-align: center;">No</th>
                <th style="width: 75px; text-align: center;">No. Urut</th>
                <th>Nama Pasangan Calon / Opsi</th>
                <th style="width: 120px; text-align: right;">Perolehan Suara</th>
                <th style="width: 90px; text-align: right;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $idx => $r)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center; font-weight: bold;">#{{ $r->nomor_urut }}</td>
                    <td>
                        <strong>{{ $r->nama_ketua }}</strong>
                        @if ($r->nama_wakil)
                            &nbsp;&amp;&nbsp; {{ $r->nama_wakil }}
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($r->votes_count, 0, ',', '.') }} suara</td>
                    <td style="text-align: right;">{{ number_format($r->percentage, 2, ',', '.') }}%</td>
                </tr>
            @endforeach
            <tr style="background-color: #F9FAFB; font-weight: bold;">
                <td colspan="3" style="text-align: right; padding-right: 14px;">TOTAL SURAT SUARA SAH:</td>
                <td style="text-align: right;">{{ number_format($totalVotes, 0, ',', '.') }} suara</td>
                <td style="text-align: right;">100,00%</td>
            </tr>
        </tbody>
    </table>

    <div class="content-p">
        Demikian Berita Acara Rekapitulasi ini dibuat dengan sebenar-benarnya dalam keadaan sadar dan tanpa paksaan dari pihak mana pun, untuk dipergunakan sebagai dasar penetapan hasil akhir pemilihan resmi yang sah menurut tata tertib organisasi yang berlaku.
    </div>

    <!-- TANDA TANGAN 3 PIHAK -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-role">Ketua Panitia Pemilihan</div>
                <div class="signature-space"></div>
                <div class="signature-name">( .................................... )</div>
            </td>
            <td>
                <div class="signature-role">Saksi Paslon No. 01</div>
                <div class="signature-space"></div>
                <div class="signature-name">( .................................... )</div>
            </td>
            <td>
                <div class="signature-role">Saksi Paslon No. 02 / Independen</div>
                <div class="signature-space"></div>
                <div class="signature-name">( .................................... )</div>
            </td>
        </tr>
    </table>

    <!-- FOOTER VERIFIKASI DIGITAL & KRIPTOGRAFI -->
    <div class="footer-stamp">
        <div class="footer-stamp-left">
            <strong>Keaslian Dokumen Terverifikasi Secara Elektronik:</strong><br>
            Diterbitkan oleh Portal SatuSuara pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i:s T') }}
        </div>
        <div class="footer-stamp-right">
            HASH DOKUMEN:<br>
            {{ strtoupper(substr($verificationHash, 0, 32)) }}
        </div>
    </div>

</body>
</html>

