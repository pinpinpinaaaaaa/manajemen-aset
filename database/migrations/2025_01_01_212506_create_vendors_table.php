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
        Schema::create('vendors', function (Blueprint $table) {
            $table->string('id_vendor', 10)->primary();
            $table->string('nama_perusahaan');
            $table->text('alamat')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('jabatan_cp')->nullable();
            $table->string('no_telp_cp', 20)->nullable();
            $table->string('email_perusahaan')->nullable();
            $table->string('bidang_usaha')->nullable();
            $table->text('akta')->nullable();
            $table->text('nib')->nullable();
            $table->text('npwp')->nullable();
            $table->text('pakta_integritas')->nullable();
            $table->text('akta_link')->nullable();
            $table->text('nib_link')->nullable();
            $table->text('npwp_link')->nullable();
            $table->text('pakta_integritas_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
