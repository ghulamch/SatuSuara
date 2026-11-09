<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the authentication view.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey, 60);

            activity('auth')
                ->withProperties([
                    'email' => $request->input('email'),
                    'ip' => $request->ip(),
                ])
                ->log('Percobaan login gagal');

            return back()->withErrors([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = Auth::user();

        // Single-Session Locking check for voter accounts (Anti Akun Dipakai Bersamaan)
        if ($user->hasRole(UserRole::Voters->value) && config('election.enable_single_session', true) && ! $user->sudahVoting()) {
            if ($user->hasActiveSession($request->session()->getId())) {
                // Voter account is currently locked to another active device/booth
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                activity('auth')
                    ->causedBy($user)
                    ->withProperties([
                        'email'              => $request->input('email'),
                        'ip'                 => $request->ip(),
                        'active_session_ip'  => $user->session_ip,
                        'session_last_active'=> $user->session_last_active_at?->toIso8601String(),
                    ])
                    ->log('Percobaan login ditolak: Akun pemilih sedang aktif di perangkat / bilik lain');

                return back()->withErrors([
                    'email' => '🔒 Akun pemilih ini sedang aktif digunakan di perangkat / bilik lain. Sesi dikunci untuk 1 perangkat demi menjaga asas 1 orang 1 suara secara ketat. Silakan selesaikan pencoblosan di perangkat awal, atau lapor ke Panitia TPS untuk reset sesi jika perangkat sebelumnya berkendala.',
                ])->onlyInput('email');
            }

            // Lock this session to the current device
            $user->lockSession($request->session()->getId(), $request->ip(), $request->userAgent());
        }

        activity('auth')
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip()])
            ->log('Pengguna berhasil masuk ke sistem');

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole(UserRole::Voters->value)) {
                $user->unlockSession();
            }

            activity('auth')
                ->causedBy($user)
                ->withProperties(['ip' => $request->ip()])
                ->log('Pengguna keluar dari sistem');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Redirect the user according to their assigned role.
     */
    protected function redirectBasedOnRole($user): RedirectResponse
    {
        if ($user->hasRole(UserRole::Admin->value) || $user->can('view_dashboard')) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('status', 'Selamat datang kembali, ' . $user->name . '!');
        }

        if ($user->can('view_checkin')) {
            return redirect()->intended(route('admin.checkin.index'))
                ->with('status', 'Selamat datang di Meja Presensi TPS, ' . $user->name . '!');
        }

        if ($user->hasRole(UserRole::Voters->value)) {
            return redirect()->intended(route('voter.dashboard'))
                ->with('status', 'Selamat datang di bilik suara digital SatuSuara.');
        }

        if ($user->can('view_reports')) {
            return redirect()->intended(route('admin.reports.index'))
                ->with('status', 'Selamat datang di panel Laporan.');
        }

        if ($user->can('view_voters')) {
            return redirect()->intended(route('admin.voters.index'))
                ->with('status', 'Selamat datang di panel Data Pemilih.');
        }

        if ($user->can('view_candidates')) {
            return redirect()->intended(route('admin.candidates.index'))
                ->with('status', 'Selamat datang di panel Pasangan Calon.');
        }

        if ($user->can('view_users')) {
            return redirect()->intended(route('admin.users.index'))
                ->with('status', 'Selamat datang di panel Manajemen Pengguna.');
        }

        if ($user->can('view_settings')) {
            return redirect()->intended(route('admin.settings.index'))
                ->with('status', 'Selamat datang di panel Pengaturan.');
        }

        Auth::logout();

        return redirect()->route('login')
            ->withErrors(['email' => 'Akun Anda belum memiliki peran (role) aktif untuk mengakses sistem.']);
    }
}

