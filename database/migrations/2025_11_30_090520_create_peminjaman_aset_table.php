<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_aset', function (Blueprint $table) {
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

            // Alasan & catatan global
            $table->text('alasan');
            $table->text('catatan')->nullable();

            // Status approval
            $table->enum('decision_status', [
                'menunggu_persetujuan',
                'disetujui',
                'ditolak'
            ])->default('menunggu_persetujuan');

            $table->string('decided_by', 10)->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->text('lampiran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_aset');
    }
};
