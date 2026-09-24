<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ALTER rkat_anggaran — tambah 12 kolom rencana bulanan.
 * Aditif sepenuhnya, tidak menyentuh kolom/data yang sudah ada.
 *
 * Aturan bisnis (di-enforce di controller, bukan di sini):
 *   SUM(rencana_jan..rencana_des) TIDAK BOLEH MELEBIHI anggaran.
 *   Boleh lebih kecil (sisa ditampilkan di UI sebagai "belum dialokasikan").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rkat_anggaran', function (Blueprint $table) {
            $table->decimal('rencana_jan', 15, 2)->default(0)->after('anggaran');
            $table->decimal('rencana_feb', 15, 2)->default(0)->after('rencana_jan');
            $table->decimal('rencana_mar', 15, 2)->default(0)->after('rencana_feb');
            $table->decimal('rencana_apr', 15, 2)->default(0)->after('rencana_mar');
            $table->decimal('rencana_mei', 15, 2)->default(0)->after('rencana_apr');
            $table->decimal('rencana_jun', 15, 2)->default(0)->after('rencana_mei');
            $table->decimal('rencana_jul', 15, 2)->default(0)->after('rencana_jun');
            $table->decimal('rencana_agu', 15, 2)->default(0)->after('rencana_jul');
            $table->decimal('rencana_sep', 15, 2)->default(0)->after('rencana_agu');
            $table->decimal('rencana_okt', 15, 2)->default(0)->after('rencana_sep');
            $table->decimal('rencana_nov', 15, 2)->default(0)->after('rencana_okt');
            $table->decimal('rencana_des', 15, 2)->default(0)->after('rencana_nov');
        });
    }

    public function down(): void
    {
        Schema::table('rkat_anggaran', function (Blueprint $table) {
            $table->dropColumn([
                'rencana_jan', 'rencana_feb', 'rencana_mar', 'rencana_apr',
                'rencana_mei', 'rencana_jun', 'rencana_jul', 'rencana_agu',
                'rencana_sep', 'rencana_okt', 'rencana_nov', 'rencana_des',
            ]);
        });
    }
};
