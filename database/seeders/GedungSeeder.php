<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GedungSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder tabel gedung
        DB::table('gedung')->insert([
            [
                'id_gedung' => 'G001',
                'nama_gedung' => 'Gedung Moh. Sadli',
                'status' => 'aktif'
            ],
            [
                'id_gedung' => 'G002',
                'nama_gedung' => 'Gedung Nasrudin Sumintapura',
                'status' => 'maintenance'
            ],
            [
                'id_gedung' => 'G003',
                'nama_gedung' => 'Gedung Hasad Anwar',
                'status' => 'aktif'
            ],
        ]);

        // Seeder tabel gedung_gambar
        DB::table('gedung_gambar')->insert([
            [
                'id_gedung' => 'G001',
                'gambar' => 'gedung/Gedung Moh. Sadli.jpg'
            ],
            [
                'id_gedung' => 'G002',
                'gambar' => 'gedung/Gedung Nasrudin Sumintapura.jpg'
            ],
            [
                'id_gedung' => 'G003',
                'gambar' => 'gedung/default.jpg'
            ],
        ]);
    }
}