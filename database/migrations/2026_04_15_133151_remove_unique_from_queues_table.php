<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            // Check if index exists before dropping
            $table->dropUnique(['queue_no']);
            // Add a new composite index that includes the department and date if you want uniqueness per day/dept
            // But since we have queue_counters, we can just rely on application logic and maybe just a regular index.
            $table->index('queue_no');
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropIndex(['queue_no']);
            $table->unique('queue_no');
        });
    }
};
