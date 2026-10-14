<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekspedisi', function (Blueprint $table) {
            $table->string('instansi_pengirim')->nullable()->after('id_divisi_pengirim');
            $table->text('alamat_pengirim')->nullable()->after('instansi_pengirim');
        });
    }

    public function down(): void
    {
        Schema::table('ekspedisi', function (Blueprint $table) {
            $table->dropColumn(['instansi_pengirim', 'alamat_pengirim']);
        });
    }
};
