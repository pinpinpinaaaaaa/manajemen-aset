<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Divisi;
use App\Models\Ekspedisi;
use App\Models\EkspedisiDokumen;
use App\Models\EkspedisiBarang;
use App\Models\EkspedisiPengiriman;
use App\Models\User;


use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class EkspedisiController extends Controller
{
    private function generateEkspedisiId(): string
    {
        $date = now()->format('Ymd');

        $last = Ekspedisi::where('id_ekspedisi', 'like', "EXP-$date-%")
            ->lockForUpdate()
            ->orderByDesc('id_ekspedisi')
            ->first();

        $next = $last
            ? (int) substr($last->id_ekspedisi, -4) + 1
            : 1;

        return "EXP-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $data = Ekspedisi::with(['dokumen', 'barang', 'pengiriman'])
            ->where('decision_status', '!=', 'ditolak')
            ->where(function ($q) {
                $q->where('decision_status', '!=', 'disetujui')
                ->orWhereHas('pengiriman', function ($p) {
                    $p->where('status_pengiriman', '!=', 'selesai');
                });
            })
            ->latest()
            ->paginate($perPage);

        $users = User::all();

        return view('ekspedisi.index', compact('data', 'users', 'perPage'));
    }

    public function riwayat(Request $request)
    {
        $query = Ekspedisi::with([
            'dokumen',
            'barang',
            'pengiriman',
            'divisi_pengirim'
        ])
        ->where(function ($q) {
            $q->where('decision_status', 'ditolak')
            ->orWhereHas('pengiriman', function ($q2) {
                $q2->where('status_pengiriman', 'selesai');
            });
        });

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $data = $query->latest()->get();

        $totalSelesai = $data->filter(function ($item) {
            return optional($item->pengiriman)->status_pengiriman === 'selesai';
        })->count();

        $totalDitolak = $data->where('decision_status', 'ditolak')->count();

        return view('ekspedisi.riwayat', compact(
            'data',
            'totalSelesai',
            'totalDitolak'
        ));
    }
    public function show($id)
    {
        $data = Ekspedisi::with(['dokumen','barang','pengiriman'])
            ->findOrFail($id);

        return view('ekspedisi.show', compact('data'));
    }

    public function create()
    {
        $divisi = Divisi::all();

        return view('ekspedisi.form', compact('divisi'));
    }
    public function store(Request $request)
    {
        $request->validate([
            // pengaju
            'nama_pengaju' => 'required|string|max:255',
            'email_pengaju' => 'required|email',
            'id_divisi_pengaju' => 'required|exists:divisi,id_divisi',

            // pengirim
            'nama_pengirim' => 'required|string|max:255',
            'email_pengirim' => 'required|email',
            'no_hp_pengirim' => 'nullable|string|max:13',
            'id_divisi_pengirim' => 'required|exists:divisi,id_divisi',

            // penerima
            'instansi_penerima' => 'required|string|max:255',
            'nama_penerima' => 'required|string|max:255',
            'email_penerima' => 'nullable|email',
            'no_hp_penerima' => 'nullable|string|max:13',
            'alamat_penerima' => 'required',

            // kegiatan
            'judul_kegiatan' => 'required|string|max:255',

            // item
            'items' => 'required|array|min:1',
            'items.*.jenis' => 'required|in:dokumen,barang',
            'items.*.nama' => 'required|string|max:255',
            'items.*.jumlah' => 'nullable|integer|min:1',
            'items.*.keterangan' => 'nullable|string',

            'items.*.files' => 'nullable|array|max:5',
            'items.*.files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'nama_pengaju.required'       => 'Nama pengaju wajib diisi.',
            'email_pengaju.required'      => 'Email pengaju wajib diisi.',
            'email_pengaju.email'         => 'Format email pengaju tidak valid.',
            'id_divisi_pengaju.required'  => 'Divisi pengaju wajib dipilih.',
            'id_divisi_pengaju.exists'    => 'Divisi pengaju yang dipilih tidak valid.',
            'nama_pengirim.required'      => 'Nama pengirim wajib diisi.',
            'email_pengirim.required'     => 'Email pengirim wajib diisi.',
            'email_pengirim.email'        => 'Format email pengirim tidak valid.',
            'id_divisi_pengirim.required' => 'Divisi pengirim wajib dipilih.',
            'id_divisi_pengirim.exists'   => 'Divisi pengirim yang dipilih tidak valid.',
            'instansi_penerima.required'  => 'Instansi tujuan wajib diisi.',
            'nama_penerima.required'      => 'Nama penerima wajib diisi.',
            'email_penerima.email'        => 'Format email penerima tidak valid.',
            'alamat_penerima.required'    => 'Alamat tujuan wajib diisi.',
            'judul_kegiatan.required'     => 'Judul kegiatan wajib diisi.',
            'items.required'              => 'Minimal 1 item kiriman harus diisi.',
            'items.*.jenis.required'      => 'Jenis item wajib dipilih (Dokumen atau Barang).',
            'items.*.jenis.in'            => 'Jenis item harus Dokumen atau Barang.',
            'items.*.nama.required'       => 'Nama item wajib diisi.',
            'items.*.files.*.mimes'       => 'File item hanya boleh berformat PDF, JPG, atau PNG.',
            'items.*.files.*.max'         => 'Ukuran file item maksimal 5 MB.',
        ]);

        DB::transaction(function () use ($request) {

            $idEkspedisi = $this->generateEkspedisiId();
           $ekspedisi = Ekspedisi::create([
                'id_ekspedisi' => $idEkspedisi,

                'nama_pengaju' => $request->nama_pengaju,
                'email_pengaju' => $request->email_pengaju,
                'id_divisi_pengaju' => $request->id_divisi_pengaju,

                'nama_pengirim' => $request->nama_pengirim,
                'email_pengirim' => $request->email_pengirim,
                'no_hp_pengirim' => $request->no_hp_pengirim,
                'id_divisi_pengirim' => $request->id_divisi_pengirim,

                'instansi_penerima' => $request->instansi_penerima,
                'nama_penerima' => $request->nama_penerima,
                'email_penerima' => $request->email_penerima,
                'no_hp_penerima' => $request->no_hp_penerima,
                'alamat_penerima' => $request->alamat_penerima,

                'judul_kegiatan' => $request->judul_kegiatan,
                'keterangan' => $request->keterangan,

                'decision_status' => 'menunggu_persetujuan',
            ]);
           foreach ($request->items as $index => $item) {

                $uploadedFiles = [];

                // upload file jika ada
                if ($request->hasFile("items.$index.files")) {

                    foreach ($request->file("items.$index.files") as $file) {

                        $path = $file->store(
                            'ekspedisi',
                            'public'
                        );

                        $uploadedFiles[] = $path;
                    }
                }

                if ($item['jenis'] == 'dokumen') {

                    EkspedisiDokumen::create([
                        'id_ekspedisi' => $ekspedisi->id_ekspedisi,

                        'nama_dokumen' => $item['nama'],
                        'jenis_dokumen' => $item['keterangan'] ?? null,

                        'jumlah' => $item['jumlah'] ?? 1,

                        'file_dokumen' => !empty($uploadedFiles)
                            ? json_encode($uploadedFiles)
                            : null,
                    ]);
                }
               if ($item['jenis'] == 'barang') {

                    EkspedisiBarang::create([
                        'id_ekspedisi' => $ekspedisi->id_ekspedisi,

                        'nama_barang' => $item['nama'],
                        'jumlah' => $item['jumlah'] ?? 1,

                        'keterangan' => $item['keterangan'] ?? null,

                        'file_barang' => !empty($uploadedFiles)
                            ? json_encode($uploadedFiles)
                            : null,
                    ]);
                }
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Permintaan ekspedisi berhasil dikirim');
    }
    public function edit($id)
    {
        $data = Ekspedisi::with(['dokumen', 'barang'])
            ->findOrFail($id);

        $divisi = Divisi::all();

        return view('ekspedisi.edit', compact('data', 'divisi'));
    }

   public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pengaju' => 'required',
            'id_divisi_pengaju' => 'required|exists:divisi,id_divisi',
            'nama_penerima' => 'required',
            'alamat_penerima' => 'required',
        ], [
            'id_divisi_pengaju.exists' => 'Divisi pengaju yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($request, $id) {

            $ekspedisi = Ekspedisi::findOrFail($id);

            $ekspedisi->update($request->only([
                'nama_pengaju',
                'email_pengaju',
                'id_divisi_pengaju',
                'nama_pengirim',
                'email_pengirim',
                'no_hp_pengirim',
                'id_divisi_pengirim',
                'nama_penerima',
                'email_penerima',
                'no_hp_penerima',
                'instansi_penerima',
                'alamat_penerima',
                'judul_kegiatan',
                'keterangan'
            ]));

            EkspedisiDokumen::where('id_ekspedisi',$id)->delete();
            EkspedisiBarang::where('id_ekspedisi',$id)->delete();

            if ($request->dokumen) {
                foreach ($request->dokumen as $doc) {
                    EkspedisiDokumen::create([
                        'id_ekspedisi' => $id,
                        'nama_dokumen' => $doc['nama'],
                        'jenis_dokumen' => $doc['jenis'] ?? null,
                    ]);
                }
            }
            if ($request->barang) {
                foreach ($request->barang as $item) {
                    EkspedisiBarang::create([
                        'id_ekspedisi' => $id,
                        'nama_barang' => $item['nama'],
                        'jumlah' => $item['jumlah'],
                        'berat' => $item['berat'] ?? null,
                        'satuan' => $item['satuan'] ?? null,
                    ]);
                }
            }
        });

        return back()->with('success','Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $data = Ekspedisi::findOrFail($id);

        if ($data->decision_status != 'draft') {
            abort(403, 'Tidak bisa dihapus');
        }

        $data->delete();

        return redirect()->route('ekspedisi.index')
            ->with('success','Data dihapus');
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $data = Ekspedisi::findOrFail($id);

            if ($data->decision_status != 'menunggu_persetujuan') {
                abort(403);
            }

            $data->update([
                'decision_status' => 'disetujui',
                'approved_by' => auth()->user()->name,
                'approved_at' => now(),
            ]);

            EkspedisiPengiriman::create([
                'id_ekspedisi' => $id,
                'status_pengiriman' => 'belum_dikirim',
            ]);
        });

        return back()->with('success', 'Ekspedisi disetujui');
    }
    public function reject(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:1000',
        ]);
        $data = Ekspedisi::findOrFail($id);

        $data->update([
            'decision_status' => 'ditolak',
            'approved_by' => auth()->user()->name ?? 'system',
            'approved_at' => now(),
            'keterangan'      => $request->keterangan,

        ]);

        return back()->with('success','Ditolak');
    }
    public function process(Request $request, $id)
    {
        $request->validate([
            'jenis_kurir' => 'required|in:internal,eksternal',

            'id_user_kurir' =>
                'required_if:jenis_kurir,internal',

            'nama_jasa_ekspedisi' =>
                'required_if:jenis_kurir,eksternal',

            'no_resi' =>
                'required_if:jenis_kurir,eksternal',
        ]);

        $pengiriman = EkspedisiPengiriman::where(
            'id_ekspedisi',
            $id
        )->firstOrFail();

        $pengiriman->update([
            'jenis_kurir' => $request->jenis_kurir,
            'id_kurir_internal' => $request->id_user_kurir,

            'nama_jasa_ekspedisi' =>
                $request->nama_jasa_ekspedisi,

            'no_resi' => $request->no_resi,

            'status_pengiriman' => 'dikirim',

            'waktu_dikirim' => now(),
        ]);

        return back()->with('success', 'Paket dikirim');
    }

    public function received(Request $request, $id)
    {
        $request->validate([
            'foto_bukti' => 'required|image|max:5120',

            'jenis_ttd' => 'required|in:scan,digital',

            'file_ttd_scan' =>
                'required_if:jenis_ttd,scan|file|mimes:jpg,jpeg,png,pdf',

            'ttd_digital' =>
                'required_if:jenis_ttd,digital',
        ]);

        $pengiriman = EkspedisiPengiriman::where(
            'id_ekspedisi',
            $id
        )->firstOrFail();

        // upload foto
        $fotoFile = $request->file('foto_bukti');

        $fotoName =
            'FOTO-BUKTI-' .
            $id . '-' .
            time() . '.' .
            $fotoFile->getClientOriginalExtension();

        $foto = $fotoFile->storeAs(
            'ekspedisi/foto-bukti',
            $fotoName,
            'public'
        );

        $update = [
            'status_pengiriman' => 'diterima',

            'waktu_diterima' => now(),

            'foto_bukti' => $foto,
            'jenis_ttd' => $request->jenis_ttd,

            'nama_penerima_ttd' =>
                $request->nama_penerima_ttd,

            'jabatan_penerima' =>
                $request->jabatan_penerima,
        ];

        if (
            !$request->nama_penerima_ttd &&
            !$request->jabatan_penerima
        ) {
            $update['catatan_penerimaan'] =
                'Paket diterima tanpa identitas penerima.';
        }

        if ($request->jenis_ttd == 'scan') {

            $scanFile = $request->file('file_ttd_scan');

            $scanName =
                'TTD-SCAN-' .
                $id . '-' .
                time() . '.' .
                $scanFile->getClientOriginalExtension();

            $scan = $scanFile->storeAs(
                'ekspedisi/ttd-scan',
                $scanName,
                'public'
            );

            $update['ttd_file'] = $scan;

            // kosongkan digital
            $update['ttd_digital'] = null;
        }

        if ($request->jenis_ttd == 'digital') {

            $update['ttd_digital'] =
                $request->ttd_digital;

            $update['ttd_file'] = null;
        }

        $pengiriman->update($update);

        return back()->with('success', 'Paket diterima');
    }

    public function complete(Request $request, $id)
    {
        $pengiriman = EkspedisiPengiriman::where('id_ekspedisi',$id)->firstOrFail();

        $pengiriman->update([
            'waktu_diterima' => now(),
            'waktu_selesai' => now(),
            'status_pengiriman' => 'selesai',
            'nama_penerima_ttd' => $request->nama_penerima_ttd,
        ]);

        return back()->with('success','Selesai');
    }

    public function exportPdfRiwayat(Request $request)
    {
        $laporan = Ekspedisi::with([
            'pengiriman',
            'dokumen',
            'barang',
            'divisi_pengaju',
            'divisi_pengirim'
        ])
        ->where(function ($q) {
            $q->where('decision_status', 'ditolak')
            ->orWhereHas('pengiriman', function ($x) {
                $x->where('status_pengiriman', 'selesai');
            });
        })
        ->latest()
        ->get();

        $totalSelesai = $laporan->filter(function ($item) {
            return optional($item->pengiriman)->status_pengiriman == 'selesai';
        })->count();

        $totalDitolak = $laporan->where('decision_status', 'ditolak')->count();

        $pdf = Pdf::loadView(
            'ekspedisi.pdf',
            compact(
                'laporan',
                'totalSelesai',
                'totalDitolak'
            )
        )->setPaper('A4', 'landscape');

        return $pdf->download('LAPORAN_RIWAYAT_EKSPEDISI.pdf');
    }
    
    public function exportExcelRiwayat()
    {
        return Excel::download(new \App\Exports\EkspedisiExport, 'EKSPEDISI.xlsx');
    }

    public function printLabel($id)
    {
        $data = Ekspedisi::with([
            'dokumen',
            'barang',
            'pengiriman',
            'divisi_pengirim'
        ])->findOrFail($id);

        $pdf = Pdf::loadView(
            'ekspedisi.label-amplop',
            compact('data')
        )->setPaper('A5');

        return $pdf->download('LABEL-'.$id.'.pdf');
    }

    public function printTandaTerima($id)
    {
        $data = Ekspedisi::with([
            'dokumen',
            'barang',
            'pengiriman',
            'divisi_pengirim'
        ])->findOrFail($id);

        $pengiriman = $data->pengiriman;

        if (
            $pengiriman &&
            $pengiriman->jenis_ttd === 'scan' &&
            $pengiriman->ttd_file
        ) {

            return response()->download(
                storage_path('app/public/' . $pengiriman->ttd_file)
            );
        }

        $pdf = Pdf::loadView(
            'ekspedisi.tanda-terima',
            compact('data')
        )->setPaper('A4');

        return $pdf->download('TANDA-TERIMA-' . $id . '.pdf');
    }
}