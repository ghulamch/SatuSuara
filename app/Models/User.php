<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'attendance_token',
    'checked_in_at',
    'current_session_id',
    'session_last_active_at',
    'session_ip',
    'session_user_agent',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Boot the model to auto-generate attendance tokens for voters.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->attendance_token)) {
                $user->attendance_token = 'SS-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 4)) . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 4));
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'checked_in_at'          => 'datetime',
            'session_last_active_at' => 'datetime',
            'password'               => 'hashed',
        ];
    }

    /**
     * Check whether this user has checked in at the polling station (TPS).
     */
    public function isCheckedIn(): bool
    {
        return ! is_null($this->checked_in_at);
    }

    /**
     * Get the vote associated with this user.
     */
    public function vote(): HasOne
    {
        return $this->hasOne(Vote::class);
    }

    /**
     * Check whether this user has already voted.
     */
    public function sudahVoting(): bool
    {
        return $this->vote()->exists();
    }

    /**
     * Check if user currently has an active, non-stale session on another device.
     */
    public function hasActiveSession(?string $exceptSessionId = null): bool
    {
        if (empty($this->current_session_id)) {
            return false;
        }

        // Same session
        if ($exceptSessionId && $this->current_session_id === $exceptSessionId) {
            return false;
        }

        // If sessions are stored in the database, check if the session row still exists
        if (config('session.driver') === 'database') {
            $lifetimeSeconds = config('session.lifetime', 120) * 60;
            $sessionExists = DB::table('sessions')
                ->where('id', $this->current_session_id)
                ->where('last_activity', '>=', now()->subSeconds($lifetimeSeconds)->timestamp)
                ->exists();

            if (! $sessionExists) {
                // Session is expired or terminated, auto-clean user lock
                $this->unlockSession();
                return false;
            }
        } elseif ($this->session_last_active_at) {
            $lifetimeMinutes = (int) config('session.lifetime', 120);
            if (now()->diffInMinutes($this->session_last_active_at) > $lifetimeMinutes) {
                $this->unlockSession();
                return false;
            }
        }

        return true;
    }

    /**
     * Lock voter session to a specific device/browser session.
     */
    public function lockSession(string $sessionId, ?string $ip = null, ?string $userAgent = null): void
    {
        $this->updateQuietly([
            'current_session_id'     => $sessionId,
            'session_last_active_at' => now(),
            'session_ip'             => $ip,
            'session_user_agent'     => $userAgent ? substr($userAgent, 0, 500) : null,
        ]);
    }

    /**
     * Unlock / reset active session.
     */
    public function unlockSession(): void
    {
        // Also remove row from sessions table if database driver is used
        if ($this->current_session_id && config('session.driver') === 'database') {
            try {
                DB::table('sessions')->where('id', $this->current_session_id)->delete();
            } catch (\Throwable $e) {
                // Ignore if DB connection fails
            }
        }

        $this->updateQuietly([
            'current_session_id'     => null,
            'session_last_active_at' => null,
            'session_ip'             => null,
            'session_user_agent'     => null,
        ]);
    }
}
