<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Restore 'pending' to the status enum
        DB::statement("ALTER TABLE jobs_field MODIFY COLUMN status ENUM('pending', 'working', 'done') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::table('jobs_field')->where('status', 'pending')->update(['status' => 'working', 'started_at' => now()]);
        DB::statement("ALTER TABLE jobs_field MODIFY COLUMN status ENUM('working', 'done') DEFAULT 'working'");
    }
};
