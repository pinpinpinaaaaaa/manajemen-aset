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
        Schema::create('jenis_barang', function (Blueprint $table) {
            $table->string('id_jenis_barang', 10)->primary();
            $table->enum('jenis', ['sarana', 'prasarana'])->comment('Jenis utama aset');
            $table->enum('kategori', ['it', 'elektronik', 'non elektronik']);
            $table->string('nama_barang', 100);
            $table->string('prefix_kode', 10)->comment('Contoh: KOM, PRN, MEJ');
            $table->boolean('bisa_dipindah')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_barang');
    }
};
