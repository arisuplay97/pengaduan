<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notulens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->nullable()->constrained('agendas')->nullOnDelete();
            $table->string('title');
            $table->date('meeting_date');
            $table->integer('duration')->nullable(); // in minutes
            $table->integer('participants_count')->default(0);
            $table->text('overview')->nullable();
            $table->text('summary')->nullable();
            $table->text('transcript')->nullable();
            $table->string('video_url')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', ['draft', 'completed', 'approved'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notulens');
    }
};
