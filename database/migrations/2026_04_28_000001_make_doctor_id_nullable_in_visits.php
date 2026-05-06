<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['doctor_id']);
            // Make doctor_id nullable
            $table->unsignedBigInteger('doctor_id')->nullable()->change();
            // Re-add foreign key with SET NULL on delete
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->unsignedBigInteger('doctor_id')->nullable(false)->change();
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};