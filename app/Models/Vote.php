<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'candidate_id',
        'is_kotak_kosong',
        'receipt_hash',
        'voted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_kotak_kosong' => 'boolean',
            'voted_at'        => 'datetime',
        ];
    }

    /**
     * Generate a cryptographic receipt hash/code.
     */
    public static function generateReceiptCode(int $userId): string
    {
        $rawSeed = $userId . '-' . hrtime(true) . '-' . bin2hex(random_bytes(16));
        $hash = strtoupper(hash('sha256', $rawSeed));

        // Format into human-readable segmented cryptographic receipt: VOTE-8F2A-99B1-XXXX
        return 'VOTE-' . substr($hash, 0, 4) . '-' . substr($hash, 4, 4) . '-' . substr($hash, 8, 4);
    }

    /**
     * Indicates if the model should be timestamped.
     * We use voted_at instead of created_at/updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the user who cast this vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the candidate this vote was cast for.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
