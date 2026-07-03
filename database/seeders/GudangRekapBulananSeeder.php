<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GudangRekapBulananSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // =====================================
            // 📌 Rekap Amplop Coklat Besar Logo Baru (AT001)
            // =====================================
            [
                'id_rekap' => 'RK0001',
                'id_barang' => 'AT001',
                'bulan' => 1,
                'tahun' => 2025,
                'stok_awal' => 1000,
                'stok_masuk' => 1000, // dari transaksi TRX0001
                'stok_keluar' => 200, // dari transaksi TRX0002
                'stok_akhir' => 1800, // 1000 + 1000 - 200
            ],

            // =====================================
            // 📌 Amplop Coklat Besar Polos (AT002)
            // =====================================
            [
                'id_rekap' => 'RK0002',
                'id_barang' => 'AT002',
                'bulan' => 1,
                'tahun' => 2025,
                'stok_awal' => 600,
                'stok_masuk' => 100, // TRX0003
                'stok_keluar' => 20, // TRX0004
                'stok_akhir' => 680,
            ],

            // =====================================
            // 📌 Amplop Coklat Polos Sedang (AT003)
            // =====================================
            [
                'id_rekap' => 'RK0003',
                'id_barang' => 'AT003',
                'bulan' => 1,
                'tahun' => 2025,
                'stok_awal' => 1000,
                'stok_masuk' => 50, // TRX0005
                'stok_keluar' => 0,
                'stok_akhir' => 1050,
            ],

            // =====================================
            // 📌 Aqua Galon LM (RT001)
            // =====================================
            [
                'id_rekap' => 'RK0004',
                'id_barang' => 'RT001',
                'bulan' => 1,
                'tahun' => 2025,
                'stok_awal' => 32,
                'stok_masuk' => 0,
                'stok_keluar' => 5, // TRX0006
                'stok_akhir' => 27,
            ],

            // =====================================
            // 📌 Gula (RT002)
            // =====================================
            [
                'id_rekap' => 'RK0005',
                'id_barang' => 'RT002',
                'bulan' => 1,
                'tahun' => 2025,
                'stok_awal' => 15,
                'stok_masuk' => 3, // TRX0007
                'stok_keluar' => 4, // dari stok barang
                'stok_akhir' => 14, // 15 + 3 - 4
            ],
        ];

        DB::table('gudang_rekap_bulanan')->insert($data);
    }
}
