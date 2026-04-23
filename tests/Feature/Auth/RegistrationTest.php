<?php

namespace Tests\Feature\Auth;

use App\Models\Department;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $role = Role::create([
            'name' => 'Hospital Staff',
            'description' => 'Hospital Staff',
        ]);

        $department = Department::create([
            'name' => 'General Medicine',
            'code' => 'GMED',
            'description' => 'General medical services',
            'location' => 'Building A, Floor 1',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $role->id,
            'department_id' => $department->id,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
