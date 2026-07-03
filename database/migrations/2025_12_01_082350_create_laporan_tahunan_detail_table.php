<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_tahunan_detail', function (Blueprint $table) {
            $table->id();
            $table->string('id_laporan_tahunan', 12);
            $table->enum('jenis', ['Pengadaan', 'Maintenance', 'Pemusnahan']);
            $table->string('id_referensi', 20); // id_maintenance atau id_pemusnahan
            $table->timestamps();

            $table->foreign('id_laporan_tahunan')
                ->references('id_laporan_tahunan')
                ->on('laporan_tahunan')
                ->onDelete('cascade'); // jika header dihapus → detail ikut hilang
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_tahunan_detail');
    }
};
