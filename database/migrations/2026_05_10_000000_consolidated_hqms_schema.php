<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Consolidated HQMS Schema Migration
        // This replaces multiple smaller migrations for faster deployment

        // Roles table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Departments table
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->string('location')->nullable();
                $table->timestamps();
            });
        }

        // Users table modifications
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->after('password')->constrained()->nullOnDelete();
                $table->foreignId('department_id')->nullable()->after('role_id')->constrained()->nullOnDelete();
                // Stripe billing columns (optional)
                $table->string('stripe_id')->nullable()->index();
                $table->string('pm_type')->nullable();
                $table->string('pm_last_four', 4)->nullable();
                $table->timestamp('trial_ends_at')->nullable();
            });
        }

        // Services table
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained()->cascadeOnDelete();
                $table->string('service_name');
                $table->unsignedInteger('average_duration');
                $table->decimal('cost', 10, 2)->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Patients table
        if (!Schema::hasTable('patients')) {
            Schema::create('patients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
                $table->string('first_name');
                $table->string('last_name');
                $table->date('birth_date');
                $table->string('gender', 30);
                $table->string('contact_no', 40)->nullable();
                $table->text('address')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Queues table
        if (!Schema::hasTable('queues')) {
            Schema::create('queues', function (Blueprint $table) {
                $table->id();
                $table->string('queue_no')->unique();
                $table->string('priority_level', 20)->default('standard');
                $table->string('status', 30)->default('waiting');
                $table->timestamp('called_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('symptoms')->nullable();
                $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
                $table->foreignId('department_id')->constrained()->cascadeOnDelete();
                $table->foreignId('service_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Counters table
        if (!Schema::hasTable('counters')) {
            Schema::create('counters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('current_queue_id')->nullable()->constrained('queues')->nullOnDelete();
                $table->string('status', 30)->default('ready');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Visits table
        if (!Schema::hasTable('visits')) {
            Schema::create('visits', function (Blueprint $table) {
                $table->id();
                $table->dateTime('visit_date');
                $table->text('notes')->nullable();
                $table->text('diagnosis')->nullable();
                $table->string('status', 30)->default('ongoing');
                $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
                $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('queue_id')->unique()->constrained('queues')->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Payments table
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('queue_id')->constrained('queues')->cascadeOnDelete();
                $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
                $table->decimal('amount', 10, 2);
                $table->string('payment_method', 30);
                $table->string('status', 30)->default('completed');
                $table->timestamp('paid_at')->useCurrent();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Queue counters table
        if (!Schema::hasTable('queue_counters')) {
            Schema::create('queue_counters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('department_id');
                $table->date('counter_date');
                $table->integer('current_count')->default(0);
                $table->timestamps();

                $table->unique(['department_id', 'counter_date']);
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            });
        }

        // Sessions table
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to handle foreign keys
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('queue_counters');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('counters');
        Schema::dropIfExists('queues');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('services');

        // Remove user table modifications
        if (Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('department_id');
                $table->dropConstrainedForeignId('role_id');
                $table->dropIndex(['stripe_id']);
                $table->dropColumn([
                    'stripe_id',
                    'pm_type',
                    'pm_last_four',
                    'trial_ends_at',
                ]);
            });
        }

        Schema::dropIfExists('departments');
        Schema::dropIfExists('roles');
    }
};