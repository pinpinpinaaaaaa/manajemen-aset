<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudang_rekap_bulanan', function (Blueprint $table) {
            $table->string('id_rekap', 15)->primary();
            $table->string('id_barang', 10);
            $table->tinyInteger('bulan');
            $table->integer('tahun');

            $table->integer('stok_awal')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);
            $table->integer('stok_akhir')->default(0);

            $table->timestamps();

            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('gudang_barang')
                ->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('gudang_rekap_bulanan');
    }
};
