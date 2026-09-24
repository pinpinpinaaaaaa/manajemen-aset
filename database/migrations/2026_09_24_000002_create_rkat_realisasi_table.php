<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rkat_realisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rkat_anggaran_id')->constrained('rkat_anggaran')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->string('sumber_type', 100)->nullable();
            $table->string('sumber_id', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rkat_realisasi');
    }
};
