<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance', function (Blueprint $table) {
            $table->string('id_pengaduan', 20)->nullable()->after('id_maintenance');
            $table->foreign('id_pengaduan')
                ->references('id_pengaduan')
                ->on('pengaduan_kerusakan')
                ->nullOnDelete();
        });

        // Backfill: sambungkan maintenance yang sudah ada ke pengaduan asalnya
        // lewat maintenance_detail.id_aset → pengaduan_kerusakan_detail.id_aset
        DB::table('maintenance')
            ->whereNull('id_pengaduan')
            ->where('decision_status', 'disetujui')
            ->get(['id_maintenance'])
            ->each(function ($m) {
                $asets = DB::table('maintenance_detail')
                    ->where('id_maintenance', $m->id_maintenance)
                    ->pluck('id_aset');

                if ($asets->isEmpty()) return;

                $pengaduanId = DB::table('pengaduan_kerusakan_detail as pkd')
                    ->join('pengaduan_kerusakan as pk', 'pk.id_pengaduan', '=', 'pkd.id_pengaduan')
                    ->whereIn('pkd.id_aset', $asets)
                    ->where('pk.decision_status', 'disetujui')
                    ->value('pkd.id_pengaduan');

                if ($pengaduanId) {
                    DB::table('maintenance')
                        ->where('id_maintenance', $m->id_maintenance)
                        ->update(['id_pengaduan' => $pengaduanId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('maintenance', function (Blueprint $table) {
            $table->dropForeign(['id_pengaduan']);
            $table->dropColumn('id_pengaduan');
        });
    }
};
