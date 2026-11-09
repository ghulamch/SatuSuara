<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class AuditLogger
{
    /**
     * Log an administrative action into the immutable audit trail.
     *
     * @param  string  $logName  Category (e.g. 'paslon', 'dpt', 'pengaturan', 'reset', 'laporan', 'presensi')
     * @param  string  $description  Human-readable description of what was done
     * @param  array<string, mixed>  $properties  Extra context data (IP, changes, etc.)
     */
    public static function log(string $logName, string $description, array $properties = []): ?Activity
    {
        try {
            $user = Auth::user();

            $mergedProperties = array_merge([
                'ip'         => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'System',
                'url'        => request()->fullUrl() ?? '',
            ], $properties);

            return activity($logName)
                ->causedBy($user)
                ->withProperties($mergedProperties)
                ->log($description);
        } catch (\Throwable $e) {
            // Fail safely without blocking critical workflows
            return null;
        }
    }
}

