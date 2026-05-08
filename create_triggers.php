<?php
// File: create_triggers.php
// Run: php create_triggers.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Creating triggers...\n";

// Drop existing triggers if they exist
DB::statement("DROP TRIGGER IF EXISTS trg_increment_counter");
DB::statement("DROP TRIGGER IF EXISTS trg_create_visit");
DB::statement("DROP TRIGGER IF EXISTS trg_create_payment");

echo "Creating trg_increment_counter...\n";
$sql1 = <<<SQL
CREATE TRIGGER trg_increment_counter
AFTER INSERT ON queues
FOR EACH ROW
BEGIN
    UPDATE counters SET current_queue_id = NEW.id
    WHERE department_id = NEW.department_id
    AND status = 'ready'
    AND current_queue_id IS NULL
    ORDER BY id ASC
    LIMIT 1;
END;
SQL;
DB::statement($sql1);

echo "Creating trg_create_visit...\n";
$sql2 = <<<SQL
CREATE TRIGGER trg_create_visit
AFTER UPDATE ON queues
FOR EACH ROW
BEGIN
    IF NEW.status = 'called' AND OLD.status != 'called' THEN
        INSERT INTO visits (visit_date, status, patient_id, queue_id, doctor_id)
        VALUES (NOW(), 'ongoing', NEW.patient_id, NEW.id, NULL);
    END IF;
END;
SQL;
DB::statement($sql2);

echo "Creating trg_create_payment...\n";
$sql3 = <<<SQL
CREATE TRIGGER trg_create_payment
AFTER UPDATE ON visits
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        INSERT INTO payments (queue_id, patient_id, amount, payment_method, status, paid_at)
        VALUES (NEW.queue_id, NEW.patient_id, 0.00, 'cash', 'pending', NULL);
    END IF;
END;
SQL;
DB::statement($sql3);

echo "All triggers created successfully.\n";
echo "trg_increment_counter: Updates counters.current_queue_id to the new queue ID for the first ready counter in the department.\n";
echo "trg_create_visit: Inserts a new visit when queue status changes to 'called'.\n";
echo "trg_create_payment: Inserts a pending payment with amount 0.00 when visit status changes to 'completed'.\n";