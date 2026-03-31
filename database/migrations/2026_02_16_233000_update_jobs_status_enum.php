<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change status enum to only working/done. First update any 'pending' rows.
        DB::table('jobs_field')->where('status', 'pending')->update(['status' => 'working', 'started_at' => now()]);

        // For MySQL, modify the enum
        DB::statement("ALTER TABLE jobs_field MODIFY COLUMN status ENUM('working', 'done') DEFAULT 'working'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE jobs_field MODIFY COLUMN status ENUM('pending', 'working', 'done') DEFAULT 'pending'");
    }
};
