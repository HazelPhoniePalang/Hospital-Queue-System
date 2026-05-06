<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\QueueEntry;
use App\Models\Visit;

Route::get('/debug/queue/32', function() {
    $queue = QueueEntry::with('visit')->find(32);
    
    echo "<h2>Queue #32</h2>";
    echo "<pre>";
    print_r($queue ? $queue->toArray() : 'Not found');
    echo "</pre>";
    
    if ($queue && $queue->visit) {
        echo "<h2>Associated Visit</h2>";
        echo "<pre>";
        print_r($queue->visit->toArray());
        echo "</pre>";
    }
    
    echo "<h2>Check for invalid doctor_id in visits</h2>";
    $badVisits = DB::table('visits')
        ->join('users', 'visits.doctor_id', '=', 'users.id', 'left')
        ->whereNull('users.id')
        ->select('visits.*')
        ->get();
    echo "<pre>";
    print_r($badVisits->toArray());
    echo "</pre>";
    
    echo "<h2>Triggers on queues table</h2>";
    $triggers = DB::select("SHOW TRIGGERS LIKE 'queues'");
    echo "<pre>";
    print_r($triggers);
    echo "</pre>";
});