<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PermintaanBarangGudang;
use App\Models\PermintaanBarangGudangDetail;
use App\Models\GudangBarang;
use App\Models\GudangTransaksi;
use App\Models\GudangTransaksiDetail;
use App\Models\Divisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermintaanBarangGudangExport;

class PermintaanBarangGudangController extends Controller
{

    private function createGudangTransaksi(
    string $jenis,
    string $referensi,
    array $items
    ) {
        $idTransaksi = $this->generateTransaksiId();
        $totalBiaya = 0;

        $transaksi = GudangTransaksi::create([
            'id_transaksi' => $idTransaksi,
            'tanggal' => now(),
            'jenis_transaksi' => $jenis,
            'referensi' => $referensi,
            'dibuat_oleh' => auth()->user()->name ?? 'system',
            'total_biaya' => 0,
            'status' => 'approved',
            'approved_by' => auth()->user()->id_user ?? null,
            'approved_at' => now(),
        ]);

        foreach ($items as $item) {

            $barang = GudangBarang::lockForUpdate()
                ->where('id_barang', $item['id_barang'])
                ->firstOrFail();

            $jumlahInput = $item['jumlah'];

            $satuanDipilih = $item['satuan_pilih'] ?? $barang->satuan_dasar;

            if ($satuanDipilih === $barang->satuan_dasar) {

                $konversi = 1;
                $jumlahReal = $jumlahInput;

            } else {

                $konversi = max((int) $barang->konversi_satuan, 1);
                $jumlahReal = $jumlahInput * $konversi;
            }

            if ($jenis === 'keluar' && $barang->stok_akhir < $jumlahReal) {
                throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi");
            }

            $harga = $item['harga_satuan'] ?? 0;
            $subtotal = $harga * $jumlahInput;
            $totalBiaya += $subtotal;

            GudangTransaksiDetail::create([
                'id_transaksi' => $idTransaksi,
                'id_barang' => $barang->id_barang,
                'jumlah_input' => $jumlahInput,
                'satuan' => $satuanDipilih,
                'konversi_pakai' => $konversi,
                'jumlah' => $jumlahReal,
                'harga_satuan' => $harga,
                'subtotal' => $subtotal,
            ]);

            if ($jenis === 'keluar') {

                $barang->stok_keluar += $jumlahReal;
                $barang->stok_akhir -= $jumlahReal;

                if ($barang->stok_akhir < 0) {
                    throw new \Exception("Stok tidak boleh minus");
                }

                $barang->save();
            }
        }

        $transaksi->update([
            'total_biaya' => $totalBiaya
        ]);
    }

