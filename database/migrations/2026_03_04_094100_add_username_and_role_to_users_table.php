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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'username');
            $table->string('email')->nullable()->change();
            $table->foreignId('role_id')->after('id')->constrained('roles');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('users', function (Blueprint $table) {          
            $table->dropUnique(['username']); 
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
            $table->string('email')->nullable(false)->change();
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
