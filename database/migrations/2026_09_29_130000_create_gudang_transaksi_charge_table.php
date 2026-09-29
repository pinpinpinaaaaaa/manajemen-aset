<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudang_transaksi_charge', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi', 20);
            $table->string('nama', 100);
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_transaksi')
                  ->references('id_transaksi')
                  ->on('gudang_transaksi')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gudang_transaksi_charge');
    }
};
