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
        Schema::create('gudang_transaksi_log', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi', 20);
            $table->foreign('id_transaksi')
                ->references('id_transaksi')
                ->on('gudang_transaksi')
                ->onDelete('cascade');
            $table->string('aksi');
            $table->string('user');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gudang_transaksi_log');
    }
};
