<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JenisBarangSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('jenis_barang')->insert([
            // ================= SARANA =================
            [
                'id_jenis_barang' => 'JB001',
                'jenis' => 'sarana',
                'kategori' => 'it',
                'nama_barang' => 'Komputer',
                'prefix_kode' => 'KOM',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB002',
                'jenis' => 'sarana',
                'kategori' => 'it',
                'nama_barang' => 'Printer',
                'prefix_kode' => 'PRN',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB003',
                'jenis' => 'sarana',
                'kategori' => 'it',
                'nama_barang' => 'Monitor',
                'prefix_kode' => 'MON',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'id_jenis_barang' => 'JB004',
                'jenis' => 'sarana',
                'kategori' => 'elektronik',
                'nama_barang' => 'Televisi',
                'prefix_kode' => 'TV',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB005',
                'jenis' => 'sarana',
                'kategori' => 'elektronik',
                'nama_barang' => 'Kulkas',
                'prefix_kode' => 'KUL',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB006',
                'jenis' => 'sarana',
                'kategori' => 'elektronik',
                'nama_barang' => 'Kipas Angin',
                'prefix_kode' => 'KIP',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'id_jenis_barang' => 'JB007',
                'jenis' => 'sarana',
                'kategori' => 'non elektronik',
                'nama_barang' => 'Meja',
                'prefix_kode' => 'MEJ',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB008',
                'jenis' => 'sarana',
                'kategori' => 'non elektronik',
                'nama_barang' => 'Kursi',
                'prefix_kode' => 'KUR',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB009',
                'jenis' => 'sarana',
                'kategori' => 'non elektronik',
                'nama_barang' => 'Lemari',
                'prefix_kode' => 'LEM',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB010',
                'jenis' => 'sarana',
                'kategori' => 'non elektronik',
                'nama_barang' => 'Papan Tulis',
                'prefix_kode' => 'PBT',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ================= PRASARANA =================
            [
                'id_jenis_barang' => 'JB011',
                'jenis' => 'prasarana',
                'kategori' => 'elektronik',
                'nama_barang' => 'AC',
                'prefix_kode' => 'AC',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB012',
                'jenis' => 'prasarana',
                'kategori' => 'elektronik',
                'nama_barang' => 'Lampu',
                'prefix_kode' => 'LMP',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB013',
                'jenis' => 'prasarana',
                'kategori' => 'non elektronik',
                'nama_barang' => 'Partisi Ruangan',
                'prefix_kode' => 'PRT',
                'bisa_dipindah' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB014',
                'jenis' => 'prasarana',
                'kategori' => 'non elektronik',
                'nama_barang' => 'Dinding',
                'prefix_kode' => 'DND',
                'bisa_dipindah' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB015',
                'jenis' => 'prasarana',
                'kategori' => 'non elektronik',
                'nama_barang' => 'Lantai',
                'prefix_kode' => 'LNT',
                'bisa_dipindah' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_jenis_barang' => 'JB016',
                'jenis' => 'prasarana',
                'kategori' => 'non elektronik',
                'nama_barang' => 'Plafon',
                'prefix_kode' => 'PLF',
                'bisa_dipindah' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}