<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained('queues', 'id')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients', 'id')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 30); // 'cash', 'gcash', etc.
            $table->string('status', 30)->default('completed');
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
