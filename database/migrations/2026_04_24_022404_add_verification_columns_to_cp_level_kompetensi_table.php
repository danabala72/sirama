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
            $table->boolean('valid')->default(false)->after('level_kompetensi');
            $table->boolean('asli')->default(false)->after('valid');
            $table->boolean('terkini')->default(false)->after('asli');
            $table->boolean('cukup')->default(false)->after('terkini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cp_level_kompetensi', function (Blueprint $table) {
            $table->dropColumn(['valid', 'asli', 'terkini', 'cukup']);
        });
    }
};
