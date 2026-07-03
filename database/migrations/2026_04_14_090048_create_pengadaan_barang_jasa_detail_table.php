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
        Schema::create('pengadaan_barang_jasa_detail', function (Blueprint $table) {
            $table->id();

            $table->string('id_pengadaan', 20);
            $table->foreign('id_pengadaan')
                ->references('id_pengadaan')
                ->on('pengadaan_barang_jasa')
                ->onDelete('cascade');

            $table->enum('jenis', ['barang', 'jasa']);

            // barang
            $table->string('nama_barang')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe_model')->nullable();
            $table->string('spesifikasi')->nullable();
            $table->integer('jumlah')->nullable();

            // jasa
            $table->string('kategori_jasa')->nullable();

            // umum
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_barang_jasa_detail');
    }
};
