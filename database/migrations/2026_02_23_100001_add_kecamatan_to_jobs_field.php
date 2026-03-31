<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->foreignId('kecamatan_id')->nullable()->after('address')->constrained('kecamatans')->nullOnDelete();
            $table->index('created_at');
            $table->index('problem_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
            $table->dropColumn('kecamatan_id');
            $table->dropIndex(['created_at']);
            $table->dropIndex(['problem_type']);
            $table->dropIndex(['status']);
        });
    }
};
