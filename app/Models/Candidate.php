<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_urut',
        'nama_ketua',
        'nama_wakil',
        'visi',
        'misi',
        'foto_ketua',
        'foto_wakil',
        'aktif',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    /**
     * Get all votes for this candidate.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Scope a query to only include active candidates.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /**
     * Get the direct public URL for ketua photo without requiring storage:link.
     */
    public function getFotoKetuaUrlAttribute(): ?string
    {
        if (! $this->foto_ketua) {
            return null;
        }

        if (str_starts_with($this->foto_ketua, 'http')) {
            return $this->foto_ketua;
        }

        return asset($this->foto_ketua);
    }

    /**
     * Get the direct public URL for wakil photo without requiring storage:link.
     */
    public function getFotoWakilUrlAttribute(): ?string
    {
        if (! $this->foto_wakil) {
            return null;
        }

        if (str_starts_with($this->foto_wakil, 'http')) {
            return $this->foto_wakil;
        }

        return asset($this->foto_wakil);
    }
}
