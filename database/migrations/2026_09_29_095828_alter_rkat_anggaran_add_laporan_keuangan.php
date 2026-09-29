<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rkat_anggaran', function (Blueprint $table) {
            $table->string('laporan_keuangan', 2)->nullable()->after('kode_coa')
                  ->comment('IS = Income Statement, BS = Balance Sheet');
        });
    }

    public function down(): void
    {
        Schema::table('rkat_anggaran', function (Blueprint $table) {
            $table->dropColumn('laporan_keuangan');
        });
    }
};
