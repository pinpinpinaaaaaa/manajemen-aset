<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarangJasa;
use App\Models\GudangTransaksi;
use App\Models\GudangRekapBulanan;
use App\Models\LaporanTahunanSummary;
use App\Models\LaporanTahunan;
use App\Models\LaporanTahunanDetail;
use App\Models\Maintenance;
use App\Models\MaintenanceDetail;
use App\Models\LaporanPemusnahan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanTahunanController extends Controller
{
    public function index()
    {
        $laporanTahunan = LaporanTahunan::withCount('details')
            ->orderBy('tahun', 'desc')
            ->get();

        return view('laporan_tahunan.index', compact('laporanTahunan'));
    }

    public function generate(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $id = "LPT-" . $tahun;

        LaporanTahunan::updateOrCreate(
            ['tahun' => $tahun],
            [
                'id_laporan_tahunan' => $id,
                'catatan' => "Laporan otomatis untuk tahun $tahun",
            ]
        );

        LaporanTahunanDetail::where('id_laporan_tahunan', $id)->delete();

        $maintenance = Maintenance::whereYear('tanggal_laporan', $tahun)->get();
        $pemusnahan  = LaporanPemusnahan::whereYear('tanggal_pemusnahan', $tahun)->get();
        $pengadaan = PengadaanBarangJasa::whereYear('created_at', $tahun)
            ->where('decision_status', 'disetujui')
            ->get();

        foreach ($maintenance as $data) {
            LaporanTahunanDetail::create([
                'id_laporan_tahunan' => $id,
                'jenis' => 'Maintenance',
                'id_referensi' => $data->id_maintenance,
            ]);
        }

        foreach ($pemusnahan as $data) {
            LaporanTahunanDetail::create([
                'id_laporan_tahunan' => $id,
                'jenis' => 'Pemusnahan',
                'id_referensi' => $data->id_pemusnahan,
            ]);
        }

        foreach ($pengadaan as $data) {
            LaporanTahunanDetail::create([
                'id_laporan_tahunan' => $id,
                'jenis' => 'Pengadaan',
                'id_referensi' => $data->id_pengadaan,
            ]);
        }
        $totalPengadaan = $pengadaan->count();

        $stokAwalTahun  = GudangRekapBulanan::where('tahun', $tahun - 1)->sum('stok_akhir');
        $stokAkhirTahun = GudangRekapBulanan::where('tahun', $tahun)->sum('stok_akhir');

        LaporanTahunanSummary::updateOrCreate(
            ['id_laporan_tahunan' => $id],
            [
                'total_pengadaan' => $totalPengadaan,
                'total_maintenance' => $maintenance->count(),
                'total_pemusnahan' => $pemusnahan->count(),
                'stok_awal_tahun' => $stokAwalTahun,
                'stok_akhir_tahun' => $stokAkhirTahun,
                'total_transaksi_gudang' =>
                    GudangTransaksi::whereYear('tanggal', $tahun)->count(),
            ]
        );

        return redirect()
            ->route('laporan_tahunan.show', $id)
            ->with('success', "Laporan tahun $tahun berhasil digenerate!");
    }

    public function show($id)
    {
        return view(
            'laporan_tahunan.show',
            $this->getLaporanData($id)
        );
    }

    private function getLaporanData($id)
    {
        $laporan = LaporanTahunan::with([
            'details.maintenance.aset',
            'details.maintenance.gedung',
            'details.maintenance.ruangan',
            'details.pemusnahan.aset',
            'details.pemusnahan.aset.gedung',
            'details.pemusnahan.aset.ruangan',
            'summary',
        ])->where('id_laporan_tahunan', $id)->firstOrFail();

        $tahun = $laporan->tahun;

        $pengadaanList = PengadaanBarangJasa::with('details')
            ->whereYear('created_at', $tahun)
            ->where('decision_status', 'disetujui')
            ->get();

        $totalPengadaan = $pengadaanList->count();

        $totalBiayaPengadaan = $pengadaanList->sum('total_biaya');

        $barangGudang = GudangRekapBulanan::with('barang')
            ->whereIn('tahun', [$tahun, $tahun - 1])
            ->get()
            ->groupBy('id_barang');

        $gudangList = [];
        $totalNilaiTransaksi = 0;

        foreach ($barangGudang as $idBarang => $rekap) {
            $barang = optional($rekap->first())->barang;

            $stokAwal = $rekap->where('tahun', $tahun - 1)->sum('stok_akhir');
            $stokAkhir = $rekap->where('tahun', $tahun)->sum('stok_akhir');

            $stokMasuk = \DB::table('gudang_transaksi_detail as d')
                ->join('gudang_transaksi as t', 't.id_transaksi', '=', 'd.id_transaksi')
                ->where('d.id_barang', $idBarang)
                ->whereYear('t.tanggal', $tahun)
                ->where('t.jenis_transaksi', 'masuk')
                ->sum('d.jumlah');

            $stokKeluar = \DB::table('gudang_transaksi_detail as d')
                ->join('gudang_transaksi as t', 't.id_transaksi', '=', 'd.id_transaksi')
                ->where('d.id_barang', $idBarang)
                ->whereYear('t.tanggal', $tahun)
                ->where('t.jenis_transaksi', 'keluar')
                ->sum('d.jumlah');

            $nilaiTransaksi = \DB::table('gudang_transaksi_detail as d')
                ->join('gudang_transaksi as t', 't.id_transaksi', '=', 'd.id_transaksi')
                ->where('d.id_barang', $idBarang)
                ->whereYear('t.tanggal', $tahun)
                ->sum('d.subtotal');

            $totalNilaiTransaksi += $nilaiTransaksi;

            $gudangList[] = [
                'nama_barang' => $barang->nama_barang ?? '-',
                'stok_awal' => $stokAwal,
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'stok_akhir' => $stokAkhir,
                'nilai_transaksi' => $nilaiTransaksi,
            ];
        }

        $jumlahJenisBarang = count($gudangList);

        $maintenanceList = Maintenance::with([
            'details.aset',
            'gedung',
            'ruangan'
        ])
        ->whereYear('tanggal_laporan', $tahun)
        ->get();

        $totalMaintenance = $maintenanceList->count();

        $totalBiayaMaintenance = $maintenanceList
            ->flatMap(fn($m) => $m->details)
            ->sum('biaya');

        $asetTermaintenance = $maintenanceList
            ->flatMap(fn($m) => $m->details)
            ->pluck('id_aset')
            ->unique()
            ->count();

        $pemusnahanList = LaporanPemusnahan::with([
            'gedung',
            'ruangan',
            'aset'
        ])
        ->whereYear('tanggal_pemusnahan', $tahun)
        ->get();

        $totalPemusnahan = $pemusnahanList->count();
        $totalBiayaKeluarPemusnahan = $pemusnahanList->sum('biaya_keluar');
        $totalNilaiMasukPemusnahan = $pemusnahanList->sum('nilai_masuk');

        return compact(
            'laporan',
            'pengadaanList',
            'totalPengadaan',
            'totalBiayaPengadaan',
            'gudangList',
            'jumlahJenisBarang',
            'totalNilaiTransaksi',
            'maintenanceList',
            'totalMaintenance',
            'totalBiayaMaintenance',
            'asetTermaintenance',
            'pemusnahanList',
            'totalPemusnahan',
            'totalBiayaKeluarPemusnahan',
            'totalNilaiMasukPemusnahan',
        );
    }


    public function exportPdf(Request $request, $id)
    {
        $section = $request->query('section');
        $data = $this->getLaporanData($id);

        if ($section) {
            $view = "laporan_tahunan.pdf.$section";
            $filename = "Laporan-{$id}-{$section}.pdf";
        } else {
            $view = "laporan_tahunan.pdf.full";
            $filename = "Laporan-Tahunan-{$id}.pdf";
        }

        return Pdf::loadView($view, $data)
            ->setPaper('A4', 'portrait')
            ->download($filename);

    }


}
