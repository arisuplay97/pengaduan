<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Change ENUM to VARCHAR first (to accept new values)
        DB::statement("ALTER TABLE jobs_field MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending'");

        // Step 2: Now map old values to new ones
        DB::table('jobs_field')->where('status', 'working')->update(['status' => 'on_progress']);
        DB::table('jobs_field')->where('status', 'done')->update(['status' => 'selesai']);
    }

    public function down(): void
    {
        // Map back to old values
        DB::table('jobs_field')->where('status', 'on_progress')->update(['status' => 'working']);
        DB::table('jobs_field')->where('status', 'selesai')->update(['status' => 'done']);
        DB::table('jobs_field')->whereNotIn('status', ['pending', 'working', 'done'])->update(['status' => 'pending']);

        DB::statement("ALTER TABLE jobs_field MODIFY COLUMN status ENUM('pending', 'working', 'done') DEFAULT 'pending'");
    }
};
