<?php

namespace Tests\Feature;

use App\Support\Installer;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    public function test_installer_shows_already_installed_when_lockfile_exists(): void
    {
        $lockFile = storage_path('installed');
        if (! file_exists($lockFile)) {
            file_put_contents($lockFile, json_encode(['installed' => true]));
        }

        $response = $this->get(route('install.index'));
        $response->assertStatus(200);
        $response->assertSee('SatuSuara Sudah Terinstal');
    }

    public function test_installer_shows_requirements_step_when_not_installed(): void
    {
        $lockFile = storage_path('installed');
        $backup = file_exists($lockFile) ? file_get_contents($lockFile) : null;
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }

        try {
            $response = $this->get(route('install.index'));
            $response->assertStatus(200);
            $response->assertSee('Pemeriksaan Lingkungan Server');
            $response->assertSee('Versi PHP Server');
        } finally {
            if ($backup !== null) {
                file_put_contents($lockFile, $backup);
            }
        }
    }

    public function test_installer_database_step_renders(): void
    {
        $lockFile = storage_path('installed');
        $backup = file_exists($lockFile) ? file_get_contents($lockFile) : null;
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }

        try {
            $response = $this->get(route('install.database'));
            $response->assertStatus(200);
            $response->assertSee('Sambungan Database MySQL');
            $response->assertSee('Uji Koneksi');
        } finally {
            if ($backup !== null) {
                file_put_contents($lockFile, $backup);
            }
        }
    }

    public function test_installer_admin_step_renders(): void
    {
        $lockFile = storage_path('installed');
        $backup = file_exists($lockFile) ? file_get_contents($lockFile) : null;
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }

        try {
            $response = $this->get(route('install.admin'));
            $response->assertStatus(200);
            $response->assertSee('Informasi Instansi & Akun Administrator');
            $response->assertSee('Nama Lembaga / Instansi Penyelenggara');
        } finally {
            if ($backup !== null) {
                file_put_contents($lockFile, $backup);
            }
        }
    }

    public function test_installer_process_validates_required_fields(): void
    {
        $lockFile = storage_path('installed');
        $backup = file_exists($lockFile) ? file_get_contents($lockFile) : null;
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }

        try {
            $response = $this->post(route('install.process'), []);
            $response->assertSessionHasErrors([
                'db_host',
                'db_port',
                'db_name',
                'db_user',
                'institution_name',
                'election_name',
                'admin_name',
                'admin_email',
                'admin_password',
            ]);
        } finally {
            if ($backup !== null) {
                file_put_contents($lockFile, $backup);
            }
        }
    }
}

