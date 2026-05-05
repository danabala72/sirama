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
        Schema::table('mahasiswa', function (Blueprint $column) {
            $column->string('nama_sekolah')->nullable()->after('email');
            $column->string('alamat_sekolah')->nullable()->after('nama_sekolah');
            $column->year('tahun_lulus_sekolah')->nullable()->after('alamat_sekolah');
            $column->string('nama_pt')->nullable()->after('tahun_lulus_sekolah');
            $column->string('prodi_pt')->nullable()->after('nama_pt');
            $column->string('program_pt')->nullable()->after('prodi_pt');
            $column->year('tahun_lulus_pt')->nullable()->after('program_pt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $column) {
            $column->dropColumn([
                'nama_sekolah',
                'alamat_sekolah',
                'tahun_lulus_sekolah',
                'nama_pt',
                'prodi_pt',
                'program_pt',
                'tahun_lulus_pt'
            ]);
        });
    }
};
