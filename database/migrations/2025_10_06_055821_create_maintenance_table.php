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
        Schema::create('maintenance', function (Blueprint $table) {
            $table->string('id_maintenance', 20)->primary();
            $table->string('id_ruangan', 10)->nullable();
            $table->string('id_gedung', 10)->nullable();
            $table->dateTime('tanggal_laporan'); // kapan pertama kali status “perlu perbaikan”
            $table->text('catatan')->nullable();
            $table->enum('decision_status', ['menunggu_persetujuan', 'disetujui', 'ditolak'])->default('menunggu_persetujuan');
            $table->decimal('biaya_total', 15, 2)->nullable();

            $table->string('requested_by', 10);
            $table->string('decided_by', 10)->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
            $table->foreign('id_ruangan')->references('id_ruangan')->on('ruangan')->onDelete('set null');
            $table->foreign('id_gedung')->references('id_gedung')->on('gedung')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};
