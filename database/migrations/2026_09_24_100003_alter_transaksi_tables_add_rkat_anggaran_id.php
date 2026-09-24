<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ALTER 4 tabel transaksi — tambah kolom rkat_anggaran_id (nullable FK).
 * Aditif. Tidak mengubah/menghapus kolom atau data yang sudah ada.
 *
 * onDelete: SET NULL (nullOnDelete) — supaya transaksi TIDAK ikut terhapus
 * jika pos anggaran dihapus. Transaksi akan jadi "belum dialokasikan"
 * (rkat_anggaran_id = null) yang bisa dialokasikan ulang.
 *
 * Nullable karena pemilihan pos RKAT bersifat OPSIONAL di awal (pengajuan),
 * dan baru WAJIB saat transaksi ditandai Selesai.
 */
return new class extends Migration
{
    public function up(): void
    {
        // maintenance (PK string: id_maintenance)
        Schema::table('maintenance', function (Blueprint $table) {
            $table->unsignedBigInteger('rkat_anggaran_id')
                ->nullable()
                ->after('id_maintenance');

            $table->foreign('rkat_anggaran_id')
                ->references('id')
                ->on('rkat_anggaran')
                ->nullOnDelete();
        });

        // pengadaan_barang_jasa (PK string: id_pengadaan)
        Schema::table('pengadaan_barang_jasa', function (Blueprint $table) {
            $table->unsignedBigInteger('rkat_anggaran_id')
                ->nullable()
                ->after('id_pengadaan');

            $table->foreign('rkat_anggaran_id')
                ->references('id')
                ->on('rkat_anggaran')
                ->nullOnDelete();
        });

        // laporan_pemusnahan (PK string: id_pemusnahan)
        Schema::table('laporan_pemusnahan', function (Blueprint $table) {
            $table->unsignedBigInteger('rkat_anggaran_id')
                ->nullable()
                ->after('id_pemusnahan');

            $table->foreign('rkat_anggaran_id')
                ->references('id')
                ->on('rkat_anggaran')
                ->nullOnDelete();
        });

        // gudang_transaksi (PK string: id_transaksi)
        Schema::table('gudang_transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('rkat_anggaran_id')
                ->nullable()
                ->after('id_transaksi');

            $table->foreign('rkat_anggaran_id')
                ->references('id')
                ->on('rkat_anggaran')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance', function (Blueprint $table) {
            $table->dropForeign(['rkat_anggaran_id']);
            $table->dropColumn('rkat_anggaran_id');
        });

        Schema::table('pengadaan_barang_jasa', function (Blueprint $table) {
            $table->dropForeign(['rkat_anggaran_id']);
            $table->dropColumn('rkat_anggaran_id');
        });

        Schema::table('laporan_pemusnahan', function (Blueprint $table) {
            $table->dropForeign(['rkat_anggaran_id']);
            $table->dropColumn('rkat_anggaran_id');
        });

        Schema::table('gudang_transaksi', function (Blueprint $table) {
            $table->dropForeign(['rkat_anggaran_id']);
            $table->dropColumn('rkat_anggaran_id');
        });
    }
};
