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
        Schema::table('peminjaman_aset', function (Blueprint $table) {
            $table->enum('status', ['Belum Diproses', 'Sedang Diproses', 'Selesai'])
                ->default('Belum Diproses')
                ->after('decision_status');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman_aset', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
