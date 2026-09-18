<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika kolom id_user tidak ada (tabel sudah benar sejak awal), skip semua.
        if (! Schema::hasColumn('sessions', 'id_user')) {
            // Pastikan user_id ada — kalau sudah ada ini no-op.
            if (! Schema::hasColumn('sessions', 'user_id')) {
                Schema::table('sessions', function (Blueprint $table) {
                    $table->string('user_id')->nullable()->index()->after('id');
                });
            }
            return;
        }

        // id_user ada → lakukan rename ke user_id.
        // Kosongkan sessions dulu (data sementara, user cukup login ulang).
        DB::table('sessions')->truncate();

        Schema::table('sessions', function (Blueprint $table) {
            // Drop index sebelum drop kolom — bungkus try/catch kalau index
            // ternyata sudah tidak ada (defensive).
            try {
                $table->dropIndex(['id_user']);
            } catch (\Throwable) {
                // Index tidak ada — lanjut.
            }
            $table->dropColumn('id_user');
        });

        if (! Schema::hasColumn('sessions', 'user_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->string('user_id')->nullable()->index()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('sessions', 'user_id')) {
            return;
        }

        DB::table('sessions')->truncate();

        Schema::table('sessions', function (Blueprint $table) {
            try {
                $table->dropIndex(['user_id']);
            } catch (\Throwable) {
                // Index tidak ada — lanjut.
            }
            $table->dropColumn('user_id');
        });

        if (! Schema::hasColumn('sessions', 'id_user')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->unsignedBigInteger('id_user')->nullable()->index()->after('id');
            });
        }
    }
};
