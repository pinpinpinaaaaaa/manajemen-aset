<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

use App\Models\Maintenance;
use App\Models\MaintenanceDetail;
use App\Models\Aset;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Services\AsetLogService;
use App\Models\Vendor;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaintenanceExport;

class MaintenanceController extends Controller
{
    private function generateMaintenanceId(): string
    {
        $tgl = now()->format('Ymd');

        $last = Maintenance::where('id_maintenance','like',"MNT-$tgl-%")
            ->lockForUpdate()
            ->orderBy('id_maintenance','desc')
            ->first();

        $next = $last
            ? (int) substr($last->id_maintenance,-4)+1
            : 1;

        return "MNT-$tgl-".str_pad($next,4,'0',STR_PAD_LEFT);
    }

    private function uploadPhoto($file,$id,$type)
    {
        if(!$file) return null;

        $manager = new ImageManager(new Driver());

        $img = $manager->read($file)
            ->scaleDown(width:1600)
            ->toJpeg(60);

        $name = $type.'_'.$id.'_'.Str::uuid().'.jpg';

        $path = "maintenance/$type/$name";

        Storage::disk('public')->put($path,$img);

        return $path;
    }



    private function buildFilteredQuery(Request $r)
    {
        $q = Maintenance::with([
            'details.aset',
            'details.vendor',
            'gedung',
            'ruangan',
            'requester',
            'approver'
        ])
        ->where(function($qq){

            $qq->where('decision_status','ditolak')

            ->orWhereHas('details', function($x){
                $x->where('status','Selesai');
            });

        });

        if($r->start_date){
            $q->whereDate('tanggal_laporan','>=',$r->start_date);
        }

        if($r->end_date){
            $q->whereDate('tanggal_laporan','<=',$r->end_date);
        }

        return $q;
    }

    private function summaryData($data)
    {
        return [

            'maintenance' => $data,

            'totalBiaya' => $data->sum(function ($m) {
                return $m->details->sum('biaya');
            }),

            'totalSelesai' => $data
                ->filter(fn($m) =>
                    $m->details->where('status','Selesai')->count()
                )
                ->count(),

            'totalDitolak' => $data
                ->where('decision_status','ditolak')
                ->count(),
        ];
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $maintenance = Maintenance::with([
            'details.aset',
            'details.vendor',
        ])
        ->where(function ($q) {

            $q->where('decision_status', 'menunggu_persetujuan')

            ->orWhere(function ($qq) {

                $qq->where('decision_status', 'disetujui')
                ->whereHas('details', function ($d) {
                        $d->whereIn('status', [
                            'Perlu Perbaikan',
                            'Sedang Diperbaiki'
                        ]);
                });

            });

        })
        ->orderByDesc('tanggal_laporan')
        ->paginate($perPage);

        $vendors = Vendor::all();

        return view('maintenance.index', compact(
            'maintenance',
            'vendors',
            'perPage'
        ));
    }
    
    public function show($id)
    {
        $maintenance = Maintenance::with([
            'details.aset.gedung',
            'details.aset.ruangan',
            'details.vendor',
            'gedung',
            'ruangan',
            'requester',
            'decider'
        ])->findOrFail($id);
        $vendors = Vendor::all();

        return view('maintenance.show', compact(
            'maintenance',
            'vendors'
        ));
    }

    public function create()
    {
        
        return view('maintenance.create',[
            'aset'=>Aset::all(),
            'gedung'=>Gedung::all(),
            'ruangan'=>Ruangan::all(),
            'vendors'=>Vendor::all()
        ]);
    }


