<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update user dirut, dirop, dirum to have role = 'direksi'
        DB::table('users')
            ->whereIn('username', ['dirut', 'dirop', 'dirum'])
            ->update(['role' => 'direksi']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to admin
        DB::table('users')
            ->whereIn('username', ['dirut', 'dirop', 'dirum'])
            ->where('role', 'direksi')
            ->update(['role' => 'admin']);
    }
};
