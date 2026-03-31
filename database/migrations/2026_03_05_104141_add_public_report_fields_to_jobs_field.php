<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->string('reporter_name')->nullable()->after('user_id');
            $table->string('reporter_phone', 20)->nullable()->after('reporter_name');
            $table->string('ticket_code', 20)->nullable()->unique()->after('reporter_phone');
            $table->enum('source', ['internal', 'public'])->default('internal')->after('ticket_code');
        });
    }

    public function down(): void
    {
        Schema::table('jobs_field', function (Blueprint $table) {
            $table->dropColumn(['reporter_name', 'reporter_phone', 'ticket_code', 'source']);
        });
    }
};
