<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom yang akan diisi oleh masing-masing asesor
        Schema::table('cp_level_kompetensi', function (Blueprint $table) {
            $table->dropColumn(['level_kompetensi', 'valid', 'asli', 'terkini', 'memadai']);
        });
    }

    public function down(): void
    {
        Schema::table('cp_level_kompetensi', function (Blueprint $table) {
            $table->tinyInteger('level_kompetensi')->nullable()->after('cp_mata_kuliah_id');
            $table->tinyInteger('valid')->default(0)->after('level_kompetensi');
            $table->tinyInteger('asli')->default(0)->after('valid');
            $table->tinyInteger('terkini')->default(0)->after('asli');
            $table->tinyInteger('memadai')->default(0)->after('terkini');
        });
    }
};
