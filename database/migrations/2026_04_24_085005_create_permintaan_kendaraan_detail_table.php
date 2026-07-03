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
        Schema::create('permintaan_kendaraan_detail', function (Blueprint $table) {
            $table->id();

            $table->string('id_permohonan', 20);

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('jam_mulai');
            $table->time('jam_selesai');

            $table->string('keperluan');
            $table->string('tempat_jemput');
            $table->string('tempat_tujuan');
            $table->integer('jumlah')->default(1);

            $table->text('catatan')->nullable();
            $table->string('surat_tugas')->nullable();

            $table->timestamps();

            $table->foreign('id_permohonan')
                ->references('id_permohonan')
                ->on('permintaan_kendaraan')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_kendaraan_detail');
    }
};
