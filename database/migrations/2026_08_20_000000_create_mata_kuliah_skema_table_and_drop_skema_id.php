<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah_skema', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('skema_id')->constrained('skema')->cascadeOnDelete();
            $table->unique(['mata_kuliah_id', 'skema_id']);
            $table->timestamps();
        });

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropForeign(['skema_id']);
            $table->dropColumn('skema_id');
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->foreignId('skema_id')->nullable()->constrained('skema')->nullOnDelete()->after('jurusan_id');
        });

        Schema::dropIfExists('mata_kuliah_skema');
    }
};
