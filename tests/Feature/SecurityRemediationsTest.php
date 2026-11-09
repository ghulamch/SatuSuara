<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Vote;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityRemediationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /**
     * SS-CONF-001: Secret Ballot Preservation
     */
    public function test_voter_ballot_choice_is_not_exposed_in_dpt_table(): void
    {
        $admin = User::create([
            'name'     => 'Panitia Admin',
            'email'    => 'admin@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        $candidate = Candidate::create([
            'nomor_urut' => 1,
            'nama_ketua' => 'Calon Rahasia Sangat Penting',
            'aktif'      => true,
        ]);

        $voter = User::create([
            'name'     => 'Pemilih Rahasia',
            'email'    => 'pemilih.rahasia@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        // Record vote
        Vote::create([
            'user_id'      => $voter->id,
            'candidate_id' => $candidate->id,
            'receipt_hash' => Vote::generateReceiptCode($voter->id),
            'voted_at'     => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.voters.index'));

        $response->assertStatus(200);
        $response->assertSee('Pemilih Rahasia');
        $response->assertSee('Sudah Memilih');

        // Secret ballot assertion: Candidate name MUST NOT appear in the voter table row!
        $response->assertDontSee('Paslon #1 (Calon Rahasia Sangat Penting)');
        $response->assertDontSee('Audit Pilihan');
    }

    /**
     * SS-CONF-002: Stored XSS & XXE SVG Sanitization
     */
    public function test_malicious_svg_with_xxe_is_rejected(): void
    {
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        // XXE payload
        $maliciousSvg = '<!DOCTYPE svg [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><svg>&xxe;</svg>';
        $file = UploadedFile::fake()->createWithContent('logo.svg', $maliciousSvg);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'logo_instansi' => $file,
        ]);

        $response->assertSessionHasErrors('logo_instansi');
    }

    public function test_malicious_svg_with_script_is_sanitized_cleanly(): void
    {
        $settingsFile = storage_path('app/settings.json');
        $originalSettings = file_exists($settingsFile) ? file_get_contents($settingsFile) : null;

        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        try {
            // XSS script payload inside SVG
            $xssSvg = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert("XSS")</script><circle cx="50" cy="50" r="40" onload="alert(1)"/></svg>';
            $file = UploadedFile::fake()->createWithContent('logo.svg', $xssSvg);

            $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
                'logo_instansi' => $file,
            ]);

            $response->assertRedirect(route('admin.settings.index'));

            // Read the saved file from settings
            $settings = json_decode(file_get_contents($settingsFile), true);
            $savedPath = public_path($settings['logo_instansi']);

            $this->assertFileExists($savedPath);
            $savedContent = file_get_contents($savedPath);

            // Verify that dangerous tags and event handlers were stripped
            $this->assertStringNotContainsString('<script', $savedContent);
            $this->assertStringNotContainsString('alert', $savedContent);
            $this->assertStringNotContainsString('onload', $savedContent);
            $this->assertStringContainsString('<circle', $savedContent);

            // Clean up the temporary test file
            @unlink($savedPath);
        } finally {
            // Restore original settings so tests don't corrupt real settings
            if ($originalSettings !== null) {
                file_put_contents($settingsFile, $originalSettings);
            }
        }
    }

    /**
     * SS-CONF-003: Vertical Privilege Escalation Prevention
     */
    public function test_non_admin_cannot_promote_account_to_admin(): void
    {
        // Create custom staff role with edit_users permission
        $staffRole = Role::firstOrCreate(['name' => 'petugas_dpt', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'edit_users', 'guard_name' => 'web']);
        $staffRole->syncPermissions([$permission]);

        $staffUser = User::create([
            'name'     => 'Staf Biasa',
            'email'    => 'staf@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $staffUser->assignRole('petugas_dpt');

        $targetUser = User::create([
            'name'     => 'User Target',
            'email'    => 'target@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $targetUser->assignRole('petugas_dpt');

        // Staff tries to escalate targetUser to admin
        $response = $this->actingAs($staffUser)->put(route('admin.users.update', $targetUser), [
            'name'  => 'User Target',
            'email' => 'target@satusuara.id',
            'role'  => 'admin',
        ]);

        $response->assertSessionHas('error');
        $targetUser->refresh();
        $this->assertFalse($targetUser->hasRole('admin'));
    }

    public function test_non_admin_cannot_modify_admin_account(): void
    {
        $staffRole = Role::firstOrCreate(['name' => 'petugas_dpt', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'edit_users', 'guard_name' => 'web']);
        $staffRole->syncPermissions([$permission]);

        $staffUser = User::create([
            'name'     => 'Staf Biasa',
            'email'    => 'staf@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $staffUser->assignRole('petugas_dpt');

        $adminUser = User::create([
            'name'     => 'Admin Utama',
            'email'    => 'admin.utama@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $adminUser->assignRole(UserRole::Admin->value);

        // Staff tries to edit Admin Utama
        $response = $this->actingAs($staffUser)->put(route('admin.users.update', $adminUser), [
            'name'  => 'Hacked Admin',
            'email' => 'admin.utama@satusuara.id',
            'role'  => 'petugas_dpt',
        ]);

        $response->assertSessionHas('error');
        $adminUser->refresh();
        $this->assertEquals('Admin Utama', $adminUser->name);
        $this->assertTrue($adminUser->hasRole('admin'));
    }

    /**
     * SS-CONF-004: Scanner Presensi TPS Role Filter Integrity
     */
    public function test_checkin_scan_does_not_match_non_voter_accounts(): void
    {
        $petugas = User::create([
            'name'     => 'Petugas TPS',
            'email'    => 'petugas@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $petugas->assignRole('admin');

        $admin = User::create([
            'name'     => 'Akun Admin',
            'email'    => 'admin.tps@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Attempt scan using admin's email or ID
        $response = $this->actingAs($petugas)->postJson(route('admin.checkin.scan'), [
            'token' => 'admin.tps@satusuara.id',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Token / Kartu Pemilih tidak terdaftar dalam DPT.',
        ]);
        $this->assertNull($admin->fresh()->checked_in_at);

        // Valid voter scan succeeds
        $voter = User::create([
            'name'             => 'Pemilih Sah',
            'email'            => 'pemilih.sah@satusuara.id',
            'password'         => Hash::make('password123'),
            'attendance_token' => 'SS-TEST-VOTE',
        ]);
        $voter->assignRole(UserRole::Voters->value);

        $validResponse = $this->actingAs($petugas)->postJson(route('admin.checkin.scan'), [
            'token' => 'SS-TEST-VOTE',
        ]);

        $validResponse->assertStatus(200);
        $validResponse->assertJson(['success' => true]);
        $this->assertNotNull($voter->fresh()->checked_in_at);
    }

    /**
     * SS-CONF-005: Data Purge Password Confirmation
     */
    public function test_data_purge_requires_valid_admin_password(): void
    {
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@satusuara.id',
            'password' => Hash::make('secret_admin_pass'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        $voter = User::create([
            'name'     => 'Voter Data',
            'email'    => 'voter.data@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        // 1. Without password -> validation error
        $responseNoPass = $this->actingAs($admin)->post(route('admin.settings.clear-voters'), []);
        $responseNoPass->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $voter->id]);

        // 2. With wrong password -> validation error
        $responseWrongPass = $this->actingAs($admin)->post(route('admin.settings.clear-voters'), [
            'password' => 'wrong_password',
        ]);
        $responseWrongPass->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $voter->id]);

        // 3. With correct password -> success
        $responseValid = $this->actingAs($admin)->post(route('admin.settings.clear-voters'), [
            'password' => 'secret_admin_pass',
        ]);
        $responseValid->assertRedirect(route('admin.settings.index'));
        $this->assertDatabaseMissing('users', ['id' => $voter->id]);
    }

    /**
     * SS-CONF-006: Public Receipt Verification Rate Limiting
     */
    public function test_public_receipt_verification_is_rate_limited(): void
    {
        // Route is throttled at 30 requests per minute
        for ($i = 0; $i < 30; $i++) {
            $response = $this->post(route('verify-receipt.check'), [
                'receipt_hash' => 'VOTE-TEST-0000-0000',
            ]);
            $response->assertStatus(200);
        }

        // 31st request triggers 429 Too Many Requests
        $overLimitResponse = $this->post(route('verify-receipt.check'), [
            'receipt_hash' => 'VOTE-TEST-0000-0000',
        ]);
        $overLimitResponse->assertStatus(429);
    }
}

