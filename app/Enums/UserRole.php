<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case PetugasPresensi = 'petugas_presensi';
    case Voters = 'voters';

    /**
     * Get all role values as an array.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable Indonesian label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator (Kendali Penuh)',
            self::PetugasPresensi => 'Petugas Presensi TPS (Kamera Scanner & Presensi Meja TPS)',
            self::Voters => 'Pemilih Terdaftar (Bilik Suara)',
        };
    }

    /**
     * Resolve label from string or enum case, with fallback for custom dynamic roles.
     */
    public static function getLabel(string $role): string
    {
        $case = self::tryFrom($role);
        if ($case !== null) {
            return $case->label();
        }

        return ucfirst(str_replace('_', ' ', $role)) . ' (Peran Dinamis)';
    }
}

