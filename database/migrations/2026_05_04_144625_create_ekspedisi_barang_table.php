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
        Schema::create('ekspedisi_barang', function (Blueprint $table) {
            $table->id();

            $table->string('id_ekspedisi', 20)->index();

            $table->string('nama_barang', 255);
            $table->integer('jumlah')->default(1);
            $table->decimal('berat', 8, 2)->nullable();
            $table->string('satuan', 50)->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->foreign('id_ekspedisi')
                ->references('id_ekspedisi')
                ->on('ekspedisi')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekspedisi_barang');
    }
};
