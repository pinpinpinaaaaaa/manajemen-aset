<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman_ruangan', function (Blueprint $table) {
            $table->unsignedBigInteger('rkat_anggaran_id')->nullable()->after('lampiran');
            $table->decimal('biaya_konsumsi', 15, 2)->nullable()->after('rkat_anggaran_id');
            $table->foreign('rkat_anggaran_id')->references('id')->on('rkat_anggaran')->nullOnDelete();
        });

        Schema::table('ekspedisi', function (Blueprint $table) {
            $table->unsignedBigInteger('rkat_anggaran_id')->nullable()->after('decision_status');
            $table->decimal('biaya_pengiriman', 15, 2)->nullable()->after('rkat_anggaran_id');
            $table->foreign('rkat_anggaran_id')->references('id')->on('rkat_anggaran')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman_ruangan', function (Blueprint $table) {
            $table->dropForeign(['rkat_anggaran_id']);
            $table->dropColumn(['rkat_anggaran_id', 'biaya_konsumsi']);
        });

        Schema::table('ekspedisi', function (Blueprint $table) {
            $table->dropForeign(['rkat_anggaran_id']);
            $table->dropColumn(['rkat_anggaran_id', 'biaya_pengiriman']);
        });
    }
};
