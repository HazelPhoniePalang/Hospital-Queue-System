<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize legacy status labels.
        DB::table('counters')->where('status', 'active')->update(['status' => 'ready']);
        DB::table('counters')->where('status', 'inactive')->update(['status' => 'unavailable']);

        // Counters status is already a VARCHAR in fresh schema; only alter on MySQL for older DBs.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE counters MODIFY status VARCHAR(30) NOT NULL DEFAULT 'ready'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE counters MODIFY status ENUM('active','inactive','busy') NOT NULL DEFAULT 'active'");
        }
    }
};
