<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_ruangan', function (Blueprint $table) {
            $table->string('id_peminjaman', 20)->primary();

            // Data pemohon
            $table->string('nama_pengaju');
            $table->string('email_pengaju');

            // Divisi
            $table->string('id_divisi', 10);
            $table->foreign('id_divisi')
                ->references('id_divisi')
                ->on('divisi')
                ->onDelete('restrict');

            // Jenis kegiatan
            $table->enum('jenis_kegiatan', ['rapat', 'pelatihan', 'asasmen','lainnya']);

            // Jika pelatihan → wajib isi nama kegiatan
            $table->string('nama_kegiatan')->nullable();

            // Jika rapat → wajib isi peserta
            $table->text('peserta_rapat')->nullable();

            // Catatan (layout kursi, dekor, dll)
            $table->text('catatan')->nullable();

            // Status peminjaman
            $table->enum('decision_status', [
                'menunggu_persetujuan',
                'disetujui',
                'ditolak'
            ])->default('menunggu_persetujuan');

            $table->enum('status', [
                'Belum Diproses',
                'Sedang Diproses',
                'Sudah Tersedia',
                'Selesai'
            ])->default('Belum Diproses');

            $table->string('decided_by', 10)->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->text('lampiran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_ruangan');
    }
};
