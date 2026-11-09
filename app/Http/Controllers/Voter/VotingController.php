<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\VoterQueue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VotingController extends Controller
{
    /**
     * Determine current voting schedule status.
     *
     * @return array{status: string, start_time: ?string, end_time: ?string}
     */
    protected function getScheduleStatus(): array
    {
        $statusPelaksanaan = config('election.status_pelaksanaan', 'auto');
        $startTime = config('election.start_time');
        $endTime = config('election.end_time');

        if ($statusPelaksanaan === 'buka') {
            return [
                'status'     => 'DIBUKA',
                'start_time' => $startTime ? Carbon::parse($startTime)->toISOString() : null,
                'end_time'   => $endTime ? Carbon::parse($endTime)->toISOString() : null,
            ];
        }

        if ($statusPelaksanaan === 'tutup') {
            return [
                'status'     => 'DITUTUP',
                'start_time' => $startTime ? Carbon::parse($startTime)->toISOString() : null,
                'end_time'   => $endTime ? Carbon::parse($endTime)->toISOString() : null,
            ];
        }

        $now = now();

        if ($startTime) {
            $start = Carbon::parse($startTime);
            if ($now->lt($start)) {
                return [
                    'status'     => 'BELUM_DIBUKA',
                    'start_time' => $start->toISOString(),
                    'end_time'   => $endTime ? Carbon::parse($endTime)->toISOString() : null,
                ];
            }
        }

        if ($endTime) {
            $end = Carbon::parse($endTime);
            if ($now->gt($end)) {
                return [
                    'status'     => 'DITUTUP',
                    'start_time' => $startTime ? Carbon::parse($startTime)->toISOString() : null,
                    'end_time'   => $end->toISOString(),
                ];
            }
        }

        return [
            'status'     => 'DIBUKA',
            'start_time' => $startTime ? Carbon::parse($startTime)->toISOString() : null,
            'end_time'   => $endTime ? Carbon::parse($endTime)->toISOString() : null,
        ];
    }

    /**
     * Housekeeping and promotion of queue waiting voters into active booth slots.
     */
    protected function promoteNextVoters(): void
    {
        $maxConcurrent = max(1, (int) config('election.max_concurrent_voters', 10));
        $sessionMinutes = max(1, (int) config('election.booth_session_minutes', 5));

        // 0. Free booth slots for voters who have already successfully cast their vote
        VoterQueue::where('status', 'active')
            ->whereIn('user_id', Vote::pluck('user_id'))
            ->update(['status' => 'completed']);

        // 1. Expire stale active voters who exceeded max booth time or haven't sent heartbeat in 60s
        VoterQueue::active()
            ->where(function ($q) use ($sessionMinutes) {
                $q->where('entered_booth_at', '<', now()->subMinutes($sessionMinutes))
                  ->orWhere('last_heartbeat_at', '<', now()->subSeconds(60));
            })
            ->update(['status' => 'expired']);

        // 2. Expire waiting voters who left or closed tab (no heartbeat in 60s)
        VoterQueue::waiting()
            ->where('last_heartbeat_at', '<', now()->subSeconds(60))
            ->update(['status' => 'expired']);

        // 3. Count valid active voters
        $activeCount = VoterQueue::active()->count();
        $availableSlots = max(0, $maxConcurrent - $activeCount);

        // 4. Promote next waiting voters in order of queue_number
        if ($availableSlots > 0) {
            $promotable = VoterQueue::waiting()
                ->orderBy('queue_number', 'asc')
                ->take($availableSlots)
                ->get();

            foreach ($promotable as $item) {
                $item->update([
                    'status'           => 'active',
                    'entered_booth_at' => now(),
                    'last_heartbeat_at'=> now(),
                ]);
            }
        }
    }

    /**
     * Manage user queue position and admission.
     *
     * @return array{can_enter: bool, queue_number?: int, ahead_count?: int, estimated_wait_text?: string}
     */
    protected function manageQueue(User $user): array
    {
        $queueEnabled = config('election.enable_queue', true);

        if (! $queueEnabled) {
            return ['can_enter' => true];
        }

        $maxConcurrent = max(1, (int) config('election.max_concurrent_voters', 10));

        // Fetch or create user's queue token
        $queue = VoterQueue::where('user_id', $user->id)->first();

        if (! $queue) {
            $maxQueue = VoterQueue::max('queue_number') ?? 0;
            $queue = VoterQueue::create([
                'user_id'           => $user->id,
                'queue_number'      => $maxQueue + 1,
                'status'            => 'waiting',
                'last_heartbeat_at' => now(),
            ]);
        } elseif ($queue->status !== 'active' && $queue->status !== 'waiting') {
            // Re-queue voters with reset, completed, expired, or invalid previous status
            $maxQueue = VoterQueue::max('queue_number') ?? 0;
            $queue->update([
                'queue_number'      => $maxQueue + 1,
                'status'            => 'waiting',
                'entered_booth_at'  => null,
                'last_heartbeat_at' => now(),
            ]);
        } else {
            $queue->update(['last_heartbeat_at' => now()]);
        }

        // Run promotion
        $this->promoteNextVoters();
        $queue->refresh();

        if ($queue->status === 'active') {
            return [
                'can_enter'    => true,
                'queue_number' => $queue->queue_number,
            ];
        }

        // Waiting in line
        $aheadCount = VoterQueue::waiting()
            ->where('queue_number', '<', $queue->queue_number)
            ->count();

        // Safety fallback: if nobody is ahead and active slots are available, admit immediately
        if ($aheadCount === 0 && VoterQueue::active()->count() < $maxConcurrent) {
            $queue->update([
                'status'           => 'active',
                'entered_booth_at' => now(),
                'last_heartbeat_at'=> now(),
            ]);

            return [
                'can_enter'    => true,
                'queue_number' => $queue->queue_number,
            ];
        }

        $estMinutes = max(1, (int) ceil(($aheadCount + 1) / $maxConcurrent) * 1.5);

        return [
            'can_enter'           => false,
            'queue_number'        => $queue->queue_number,
            'ahead_count'         => $aheadCount,
            'estimated_wait_text' => "~{$estMinutes} Menit",
        ];
    }

    /**
     * Display the voter's digital voting booth.
     */
    public function index(): View
    {
        $candidates = Candidate::aktif()->orderBy('nomor_urut')->get();
        $user = auth()->user();
        $sudahVoting = $user->sudahVoting();
        $voteKu = $user->vote;

        $schedule = $this->getScheduleStatus();
        $votingStatus = $schedule['status'];
        $startTimeTarget = $schedule['start_time'];
        $endTimeTarget = $schedule['end_time'];

        $queueInfo = null;

        if (! $sudahVoting && $votingStatus === 'DIBUKA') {
            $queueInfo = $this->manageQueue($user);
        }

        $now = now();
        $serverTimeNowMs = (int) ($now->getPreciseTimestamp(3));
        $serverTargetMs = $startTimeTarget ? (int) (Carbon::parse($startTimeTarget)->getPreciseTimestamp(3)) : null;
        $serverRemainingMs = ($serverTargetMs && $serverTargetMs > $serverTimeNowMs)
            ? ($serverTargetMs - $serverTimeNowMs)
            : 0;

        // Kotak Kosong HANYA berlaku jika calon tunggal (jumlah paslon tepat 1)
        $isSingleCandidate = ($candidates->count() === 1) && config('election.enable_kotak_kosong', true);

        return view('voter.dashboard', compact(
            'candidates',
            'sudahVoting',
            'voteKu',
            'votingStatus',
            'startTimeTarget',
            'endTimeTarget',
            'queueInfo',
            'serverTimeNowMs',
            'serverTargetMs',
            'serverRemainingMs',
            'isSingleCandidate'
        ));
    }

    /**
     * Authoritative server time and remaining ms for synchronized countdown.
     * Prevents voter device clock tampering from altering the countdown.
     */
    public function serverTime(): JsonResponse
    {
        $schedule = $this->getScheduleStatus();
        $now = now();
        $startTime = $schedule['start_time'] ? Carbon::parse($schedule['start_time']) : null;
        $serverTimeNowMs = (int) ($now->getPreciseTimestamp(3));
        $serverTargetMs = $startTime ? (int) ($startTime->getPreciseTimestamp(3)) : null;
        $remainingMs = ($serverTargetMs && $serverTargetMs > $serverTimeNowMs)
            ? ($serverTargetMs - $serverTimeNowMs)
            : 0;

        return response()->json([
            'server_time'    => $now->toISOString(),
            'server_time_ms' => $serverTimeNowMs,
            'server_clock'   => $now->format('H:i:s'),
            'target_time_ms' => $serverTargetMs,
            'remaining_ms'   => $remainingMs,
            'status'         => $schedule['status'],
            'timezone'       => config('app.timezone'),
        ]);
    }

    /**
     * Poll endpoint to check queue status and receive heartbeat.
     */
    public function queueStatus(): JsonResponse
    {
        $user = auth()->user();

        if ($user->sudahVoting()) {
            return response()->json([
                'can_enter' => true,
                'status'    => 'voted',
            ]);
        }

        $schedule = $this->getScheduleStatus();

        if ($schedule['status'] !== 'DIBUKA') {
            return response()->json([
                'can_enter' => false,
                'status'    => $schedule['status'],
            ]);
        }

        $queueInfo = $this->manageQueue($user);

        return response()->json([
            'can_enter'           => $queueInfo['can_enter'],
            'status'              => $queueInfo['can_enter'] ? 'active' : 'waiting',
            'queue_number'        => $queueInfo['queue_number'] ?? null,
            'ahead_count'         => $queueInfo['ahead_count'] ?? 0,
            'estimated_wait_text' => $queueInfo['estimated_wait_text'] ?? '~1 Menit',
        ]);
    }

    /**
     * Store the voter's vote.
     */
    public function store(Request $request): RedirectResponse
    {
        $schedule = $this->getScheduleStatus();

        if ($schedule['status'] === 'BELUM_DIBUKA') {
            return back()->with('error', 'Bilik pemungutan suara belum dibuka. Silakan tunggu hingga waktu pelaksanaan.');
        }

        if ($schedule['status'] === 'DITUTUP') {
            return back()->with('error', 'Waktu pemungutan suara telah berakhir.');
        }

        $user = auth()->user();

        // Check if queue is enabled and voter is allowed in booth
        $queueEnabled = config('election.enable_queue', true);
        if ($queueEnabled) {
            $queue = VoterQueue::where('user_id', $user->id)->first();
            if (! $queue || $queue->status !== 'active') {
                return back()->with('error', 'Sesi antrian Anda tidak valid atau telah kedaluwarsa. Silakan muat ulang halaman.');
            }
        }

        $isKotakKosong = $request->boolean('kotak_kosong') || $request->input('candidate_id') === 'kotak_kosong';
        $candidateId = null;

        if ($isKotakKosong) {
            $allowedKotakKosong = (Candidate::where('aktif', true)->count() === 1) && config('election.enable_kotak_kosong', true);
            if (! $allowedKotakKosong) {
                return back()->with('error', 'Opsi Kotak Kosong hanya berlaku pada agenda pemilihan dengan calon tunggal (1 paslon).');
            }
        } else {
            $request->validate([
                'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
            ]);

            $candidate = Candidate::where('id', $request->candidate_id)
                ->where('aktif', true)
                ->first();

            if (! $candidate) {
                return back()->with('error', 'Kandidat tidak valid atau tidak aktif.');
            }

            $candidateId = $candidate->id;
        }

        // Prevent double voting
        if ($user->sudahVoting()) {
            return back()->with('error', 'Anda sudah memberikan suara.');
        }

        // Generate cryptographic receipt hash code (e.g. VOTE-8F2A-99B1-3E2C)
        $receiptCode = Vote::generateReceiptCode($user->id);

        Vote::create([
            'user_id'         => $user->id,
            'candidate_id'    => $candidateId,
            'is_kotak_kosong' => $isKotakKosong,
            'receipt_hash'    => $receiptCode,
            'voted_at'        => now(),
        ]);

        // Mark queue as completed and promote next waiting voter
        VoterQueue::where('user_id', $user->id)->update(['status' => 'completed']);
        $this->promoteNextVoters();

        // Release single session lock once voting is finished
        $user->unlockSession();

        return back()->with('status', 'Suara Anda berhasil dicatat secara sah. Terima kasih telah berpartisipasi!')
            ->with('receipt_code', $receiptCode);
    }

    /**
     * Display the public cryptographic receipt verification portal.
     */
    public function verifyReceiptPage(): View
    {
        return view('voter.verify_receipt');
    }

    /**
     * Verify whether a cryptographic receipt code exists in the official ballot box.
     * Preserves secret ballot by NEVER disclosing which candidate was chosen!
     */
    public function verifyReceiptCheck(Request $request): View
    {
        $request->validate([
            'receipt_hash' => ['required', 'string', 'max:50'],
        ]);

        $code = trim(strtoupper($request->input('receipt_hash')));
        $vote = Vote::where('receipt_hash', $code)->first();

        $result = [
            'searched_code' => $code,
            'is_valid'      => ! is_null($vote),
            'voted_at'      => $vote ? $vote->voted_at->translatedFormat('l, d F Y — H:i:s T') : null,
        ];

        return view('voter.verify_receipt', compact('result'));
    }
}
