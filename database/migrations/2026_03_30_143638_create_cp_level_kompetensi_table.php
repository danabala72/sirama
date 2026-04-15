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
        Schema::create('cp_level_kompetensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_pilihan_id')
                  ->constrained('mata_kuliah_pilihan')
                  ->cascadeOnDelete();
            $table->foreignId('cp_mata_kuliah_id')
                  ->constrained('cp_mata_kuliah')
                  ->cascadeOnDelete();
            $table->boolean('level_kompetensi')->nullable();
            
            $table->timestamps();
             $table->unique([
                'mata_kuliah_pilihan_id',
                'cp_mata_kuliah_id'
            ], 'unik_cp_per_mk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_level_kompetensi');
    }
};
