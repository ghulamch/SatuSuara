<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SingleSessionLockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_voter_first_login_sets_current_session_id(): void
    {
        $voter = User::create([
            'name'     => 'Pemilih Uji Coba',
            'email'    => 'pemilih@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        $response = $this->post('/login', [
            'email'    => 'pemilih@satusuara.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/voting');
        $voter->refresh();

        $this->assertNotEmpty($voter->current_session_id);
        $this->assertNotNull($voter->session_last_active_at);
    }

    public function test_concurrent_login_on_second_device_is_blocked(): void
    {
        $voter = User::create([
            'name'     => 'Pemilih Uji Coba 2',
            'email'    => 'pemilih2@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        // Simulate an active session in the database for Device 1
        $activeSessionId = 'active_session_device_1_' . bin2hex(random_bytes(8));
        DB::table('sessions')->insert([
            'id'            => $activeSessionId,
            'user_id'       => $voter->id,
            'ip_address'    => '192.168.1.101',
            'user_agent'    => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)',
            'payload'       => base64_encode(serialize(['test' => true])),
            'last_activity' => now()->timestamp,
        ]);

        $voter->updateQuietly([
            'current_session_id'     => $activeSessionId,
            'session_last_active_at' => now(),
            'session_ip'             => '192.168.1.101',
        ]);

        // Attempt login from Device 2
        $response = $this->post('/login', [
            'email'    => 'pemilih2@satusuara.id',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Device 1 session lock remains intact
        $voter->refresh();
        $this->assertEquals($activeSessionId, $voter->current_session_id);
    }

    public function test_voter_logout_releases_session_lock(): void
    {
        $voter = User::create([
            'name'     => 'Pemilih Uji Coba 3',
            'email'    => 'pemilih3@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        $this->actingAs($voter);
        $voter->lockSession(session()->getId(), '127.0.0.1');

        $this->assertNotEmpty($voter->fresh()->current_session_id);

        $response = $this->post('/logout');
        $response->assertRedirect('/login');

        $this->assertNull($voter->fresh()->current_session_id);
    }

    public function test_admin_can_reset_voter_session(): void
    {
        $admin = User::create([
            'name'     => 'Admin Panitia',
            'email'    => 'admin@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        $voter = User::create([
            'name'     => 'Pemilih Terkunci',
            'email'    => 'terkunci@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        $voter->lockSession('stuck_session_999', '10.0.0.1');
        $this->assertEquals('stuck_session_999', $voter->fresh()->current_session_id);

        $response = $this->actingAs($admin)->post(route('admin.voters.reset-session', $voter->id));

        $response->assertRedirect(route('admin.voters.index'));
        $this->assertNull($voter->fresh()->current_session_id);
    }
}
