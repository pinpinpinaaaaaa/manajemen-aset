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
        Schema::create('pemindahan_aset', function (Blueprint $table) {
            $table->string('id_pemindahan', 20)->primary();
            $table->text('alasan')->nullable();
            $table->enum('decision_status', ['menunggu_persetujuan', 'disetujui', 'ditolak'])->default('menunggu_persetujuan');
            $table->string('requested_by', 10);
            $table->string('decided_by', 10)->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemindahan_aset');
    }
};
