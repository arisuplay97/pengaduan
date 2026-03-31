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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            
            // Informasi Dasar
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable(); // Bisa diisi "R. Rapat" atau "Zona A"

            // Jenis Agenda (Untuk Warna di Kalender)
            // DIRUT = Biru, UMUM = Kuning/Orange, OPERASIONAL = Hijau
            $table->enum('type', ['DIRUT', 'UMUM', 'OPERASIONAL'])->index();

            // Waktu
            $table->dateTime('start_at')->index();
            $table->dateTime('end_at')->index();

            // Fitur Keren Tambahan
            $table->boolean('is_all_day')->default(false);
            $table->boolean('is_private')->default(false); // Agenda rahasia direksi
            $table->string('meeting_link')->nullable(); // Link Google Meet/Zoom
            $table->unsignedBigInteger('created_by')->nullable(); // Siapa yang input

            // Status & Approval (Buat Laporan Dashboard)
            $table->enum('status', ['DRAFT','APPROVED','CANCELED','DONE'])->default('APPROVED')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};