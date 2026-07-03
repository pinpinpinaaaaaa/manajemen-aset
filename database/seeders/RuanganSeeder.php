<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];
        $gambarData = [];
        $counter = 1;

        $makeRuangan = function (
            $idGedung,
            $kategori,
            $lantai,
            $nama
        ) use (&$counter, &$data, &$gambarData) {

            $id = 'R' . str_pad($counter++, 3, '0', STR_PAD_LEFT);

            $data[] = [
                'id_ruangan' => $id,
                'id_gedung' => $idGedung,
                'kategori' => $kategori,
                'lantai' => $lantai,
                'nama_ruangan' => $nama,
                'status' => 'tersedia',
            ];

            $gambarData[] = [
                'id_ruangan' => $id,
                'foto' => 'ruangan/default.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        };

        /* ======================================================
        =============== G E D U N G G001 ========================
        ====================================================== */

        $eksterior_g1 = [
            'Parkir (Ged. Moh. Sadli)',
        ];

        foreach ($eksterior_g1 as $nama) {
            $makeRuangan('G001', 'eksterior', null, $nama);
        }

        $g1_l1 = [
            'Lobby Moh. Sadli',
            'Executive Lounge',
            'Ruang Makan',
            'Toilet Lt. 1',
            'Ruang 101',
            'Ruang 102-103',
            'Ruang 104',
        ];

        foreach ($g1_l1 as $nama) {
            $makeRuangan('G001', 'interior', 1, $nama);
        }

        $g1_l2 = [
            'Lobby Lt. 2',
            'Ruang 201',
            'Ruang 202',
            'Ruang 203',
            'Ruang 204-205',
            'Ruang 206',
            'Ruang 207',
            'Toilet Lt. 2',
            'R. 208/Pantry',
            'Ruang 209',
            'Ruang 210',
            'Ruang 211',
            'Ruang 212',
            'Ruang 213',
            'Ruang 214',
            'Ruang 215',
        ];

        foreach ($g1_l2 as $nama) {
            $makeRuangan('G001', 'interior', 2, $nama);
        }

                /* ======================================================
        =============== G E D U N G G002 ========================
        ====================================================== */

        // Lantai 1 — Ruangan Pimpinan
        $g2_l1_pimpinan = [
            'Ruang Kepala Pimpinan (Yas)',
            'Ruang Wakabid Admin Keu (Riri)',
            'Ruang Wakabid R&K (TEB)',
            'Ruang Wakabid Training & Assesment (Mone)',
            'Ruang Prof. RP',
            'Ruang Rapat DH/ Senior Lounge',
        ];

        foreach ($g2_l1_pimpinan as $nama) {
            $makeRuangan('G002', 'interior', 1, $nama);
        }


        // Lantai 1 — Konsultan & Staff
        $g2_l1_konsultan = [
            'Lobby Nasrudin',
            'Ruang Sekpim',
            'Ruang Nurdin Sobari',
            'Ruang Slamet & Sumiyarto',
            'Ruang Rina Wurjandari',
            'Ruang Rizqiah Insanita',
            'Ruang Toto Pranoto',
            'Ruang Fandis Ekyawan',
            'Ruang Haris P. Louise',
            'Ruang Lisa F. Akbar',
            'Ruang Niken dan Ida',
            'Ruang Willem Makaliwe',
            'Ruang Staff R&K (Viko, Imam, Agung)',
            'Ruang Adam Amru',
            'Ruang Rapat NS',
            'Ruang Staff Legal',
            'Arsip Legal & Keuangan',
        ];

        foreach ($g2_l1_konsultan as $nama) {
            $makeRuangan('G002', 'interior', 1, $nama);
        }


        // Lantai 1 — Eks Konsultasi
        $g2_l1_eks = [
            'Ruang Tamu',
            'Ruang Rapat',
            'Ruang Sorta & Mustia',
            'Ruang Benyamin',
            'Ruang Bayu dan Fauzhan',
            'Gudang Konsultasi',
            'Gudang GA 3 (Ruang 3)',
            'Ruang Tengah',
            'Ruang Bagus',
            'Ruang Heruwasto',
            'Ruang Eks. Konsultan',
            'Ruang Andrew',
        ];

        foreach ($g2_l1_eks as $nama) {
            $makeRuangan('G002', 'interior', 1, $nama);
        }


        // Lantai 1 — Divisi RSP
        $g2_l1_rsp = [
            'Ruang Dyah.P',
            'R. Fika & Deby',
            'R. Rapat Asociate',
            'R. Admin RSP (Indr & Dwi)',
            'Gudang/Arsip RSP',
            'R. Rumi. A',
            'R. Asociate',
            'Ruang Tamu',
            'Ruang Leo/Rika',
            'Ruang Rapat RSP',
        ];

        foreach ($g2_l1_rsp as $nama) {
            $makeRuangan('G002', 'interior', 1, $nama);
        }


        // Lantai 1 — Koperasi LM FEB
        $g2_l1_koperasi = [
            'Ruang Makan AC',
            'Lapak Pedagang',
            'Ruang Admin Koperasi',
        ];

        foreach ($g2_l1_koperasi as $nama) {
            $makeRuangan('G002', 'interior', 1, $nama);
        }


        // Lantai 1 — Divisi Training
        $g2_l1_training = [
            'R. Fajar. T',
            'R. Shona',
            'R. Rapat Training',
            'Ruang Tamu',
            'Gudang/Arsip Training',
            'Staff Training (Edy, Sri, Anton)',
        ];

        foreach ($g2_l1_training as $nama) {
            $makeRuangan('G002', 'interior', 1, $nama);
        }


        // Lantai 1 — Area Gedung Nasrudin
        $g2_l1_nasrudin = [
            'Ruang 01',
            'Ruang 02',
            'Ruang 03',
            'Ruang 04',
            'Harmony Lounge',
            'Toilet Harmony',
            'Ruang IT',
            'Gudang IT',
            'Ruang Konsultan (CP, FN, Mega, RY, IS)',
            'Gudang GA 2 (CP)',
            'R. Foto Copy',
            'Ekspidisi & Kendaraan',
            'Gudang GA 1 (Ahyar)',
            'R. Staff Gudang',
            'Toilet Pria',
            'Toilet Wanita',
            'Musholla',
        ];

        foreach ($g2_l1_nasrudin as $nama) {
            $makeRuangan('G002', 'interior', 1, $nama);
        }

                // Lantai 2 — Gedung Nasrudin
        $g2_l2 = [
            'Ruang 07',
            'Ruang 08',
            'Ruang Tunggu/Tamu',
            'Ruang Arsip RSP',
            'Ruang 09-10',
            'Ruang 11',
            'Ruang 12 (Yuli Setiono)',
            'Ruang 13 (R. Nugroho Purwantoro)',
            'Toilet Wanita',
            'Toilet Pria',
            'Ruang Atas Harmoni Lounge',
        ];

        foreach ($g2_l2 as $nama) {
            $makeRuangan('G002', 'interior', 2, $nama);
        }


        // Eksterior G002
        $g2_eksterior = [
            'Selasar Gd. Nasrudin',
            'Taman Harmoni',
            'Taman Heritage',
            'Pendopo Taman Heritage',
            'Selasar Heritage',
            'Selasar Ruang 1-3',
            'Selasar IT - Fotocopy',
            'Selasar Training - Mushola',
            'Selasar RSP - Yas',
            'Rooftop',
            'Selasar Ruang 7 - Toilet',
            'Kamar Mandi Taman',
        ];

        foreach ($g2_eksterior as $nama) {
            $makeRuangan('G002', 'eksterior', null, $nama);
        }

                /* ======================================================
        =============== G E D U N G G003 ========================
        ====================================================== */

        // Interior Lantai 1 — G003
        $g3_l1 = [
            'Lobby Gd Iber',
            'Pantri Iber',
            'Ruang 6.01',
            'Ruang 6.02',
            'Ruang 6.03',
            'Ruang 6.04',
            'Ruang 6.05',
            'Ruang 6.06',
            'Toilet Pria',
            'Toilet Wanita',
            'Gudang Arsip RSP',
        ];

        foreach ($g3_l1 as $nama) {
            $makeRuangan('G003', 'interior', 1, $nama);
        }


        // Interior Lantai 2 — G003
        $g3_l2 = [
            'Ruang BKG',
            'Ruang Acha',
            'Ruang Eks. Konsultan',
            'Ruang Tamu',
            'Ruang Staff GA & Aset',
            'Ruang SDM',
            'Ruang Wina dan Keuangan',
        ];

        foreach ($g3_l2 as $nama) {
            $makeRuangan('G003', 'interior', 2, $nama);
        }


        // Eksterior G003
        $g3_eksterior = [
            'Selasar Iber',
            'Tempat Cuci Piring',
        ];

        foreach ($g3_eksterior as $nama) {
            $makeRuangan('G003', 'eksterior', null, $nama);
        }


        /* ======================================================
        =============== INSERT SEMUA DATA =======================
        ====================================================== */

        // Insert tabel ruangan terlebih dahulu
        DB::table('ruangan')->insert($data);

        // Setelah itu baru insert tabel ruangan_gambar
        DB::table('ruangan_gambar')->insert($gambarData);
    }
}