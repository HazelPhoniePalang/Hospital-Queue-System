<?php
// File: create_views.php
// Run: php create_views.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Creating database views...\n";

// Drop existing views if they exist
DB::statement("DROP VIEW IF EXISTS active_waiting_details");
DB::statement("DROP VIEW IF EXISTS completed_consultations");
DB::statement("DROP VIEW IF EXISTS patient_queue_details");

echo "Creating active_waiting_details...\n";
$sql1 = <<<SQL
CREATE VIEW active_waiting_details AS
SELECT
    q.id AS queue_id,
    q.queue_no,
    q.priority_level,
    q.status,
    q.created_at AS queue_created_at,
    q.called_at,
    p.id AS patient_id,
    p.first_name,
    p.last_name,
    p.birth_date,
    p.gender,
    p.contact_no,
    d.name AS department_name,
    d.code AS department_code,
    s.service_name,
    s.average_duration,
    s.cost
FROM queues q
JOIN patients p ON q.patient_id = p.id
JOIN departments d ON q.department_id = d.id
JOIN services s ON q.service_id = s.id
WHERE q.status IN ('waiting', 'called', 'paid')
ORDER BY q.created_at ASC;
SQL;
DB::statement($sql1);

echo "Creating completed_consultations...\n";
$sql2 = <<<SQL
CREATE VIEW completed_consultations AS
SELECT
    v.id AS visit_id,
    v.visit_date,
    v.notes,
    v.diagnosis,
    v.status AS visit_status,
    v.created_at AS visit_created_at,
    v.updated_at AS visit_updated_at,
    p.id AS patient_id,
    p.first_name,
    p.last_name,
    p.birth_date,
    p.gender,
    p.contact_no,
    q.queue_no,
    d.name AS department_name,
    d.code AS department_code,
    s.service_name,
    u.name AS doctor_name,
    pay.amount AS payment_amount,
    pay.payment_method,
    pay.status AS payment_status,
    pay.paid_at
FROM visits v
JOIN patients p ON v.patient_id = p.id
JOIN queues q ON v.queue_id = q.id
JOIN departments d ON q.department_id = d.id
JOIN services s ON q.service_id = s.id
LEFT JOIN users u ON v.doctor_id = u.id
LEFT JOIN payments pay ON pay.queue_id = q.id
WHERE v.status = 'completed'
ORDER BY v.visit_date DESC;
SQL;
DB::statement($sql2);

echo "Creating patient_queue_details...\n";
$sql3 = <<<SQL
CREATE VIEW patient_queue_details AS
SELECT
    p.id AS patient_id,
    p.first_name,
    p.last_name,
    p.birth_date,
    p.gender,
    p.contact_no,
    p.address,
    q.symptoms,
    q.id AS queue_id,
    q.queue_no,
    q.priority_level,
    q.status AS queue_status,
    q.created_at AS queue_created_at,
    q.called_at,
    q.updated_at AS queue_updated_at,
    d.name AS department_name,
    d.code AS department_code,
    s.service_name,
    s.average_duration,
    s.cost,
    v.id AS visit_id,
    v.visit_date,
    v.notes,
    v.diagnosis,
    v.status AS visit_status,
    u.name AS doctor_name,
    pay.id AS payment_id,
    pay.amount AS payment_amount,
    pay.payment_method,
    pay.status AS payment_status,
    pay.paid_at
FROM patients p
LEFT JOIN queues q ON p.id = q.patient_id
LEFT JOIN departments d ON q.department_id = d.id
LEFT JOIN services s ON q.service_id = s.id
LEFT JOIN visits v ON q.id = v.queue_id
LEFT JOIN users u ON v.doctor_id = u.id
LEFT JOIN payments pay ON q.id = pay.queue_id
ORDER BY p.last_name, p.first_name, q.created_at DESC;
SQL;
DB::statement($sql3);

echo "All views created successfully.\n";
echo "active_waiting_details: Shows patients with queues in 'waiting', 'called', or 'paid' status.\n";
echo "completed_consultations: Shows completed visits with patient and payment details.\n";
echo "patient_queue_details: Comprehensive view of all patients with their queue, visit, and payment history.\n";