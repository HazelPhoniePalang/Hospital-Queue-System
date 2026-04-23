<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\QueueEntry;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InsertSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing departments, services, and doctors
        $departments = Department::all(['id', 'name', 'code']);
        $services = Service::all(['id', 'service_name', 'department_id', 'cost']);
        $doctors = User::whereHas('role', function ($q) {
            $q->where('name', 'doctor');
        })->get(['id', 'name']);

        // If no doctors exist, create sample doctors
        if ($doctors->isEmpty()) {
            $this->command->info('No doctors found. Creating sample doctors...');
            $doctorRole = Role::firstOrCreate(
                ['name' => 'doctor'],
                ['description' => 'Medical Doctor']
            );

            $doctorNames = [
                'Dr. Maria Santos',
                'Dr. Juan Reyes',
                'Dr. Ana Cruz',
            ];

            $doctors = collect();
            foreach ($doctorNames as $name) {
                $doctors->push(User::firstOrCreate(
                    ['email' => strtolower(str_replace(['Dr. ', ' '], ['', '.'], $name)).'@hospital.com'],
                    [
                        'name' => $name,
                        'password' => bcrypt('password123'),
                        'role_id' => $doctorRole->id,
                        'department_id' => $departments->random()->id,
                    ]
                ));
            }
        }

        // Insert sample patients (10 patients) - skip if already exist
        $existingPatientCount = Patient::count();
        if ($existingPatientCount < 10) {
            $firstNames = ['Maria', 'Juan', 'Ana', 'Jose', 'Elena', 'Carlos', 'Rosa', 'Pedro', 'Lucia', 'Manuel'];
            $lastNames = ['Santos', 'Reyes', 'Cruz', 'Garcia', 'Torres', 'Flores', 'Ramos', 'Castro', 'Navarro', 'Mendoza'];
            $genders = ['Female', 'Male', 'Female', 'Male', 'Female', 'Male', 'Female', 'Male', 'Female', 'Male'];

            $toCreate = 10 - $existingPatientCount;
            for ($i = 0; $i < $toCreate; $i++) {
                $idx = ($existingPatientCount + $i) % 10;
                $birthYear = rand(1950, 2005);
                $birthMonth = rand(1, 12);
                $birthDay = rand(1, 28);

                Patient::create([
                    'first_name' => $firstNames[$idx],
                    'last_name' => $lastNames[$idx],
                    'birth_date' => sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay),
                    'gender' => $genders[$idx],
                    'contact_no' => '09'.rand(700000000, 999999999),
                    'address' => rand(1, 200).' Main Street, City',
                ]);
            }
            $this->command->info("Created $toCreate additional patients");
        }

        // Reload patients with IDs
        $patients = Patient::all();

        // Insert sample queues (15 total) - add only if less than 15
        $existingQueueCount = QueueEntry::count();
        if ($existingQueueCount < 15) {
            $statuses = ['waiting', 'called', 'in-progress', 'completed'];
            $priorities = ['standard', 'standard', 'standard', 'standard', 'priority'];
            $toCreate = 15 - $existingQueueCount;

            for ($i = $existingQueueCount + 1; $i <= 15; $i++) {
                $patient = $patients->random();
                $department = $departments->random();
                $filteredServices = $services->where('department_id', $department->id);
                if ($filteredServices->isEmpty()) {
                    continue;
                }
                $service = $filteredServices->random();

                $queueNo = sprintf('%s-%04d', $department->code, $i);
                $status = $statuses[array_rand($statuses)];

                $calledAt = null;
                $completedAt = null;
                if ($status === 'called' || $status === 'in-progress') {
                    $calledAt = Carbon::now()->subMinutes(rand(5, 30));
                } elseif ($status === 'completed') {
                    $calledAt = Carbon::now()->subMinutes(rand(30, 120));
                    $completedAt = (clone $calledAt)->addMinutes(rand(5, 20));
                }

                QueueEntry::create([
                    'queue_no' => $queueNo,
                    'priority_level' => $priorities[array_rand($priorities)],
                    'status' => $status,
                    'called_at' => $calledAt,
                    'completed_at' => $completedAt,
                    'patient_id' => $patient->id,
                    'department_id' => $department->id,
                    'service_id' => $service->id,
                    'symptoms' => 'Sample symptoms: headache, fever',
                ]);
            }
            $this->command->info("Created $toCreate additional queue entries");
        }

        // Reload queues
        $queues = QueueEntry::all();

        // Insert sample visits (10 total) - add only if less than 10
        $existingVisitCount = Visit::count();
        if ($existingVisitCount < 10) {
            $visitStatuses = ['ongoing', 'completed', 'cancelled'];
            $diagnoses = [
                'Upper respiratory infection',
                'Hypertension',
                'Diabetes type 2',
                'Acute gastroenteritis',
                'Urinary tract infection',
                'Migraine',
                'Bronchial asthma',
                'Anemia',
                'Dermatitis',
                'Musculoskeletal pain',
            ];

            $visitCount = $existingVisitCount;
            foreach ($queues as $index => $queue) {
                // Check if visit already exists for this queue
                $existingVisit = Visit::where('queue_id', $queue->id)->first();
                if ($existingVisit) {
                    continue;
                }

                if ($visitCount >= 10) {
                    break;
                }

                $doctor = $doctors->random();
                $patient = $queue->patient;

                $visitDate = $queue->called_at ?? Carbon::now()->subDays(rand(1, 30));
                $status = $visitStatuses[array_rand($visitStatuses)];

                $notes = null;
                $diagnosis = null;
                if ($status === 'completed') {
                    $diagnosis = $diagnoses[$index % count($diagnoses)];
                    $notes = 'Patient responded well to treatment. Follow-up recommended.';
                } elseif ($status === 'ongoing') {
                    $diagnosis = 'Under evaluation';
                    $notes = 'Patient currently under consultation.';
                }

                Visit::create([
                    'visit_date' => $visitDate,
                    'notes' => $notes,
                    'diagnosis' => $diagnosis,
                    'status' => $status,
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'queue_id' => $queue->id,
                ]);
                $visitCount++;
            }
            $this->command->info('Created '.($visitCount - $existingVisitCount).' additional visits');
        }

        // Insert sample payments for completed queues (no duplicate payments)
        $paymentMethods = ['cash', 'gcash', 'credit_card', 'debit_card', 'insurance'];
        $paymentCount = 0;

        foreach ($queues as $queue) {
            if ($queue->status !== 'completed') {
                continue;
            }

            $existingPayment = Payment::where('queue_id', $queue->id)->first();
            if ($existingPayment) {
                continue;
            }

            $service = Service::find($queue->service_id);
            if (! $service || $service->cost <= 0) {
                continue;
            }

            Payment::create([
                'queue_id' => $queue->id,
                'patient_id' => $queue->patient_id,
                'amount' => $service->cost,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'status' => 'completed',
                'paid_at' => $queue->completed_at ?? Carbon::now()->subDays(rand(1, 10)),
            ]);
            $paymentCount++;
        }

        if ($paymentCount > 0) {
            $this->command->info("Created $paymentCount additional payments");
        }

        $this->command->info('Sample data seeding complete');
    }
}
