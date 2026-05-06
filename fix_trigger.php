<?php
// File: fix_trigger.php
// Run: php fix_trigger.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Dropping old trigger...\n";
DB::statement("DROP TRIGGER IF EXISTS trg_create_visit");

echo "Creating new trigger with doctor_id handling...\n";
$sql = <<<SQL
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

DB::statement($sql);

echo "Trigger updated successfully.\n";
echo "The doctor_id is now set to NULL initially, allowing the visit record to be created.\n";
echo "Later, when a doctor is assigned via the assignDoctor action, the doctor_id will be updated.\n";
