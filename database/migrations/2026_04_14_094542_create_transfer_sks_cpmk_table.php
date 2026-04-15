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
        Schema::create('transfer_sks_cpmk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_sks_id')->constrained('transfer_sks')->onDelete('cascade');
            $table->text('cpmk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_sks_cpmk');
    }
};
