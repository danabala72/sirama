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
        Schema::table('transfer_sks', function (Blueprint $table) {
            $table->text('kesenjangan')->nullable()->after('nama_mk_asal');
            $table->integer('hasil')->nullable()->after('kesenjangan');
            $table->text('catatan_asesor')->nullable()->after('hasil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_sks', function (Blueprint $table) {
            $table->dropColumn(['kesenjangan', 'hasil', 'catatan_asesor']);
        });
    }
};
