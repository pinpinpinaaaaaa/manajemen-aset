<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\PengaduanKerusakan;
use App\Models\PengaduanKerusakanDetail;
use App\Models\Maintenance;
use App\Models\MaintenanceDetail;
use App\Models\Aset;
use App\Services\AsetLogService;
use App\Models\Divisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengaduanKerusakanExport;
use App\Models\Gedung;
use App\Models\Ruangan;

class PengaduanKerusakanController extends Controller
{

    /*=================================================
    GENERATE ID PENGADUAN
    =================================================*/
    private function generatePengaduanId()
    {
        $date = now()->format('Ymd');

        $last = PengaduanKerusakan::where(
            'id_pengaduan',
            'like',
            "PGD-$date-%"
        )->lockForUpdate()->orderByDesc('id_pengaduan')->first();

        $next = $last
            ? (int) substr($last->id_pengaduan,-4)+1
            :1;

        return "PGD-$date-".str_pad($next,4,'0',STR_PAD_LEFT);
    }


    /*=================================================
    GENERATE ID MAINTENANCE
    =================================================*/
    private function generateMaintenanceId()
    {
        $tgl = now()->format('Ymd');

        $last = Maintenance::where('id_maintenance','like',"MNT-$tgl-%")
            ->lockForUpdate()
            ->orderByDesc('id_maintenance')
            ->first();

        $next = $last
            ? (int) substr($last->id_maintenance,-4)+1
            : 1;

        return "MNT-$tgl-".str_pad($next,4,'0',STR_PAD_LEFT);
    }

    private function buildRiwayatQuery(Request $request)
    {
        $query = PengaduanKerusakan::with(['divisi','details.aset']);

        if($request->start_date){
            $query->whereDate('created_at','>=',$request->start_date);
        }

        if($request->end_date){
            $query->whereDate('created_at','<=',$request->end_date);
        }

        return $query->get();
    }

