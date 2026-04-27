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
        Schema::create('admin_jurusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('no action')
                ->onDelete('cascade');

            $table->foreignId('jurusan_id')
                ->constrained('jurusan')
                ->onUpdate('no action')
                ->onDelete('restrict');

            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('no_hp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_jurusan');
    }
};
