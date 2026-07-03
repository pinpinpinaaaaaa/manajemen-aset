<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemindahan_aset_detail', function (Blueprint $table) {

            $table->id();

            $table->string('id_pemindahan', 20);
            $table->string('id_aset', 10);
            // lokasi lama
            $table->string('from_gedung', 10);
            $table->string('from_ruangan', 10);

            // lokasi tujuan
            $table->string('to_gedung', 10);
            $table->string('to_ruangan', 10);
            $table->enum('status', ['Belum dipindahkan', 'Sudah dipindahkan'])->default('Belum dipindahkan');

            $table->text('lampiran')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);

            $table->timestamps();
            $table->enum('pelaksana_type', ['vendor', 'internal', 'lainnya'])->nullable();
            $table->string('id_vendor', 10)->nullable();

            $table->foreign('id_vendor')
                ->references('id_vendor')
                ->on('vendors')
                ->onDelete('set null');


            $table->foreign('id_aset')->references('id_aset')->on('aset');

            $table->foreign('id_pemindahan')
                ->references('id_pemindahan')
                ->on('pemindahan_aset')
                ->onDelete('cascade');

            $table->unique([
                'id_pemindahan',
                'id_aset'
            ]);
            $table->text('catatan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemindahan_aset_detail');
    }
};