<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisi = [
            'Pelatihan',
            'Asesmen',
            'Klaster',
            'Pimpinan',
            'Sekretaris Lembaga',
            'Legal',
            'Komunikasi & IT',
            'Umum & Aset',
            'Keuangan',
            'SDM Penelitian & Konsultasi'
        ];

        $idNumber = 1;

        foreach ($divisi as $nama) {
            $id = 'DIV' . str_pad($idNumber, 3, '0', STR_PAD_LEFT);

            Divisi::create([
                'id_divisi' => $id,
                'nama_divisi' => $nama,
                'kode_divisi' => null,
                'deskripsi' => null
            ]);

            $idNumber++;
        }
    }
}