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
        Schema::create('roles', function (Blueprint $table) {
            $table->string('id_role', 10)->primary();
            $table->string('nama_role', 50)->unique();

            /*
            | menu:
            | null  => role dapat SEMUA menu
            | json  => whitelist menu id, contoh: [1,2,3]
            */
            $table->json('menu')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
