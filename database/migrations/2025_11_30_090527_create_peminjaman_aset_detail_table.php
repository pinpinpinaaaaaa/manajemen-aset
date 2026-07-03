<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_aset_detail', function (Blueprint $table) {
            $table->id();
            $table->string('id_peminjaman', 20);
            $table->string('id_aset', 10);
            $table->integer('jumlah')->default(1);

            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman_aset')->onDelete('cascade');
            $table->foreign('id_aset')->references('id_aset')->on('aset')->onDelete('cascade');

            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_dikembalikan')->nullable();

            $table->enum('status_pengembalian', [
                'menunggu','dipinjam','dikembalikan'
            ])->default('menunggu');

            $table->enum('kondisi_kembali', [
                'baik',
                'rusak_ringan',
                'rusak_berat'
            ])->nullable();

            $table->text('catatan_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_aset_detail');
    }
};
