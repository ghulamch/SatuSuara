<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Vote;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CandidateController extends Controller
{
    /**
     * Display a paginated list of candidates.
     */
    public function index(): View
    {
        $candidates = Candidate::orderBy('nomor_urut')->paginate(10);

        return view('admin.candidates.index', compact('candidates'));
    }

    /**
     * Show the form to create a new candidate.
     */
    public function create(): View
    {
        return view('admin.candidates.create');
    }

    /**
     * Store a newly created candidate.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_urut'  => ['required', 'integer', 'unique:candidates,nomor_urut'],
            'nama_ketua'  => ['required', 'string', 'max:255'],
            'nama_wakil'  => ['nullable', 'string', 'max:255'],
            'visi'        => ['nullable', 'string', 'max:500'],
            'misi'        => ['nullable', 'string'],
            'foto_ketua'  => ['nullable', 'image', 'max:2048'],
            'foto_wakil'  => ['nullable', 'image', 'max:2048'],
            'aktif'       => ['boolean'],
        ]);

        $uploadPath = public_path('uploads/candidates');
        if (! file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($request->hasFile('foto_ketua')) {
            $file = $request->file('foto_ketua');
            $filename = time() . '_ketua_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $validated['foto_ketua'] = 'uploads/candidates/' . $filename;
        }

        if ($request->hasFile('foto_wakil')) {
            $file = $request->file('foto_wakil');
            $filename = time() . '_wakil_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $validated['foto_wakil'] = 'uploads/candidates/' . $filename;
        }

        $validated['aktif'] = $request->boolean('aktif', true);

        $created = Candidate::create($validated);

        AuditLogger::log('paslon', "Menambahkan pasangan calon baru: #{$created->nomor_urut} {$created->nama_ketua}" . ($created->nama_wakil ? " & {$created->nama_wakil}" : ""));

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Kandidat berhasil ditambahkan.');
    }

    /**
     * Show the form to edit an existing candidate.
     */
    public function edit(string $id): View
    {
        $candidate = Candidate::findOrFail($id);

        return view('admin.candidates.edit', compact('candidate'));
    }

    /**
     * Update the specified candidate.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $candidate = Candidate::findOrFail($id);

        $validated = $request->validate([
            'nomor_urut'  => ['required', 'integer', 'unique:candidates,nomor_urut,' . $candidate->id],
            'nama_ketua'  => ['required', 'string', 'max:255'],
            'nama_wakil'  => ['nullable', 'string', 'max:255'],
            'visi'        => ['nullable', 'string', 'max:500'],
            'misi'        => ['nullable', 'string'],
            'foto_ketua'  => ['nullable', 'image', 'max:2048'],
            'foto_wakil'  => ['nullable', 'image', 'max:2048'],
            'aktif'       => ['boolean'],
        ]);

        $uploadPath = public_path('uploads/candidates');
        if (! file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($request->hasFile('foto_ketua')) {
            // Delete old photo if it exists in public directory
            if ($candidate->foto_ketua && file_exists(public_path($candidate->foto_ketua))) {
                @unlink(public_path($candidate->foto_ketua));
            }
            $file = $request->file('foto_ketua');
            $filename = time() . '_ketua_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $validated['foto_ketua'] = 'uploads/candidates/' . $filename;
        }

        if ($request->hasFile('foto_wakil')) {
            // Delete old photo if it exists in public directory
            if ($candidate->foto_wakil && file_exists(public_path($candidate->foto_wakil))) {
                @unlink(public_path($candidate->foto_wakil));
            }
            $file = $request->file('foto_wakil');
            $filename = time() . '_wakil_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $validated['foto_wakil'] = 'uploads/candidates/' . $filename;
        }

        $validated['aktif'] = $request->boolean('aktif', $candidate->aktif);

        $candidate->update($validated);

        AuditLogger::log('paslon', "Memperbarui pasangan calon: #{$candidate->nomor_urut} {$candidate->nama_ketua}");

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Kandidat berhasil diperbarui.');
    }

    /**
     * Delete the specified candidate, their vote transactions, and their photos.
     */
    public function destroy(string $id): RedirectResponse
    {
        $candidate = Candidate::findOrFail($id);
        $candidateInfo = "#{$candidate->nomor_urut} {$candidate->nama_ketua}";

        $deletedVotesCount = 0;

        DB::transaction(function () use ($candidate, $candidateInfo, &$deletedVotesCount) {
            // Hapus seluruh transaksi voting yang memilih paslon ini agar data suara sinkron
            $deletedVotesCount = Vote::where('candidate_id', $candidate->id)->delete();

            // Hapus berkas foto dari penyimpanan publik
            if ($candidate->foto_ketua && file_exists(public_path($candidate->foto_ketua))) {
                @unlink(public_path($candidate->foto_ketua));
            }

            if ($candidate->foto_wakil && file_exists(public_path($candidate->foto_wakil))) {
                @unlink(public_path($candidate->foto_wakil));
            }

            $candidate->delete();

            AuditLogger::log('paslon', "Menghapus pasangan calon {$candidateInfo} beserta {$deletedVotesCount} transaksi suara terkait.");
        });

        $message = "Kandidat {$candidateInfo} berhasil dihapus.";
        if ($deletedVotesCount > 0) {
            $message .= " Sebanyak {$deletedVotesCount} suara terkait otomatis dibersihkan dan hak pilih pemilih telah dikembalikan.";
        }

        return redirect()->route('admin.candidates.index')
            ->with('success', $message);
    }
}
