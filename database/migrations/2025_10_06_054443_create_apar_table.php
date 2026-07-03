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
        Schema::create('apar', function (Blueprint $table) {
            $table->string('id_apar', 10)->primary();
            $table->string('id_gedung',10);
            $table->string('id_ruangan',10);
            $table->date('expired_date')->nullable();
            $table->enum('jenis', ['refill', 'sekali pakai', 'hydran'])->nullable();
            $table->date('tanggal_refill')->nullable();
            $table->string('ukuran', 20)->nullable();
            $table->string('merk', 100)->nullable();
            $table->string('media_isi', 100)->nullable();
            $table->string('keterangan', 100)->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->string('foto', 255)->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->string('last_checked_by', 10)->nullable();
            

            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_by', 10)->nullable();

            // Relasi opsional
            $table->foreign('id_gedung')->references('id_gedung')->on('gedung')->onDelete('cascade');
            $table->foreign('id_ruangan')->references('id_ruangan')->on('ruangan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apar');
    }
};
