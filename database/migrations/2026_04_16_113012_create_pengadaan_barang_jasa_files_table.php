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
        Schema::create('pengadaan_barang_jasa_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_detail');

            $table->foreign('id_detail')
                ->references('id')
                ->on('pengadaan_barang_jasa_detail')
                ->onDelete('cascade');

            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaan_barang_jasa_files');
    }
};
