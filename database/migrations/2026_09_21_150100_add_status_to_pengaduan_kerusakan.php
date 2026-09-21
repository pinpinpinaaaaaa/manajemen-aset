<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan_kerusakan', function (Blueprint $table) {
            $table->enum('status', [
                'Belum Diproses',
                'Sedang Diproses',
                'Selesai',
            ])->default('Belum Diproses')->after('decision_status');
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan_kerusakan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
