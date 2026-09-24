<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rkat_anggaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun');
            $table->string('kode_kegiatan', 50)->unique();
            $table->string('coa_pos', 100);
            $table->string('coa_sub', 150);
            $table->string('nama_kegiatan', 255);
            $table->decimal('anggaran', 15, 2);
            $table->string('created_by', 10)->nullable();
            $table->foreign('created_by')->references('id_user')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rkat_anggaran');
    }
};
