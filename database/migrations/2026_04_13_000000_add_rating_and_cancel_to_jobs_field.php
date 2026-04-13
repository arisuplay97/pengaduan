<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('estimated_time');
            $table->text('rating_feedback')->nullable()->after('rating');
            $table->timestamp('cancelled_at')->nullable()->after('finished_at');
        });
    }

    public function down(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rating_feedback', 'cancelled_at']);
        });
    }
};
