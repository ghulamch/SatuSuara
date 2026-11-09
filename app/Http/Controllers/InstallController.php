<?php

namespace App\Http\Controllers;

use App\Support\Installer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstallController extends Controller
{
    /**
     * Step 1: Welcome & Requirements Inspection
     */
    public function index(): View
    {
        if (Installer::isInstalled()) {
            return view('install.already_installed');
        }

        $check = Installer::checkRequirements();

        return view('install.index', compact('check'));
    }

    /**
     * Step 2: Database Configuration View
     */
    public function database(): View|RedirectResponse
    {
        if (Installer::isInstalled()) {
            return redirect()->route('install.index');
        }

        $check = Installer::checkRequirements();
        if (! $check['allPassed']) {
            return redirect()->route('install.index')->with('error', 'Silakan penuhi semua prasyarat server terlebih dahulu.');
        }

        $currentDb = [
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'satusuara'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
        ];

        return view('install.database', compact('currentDb'));
    }

    /**
     * AJAX endpoint to test database connectivity
     */
    public function testDatabase(Request $request): JsonResponse
    {
        if (Installer::isInstalled()) {
            return response()->json(['success' => false, 'message' => 'Aplikasi sudah terinstal.'], 403);
        }

        $validated = $request->validate([
            'host'     => ['required', 'string'],
            'port'     => ['required', 'numeric'],
            'database' => ['required', 'string'],
            'username' => ['required', 'string'],
            'password' => ['nullable', 'string'],
        ]);

        $result = Installer::testDatabaseConnection($validated);

        return response()->json($result);
    }

    /**
     * Step 3: Institution & Superadmin Account View
     */
    public function admin(Request $request): View|RedirectResponse
    {
        if (Installer::isInstalled()) {
            return redirect()->route('install.index');
        }

        $dbConfig = $request->session()->get('install_db_config');

        // Allow continuing if session has valid DB config or direct test pass
        return view('install.admin', compact('dbConfig'));
    }

    /**
     * Step 4: Execute Installation Pipeline
     */
    public function process(Request $request): RedirectResponse
    {
        if (Installer::isInstalled()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            // Database
            'db_host'          => ['required', 'string'],
            'db_port'          => ['required', 'numeric'],
            'db_name'          => ['required', 'string'],
            'db_user'          => ['required', 'string'],
            'db_password'      => ['nullable', 'string'],

            // Institution
            'institution_name' => ['required', 'string', 'max:255'],
            'election_name'    => ['required', 'string', 'max:255'],

            // Superadmin
            'admin_name'       => ['required', 'string', 'max:255'],
            'admin_email'      => ['required', 'email', 'max:255'],
            'admin_password'   => ['required', 'string', 'min:8', 'confirmed'],
            'seed_demo_data'   => ['nullable', 'boolean'],
        ]);

        // Verify database connection first
        $dbCheck = Installer::testDatabaseConnection([
            'host'     => $validated['db_host'],
            'port'     => $validated['db_port'],
            'database' => $validated['db_name'],
            'username' => $validated['db_user'],
            'password' => $validated['db_password'] ?? '',
        ]);

        if (! $dbCheck['success']) {
            return back()->withInput()->with('error', $dbCheck['message']);
        }

        // Execute full setup
        $setup = Installer::executeSetup($validated);

        if (! $setup['success']) {
            return back()->withInput()->with('error', $setup['message'] ?? 'Instalasi gagal dieksekusi.');
        }

        // Store success metadata in flash session
        return redirect()->route('install.success')->with('install_summary', [
            'admin_email' => $validated['admin_email'],
            'institution' => $validated['institution_name'],
            'election'    => $validated['election_name'],
        ]);
    }

    /**
     * Step 5: Success screen
     */
    public function success(): View|RedirectResponse
    {
        $summary = session('install_summary');

        return view('install.success', compact('summary'));
    }
}

