<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_cp_kompetensi', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel utama cp_level_kompetensi
            $table->foreignId('cp_level_kompetensi_id')
                  ->constrained('cp_level_kompetensi')
                  ->onDelete('cascade');

            // Relasi ke tabel asesor yang menilai
            $table->foreignId('asesor_id')
                  ->constrained('asesor')
                  ->onDelete('cascade');

            // Kolom indikator penilaian yang diisi berbeda oleh masing-masing asesor
            $table->tinyInteger('level_kompetensi')->nullable();
            $table->tinyInteger('valid')->default(0);
            $table->tinyInteger('asli')->default(0);
            $table->tinyInteger('terkini')->default(0);
            $table->tinyInteger('memadai')->default(0);
            
            $table->timestamps();

            // Memastikan 1 asesor hanya menginput 1 baris penilaian untuk 1 CP kompetensi MK pilihan
            $table->unique(['cp_level_kompetensi_id', 'asesor_id'], 'unique_penilaian_cp_asesor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_cp_kompetensi');
    }
};
