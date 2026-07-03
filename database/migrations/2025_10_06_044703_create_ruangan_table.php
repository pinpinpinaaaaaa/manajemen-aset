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
        Schema::create('ruangan', function (Blueprint $table) {
            $table->string('id_ruangan',10 )->primary();
            $table->string('id_gedung', 10);
            $table->enum('kategori', ['interior', 'eksterior'])->nullable();
            $table->string('lantai', 20)->nullable();
            $table->string('nama_ruangan', 100);
            $table->enum('status', ['tersedia', 'terpakai', 'non aktif', 'maintenance'])->default('tersedia');

            // optional: foreign key
            $table->foreign('id_gedung')->references('id_gedung')->on('gedung')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
