<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rkat_anggaran', function (Blueprint $table) {
            $table->string('kode_coa', 30)->nullable()->after('tahun');
        });
    }

    public function down(): void
    {
        Schema::table('rkat_anggaran', function (Blueprint $table) {
            $table->dropColumn('kode_coa');
        });
    }
};
