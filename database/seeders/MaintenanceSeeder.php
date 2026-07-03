<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ================= HEADER MAINTENANCE =================
        DB::table('maintenance')->insert([
            [
                'id_maintenance' => 'MNT1001',
                'id_gedung' => 'G002',
                'id_ruangan' => 'R083',

                'tanggal_laporan' => $now->copy()->subDays(10),
                'catatan' => 'Menunggu perbaikan tukang.',
                'decision_status' => 'menunggu_persetujuan',
                'biaya_total' => 0,

                'requested_by' => 'U002',
                'decided_by' => null,
                'decided_at' => null,

                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'id_maintenance' => 'MNT3001',
                'id_gedung' => 'G002',
                'id_ruangan' => 'R079',

                'tanggal_laporan' => $now->copy()->subDays(30),
                'catatan' => 'Penggantian ballast berhasil.',
                'decision_status' => 'disetujui',
                'biaya_total' => 35000,

                'requested_by' => 'U002',
                'decided_by' => 'U001',
                'decided_at' => $now->copy()->subDays(29),

                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);


        // ================= DETAIL MAINTENANCE =================
        DB::table('maintenance_detail')->insert([
            [
                'id_maintenance' => 'MNT1001',
                'id_aset' => 'A0005',

                'tanggal_mulai' => $now->copy()->subDays(9),
                'tanggal_selesai' => null,
                'durasi_jam' => null,

                'kerusakan' => 'Gypsum retak bagian tengah.',
                'biaya' => 0,

                'foto_before' => 'maintenance_before/MNT1001_before.jpg',
                'foto_after' => null,

                'catatan' => 'Menunggu perbaikan tukang.',
                'status' => 'Perlu Perbaikan',

                'kelayakan_awal' => 4,
                'keterangan_awal' => 'Retak ringan',
                'status_aset_awal' => 'Aktif',

                'lampiran' => null,
                'pelaksana_type' => 'internal',
                'id_vendor' => null,

                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'id_maintenance' => 'MNT3001',
                'id_aset' => 'A0003',

                'tanggal_mulai' => $now->copy()->subDays(29),
                'tanggal_selesai' => $now->copy()->subDays(27),
                'durasi_jam' => 10,

                'kerusakan' => 'Lampu redup dan berkedip.',
                'biaya' => 35000,

                'foto_before' => 'maintenance_before/MNT3001_before.jpg',
                'foto_after' => 'maintenance_after/MNT3001_after.jpg',

                'catatan' => 'Penggantian ballast berhasil.',
                'status' => 'Selesai',

                'kelayakan_awal' => 2,
                'keterangan_awal' => 'Hampir rusak',
                'status_aset_awal' => 'Aktif',

                'lampiran' => null,
                'pelaksana_type' => 'vendor',
                'id_vendor' => null,

                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}