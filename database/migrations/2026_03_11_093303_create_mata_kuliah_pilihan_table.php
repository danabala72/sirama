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
        Schema::create('mata_kuliah_pilihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('kode_mk');
            $table->string('nama_mk');
            $table->integer('nilai_angka')->nullable();
            $table->string('nilai_huruf', 5)->nullable();
            $table->integer('sks')->nullable(); 
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'kode_mk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah_pilihan');
    }
};
