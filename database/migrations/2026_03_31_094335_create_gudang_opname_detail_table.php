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
        Schema::create('gudang_opname_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_opname');
            $table->string('id_barang', 10);

            $table->integer('stok_sistem');
            $table->integer('stok_fisik')->nullable();
            $table->integer('selisih')->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('id_opname')
                ->references('id')
                ->on('gudang_opname_header')
                ->onDelete('cascade');

            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('gudang_barang')
                ->onDelete('cascade');

            $table->unique(['id_opname', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gudang_opname_detail');
    }
};