    public function store(Request $r)
    {
        $r->merge([
            'details' => collect($r->details)
                ->filter(function ($detail) {
                    return isset($detail['dipilih']);
                })
                ->values()
                ->toArray()
        ]);
        $r->validate([
            'pelaksana_type' => 'nullable|in:vendor,internal,lainnya',
            'id_vendor' => 'required_if:pelaksana_type,vendor|nullable|exists:vendors,id_vendor',
            'details' => 'required|array|min:1',

            'details.*.id_aset' => 'required|exists:aset,id_aset',
            'details.*.kerusakan' => 'required|string',

            'details.*.foto_before' => 'required|image',
            'details.*.lampiran' => 'nullable|file|max:5120',
        ]);

        foreach($r->details as $detail)
        {
            $aset = Aset::findOrFail($detail['id_aset']);

            if(
                MaintenanceDetail::where('id_aset', $aset->id_aset)
                    ->whereIn('status', [
                        'Perlu Perbaikan',
                        'Sedang Diperbaiki'
                    ])
                    ->whereHas('maintenance', function ($q) {
                        $q->where('decision_status', '!=', 'ditolak');
                    })
                    ->exists()
            ){
                return back()->with(
                    'error',
                    "Aset {$aset->nama_aset} masih dalam proses maintenance."
                );
            }
        }
        $id = DB::transaction(function () use ($r) {
            $id = $this->generateMaintenanceId();

            Maintenance::create([
                'id_maintenance' => $id,
                'tanggal_laporan' => now(),
                'requested_by' => auth()->user()->id_user,
                'decision_status' => 'menunggu_persetujuan',
                'catatan' => $r->catatan,
            ]);

            foreach($r->details as $i => $detail)
            {
                $aset = Aset::findOrFail($detail['id_aset']);

                MaintenanceDetail::create([
                    'id_maintenance' => $id,
                    'id_aset' => $aset->id_aset,

                    'kerusakan' => $detail['kerusakan'],
                    'status' => 'Perlu Perbaikan',

                    'kelayakan_awal' => $aset->kelayakan,
                    'keterangan_awal' => $aset->keterangan_kelayakan,
                    'status_aset_awal' => $aset->status,

                    'foto_before' => $this->uploadPhoto(
                        $r->file("details.$i.foto_before"),
                        $id,
                        'before'
                    ),

                    'lampiran' => $r->file("details.$i.lampiran")
                        ? $r->file("details.$i.lampiran")
                            ->store('maintenance/lampiran','public')
                        : null,

                    'pelaksana_type' => $r->pelaksana_type,
                    'id_vendor' => $r->id_vendor,
                ]);

                $aset->update([
                    'status'=>'maintenance',
                    'kelayakan'=>3,
                    'keterangan_kelayakan'=>'Perlu pemantauan'
                ]);

                AsetLogService::log($aset->id_aset,'maintenance_baru',$id,'Maintenance dibuat');
            }

            return $id;
        });

        return redirect()->route('maintenance.index')
            ->with('success',"Maintenance #$id dibuat");
    }


    public function approve($id)
    {
        $m = Maintenance::with('details')->findOrFail($id);

        if($m->decision_status!='menunggu_persetujuan')
            return back()->with('error',
                'Permintaan ini sudah diproses sebelumnya dan tidak dapat diubah lagi.'
            );

        $m->update([
            'decision_status'=>'disetujui',
            'decided_by'=>auth()->user()->id_user,
            'decided_at'=>now()
        ]);

        foreach ($m->details as $detail) {

            AsetLogService::log(
                $detail->id_aset,
                'maintenance_approve',
                $id,
                'Disetujui'
            );
        }

        return back()->with('success','Disetujui');
    }


    public function reject(Request $request, $id)
    {
        
        $request->validate([
            'catatan' => 'required|string|min:5'
        ],[
            'catatan.required' => 'Alasan penolakan wajib diisi'
        ]);

        $m = Maintenance::with('details.aset')->findOrFail($id);

        $m->update([
            'decision_status' => 'ditolak',
            'decided_by'      => auth()->user()->id_user,
            'decided_at'      => now(),
            'catatan'         => $request->catatan
        ]);

        foreach ($m->details as $detail) {

            $aset = $detail->aset;

            if(!$aset){
                continue;
            }

            $aset->update([
                'status' => $detail->status_aset_awal,
                'kelayakan' => $detail->kelayakan_awal,
                'keterangan_kelayakan' => $detail->keterangan_awal
            ]);

            AsetLogService::log(
                $detail->id_aset,
                'maintenance_reject',
                $id,
                'Ditolak : '.$request->catatan
            );
        }
        return back()->with('success','Maintenance ditolak');
    }

