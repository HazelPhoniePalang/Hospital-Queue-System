<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = bcrypt('password123');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('roles')->truncate();
        DB::table('departments')->truncate();
        DB::table('users')->truncate();
        DB::table('services')->truncate();
        DB::table('counters')->truncate();
        DB::table('patients')->truncate();
        DB::table('queues')->truncate();
        DB::table('visits')->truncate();
        DB::table('payments')->truncate();

        $roles = [
            ['name' => 'Administrator', 'description' => 'System administrator with full access'],
            ['name' => 'Doctor', 'description' => 'Medical doctor for consultations'],
            ['name' => 'Nurse', 'description' => 'Nursing staff for patient care'],
            ['name' => 'Receptionist', 'description' => 'Front desk staff for queue management'],
            ['name' => 'Staff', 'description' => 'General staff member'],
        ];
        DB::table('roles')->insert($roles);

        $departments = [
            ['name' => 'General Medicine', 'code' => 'GEN', 'description' => 'General medical consultations', 'location' => 'Building A - Floor 1'],
            ['name' => 'Pediatrics', 'code' => 'PED', 'description' => 'Child healthcare services', 'location' => 'Building A - Floor 2'],
            ['name' => 'Cardiology', 'code' => 'CARD', 'description' => 'Heart and cardiovascular care', 'location' => 'Building B - Floor 1'],
            ['name' => 'Orthopedics', 'code' => 'ORTH', 'description' => 'Bone and joint treatments', 'location' => 'Building B - Floor 2'],
            ['name' => 'Dermatology', 'code' => 'DERM', 'description' => 'Skin care and treatments', 'location' => 'Building C - Floor 1'],
        ];
        DB::table('departments')->insert($departments);

        $users = [
            ['name' => 'Admin User', 'email' => 'admin@hqms.com', 'password' => $password, 'role_id' => 1, 'department_id' => 1],
            ['name' => 'Dr. John Smith', 'email' => 'dr.smith@hqms.com', 'password' => $password, 'role_id' => 2, 'department_id' => 1],
            ['name' => 'Dr. Sarah Johnson', 'email' => 'dr.johnson@hqms.com', 'password' => $password, 'role_id' => 2, 'department_id' => 2],
            ['name' => 'Dr. Michael Brown', 'email' => 'dr.brown@hqms.com', 'password' => $password, 'role_id' => 2, 'department_id' => 3],
            ['name' => 'Nurse Emily Davis', 'email' => 'nurse.davis@hqms.com', 'password' => $password, 'role_id' => 3, 'department_id' => 1],
            ['name' => 'Nurse Robert Wilson', 'email' => 'nurse.wilson@hqms.com', 'password' => $password, 'role_id' => 3, 'department_id' => 2],
            ['name' => 'Receptionist Lisa Martinez', 'email' => 'reception@hqms.com', 'password' => $password, 'role_id' => 4, 'department_id' => 1],
            ['name' => 'Staff Carlos Garcia', 'email' => 'staff.garcia@hqms.com', 'password' => $password, 'role_id' => 5, 'department_id' => 1],
        ];
        DB::table('users')->insert($users);

        $services = [
            ['department_id' => 1, 'service_name' => 'General Checkup', 'average_duration' => 15, 'cost' => 500.00, 'description' => 'Routine health examination'],
            ['department_id' => 1, 'service_name' => 'Blood Pressure Test', 'average_duration' => 10, 'cost' => 200.00, 'description' => 'Blood pressure monitoring'],
            ['department_id' => 2, 'service_name' => 'Child Vaccination', 'average_duration' => 20, 'cost' => 350.00, 'description' => 'Immunization for children'],
            ['department_id' => 2, 'service_name' => 'Pediatric Consultation', 'average_duration' => 25, 'cost' => 600.00, 'description' => 'Child health consultation'],
            ['department_id' => 3, 'service_name' => 'ECG Test', 'average_duration' => 30, 'cost' => 800.00, 'description' => 'Heart rhythm analysis'],
            ['department_id' => 4, 'service_name' => 'X-Ray', 'average_duration' => 15, 'cost' => 450.00, 'description' => 'Bone X-ray imaging'],
            ['department_id' => 5, 'service_name' => 'Skin Allergy Test', 'average_duration' => 20, 'cost' => 550.00, 'description' => 'Allergen testing'],
        ];
        DB::table('services')->insert($services);

        $counters = [
            ['department_id' => 1, 'name' => 'Counter 1', 'assigned_staff_id' => 7, 'current_queue_id' => null, 'status' => 'ready'],
            ['department_id' => 1, 'name' => 'Counter 2', 'assigned_staff_id' => 8, 'current_queue_id' => null, 'status' => 'ready'],
            ['department_id' => 2, 'name' => 'Pediatrics Counter', 'assigned_staff_id' => null, 'current_queue_id' => null, 'status' => 'ready'],
            ['department_id' => 3, 'name' => 'Cardiology Counter', 'assigned_staff_id' => null, 'current_queue_id' => null, 'status' => 'ready'],
            ['department_id' => 4, 'name' => 'Orthopedics Counter', 'assigned_staff_id' => null, 'current_queue_id' => null, 'status' => 'ready'],
        ];
        DB::table('counters')->insert($counters);

        $patients = [
            ['user_id' => null, 'first_name' => 'Alice', 'last_name' => 'Anderson', 'birth_date' => '1990-05-15', 'gender' => 'Female', 'contact_no' => '09123456701', 'address' => '123 Main Street, City'],
            ['user_id' => null, 'first_name' => 'Bob', 'last_name' => 'Thompson', 'birth_date' => '1985-08-22', 'gender' => 'Male', 'contact_no' => '09123456702', 'address' => '456 Oak Avenue, City'],
            ['user_id' => null, 'first_name' => 'Charlie', 'last_name' => 'Wilson', 'birth_date' => '1992-03-10', 'gender' => 'Male', 'contact_no' => '09123456703', 'address' => '789 Pine Road, City'],
            ['user_id' => null, 'first_name' => 'Diana', 'last_name' => 'Lee', 'birth_date' => '1988-11-28', 'gender' => 'Female', 'contact_no' => '09123456704', 'address' => '321 Elm Street, City'],
            ['user_id' => null, 'first_name' => 'Edward', 'last_name' => 'Kim', 'birth_date' => '1995-07-03', 'gender' => 'Male', 'contact_no' => '09123456705', 'address' => '654 Maple Drive, City'],
        ];
        DB::table('patients')->insert($patients);

        $queues = [
            ['queue_no' => 'A001', 'priority_level' => 'standard', 'status' => 'waiting', 'called_at' => null, 'completed_at' => null, 'patient_id' => 1, 'department_id' => 1, 'service_id' => 1, 'symptoms' => 'General feeling unwell'],
            ['queue_no' => 'A002', 'priority_level' => 'priority', 'status' => 'waiting', 'called_at' => null, 'completed_at' => null, 'patient_id' => 2, 'department_id' => 1, 'service_id' => 2, 'symptoms' => 'Headache and dizziness'],
            ['queue_no' => 'P001', 'priority_level' => 'standard', 'status' => 'waiting', 'called_at' => null, 'completed_at' => null, 'patient_id' => 3, 'department_id' => 2, 'service_id' => 3, 'symptoms' => 'Child fever'],
            ['queue_no' => 'C001', 'priority_level' => 'priority', 'status' => 'waiting', 'called_at' => null, 'completed_at' => null, 'patient_id' => 4, 'department_id' => 3, 'service_id' => 5, 'symptoms' => 'Chest pain'],
            ['queue_no' => 'O001', 'priority_level' => 'standard', 'status' => 'waiting', 'called_at' => null, 'completed_at' => null, 'patient_id' => 5, 'department_id' => 4, 'service_id' => 6, 'symptoms' => 'Leg pain after fall'],
        ];
        DB::table('queues')->insert($queues);

        $visits = [
            ['visit_date' => now(), 'notes' => 'Patient showed symptoms of common cold', 'diagnosis' => 'Upper Respiratory Infection', 'status' => 'completed', 'patient_id' => 1, 'doctor_id' => 2, 'queue_id' => 1],
            ['visit_date' => now(), 'notes' => 'Blood pressure slightly elevated', 'diagnosis' => 'Hypertension Stage 1', 'status' => 'completed', 'patient_id' => 2, 'doctor_id' => 2, 'queue_id' => 2],
            ['visit_date' => now(), 'notes' => 'Child received vaccination', 'diagnosis' => 'Healthy - Vaccinated', 'status' => 'completed', 'patient_id' => 3, 'doctor_id' => 3, 'queue_id' => 3],
            ['visit_date' => now(), 'notes' => 'ECG performed, results normal', 'diagnosis' => 'Normal sinus rhythm', 'status' => 'completed', 'patient_id' => 4, 'doctor_id' => 4, 'queue_id' => 4],
            ['visit_date' => now(), 'notes' => 'X-ray shows minor fracture', 'diagnosis' => 'Hairline fracture - right leg', 'status' => 'completed', 'patient_id' => 5, 'doctor_id' => 4, 'queue_id' => 5],
        ];
        DB::table('visits')->insert($visits);

        $payments = [
            ['queue_id' => 1, 'patient_id' => 1, 'amount' => 500.00, 'payment_method' => 'Cash', 'status' => 'completed', 'paid_at' => now()],
            ['queue_id' => 2, 'patient_id' => 2, 'amount' => 200.00, 'payment_method' => 'Cash', 'status' => 'completed', 'paid_at' => now()],
            ['queue_id' => 3, 'patient_id' => 3, 'amount' => 350.00, 'payment_method' => 'G-Cash', 'status' => 'completed', 'paid_at' => now()],
            ['queue_id' => 4, 'patient_id' => 4, 'amount' => 800.00, 'payment_method' => 'Credit Card', 'status' => 'completed', 'paid_at' => now()],
            ['queue_id' => 5, 'patient_id' => 5, 'amount' => 450.00, 'payment_method' => 'Cash', 'status' => 'completed', 'paid_at' => now()],
        ];
        DB::table('payments')->insert($payments);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        echo "Sample data seeded successfully!\n";
    }
}
