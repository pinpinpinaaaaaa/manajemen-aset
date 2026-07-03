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
        Schema::create('permintaan_kendaraan_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('detail_id')
                ->constrained('permintaan_kendaraan_detail')
                ->cascadeOnDelete();

            $table->string('id_kendaraan', 10);

            $table->timestamps();

            $table->foreign('id_kendaraan')
                ->references('id_kendaraan')
                ->on('kendaraan')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_kendaraan_items');
    }
};
