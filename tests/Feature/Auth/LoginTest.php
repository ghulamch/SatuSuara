<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun');
    }

    public function test_admin_user_redirected_to_admin_dashboard_after_login(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        $response = $this->post('/login', [
            'email' => 'admin@satusuara.id',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_voter_user_redirected_to_voter_dashboard_after_login(): void
    {
        $voter = User::create([
            'name' => 'Voter User',
            'email' => 'voter@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        $response = $this->post('/login', [
            'email' => 'voter@satusuara.id',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($voter);
        $response->assertRedirect(route('voter.dashboard'));
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::create([
            'name' => 'User Test',
            'email' => 'test@satusuara.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@satusuara.id',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_voter_cannot_access_admin_dashboard(): void
    {
        $voter = User::create([
            'name' => 'Voter User',
            'email' => 'voter@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        $response = $this->actingAs($voter)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_user_can_logout(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@satusuara.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($admin)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_visiting_login_redirected_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_admin_visiting_root_redirected_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole(UserRole::Admin->value);

        $response = $this->actingAs($admin)->get('/');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_voter_visiting_root_redirected_to_voter_dashboard(): void
    {
        $voter = User::create([
            'name' => 'Voter User',
            'email' => 'voter@satusuara.id',
            'password' => Hash::make('password123'),
        ]);
        $voter->assignRole(UserRole::Voters->value);

        $response = $this->actingAs($voter)->get('/');

        $response->assertRedirect(route('voter.dashboard'));
    }
}

