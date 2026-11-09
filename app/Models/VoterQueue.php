<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoterQueue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'queue_number',
        'status',
        'entered_booth_at',
        'last_heartbeat_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entered_booth_at'  => 'datetime',
            'last_heartbeat_at' => 'datetime',
        ];
    }

    /**
     * The user this queue record belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for waiting voters.
     */
    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope for voters currently active inside the booth.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}

