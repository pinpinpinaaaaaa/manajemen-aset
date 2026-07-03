<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GudangBarang;
use App\Models\GudangTransaksi;
use App\Models\GudangRekapBulanan;
use App\Models\GudangTransaksiDetail;
use App\Models\PermintaanBarangJasaDetail;
use Carbon\Carbon;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OpnameExport;

class GudangController extends Controller
{
    public function index()
    {
        $barang = GudangBarang::orderBy('nama_barang')->get();
        $totalBarang = $barang->count();
        $totalStok = $barang->sum('stok_akhir');
        $totalNilaiStok = 0;

        // Ambil harga terakhir semua barang sekaligus dalam 1 query (bukan N query).
        // "Terakhir" = transaksi masuk dengan t.tanggal terbesar dan harga_satuan > 0.
        $hargaMap = DB::table('gudang_transaksi_detail as d')
            ->join('gudang_transaksi as t', 't.id_transaksi', '=', 'd.id_transaksi')
            ->where('t.jenis_transaksi', 'masuk')
            ->where('d.harga_satuan', '>', 0)
            ->select('d.id_barang', 'd.harga_satuan', 't.tanggal')
            ->orderByDesc('t.tanggal')
            ->get()
            ->groupBy('id_barang')
            ->map(fn($rows) => $rows->first()->harga_satuan);

        foreach ($barang as $b) {
            $b->harga_terakhir = $hargaMap[$b->id_barang] ?? 0;
            $b->nilai_stok     = $b->harga_terakhir * $b->stok_akhir;
            $totalNilaiStok   += $b->nilai_stok;
        }

        $chartKeluar = DB::table('gudang_transaksi_detail as d')
            ->join('gudang_transaksi as t', 't.id_transaksi', '=', 'd.id_transaksi')
            ->join('gudang_barang as b', 'b.id_barang', '=', 'd.id_barang')
            ->select('b.nama_barang', DB::raw('SUM(d.jumlah) as total'))
            ->where('t.jenis_transaksi', 'keluar')
            ->where('t.status', 'approved')
            ->groupBy('b.nama_barang')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $chartKeluar_labels = $chartKeluar->pluck('nama_barang');
        $chartKeluar_values = $chartKeluar->pluck('total');

        $chartMasuk = DB::table('gudang_transaksi_detail as d')
            ->join('gudang_transaksi as t', 't.id_transaksi', '=', 'd.id_transaksi')
            ->join('gudang_barang as b', 'b.id_barang', '=', 'd.id_barang')
            ->select('b.nama_barang', DB::raw('SUM(d.jumlah) as total'))
            ->where('t.jenis_transaksi', 'masuk')
            ->where('t.status', 'approved')
            ->groupBy('b.nama_barang')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $chartMasuk_labels = $chartMasuk->pluck('nama_barang');
        $chartMasuk_values = $chartMasuk->pluck('total');

        $recent = DB::table('gudang_transaksi_detail as d')
            ->join('gudang_transaksi as t', 't.id_transaksi', '=', 'd.id_transaksi')
            ->join('gudang_barang as b', 'b.id_barang', '=', 'd.id_barang')
            ->select(
                't.tanggal',
                't.jenis_transaksi',
                'd.jumlah',
                'd.harga_satuan',
                'd.subtotal',
                'b.nama_barang',
                'b.satuan_dasar'
            )
            ->orderByDesc('t.created_at')
            ->limit(5)
            ->where('t.status', 'approved')
            ->get();

        $lowStock = GudangBarang::whereColumn('stok_akhir', '<=', 'limit_stok')
            ->orderBy('stok_akhir')
            ->get();

        return view('gudang.index', compact(
            'barang',
            'recent',
            'lowStock',
            'totalBarang',
            'totalStok',
            'totalNilaiStok',
            'chartKeluar_labels',
            'chartKeluar_values',
            'chartMasuk_labels',
            'chartMasuk_values'
        ));
    }

