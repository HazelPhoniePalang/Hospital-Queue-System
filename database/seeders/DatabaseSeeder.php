<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Department;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'System Administrator']
        );
        $staffRole = Role::firstOrCreate(
            ['name' => 'Hospital Staff'],
            ['description' => 'Hospital Staff']
        );
        $doctorRole = Role::firstOrCreate(
            ['name' => 'Doctor'],
            ['description' => 'Medical Professional']
        );
        $patientRole = Role::firstOrCreate(
            ['name' => 'Patient'],
            ['description' => 'Patient User']
        );

        // 2. Create Departments
        $genMed = Department::firstOrCreate(
            ['code' => 'GMED'],
            [
                'name' => 'General Medicine',
                'description' => 'General medical services',
                'location' => 'Building A, Floor 1',
            ]
        );

        $peds = Department::firstOrCreate(
            ['code' => 'PEDS'],
            [
                'name' => 'Pediatrics',
                'description' => 'Child care services',
                'location' => 'Building B, Floor 2',
            ]
        );

        // 3. Create Services for General Medicine
        Service::firstOrCreate(
            [
                'department_id' => $genMed->id,
                'service_name' => 'Check-up',
            ],
            [
                'average_duration' => 15,
                'cost' => 500,
                'description' => 'Standard medical check-up',
            ]
        );

        // 4. Create Users (default credentials)
        $defaultPassword = 'password123';

        User::updateOrCreate(
            ['email' => 'admin@hospital.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make($defaultPassword),
                'role_id' => $adminRole->id,
                'department_id' => null,
            ]
        );

        // Staff for General Medicine
        $staff1 = User::updateOrCreate(
            ['email' => 'staff1@hospital.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make($defaultPassword),
                'role_id' => $staffRole->id,
                'department_id' => $genMed->id,
            ]
        );

        $staff2 = User::updateOrCreate(
            ['email' => 'staff2@hospital.com'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make($defaultPassword),
                'role_id' => $staffRole->id,
                'department_id' => $genMed->id,
            ]
        );

        // Staff for Pediatrics
        $staff3 = User::updateOrCreate(
            ['email' => 'staff3@hospital.com'],
            [
                'name' => 'Ana Garcia',
                'password' => Hash::make($defaultPassword),
                'role_id' => $staffRole->id,
                'department_id' => $peds->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'doctor@hospital.com'],
            [
                'name' => 'Dr. Smith',
                'password' => Hash::make($defaultPassword),
                'role_id' => $doctorRole->id,
                'department_id' => $genMed->id,
            ]
        );

        // 5. Create Counters and Assign Staff
        // General Medicine Counters
        Counter::updateOrCreate(
            ['department_id' => $genMed->id, 'name' => 'Counter 1'],
            [
                'assigned_staff_id' => $staff1->id,
                'status' => 'ready',
            ]
        );

        Counter::updateOrCreate(
            ['department_id' => $genMed->id, 'name' => 'Counter 2'],
            [
                'assigned_staff_id' => $staff2->id,
                'status' => 'ready',
            ]
        );

        Counter::updateOrCreate(
            ['department_id' => $genMed->id, 'name' => 'Counter 3'],
            [
                'status' => 'ready',
            ]
        );

        // Pediatrics Counters
        Counter::updateOrCreate(
            ['department_id' => $peds->id, 'name' => 'Counter 1'],
            [
                'assigned_staff_id' => $staff3->id,
                'status' => 'ready',
            ]
        );

        Counter::updateOrCreate(
            ['department_id' => $peds->id, 'name' => 'Counter 2'],
            [
                'status' => 'ready',
            ]
        );

        // 6. Insert sample data for empty tables
        $this->call([
            AdminUserSeeder::class,
            InsertSampleDataSeeder::class,
            DepartmentSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
