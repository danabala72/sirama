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
            $table->decimal('nilai_angka', 3, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliah_pilihan', function (Blueprint $table) {
            $table->integer('nilai_angka')->change();
        });
    }
};
