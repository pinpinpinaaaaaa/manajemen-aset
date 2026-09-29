<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\LaporanPemusnahan;
use App\Models\Maintenance;
use App\Models\PengaduanKerusakan;
use App\Models\PengadaanBarangJasa;
use App\Models\RkatAnggaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tahun = Carbon::now()->year;

        // ── ROW 1 · Stat cards (snapshot saat ini, tidak difilter tahun) ────
        // Hanya hitung aset berkategori "sarana" (bukan inventaris/kendaraan/apar)
        $totalAset        = Aset::whereHas('jenisBarang', fn($q) => $q->where('jenis', 'sarana'))
                                ->whereIn('status', ['tersedia', 'dipinjam', 'maintenance'])->count();
        $asetTersedia     = Aset::whereHas('jenisBarang', fn($q) => $q->where('jenis', 'sarana'))
                                ->where('status', 'tersedia')->count();
        $asetMaintenance  = Aset::whereHas('jenisBarang', fn($q) => $q->where('jenis', 'sarana'))
                                ->where('status', 'maintenance')->count();
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

        // ── Referensi anggaran RKAT per bulan (untuk garis batas di grafik) ──
        $rkatBulanKolom = [
            'rencana_jan','rencana_feb','rencana_mar','rencana_apr',
            'rencana_mei','rencana_jun','rencana_jul','rencana_agu',
            'rencana_sep','rencana_okt','rencana_nov','rencana_des',
        ];
        $rkatReferensi = RkatAnggaran::where('tahun', $tahun)
            ->whereIn('coa_pos', ['Maintenance', 'Pengadaan'])
            ->get(array_merge(['coa_pos', 'anggaran'], $rkatBulanKolom));

        $buildRencana = function (string $pos) use ($rkatReferensi, $rkatBulanKolom): ?array {
            $rows = $rkatReferensi->where('coa_pos', $pos);
            if ($rows->isEmpty()) return null;
            $sums = [];
            foreach ($rkatBulanKolom as $k) {
                $sums[] = round((float) $rows->sum($k), 2);
            }
            if (array_sum($sums) > 0) return $sums;
            $flat = round((float) $rows->sum('anggaran') / 12, 2);
            return array_fill(0, 12, $flat);
        };

        $rencanaMaintenanceBulan = $buildRencana('Maintenance');
        $rencanaPengadaanBulan   = $buildRencana('Pengadaan');

        // ── ROW 4 · Pengaduan kerusakan (tahun terpilih) ────────────────────
        // Status diturunkan (computed) dari relasi maintenance — eager-load untuk hindari N+1
        $allPengaduan = PengaduanKerusakan::with(['maintenances.details'])
            ->whereYear('created_at', $tahun)
            ->get();

        $pengaduanBelumApprove   = $allPengaduan->filter(fn($p) => $p->status_computed === 'belum_approve')->count();
        $pengaduanSedangDiproses = $allPengaduan->filter(fn($p) => $p->status_computed === 'sedang_diproses')->count();
        $pengaduanSelesai        = $allPengaduan->filter(fn($p) => $p->status_computed === 'selesai')->count();
        $pengaduanDitolak        = $allPengaduan->filter(fn($p) => $p->status_computed === 'ditolak')->count();
        $pengaduanTotal          = $allPengaduan->count();

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
            'rencanaMaintenanceBulan', 'rencanaPengadaanBulan',
            'pengaduanTotal', 'pengaduanBelumApprove', 'pengaduanSedangDiproses', 'pengaduanSelesai', 'pengaduanDitolak',
            'maintenanceBelumSelesai'
        ));
    }
}
