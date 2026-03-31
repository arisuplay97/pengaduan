<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->string('photo_before')->nullable()->after('description');
            $table->string('photo_after')->nullable()->after('photo_before');
            $table->string('problem_type')->nullable()->after('photo_after');
        });
    }

    public function down(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->dropColumn(['photo_before', 'photo_after', 'problem_type']);
        });
    }
};
