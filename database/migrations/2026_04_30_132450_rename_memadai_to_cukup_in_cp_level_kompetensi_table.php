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
        Schema::table('cp_level_kompetensi', function (Blueprint $table) {
            // Hapus kolom 'cukup' (jika ada) dan ganti ke 'memadai' agar sama dengan Blade
            if (Schema::hasColumn('cp_level_kompetensi', 'cukup')) {
                $table->dropColumn('cukup');
            }
            $table->boolean('memadai')->default(0)->after('terkini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cp_level_kompetensi', function (Blueprint $table) {
            $table->dropColumn('memadai');
            $table->boolean('cukup')->default(0);
        });
    }
};
