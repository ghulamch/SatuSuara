<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VotersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * Number of successfully imported voters.
     */
    public int $importedCount = 0;

    /**
     * Number of skipped rows.
     */
    public int $skippedCount = 0;

    /**
     * List of warnings or errors during import.
     *
     * @var array<string>
     */
    public array $messages = [];

    /**
     * Cache for emails already seen in this file to prevent duplicate processing.
     *
     * @var array<string, bool>
     */
    protected array $seenEmails = [];

    /**
     * Process the imported collection of rows.
     *
     * @param  Collection<int, array<string, mixed>>  $rows
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // header is row 1

            // Support flexible column headers
            $name = trim((string) ($row['nama'] ?? $row['name'] ?? $row['nama_lengkap'] ?? $row['nama_pemilih'] ?? ''));
            $email = strtolower(trim((string) ($row['email'] ?? $row['surel'] ?? $row['alamat_email'] ?? '')));
            $password = trim((string) ($row['password'] ?? $row['kata_sandi'] ?? $row['pass'] ?? 'password123'));

            if ($password === '') {
                $password = 'password123';
            }

            // Skip empty rows
            if ($name === '' && $email === '') {
                $this->skippedCount++;
                continue;
            }

            if ($name === '') {
                $this->messages[] = "Baris {$rowNum}: Kolom nama kosong (dilewati).";
                $this->skippedCount++;
                continue;
            }

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->messages[] = "Baris {$rowNum}: Email '{$email}' tidak valid (dilewati).";
                $this->skippedCount++;
                continue;
            }

            // Check duplicate in current file
            if (isset($this->seenEmails[$email])) {
                $this->messages[] = "Baris {$rowNum}: Email '{$email}' terduplikasi di file ini (dilewati).";
                $this->skippedCount++;
                continue;
            }
            $this->seenEmails[$email] = true;

            // Check duplicate in database
            if (User::where('email', $email)->exists()) {
                $this->messages[] = "Baris {$rowNum}: Email '{$email}' sudah terdaftar di sistem (dilewati).";
                $this->skippedCount++;
                continue;
            }

            // Create new voter
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($password),
            ]);

            $user->assignRole('voters');
            $this->importedCount++;
        }
    }
}

