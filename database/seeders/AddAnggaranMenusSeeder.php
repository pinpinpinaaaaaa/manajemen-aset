<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddAnggaranMenusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menus')->insertOrIgnore([
            [
                'nama_menu'  => 'Anggaran RKAT',
                'route_name' => 'anggaran-rkat.index',
                'icon'       => 'fas fa-file-invoice-dollar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_menu'  => 'Riwayat Realisasi',
                'route_name' => 'riwayat-realisasi.index',
                'icon'       => 'fas fa-chart-bar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
