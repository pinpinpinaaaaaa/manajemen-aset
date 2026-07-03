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
        Schema::create('aset', function (Blueprint $table) {
            $table->string('id_aset', 10)->primary();
            $table->string('kode_aset', 50)->unique()->comment('Kode barang fisik, misalnya MJ001, KR002');
            $table->string('nama_aset', 100);
            $table->string('merk', 100)->nullable();
            $table->string('tipe_model', 100)->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('id_jenis_barang', 10)->nullable();
            $table->string('id_gedung', 10);
            $table->string('id_ruangan', 10);
            $table->year('tahun_perolehan')->nullable();
            $table->decimal('nilai', 15, 2)->nullable();
            $table->tinyInteger('kelayakan')->check('kelayakan between 1 and 5');
            $table->enum('keterangan_kelayakan', [
                'Layak',
                'Perlu pemantauan',
                'Perlu perbaikan',
                'Lelang',
                'Hibahkan',
                'Dijual',
                'Dimusnahkan'
            ])->comment('Status kondisi fisik aset');
            $table->enum('status', ['tersedia', 'terpakai', 'non aktif', 'maintenance'])->default('tersedia')->comment('Status ketersediaan aset')->nullable();
            $table->string('foto', 255)->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->string('last_checked_by', 50)->nullable();

            // Relasi ke tabel lain
            $table->foreign('id_gedung')->references('id_gedung')->on('gedung')->onDelete('cascade');
            $table->foreign('id_ruangan')->references('id_ruangan')->on('ruangan')->onDelete('cascade');
            $table->foreign('id_jenis_barang')->references('id_jenis_barang')->on('jenis_barang')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
