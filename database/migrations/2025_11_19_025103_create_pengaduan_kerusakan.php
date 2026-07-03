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
        Schema::create('pengaduan_kerusakan', function (Blueprint $table) {

            $table->string('id_pengaduan',20)->primary();

            // data pelapor
            $table->string('nama_pelapor',100);
            $table->string('email_pelapor')->nullable();

            $table->string('id_divisi',10);

            // lokasi laporan
            $table->string('id_gedung',10);
            $table->string('id_ruangan',10);

            $table->enum('decision_status',[
                'menunggu_persetujuan',
                'disetujui',
                'ditolak'
            ])->default('menunggu_persetujuan');

            $table->string('decided_by',10)->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            $table->foreign('id_divisi')->references('id_divisi')->on('divisi');

            $table->foreign('id_gedung')
                ->references('id_gedung')
                ->on('gedung')
                ->cascadeOnDelete();

            $table->foreign('id_ruangan')
                ->references('id_ruangan')
                ->on('ruangan')
                ->cascadeOnDelete();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan_kerusakan');
    }
};
