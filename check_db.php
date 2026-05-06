<?php
// File: check_db.php
// Run: php check_db.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\QueueEntry;
use App\Models\Visit;
use App\Models\User;

echo "=== Checking Queue #32 ===\n";
$queue = QueueEntry::with('visit')->find(32);
if ($queue) {
    echo "Queue found: " . $queue->queue_no . "\n";
    echo "Status: " . $queue->status . "\n";
    if ($queue->visit) {
        echo "Associated Visit ID: " . $queue->visit->id . "\n";
        echo "Visit doctor_id: " . $queue->visit->doctor_id . "\n";
        
        $doctor = User::find($queue->visit->doctor_id);
        echo "Doctor exists: " . ($doctor ? 'YES' : 'NO') . "\n";
        if (!$doctor) {
            echo "INVALID DOCTOR ID - This is the problem!\n";
        }
    } else {
        echo "No visit associated yet.\n";
    }
} else {
    echo "Queue not found.\n";
}

echo "\n=== Checking ALL visits with invalid doctor_id ===\n";
$visits = DB::table('visits')
    ->leftJoin('users', 'visits.doctor_id', '=', 'users.id')
    ->whereNull('users.id')
    ->select('visits.id', 'visits.queue_id', 'visits.doctor_id')
    ->get();

if ($visits->count() > 0) {
    echo "Found " . $visits->count() . " visits with invalid doctor_id:\n";
    foreach ($visits as $v) {
        echo "- Visit ID {$v->id}, Queue ID {$v->queue_id}, doctor_id={$v->doctor_id}\n";
    }
} else {
    echo "All visits have valid doctor_id.\n";
}

echo "\n=== Checking database triggers on queues table ===\n";
$triggers = DB::select("SHOW TRIGGERS WHERE `Table` = 'queues'");
if (empty($triggers)) {
    echo "No triggers found on queues table.\n";
} else {
    foreach ($triggers as $t) {
        echo "Trigger: {$t->Trigger}, Event: {$t->Event}, Statement: {$t->Statement}\n";
    }
}

echo "\n=== Checking foreign keys on visits table ===\n";
$fks = DB::select("SHOW CREATE TABLE visits");
echo $fks[0]->{'Create Table'} . "\n";

echo "\nDone.\n";
