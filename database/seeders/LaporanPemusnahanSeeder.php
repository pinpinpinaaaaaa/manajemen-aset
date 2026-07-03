<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPemusnahanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('laporan_pemusnahan')->insert([
            [
                'id_pemusnahan' => 'PMN0001',
                'id_aset' => 'A0007',

                'id_gedung' => 'G003',
                'id_ruangan' => 'R119',

                'tanggal_pemusnahan' => $now->copy()->subDays(7)->toDateString(),
                'metode' => 'Lelang',

                'decision_status' => 'disetujui',
                'status' => 'Selesai',

                'catatan' => 'Dilelang untuk mengurangi biaya penyimpanan.',
                
                // ✅ FIX FIELD
                'biaya_keluar' => 150000,
                'nilai_masuk'  => 500000,

                'requested_by' => 'U002',
                'decided_by'  => 'U001',
                'decided_at'  => $now->copy()->subDays(8),

                'lampiran' => null,

                // ✅ INTERNAL (sesuai permintaan)
                'pelaksana_type' => 'internal',
                'id_vendor' => null,

                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
