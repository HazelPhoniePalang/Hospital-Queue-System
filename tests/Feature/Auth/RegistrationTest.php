<?php

namespace Tests\Feature\Auth;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_admin_can_be_registered(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'description' => 'System Administrator',
        ]);

        $response = $this->post('/register', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $adminRole->id,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'role_id' => $adminRole->id,
            'department_id' => null,
        ]);
        $response->assertRedirect(route('login', absolute: false));
    }

    private function adminUser(): User
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'description' => 'System Administrator',
        ]);

        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);
    }
}
