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
        Schema::table('cp_mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['mata_kuliah_id']);
            $table->dropColumn('mata_kuliah_id');

             $table->foreignId('mata_kuliah_semester_id')
                  ->after('id')
                  ->constrained('mata_kuliah_semester')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cp_mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['mata_kuliah_semester_id']);
            $table->dropColumn('mata_kuliah_semester_id');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah');
        });
    }
};
