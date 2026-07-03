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
        Schema::create('pengadaan_barang_jasa', function (Blueprint $table) {
            $table->string('id_pengadaan', 20)->primary();

            $table->string('nama_pengaju');
            $table->string('email_pengaju');

            $table->string('id_divisi', 10);
            $table->foreign('id_divisi')->references('id_divisi')->on('divisi');

            $table->text('alasan');
            $table->date('tanggal_kebutuhan');

            $table->decimal('total_biaya', 15, 2)->default(0);

            $table->enum('decision_status', [
                'menunggu_persetujuan',
                'disetujui',
                'ditolak'
            ])->default('menunggu_persetujuan');

            $table->enum('status', [
                'Belum Diproses',
                'Sedang Diproses',
                'Tersedia',
                'Selesai'
            ])->default('Belum Diproses');

            $table->string('decided_by', 10)->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_barang_jasa');
    }
};
