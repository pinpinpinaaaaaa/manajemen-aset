<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudang_barang', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id_barang', 10)->primary();
            $table->string('nama_barang', 100);
            $table->enum('jenis', ['atk', 'rt']); // ATK / Rumah Tangga

            // satuan
            $table->string('satuan', 20)->default('pcs');        // satuan yang dipilih user
            $table->integer('konversi_satuan')->default(1);      // pack isi berapa?
            $table->string('satuan_dasar', 20)->default('pcs');  // penyimpanan stok dalam unit apa?

            // stok
            $table->integer('limit_stok')->default(0);
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);
            $table->integer('stok_akhir')->default(0);
            $table->integer('stok_dipesan')->default(0);

            // opsional
            $table->string('foto_produk', 255)->nullable();
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gudang_barang');
    }
};
