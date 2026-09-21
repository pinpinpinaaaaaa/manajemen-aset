<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\LaporanPemusnahan;
use App\Models\Maintenance;
use App\Models\PengaduanKerusakan;
use App\Models\PengadaanBarangJasa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tahun = Carbon::now()->year;

        // ── ROW 1 · Stat cards (snapshot saat ini, tidak difilter tahun) ────
        $totalAset        = Aset::whereIn('status', ['tersedia', 'dipinjam', 'maintenance'])->count();
        $asetTersedia     = Aset::where('status', 'tersedia')->count();
        $asetMaintenance  = Aset::where('status', 'maintenance')->count();
        $sedangPemusnahan = LaporanPemusnahan::whereIn('status', ['Belum Dimusnahkan', 'Sedang Dimusnahkan'])
            ->where('decision_status', '!=', 'ditolak')
            ->count();

        // ── ROW 2 · Tren biaya maintenance per bulan ────────────────────────
        $trendMaintenance = array_fill(1, 12, 0.0);
        Maintenance::whereYear('tanggal_laporan', $tahun)
            ->get(['tanggal_laporan', 'biaya_total'])
            ->each(function ($m) use (&$trendMaintenance) {
                $trendMaintenance[Carbon::parse($m->tanggal_laporan)->month] += (float) $m->biaya_total;
            });

        // ── ROW 2 · Tren biaya pengadaan barang & jasa per bulan ────────────
        $trendBarang = array_fill(1, 12, 0.0);
        $trendJasa   = array_fill(1, 12, 0.0);
        PengadaanBarangJasa::with(['details' => fn($q) => $q->select('id_pengadaan', 'jenis', 'subtotal')])
            ->whereYear('created_at', $tahun)
            ->get(['id_pengadaan', 'created_at'])
            ->each(function ($p) use (&$trendBarang, &$trendJasa) {
                $bulan = Carbon::parse($p->created_at)->month;
                foreach ($p->details as $d) {
                    if ($d->jenis === 'barang') $trendBarang[$bulan] += (float) $d->subtotal;
                    else                        $trendJasa[$bulan]   += (float) $d->subtotal;
                }
            });

        // ── ROW 3 · Ringkasan nilai uang (tahun terpilih) ───────────────────
        $totalBiayaMaintenance = (float) Maintenance::whereYear('tanggal_laporan', $tahun)->sum('biaya_total');
        $jumlahMaintenance     = Maintenance::whereYear('tanggal_laporan', $tahun)->count();
        $totalBiayaPengadaan   = (float) PengadaanBarangJasa::whereYear('created_at', $tahun)->sum('total_biaya');
        $jumlahPengadaan       = PengadaanBarangJasa::whereYear('created_at', $tahun)->count();

        // ── ROW 4 · Pengaduan kerusakan (tahun terpilih) ────────────────────
        $pengaduanBelumApprove = PengaduanKerusakan::whereYear('created_at', $tahun)
            ->where('decision_status', 'menunggu_persetujuan')->count();
        $pengaduanSedangDiproses = PengaduanKerusakan::whereYear('created_at', $tahun)
            ->where('status', 'Sedang Diproses')->count();
        $pengaduanSelesai      = PengaduanKerusakan::whereYear('created_at', $tahun)
            ->where('status', 'Selesai')->count();
        $pengaduanDitolak      = PengaduanKerusakan::whereYear('created_at', $tahun)
            ->where('decision_status', 'ditolak')->count();
        $pengaduanTotal        = $pengaduanBelumApprove + $pengaduanSedangDiproses
                                 + $pengaduanSelesai + $pengaduanDitolak;

        // ── ROW 4 · Maintenance belum selesai (belum approve + sedang proses) ─
        // "Belum selesai" = decision_status bukan ditolak DAN masih ada detail yg belum Selesai
        $maintenanceBelumSelesai = Maintenance::with(['details.aset', 'ruangan'])
            ->whereYear('tanggal_laporan', $tahun)
            ->whereIn('decision_status', ['menunggu_persetujuan', 'disetujui'])
            ->whereHas('details', fn($q) => $q->whereIn('status', ['Perlu Perbaikan', 'Sedang Diperbaiki']))
            ->oldest('tanggal_laporan')
            ->get();

        return view('dashboard', compact(
            'tahun',
            'totalAset', 'asetTersedia', 'asetMaintenance', 'sedangPemusnahan',
            'trendMaintenance', 'trendBarang', 'trendJasa',
            'totalBiayaMaintenance', 'jumlahMaintenance',
            'totalBiayaPengadaan', 'jumlahPengadaan',
            'pengaduanTotal', 'pengaduanBelumApprove', 'pengaduanSedangDiproses', 'pengaduanSelesai', 'pengaduanDitolak',
            'maintenanceBelumSelesai'
        ));
    }
}
