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
        Schema::create('attachment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
        
            // Konteks file: 'mata_kuliah', 'cv', 'umum'
            $table->string('label'); 
            
            // ID Mata Kuliah (nullable karena file CV tidak butuh MK)
            $table->foreignId('mata_kuliah_id')->nullable()->constrained('mata_kuliah')->onDelete('set null');

            // Metadata File
            $table->string('file_name');      // Nama asli
            $table->string('file_path');      // Path di storage
            $table->string('file_type');      // e.g., 'video', 'document', 'image'
            $table->string('mime_type');      // e.g., 'video/mp4', 'application/pdf'
            $table->unsignedBigInteger('file_size');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment');
    }
};
