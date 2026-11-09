<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PDO;
use Throwable;

class Installer
{
    /**
     * Check if the application is already installed.
     */
    public static function isInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }

    /**
     * Mark the application as installed.
     */
    public static function markAsInstalled(): void
    {
        $info = [
            'installed_at' => now()->toIso8601String(),
            'app_version'  => '1.0.0',
            'environment'  => app()->environment(),
        ];

        file_put_contents(storage_path('installed'), json_encode($info, JSON_PRETTY_PRINT));
    }

    /**
     * Cross-platform check if a directory is writable (handles Windows NTFS ACLs).
     */
    public static function isPathWritable(string $path): bool
    {
        if (is_writable($path)) {
            return true;
        }

        $testFile = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . '.writable_check_' . uniqid();
        $test = @file_put_contents($testFile, 'test');
        if ($test !== false) {
            @unlink($testFile);
            return true;
        }

        return false;
    }

    /**
     * Inspect system PHP and directory requirements.
     *
     * @return array<string, mixed>
     */
    public static function checkRequirements(): array
    {
        $phpVersion = PHP_VERSION;
        $phpPassed = version_compare($phpVersion, '8.3.0', '>=');

        $requiredExtensions = [
            'pdo'       => 'Ekstensi PHP Data Objects (PDO)',
            'pdo_mysql' => 'Driver Database MySQL/MariaDB (PDO_MySQL)',
            'mbstring'  => 'Multibyte String Support',
            'openssl'   => 'OpenSSL Cryptographic Library',
            'xml'       => 'XML & DOM Parser',
            'fileinfo'  => 'File Information (MIME Detection)',
            'gd'        => 'GD Graphics Library (Manipulasi Gambar)',
            'zip'       => 'Zip Archive (Impor/Ekspor Excel)',
        ];

        $extensionResults = [];
        $extensionsPassed = true;

        foreach ($requiredExtensions as $ext => $label) {
            $loaded = extension_loaded($ext);
            $extensionResults[$ext] = [
                'name'   => $ext,
                'label'  => $label,
                'passed' => $loaded,
            ];
            if (! $loaded) {
                $extensionsPassed = false;
            }
        }

        $directories = [
            'storage/app'                => storage_path('app'),
            'storage/framework/cache'    => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/views'    => storage_path('framework/views'),
            'storage/logs'               => storage_path('logs'),
            'bootstrap/cache'            => base_path('bootstrap/cache'),
            'public/uploads'             => public_path('uploads'),
        ];

        $directoryResults = [];
        $directoriesPassed = true;

        foreach ($directories as $label => $path) {
            if (! file_exists($path)) {
                @mkdir($path, 0755, true);
            }
            $writable = self::isPathWritable($path);
            $directoryResults[$label] = [
                'path'     => $label,
                'writable' => $writable,
            ];
            if (! $writable) {
                $directoriesPassed = false;
            }
        }

        $allPassed = $phpPassed && $extensionsPassed && $directoriesPassed;

        return [
            'php'         => [
                'current'  => $phpVersion,
                'required' => '8.3.0',
                'passed'   => $phpPassed,
            ],
            'extensions'  => $extensionResults,
            'directories' => $directoryResults,
            'allPassed'   => $allPassed,
        ];
    }

    /**
     * Test connection to MySQL/MariaDB using raw PDO.
     *
     * @param  array<string, mixed>  $config
     * @return array{success: bool, message: string}
     */
    public static function testDatabaseConnection(array $config): array
    {
        $host     = $config['host'] ?? '127.0.0.1';
        $port     = $config['port'] ?? '3306';
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';

        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT            => 4,
            ]);

            // Ensure database exists or create it if user has privileges
            if (! empty($database)) {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }

            return [
                'success' => true,
                'message' => 'Koneksi ke database server MySQL berhasil diverifikasi.',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke database: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update environment file (.env).
     *
     * @param  array<string, string>  $values
     */
    public static function updateEnvironmentFile(array $values): void
    {
        $envPath = base_path('.env');
        $examplePath = base_path('.env.example');

        if (! file_exists($envPath) && file_exists($examplePath)) {
            copy($examplePath, $envPath);
        }

        $content = file_exists($envPath) ? file_get_contents($envPath) : '';
        $original = $content;

        foreach ($values as $key => $value) {
            $escapedValue = (str_contains($value, ' ') || str_contains($value, '#')) ? "\"{$value}\"" : $value;
            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$escapedValue}", $content);
            } else {
                $content .= "\n{$key}={$escapedValue}";
            }
        }

        if ($content !== $original) {
            file_put_contents($envPath, $content);
        }
    }

    /**
     * Execute full database setup, migrations, and superadmin generation.
     *
     * @param  array<string, mixed>  $data
     * @return array{success: bool, message?: string}
     */
    public static function executeSetup(array $data): array
    {
        try {
            // 1. Update runtime database connection configuration
            config([
                'database.connections.mysql.host'     => $data['db_host'],
                'database.connections.mysql.port'     => $data['db_port'],
                'database.connections.mysql.database' => $data['db_name'],
                'database.connections.mysql.username' => $data['db_user'],
                'database.connections.mysql.password' => $data['db_password'] ?? '',
            ]);

            DB::purge('mysql');
            DB::reconnect('mysql');

            // 2. Generate APP_KEY if missing
            if (empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // 3. Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // 4. Seed Roles & Permissions
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder', '--force' => true]);
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\PermissionSeeder', '--force' => true]);

            // 5. Create or Update Superadmin Account
            $admin = User::updateOrCreate(
                ['email' => $data['admin_email']],
                [
                    'name'              => $data['admin_name'],
                    'password'          => Hash::make($data['admin_password']),
                    'email_verified_at' => now(),
                ]
            );
            $admin->syncRoles([UserRole::Admin->value]);

            // 6. Save initial institution settings
            $settingsPath = storage_path('app/settings.json');
            $existingSettings = file_exists($settingsPath) ? (json_decode(file_get_contents($settingsPath), true) ?: []) : [];

            $newSettings = array_merge($existingSettings, [
                'institution'           => $data['institution_name'],
                'election_name'         => $data['election_name'],
                'status_pelaksanaan'    => 'buka',
                'enable_queue'          => true,
                'max_concurrent_voters' => 10,
                'booth_session_minutes' => 3,
                'timezone'              => 'Asia/Jakarta',
                'locale'                => 'id',
                'logo_instansi'         => null,
                'kiosk_mode'            => false,
                'kiosk_timeout_seconds' => 5,
                'enable_kotak_kosong'   => false,
                'enable_single_session' => true,
            ]);

            file_put_contents($settingsPath, json_encode($newSettings, JSON_PRETTY_PRINT));

            // 8. Mark application as installed FIRST before persisting .env
            self::markAsInstalled();

            // 9. Persist to .env (only updates changed keys)
            self::updateEnvironmentFile([
                'APP_NAME'     => $data['institution_name'] ?: 'SatuSuara',
                'APP_URL'      => url('/'),
                'DB_CONNECTION'=> 'mysql',
                'DB_HOST'      => $data['db_host'],
                'DB_PORT'      => (string) $data['db_port'],
                'DB_DATABASE'  => $data['db_name'],
                'DB_USERNAME'  => $data['db_user'],
                'DB_PASSWORD'  => (string) ($data['db_password'] ?? ''),
            ]);

            return ['success' => true];
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Installer failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengeksekusi instalasi: ' . $e->getMessage(),
            ];
        }
    }
}

