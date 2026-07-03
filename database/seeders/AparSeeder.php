<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AparSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('apar')->insert([
            [
                'id_apar' => 'AP001',
                'id_gedung' => 'G001',
                'id_ruangan' => 'R001',
                'expired_date' => '2026-05-01',
                'jenis' => 'refill',
                'tanggal_refill' => '2025-05-01',
                'ukuran' => '5kg',
                'keterangan' => 'Baik',
                'foto' => 'apar/default.jpg'
            ],
        ]);
    }
}
