<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_ruangan_konsumsi', function (Blueprint $table) {
            $table->id();

            $table->string('id_peminjaman', 20);
            $table->foreign('id_peminjaman')->references('id_peminjaman')
                  ->on('peminjaman_ruangan')->onDelete('cascade');

            $table->enum('jenis_konsumsi', [
                'air_mineral',
                'makanan_ringan',
                'makanan_berat'
            ]);

            $table->integer('jumlah')->default(0);
            $table->text('catatan')->nullable(); // misal nama makanan

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_ruangan_konsumsi');
    }
};
