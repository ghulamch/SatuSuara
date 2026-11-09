<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckInController extends Controller
{
    /**
     * Display the TPS QR Scanner & Check-in Dashboard.
     */
    public function index(Request $request): View
    {
        $votersQuery = User::role('voters');

        $totalVoters = (clone $votersQuery)->count();
        $checkedInCount = (clone $votersQuery)->whereNotNull('checked_in_at')->count();
        $notCheckedInCount = max(0, $totalVoters - $checkedInCount);
        $attendancePercentage = $totalVoters > 0 ? round(($checkedInCount / $totalVoters) * 100, 1) : 0;

        $recentCheckins = (clone $votersQuery)
            ->whereNotNull('checked_in_at')
            ->orderBy('checked_in_at', 'desc')
            ->take(8)
            ->get();

        $searchQuery = (clone $votersQuery);
        if ($request->filled('search')) {
            $s = $request->query('search');
            $searchQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('attendance_token', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->query('status') === 'hadir') {
                $searchQuery->whereNotNull('checked_in_at');
            } elseif ($request->query('status') === 'belum') {
                $searchQuery->whereNull('checked_in_at');
            }
        }

        $votersList = $searchQuery->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.checkin.index', compact(
            'totalVoters',
            'checkedInCount',
            'notCheckedInCount',
            'attendancePercentage',
            'recentCheckins',
            'votersList'
        ));
    }

    /**
     * Process scanned QR code or manual token input.
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'max:500'],
        ]);

        $rawInput = trim($request->input('token'));

        // Extract SS-XXXX-XXXX token pattern if QR code contains a URL, label, or extra text
        $extractedToken = $rawInput;
        if (preg_match('/SS-[A-Z0-9]{4}-[A-Z0-9]{4}/i', $rawInput, $matches)) {
            $extractedToken = strtoupper($matches[0]);
        } elseif (str_contains($rawInput, 'token=')) {
            parse_str(parse_url($rawInput, PHP_URL_QUERY) ?? '', $queryParams);
            if (! empty($queryParams['token'])) {
                $extractedToken = strtoupper(trim($queryParams['token']));
            }
        }

        // Match strictly within voters role by attendance_token or email
        $voter = User::role('voters')
            ->where(function ($query) use ($extractedToken, $rawInput) {
                $query->where('attendance_token', $extractedToken)
                    ->orWhere('attendance_token', $rawInput)
                    ->orWhere('email', $rawInput);
            })
            ->first();

        if (! $voter) {
            return response()->json([
                'success' => false,
                'message' => 'Token / Kartu Pemilih tidak terdaftar dalam DPT.',
            ], 404);
        }

        $voterData = [
            'id'               => $voter->id,
            'name'             => $voter->name,
            'email'            => $voter->email,
            'attendance_token' => $voter->attendance_token,
            'has_voted'        => $voter->sudahVoting(),
        ];

        // Check if already checked in
        if ($voter->isCheckedIn()) {
            return response()->json([
                'success'            => true,
                'already_checked_in' => true,
                'voter'              => $voterData,
                'checked_in_at'      => $voter->checked_in_at->translatedFormat('H:i:s T — d F Y'),
                'message'            => "Pemilih {$voter->name} SUDAH melakukan presensi TPS sebelumnya.",
            ]);
        }

        // Mark as checked in
        $voter->checked_in_at = now();
        $voter->save();

        AuditLogger::log('presensi', "Presensi TPS berhasil untuk pemilih: {$voter->name} ({$voter->email})", [
            'attendance_token' => $voter->attendance_token,
        ]);

        return response()->json([
            'success'            => true,
            'already_checked_in' => false,
            'voter'              => $voterData,
            'checked_in_at'      => $voter->checked_in_at->translatedFormat('H:i:s T — d F Y'),
            'message'            => "Presensi BERHASIL! {$voter->name} dipersilakan menuju bilik suara.",
        ]);
    }

    /**
     * Manually toggle voter attendance status.
     */
    public function toggle(User $user): RedirectResponse
    {
        if ($user->isCheckedIn()) {
            $user->checked_in_at = null;
            $user->save();
            AuditLogger::log('presensi', "Membatalkan status presensi TPS pemilih: {$user->name}");
            $message = "Status presensi pemilih {$user->name} dibatalkan.";
        } else {
            $user->checked_in_at = now();
            $user->save();
            AuditLogger::log('presensi', "Menandai manual presensi TPS pemilih: {$user->name}");
            $message = "Presensi pemilih {$user->name} berhasil ditandai.";
        }

        return back()->with('success', $message);
    }
}