    /**
     * Generate ID Barang (AT001 / RT001)
     */
    private function generateIdBarang(string $jenis): string
    {
        $prefix = $jenis === 'atk' ? 'AT' : 'RT';

        $last = GudangBarang::where('id_barang', 'like', $prefix . '%')
                ->orderBy('id_barang', 'desc')
                ->lockForUpdate()
                ->first();

        $next = $last
                    ? (int) substr($last->id_barang, strlen($prefix)) + 1
                    : 1;

        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate ID Transaksi Gudang
     * Format: TRX-YYYYMMDD-0001
     */
    private function generateIdTransaksi(): string
    {
        $tanggal = now()->format('Ymd');

        $last = GudangTransaksi::lockForUpdate() 
            -> where('id_transaksi', 'like', "TRX-$tanggal-%")
            ->orderBy('id_transaksi', 'desc')
            ->first();

        $next = $last
                    ? (int) substr($last->id_transaksi, -4) + 1
                    : 1;

        return "TRX-$tanggal-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function generateIdRekapBulanan(int $tahun, int $bulan, string $idBarang): string
    {
        return sprintf('RB-%d%02d-%s', $tahun, $bulan, $idBarang);
    }

    private function getEnumJenis()
    {
        $column = DB::select("SHOW COLUMNS FROM gudang_barang WHERE Field = 'jenis'");
        preg_match("/^enum\((.*)\)$/", $column[0]->Type, $matches);

        return array_map(
            fn($val) => trim($val, "'"),
            explode(",", $matches[1])
        );
    }

    public function create(Request $request)
    {

        $namaBarang = $request->nama_barang;
        $permintaanId = $request->permintaan_id;
        $detailId = $request->detail_id;

        $enumValues = ['atk', 'rt'];

        return view('gudang.create', compact(
            'namaBarang',
            'permintaanId',
            'detailId',
            'enumValues'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'jenis' => 'required|in:atk,rt',
            'satuan' => 'required|string',
            'konversi_satuan' => 'required|integer|min:1',
            'satuan_dasar' => 'required|string',
            'limit_stok' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'permintaan_id' => 'nullable',
            'detail_id' => 'nullable'
        ]);

        DB::beginTransaction();

        try {

            $newId = $this->generateIdBarang($request->jenis);

            $barang = GudangBarang::create([
                'id_barang' => $newId,
                'nama_barang' => $request->nama_barang,
                'jenis' => $request->jenis,
                'satuan' => $request->satuan,
                'konversi_satuan' => $request->konversi_satuan,
                'satuan_dasar' => $request->satuan_dasar,
                'limit_stok' => $request->limit_stok ?? 0,
                'stok_awal' => 0,
                'stok_masuk' => 0,
                'stok_keluar' => 0,
                'stok_akhir' => 0,
                'foto_produk' => null,
                'keterangan' => $request->keterangan,
            ]);

            //Kalau berasal dari permintaan custom
            if ($request->permintaan_id && $request->detail_id) {

                PermintaanBarangJasaDetail::where('id', $request->detail_id)
                    ->update([
                        'id_barang' => $newId
                    ]);

                DB::commit();

                app(\App\Http\Controllers\PermintaanBarangJasaController::class)
                    ->complete($request->permintaan_id);

                return redirect()
                    ->route('permintaan-barang.index')
                    ->with('success', 'Barang berhasil dibuat dan permintaan dilanjutkan.');
            }

            DB::commit();

            return redirect()->route('gudang.index')
                ->with('success', "Barang berhasil ditambahkan! ID: $newId");

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $barang = GudangBarang::findOrFail($id);
        return view('gudang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = GudangBarang::findOrFail($id);
        $enumValues = $this->getEnumJenis();

        return view('gudang.edit', compact('barang', 'enumValues'));
    }

    public function update(Request $request, $id)
    {

        $barang = GudangBarang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required',
            'jenis' => 'required|in:atk,rt',
            'satuan' => 'required|string',
            'konversi_satuan' => 'required|integer|min:1',
            'satuan_dasar' => 'required|string',
            'limit_stok' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'jenis' => $request->jenis,
            'satuan' => $request->satuan,
            'konversi_satuan' => $request->konversi_satuan,
            'satuan_dasar' => $request->satuan_dasar,
            'limit_stok' => $request->limit_stok ?? 0,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('gudang.index')
            ->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $barang = GudangBarang::findOrFail($id);
        $barang->delete();

        return redirect()->route('gudang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
    
    public function rekapBulanan(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m'
        ]);

        $bulanDipilih = Carbon::createFromFormat('Y-m', $request->bulan)->startOfMonth();
        $bulanSekarang = now()->startOfMonth();

        if ($bulanDipilih->greaterThanOrEqualTo($bulanSekarang)) {
            return back()->with(
                'error',
                'Rekap hanya bisa dilakukan untuk bulan yang sudah selesai'
            );
        }

        $bulan = $bulanDipilih->month;
        $tahun = $bulanDipilih->year;

        if (GudangRekapBulanan::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists()) {

            return back()->with(
                'error',
                'Rekap bulan ini sudah pernah dilakukan'
            );
        }

        $barangList = GudangBarang::all();

        foreach ($barangList as $barang) {

            $idRekap = $this->generateIdRekapBulanan(
                $tahun,
                $bulan,
                $barang->id_barang
            );

            GudangRekapBulanan::create([
                'id_rekap'    => $idRekap,
                'id_barang'   => $barang->id_barang,
                'bulan'       => $bulan,
                'tahun'       => $tahun,
                'stok_awal'   => $barang->stok_awal,
                'stok_masuk'  => $barang->stok_masuk,
                'stok_keluar' => $barang->stok_keluar,
                'stok_akhir'  => $barang->stok_akhir,
            ]);

            $barang->stok_awal   = $barang->stok_akhir;
            $barang->stok_masuk  = 0;
            $barang->stok_keluar = 0;
            $barang->save();
        }

        return redirect()
            ->route('gudang.index')
            ->with(
                'success',
                'Rekap bulan ' . $request->bulan . ' berhasil diproses'
            );
    }

    public function laporan_transaksi(Request $request)
    {
        $query = GudangTransaksi::with(['details.barang'])
            ->orderBy('tanggal', 'desc');

        if ($request->dari) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }

        if ($request->sampai) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $transaksi = $query->get();

        return view('gudang.transaksi.index', compact('transaksi'));
    }



    public function laporan_opname(Request $request)
    {
        $query = \App\Models\GudangRekapBulanan::with('barang')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $rekap = $query->get();

        $tahunList = \App\Models\GudangRekapBulanan::select('tahun')
                        ->distinct()
                        ->orderBy('tahun', 'desc')
                        ->pluck('tahun');

        return view('gudang.laporan_opname', compact('rekap', 'tahunList'));
    }


    public function transaksiForm()
    {
        $barang = GudangBarang::orderBy('nama_barang')->get();
        return view('gudang.transaksi.create', compact('barang'));
    }

    public function transaksiStore(Request $request)
    {
        $request->validate([
            'jenis_transaksi' => 'required|in:masuk,keluar,penyesuaian',

            'alasan' => 'required_if:jenis_transaksi,keluar,penyesuaian',

            'tipe_penyesuaian' => 'nullable|required_if:jenis_transaksi,penyesuaian|in:tambah,kurang',

            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:gudang_barang,id_barang',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required_if:jenis_transaksi,masuk|numeric|min:1',
            'items.*.satuan_pilih' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $idTransaksi = $this->generateIdTransaksi();
            $totalBiaya = 0;

            $transaksi = GudangTransaksi::create([
                'id_transaksi' => $idTransaksi,
                'tanggal' => now(),
                'jenis_transaksi' => $request->jenis_transaksi,
                'dibuat_oleh' => auth()->user()->name ?? 'system',
                'total_biaya' => 0,
                'status' => 'pending',
                'alasan' => $request->alasan,
                'tipe_penyesuaian' => $request->tipe_penyesuaian,
            ]);

            foreach ($request->items as $item) {

                $barang = GudangBarang::lockForUpdate()
                    ->where('id_barang', $item['id_barang'])
                    ->firstOrFail();

                $jumlahInput = (int) $item['jumlah'];
                $satuanInput = $item['satuan_pilih'];

                if (!in_array($satuanInput, [$barang->satuan, $barang->satuan_dasar])) {
                    throw new \Exception("Satuan tidak valid untuk barang {$barang->nama_barang}");
                }

                $konversi = ($satuanInput === $barang->satuan_dasar)
                    ? 1
                    : (int) $barang->konversi_satuan;

                $jumlahReal = $jumlahInput * $konversi;

                $harga = $request->jenis_transaksi === 'masuk'
                    ? (float) $item['harga_satuan']
                    : 0;

                $subtotal = $harga * $jumlahInput;
                $totalBiaya += $subtotal;

                GudangTransaksiDetail::create([
                    'id_transaksi'   => $idTransaksi,
                    'id_barang'      => $barang->id_barang,

                    'jumlah_input'   => $jumlahInput,
                    'satuan'         => $satuanInput,
                    'konversi_pakai' => $konversi,
                    'jumlah'         => $jumlahReal,
                    'harga_satuan'   => $harga,
                    'subtotal'       => $subtotal,
                ]);
            }

            $transaksi->update([
                'total_biaya' => $totalBiaya
            ]);

            DB::table('gudang_transaksi_log')->insert([
                'id_transaksi' => $idTransaksi,
                'aksi' => 'create',
                'user' => auth()->user()->name ?? 'system',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()
                ->route('gudang.transaksi.index')
                ->with('success', "Transaksi berhasil disimpan (ID: $idTransaksi)");

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function approve($id)
    {
        if (auth()->user()->role->nama_role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang bisa approve');
        }

        DB::beginTransaction();
        
        try {

            $trx = GudangTransaksi::lockForUpdate()
                ->with('details')
                ->findOrFail($id);

            if ($trx->status !== 'pending') {
                throw new \Exception("Transaksi sudah diproses");
            }

            foreach ($trx->details as $detail) {
                $barang = GudangBarang::lockForUpdate()
                    ->findOrFail($detail->id_barang);

                switch ($trx->jenis_transaksi) {
                    case 'masuk':
                        $barang->stok_masuk += $detail->jumlah;
                        $barang->stok_akhir += $detail->jumlah;
                        break;

                    case 'keluar':
                        if ($barang->stok_akhir < $detail->jumlah) {
                            throw new \Exception("Stok tidak cukup");
                        }
                        $barang->stok_keluar += $detail->jumlah;
                        $barang->stok_akhir -= $detail->jumlah;
                        break;

                    case 'penyesuaian':
                        if ($trx->tipe_penyesuaian === 'tambah') {
                            $barang->stok_masuk += $detail->jumlah;
                            $barang->stok_akhir += $detail->jumlah;
                        } else {
                            if ($barang->stok_akhir < $detail->jumlah) {
                                throw new \Exception("Stok tidak cukup untuk penyesuaian");
                            }
                            $barang->stok_keluar += $detail->jumlah;
                            $barang->stok_akhir -= $detail->jumlah;
                        }
                        break;
                }

                $barang->save();
            }

            $trx->update([
                'status' => 'approved',
                'approved_by' => auth()->user()->name,
                'approved_at' => now()
            ]);
            
            DB::table('gudang_transaksi_log')->insert([
                'id_transaksi' => $id,
                'aksi' => 'approve',
                'user' => auth()->user()->name,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return back()->with('success', 'Transaksi di-approve');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        DB::beginTransaction();

        try {
            $trx = GudangTransaksi::findOrFail($id);

            if (auth()->user()->role->nama_role !== 'superadmin') {
                abort(403, 'Hanya superadmin yang bisa approve');
            }
            

            if ($trx->status !== 'pending') {
                throw new \Exception("Transaksi tidak bisa ditolak");
            }

            $trx->update([
                'status' => 'rejected',
                'approved_by' => auth()->user()->name,
                'approved_at' => now()
            ]);            

            DB::table('gudang_transaksi_log')->insert([
                'id_transaksi' => $id,
                'aksi' => 'reject',
                'user' => auth()->user()->name,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::commit();

            return back()->with('success', 'Transaksi ditolak');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id)
    {
        DB::beginTransaction();

        try {
            $trx = GudangTransaksi::findOrFail($id);

            if ($trx->status !== 'pending') {
                throw new \Exception("Transaksi tidak bisa dibatalkan");
            }

            $trx->update([
                'status' => 'cancelled'
            ]);

            DB::table('gudang_transaksi_log')->insert([
                'id_transaksi' => $id,
                'aksi' => 'cancel',
                'user' => auth()->user()->name ?? 'system',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return back()->with('success', 'Transaksi dibatalkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function transaksiIndex(Request $request)
    {
        $query = GudangTransaksi::with(['details.barang'])
            ->orderBy('tanggal', 'desc');

        if ($request->dari) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }

        if ($request->sampai) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $transaksi = $query->get();

        return view('gudang.transaksi.index', compact('transaksi'));
    }

    public function transaksiDetail($id)
    {
        $transaksi = GudangTransaksi::with('details.barang')
            ->where('id_transaksi', $id)
            ->firstOrFail();

        return view('gudang.transaksi.detail', compact('transaksi'));
    }

    public function stokOpnameIndex(Request $request)
    {
        $query = DB::table('gudang_opname_header')
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $data = $query->get();

        return view('gudang.stok_opname.index', compact('data'));
    }

    public function opnameMulai()
    {
        DB::beginTransaction();

        try {
            $kode = 'OPN-' . now()->format('Ymd-His');

            $header = DB::table('gudang_opname_header')->insertGetId([
                'kode_opname' => $kode,
                'id_user' => auth()->user()->id_user ?? null,
                'status' => 'draft',
                'started_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $barangList = GudangBarang::all();

            foreach ($barangList as $barang) {
                DB::table('gudang_opname_detail')->insert([
                    'id_opname' => $header,
                    'id_barang' => $barang->id_barang,
                    'stok_sistem' => $barang->stok_akhir,
                    'stok_fisik' => null,
                    'selisih' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('gudang.stok_opname.detail', $header);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function opnameDetail($id)
    {
        $header = DB::table('gudang_opname_header')->where('id', $id)->first();

        $details = DB::table('gudang_opname_detail as d')
            ->join('gudang_barang as b', 'b.id_barang', '=', 'd.id_barang')
            ->where('d.id_opname', $id)
            ->select('d.*', 'b.nama_barang')
            ->get();

        return view('gudang.stok_opname.detail', compact('header', 'details'));
    }

    public function opnameUpdate(Request $request, $id)
    {
        $request->validate([
            'stok_fisik' => 'required|integer|min:0'
        ]);

        $detail = DB::table('gudang_opname_detail')
            ->where('id', $id)
            ->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $selisih = $request->stok_fisik - $detail->stok_sistem;

        DB::table('gudang_opname_detail')
            ->where('id', $id)
            ->update([
                'stok_fisik' => $request->stok_fisik,
                'selisih' => $selisih,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'selisih' => $selisih
        ]);
    }

    public function opnameSelesai($id)
    {
        DB::beginTransaction();

        try {
            $header = DB::table('gudang_opname_header')
                ->where('id', $id)
                ->first();

            if ($header->status === 'selesai') {
                throw new \Exception("Sudah diselesaikan");
            }

            $details = DB::table('gudang_opname_detail')
                ->where('id_opname', $id)
                ->get();

            DB::table('gudang_opname_header')
                ->where('id', $id)
                ->update([
                    'status' => 'selesai',
                    'finished_at' => now()
                ]);

            DB::commit();

            return redirect()->route('gudang.stok_opname.index')
                ->with('success', 'Opname selesai');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportOpnamePdf($id)
    {
        $header = DB::table('gudang_opname_header')->where('id', $id)->first();

        $details = DB::table('gudang_opname_detail as d')
            ->join('gudang_barang as b', 'b.id_barang', '=', 'd.id_barang')
            ->where('d.id_opname', $id)
            ->select('d.*', 'b.nama_barang')
            ->get();

        $totalSelisih = $details->sum('selisih');

        $pdf = Pdf::loadView('gudang.stok_opname.pdf', [
            'header' => $header,
            'details' => $details,
            'totalSelisih' => $totalSelisih
        ])->setPaper('A4', 'portrait');

        return $pdf->download('stok-opname-'.$header->kode_opname.'.pdf');
    }

    public function exportOpnameExcel($id)
    {
        return Excel::download(new OpnameExport($id), 'stok-opname.xlsx');
    }
}
