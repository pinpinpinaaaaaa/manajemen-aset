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
        Schema::create('pengaduan_kerusakan_detail', function (Blueprint $table) {

            $table->id();

            $table->string('id_pengaduan',20);

            $table->string('id_aset',10)->nullable();

            $table->text('keluhan');

            $table->enum('kategori_kerusakan',[
                'ringan',
                'sedang',
                'berat'
            ]);

            $table->string('foto',255)->nullable();

            $table->timestamps();

            $table->foreign('id_pengaduan')
                ->references('id_pengaduan')
                ->on('pengaduan_kerusakan')
                ->cascadeOnDelete();

            $table->foreign('id_aset')
                ->references('id_aset')
                ->on('aset')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan_kerusakan_detail');
    }
};
