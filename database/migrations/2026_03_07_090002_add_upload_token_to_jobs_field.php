<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->string('upload_token', 64)->nullable()->unique()->after('photo_after');
        });
    }

    public function down(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->dropColumn('upload_token');
        });
    }
};
