<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\PengadaanBarangJasa;
use App\Models\PengadaanBarangJasaDetail;
use App\Models\PengadaanBarangJasaFile;
use App\Models\Divisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Aset;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\JenisBarang;

class PengadaanBarangJasaController extends Controller
{
    /* ================= HELPER ================= */

    private function generateIdAset()
    {
        $latest = Aset::selectRaw("CAST(SUBSTRING(id_aset, 2) AS UNSIGNED) AS num")
            ->orderByDesc('num')
            ->lockForUpdate()
            ->first();

        if (!$latest) {
            return 'A0001';
        }

        return 'A' . str_pad($latest->num + 1, 4, '0', STR_PAD_LEFT);
    }

    private function generateKodeAset($idJenisBarang, $tahun)
    {
        $barang = JenisBarang::findOrFail($idJenisBarang);

        $prefix = strtoupper($barang->prefix_kode);
        $jenis = strtoupper($barang->jenis);
        $kategori = strtoupper(str_replace(' ', '_', $barang->kategori));

        $maxNomor = 0;

        $asetList = Aset::where('id_jenis_barang', $barang->id_jenis_barang)
            ->lockForUpdate()
            ->pluck('kode_aset');

        foreach ($asetList as $kode) {

            if (preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $kode, $match)) {
                $maxNomor = max($maxNomor, (int) $match[1]);
            }
        }

        $nomor = $maxNomor + 1;