    public function update(Request $r, $id)
    {
        $m = Maintenance::with('details')->findOrFail($id);

        foreach($m->details as $detail){
        $r->validate([
            'detail_id' => 'required|array',

            'kerusakan.*' => 'nullable|string',
            'biaya.*' => 'nullable|numeric|min:0',

            'pelaksana_type.*' => 'nullable|in:vendor,internal,lainnya',
            'id_vendor.*' => 'nullable|exists:vendors,id_vendor',

            'foto_before.*' => 'nullable|image',
            'foto_after.*' => 'nullable|image',

            'lampiran.*' => 'nullable|file|max:5120',

            'catatan.*' => 'nullable|string',
        ]);

        }


        \DB::transaction(function () use ($r, $m) {

            foreach ($m->details as $detail) {

                $id = $detail->id;

                if ($r->hasFile("foto_before.$id")) {

                    if (
                        $detail->foto_before &&
                        Storage::disk('public')->exists($detail->foto_before)
                    ) {
                        Storage::disk('public')->delete($detail->foto_before);
                    }

                    $detail->foto_before =
                        $r->file("foto_before.$id")
                            ->store('maintenance/before', 'public');
                }

                if ($r->hasFile("foto_after.$id")) {

                    if (
                        $detail->foto_after &&
                        Storage::disk('public')->exists($detail->foto_after)
                    ) {
                        Storage::disk('public')->delete($detail->foto_after);
                    }

                    $detail->foto_after =
                        $r->file("foto_after.$id")
                            ->store('maintenance/after', 'public');
                }

                if ($r->hasFile("lampiran.$id")) {

                    if (
                        $detail->lampiran &&
                        Storage::disk('public')->exists($detail->lampiran)
                    ) {
                        Storage::disk('public')->delete($detail->lampiran);
                    }

                    $detail->lampiran =
                        $r->file("lampiran.$id")
                            ->store('maintenance/lampiran', 'public');
                }

                $detail->update([
                    'kerusakan' => $r->kerusakan[$id] ?? $detail->kerusakan,
                    'biaya' => $r->biaya[$id] ?? $detail->biaya,

                    'pelaksana_type' =>
                        $r->pelaksana_type[$id] ?? null,

                    'id_vendor' =>
                        ($r->pelaksana_type[$id] ?? null) == 'vendor'
                            ? ($r->id_vendor[$id] ?? null)
                            : null,

                    'catatan' =>
                        $r->catatan[$id] ?? null,

                    'foto_before' => $detail->foto_before,
                    'foto_after' => $detail->foto_after,
                    'lampiran' => $detail->lampiran,
                ]);
            }
        });

        return redirect()
            ->route('maintenance.edit', $id)
            ->with('success','Data maintenance berhasil diperbarui');
    }



    public function edit($id)
    {
        $maintenance = Maintenance::with([
            'details.aset',
            'details.vendor'
        ])->findOrFail($id);

        if (
            $maintenance->details()
                ->where('status','Selesai')
                ->exists()
        ) {
            return back()->with('error',
                'Data maintenance yang sudah selesai tidak dapat diedit.'
            );
        }

        return view('maintenance.edit',[
            'maintenance'=>$maintenance,
            'aset'=>Aset::all(),
            'gedung'=>Gedung::all(),
            'ruangan'=>Ruangan::all(),
            'vendors'=>Vendor::all()
        ]);
    }

    public function destroy($id)
    {
        $m = Maintenance::findOrFail($id);

        if (
            $m->details()
                ->where('status','Selesai')
                ->exists()
        ) {
            return back()->with(
                'error',
                'Tidak bisa hapus maintenance selesai'
            );
        }
        foreach ($m->details as $detail) {
            $aset = $detail->aset;

            if(!$aset){
                continue;
            }

            $aset->update([
                'status' => $detail->status_aset_awal,
                'kelayakan' => $detail->kelayakan_awal,
                'keterangan_kelayakan' => $detail->keterangan_awal
            ]);
                Storage::disk('public')->delete(
                    array_filter([
                        $detail->foto_before,
                        $detail->foto_after,
                        $detail->lampiran
                    ])
                );
        }
        $m->details()->delete();
        $m->delete();   

        return back()->with('success','Maintenance dihapus');
    }


    public function laporan(Request $r)
    {
        $data=$this->buildFilteredQuery($r)->get();

        return view('maintenance.laporan',
            $this->summaryData($data)
        );
    }


    public function exportPdf(Request $r)
    {
        $data=$this->buildFilteredQuery($r)->get();

        $pdf=Pdf::loadView(
            'maintenance.pdf',
            array_merge(
                $this->summaryData($data),
                ['start_date'=>$r->start_date,'end_date'=>$r->end_date]
            )
        )->setPaper('A4');

        return $pdf->download('laporan-maintenance.pdf');
    }


    public function exportExcel(Request $r)
    {
        return Excel::download(
            new MaintenanceExport(
                $r->start_date,
                $r->end_date
            ),
            'laporan-maintenance.xlsx'
        );
    }

    public function mulai($id)
    {
        $m = Maintenance::findOrFail($id);

        if ($m->decision_status !== 'disetujui') {
            return back()->with('error','Belum disetujui');
        }

        if (
            !$m->details()
                ->where('status','Perlu Perbaikan')
                ->exists()
        ) {
            return back()->with('error','Status tidak valid');
        }

        foreach ($m->details as $detail) {

            if($detail->status == 'Perlu Perbaikan'){

                $detail->update([
                    'status' => 'Sedang Diperbaiki',
                    'tanggal_mulai' => now()
                ]);
            }
        }

        return back()->with('success','Maintenance dimulai');
    }

