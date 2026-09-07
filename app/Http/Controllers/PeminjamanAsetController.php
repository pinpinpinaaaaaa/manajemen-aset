<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PeminjamanAset;
use App\Models\PeminjamanAsetDetail;
use App\Models\Aset;
use App\Models\Divisi;
use App\Services\AsetLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeminjamanAsetExport;

class PeminjamanAsetController extends Controller
{
    private function generateId(): string
    {
        $date = now()->format('Ymd');

        $last = PeminjamanAset::where(
            'id_peminjaman',
            'like',
            "PJM-$date-%"
        )->lockForUpdate()->orderByDesc('id_peminjaman')->first();

        $next = $last
            ? (int) substr($last->id_peminjaman, -4) + 1
            : 1;

        return "PJM-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $today = Carbon::today();

        $data = PeminjamanAset::with(['divisi','details'])
            ->where('decision_status','!=','ditolak')
            ->whereHas('details', function($q){
                $q->where('status_pengembalian', '!=', 'dikembalikan');
            })
            ->paginate($perPage)
            ->through(function($p) use ($today){

                $p->status_runtime='Belum Diproses';

                if($p->decision_status=='disetujui'){

                    $sudahMulai=$p->details->contains(function($d) use($today){
                        return $d->tanggal_pinjam <= $today;
                    });

                    if($sudahMulai){
                        $p->status_runtime='Sedang Dipinjam';
                    }
                }

                return $p;
            });

        return view('peminjaman_aset.index', compact('data', 'perPage'));
    }

    public function show($id)
    {
        $data = PeminjamanAset::with([
            'divisi',
            'details.aset'
        ])->findOrFail($id);

        return view('peminjaman_aset.show', compact('data'));
    }

    private function buildRiwayatQuery(Request $request)
    {
        $query = PeminjamanAset::with(['divisi','details']);

            if($request->start_date){
            $query->whereDate('created_at','>=',$request->start_date);
            }

            if($request->end_date){
            $query->whereDate('created_at','<=',$request->end_date);
            }

        return $query->get()->filter(function($p){

            if($p->decision_status=='ditolak'){
                return true;
            }

            return $p->details->every(function($d){
                return $d->status_pengembalian=='dikembalikan';
            });

        });
    }

    public function riwayat(Request $request)
    {
        $data = $this->buildRiwayatQuery($request);

        $totalSelesai = $data
            ->where('decision_status','disetujui')
            ->count();

        $totalDitolak = $data
            ->where('decision_status','ditolak')
            ->count();

        return view('peminjaman_aset.riwayat', compact(
            'data',
            'totalSelesai',
            'totalDitolak'
        ));
    }

