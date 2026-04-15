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
        Schema::create('transfer_sks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_pilihan_id')->constrained('mata_kuliah_pilihan')->onDelete('cascade');
    
            $table->string('kode_mk_asal');
            $table->string('nama_mk_asal');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_sks');
    }
};
