<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cp_mata_kuliah', function (Blueprint $table) {

            // =========================
            // 1. DROP OLD RELATION (SAFE)
            // =========================
            if (Schema::hasColumn('cp_mata_kuliah', 'mata_kuliah_id')) {
                try {
                    $table->dropForeign(['mata_kuliah_id']);
                } catch (\Throwable $e) {
                    // FK tidak ada → skip
                }

                $table->dropColumn('mata_kuliah_id');
            }

            // =========================
            // 2. ENSURE NEW COLUMN EXISTS
            // =========================
            if (!Schema::hasColumn('cp_mata_kuliah', 'mata_kuliah_semester_id')) {
                $table->unsignedBigInteger('mata_kuliah_semester_id')->after('id');
            }
        });

        // =========================
        // 3. ADD FOREIGN KEY BARU
        // =========================
        try {
            Schema::table('cp_mata_kuliah', function (Blueprint $table) {
                $table->foreign('mata_kuliah_semester_id')
                    ->references('id')
                    ->on('mata_kuliah_semester')
                    ->cascadeOnDelete();
            });
        } catch (\Throwable $e) {
            // FK sudah ada → skip
        }
    }

    public function down(): void
    {
        Schema::table('cp_mata_kuliah', function (Blueprint $table) {

            // drop FK baru
            try {
                $table->dropForeign(['mata_kuliah_semester_id']);
            } catch (\Throwable $e) {
            }

            // drop column baru
            if (Schema::hasColumn('cp_mata_kuliah', 'mata_kuliah_semester_id')) {
                $table->dropColumn('mata_kuliah_semester_id');
            }

            // restore column lama
            if (!Schema::hasColumn('cp_mata_kuliah', 'mata_kuliah_id')) {
                $table->unsignedBigInteger('mata_kuliah_id')->nullable();
            }
        });
    }
};
