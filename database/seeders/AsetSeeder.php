<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('aset')->insert([

            // =================== GEDUNG G001 ===================
            [
                'id_aset' => 'A0001',
                'kode_aset' => 'KUR001',
                'nama_aset' => 'Kursi Kantor Ergonomis',
                'merk' => 'Informa',
                'tipe_model' => 'Ergo Pro X',
                'spesifikasi' => 'Kursi kantor dengan sandaran adjustable dan bahan mesh premium.',
                'id_jenis_barang' => 'JB008', // Kursi
                'id_gedung' => 'G001',
                'id_ruangan' => 'R002',
                'tahun_perolehan' => 2022,
                'nilai' => 750000.00,
                'kelayakan' => 1,
                'keterangan_kelayakan' => 'Layak',
                'status' => 'tersedia',
                'foto' => 'aset/kursi.jpg',
            ],

            [
                'id_aset' => 'A0002',
                'kode_aset' => 'MEJ002',
                'nama_aset' => 'Meja Kayu Staff',
                'merk' => 'Olympic',
                'tipe_model' => 'MK-120',
                'spesifikasi' => 'Meja kayu dengan 3 laci dan finishing anti gores.',
                'id_jenis_barang' => 'JB007', // Meja
                'id_gedung' => 'G001',
                'id_ruangan' => 'R004',
                'tahun_perolehan' => 2021,
                'nilai' => 1200000.00,
                'kelayakan' => 2,
                'keterangan_kelayakan' => 'Perlu pemantauan',
                'status' => 'tersedia',
                'foto' => 'aset/meja.jpg',
            ],

            // =================== PRASARANA ===================
            [
                'id_aset' => 'A0003',
                'kode_aset' => 'LMP001',
                'nama_aset' => 'Lampu Plafon Ruangan',
                'merk' => 'Philips',
                'tipe_model' => 'LED Panel 24W',
                'spesifikasi' => 'Lampu LED panel hemat energi 24 watt.',
                'id_jenis_barang' => 'JB012', // ✅ Lampu (bisa dipindah)
                'id_gedung' => 'G002',
                'id_ruangan' => 'R079',
                'tahun_perolehan' => 2018,
                'nilai' => 250000.00,
                'kelayakan' => 2,
                'keterangan_kelayakan' => 'Layak',
                'status' => 'tersedia',
                'foto' => 'aset/lampu.jpg',
            ],

            [
                'id_aset' => 'A0004',
                'kode_aset' => 'PRT001',
                'nama_aset' => 'Partisi Aluminium',
                'merk' => 'YKK',
                'tipe_model' => 'Sliding Partition',
                'spesifikasi' => 'Partisi aluminium modular untuk ruangan.',
                'id_jenis_barang' => 'JB013', // ✅ Partisi (bisa dipindah)
                'id_gedung' => 'G002',
                'id_ruangan' => 'R083',
                'tahun_perolehan' => 2017,
                'nilai' => 450000.00,
                'kelayakan' => 1,
                'keterangan_kelayakan' => 'Layak',
                'status' => 'tersedia',
                'foto' => 'aset/partisi.jpg',
            ],

            [
                'id_aset' => 'A0005',
                'kode_aset' => 'DND001',
                'nama_aset' => 'Dinding Gypsum (Panel Kanan)',
                'merk' => 'Aplus',
                'tipe_model' => 'Gypsum Board 9mm',
                'spesifikasi' => 'Panel gypsum untuk partisi ruangan.',
                'id_jenis_barang' => 'JB014', // ❌ Dinding (tidak bisa dipindah)
                'id_gedung' => 'G002',
                'id_ruangan' => 'R083',
                'tahun_perolehan' => 2019,
                'nilai' => 300000.00,
                'kelayakan' => 4,
                'keterangan_kelayakan' => 'Perlu perbaikan',
                'status' => 'maintenance',
                'foto' => 'aset/dinding.jpg',
            ],

            // =================== GEDUNG G003 ===================
            [
                'id_aset' => 'A0007',
                'kode_aset' => 'KOM004',
                'nama_aset' => 'Komputer All-in-One',
                'merk' => 'HP',
                'tipe_model' => 'HP ProOne 440 G6',
                'spesifikasi' => 'Intel Core i5, RAM 8GB, SSD 512GB.',
                'id_jenis_barang' => 'JB001',
                'id_gedung' => 'G003',
                'id_ruangan' => 'R119',
                'tahun_perolehan' => 2020,
                'nilai' => 8500000.00,
                'kelayakan' => 5,
                'keterangan_kelayakan' => 'Dimusnahkan',
                'status' => 'non aktif',
                'foto' => 'aset/komputer.jpg',
            ],

        ]);
    }
}