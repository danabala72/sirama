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
        Schema::table('mata_kuliah_pilihan', function (Blueprint $table) {
            $table->foreignId('mata_kuliah_semester_id')
                ->after('mahasiswa_id')
                ->nullable()
                ->constrained('mata_kuliah_semester')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliah_pilihan', function (Blueprint $table) {
            $table->dropForeign(['mata_kuliah_semester_id']);
            $table->dropColumn('mata_kuliah_semester_id');
        });
    }
};
