<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_log', function (Blueprint $table) {
            $table->id();

            $table->string('id_aset', 10);

            $table->string('tipe', 50);

            $table->string('ref_id', 20)->nullable();
            $table->text('keterangan')->nullable();

            $table->dateTime('tanggal_kejadian');

            $table->string('id_user', 10)->nullable();
            $table->timestamps();

            $table->foreign('id_aset')
                ->references('id_aset')
                ->on('aset')
                ->onDelete('cascade');
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('aset_log');
    }
};
