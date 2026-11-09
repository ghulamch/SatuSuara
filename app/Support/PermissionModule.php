<?php

namespace App\Support;

class PermissionModule
{
    /**
     * Get all modules and their granular permissions.
     *
     * @return array<string, array{name: string, icon: string, description: string, permissions: array<string, string>}>
     */
    public static function all(): array
    {
        return [
            'dashboard' => [
                'name'        => 'Dashboard & Analitik',
                'description' => 'Halaman utama ringkasan statistik perolehan suara & progres pemilihan',
                'icon'        => 'grid',
                'permissions' => [
                    'view_dashboard' => 'Melihat ringkasan statistik pemilihan & grafik quick count',
                ],
            ],
            'candidates' => [
                'name'        => 'Pasangan Calon (Kandidat)',
                'description' => 'Manajemen data calon ketua & wakil pemilihan',
                'icon'        => 'users',
                'permissions' => [
                    'view_candidates'   => 'Melihat daftar pasangan calon',
                    'create_candidates' => 'Mendaftarkan pasangan calon baru',
                    'edit_candidates'   => 'Mengubah informasi, visi, misi, dan foto kandidat',
                    'delete_candidates' => 'Menghapus pasangan calon & transaksi suara terkait',
                ],
            ],
            'voters' => [
                'name'        => 'Data Pemilih (DPT)',
                'description' => 'Daftar pemilih tetap, impor Excel, reset hak pilih, dan token presensi',
                'icon'        => 'user-check',
                'permissions' => [
                    'view_voters'   => 'Melihat daftar seluruh pemilih DPT',
                    'create_voters' => 'Menambah akun pemilih manual',
                    'import_voters' => 'Mengimpor berkas Excel DPT & mengunduh template resmi',
                    'reset_voters'  => 'Mereset status suara pemilih (kembalikan hak suara)',
                    'delete_voters' => 'Menghapus akun pemilih dari DPT',
                ],
            ],
            'checkin' => [
                'name'        => 'Presensi TPS & Pemindai QR',
                'description' => 'Operasional meja registrasi TPS fisik dan scanner kartu pemilih',
                'icon'        => 'maximize',
                'permissions' => [
                    'view_checkin'    => 'Mengakses halaman presensi TPS & kamera scanner',
                    'process_checkin' => 'Memindai QR kartu atau menandai kehadiran manual',
                ],
            ],
            'reports' => [
                'name'        => 'Laporan & Berita Acara',
                'description' => 'Rekapitulasi resmi, layar live proyektor, dan ekspor dokumen PDF bertanda tangan',
                'icon'        => 'bar-chart-2',
                'permissions' => [
                    'view_reports'      => 'Melihat rekapitulasi perolehan suara & diagram hasil',
                    'view_live_reports' => 'Membuka layar Live Quick Count Fullscreen proyektor',
                    'export_reports'    => 'Mengunduh & mencetak dokumen resmi PDF Berita Acara',
                ],
            ],
            'audit_logs' => [
                'name'        => 'Audit Trail Panitia',
                'description' => 'Pencatatan riwayat aktivitas seluruh aksi admin dan panitia',
                'icon'        => 'shield',
                'permissions' => [
                    'view_audit_logs' => 'Melihat catatan log aktivitas, timestamp, dan alamat IP',
                ],
            ],
            'users' => [
                'name'        => 'Pengguna Staf & Peran (RBAC)',
                'description' => 'Manajemen staf internal dan konfigurasi matriks hak akses peran dinamis',
                'icon'        => 'user-plus',
                'permissions' => [
                    'view_users'   => 'Melihat daftar akun staf & panitia',
                    'create_users' => 'Menambahkan akun staf baru',
                    'edit_users'   => 'Mengubah profil, email, peran, dan password staf',
                    'delete_users' => 'Menghapus akun staf dari sistem',
                    'manage_roles' => 'Mengatur peran dinamis & matriks hak akses izin (RBAC)',
                ],
            ],
            'settings' => [
                'name'        => 'Pengaturan Sistem & Data Purge',
                'description' => 'Konfigurasi jadwal, logo instansi, zona waktu, dan opsi pengosongan data',
                'icon'        => 'settings',
                'permissions' => [
                    'view_settings' => 'Melihat konfigurasi umum, jadwal, dan status bilik',
                    'edit_settings' => 'Mengubah nama instansi, logo, jadwal, timezone, dan mode kiosk',
                    'purge_data'    => 'Mereset data massal (kosongkan suara/pemilih/kandidat)',
                ],
            ],
        ];
    }
}

