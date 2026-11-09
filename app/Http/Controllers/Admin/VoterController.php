<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\VotersImport;
use App\Models\User;
use App\Models\VoterQueue;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VoterController extends Controller
{
    /**
     * Display a list of all voters with their voting status.
     */
    public function index(): View
    {
        $voters = User::role('voters')
            ->with('vote')
            ->orderBy('name')
            ->get();

        return view('admin.voters.index', compact('voters'));
    }

    /**
     * Show the form to create a new voter account.
     */
    public function create(): View
    {
        return view('admin.voters.create');
    }

    /**
     * Store a newly created voter account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('voters');

        AuditLogger::log('dpt', "Mendaftarkan pemilih baru: {$user->name} ({$user->email})");

        return redirect()->route('admin.voters.index')
            ->with('success', 'Akun voter berhasil dibuat.');
    }

    /**
     * Import voters from an uploaded Excel or CSV file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
        ], [
            'file.required' => 'Silakan pilih berkas Excel atau CSV untuk diunggah.',
            'file.mimes'    => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran berkas maksimal adalah 5MB.',
        ]);

        try {
            $import = new VotersImport();
            Excel::import($import, $request->file('file'));

            $message = "Proses import selesai. Berhasil menambahkan {$import->importedCount} data pemilih baru.";
            if ($import->skippedCount > 0) {
                $message .= " ({$import->skippedCount} baris dilewati).";
            }

            AuditLogger::log('dpt', "Mengimpor {$import->importedCount} data DPT dari berkas Excel/CSV", [
                'imported_count' => $import->importedCount,
                'skipped_count'  => $import->skippedCount,
            ]);

            return redirect()->route('admin.voters.index')
                ->with('success', $message)
                ->with('import_warnings', $import->messages);
        } catch (\Throwable $e) {
            return redirect()->route('admin.voters.index')
                ->with('error', 'Gagal memproses berkas Excel: ' . $e->getMessage());
        }
    }

    /**
     * Download an Excel/CSV template for importing voters.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $fileName = 'template_import_pemilih_satusuara.csv';

        return response()->streamDownload(function () {
            // Output UTF-8 BOM so Excel opens indonesian characters properly
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');
            // Header
            fputcsv($handle, ['nama', 'email', 'password']);

            // Sample rows
            fputcsv($handle, ['Ahmad Fauzan Pratama', 'ahmad.fauzan@instansi.ac.id', 'password123']);
            fputcsv($handle, ['Siti Nurhaliza Putri', 'siti.nurhaliza@instansi.ac.id', 'password123']);
            fputcsv($handle, ['Budi Santoso Wibowo', 'budi.santoso@instansi.ac.id', 'password123']);
            fputcsv($handle, ['Dewi Lestari Anggraeni', 'dewi.lestari@instansi.ac.id', 'password123']);

            fclose($handle);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Delete the specified voter account.
     */
    public function destroy(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $userInfo = "{$user->name} ({$user->email})";
        $user->delete();

        AuditLogger::log('dpt', "Menghapus akun pemilih DPT: {$userInfo}");

        return redirect()->route('admin.voters.index')
            ->with('success', 'Akun voter berhasil dihapus.');
    }

    /**
     * Reset the vote of the specified voter.
     */
    public function resetVote(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->vote) {
            $user->vote->delete();
        }

        // Clean up queue session so voter can enter cleanly
        VoterQueue::where('user_id', $user->id)->delete();

        AuditLogger::log('reset', "Mereset hak suara pemilih: {$user->name} ({$user->email})");

        return redirect()->route('admin.voters.index')
            ->with('success', 'Suara voter berhasil direset.');
    }

    /**
     * Reset the active device session of the specified voter.
     */
    public function resetSession(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $user->unlockSession();

        // Also remove any queue session entry so voter can re-enter freshly
        VoterQueue::where('user_id', $user->id)->delete();

        AuditLogger::log('reset', "Mereset sesi perangkat aktif pemilih: {$user->name} ({$user->email})");

        return redirect()->route('admin.voters.index')
            ->with('success', "Sesi perangkat pemilih {$user->name} berhasil direset. Pemilih dapat login kembali di perangkat baru.");
    }
}