        return sprintf(
            'ASET/%s/%s/%s%s/%s',
            $jenis,
            $kategori,
            $prefix,
            str_pad($nomor, 3, '0', STR_PAD_LEFT),
            $tahun
        );
    }

    private function generateId(): string
    {
        $date = now()->format('Ymd');

        $last = PengadaanBarangJasa::where(
            'id_pengadaan',
            'like',
            "PGD-$date-%"
        )->lockForUpdate()->orderByDesc('id_pengadaan')->first();

        $next = $last
            ? (int) substr($last->id_pengadaan, -4) + 1
            : 1;

        return "PGD-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function buildRiwayatQuery(Request $request)
    {
        $query = PengadaanBarangJasa::with(['divisi', 'details.files'])
            ->where(function ($q) {
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

    /* ================= CRUD ================= */

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $pengadaan = PengadaanBarangJasa::with(['divisi', 'details.files'])
            ->where('status', '!=', 'Selesai')
            ->latest()
            ->paginate($perPage);

        $jenisBarang = JenisBarang::orderBy('nama_barang')->get();
        $gedung = Gedung::orderBy('nama_gedung')->get();
        $ruangan = Ruangan::orderBy('nama_ruangan')->get();

        return view('pengadaan_barang.index', compact(
            'pengadaan',
            'jenisBarang',
            'gedung',
            'ruangan',
            'perPage'
        ));
    }

    public function riwayat(Request $request)
    {
        $laporan = $this->buildRiwayatQuery($request)
            ->latest()
            ->get();

        $totalSelesai = $laporan
            ->where('status', 'Selesai')
            ->where('decision_status', 'disetujui')
            ->count();

        $totalDitolak = $laporan
            ->where('decision_status', 'ditolak')
            ->count();

        return view('pengadaan_barang.riwayat', compact(
            'laporan',
            'totalSelesai',
            'totalDitolak'
        ));
    }

    public function show($id)
    {
        $pengadaan = PengadaanBarangJasa::with(['divisi', 'details.files'])
            ->findOrFail($id);

        return view('pengadaan_barang.show', compact('pengadaan'));
    }

    public function create()
    {
        $divisi = Divisi::all();
        return view('pengadaan_barang.form', compact('divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pengaju' => 'required',
            'email_pengaju' => 'required|email',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'tanggal_kebutuhan' => 'required|date',
            'items' => 'required|array|min:1',
            'catatan' => 'nullable|string',
            'items.*.files' => 'nullable|array|max:5',
            'items.*.files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'nama_pengaju.required'       => 'Nama pengaju wajib diisi.',
            'email_pengaju.required'      => 'Email pengaju wajib diisi.',
            'email_pengaju.email'         => 'Format email tidak valid.',
            'id_divisi.required'          => 'Divisi wajib dipilih.',
            'id_divisi.exists'            => 'Divisi yang dipilih tidak valid.',
            'tanggal_kebutuhan.required'  => 'Tanggal kebutuhan wajib diisi.',
            'tanggal_kebutuhan.date'      => 'Format tanggal tidak valid.',
            'items.required'              => 'Minimal 1 item pengadaan harus diisi.',
            'items.min'                   => 'Minimal 1 item pengadaan harus diisi.',
            'items.*.files.max'           => 'Setiap item maksimal 5 file pendukung.',
            'items.*.files.*.mimes'       => 'File harus berformat PDF, JPG, atau PNG.',
            'items.*.files.*.max'         => 'Ukuran file maksimal 2MB.',
        ]);

        DB::transaction(function () use ($request) {

            $id = $this->generateId();

            $pengadaan = PengadaanBarangJasa::create([
                'id_pengadaan' => $id,
                'nama_pengaju' => $request->nama_pengaju,
                'email_pengaju' => $request->email_pengaju,
                'id_divisi' => $request->id_divisi,
                'alasan' => $request->alasan,
                'tanggal_kebutuhan' => $request->tanggal_kebutuhan,
            ]);

            $total = 0;

            foreach ($request->items as $item) {

                $harga=(int)preg_replace('/\D/','',$item['harga_satuan'] ?? 0);
                $jumlah=(int)($item['jumlah'] ?? 1);

                $subtotal=$harga*$jumlah;
                $total+=$subtotal;

                $detail = PengadaanBarangJasaDetail::create([
                    'id_pengadaan'=>$id,
                    'jenis'=>$item['jenis'],

                    'nama_barang'=>$item['jenis']=='barang'
                        ? ($item['nama'] ?? null)
                        : null,

                    'kategori_jasa'=>$item['jenis']=='jasa'
                        ? ($item['kategori_jasa'] ?? null)
                        : null,

                    'merk'=>$item['merk'] ?? null,
                    'tipe_model'=>$item['tipe_model'] ?? null,
                    'spesifikasi'=>$item['spesifikasi'] ?? null,

                    'jumlah'=>$jumlah,
                    'harga_satuan'=>$harga,
                    'subtotal'=>$subtotal,
                    'catatan'=>$item['catatan'] ?? null
                ]);


                if(isset($item['files'])){
                    foreach($item['files'] as $file){

                        $path = $file->store(
                            "pengadaan_files/{$id}/detail_{$detail->id}",
                            'public'
                        );

                        PengadaanBarangJasaFile::create([
                            'id_detail' => $detail->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $file->getClientOriginalExtension(),
                        ]);
                    }
                }

            }

            $pengadaan->update(['total_biaya' => $total]);
        });

        return back()->with('success', 'Pengadaan berhasil diajukan');
    }

    public function edit($id)
    {
        $pengadaan = PengadaanBarangJasa::with(['details.files'])->findOrFail($id);
        $divisi = Divisi::all();

        return view('pengadaan_barang.edit', compact('pengadaan', 'divisi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'items.*.files' => 'nullable|array|max:5',
            'items.*.files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'id_divisi.exists' => 'Divisi yang dipilih tidak valid.',
        ]);
        DB::transaction(function () use ($request, $id) {

            $pengadaan = PengadaanBarangJasa::with('details')->findOrFail($id);

            $pengadaan->update([
                'nama_pengaju' => $request->nama_pengaju,
                'email_pengaju' => $request->email_pengaju,
                'id_divisi' => $request->id_divisi,
                'alasan' => $request->alasan,
                'tanggal_kebutuhan' => $request->tanggal_kebutuhan,
            ]);

            $oldFiles = PengadaanBarangJasaFile::whereHas(
                'detail',
                fn($q) => $q->where('id_pengadaan', $id)
            )->get();

            foreach ($oldFiles as $file) {

                if (Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }

                $file->delete();
            }

            Storage::disk('public')->deleteDirectory(
                "pengadaan_files/{$id}"
            );

            $pengadaan->details()->delete();

            $total = 0;

            foreach ($request->items as $item) {

                $harga = (int) preg_replace('/\D/', '', $item['harga_satuan'] ?? 0);
                $jumlah = (int) ($item['jumlah'] ?? 1);
                $subtotal = $harga * $jumlah;
                $total += $subtotal;

                $detail = PengadaanBarangJasaDetail::create([
                    'id_pengadaan' => $id,
                    'jenis' => $item['jenis'],

                    'nama_barang' => $item['jenis'] == 'barang'
                        ? ($item['nama'] ?? null)
                        : null,

                    'kategori_jasa' => $item['jenis'] == 'jasa'
                        ? ($item['kategori_jasa'] ?? null)
                        : null,

                    'merk' => $item['merk'] ?? null,
                    'tipe_model' => $item['tipe_model'] ?? null,
                    'spesifikasi' => $item['spesifikasi'] ?? null,

                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                    'subtotal' => $subtotal,
                    'catatan' => $item['catatan'] ?? null,
                ]);



                /*
                upload file per detail
                */
                if(isset($item['files'])){

                    foreach($item['files'] as $file){

                        $path = $file->store(
                            "pengadaan_files/{$id}/detail_{$detail->id}",
                            'public'
                        );

                        PengadaanBarangJasaFile::create([
                            'id_detail' => $detail->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $file->getClientOriginalExtension(),
                        ]);

                    }

                }

            }

            $pengadaan->update(['total_biaya' => $total]);
        });

        return back()->with('success', 'pengadaan berhasil diupdate');
    }

    public function destroy($id)
    {
        $pengadaan = PengadaanBarangJasa::findOrFail($id);

        if ($pengadaan->status !== 'Belum Diproses') {
            abort(403, 'Tidak bisa dihapus');
        }

        Storage::disk('public')->deleteDirectory(
            "pengadaan_files/{$id}"
        );

        $pengadaan->delete();

        return back()->with('success', 'pengadaan dihapus');
    }

    /* ================= FLOW ================= */

    public function approve($id)
    {
        PengadaanBarangJasa::findOrFail($id)->update([
            'decision_status' => 'disetujui',
            'decided_by' => auth()->user()->id_user,
            'decided_at' => now(),
        ]);

        return back()->with('success', 'Disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000'
        ]);

        PengadaanBarangJasa::findOrFail($id)->update([
            'decision_status' => 'ditolak',
            'status'          => 'Selesai',
            'decided_by'      => auth()->user()->id_user,
            'decided_at'      => now(),
            'catatan'         => $request->catatan,
        ]);

        return back()->with('success', 'Pengadaan berhasil ditolak');
    }

    public function process($id)
    {
        $pengadaan = PengadaanBarangJasa::findOrFail($id);

        if ($pengadaan->decision_status !== 'disetujui') {
            abort(403, 'Belum disetujui');
        }

        $pengadaan->update(['status' => 'Sedang Diproses']);

        return back()->with('success', 'Diproses');
    }

    public function complete(Request $request, $id)
    {
        
        $request->validate([
            'items' => 'required|array'
        ]);

        $pengadaan = PengadaanBarangJasa::with('details')
            ->findOrFail($id);

        if ($pengadaan->status !== 'Sedang Diproses') {
            abort(403, 'Belum diproses');
        }

        DB::transaction(function () use ($pengadaan, $request) {

            $asetData = [];
            $pengadaanUpdate = [
                'status' => 'Selesai'
            ];

            foreach ($request->items as $detailId => $rows) {

                $detail = $pengadaan->details
                    ->where('id', $detailId)
                    ->first();

                if (!$detail || $detail->jenis !== 'barang') {
                    continue;
                }

                $totalDistribusi = collect($rows)->sum('qty');


                if ($totalDistribusi != $detail->jumlah) {
                    throw new \Exception(
                        "Jumlah distribusi {$detail->nama_barang} harus {$detail->jumlah}"
                    );
                }

                foreach ($rows as $row) {

                    for ($i = 1; $i <= $row['qty']; $i++) {
                        Aset::create([
                            'id_aset' => $this->generateIdAset(),
                            'kode_aset' => $this->generateKodeAset(
                                $row['id_jenis_barang'],
                                now()->year
                            ),

                            'nama_aset' => $detail->nama_barang,
                            'merk' => $detail->merk,
                            'tipe_model' => $detail->tipe_model,
                            'spesifikasi' => $detail->spesifikasi,

                            'id_jenis_barang' => $row['id_jenis_barang'],
                            'id_gedung' => $row['id_gedung'],
                            'id_ruangan' => $row['id_ruangan'],

                            'tahun_perolehan' => now()->year,
                            'nilai' => $detail->harga_satuan,

                            'kelayakan' => 1,
                            'keterangan_kelayakan' => 'Layak',
                            'status' => 'tersedia',
                        ]);

                        
                    }
                }
            }

            $pengadaan->update([
                'status' => 'Selesai'
            ]);
        });

        return back()->with(
            'success',
            'Pengadaan selesai dan aset berhasil dibuat'
        );
    }

    public function exportPdfRiwayat(Request $request)
    {
        $pengadaan = $this->buildRiwayatQuery($request)->get();

        $totalSelesai = $pengadaan
            ->where('status', 'Selesai')
            ->where('decision_status', 'disetujui')
            ->count();

        $totalDitolak = $pengadaan
            ->where('decision_status', 'ditolak')
            ->count();

        $pdf = Pdf::loadView(
            'pengadaan_barang.pdf',
            [
                'pengadaan'    => $pengadaan,
                'start_date'   => $request->start_date,
                'end_date'     => $request->end_date,
                'totalSelesai' => $totalSelesai,
                'totalDitolak' => $totalDitolak,
            ]
        );

        return $pdf->download('PENGADAAN-BARANG-JASA.pdf');
    }

    public function exportExcelRiwayat(Request $request)
    {
        return Excel::download(
            new \App\Exports\PengadaanBarangJasaExport(
                $request->start_date,
                $request->end_date
            ),
            'PENGADAAN-BARANG-JASA.xlsx'
        );
    }
}