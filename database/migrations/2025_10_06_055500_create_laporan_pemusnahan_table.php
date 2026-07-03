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
        Schema::create('laporan_pemusnahan', function (Blueprint $table) {
            $table->string('id_pemusnahan', 20)->primary();
            $table->string('id_aset',10)->nullable();
            $table->string('id_ruangan', 10)->nullable();
            $table->string('id_gedung', 10)->nullable();
            $table->date('tanggal_pemusnahan');
            $table->string('metode', 50);
            $table->enum('decision_status', ['menunggu_persetujuan', 'disetujui', 'ditolak'])->default('menunggu_persetujuan');
            $table->enum('status', ['Belum Dimusnahkan', 'Sedang Dimusnahkan', 'Selesai'])->default('Belum Dimusnahkan');
            $table->text('catatan')->nullable();
            $table->decimal('biaya_keluar', 15, 2)->default(0);
            $table->decimal('nilai_masuk', 15, 2)->default(0);
            $table->string('requested_by', 10);
            $table->string('decided_by', 10)->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('lampiran')->nullable();
            $table->enum('pelaksana_type', ['vendor', 'internal', 'lainnya'])->nullable();
            $table->string('id_vendor', 10)->nullable();
            $table->timestamps();
            
            $table->foreign('id_vendor')
                ->references('id_vendor')
                ->on('vendors')
                ->onDelete('set null');
            // Relasi opsional (kalau nanti id_aset terhubung ke tabel aset)
            $table->foreign('id_aset')->references('id_aset')->on('aset')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pemusnahan');
    }
};
