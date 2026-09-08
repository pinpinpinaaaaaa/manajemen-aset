<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class AddGudangMenusSeeder extends Seeder
{
    public function run(): void
    {
        $stokMenu = Menu::where('route_name', 'gudang.index')->first();

        if (!$stokMenu) {
            $this->command->error(
                'Menu gudang.index tidak ditemukan di tabel menus. ' .
                'Pastikan MenuSeeder sudah dijalankan terlebih dahulu.'
            );
            return;
        }

        $gudangId = $stokMenu->parent_id;

        $transaksi = Menu::firstOrCreate(
            ['route_name' => 'gudang.transaksi.index'],
            [
                'name'      => 'Transaksi Gudang',
                'type'      => 'file',
                'parent_id' => $gudangId,
                'order'     => 5,
                'is_active' => true,
            ]
        );

        $opname = Menu::firstOrCreate(
            ['route_name' => 'gudang.stok_opname.index'],
            [
                'name'      => 'Stok Opname',
                'type'      => 'file',
                'parent_id' => $gudangId,
                'order'     => 6,
                'is_active' => true,
            ]
        );

        $this->command->info(
            'Transaksi Gudang: ' . ($transaksi->wasRecentlyCreated ? 'DIBUAT BARU' : 'sudah ada') .
            ' (id=' . $transaksi->id . ')'
        );
        $this->command->info(
            'Stok Opname: ' . ($opname->wasRecentlyCreated ? 'DIBUAT BARU' : 'sudah ada') .
            ' (id=' . $opname->id . ')'
        );

        $this->command->info('Total menu sekarang: ' . Menu::count());
    }
}
