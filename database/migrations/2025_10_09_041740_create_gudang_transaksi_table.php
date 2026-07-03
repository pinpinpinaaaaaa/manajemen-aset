<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudang_transaksi', function (Blueprint $table) {
            $table->string('id_transaksi', 20)->primary();
            $table->date('tanggal');
            $table->enum('jenis_transaksi', ['masuk', 'keluar', 'penyesuaian']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                ->default('pending');

            $table->string('referensi')->nullable();

            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->string('alasan')->nullable();
            $table->string('dibuat_oleh');
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->enum('tipe_penyesuaian', ['tambah', 'kurang'])->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('gudang_transaksi');
    }
};