    private function generatePermintaanId(): string
    {
        $date = now()->format('Ymd');

        $last = PermintaanBarangGudang::where(
            'id_permintaan',
            'like',
            "PRM-$date-%"
        )->lockForUpdate()->orderByDesc('id_permintaan')->first();

        $next = $last
            ? (int) substr($last->id_permintaan, -4) + 1
            : 1;

        return "PRM-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function generateTransaksiId(): string
    {
        $date = now()->format('Ymd');

        $last = GudangTransaksi::where(
            'id_transaksi',
            'like',
            "TRX-$date-%"
        )->lockForUpdate()->orderByDesc('id_transaksi')->first();

        $next = $last
            ? (int) substr($last->id_transaksi, -4) + 1
            : 1;

        return "TRX-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function buildRiwayatQuery(Request $request)
    {
        $query = PermintaanBarangGudang::with(['divisi','details'])
            ->where(function($q){
                $q->where('status', 'Selesai')
                ->orWhere('decision_status', 'ditolak');
            });

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return $query;
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $permintaan = PermintaanBarangGudang::with(['divisi','details'])
            ->where('status', '!=', 'Selesai')
            ->latest()
            ->paginate($perPage);

        return view('permintaan_barang.index', compact('permintaan', 'perPage'));
    }

    public function riwayat(Request $request)
    {
        $laporan = $this->buildRiwayatQuery($request)
            ->latest()
            ->get();

        $totalSelesai = $laporan->where('status','Selesai')->count();
        $totalDitolak = $laporan->where('decision_status','ditolak')->count();

        return view('permintaan_barang.riwayat', compact(
            'laporan',
            'totalSelesai',
            'totalDitolak'
        ));
    }

    public function show($id)
    {
        $permintaan = PermintaanBarangGudang::with(['divisi','details'])
            ->findOrFail($id);

        return view('permintaan_barang.show', compact('permintaan'));
    }

    public function edit($id)
    {
        $permintaan = PermintaanBarangGudang::with('details')
            ->findOrFail($id);

        $divisi = Divisi::all();
        $gudang = GudangBarang::all();

        return view('permintaan_barang.edit', compact(
            'permintaan',
            'divisi',
            'gudang'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pengaju' => 'required|string|max:100',
            'email_pengaju' => 'required|email|max:100',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'tanggal_kebutuhan' => 'required|date',
            'items' => 'required|array|min:1',
            'catatan' => 'nullable|string|max:500',
        ], [
            'id_divisi.exists' => 'Divisi yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($request, $id) {

            $permintaan = PermintaanBarangGudang::with('details')->findOrFail($id);

            $permintaan->update([
                'nama_pengaju' => $request->nama_pengaju,
                'email_pengaju' => $request->email_pengaju,
                'id_divisi' => $request->id_divisi,
                'alasan' => $request->alasan,
                'tanggal_kebutuhan' => $request->tanggal_kebutuhan,
                'catatan' => $request->catatan,
            ]);

            foreach ($permintaan->details as $detail) {
                $barang = GudangBarang::where('id_barang', $detail->id_barang)->first();

                if ($barang) {
                    $barang->stok_dipesan = max(0, $barang->stok_dipesan - $detail->jumlah);
                    $barang->save();
                }
            }

            $permintaan->details()->delete();

            foreach ($request->items as $item) {

                $barang = GudangBarang::lockForUpdate()
                    ->where('id_barang', $item['id_barang'])
                    ->firstOrFail();

                $stokTersedia = $barang->stok_akhir - $barang->stok_dipesan;

                $jumlahReal = (int) $item['jumlah'];

                if ($jumlahReal > $stokTersedia) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi (tersedia: $stokTersedia)");
                }

                PermintaanBarangGudangDetail::create([
                    'id_permintaan' => $permintaan->id_permintaan,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $jumlahReal,
                    'satuan' => $barang->satuan_dasar,
                ]);

                $barang->stok_dipesan += $jumlahReal;
                $barang->save();
            }
        });

        return back()->with('success', 'Permintaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $permintaan = PermintaanBarangGudang::with('details.barang')->findOrFail($id);

            if ($permintaan->status !== 'Belum Diproses') {
                abort(403, 'Tidak bisa dihapus.');
            }

            foreach ($permintaan->details as $detail) {
                if ($detail->barang) {
                    $detail->barang->stok_dipesan = max(0, $detail->barang->stok_dipesan - $detail->jumlah);
                    $detail->barang->save();
                }
            }

            $permintaan->delete();
        });

        return redirect()->route('permintaan-barang.index')
            ->with('success', 'Permintaan berhasil dihapus.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pengaju' => 'required|string|max:100',
            'email_pengaju' => 'required|email|max:100',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'tanggal_kebutuhan' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:gudang_barang,id_barang',
            'items.*.jumlah' => 'required|numeric|min:1',
            'catatan' => 'nullable|string|max:500',
        ], [
            'id_divisi.exists'         => 'Divisi yang dipilih tidak valid.',
            'items.*.id_barang.exists' => 'Barang yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($request) {

            $id = $this->generatePermintaanId();

            $permintaan = PermintaanBarangGudang::create([
                'id_permintaan' => $id,
                'nama_pengaju' => $request->nama_pengaju,
                'email_pengaju' => $request->email_pengaju,
                'id_divisi' => $request->id_divisi,
                'alasan' => $request->alasan,
                'tanggal_kebutuhan' => $request->tanggal_kebutuhan,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {

                $barang = GudangBarang::lockForUpdate()
                    ->where('id_barang', $item['id_barang'])
                    ->firstOrFail();

                $jumlahInput = (int) $item['jumlah'];
                $jumlahReal = $jumlahInput;

                $stokTersedia = $barang->stok_akhir - $barang->stok_dipesan;

                if ($jumlahReal > $stokTersedia) {
                    throw new \Exception(
                        "Stok {$barang->nama_barang} tidak mencukupi (tersedia: $stokTersedia)"
                    );
                }

                PermintaanBarangGudangDetail::create([
                    'id_permintaan' => $permintaan->id_permintaan,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $jumlahReal,
                    'satuan' => $barang->satuan_dasar,
                ]);

                $barang->stok_dipesan += $jumlahReal;
                $barang->save();
            }
        });

        return redirect()->route('form-permintaan-barang.create')
            ->with('success', 'Permintaan berhasil diajukan.');
    }

    public function create()
    {
        $divisi = Divisi::all();
        $gudang = GudangBarang::all(); 

        return view('permintaan_barang.form', compact('divisi', 'gudang'));
    }

    public function approve($id)
    {
        
        $permintaan = PermintaanBarangGudang::findOrFail($id);

        if ($permintaan->decision_status !== 'menunggu_persetujuan') {
            abort(403, 'Sudah diputuskan');
        }
        $permintaan->update([
            'decision_status' => 'disetujui',
            'decided_by' => auth()->user()->id_user,
            'decided_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Permintaan disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000'
        ]);

        DB::transaction(function () use ($id, $request) {

            $permintaan = PermintaanBarangGudang::with('details.barang')
                ->findOrFail($id);

            foreach ($permintaan->details as $detail) {

                $barang = $detail->barang;

                if ($barang) {
                    $barang->stok_dipesan = max(
                        0,
                        $barang->stok_dipesan - $detail->jumlah
                    );

                    $barang->save();
                }
            }

            $permintaan->update([
                'decision_status' => 'ditolak',
                'status' => 'Selesai',
                'catatan' => $request->catatan,
                'decided_by' => auth()->user()->id_user,
                'decided_at' => now(),
            ]);
        });

        return back()->with('success', 'Permintaan ditolak.');
    }

    public function process($id)
    {
        $permintaan = PermintaanBarangGudang::findOrFail($id);

        if ($permintaan->decision_status !== 'disetujui') {
            abort(403, 'Belum disetujui.');
        }

        if ($permintaan->status !== 'Belum Diproses') {
            abort(403, 'Sudah diproses.');
        }

        $permintaan->update([
            'status' => 'Sedang Diproses'
        ]);

        return back()->with('success', 'Permintaan sedang diproses.');
    }

    public function markAvailable($id)
    {
        DB::beginTransaction();

        try {
            $permintaan = PermintaanBarangGudang::with('details.barang')
                ->findOrFail($id);

            if ($permintaan->status !== 'Sedang Diproses') {
                abort(403, 'Belum diproses.');
            }

            foreach ($permintaan->details as $detail) {

                if (!$detail->barang) {
                    throw new \Exception("Barang tidak ditemukan di gudang.");
                }

                $stokTersedia = $detail->barang->stok_akhir - $detail->barang->stok_dipesan + $detail->jumlah;

                if ($stokTersedia < $detail->jumlah) {
                    throw new \Exception("Stok {$detail->barang->nama_barang} tidak cukup.");
                }
            }

            $permintaan->update([
                'status' => 'Tersedia'
            ]);

            DB::commit();

            return back()->with('success', 'Barang tersedia.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete($id)
    {
        DB::beginTransaction();

        try {
            $permintaan = PermintaanBarangGudang::with('details')
                ->findOrFail($id);

            if ($permintaan->status !== 'Tersedia') {
                abort(403, 'Barang belum tersedia.');
            }

            $items = [];

            foreach ($permintaan->details as $detail) {

                $items[] = [
                    'id_barang' => $detail->id_barang,
                    'jumlah' => $detail->jumlah,
                    'harga_satuan' => 0,
                ];
            }

            $this->createGudangTransaksi(
                'keluar',
                $permintaan->id_permintaan,
                $items
            );

            foreach ($permintaan->details as $detail) {

                $barang = GudangBarang::lockForUpdate()
                    ->where('id_barang', $detail->id_barang)
                    ->first();

                $barang->stok_dipesan = max(
                    0,
                    $barang->stok_dipesan - $detail->jumlah
                );

                $barang->save();
            }

            $permintaan->update([
                'status' => 'Selesai'
            ]);

            DB::commit();

            return back()->with('success', 'Permintaan selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportPdfRiwayat(Request $request)
    {
        $laporan = $this->buildRiwayatQuery($request)->get();

        $totalSelesai = $laporan
            ->where('status','Selesai')
            ->where('decision_status','disetujui')
            ->count();

        $totalDitolak = $laporan
            ->where('decision_status','ditolak')
            ->count();

        $totalBiaya = $laporan
            ->where('status','Selesai')
            ->where('decision_status','disetujui')
            ->sum('total_biaya');

        $pdf = Pdf::loadView(
            'permintaan_barang.pdf',
            [
                'laporan'      => $laporan,
                'totalSelesai' => $totalSelesai,
                'totalDitolak' => $totalDitolak,
                'totalBiaya'   => $totalBiaya,
                'start_date'   => $request->start_date,
                'end_date'     => $request->end_date,
            ]
        )->setPaper('A4','portrait');

        return $pdf->download('PERMINTAAN-BARANG-GUDANG.pdf');
    }

    public function exportExcelRiwayat(Request $request)
    {
        return Excel::download(
            new PermintaanBarangGudangExport(
                $request->start_date,
                $request->end_date
            ),
            'PERMINTAAN-BARANG-GUDANG.xlsx'
        );
    }

}
