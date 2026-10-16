<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekspedisi', function (Blueprint $table) {
            $table->string('jenis_ekspedisi', 20)->default('mengantar')->after('id_ekspedisi');
            $table->string('cara_penyerahan', 50)->nullable()->after('keterangan');
            $table->string('id_divisi_pengirim', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ekspedisi', function (Blueprint $table) {
            $table->dropColumn(['jenis_ekspedisi', 'cara_penyerahan']);
        });
    }
};
