<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gudang_transaksi', function (Blueprint $table) {
            $table->string('struk')->nullable()->after('rkat_anggaran_id');
        });
    }

    public function down(): void
    {
        Schema::table('gudang_transaksi', function (Blueprint $table) {
            $table->dropColumn('struk');
        });
    }
};
