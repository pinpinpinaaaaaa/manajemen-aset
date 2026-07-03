<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::truncate();

        // ================= DASHBOARD =================
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'type' => 'folder',
            'order' => 1,
        ]);

        Menu::create([
            'name' => 'Dashboard',
            'type' => 'file',
            'route_name' => 'dashboard',
            'parent_id' => $dashboard->id,
            'order' => 1,
        ]);

        // ================= MASTER DATA =================
        $master = Menu::create([
            'name' => 'Master Data',
            'type' => 'folder',
            'order' => 2,
        ]);

        Menu::create([
            'name' => 'Users',
            'type' => 'file',
            'route_name' => 'users.index',
            'parent_id' => $master->id,
            'order' => 1,
        ]);

        Menu::create([
            'name' => 'Roles',
            'type' => 'file',
            'route_name' => 'roles.index',
            'parent_id' => $master->id,
            'order' => 2,
        ]);

                Menu::create([
            'name' => 'Vendor',
            'type' => 'file',
            'route_name' => 'vendor.index',
            'parent_id' => $master->id,
            'order' => 3,
        ]);

        Menu::create([
            'name' => 'Jenis Barang',
            'type' => 'file',
            'route_name' => 'jenis_barang.index',
            'parent_id' => $master->id,
            'order' => 4,
        ]);

        // ================= LIST ASET =================
        $listAset = Menu::create([
            'name' => 'List Aset',
            'type' => 'folder',
            'order' => 3,
        ]);

        // Sarana Prasarana
        $sarpras = Menu::create([
            'name' => 'Sarana Prasarana',
            'type' => 'folder',
            'parent_id' => $listAset->id,
            'order' => 1,
        ]);

        Menu::create(['name'=>'Gedung','type'=>'file','route_name'=>'gedung.index','parent_id'=>$sarpras->id,'order'=>1]);
        Menu::create(['name'=>'Ruangan','type'=>'file','route_name'=>'ruangan.index','parent_id'=>$sarpras->id,'order'=>2]);
        Menu::create(['name'=>'Apar','type'=>'file','route_name'=>'apar.index','parent_id'=>$sarpras->id,'order'=>3]);
        Menu::create(['name'=>'Sarana','type'=>'file','route_name'=>'aset.index','parent_id'=>$sarpras->id,'order'=>4]);

        // Kendaraan
        Menu::create([
            'name' => 'Kendaraan',
            'type' => 'file',
            'route_name' => 'kendaraan.index',
            'parent_id' => $listAset->id,
            'order' => 2,
        ]);

        // Gudang
        $gudang = Menu::create([
            'name' => 'Gudang',
            'type' => 'folder',
            'parent_id' => $listAset->id,
            'order' => 3,
        ]);

        Menu::create(['name'=>'Dashboard Gudang','type'=>'file','route_name'=>'gudang.dashboard','parent_id'=>$gudang->id,'order'=>1]);
        Menu::create(['name'=>'Stok','type'=>'file','route_name'=>'gudang.index','parent_id'=>$gudang->id,'order'=>2]);
        Menu::create(['name'=>'Laporan Transaksi','type'=>'file','route_name'=>'gudang.laporan_transaksi','parent_id'=>$gudang->id,'order'=>3]);
        Menu::create(['name'=>'Laporan Opname','type'=>'file','route_name'=>'gudang.laporan_opname','parent_id'=>$gudang->id,'order'=>4]);

        // ================= LAPORAN =================
        $laporan = Menu::create([
            'name' => 'Laporan',
            'type' => 'folder',
            'order' => 4,
        ]);

        $maintenance = Menu::create([
            'name' => 'Maintenance',
            'type' => 'folder',
            'parent_id' => $laporan->id,
            'order' => 1,
        ]);

        Menu::create(['name'=>'List Maintenance','type'=>'file','route_name'=>'maintenance.index','parent_id'=>$maintenance->id,'order'=>1]);
        Menu::create(['name'=>'Laporan Maintenance','type'=>'file','route_name'=>'maintenance.laporan','parent_id'=>$maintenance->id,'order'=>2]);

        Menu::create([
            'name' => 'Laporan Pemusnahan',
            'type' => 'file',
            'route_name' => 'laporan_pemusnahan.index',
            'parent_id' => $laporan->id,
            'order' => 2,
        ]);

        Menu::create([
            'name' => 'Laporan Tahunan',
            'type' => 'file',
            'route_name' => 'laporan_tahunan.index',
            'parent_id' => $laporan->id,
            'order' => 3,
        ]);

                Menu::create([
            'name' => 'Pemindahan Aset',
            'type' => 'file',
            'route_name' => 'pemindahan_aset.index',
            'parent_id' => $laporan->id,
            'order' => 4,
        ]);

        Menu::create([
            'name' => 'Audit Logs',
            'type' => 'file',
            'route_name' => 'audit.logs',
            'parent_id' => $laporan->id,
            'order' => 5,
        ]);

        // Halaman detail audit log — tidak muncul di sidebar tapi tetap dicek oleh menu.access
        Menu::create([
            'name' => 'Audit Log Detail',
            'type' => 'file',
            'route_name' => 'audit.logs.show',
            'parent_id' => $laporan->id,
            'order' => 6,
            'is_active' => false,
        ]);

        // ================= LAYANAN (NON-AKTIF) =================
        $layanan = Menu::create([
            'name' => 'Layanan',
            'type' => 'folder',
            'order' => 5,
        ]);

                Menu::create([
            'name'=>'Peminjaman Ruangan',
            'type'=>'file',
            'route_name'=>'peminjaman-ruangan.index',
            'parent_id'=>$layanan->id,
            'order'=>3
        ]);

        Menu::create([
            'name'=>'Permintaan Kendaraan',
            'type'=>'file',
            'route_name'=>'permintaan-kendaraan.index',
            'parent_id'=>$layanan->id,
            'order'=>4
        ]);

        Menu::create([
            'name'=>'Pengadaan Barang',
            'type'=>'file',
            'route_name'=>'pengadaan-barang.index',
            'parent_id'=>$layanan->id,
            'order'=>5
        ]);

        Menu::create([
            'name'=>'Pengaduan Kerusakan',
            'type'=>'file',
            'route_name'=>'pengaduan-kerusakan.index',
            'parent_id'=>$layanan->id,
            'order'=>6
        ]);

        Menu::create([
            'name'=>'Ekspedisi',
            'type'=>'file',
            'route_name'=>'ekspedisi.index',
            'parent_id'=>$layanan->id,
            'order'=>7
        ]);

        Menu::create(['name'=>'Permintaan Barang','type'=>'file','route_name'=>'permintaan-barang.index','parent_id'=>$layanan->id,'order'=>1]);
        Menu::create(['name'=>'Peminjaman Aset','type'=>'file','route_name'=>'peminjaman_aset.index','parent_id'=>$layanan->id,'order'=>2]);
    }
}