    public function riwayat(Request $request)
    {
        $data = $this->buildRiwayatQuery($request);

        $totalDisetujui = $data
            ->where('decision_status','disetujui')
            ->count();

        $totalDitolak = $data
            ->where('decision_status','ditolak')
            ->count();

        return view('pengaduan_kerusakan.riwayat', compact(
            'data',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    /*=================================================
    INDEX
    =================================================*/
    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $data=PengaduanKerusakan::with([
            'divisi',
            'details.aset'
        ])
        ->where('decision_status', 'menunggu_persetujuan')
        ->latest()
        ->paginate($perPage);

        return view(
            'pengaduan_kerusakan.index',
            compact('data', 'perPage')
        );
    }


    /*=================================================
    SHOW
    =================================================*/
    public function show($id)
    {
        $data = PengaduanKerusakan::with([
            'divisi',
            'details.aset.gedung',
            'details.aset.ruangan',
            'approver'
        ])->findOrFail($id);

        return view(
            'pengaduan_kerusakan.show',
            compact('data')
        );
    }


    /*=================================================
    CREATE FORM
    =================================================*/
    public function create()
    {
        return view('pengaduan_kerusakan.form', [
            'divisi' => Divisi::all(),
            'gedung' => Gedung::all(),
        ]);
    }


    /*=================================================
    STORE
    =================================================*/
    public function store(Request $request)
    {
        $request->validate([

            'nama_pelapor'=>'required',

            'email_pelapor'=>'nullable|email',

            'id_divisi' => 'required|exists:divisi,id_divisi',

            'items' => 'required|array|min:1|max:10',

            'items.*.id_gedung' => 'required|exists:gedung,id_gedung',

            'items.*.id_ruangan' => 'required|exists:ruangan,id_ruangan',

            'items.*.id_aset' => 'required|exists:aset,id_aset',

            'items.*.keluhan'=>'required',

            'items.*.kategori_kerusakan'=>'required',
            'items.*.foto' => 'required|image|mimes:jpg,jpeg,png',

        ], [
            'id_divisi.exists'              => 'Divisi yang dipilih tidak valid.',
            'items.max'                     => 'Laporan tidak boleh lebih dari 10 item kerusakan.',
            'items.*.id_gedung.exists'      => 'Gedung yang dipilih tidak valid.',
            'items.*.id_ruangan.exists'     => 'Ruangan yang dipilih tidak valid.',
            'items.*.id_aset.exists'        => 'Aset yang dipilih tidak valid.',
        ]);

        DB::transaction(function() use($request){

            $id=$this->generatePengaduanId();

            $firstItem = $request->items[0];

            PengaduanKerusakan::create([

                'id_pengaduan'=>$id,

                'nama_pelapor'=>$request->nama_pelapor,

                'email_pelapor'=>$request->email_pelapor,

                'id_divisi'=>$request->id_divisi,

                'id_gedung' => $firstItem['id_gedung'],
                'id_ruangan' => $firstItem['id_ruangan'],

                'decision_status'=>'menunggu_persetujuan'

            ]);

            foreach($request->items as $index => $item){

                $fotoPath = null;

                if(isset($item['foto'])){

                    $file = $item['foto'];
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($file)->scaleDown(width: 1600)->toJpeg(60);
                    $fileName = $id . '-' . ($index + 1) . '.jpg';
                    $fotoPath = 'pengaduan_kerusakan/' . $fileName;
                    Storage::disk('public')->put($fotoPath, $image);
                }

                PengaduanKerusakanDetail::create([
                    'id_pengaduan' => $id,
                    'id_aset' => $item['id_aset'],
                    'id_gedung' => $item['id_gedung'],
                    'id_ruangan' => $item['id_ruangan'],
                    'keluhan' => $item['keluhan'],
                    'kategori_kerusakan' => $item['kategori_kerusakan'],
                    'foto' => $fotoPath
                ]);
            }

        });

        return redirect()
            ->route('form-pengaduan-kerusakan.create')
            ->with(
                'success',
                'Pengaduan berhasil diajukan'
            );
    }



    /*=================================================
    APPROVE
    otomatis generate maintenance
    =================================================*/
    public function approve($id)
    {
        DB::transaction(function() use($id){

            $pengaduan = PengaduanKerusakan::with('details.aset')->findOrFail($id);

            $pengaduan->update([
                'decision_status' => 'disetujui',
                'decided_by'      => auth()->user()->id_user ?? null,
                'decided_at'      => now(),
            ]);

            foreach ($pengaduan->details as $detail) {

                $aset = $detail->aset;
                $maintenanceId = $this->generateMaintenanceId();

                // Header maintenance — hanya field yang ada di tabel/fillable
                Maintenance::create([
                    'id_maintenance'  => $maintenanceId,
                    'id_ruangan'      => $aset->id_ruangan ?? null,
                    'id_gedung'       => $aset->id_gedung  ?? null,
                    'tanggal_laporan' => $pengaduan->created_at,
                    'decision_status' => 'disetujui',
                    'requested_by'    => auth()->user()->id_user ?? null,
                    'decided_by'      => auth()->user()->id_user ?? null,
                    'decided_at'      => now(),
                ]);

                // Detail per-aset — harus dibuat agar tiket muncul di Maintenance Berjalan
                // (index() filter: whereHas('details', status Perlu Perbaikan/Sedang Diperbaiki))
                MaintenanceDetail::create([
                    'id_maintenance'   => $maintenanceId,
                    'id_aset'          => $detail->id_aset,
                    'kerusakan'        => $detail->keluhan,
                    'status'           => 'Perlu Perbaikan',
                    'foto_before'      => $detail->foto,
                    // Simpan status aset SEBELUM diubah ke maintenance
                    'status_aset_awal' => $aset->status,
                    'kelayakan_awal'   => $aset->kelayakan,
                    'keterangan_awal'  => $aset->keterangan_kelayakan,
                ]);

                if ($detail->id_aset) {
                    Aset::where('id_aset', $detail->id_aset)->update(['status' => 'maintenance']);
                    AsetLogService::log(
                        $detail->id_aset,
                        'maintenance_baru',
                        $maintenanceId,
                        'Maintenance dibuat dari pengaduan kerusakan'
                    );
                }
            }

        });

        return back()->with('success', 'Pengaduan disetujui & masuk maintenance');
    }



    /*=================================================
    REJECT
    =================================================*/
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000'
        ]);

        $data = PengaduanKerusakan::findOrFail($id);

        $data->update([
            'decision_status' => 'ditolak',
            'catatan' => $request->catatan,
            'decided_by' => auth()->user()->id_user ?? null,
            'decided_at' => now(),
        ]);

        return back()->with(
            'success',
            'Pengaduan berhasil ditolak'
        );
    }


    /*=================================================
    DELETE
    =================================================*/
    public function destroy($id)
    {
        $data=
            PengaduanKerusakan::findOrFail($id);

        $data->delete();

        return back()->with(
            'success',
            'Data dihapus'
        );
    }

    public function exportPdfRiwayat(Request $request)
    {
        $data = $this->buildRiwayatQuery($request);

        $totalDisetujui = $data
            ->where('decision_status','disetujui')
            ->count();

        $totalDitolak = $data
            ->where('decision_status','ditolak')
            ->count();

        $pdf = Pdf::loadView(
            'pengaduan_kerusakan.pdf',
            [
                'data'=>$data,
                'totalDisetujui'=>$totalDisetujui,
                'totalDitolak'=>$totalDitolak,
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
            ]
        )->setPaper('A4','portrait');

        return $pdf->download('laporan-pengaduan-kerusakan.pdf');
    }

    public function exportExcelRiwayat(Request $request)
    {
        return Excel::download(
            new PengaduanKerusakanExport(
                $request->start_date,
                $request->end_date
            ),
            'laporan-pengaduan-kerusakan.xlsx'
        );
    }

}