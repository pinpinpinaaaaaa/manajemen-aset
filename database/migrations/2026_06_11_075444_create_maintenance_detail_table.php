<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_detail', function (Blueprint $table) {

            $table->id();

            $table->string('id_maintenance', 20);
            $table->string('id_aset', 10);

            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->integer('durasi_jam')->nullable();
            $table->text('kerusakan')->nullable();
            $table->decimal('biaya', 15, 2)->nullable();
            $table->string('foto_before', 255)->nullable();
            $table->string('foto_after', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['Perlu Perbaikan', 'Sedang Diperbaiki', 'Selesai'])->default('Perlu Perbaikan');
            $table->tinyInteger('kelayakan_awal')->nullable();
            $table->string('keterangan_awal')->nullable();
            $table->string('status_aset_awal')->nullable();
            $table->text('lampiran')->nullable();
            $table->enum('pelaksana_type', ['vendor', 'internal', 'lainnya'])->nullable();
            $table->string('id_vendor', 10)->nullable();

            $table->timestamps();
            $table->foreign('id_vendor')
                ->references('id_vendor')
                ->on('vendors')
                ->onDelete('set null');

            $table->foreign('id_maintenance')
                ->references('id_maintenance')
                ->on('maintenance')
                ->onDelete('cascade');

            $table->foreign('id_aset')
                ->references('id_aset')
                ->on('aset')
                ->onDelete('cascade');

            $table->unique([
                'id_maintenance',
                'id_aset'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_detail');
    }
};