<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemindahanAset;
use App\Models\PemindahanAsetDetail;
use App\Models\Aset;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\Vendor;
use App\Services\AsetLogService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemindahanAsetExport;

class PemindahanAsetController extends Controller
{

/* =========================================================
    HELPERS
=========================================================*/

private function summaryData($data)
{
    $belum = PemindahanAsetDetail::where('status','Belum dipindahkan')
        ->whereHas('pemindahan', function($q){
            $q->where('decision_status','disetujui');
        })
        ->count();
    return [
        'pemindahan'=>$data,
        'total'=>$data->count(),
        'disetujui'=>$data->where('decision_status','disetujui')->count(),
        'ditolak'=>$data->where('decision_status','ditolak')->count(),
        'belum'=>$belum,
    ];
}

private function buildFilteredQuery(Request $request)
{
    $query = PemindahanAset::with([
        'details.aset',
        'details.gedungAsal',
        'details.ruanganAsal',
        'details.gedungTujuan',
        'details.ruanganTujuan'
    ])->where(function($q){

        $q->where('decision_status','ditolak')

        ->orWhereHas('details', function($x){
                $x->where('status','Sudah dipindahkan');
        });

    });

    if ($request->start_date) {
        $query->whereDate('created_at','>=',$request->start_date);
    }

    if ($request->end_date) {
        $query->whereDate('created_at','<=',$request->end_date);
    }

    return $query;
}

public function getAsetPindah($id_ruangan)
{
    $aset = Aset::with('jenisBarang')
        ->where('id_ruangan', $id_ruangan)
        ->whereHas('jenisBarang', function($q){
            $q->where('bisa_dipindah', true);
        })
        ->get();

    return response()->json($aset);
}


/* =========================================================
    GENERATE ID
=========================================================*/

private function generateId(): string
{
    $date = now()->format('Ymd');

    $last = PemindahanAset::where(
        'id_pemindahan',
        'like',
        "PMD-$date-%"
    )->lockForUpdate()->orderByDesc('id_pemindahan')->first();

    $next = $last
        ? (int) substr($last->id_pemindahan,-4) + 1
        : 1;

    return "PMD-$date-".str_pad($next,4,'0',STR_PAD_LEFT);
}


/* =========================================================
    CREATE
=========================================================*/

public function create()
{
    return view('pemindahan_aset.create',[
        'gedung'=>Gedung::all(),
        'aset'=>null,
        'vendors'=>Vendor::all()
    ]);
}

public function createWithAset($id)
{
    $aset = Aset::with([
        'gedung',
        'ruangan',
        'jenisBarang'
    ])->findOrFail($id);

    if (!$aset->jenisBarang || !$aset->jenisBarang->bisa_dipindah) {
        return back()->with(
            'error',
            'Aset ini tidak dapat dipindahkan karena jenis barang tidak dapat dipindahkan.'
        );
    }

    return view('pemindahan_aset.create', [
        'aset'    => $aset,
        'gedung'  => Gedung::all(),
        'vendors' => Vendor::all()
    ]);
}


public function store(Request $request)
{
    $request->validate([
        'id_aset' => 'required|array|min:1',
        'id_aset.*' => 'exists:aset,id_aset',

        'to_gedung' => 'required|array',
        'to_gedung.*' => 'required|exists:gedung,id_gedung',

        'to_ruangan' => 'required|array',
        'to_ruangan.*' => 'required|exists:ruangan,id_ruangan',

        'alasan' => 'required|string|max:1000',

        'biaya' => 'nullable|array',
        'biaya.*' => 'nullable|numeric|min:0',

        'pelaksana_type' => 'nullable|array',
        'pelaksana_type.*' => 'nullable|in:internal,vendor,lainnya',

        'id_vendor' => 'nullable|array',
        'id_vendor.*' => 'nullable|exists:vendors,id_vendor',
    ],[
        'alasan.required' => 'Alasan pemindahan wajib diisi.'
    ]);
    

    $id = DB::transaction(function () use ($request) {
        $id = $this->generateId();

        PemindahanAset::create([
            'id_pemindahan'=>$id,
            'alasan'=>$request->alasan,
            'requested_by'=>auth()->user()->id_user,
        ]);

        foreach ($request->id_aset as $index => $idAset) {

            $aset = Aset::with('jenisBarang')->findOrFail($idAset);

            if (!$aset->jenisBarang?->bisa_dipindah) {
                continue;
            }

            if (
                PemindahanAsetDetail::where('id_aset', $aset->id_aset)
                    ->where('status', 'Belum dipindahkan')
                    ->whereHas('pemindahan', function ($q) {
                        $q->where('decision_status', '!=', 'ditolak');
                    })
                    ->exists()
            ) {
                continue;
            }

            PemindahanAsetDetail::create([
                'id_pemindahan' => $id,
                'id_aset' => $aset->id_aset,

                'from_gedung' => $aset->id_gedung,
                'from_ruangan' => $aset->id_ruangan,

                'to_gedung' => $request->to_gedung[$index] ?? null,
                'to_ruangan' => $request->to_ruangan[$index] ?? null,

                'biaya' => $request->biaya[$index] ?? 0,

                'pelaksana_type' => $request->pelaksana_type[$index] ?? 'internal',

                'id_vendor' => $request->id_vendor[$index] ?? null,
            ]);

            AsetLogService::log(
                $aset->id_aset,
                'permintaan_pemindahan',
                $id,
                'Permintaan pemindahan diajukan'
            );
        }

        return $id;
    });

    return redirect()->route('pemindahan_aset.index')
        ->with('success',"Pemindahan #$id dibuat");
}


/* =========================================================
    APPROVAL LIST
=========================================================*/

public function index()
{
    $perPage = (int) request('per_page', 50);
    if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

    $pemindahan = PemindahanAset::with([
        'details.aset',
        'details.ruanganAsal',
        'details.ruanganTujuan'
    ])
    ->where(function($q){

        $q->where('decision_status','menunggu_persetujuan')

        ->orWhere(function($x){
            $x->where('decision_status','disetujui')
                ->whereHas('details', function($d){
                    $d->where('status','Belum dipindahkan');
                });
        });

    })
    ->latest()
    ->paginate($perPage);

    return view('pemindahan_aset.index',compact('pemindahan','perPage'));
}

public function show($id)
{
    $pemindahan = PemindahanAset::with([
        'details.aset',
        'details.gedungAsal',
        'details.ruanganAsal',
        'details.gedungTujuan',
        'details.ruanganTujuan',
        'details.vendor',
        'requester',
        'approver'
    ])->findOrFail($id);

    return view('pemindahan_aset.show',compact('pemindahan'));
}


public function edit($id)
{
    $data = PemindahanAset::findOrFail($id);

    if(
        $data->details()
            ->where('status','Sudah dipindahkan')
            ->exists()
    ){
        return back()->with('error','Data sudah final');
    }

    return view('pemindahan_aset.edit',[
        'data'=>$data,
        'gedung'=>Gedung::all(),
        'vendors'=>Vendor::all()
    ]);
}

public function update(Request $request,$id)
{
    $data = PemindahanAset::findOrFail($id);

    if(
        $data->details()
            ->where('status','Sudah dipindahkan')
            ->exists()
    ){
        return back()->with('error','Tidak bisa diubah');
    }

    $request->validate([
        'alasan' => 'required|string|max:1000',

        'detail_id' => 'required|array',

        'to_gedung' => 'required|array',
        'to_ruangan' => 'required|array',

        'biaya' => 'nullable|array',

        'pelaksana_type' => 'required|array',

        'id_vendor' => 'nullable|array',
    ]);
    
    foreach ($request->detail_id as $detailId) {

        $detail = PemindahanAsetDetail::find($detailId);

        if (!$detail) {
            continue;
        }

        $detail->update([

            'to_gedung' =>
                $request->to_gedung[$detailId] ?? null,

            'to_ruangan' =>
                $request->to_ruangan[$detailId] ?? null,

            'biaya' =>
                $request->biaya[$detailId] ?? 0,

            'pelaksana_type' =>
                $request->pelaksana_type[$detailId] ?? 'internal',

            'id_vendor' =>
                ($request->pelaksana_type[$detailId] ?? '') === 'vendor'
                    ? ($request->id_vendor[$detailId] ?? null)
                    : null,
        ]);
    }

    return redirect()->route('pemindahan_aset.index')
        ->with('success','Data diperbarui');
}

public function destroy($id)
{
    $p = PemindahanAset::findOrFail($id);

    if(
        $p->details()
        ->where('status','Sudah dipindahkan')
        ->exists()
    ){
        return back()->with('error','Tidak bisa hapus data final');
    }

    DB::transaction(function() use ($p){

        $p->details()->delete();

        $p->delete();

    });

    return back()->with('success','Data dihapus');
}


public function approve($id)
{
    $p = PemindahanAset::findOrFail($id);

    if($p->decision_status != 'menunggu_persetujuan'){
        return back()->with('error','Status tidak valid');
    }

    $p->update([
        'decision_status'=>'disetujui',
        'decided_by'=>auth()->user()->id_user,
        'decided_at'=>now()
    ]);

    return back()->with('success','Disetujui');
}


public function reject(Request $request, $id)
{
    $request->validate([
        'catatan' => 'required|string'
    ],[
        'catatan.required' => 'Catatan penolakan wajib diisi'
    ]);

    $p = PemindahanAset::findOrFail($id);

    $p->update([
        'decision_status' => 'ditolak',
        'catatan' => $request->catatan,
        'decided_by' => auth()->user()->id_user,
        'decided_at' => now()
    ]);

    return back()->with(
        'success',
        'Pengajuan berhasil ditolak'
    );
}


/* =========================================================
    PROSES PINDAH
=========================================================*/

public function pindahkan($id)
{
    DB::transaction(function() use($id){

        $p = PemindahanAset::with([
            'details.aset',
            'details.ruanganAsal',
            'details.ruanganTujuan'
        ])->findOrFail($id);

        if($p->decision_status!='disetujui'){
            throw new \Exception("Belum disetujui");
        }

        foreach($p->details as $detail){

            if($detail->status === 'Sudah dipindahkan') continue;

            $detail->aset->update([
                'id_gedung' => $detail->to_gedung,
                'id_ruangan' => $detail->to_ruangan,
            ]);

            $detail->update([
                'status' => 'Sudah dipindahkan'
            ]);

            AsetLogService::log(
                $detail->id_aset,
                'pemindahan',
                $p->id_pemindahan,
                "Dipindahkan dari {$detail->ruanganAsal->nama_ruangan} ke {$detail->ruanganTujuan->nama_ruangan}"
            );
        }

    });

    return back()->with('success','Aset dipindahkan');
}


/* =========================================================
    LAPORAN + EXPORT
=========================================================*/

public function laporan(Request $request)
{
    $data = $this->buildFilteredQuery($request)
        ->latest()->get();

    return view(
        'pemindahan_aset.laporan',
        $this->summaryData($data)
    );
}

public function exportPdf(Request $request)
{
    $data = $this->buildFilteredQuery($request)->get();

    $pdf = Pdf::loadView(
        'pemindahan_aset.pdf',
        array_merge(
            $this->summaryData($data),
            [
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date
            ]
        )
    );

    return $pdf->download('laporan-pemindahan.pdf');
}

public function exportExcel(Request $request)
{
    return Excel::download(
        new PemindahanAsetExport(
            $request->start_date,
            $request->end_date
        ),
        'laporan-pemindahan-aset.xlsx'
    );
}

public function pindahkanDetail($id)
{
    DB::transaction(function () use ($id) {

        $detail = PemindahanAsetDetail::with([
            'pemindahan',
            'aset',
            'ruanganAsal',
            'ruanganTujuan'
        ])->findOrFail($id);

        if (
            $detail->pemindahan->decision_status != 'disetujui'
        ) {
            throw new \Exception('Belum disetujui');
        }

        if (
            $detail->status == 'Sudah dipindahkan'
        ) {
            return;
        }

        $detail->aset->update([
            'id_gedung' => $detail->to_gedung,
            'id_ruangan' => $detail->to_ruangan,
        ]);

        $detail->update([
            'status' => 'Sudah dipindahkan'
        ]);

        AsetLogService::log(
            $detail->id_aset,
            'pemindahan',
            $detail->id_pemindahan,
            "Dipindahkan dari {$detail->ruanganAsal->nama_ruangan} ke {$detail->ruanganTujuan->nama_ruangan}"
        );
    });

    return back()->with(
        'success',
        'Aset berhasil dipindahkan'
    );
}

}
