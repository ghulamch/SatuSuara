<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\CheckInController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\Voter\VotingController;
use App\Support\Installer;
use Illuminate\Support\Facades\Route;

// Wizard Instalasi Awal (Web Setup Installer)
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::get('/database', [InstallController::class, 'database'])->name('database');
    Route::post('/test-database', [InstallController::class, 'testDatabase'])->name('test-database');
    Route::get('/admin', [InstallController::class, 'admin'])->name('admin');
    Route::post('/process', [InstallController::class, 'process'])->name('process');
    Route::get('/success', [InstallController::class, 'success'])->name('success');
});

/**
 * Helper to redirect authenticated user to their designated landing module.
 */
$redirectUserByRole = function ($user) {
    if ($user->hasRole('admin') || $user->can('view_dashboard')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->can('view_checkin')) {
        return redirect()->route('admin.checkin.index');
    }
    if ($user->can('view_reports')) {
        return redirect()->route('admin.reports.index');
    }
    if ($user->can('view_voters')) {
        return redirect()->route('admin.voters.index');
    }
    if ($user->can('view_candidates')) {
        return redirect()->route('admin.candidates.index');
    }
    if ($user->can('view_users')) {
        return redirect()->route('admin.users.index');
    }
    if ($user->can('view_settings')) {
        return redirect()->route('admin.settings.index');
    }
    if ($user->hasRole(\App\Enums\UserRole::Voters->value)) {
        return redirect()->route('voter.dashboard');
    }
    return redirect()->route('admin.dashboard');
};

Route::get('/', function () use ($redirectUserByRole) {
    if (! Installer::isInstalled()) {
        return redirect()->route('install.index');
    }
    if (auth()->check()) {
        return $redirectUserByRole(auth()->user());
    }
    return redirect()->route('login');
});

// Shortcut /admin routing based on user permissions
Route::get('/admin', function () use ($redirectUserByRole) {
    return $redirectUserByRole(auth()->user());
})->middleware('auth');

// Public Receipt Verification (Protected with Rate Limiting)
Route::get('/verifikasi-struk', [VotingController::class, 'verifyReceiptPage'])->name('verify-receipt');
Route::post('/verifikasi-struk', [VotingController::class, 'verifyReceiptCheck'])
    ->name('verify-receipt.check')
    ->middleware('throttle:30,1');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Voter Booth
Route::middleware(['auth', 'role:voters', 'single.session'])->prefix('voting')->name('voter.')->group(function () {
    Route::get('/', [VotingController::class, 'index'])->name('dashboard');
    Route::post('/vote', [VotingController::class, 'store'])->name('vote');
    Route::get('/queue-status', [VotingController::class, 'queueStatus'])->name('queue-status');
    Route::get('/server-time', [VotingController::class, 'serverTime'])->name('server-time');
});

// Admin Panel Modules (Protected by RBAC Granular Permissions)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Modul Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:view_dashboard');

    // Modul Pasangan Calon (Kandidat)
    Route::get('candidates', [CandidateController::class, 'index'])
        ->name('candidates.index')
        ->middleware('permission:view_candidates');
    Route::get('candidates/create', [CandidateController::class, 'create'])
        ->name('candidates.create')
        ->middleware('permission:create_candidates');
    Route::post('candidates', [CandidateController::class, 'store'])
        ->name('candidates.store')
        ->middleware('permission:create_candidates');
    Route::get('candidates/{candidate}/edit', [CandidateController::class, 'edit'])
        ->name('candidates.edit')
        ->middleware('permission:edit_candidates');
    Route::put('candidates/{candidate}', [CandidateController::class, 'update'])
        ->name('candidates.update')
        ->middleware('permission:edit_candidates');
    Route::delete('candidates/{candidate}', [CandidateController::class, 'destroy'])
        ->name('candidates.destroy')
        ->middleware('permission:delete_candidates');
    Route::get('candidates/{candidate}', [CandidateController::class, 'show'])
        ->name('candidates.show')
        ->middleware('permission:view_candidates');

    // Modul Data Pemilih (DPT)
    Route::get('voters', [VoterController::class, 'index'])
        ->name('voters.index')
        ->middleware('permission:view_voters');
    Route::get('voters/create', [VoterController::class, 'create'])
        ->name('voters.create')
        ->middleware('permission:create_voters');
    Route::post('voters', [VoterController::class, 'store'])
        ->name('voters.store')
        ->middleware('permission:create_voters');
    Route::get('voters/template', [VoterController::class, 'downloadTemplate'])
        ->name('voters.template')
        ->middleware('permission:import_voters');
    Route::post('voters/import', [VoterController::class, 'import'])
        ->name('voters.import')
        ->middleware('permission:import_voters');
    Route::post('voters/{voter}/reset-vote', [VoterController::class, 'resetVote'])
        ->name('voters.reset-vote')
        ->middleware('permission:reset_voters');
    Route::post('voters/{voter}/reset-session', [VoterController::class, 'resetSession'])
        ->name('voters.reset-session')
        ->middleware('permission:reset_voters');
    Route::delete('voters/{voter}', [VoterController::class, 'destroy'])
        ->name('voters.destroy')
        ->middleware('permission:delete_voters');

    // Modul Presensi TPS & Scanner QR
    Route::get('/checkin', [CheckInController::class, 'index'])
        ->name('checkin.index')
        ->middleware('permission:view_checkin');
    Route::post('/checkin/scan', [CheckInController::class, 'scan'])
        ->name('checkin.scan')
        ->middleware('permission:process_checkin');
    Route::post('/checkin/{user}/toggle', [CheckInController::class, 'toggle'])
        ->name('checkin.toggle')
        ->middleware('permission:process_checkin');

    // Modul Laporan & Berita Acara
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('permission:view_reports');
    Route::get('/reports/live', [ReportController::class, 'liveResults'])
        ->name('reports.live')
        ->middleware('permission:view_live_reports');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.pdf')
        ->middleware('permission:export_reports');

    // Modul Audit Trail Panitia
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('permission:view_audit_logs');

    // Modul Pengguna Staf & Peran Dinamis (RBAC)
    Route::get('users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('permission:view_users');
    Route::get('users/create', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware('permission:create_users');
    Route::post('users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('permission:create_users');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('permission:edit_users');
    Route::put('users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('permission:edit_users');
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('permission:delete_users');
    Route::get('users/{user}', [UserController::class, 'show'])
        ->name('users.show')
        ->middleware('permission:view_users');

    // Konfigurasi Matriks Hak Akses Peran (RBAC)
    Route::post('/roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('permission:manage_roles');
    Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])
        ->name('roles.permissions')
        ->middleware('permission:manage_roles');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
        ->name('roles.update-permissions')
        ->middleware('permission:manage_roles');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('permission:manage_roles');

    // Modul Pengaturan Sistem & Data Purge
    Route::get('/settings', [SettingController::class, 'index'])
        ->name('settings.index')
        ->middleware('permission:view_settings');
    Route::post('/settings', [SettingController::class, 'update'])
        ->name('settings.update')
        ->middleware('permission:edit_settings');
    Route::post('/settings/clear-voters', [SettingController::class, 'clearVoters'])
        ->name('settings.clear-voters')
        ->middleware('permission:purge_data');
    Route::post('/settings/clear-candidates', [SettingController::class, 'clearCandidates'])
        ->name('settings.clear-candidates')
        ->middleware('permission:purge_data');
    Route::post('/settings/clear-all', [SettingController::class, 'clearAll'])
        ->name('settings.clear-all')
        ->middleware('permission:purge_data');
});
