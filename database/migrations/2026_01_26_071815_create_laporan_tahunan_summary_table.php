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
        Schema::create('laporan_tahunan_summary', function (Blueprint $table) {
            $table->id();
            $table->string('id_laporan_tahunan', 12);

            // === ASET ===
            $table->integer('total_pengadaan')->default(0);

            // === MAINTENANCE ===
            $table->integer('total_maintenance')->default(0);

            // === PEMUSNAHAN ===
            $table->integer('total_pemusnahan')->default(0);

            // === GUDANG ===
            $table->integer('stok_awal_tahun')->default(0);
            $table->integer('stok_akhir_tahun')->default(0);
            $table->integer('total_transaksi_gudang')->default(0);

            $table->timestamps();

            $table->foreign('id_laporan_tahunan')
                ->references('id_laporan_tahunan')
                ->on('laporan_tahunan')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_tahunan_summary');
    }
};
