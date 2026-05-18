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
        // 1. HAPUS kolom level_kompetensi dari tabel penilaian_cp_kompetensi (Jangan drop tabelnya)
        if (Schema::hasColumn('penilaian_cp_kompetensi', 'level_kompetensi')) {
            Schema::table('penilaian_cp_kompetensi', function (Blueprint $table) {
                $table->dropColumn('level_kompetensi');
            });
        }

        // 2. KEMBALIKAN kolom level_kompetensi ke tabel induk cp_level_kompetensi
        Schema::table('cp_level_kompetensi', function (Blueprint $table) {
            if (!Schema::hasColumn('cp_level_kompetensi', 'level_kompetensi')) {
                // Menambahkan kembali kolom level_kompetensi setelah cp_mata_kuliah_id
                $table->tinyInteger('level_kompetensi')->nullable()->after('cp_mata_kuliah_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Hapus kolom level_kompetensi dari tabel induk jika rollback
        Schema::table('cp_level_kompetensi', function (Blueprint $table) {
            if (Schema::hasColumn('cp_level_kompetensi', 'level_kompetensi')) {
                $table->dropColumn('level_kompetensi');
            }
        });

        // 2. Kembalikan kolom level_kompetensi ke tabel penilaian_cp_kompetensi jika rollback
        Schema::table('penilaian_cp_kompetensi', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaian_cp_kompetensi', 'level_kompetensi')) {
                $table->tinyInteger('level_kompetensi')->nullable()->after('asesor_id');
            }
        });
    }
};
