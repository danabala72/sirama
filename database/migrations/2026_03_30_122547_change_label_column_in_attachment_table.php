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
        Schema::table('attachment', function (Blueprint $table) {
            $table->enum('label', [
                'sertifikat_pelatihan',
                'ijazah',
                'transkrip',
                'pengalaman',
                'sertifikat_kompetensi',
                'video',
                'cv',
                'pernyataan'
            ])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachment', function (Blueprint $table) {
            $table->string('label')->nullable(false)->change();
        });
    }
};