    public function create()
    {
        $divisi = Divisi::all();

        $asetGrouped = Aset::whereHas('jenisBarang', function ($q) {
                $q->where('jenis', 'sarana');
            })
            ->where('status','tersedia')
            ->whereNotIn('kelayakan', [4, 5])
            ->get()
            ->groupBy(function($item){
                return $item->nama_aset . '|' . $item->id_jenis_barang;
            })
            ->map(function($group){

                $first = $group->first();

                return [
                    'kategori' => $first->jenisBarang->kategori ?? '-',
                    'nama_aset' => $first->nama_aset,
                    'id_jenis_barang' => $first->id_jenis_barang,
                    'jenis_barang' => $first->jenisBarang->nama_barang ?? '-',
                    'total_unit' => $group->count(),
                    'aset_ids' => $group->pluck('id_aset')->toArray()
                ];
            })
            ->groupBy('kategori');

        return view('peminjaman_aset.form', compact('divisi','asetGrouped'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'nama_pengaju' => 'required',
            'email_pengaju' => 'required|email',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'alasan' => 'required',
            'items' => 'required|array|min:1',
            'items.*.id_jenis_barang' => 'required',
            'items.*.nama_aset' => 'required',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.tanggal_pinjam' => 'required|date',
            'items.*.tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
        ], [
            'nama_pengaju.required'                      => 'Nama peminjam wajib diisi.',
            'email_pengaju.required'                     => 'Email peminjam wajib diisi.',
            'email_pengaju.email'                        => 'Format email tidak valid.',
            'id_divisi.required'                         => 'Divisi wajib dipilih.',
            'id_divisi.exists'                           => 'Divisi yang dipilih tidak valid.',
            'alasan.required'                            => 'Keperluan/alasan peminjaman wajib diisi.',
            'items.required'                             => 'Minimal 1 aset harus dipilih.',
            'items.min'                                  => 'Minimal 1 aset harus dipilih.',
            'items.*.id_jenis_barang.required'           => 'Aset wajib dipilih pada setiap baris.',
            'items.*.nama_aset.required'                 => 'Nama aset wajib ada (pilih aset terlebih dahulu).',
            'items.*.jumlah.required'                    => 'Jumlah unit wajib diisi.',
            'items.*.jumlah.integer'                     => 'Jumlah unit harus berupa angka bulat.',
            'items.*.jumlah.min'                         => 'Jumlah unit minimal 1.',
            'items.*.tanggal_pinjam.required'            => 'Tanggal pinjam wajib diisi.',
            'items.*.tanggal_pinjam.date'                => 'Format tanggal pinjam tidak valid.',
            'items.*.tanggal_jatuh_tempo.required'       => 'Tanggal kembali wajib diisi.',
            'items.*.tanggal_jatuh_tempo.date'           => 'Format tanggal kembali tidak valid.',
            'items.*.tanggal_jatuh_tempo.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
        ]);

        DB::transaction(function() use ($request){

            $id = $this->generateId();

            PeminjamanAset::create([
                'id_peminjaman' => $id,
                'nama_pengaju' => $request->nama_pengaju,
                'email_pengaju' => $request->email_pengaju,
                'id_divisi' => $request->id_divisi,
                'alasan' => $request->alasan,
                'catatan' => $request->catatan,
                'decision_status' => 'menunggu_persetujuan',
            ]);

            foreach ($request->items as $item) {

                $jumlahDiminta = $item['jumlah'];
                $tanggalMulai = $item['tanggal_pinjam'];
                $tanggalSelesai = $item['tanggal_jatuh_tempo'];

                $asetTersedia = Aset::where('nama_aset', $item['nama_aset'])->where('id_jenis_barang', $item['id_jenis_barang'])->where('status', 'tersedia')->whereNotIn('kelayakan', [4, 5])
                    ->whereDoesntHave('peminjamanDetails', function($q) use ($tanggalMulai, $tanggalSelesai) {

                        $q->whereHas('peminjaman', function($p) {
                            $p->whereIn('decision_status', [
                                'menunggu_persetujuan',
                                'disetujui'
                            ]);
                        })
                        ->where(function($dateQuery) use ($tanggalMulai, $tanggalSelesai) {

                            $dateQuery->whereBetween('tanggal_pinjam', [$tanggalMulai, $tanggalSelesai])
                                ->orWhereBetween('tanggal_jatuh_tempo', [$tanggalMulai, $tanggalSelesai])
                                ->orWhere(function($inner) use ($tanggalMulai, $tanggalSelesai) {
                                    $inner->where('tanggal_pinjam', '<=', $tanggalMulai)
                                        ->where('tanggal_jatuh_tempo', '>=', $tanggalSelesai);
                                });
                        });
                    })
                    ->limit($jumlahDiminta)
                    ->lockForUpdate()
                    ->get();

                if ($asetTersedia->count() < $jumlahDiminta) {
                    throw new \Exception("Unit {$item['nama_aset']} tidak tersedia di tanggal tersebut.");
                }

                foreach ($asetTersedia as $aset) {

                    PeminjamanAsetDetail::create([
                        'id_peminjaman' => $id,
                        'id_aset' => $aset->id_aset,
                        'jumlah' => 1,
                        'tanggal_pinjam' => $tanggalMulai,
                        'tanggal_jatuh_tempo' => $tanggalSelesai,
                        'status_pengembalian' => 'menunggu',
                    ]);
                }
            }

        });

        return redirect()->route('form_peminjaman_aset.create')
            ->with('success','Peminjaman berhasil diajukan');
    }

