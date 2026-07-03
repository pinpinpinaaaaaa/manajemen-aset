<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MenuSeeder::class,
            RolesSeeder::class,
         // JenisBarangSeeder::class,
         // GedungSeeder::class,
         // RuanganSeeder::class,
         // AsetSeeder::class,
         // AparSeeder::class,
         // KendaraanSeeder::class,
         // GudangBarangSeeder::class,
         // MaintenanceSeeder::class,
         // GudangTransaksiSeeder::class,
         // GudangRekapBulananSeeder::class,
            DivisiSeeder::class,
         // LaporanPemusnahanSeeder::class,
         // PengadaanBarangJasaSeeder::class
        ]);
    }
}