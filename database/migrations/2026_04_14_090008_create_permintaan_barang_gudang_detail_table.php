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
        Schema::create('permintaan_barang_gudang_detail', function (Blueprint $table) {
            $table->id();

            $table->string('id_permintaan', 20);
            $table->foreign('id_permintaan')
                ->references('id_permintaan')
                ->on('permintaan_barang_gudang')
                ->onDelete('cascade');

            $table->string('id_barang', 10);
            $table->integer('jumlah');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_barang_gudang_detail');
    }
};
