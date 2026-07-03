<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_ruangan_aset', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('detail_id');
            $table->foreign('detail_id')
                ->references('id')
                ->on('peminjaman_ruangan_detail')
                ->onDelete('cascade');

            $table->string('id_aset', 10)->nullable();    
            $table->integer('jumlah')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_ruangan_aset');
    }
};
