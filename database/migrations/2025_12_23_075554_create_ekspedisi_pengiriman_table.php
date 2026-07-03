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
        Schema::create('ekspedisi_pengiriman', function (Blueprint $table) {
            $table->id();
            $table->string('id_ekspedisi', 20)->unique();

            // ======================
            // JENIS KURIR
            // ======================
            $table->enum('jenis_kurir', ['internal', 'eksternal'])->nullable();

            // INTERNAL
            $table->string('id_kurir_internal', 10)->nullable();

            // EKSTERNAL
            $table->string('nama_jasa_ekspedisi', 100)->nullable();
            $table->string('no_resi', 100)->nullable();

            // ======================
            // WAKTU
            // ======================
            $table->dateTime('waktu_dikirim')->nullable();
            $table->dateTime('waktu_diterima')->nullable();
            $table->dateTime('waktu_selesai')->nullable();

            // ======================
            // PENERIMA
            // ======================
            $table->string('nama_penerima_ttd', 100)->nullable();
            $table->string('jabatan_penerima', 100)->nullable();
            $table->text('catatan_penerimaan')->nullable();

            // ======================
            // BUKTI
            // ======================
            $table->enum('jenis_ttd', [
                'scan',
                'digital'
            ])->nullable();
            $table->longText('ttd_digital')->nullable();
            $table->string('ttd_file', 255)->nullable();
            $table->string('foto_bukti', 255)->nullable();

            // ======================
            // STATUS
            // ======================
            $table->enum('status_pengiriman', [
                'belum_dikirim',
                'dikirim',
                'diterima',
                'selesai'
            ])->default('belum_dikirim');

            $table->timestamps();

            $table->foreign('id_ekspedisi')
                ->references('id_ekspedisi')
                ->on('ekspedisi')
                ->onDelete('cascade');

            $table->foreign('id_kurir_internal')
                ->references('id_user')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekspedisi_pengiriman');
    }
};
