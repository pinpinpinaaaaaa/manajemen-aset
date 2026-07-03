<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_ruangan_detail', function (Blueprint $table) {
            $table->id();

            $table->string('id_peminjaman', 20);
            $table->foreign('id_peminjaman')->references('id_peminjaman')
                  ->on('peminjaman_ruangan')->onDelete('cascade');

            $table->string('id_gedung', 10);
            $table->string('id_ruangan', 10);
            
            // Tanggal & Waktu
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('jam_mulai');
            $table->time('jam_selesai');

            $table->text('catatan')->nullable();
            // validasi ruangan yang tersedia dilakukan di controller

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_ruangan_detail');
    }
};
