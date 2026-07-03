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
        Schema::create('permintaan_kendaraan', function (Blueprint $table) {
            $table->string('id_permohonan', 20)->primary();

            $table->string('nama', 100);
            $table->string('email', 100);
            $table->string('id_divisi', 10);

            $table->enum('status', [
                'menunggu konfirmasi','disetujui','ditolak','proses','selesai'
            ])->default('menunggu konfirmasi');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_kendaraan');
    }
};
