<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class UpdateRolesSeeder extends Seeder
{
    public function run(): void
    {
        $m = fn(array $routes) =>
            Menu::whereIn('route_name', $routes)->pluck('id')->toArray();

        // Semua menu kecuali Panduan IT (bantuan-it)
        $bantuanItId  = Menu::where('route_name', 'bantuan-it')->value('id');
        $allExceptIT  = Menu::when($bantuanItId, fn($q) => $q->where('id', '!=', $bantuanItId))
                            ->pluck('id')->toArray();

        $roles = [
            [
                'id_role'   => 'R001',
                'nama_role' => 'superadmin',
                'menu'      => $allExceptIT,     // semua kecuali Panduan IT
            ],
            [
                'id_role'   => 'R005',
                'nama_role' => 'IT',
                'menu'      => null,             // full access tanpa batasan
            ],
            [
                'id_role'   => 'R002',
                'nama_role' => 'gudang',
                'menu'      => $m([
                    'dashboard',
                    'gudang.dashboard',
                    'gudang.index',
                    'gudang.laporan_transaksi',
                    'gudang.laporan_opname',
                    'gudang.transaksi.index',
                    'gudang.stok_opname.index',
                    'permintaan-barang.index',
                    'kendaraan.index',
                    'ekspedisi.index',
                    'permintaan-kendaraan.index',
                ]),
            ],
            [
                'id_role'   => 'R003',
                'nama_role' => 'kendaraan',
                'menu'      => $m([
                    'dashboard',
                    'kendaraan.index',
                    'permintaan-kendaraan.index',
                ]),
            ],
            [
                'id_role'   => 'R006',
                'nama_role' => 'ekspedisi',
                'menu'      => $m([
                    'dashboard',
                    'kendaraan.index',
                    'ekspedisi.index',
                ]),
            ],
            [
                'id_role'   => 'R004',
                'nama_role' => 'viewer',
                'menu'      => [],               // tidak ada akses
            ],
        ];

        foreach ($roles as $data) {
            Role::updateOrCreate(
                ['id_role' => $data['id_role']],
                ['nama_role' => $data['nama_role'], 'menu' => $data['menu']]
            );
            $this->command->info("Role {$data['nama_role']} ({$data['id_role']}) ✓");
        }

        $this->command->info('Total roles: ' . Role::count());
    }
}
