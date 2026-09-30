<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddAnggaranMenusSeeder extends Seeder
{
    public function run(): void
    {
        // Buat parent folder "Anggaran" jika belum ada
        $parentId = DB::table('menus')
            ->where('name', 'Anggaran')
            ->where('type', 'folder')
            ->value('id');

        if (!$parentId) {
            $parentId = DB::table('menus')->insertGetId([
                'name'       => 'Anggaran',
                'route_name' => null,
                'type'       => 'folder',
                'parent_id'  => null,
                'order'      => 90,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Anggaran RKAT
        if (!DB::table('menus')->where('route_name', 'anggaran-rkat.index')->exists()) {
            DB::table('menus')->insert([
                'name'       => 'Anggaran RKAT',
                'route_name' => 'anggaran-rkat.index',
                'type'       => 'file',
                'parent_id'  => $parentId,
                'order'      => 1,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Riwayat Realisasi
        if (!DB::table('menus')->where('route_name', 'riwayat-realisasi.index')->exists()) {
            DB::table('menus')->insert([
                'name'       => 'Riwayat Realisasi',
                'route_name' => 'riwayat-realisasi.index',
                'type'       => 'file',
                'parent_id'  => $parentId,
                'order'      => 2,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Bantuan IT folder
        $bantuanParentId = DB::table('menus')
            ->where('name', 'Bantuan IT')
            ->where('type', 'folder')
            ->value('id');

        if (!$bantuanParentId) {
            $bantuanParentId = DB::table('menus')->insertGetId([
                'name'       => 'Bantuan IT',
                'route_name' => null,
                'type'       => 'folder',
                'parent_id'  => null,
                'order'      => 99,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Panduan IT
        if (!DB::table('menus')->where('route_name', 'bantuan-it')->exists()) {
            DB::table('menus')->insert([
                'name'       => 'Panduan IT',
                'route_name' => 'bantuan-it',
                'type'       => 'file',
                'parent_id'  => $bantuanParentId,
                'order'      => 1,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $total = DB::table('menus')->count();
        $this->command->info("Total menu sekarang: $total");
    }
}
