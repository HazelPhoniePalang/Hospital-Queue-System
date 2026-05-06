<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verifying Fix ===\n\n";

// Check trigger
echo "1. Current trigger definition:\n";
$result = DB::select("SHOW TRIGGERS WHERE `Table` = 'queues' AND `Trigger` = 'trg_create_visit'");
if ($result) {
    foreach ($result as $row) {
        echo "   Trigger: {$row->Trigger}\n";
        echo "   Event: {$row->Event}\n";
        echo "   Statement: {$row->Statement}\n";
    }
} else {
    echo "   Trigger not found!\n";
}

echo "\n2. Visits table doctor_id column:\n";
$columns = DB::select("SHOW COLUMNS FROM visits LIKE 'doctor_id'");
foreach ($columns as $col) {
    echo "   Field: {$col->Field}, Type: {$col->Type}, Null: {$col->Null}, Key: {$col->Key}, Default: {$col->Default}\n";
}

echo "\n3. Test: Attempt to call queue (simulate trigger):\n";
// Insert a test queue record with status called to see if trigger works
$testQueue = DB::table('queues')->where('status', 'waiting')->first();
if ($testQueue) {
    echo "   Test queue ID: {$testQueue->id}\n";
    try {
        DB::statement("UPDATE queues SET status = 'called' WHERE id = ?", [$testQueue->id]);
        echo "   ✓ Queue update successful - no constraint violation!\n";
        // Check if visit was created
        $visit = DB::table('visits')->where('queue_id', $testQueue->id)->first();
        if ($visit) {
            echo "   ✓ Visit created with ID: {$visit->id}, doctor_id: " . ($visit->doctor_id ?? 'NULL') . "\n";
        }
        // Reset for clean state
        DB::statement("DELETE FROM visits WHERE queue_id = ?", [$testQueue->id]);
        DB::statement("UPDATE queues SET status = 'waiting', called_at = NULL WHERE id = ?", [$testQueue->id]);
        echo "   Test data cleaned up.\n";
    } catch (Exception $e) {
        echo "   ✗ ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "   No waiting queue found for testing.\n";
}

echo "\n=== Fix Verification Complete ===\n";
