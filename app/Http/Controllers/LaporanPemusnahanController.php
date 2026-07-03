<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPemusnahan;
use App\Models\Aset;
use App\Models\Ruangan;
use App\Models\Gedung;
use App\Models\Vendor;
use Carbon\Carbon;
use App\Services\AsetLogService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemusnahanExport;

class LaporanPemusnahanController extends Controller
{

    private function summaryData($laporan)
    {
        return [
            'laporan'=>$laporan,
            'totalKeluar'=>$laporan->sum('biaya_keluar'),
            'totalMasuk'=>$laporan->sum('nilai_masuk'),
            'totalSelesai'=>$laporan->where('status','Selesai')->count(),
            'totalDitolak'=>$laporan->where('decision_status','ditolak')->count(),
        ];
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = LaporanPemusnahan::with('aset')
            ->where(function($q){
                $q->where('status','Selesai')
                ->orWhere('decision_status','ditolak');
            });

        if ($request->start_date) {
            $query->whereDate(
                'tanggal_pemusnahan',
                '>=',
                $request->start_date
            );
        }

        if ($request->end_date) {
            $query->whereDate(
                'tanggal_pemusnahan',
                '<=',
                $request->end_date
            );
        }

        return $query;
    }

    private function generatePemusnahanId(): string
    {
        $tanggal = now()->format('Ymd');

        $last = LaporanPemusnahan::where('id_pemusnahan', 'like', "PMN-$tanggal-%")
            ->lockForUpdate()
            ->orderBy('id_pemusnahan', 'desc')
            ->first();

        $next = $last
            ? (int) substr($last->id_pemusnahan, -4) + 1
            : 1;

        return "PMN-$tanggal-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $pemusnahan = LaporanPemusnahan::with([
            'aset',
            'vendor'
        ])
        ->where(function($q){
            $q->where('status','!=','Selesai')
            ->orWhereNull('status');
        })
        ->where(function($q){
            $q->where('decision_status','!=','ditolak')
            ->orWhereNull('decision_status');
        })
        ->orderBy('tanggal_pemusnahan','desc')
        ->paginate($perPage);

        $vendors = Vendor::orderBy('nama_perusahaan')
            ->get();

        return view(
            'laporan_pemusnahan.index',
            compact('pemusnahan','vendors','perPage')
        );
    }

    public function getRuangan($id_gedung)
    {
        return Ruangan::where('id_gedung', $id_gedung)->get();
    }

    public function getAset($id_ruangan)
    {
        return Aset::where('id_ruangan', $id_ruangan)->get();
    }

    public function create()
    {
        return view('laporan_pemusnahan.create', [
            'gedung' => Gedung::all(),
            'aset' => null,
            'vendors' => Vendor::all()
        ]);
    }

    public function createWithAset($id_aset)
    {
        $aset = Aset::with(['gedung', 'ruangan'])->findOrFail($id_aset);

        return view('laporan_pemusnahan.create', [
            'aset'   => $aset,
            'gedung' => collect([$aset->gedung]),
            'vendors'=> Vendor::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_gedung' => 'required',
            'id_ruangan' => 'required',
            'id_aset' => 'required|exists:aset,id_aset',
            'tanggal_pemusnahan' => 'required|date',
            'metode' => 'required|string',
            'biaya_keluar' => 'nullable|numeric|min:0',
            'nilai_masuk' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'pelaksana_type' => 'required|in:internal,vendor,lainnya',
            'id_vendor' => [
                Rule::requiredIf($request->pelaksana_type === 'vendor'),
                'nullable',
                'exists:vendors,id_vendor'
            ],
            'vendor_manual' => [
                Rule::requiredIf($request->pelaksana_type === 'lainnya'),
                'nullable',
                'string',
                'max:255'
            ],
        ]);

        $aset = Aset::findOrFail($request->id_aset);

        if (
            LaporanPemusnahan::where('id_aset',$aset->id_aset)
                ->where('status','!=','Selesai')
                ->where('decision_status','!=','ditolak')
                ->exists()
        ) {
            return back()->with('error', 'Aset masih memiliki pemusnahan aktif!');
        }

        $id_pemusnahan = DB::transaction(function () use ($request, $aset) {
            $id_pemusnahan = $this->generatePemusnahanId();

            $biayaKeluar = $request->biaya_keluar ?? 0;
            $nilaiMasuk  = $request->nilai_masuk ?? 0;

            $pelaksanaType = $request->pelaksana_type;
            $idVendor = null;
            $catatanTambahan = '';

            if ($pelaksanaType === 'vendor') {
                $idVendor = $request->id_vendor;
            }

            if ($pelaksanaType === 'lainnya') {
                $catatanTambahan = "\nVendor manual: ".$request->vendor_manual;
            }

            LaporanPemusnahan::create([
                'id_pemusnahan' => $id_pemusnahan,
                'id_aset' => $aset->id_aset,
                'id_gedung' => $aset->id_gedung,
                'id_ruangan' => $aset->id_ruangan,
                'tanggal_pemusnahan' => $request->tanggal_pemusnahan,
                'metode' => $request->metode,
                'biaya_keluar' => $biayaKeluar,
                'nilai_masuk' => $nilaiMasuk,
                'requested_by' => auth()->user()->id_user,
                'decision_status' => 'menunggu_persetujuan',
                'status' => 'Belum Dimusnahkan',
                'pelaksana_type' => $pelaksanaType,
                'id_vendor' => $idVendor,
                'catatan' => ($request->catatan ?? '').$catatanTambahan,
            ]);

            $aset->update([
                'status' => 'non aktif',
                'kelayakan' => 3,
                'keterangan_kelayakan' => 'Perlu pemantauan',
            ]);

            AsetLogService::log(
                $aset->id_aset,
                'permintaan_pemusnahan',
                $id_pemusnahan,
                'Permintaan pemusnahan diajukan'
            );

            return $id_pemusnahan;
        });

        return redirect()->route('laporan_pemusnahan.index')
            ->with('success', "Laporan pemusnahan #{$id_pemusnahan} berhasil ditambahkan!");
    }

    public function show($id)
    {
        $laporan = LaporanPemusnahan::with([
            'aset.gedung',
            'aset.ruangan',
            'vendor',
            'requester',
            'decider'
        ])->findOrFail($id);

        return view('laporan_pemusnahan.show', compact('laporan'));
    }

    public function edit($id)
    {
        $laporan = LaporanPemusnahan::with([
            'aset.gedung',
            'aset.ruangan'
        ])->findOrFail($id);

        return view('laporan_pemusnahan.edit', [
            'laporan' => $laporan,
            'aset'    => $laporan->aset,
            'vendors' => Vendor::all(),
            'gedung'  => collect([$laporan->aset->gedung])
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'id_aset' => 'required|exists:aset,id_aset',
            'tanggal_pemusnahan' => 'required|date',
            'metode' => 'required|string',
            'biaya_keluar' => 'nullable|numeric|min:0',
            'nilai_masuk' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(
                    in_array($request->metode,['Lelang','Dijual'])
                )
            ],
            'catatan' => 'nullable|string',
            'pelaksana_type' => 'nullable|in:internal,vendor,lainnya',
            'id_vendor' => [
                Rule::requiredIf($request->pelaksana_type === 'vendor'),
                'nullable',
                'exists:vendors,id_vendor'
            ],
            'vendor_manual' => [
                Rule::requiredIf($request->pelaksana_type === 'lainnya'),
                'nullable',
                'string',
                'max:255'
            ],
        ]);

        $laporan = LaporanPemusnahan::findOrFail($id);
        if ($laporan->status === 'Selesai') {
            return back()->with('error','Metode tidak bisa diubah setelah pemusnahan selesai');
        }

        $aset = Aset::findOrFail($request->id_aset);

        if ($laporan->metode !== $request->metode) {
            AsetLogService::log(
                $laporan->id_aset,
                'perubahan_metode_pemusnahan',
                $laporan->id_pemusnahan,
                "Metode diubah dari {$laporan->metode} menjadi {$request->metode}"
            );
        }

        $biayaKeluar = $request->biaya_keluar ?? 0;
        $nilaiMasuk  = $request->nilai_masuk ?? 0;

        $pelaksanaType = $request->pelaksana_type;
        $idVendor = null;
        $catatanTambahan = '';

        if ($pelaksanaType === 'vendor') {
            $idVendor = $request->id_vendor;
        }

        if ($pelaksanaType === 'lainnya') {
            $catatanTambahan = "\nVendor manual: ".$request->vendor_manual;
        }

        $laporan->update([
            'id_aset' => $aset->id_aset,
            'tanggal_pemusnahan' => $request->tanggal_pemusnahan,
            'metode' => $request->metode,
            'catatan' => ($request->catatan ?? '').$catatanTambahan,
            'biaya_keluar' => $biayaKeluar,
            'nilai_masuk' => $nilaiMasuk,
            'pelaksana_type' => $pelaksanaType,
            'id_vendor' => $idVendor,
        ]);


        return redirect()->route('laporan_pemusnahan.index')
            ->with('success', "Laporan pemusnahan #{$laporan->id_pemusnahan} berhasil diperbarui!");
    }


    public function destroy($id)
    {
        $laporan = LaporanPemusnahan::with('aset')->findOrFail($id);

        if ($laporan->aset) {
            $laporan->aset->update([
                'status' => 'tersedia',
                'kelayakan' => 1,
                'keterangan_kelayakan' => 'Layak'
            ]);

            AsetLogService::log(
                $laporan->id_aset,
                'rollback_pemusnahan',
                $laporan->id_pemusnahan,
                'Laporan pemusnahan dihapus, aset diaktifkan kembali'
            );
        }

        $laporan->delete();

        return redirect()
            ->route('laporan_pemusnahan.index')
            ->with('success', 'Laporan pemusnahan berhasil dihapus dan aset dikembalikan');
    }

    public function approve($id)
    {
        $laporan = LaporanPemusnahan::findOrFail($id);
        if ($laporan->decision_status !== 'menunggu_persetujuan') {
            return back()->with('error','Status tidak valid');
        }
        $laporan->update([
            'decision_status' => 'disetujui',
            'decided_by' => auth()->user()->id_user,
            'decided_at' => now(),
        ]);

        return back()->with('success','Permintaan disetujui');
    }

    public function proses($id)
    {
        $laporan = LaporanPemusnahan::findOrFail($id);
        if ($laporan->decision_status !== 'disetujui') {
            return back()->with('error','Belum disetujui');
        }

        $laporan->update([
            'status' => 'Sedang Dimusnahkan'
        ]);

        return back()->with('success','Pemusnahan diproses');
    }

    public function selesai(Request $request, $id)
    {
        $laporan = LaporanPemusnahan::with(['aset','vendor'])
            ->findOrFail($id);

        if ($laporan->status !== 'Sedang Dimusnahkan') {
            return back()->with('error','Belum dalam proses');
        }

        $request->validate([
            'pelaksana_type' => 'required|in:internal,vendor',
            'id_vendor'      => 'nullable',
            'nilai_masuk'    => 'nullable|numeric|min:0',
        ]);

        $laporan->update([
            'pelaksana_type' => $request->pelaksana_type,
            'id_vendor'      => $request->pelaksana_type == 'vendor'
                ? $request->id_vendor
                : null,
            'nilai_masuk'    => $request->nilai_masuk ?? 0,
        ]);

        if (!$laporan->pelaksana_type) {
            return back()->with(
                'error',
                'Pilih pelaksana (Internal / Vendor) sebelum menyelesaikan!'
            );
        }

        if (
            $laporan->pelaksana_type === 'vendor'
            && !$laporan->id_vendor
        ) {
            return back()->with(
                'error',
                'Vendor harus dipilih sebelum selesai!'
            );
        }

        if (
            in_array($laporan->metode, ['Lelang','Dijual'])
            && ($laporan->nilai_masuk <= 0)
        ) {
            return back()->with(
                'error',
                'Nilai hasil penjualan harus diisi sebelum selesai'
            );
        }

        $laporan->update([
            'status' => 'Selesai'
        ]);

        if ($laporan->aset) {
            $laporan->aset->update([
                'status' => 'non aktif',
                'kelayakan' => 5,
                'keterangan_kelayakan' => $laporan->metode,
            ]);

            AsetLogService::log(
                $laporan->id_aset,
                'pemusnahan_selesai',
                $laporan->id_pemusnahan,
                'Aset selesai dimusnahkan'
            );
        }

        $ket = $laporan->vendor
            ? "Vendor: ".$laporan->vendor->nama_perusahaan
            : $laporan->pelaksana_type;

        AsetLogService::log(
            $laporan->id_aset,
            'pelaksana_pemusnahan',
            $laporan->id_pemusnahan,
            "Dilaksanakan oleh {$ket}"
        );

        return back()->with('success','Pemusnahan selesai');
    }


    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000'
        ]);

        $laporan = LaporanPemusnahan::with('aset')->findOrFail($id);

        if ($laporan->status === 'Selesai') {
            return back()->with(
                'error',
                'Tidak bisa menolak pemusnahan yang sudah selesai'
            );
        }

        $laporan->update([
            'decision_status' => 'ditolak',
            'catatan'         => $request->catatan,
            'decided_by'      => auth()->user()->id_user,
            'decided_at'      => now(),
        ]);

        if ($laporan->aset) {

            $laporan->aset->update([
                'status' => 'tersedia',
                'kelayakan' => 1,
                'keterangan_kelayakan' => 'Layak'
            ]);

            AsetLogService::log(
                $laporan->id_aset,
                'pemusnahan_ditolak',
                $laporan->id_pemusnahan,
                'Permintaan pemusnahan ditolak. Alasan: ' . $request->catatan
            );
        }

        return back()->with(
            'success',
            'Permintaan pemusnahan ditolak'
        );
    }

    public function laporan(Request $request)
    {
        $laporan = $this->buildFilteredQuery($request)
            ->orderBy('tanggal_pemusnahan','desc')
            ->get();

        return view('laporan_pemusnahan.laporan',
            $this->summaryData($laporan)
        );
    }

    public function exportPdf(Request $request)
    {
        $laporan = $this->buildFilteredQuery($request)->get();

        $data = array_merge(
            $this->summaryData($laporan),
            [
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
            ]
        );

        $pdf = Pdf::loadView(
            'laporan_pemusnahan.pdf',
            $data
        )->setPaper('A4','portrait');

        return $pdf->download('laporan-pemusnahan.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PemusnahanExport(
                $request->start_date,
                $request->end_date
            ),
            'laporan-pemusnahan.xlsx'
        );
    }

}
