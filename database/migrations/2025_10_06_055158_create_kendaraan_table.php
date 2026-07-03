<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {

            $table->string('id_kendaraan', 10)->primary();

            $table->enum('jenis_kendaraan', ['roda 2', 'roda 4']);
            $table->enum('tipe', ['motor', 'mobil']);
            $table->string('plat_nomor', 20)->unique();
            $table->integer('tahun_pembelian');
            $table->integer('umur_ekonomis');

            $table->string('merk', 50);
            $table->string('model', 50);        // contoh: Avanza, Beat
            $table->string('spesifikasi')->nullable();

            $table->string('no_rangka')->nullable()->unique();
            $table->string('no_mesin')->nullable()->unique();

            $table->enum('status_kondisi', ['aktif', 'perbaikan', 'non aktif']);
            $table->enum('status_penggunaan', ['tersedia', 'terpakai'])->default('tersedia');

            $table->string('foto')->nullable();

            $table->string('driver_id', 10)->nullable();

            $table->foreign('driver_id')
                ->references('id_user')
                ->on('users')
                ->nullOnDelete();


            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
