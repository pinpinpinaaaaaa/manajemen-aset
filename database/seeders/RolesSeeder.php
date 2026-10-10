<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->delete();
        DB::table('roles')->delete();

        /*
        |--------------------------------------------------------------------------
        | HELPER ambil ID menu dari route_name
        |--------------------------------------------------------------------------
        */
        $menuIds = fn (array $routes) =>
            Menu::whereIn('route_name', $routes)->pluck('id')->toArray();

        // ================= ROLES =================

        // Semua menu kecuali Panduan IT
        $bantuanItId = Menu::where('route_name', 'bantuan-it')->value('id');
        $allExceptIT = Menu::when($bantuanItId, fn($q) => $q->where('id', '!=', $bantuanItId))
                           ->pluck('id')->toArray();

        // SUPERADMIN → semua kecuali Panduan IT
        Role::create([
            'id_role'   => 'R001',
            'nama_role' => 'superadmin',
            'menu'      => $allExceptIT,
        ]);

        // IT → FULL ACCESS tanpa batasan
        Role::create([
            'id_role'   => 'R005',
            'nama_role' => 'IT',
            'menu'      => null,
        ]);

        // GUDANG
        Role::create([
            'id_role'   => 'R002',
            'nama_role' => 'gudang',
            'menu'      => $menuIds([
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
        ]);

        // KENDARAAN
        Role::create([
            'id_role'   => 'R003',
            'nama_role' => 'kendaraan',
            'menu'      => $menuIds([
                'dashboard',
                'kendaraan.index',
                'permintaan-kendaraan.index',
            ]),
        ]);

        // EKSPEDISI
        Role::create([
            'id_role'   => 'R006',
            'nama_role' => 'ekspedisi',
            'menu'      => $menuIds([
                'dashboard',
                'kendaraan.index',
                'ekspedisi.index',
            ]),
        ]);

        // VIEWER (kosong = TIDAK ADA MENU)
        Role::create([
            'id_role'   => 'R004',
            'nama_role' => 'viewer',
            'menu'      => [],
        ]);

        // ================= USERS =================

        User::create([
            'id_user' => 'U001',
            'name'    => 'Super Admin',
            'email'   => 'admin@gmail.com',
            'password'=> Hash::make('password'),
            'id_role' => 'R001',
            'menu'    => null, // FULL ACCESS
        ]);

        User::create([
            'id_user' => 'U002',
            'name'    => 'Staff Gudang',
            'email'   => 'gudang@gmail.com',
            'password'=> Hash::make('password'),
            'id_role' => 'R002',
            'menu'    => null, // ikut role
        ]);

        User::create([
            'id_user' => 'U003',
            'name'    => 'Staff Kendaraan',
            'email'   => 'kendaraan@gmail.com',
            'password'=> Hash::make('password'),
            'id_role' => 'R003',
            'menu'    => null,
        ]);

        User::create([
            'id_user' => 'U005',
            'name'    => 'Staff IT',
            'email'   => 'it@gmail.com',
            'password'=> Hash::make('password'),
            'id_role' => 'R005',
            'menu'    => null,
        ]);

        User::create([
            'id_user' => 'U004',
            'name'    => 'Viewer',
            'email'   => 'viewer@gmail.com',
            'password'=> Hash::make('password'),
            'id_role' => 'R004',
            'menu'    => [],
        ]);
    }
}
