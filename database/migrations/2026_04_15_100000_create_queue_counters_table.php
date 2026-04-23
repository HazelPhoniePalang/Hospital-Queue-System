<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    if (!Schema::hasTable('queue_counters')) {
        Schema::create('queue_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->date('counter_date');
            $table->integer('current_count')->default(0);
            $table->timestamps();

            $table->unique(['department_id', 'counter_date']);
        });
    }
}

    public function down(): void
    {
        Schema::dropIfExists('queue_counters');
    }
};
