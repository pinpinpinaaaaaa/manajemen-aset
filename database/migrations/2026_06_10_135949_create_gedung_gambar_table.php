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
        Schema::create('gedung_gambar', function (Blueprint $table) {
            $table->id();
            $table->string('id_gedung', 10);
            $table->string('gambar', 255);

            $table->foreign('id_gedung')
                ->references('id_gedung')
                ->on('gedung')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gedung_gambar');
    }
};
