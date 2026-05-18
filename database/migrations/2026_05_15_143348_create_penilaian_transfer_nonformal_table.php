<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_transfer_nonformal', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel utama nonformal
            $table->foreignId('transfer_nonformal_id')
                  ->constrained('transfer_sks_nonformal')
                  ->onDelete('cascade');

            // Relasi ke tabel asesor yang menilai
            $table->foreignId('asesor_id')
                  ->constrained('asesor')
                  ->onDelete('cascade');

            // Kolom penilaian spesifik milik asesor (menggunakan nama kolom lama 'nilai')
            $table->text('kesenjangan')->nullable();
            $table->integer('nilai')->nullable();
            $table->text('catatan_asesor')->nullable();
            
            $table->timestamps();

            // Mencegah 1 asesor menilai 1 ajuan nonformal yang sama lebih dari sekali
            $table->unique(['transfer_nonformal_id', 'asesor_id'], 'unique_nonformal_asesor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_transfer_nonformal');
    }
};
