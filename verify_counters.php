<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\Counter;

$counters = Counter::with('assignedStaff', 'department')->get();

if ($counters->isEmpty()) {
    echo "No counters found in database.\n";
} else {
    foreach ($counters as $counter) {
        echo "Counter: " . $counter->name 
            . " | Department: " . $counter->department->name 
            . " | Staff: " . $counter->assignedStaff->name 
            . " | Status: " . $counter->status . "\n";
    }
}
