<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Vote;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with election statistics.
     */
    public function index(): View
    {
        $votersRole = Role::findByName('voters', 'web');

        $totalVoters = User::role('voters')->count();
        $totalCandidates = Candidate::count();
        $totalVotes = Vote::count();

        $results = Candidate::withCount('votes')
            ->orderBy('nomor_urut')
            ->get();

        $hasVoted = User::role('voters')
            ->whereHas('vote')
            ->with('vote.candidate')
            ->get();

        $haventVoted = $totalVoters - $hasVoted->count();

        return view('admin.dashboard', compact(
            'totalVoters',
            'totalCandidates',
            'totalVotes',
            'results',
            'hasVoted',
            'haventVoted',
        ));
    }
}
