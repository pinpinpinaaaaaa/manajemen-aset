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
        Schema::create('ekspedisi', function (Blueprint $table) {
            $table->string('id_ekspedisi', 20)->primary();

            // DATA PEMOHON
            $table->string('nama_pengaju');
            $table->string('email_pengaju');
            $table->string('id_divisi_pengaju', 10)->index();
            $table->foreign('id_divisi_pengaju')
                ->references('id_divisi')
                ->on('divisi')
                ->onDelete('restrict');

            // DATA PENGIRIM
            $table->string('nama_pengirim', 100);
            $table->string('email_pengirim', 100)->nullable();
            $table->string('no_hp_pengirim', 20)->nullable();
            $table->string('id_divisi_pengirim', 10)->index();

            // DATA PENERIMA
            $table->string('nama_penerima', 100);
            $table->string('email_penerima', 100)->nullable();
            $table->string('no_hp_penerima', 20)->nullable();
            $table->string('instansi_penerima', 150)->nullable();
            $table->text('alamat_penerima');

            // KONTEKS
            $table->string('judul_kegiatan', 255);
            $table->text('keterangan')->nullable();

            // STATUS GLOBAL
            $table->enum('status_approval', [
                'draft',
                'menunggu',
                'disetujui',
                'ditolak'
            ])->default('draft');

            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->foreign('id_divisi_pengirim')
                ->references('id_divisi')->on('divisi')
                ->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekspedisi');
    }
};
