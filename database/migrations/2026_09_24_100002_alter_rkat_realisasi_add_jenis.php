<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ALTER rkat_realisasi — tambah kolom jenis enum('keluar','masuk').
 * Aditif. Default 'keluar' agar baris lama (kalau ada) tetap valid.
 *
 * 'keluar' = pengeluaran (maintenance, pengadaan, gudang)
 * 'masuk'  = pemasukan (hasil pemusnahan aset dijual, dll.)
 *            dicatat terpisah, bukan offset dari realisasi keluar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rkat_realisasi', function (Blueprint $table) {
            $table->enum('jenis', ['keluar', 'masuk'])
                ->default('keluar')
                ->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('rkat_realisasi', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
