<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_transfer_sks', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel transfer_sks utama
            $table->foreignId('transfer_sks_id')
                ->constrained('transfer_sks')
                ->onDelete('cascade');

            // Relasi ke tabel asesor yang menilai
            $table->foreignId('asesor_id')
                ->constrained('asesor')
                ->onDelete('cascade');

            // Kolom penilaian yang diisi berbeda oleh masing-masing asesor
            $table->text('kesenjangan')->nullable();
            $table->integer('hasil')->nullable();
            $table->text('catatan_asesor')->nullable();

            $table->timestamps();

            // KUNCI UTAMA: Mencegah 1 asesor menilai 1 ajuan MK yang sama lebih dari satu kali
            $table->unique(['transfer_sks_id', 'asesor_id'], 'unique_transfer_asesor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_transfer_sks');
    }
};
