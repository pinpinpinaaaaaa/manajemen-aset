<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('divisi', function (Blueprint $table) {
            $table->string('id_divisi',10)->primary();  // contoh: DIV001
            $table->string('nama_divisi');
            $table->string('kode_divisi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisi');
    }
};
