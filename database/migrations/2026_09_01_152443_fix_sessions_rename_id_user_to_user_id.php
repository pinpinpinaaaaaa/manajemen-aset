<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Hapus index dan kolom id_user yang salah nama & tipe (bigint).
            // Tabel sessions berisi data sementara — efek satu-satunya adalah
            // semua session aktif expire (user perlu login ulang).
            $table->dropIndex(['id_user']);
            $table->dropColumn('id_user');

            // Tambah kolom user_id string — sesuai nama yang diharap Laravel
            // session handler DAN sesuai tipe PK users (varchar: U001, U002...).
            $table->string('user_id')->nullable()->index()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('id_user')->nullable()->index()->after('id');
        });
    }
};
