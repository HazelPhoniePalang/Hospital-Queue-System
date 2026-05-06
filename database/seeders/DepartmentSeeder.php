<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $departments = [
        ['name' => 'Emergency',   'code' => 'ER',  'description' => 'Handles life-threatening emergencies', 'location' => 'Ground Floor, Wing A'],
        ['name' => 'Outpatient',  'code' => 'OPD', 'description' => 'General consultations and check-ups',  'location' => 'Ground Floor, Wing B'],
        ['name' => 'Radiology',   'code' => 'RAD', 'description' => 'X-ray, MRI, and imaging services',     'location' => '2nd Floor, Wing A'],
        ['name' => 'Laboratory',  'code' => 'LAB', 'description' => 'Blood tests and diagnostic exams',     'location' => '2nd Floor, Wing B'],
        ['name' => 'Pharmacy',    'code' => 'PHR', 'description' => 'Medication dispensing',                'location' => 'Ground Floor, Wing C'],
    ];

    foreach ($departments as $dept) {
        \App\Models\Department::create($dept);
    }
}
}
