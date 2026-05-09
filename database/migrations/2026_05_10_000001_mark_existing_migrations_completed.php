<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mark all existing HQMS migrations as completed
        // This prevents them from running again during deployment
        $existingMigrations = [
            '0001_01_01_000000_create_users_table',
            '0001_01_01_000001_create_cache_table',
            '0001_01_01_000002_create_jobs_table',
            '2026_04_07_000001_create_hqms_tables',
            '2026_04_15_000001_create_payments_table',
            '2026_04_15_040623_update_counters_status_column',
            '2026_04_15_100000_create_queue_counters_table',
            '2026_04_15_133151_remove_unique_from_queues_table',
            '2026_04_20_112700_add_symptoms_to_queues_table',
            '2026_04_27_155041_create_customer_columns',
            '2026_04_27_155042_create_subscriptions_table',
            '2026_04_27_155043_create_subscription_items_table',
            '2026_04_27_155044_add_meter_id_to_subscription_items_table',
            '2026_04_27_155045_add_meter_event_name_to_subscription_items_table',
            '2026_04_28_000001_make_doctor_id_nullable_in_visits',
            '2026_04_29_153118_create_sessions_table',
            '2026_04_29_153752_create_departments_table',
            '2026_05_05_121000_add_soft_deletes_to_admin_crud_tables',
        ];

        foreach ($existingMigrations as $migration) {
            DB::table('migrations')->insertOrIgnore([
                'migration' => $migration,
                'batch' => 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the migration records - this allows old migrations to run again if needed
        $existingMigrations = [
            '0001_01_01_000000_create_users_table',
            '0001_01_01_000001_create_cache_table',
            '0001_01_01_000002_create_jobs_table',
            '2026_04_07_000001_create_hqms_tables',
            '2026_04_15_000001_create_payments_table',
            '2026_04_15_040623_update_counters_status_column',
            '2026_04_15_100000_create_queue_counters_table',
            '2026_04_15_133151_remove_unique_from_queues_table',
            '2026_04_20_112700_add_symptoms_to_queues_table',
            '2026_04_27_155041_create_customer_columns',
            '2026_04_27_155042_create_subscriptions_table',
            '2026_04_27_155043_create_subscription_items_table',
            '2026_04_27_155044_add_meter_id_to_subscription_items_table',
            '2026_04_27_155045_add_meter_event_name_to_subscription_items_table',
            '2026_04_28_000001_make_doctor_id_nullable_in_visits',
            '2026_04_29_153118_create_sessions_table',
            '2026_04_29_153752_create_departments_table',
            '2026_05_05_121000_add_soft_deletes_to_admin_crud_tables',
        ];

        DB::table('migrations')->whereIn('migration', $existingMigrations)->delete();
    }
};