    public function selesai(Request $request, $id)
    {
        $m = Maintenance::with('details.aset')->findOrFail($id);

        $request->validate([
            'biaya' => 'required|numeric|min:0',
            'foto_after' => 'required|image|mimes:jpg,jpeg,png',
            'pelaksana_type' => 'required|in:internal,vendor',
            'id_vendor' => 'required_if:pelaksana_type,vendor',
        ]);

        if ($m->decision_status !== 'disetujui') {
            return back()->with('error','Maintenance belum disetujui');
        }

        if (
            !$m->details()
                ->where('status','Sedang Diperbaiki')
                ->exists()
        ) {
            return back()->with('error','Maintenance belum dalam proses');
        }

        \DB::transaction(function () use ($request, $m, $id) {

            foreach ($m->details as $detail) {
                $fotoAfter = $this->uploadPhoto(
                    $request->file('foto_after'),
                    $id,
                    'after'
                );

                $tanggalSelesai = now();

                $detail->update([
                    'status' => 'Selesai',
                    'tanggal_selesai' => $tanggalSelesai,
                    'durasi_jam' => Carbon::parse($detail->tanggal_mulai)
                        ->diffInHours($tanggalSelesai),
                    'biaya' => str_replace('.', '', $request->biaya),
                    'pelaksana_type' => $request->pelaksana_type,
                    'id_vendor' => $request->id_vendor,
                    'foto_after' => $fotoAfter,
                    'catatan' => $request->catatan,
                ]);

                if ($detail->aset) {
                    $detail->aset->update([
                        'status' => 'tersedia',
                        'kelayakan' => 2,
                        'keterangan_kelayakan' => 'Layak'
                    ]);
                }

                AsetLogService::log(
                    $detail->id_aset,
                    'maintenance_selesai',
                    $id,
                    'Maintenance selesai'
                );
            }
        });

        return back()->with('success','Maintenance berhasil diselesaikan');
    }


public function mulaiDetail($id)
{
    $detail = MaintenanceDetail::with('maintenance')
        ->findOrFail($id);

    if ($detail->maintenance->decision_status != 'disetujui') {
        return back()->with(
            'error',
            'Maintenance belum disetujui'
        );
    }

    if ($detail->status != 'Perlu Perbaikan') {
        return back()->with(
            'error',
            'Status aset tidak valid'
        );
    }

    $detail->update([
        'status' => 'Sedang Diperbaiki',
        'tanggal_mulai' => now(),
    ]);

    return back()->with(
        'success',
        'Maintenance aset dimulai'
    );
}

public function selesaiDetail(Request $request, $id)
{
    $detail = MaintenanceDetail::with([
        'maintenance',
        'aset'
    ])->findOrFail($id);

    $request->validate([
        'biaya' => 'required|numeric|min:0',
        'foto_after' => 'required|image',
        'pelaksana_type' => 'required|in:internal,vendor',
        'id_vendor' => 'required_if:pelaksana_type,vendor',
    ]);

    if ($detail->status != 'Sedang Diperbaiki') {
        return back()->with('error', 'Status tidak valid');
    }

    $fotoAfter = $this->uploadPhoto(
        $request->file('foto_after'),
        $detail->id,
        'after'
    );

    $tanggalSelesai = now();

    $detail->update([
        'status' => 'Selesai',
        'tanggal_selesai' => $tanggalSelesai,
        'durasi_jam' => Carbon::parse($detail->tanggal_mulai)
            ->diffInHours($tanggalSelesai),
        'biaya' => $request->biaya,
        'pelaksana_type' => $request->pelaksana_type,
        'id_vendor' => $request->id_vendor,
        'foto_after' => $fotoAfter,
        'catatan' => $request->catatan,
    ]);

    if ($detail->aset) {
        $detail->aset->update([
            'status' => 'tersedia',
            'kelayakan' => 2,
            'keterangan_kelayakan' => 'Layak'
        ]);
    }

    $maintenance = $detail->maintenance;

    $maintenance->update([
        'biaya_total' => $maintenance->details()->sum('biaya')
    ]);

    if (!$maintenance->details()->where('status', '!=', 'Selesai')->exists()) {

        $firstStart = $maintenance->details()
            ->whereNotNull('tanggal_mulai')
            ->orderBy('tanggal_mulai', 'asc')
            ->value('tanggal_mulai');

        $lastFinish = $maintenance->details()
            ->max('tanggal_selesai');

        $maintenance->update([
            'tanggal_mulai' => $firstStart,
            'tanggal_selesai' => $lastFinish,
            'durasi_jam' => $firstStart && $lastFinish
                ? Carbon::parse($firstStart)->diffInHours($lastFinish)
                : null
        ]);
    }

    AsetLogService::log(
        $detail->id_aset,
        'maintenance_selesai',
        $detail->id_maintenance,
        'Maintenance selesai'
    );

    return back()->with(
        'success',
        'Maintenance aset berhasil diselesaikan'
    );
}

}