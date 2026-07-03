<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [

            // ================= MOBIL =================
            [
                'jenis_kendaraan' => 'roda 4',
                'tipe' => 'mobil',
                'plat_nomor' => 'B 5217 STL',
                'tahun_pembelian' => 2008,
                'umur_ekonomis' => 8,

                'merk' => 'Toyota',
                'model' => 'Avanza',
                'spesifikasi' => '1.3L Manual',
                'no_rangka' => 'MHKA123456789001',
                'no_mesin' => 'ENG001234',

                'status_kondisi' => 'aktif',
                'status_penggunaan' => 'tersedia',
                'foto' => 'kendaraan/mobil/default.jpg',

                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'jenis_kendaraan' => 'roda 4',
                'tipe' => 'mobil',
                'plat_nomor' => 'B 8426 VO',
                'tahun_pembelian' => 2010,
                'umur_ekonomis' => 8,

                'merk' => 'Daihatsu',
                'model' => 'Xenia',
                'spesifikasi' => '1.5L Manual',
                'no_rangka' => 'MHDA987654321002',
                'no_mesin' => 'ENG002345',

                'status_kondisi' => 'aktif',
                'status_penggunaan' => 'tersedia',
                'foto' => 'kendaraan/mobil/default.jpg',

                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ================= MOTOR =================
            [
                'jenis_kendaraan' => 'roda 2',
                'tipe' => 'motor',
                'plat_nomor' => 'B 3368 PNE',
                'tahun_pembelian' => 2023,
                'umur_ekonomis' => 8,

                'merk' => 'Honda',
                'model' => 'Beat',
                'spesifikasi' => '110cc FI',
                'no_rangka' => 'MHKM123456789003',
                'no_mesin' => 'ENG003456',

                'status_kondisi' => 'aktif',
                'status_penggunaan' => 'tersedia',
                'foto' => 'kendaraan/motor/default.jpg',

                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'jenis_kendaraan' => 'roda 2',
                'tipe' => 'motor',
                'plat_nomor' => 'B 3805 PIF',
                'tahun_pembelian' => 2019,
                'umur_ekonomis' => 8,

                'merk' => 'Yamaha',
                'model' => 'NMAX',
                'spesifikasi' => '155cc ABS',
                'no_rangka' => 'MHKM987654321004',
                'no_mesin' => 'ENG004567',

                'status_kondisi' => 'aktif',
                'status_penggunaan' => 'tersedia',
                'foto' => 'kendaraan/motor/default.jpg',

                'created_at' => $now,
                'updated_at' => $now,
            ],

        ];

        // Generate ID manual
        $counter = 1;

        foreach ($data as $item) {
            $item['id_kendaraan'] = 'KND' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            DB::table('kendaraan')->insert($item);
            $counter++;
        }
    }
}
