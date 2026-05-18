<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom penilaian dari tabel utama nonformal
        Schema::table('transfer_sks_nonformal', function (Blueprint $table) {
            $table->dropColumn(['kesenjangan', 'nilai', 'catatan_asesor']);
        });
    }

    public function down(): void
    {
        Schema::table('transfer_sks_nonformal', function (Blueprint $table) {
            $table->text('kesenjangan')->nullable()->after('mata_kuliah_pilihan_id');
            $table->integer('nilai')->nullable()->after('kesenjangan');
            $table->text('catatan_asesor')->nullable()->after('nilai');
        });
    }
};
