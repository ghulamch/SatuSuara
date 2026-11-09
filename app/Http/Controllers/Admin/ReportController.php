<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Vote;
use App\Support\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the full election results report.
     */
    public function index(): View
    {
        $totalVotes = Vote::count();

        $results = Candidate::withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->orderBy('nomor_urut')
            ->get()
            ->map(function (Candidate $candidate) use ($totalVotes) {
                $candidate->percentage = $totalVotes > 0
                    ? round(($candidate->votes_count / $totalVotes) * 100, 2)
                    : 0;

                return $candidate;
            });

        // Opsi Kotak Kosong handling (Hanya muncul jika calon tunggal atau ada suara tersimpan)
        $kotakKosongVotes = Vote::where('is_kotak_kosong', true)->count();
        $hasKotakKosong = $kotakKosongVotes > 0 || (Candidate::count() === 1 && config('election.enable_kotak_kosong', true));

        if ($hasKotakKosong) {
            $kotakKosongPct = $totalVotes > 0 ? round(($kotakKosongVotes / $totalVotes) * 100, 2) : 0;
            $results->push((object) [
                'id'              => 'kotak_kosong',
                'nomor_urut'      => '-',
                'nama_ketua'      => 'KOTAK KOSONG',
                'nama_wakil'      => 'Opsi Tidak Menyetujui',
                'foto_ketua_url'  => null,
                'votes_count'     => $kotakKosongVotes,
                'percentage'      => $kotakKosongPct,
                'is_kotak_kosong' => true,
            ]);
        }

        $electionStats = [
            'total_votes'      => $totalVotes,
            'total_candidates' => Candidate::count() + ($hasKotakKosong ? 1 : 0),
            'last_updated'     => now()->toDateTimeString(),
        ];

        return view('admin.reports.index', compact('results', 'totalVotes', 'electionStats'));
    }

    /**
     * Return live election results as JSON for polling.
     */
    public function liveResults(): JsonResponse
    {
        $totalVotes = Vote::count();

        $candidates = Candidate::withCount('votes')
            ->orderBy('nomor_urut')
            ->get()
            ->map(function (Candidate $candidate) use ($totalVotes) {
                return [
                    'id'           => $candidate->id,
                    'nomor_urut'   => $candidate->nomor_urut,
                    'nama_ketua'   => $candidate->nama_ketua,
                    'nama_wakil'   => $candidate->nama_wakil,
                    'votes_count'  => $candidate->votes_count,
                    'percentage'   => $totalVotes > 0
                        ? round(($candidate->votes_count / $totalVotes) * 100, 2)
                        : 0,
                ];
            });

        $kotakKosongVotes = Vote::where('is_kotak_kosong', true)->count();
        $hasKotakKosong = $kotakKosongVotes > 0 || (Candidate::count() === 1 && config('election.enable_kotak_kosong', true));

        if ($hasKotakKosong) {
            $kotakKosongPct = $totalVotes > 0 ? round(($kotakKosongVotes / $totalVotes) * 100, 2) : 0;
            $candidates->push([
                'id'           => 'kotak_kosong',
                'nomor_urut'   => '-',
                'nama_ketua'   => 'KOTAK KOSONG',
                'nama_wakil'   => 'Opsi Tidak Menyetujui',
                'votes_count'  => $kotakKosongVotes,
                'percentage'   => $kotakKosongPct,
            ]);
        }

        return response()->json([
            'candidates'   => $candidates,
            'total_votes'  => $totalVotes,
            'last_updated' => now()->toISOString(),
        ]);
    }

    /**
     * Generate official formal PDF of Berita Acara with cryptographic validation hash.
     */
    public function exportPdf()
    {
        $totalVotes = Vote::count();

        $results = Candidate::withCount('votes')
            ->orderBy('nomor_urut')
            ->get()
            ->map(function (Candidate $candidate) use ($totalVotes) {
                $candidate->percentage = $totalVotes > 0
                    ? round(($candidate->votes_count / $totalVotes) * 100, 2)
                    : 0;

                return $candidate;
            });

        $kotakKosongVotes = Vote::where('is_kotak_kosong', true)->count();
        $hasKotakKosong = $kotakKosongVotes > 0 || (Candidate::count() === 1 && config('election.enable_kotak_kosong', true));

        if ($hasKotakKosong) {
            $kotakKosongPct = $totalVotes > 0 ? round(($kotakKosongVotes / $totalVotes) * 100, 2) : 0;
            $results->push((object) [
                'id'              => 'kotak_kosong',
                'nomor_urut'      => '-',
                'nama_ketua'      => 'KOTAK KOSONG',
                'nama_wakil'      => 'Opsi Tidak Menyetujui',
                'foto_ketua_url'  => null,
                'votes_count'     => $kotakKosongVotes,
                'percentage'      => $kotakKosongPct,
                'is_kotak_kosong' => true,
            ]);
        }

        // Generate cryptographic hash stamp
        $verificationHash = hash('sha256', $totalVotes . '-' . config('election.election_name') . '-' . now()->toIso8601String() . '-' . uniqid());

        // Base64 encode institution logo for dompdf rendering reliability
        $logoBase64 = null;
        $logoPath = config('election.logo_instansi');
        if ($logoPath && file_exists(public_path($logoPath))) {
            $ext = strtolower(pathinfo(public_path($logoPath), PATHINFO_EXTENSION));
            $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/' . $ext;
            $data = file_get_contents(public_path($logoPath));
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('admin.reports.pdf', compact('results', 'totalVotes', 'verificationHash', 'logoBase64'));
        $pdf->setPaper('a4', 'portrait');

        AuditLogger::log('laporan', 'Mengunduh berkas resmi Berita Acara Rekapitulasi Pemilihan dalam format PDF', [
            'verification_hash' => $verificationHash,
            'total_votes'       => $totalVotes,
        ]);

        $fileName = 'Berita_Acara_Pemilihan_' . Str::slug(config('election.institution', 'SatuSuara')) . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
