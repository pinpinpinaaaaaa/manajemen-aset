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
        Schema::create('gudang_transaksi_detail', function (Blueprint $table) {
            $table->id();

            $table->string('id_transaksi', 20);
            $table->string('id_barang', 10);

            // 🔥 satuan transaksi
            $table->integer('jumlah_input');        // contoh: 3
            $table->string('satuan', 20);           // contoh: dus
            $table->integer('konversi_pakai');
            $table->integer('jumlah');               // hasil konversi (pcs)

            // harga
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);

            $table->timestamps();

            $table->foreign('id_transaksi')
                ->references('id_transaksi')
                ->on('gudang_transaksi')
                ->onDelete('cascade');

            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('gudang_barang')
                ->onDelete('restrict');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gudang_transaksi_detail');
    }
};