    public function edit($id)
    {
        $peminjaman = PeminjamanAset::with([
            'details.aset',
            'divisi'
        ])->findOrFail($id);

        $divisi = Divisi::all();

        $asetGrouped = Aset::whereHas('jenisBarang', function ($q) {
                $q->where('jenis', 'sarana');
            })
            ->where('status', 'tersedia')
            ->get()
            ->groupBy(function ($item) {
                return $item->nama_aset . '|' . $item->id_jenis_barang;
            })
            ->map(function ($group) {

                $first = $group->first();

                return [
                    'kategori' => $first->jenisBarang->kategori ?? '-',
                    'nama_aset' => $first->nama_aset,
                    'id_jenis_barang' => $first->id_jenis_barang,
                    'jenis_barang' => $first->jenisBarang->nama_barang ?? '-',
                    'total_unit' => $group->count(),
                ];
            })
            ->groupBy('kategori');

        return view(
            'peminjaman_aset.edit',
            compact(
                'peminjaman',
                'divisi',
                'asetGrouped'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pengaju' => 'required',
            'email_pengaju' => 'required|email',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'alasan' => 'required',
            'items.*.id_jenis_barang' => 'required',
            'items' => 'required|array|min:1',
            'items.*.nama_aset' => 'required',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.tanggal_pinjam' => 'required|date',
            'items.*.tanggal_jatuh_tempo' => 'required|date|after_or_equal:items.*.tanggal_pinjam',
        ], [
            'id_divisi.exists' => 'Divisi yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($request, $id) {

            $peminjaman = PeminjamanAset::findOrFail($id);

            $peminjaman->update([
                'nama_pengaju' => $request->nama_pengaju,
                'email_pengaju' => $request->email_pengaju,
                'id_divisi' => $request->id_divisi,
                'alasan' => $request->alasan,
                'catatan' => $request->catatan,
            ]);

            PeminjamanAsetDetail::where(
                'id_peminjaman',
                $id
            )->delete();

            foreach ($request->items as $item) {

                $asetTersedia = Aset::where(
                        'nama_aset',
                        $item['nama_aset']
                    )
                    ->where(
                        'id_jenis_barang',
                        $item['id_jenis_barang']
                    )
                    ->where('status', 'tersedia')
                    ->whereNotIn('kelayakan', [4, 5])
                    ->limit($item['jumlah'])
                    ->get();

                if ($asetTersedia->count() < $item['jumlah']) {
                    throw new \Exception(
                        "Unit {$item['nama_aset']} tidak mencukupi."
                    );
                }

                foreach ($asetTersedia as $aset) {

                    PeminjamanAsetDetail::create([
                        'id_peminjaman' => $id,
                        'id_aset' => $aset->id_aset,
                        'jumlah' => 1,
                        'tanggal_pinjam' => $item['tanggal_pinjam'],
                        'tanggal_jatuh_tempo' => $item['tanggal_jatuh_tempo'],
                        'status_pengembalian' => 'menunggu',
                    ]);
                }
            }
        });

        return redirect()
            ->route('peminjaman_aset.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function approve($id)
    {
        $data = PeminjamanAset::findOrFail($id);

        $data->update([
            'decision_status' => 'disetujui',
            'decided_by' => auth()->user()->id_user ?? null,
            'decided_at' => now(),
        ]);

        return back()->with('success','Disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string'
        ]);

        $data = PeminjamanAset::findOrFail($id);

        $data->update([
            'decision_status' => 'ditolak',
            'catatan' => $request->catatan,
            'decided_by' => auth()->user()->id_user ?? null,
            'decided_at' => now(),
        ]);

        return back()->with('success', 'Peminjaman ditolak');
    }

    public function kembalikan(Request $request, $id_detail)
    {
        
        $detail = PeminjamanAsetDetail::with('aset')
            ->findOrFail($id_detail);
        
        if($detail->status_pengembalian=='dikembalikan'){
            abort(403,'Sudah dikembalikan');
        }

        $request->validate([
            'kondisi_kembali' => 'required'
        ]);

        $detail->update([
            'tanggal_dikembalikan' => now(),
            'status_pengembalian' => 'dikembalikan',
            'kondisi_kembali' => $request->kondisi_kembali,
            'catatan_pengembalian' => $request->catatan_pengembalian,
        ]);

        $detail->aset->update([
            'status' => 'tersedia'
        ]);

        AsetLogService::log(
            $detail->id_aset,
            'dikembalikan',
            $detail->id_peminjaman,
            'Aset dikembalikan dengan kondisi: ' . $request->kondisi_kembali
        );

        $parent = PeminjamanAset::with('details')
            ->findOrFail($detail->id_peminjaman);

        if (
            $parent->details()
                ->where('status_pengembalian', '!=', 'dikembalikan')
                ->count() == 0
        ) {
            $parent->update([
                'status' => 'Selesai'
            ]);
        }

        return back()->with('success','Aset dikembalikan');
    }

    public function cekKetersediaan(Request $request)
    {
        $namaAset = $request->nama_aset;
        $tanggalMulai = $request->tanggal_pinjam;
        $tanggalSelesai = $request->tanggal_jatuh_tempo;
        $idJenisBarang = $request->id_jenis_barang;

        if (!$namaAset || !$tanggalMulai || !$tanggalSelesai) {
            return response()->json(['sisa' => null]);
        }

        $total = Aset::where('nama_aset', $namaAset)->where('id_jenis_barang', $idJenisBarang)
            ->where('status','tersedia')
            ->whereNotIn('kelayakan', [4, 5])
            ->count();

        $dipakai = PeminjamanAsetDetail::whereHas('peminjaman', function($q){
                $q->whereIn('decision_status', [
                    'menunggu_persetujuan',
                    'disetujui'
                ]);
            })
            ->where('status_pengembalian','!=','dikembalikan')
            ->whereHas('aset', function($q) use ($namaAset,$idJenisBarang){
                $q->where('nama_aset',$namaAset)
                ->where('id_jenis_barang',$idJenisBarang);
            })
            ->where(function($dateQuery) use ($tanggalMulai, $tanggalSelesai){

                $dateQuery->whereBetween('tanggal_pinjam', [$tanggalMulai, $tanggalSelesai])
                    ->orWhereBetween('tanggal_jatuh_tempo', [$tanggalMulai, $tanggalSelesai])
                    ->orWhere(function($inner) use ($tanggalMulai, $tanggalSelesai){
                        $inner->where('tanggal_pinjam','<=',$tanggalMulai)
                            ->where('tanggal_jatuh_tempo','>=',$tanggalSelesai);
                    });
            })
            ->count();

        $sisa = $total - $dipakai;

        return response()->json([
            'total' => $total,
            'dipakai' => $dipakai,
            'sisa' => $sisa
        ]);
    }

    public function exportPdfRiwayat(Request $request)
    {
        $data = $this->buildRiwayatQuery($request);

        $totalSelesai = $data
            ->where('decision_status','disetujui')
            ->count();

        $totalDitolak = $data
            ->where('decision_status','ditolak')
            ->count();

        $pdf = Pdf::loadView(
            'peminjaman_aset.pdf',
            [
                'data'          => $data,
                'totalSelesai'  => $totalSelesai,
                'totalDitolak'  => $totalDitolak,
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date,
            ]
        )->setPaper('A4','portrait');

        return $pdf->download('riwayat-peminjaman-aset.pdf');
    }

    public function exportExcelRiwayat(Request $request)
    {
        return Excel::download(
            new PeminjamanAsetExport(
                $request->start_date,
                $request->end_date
            ),
            'riwayat-peminjaman-aset.xlsx'
        );
    }

    public function serahkanItem($id_detail)
    {
        $detail = PeminjamanAsetDetail::with('aset','peminjaman')
            ->findOrFail($id_detail);

        if ($detail->peminjaman->decision_status !== 'disetujui') {
            abort(403,'Belum disetujui');
        }

        if ($detail->status_pengembalian !== 'menunggu') {
            abort(403,'Sudah diserahkan');
        }

        $detail->update([
            'status_pengembalian' => 'dipinjam'
        ]);

        $detail->aset->update([
            'status' => 'terpakai'
        ]);

        AsetLogService::log(
            $detail->id_aset,
            'dipinjam',
            $detail->id_peminjaman,
            'Aset dipinjam oleh ' . $detail->peminjaman->nama_pengaju
        );

        $parent = $detail->peminjaman;

        return back()->with('success','Aset diserahkan');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $peminjaman = PeminjamanAset::with('details')
                ->findOrFail($id);

            $sedangDipinjam = $peminjaman->details
                ->contains(function ($detail) {
                    return $detail->status_pengembalian === 'dipinjam';
                });

            if ($sedangDipinjam) {
                throw new \Exception(
                    'Peminjaman yang sedang berjalan tidak dapat dihapus.'
                );
            }

            $peminjaman->details()->delete();
            $peminjaman->delete();
        });

        return back()->with(
            'success',
            'Data peminjaman berhasil dihapus'
        );
    }
}