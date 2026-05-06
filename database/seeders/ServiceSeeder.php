<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $services = [
        ['department_id' => 1, 'service_name' => 'Emergency Consultation', 'average_duration' => 30, 'cost' => 500.00, 'description' => 'Urgent medical evaluation'],
        ['department_id' => 1, 'service_name' => 'Wound Dressing',         'average_duration' => 20, 'cost' => 300.00, 'description' => 'Treatment and dressing of wounds'],
        ['department_id' => 2, 'service_name' => 'General Consultation',   'average_duration' => 20, 'cost' => 350.00, 'description' => 'Routine doctor consultation'],
        ['department_id' => 2, 'service_name' => 'Pediatric Consultation', 'average_duration' => 20, 'cost' => 400.00, 'description' => 'Consultation for children'],
        ['department_id' => 3, 'service_name' => 'X-Ray',                  'average_duration' => 15, 'cost' => 600.00, 'description' => 'Standard X-ray imaging'],
        ['department_id' => 3, 'service_name' => 'Ultrasound',             'average_duration' => 30, 'cost' => 1200.00,'description' => 'Abdominal/pelvic ultrasound'],
        ['department_id' => 4, 'service_name' => 'CBC',                    'average_duration' => 15, 'cost' => 350.00, 'description' => 'Complete Blood Count test'],
        ['department_id' => 4, 'service_name' => 'Urinalysis',             'average_duration' => 10, 'cost' => 150.00, 'description' => 'Urine diagnostic test'],
        ['department_id' => 5, 'service_name' => 'Prescription Release',   'average_duration' => 10, 'cost' => 0.00,   'description' => 'Release of prescribed medicine'],
    ];

    foreach ($services as $service) {
        \App\Models\Service::create($service);
    }
}
}
