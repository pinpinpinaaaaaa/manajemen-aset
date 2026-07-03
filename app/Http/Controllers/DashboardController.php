<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Kendaraan;
use App\Models\Maintenance;
use App\Models\MaintenanceDetail;
use App\Models\PengaduanKerusakan;
use App\Models\PengadaanBarangJasa;
use App\Models\PeminjamanAset;
use Carbon\Carbon;

use App\Models\AsetLog;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | CARD ATAS
        |--------------------------------------------------------------------------
        */

        $tahunSekarang = Carbon::now()->year;
        $totalAset = Aset::count();

        $totalKendaraan = Kendaraan::count();

        $maintenanceAktif = Maintenance::whereHas('details', function ($q) {
            $q->whereIn('status', [
                'Perlu Perbaikan',
                'Sedang Diperbaiki'
            ]);
        })->count();

        $pengaduanAktif = PengaduanKerusakan::whereIn(
            'decision_status',
            [
                'menunggu_persetujuan',
                'disetujui'
            ]
        )->count();

        /*
        |--------------------------------------------------------------------------
        | GRAFIK STATUS ASET
        |--------------------------------------------------------------------------
        */

        $asetTersedia = Aset::where('status', 'tersedia')->count();

        $asetDipinjam = Aset::where('status', 'dipinjam')->count();

        $asetDimusnahkan = Aset::where('status', 'dimusnahkan')->count();

        $asetMaintenance = Aset::where('status', 'maintenance')->count();

        /*
        |--------------------------------------------------------------------------
        | GRAFIK MAINTENANCE
        |--------------------------------------------------------------------------
        */

        $maintenanceSelesai = Maintenance::whereHas('details', function ($q) {
            $q->where('status', 'Selesai');
        })->count();

        $maintenanceBerjalan = Maintenance::whereHas('details', function ($q) {
            $q->whereIn('status', [
                'Perlu Perbaikan',
                'Sedang Diperbaiki'
            ]);
        })->count();

        $maintenanceDitolak = Maintenance::where(
            'decision_status',
            'ditolak'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | PENGADAAN
        |--------------------------------------------------------------------------
        */

        $pengadaanMenunggu = PengadaanBarangJasa::whereYear('created_at', $tahunSekarang)
            ->where('decision_status', 'menunggu_persetujuan')
            ->count();

        $pengadaanDiproses = PengadaanBarangJasa::whereYear('created_at', $tahunSekarang)
            ->where('status', 'Sedang Diproses')
            ->count();

        $pengadaanSelesai = PengadaanBarangJasa::whereYear('created_at', $tahunSekarang)
            ->where('status', 'Selesai')
            ->count();

        $totalPengadaan = PengadaanBarangJasa::whereYear('created_at', $tahunSekarang)
            ->sum('total_biaya');

        $jumlahPengadaan = PengadaanBarangJasa::whereYear('created_at', $tahunSekarang)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        $peminjamanAktif = PeminjamanAset::where(
            'decision_status',
            'disetujui'
        )->count();

        $peminjamanMenunggu = PeminjamanAset::where(
            'decision_status',
            'menunggu_persetujuan'
        )->count();

        $peminjamanDisetujui = PeminjamanAset::where(
            'decision_status',
            'disetujui'
        )->count();

        $peminjamanDitolak = PeminjamanAset::where(
            'decision_status',
            'ditolak'
        )->count();
        /*
        |--------------------------------------------------------------------------
        | MAINTENANCE BERJALAN
        |--------------------------------------------------------------------------
        */

        $maintenanceBerjalanList = Maintenance::with('details.aset')
            ->whereHas('details', function ($q) {
                $q->whereIn('status', [
                    'Perlu Perbaikan',
                    'Sedang Diperbaiki'
                ]);
            })
            ->latest('tanggal_laporan')
            ->take(5)
            ->get();

        $totalBiayaMaintenance = Maintenance::with('details')
            ->get()
            ->sum(function ($m) {
                return $m->details->sum('biaya');
            });

        $totalPengadaan = PengadaanBarangJasa::sum('total_biaya');

        /*
        |--------------------------------------------------------------------------
        | PENGADUAN TERBARU
        |--------------------------------------------------------------------------
        */

        $pengaduanTerbaru = PengaduanKerusakan::latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalAset',
            'totalKendaraan',
            'maintenanceAktif',
            'pengaduanAktif',

            'asetTersedia',
            'asetDipinjam',
            'asetDimusnahkan',
            'asetMaintenance',

            'maintenanceSelesai',
            'maintenanceBerjalan',
            'maintenanceDitolak',

            'pengadaanMenunggu',
            'pengadaanDiproses',
            'pengadaanSelesai',
            'totalPengadaan',
            'jumlahPengadaan',

            'peminjamanAktif',
            'peminjamanMenunggu',
            'peminjamanDisetujui',
            'peminjamanDitolak',
            'totalBiayaMaintenance',
            'totalPengadaan',
            'maintenanceBerjalanList',
            'pengaduanTerbaru'
        ));
    }
}