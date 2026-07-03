<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\PermintaanKendaraan;
use App\Models\PermintaanKendaraanDetail;
use App\Models\PermintaanKendaraanItem;
use App\Models\Kendaraan;
use App\Models\Divisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermintaanKendaraanExport;

class PermintaanKendaraanController extends Controller
{
    private function generateId(): string
    {
        $date = now()->format('Ymd');

        $last = PermintaanKendaraan::where(
            'id_permohonan',
            'like',
            "PK-$date-%"
        )->lockForUpdate()->orderByDesc('id_permohonan')->first();

        $next = $last
            ? (int) substr($last->id_permohonan, -4) + 1
            : 1;

        return "PK-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function buildRiwayatQuery($request)
    {
        $query = PermintaanKendaraan::with([
            'divisi',
            'details.items.kendaraan'
        ])
        ->whereIn('status', ['selesai', 'ditolak']);

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return $query->latest()->get();
    }

    private function getAvailableVehicles($detail)
    {
        $buffer = 2;

        $start = Carbon::parse(
            $detail->tanggal_mulai . ' ' . $detail->jam_mulai
        )->subHours($buffer);

        $end = Carbon::parse(
            $detail->tanggal_selesai . ' ' . $detail->jam_selesai
        )->addHours($buffer);

        return Kendaraan::where('status_kondisi', 'aktif')
            ->where('tipe', 'mobil')

            ->whereDoesntHave('items.detail', function ($q) use ($start, $end) {

                $q->whereHas('permintaan', function ($q2) {
                    $q2->whereIn('status', [
                        'menunggu konfirmasi',
                        'disetujui',
                        'proses'
                    ]);
                });

                $q->where(function ($q2) use ($start, $end) {

                    $q2->whereRaw("
                        CONCAT(tanggal_mulai,' ',jam_mulai) <= ?
                        AND CONCAT(tanggal_selesai,' ',jam_selesai) >= ?
                    ", [$end, $start]);

                });
            })
            ->get();
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $permintaan = PermintaanKendaraan::with([
            'divisi',
            'details.items.kendaraan'
        ])
        ->whereNotIn('status', [
            'selesai',
            'ditolak'
        ])
        ->latest()
        ->paginate($perPage);

        foreach ($permintaan as $p) {

            foreach ($p->details as $detail) {

                $detail->availableVehicles =
                    $this->getAvailableVehicles($detail);
            }
        }

        return view(
            'permintaan_kendaraan.index',
            compact('permintaan', 'perPage')
        );
    }

    public function show($id)
    {
        $data = PermintaanKendaraan::with('details.items.kendaraan')
            ->findOrFail($id);

        return view('permintaan_kendaraan.show', compact('data'));
    }

    public function create()
    {
        $divisi = Divisi::all();
        $kendaraan = Kendaraan::where('status_kondisi', 'aktif')
            ->where('status_penggunaan', 'tersedia')
            ->get();

        return view('permintaan_kendaraan.form', compact('divisi','kendaraan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pengaju' => 'required',
            'email' => 'nullable|email',
            'id_divisi' => 'required|exists:divisi,id_divisi',

            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',

            'jam_mulai' => 'required',
            'jam_selesai' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        $request->tanggal_mulai === $request->tanggal_selesai &&
                        $value <= $request->jam_mulai
                    ) {
                        $fail('Jam selesai harus lebih dari jam mulai jika tanggalnya sama.');
                    }
                },
            ],

            'keperluan' => 'required',
            'tempat_jemput' => 'required',
            'tempat_tujuan' => 'required',

            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {

            $buffer = 2;

            $start = Carbon::parse(
                $request->tanggal_mulai . ' ' . $request->jam_mulai
            )->subHours($buffer);

            $end = Carbon::parse(
                $request->tanggal_selesai . ' ' . $request->jam_selesai
            )->addHours($buffer);

            $kendaraans = Kendaraan::where('status_kondisi', 'aktif')
                ->where('tipe', 'mobil')
                ->withCount(['items as total_pemakaian'])
                ->orderBy('total_pemakaian', 'asc')
                ->get();

            $kendaraanTersedia = [];

            foreach ($kendaraans as $k) {

                $terpakai = PermintaanKendaraanItem::where(
                    'id_kendaraan',
                    $k->id_kendaraan
                )
                ->whereHas('detail', function ($q) use ($start, $end) {

                    $q->whereHas('permintaan', function ($q2) {

                        $q2->whereIn('status', [
                            'menunggu konfirmasi',
                            'disetujui',
                            'proses'
                        ]);
                    });

                    $q->where(function ($q2) use ($start, $end) {

                        $q2->whereRaw("
                            CONCAT(tanggal_mulai,' ',jam_mulai) <= ?
                            AND CONCAT(tanggal_selesai,' ',jam_selesai) >= ?
                        ", [$end, $start]);
                    });
                })
                ->exists();

                if (!$terpakai) {
                    $kendaraanTersedia[] = $k->id_kendaraan;
                }
            }

            if (count($kendaraanTersedia) < $request->jumlah) {

                throw new \Exception(
                    'Jumlah kendaraan melebihi ketersediaan'
                );
            }

            $id = $this->generateId();

            PermintaanKendaraan::create([
                'id_permohonan' => $id,
                'nama' => $request->nama_pengaju,
                'email' => $request->email,
                'id_divisi' => $request->id_divisi,
                'status' => 'menunggu konfirmasi',
            ]);

            PermintaanKendaraanDetail::create([
                'id_permohonan' => $id,

                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,

                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,

                'keperluan' => $request->keperluan,
                'tempat_jemput' => $request->tempat_jemput,
                'tempat_tujuan' => $request->tempat_tujuan,

                'jumlah' => $request->jumlah,

                'catatan' => $request->catatan,
            ]);
        });

        return redirect()
            ->route('form-permintaan-kendaraan.create')
            ->with(
                'success',
                'Permintaan kendaraan berhasil diajukan'
            );
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'kendaraan' => 'required|array'
        ]);

        DB::transaction(function () use ($request, $id) {

            $permintaan = PermintaanKendaraan::with('details')
                ->findOrFail($id);

            foreach ($permintaan->details as $detail) {

                $selected = $request->kendaraan[$detail->id] ?? [];

                if (count($selected) != $detail->jumlah) {
                    throw new \Exception(
                        "Jumlah kendaraan untuk {$detail->keperluan} harus {$detail->jumlah}"
                    );
                }

                foreach ($selected as $id_kendaraan) {

                    PermintaanKendaraanItem::create([
                        'detail_id' => $detail->id,
                        'id_kendaraan' => $id_kendaraan
                    ]);
                }
            }

            $permintaan->update([
                'status' => 'disetujui'
            ]);
        });

        return back()->with(
            'success',
            'Kendaraan berhasil diassign'
        );
    }

    public function reject($id)
    {
        $data = PermintaanKendaraan::findOrFail($id);

        $data->update([
            'status' => 'ditolak'
        ]);

        return back()->with('success', 'Ditolak');
    }

    public function process($id)
    {
        DB::transaction(function () use ($id) {

            $data = PermintaanKendaraan::with(
                'details.items.kendaraan'
            )->findOrFail($id);

            $data->update([
                'status' => 'proses'
            ]);

            foreach ($data->details as $detail) {

                foreach ($detail->items as $item) {

                    $item->kendaraan->update([
                        'status_penggunaan' => 'terpakai'
                    ]);
                }
            }
        });

        return back()->with(
            'success',
            'Kendaraan sedang digunakan'
        );
    }

    public function complete($id)
    {
        DB::transaction(function () use ($id) {

            $data = PermintaanKendaraan::with(
                'details.items.kendaraan'
            )->findOrFail($id);

            $data->update([
                'status' => 'selesai'
            ]);

            foreach ($data->details as $detail) {

                foreach ($detail->items as $item) {

                    $item->kendaraan->update([
                        'status_penggunaan' => 'tersedia'
                    ]);
                }
            }
        });

        return back()->with(
            'success',
            'Permintaan selesai'
        );
    }

    public function riwayat(Request $request)
    {
        $data = $this->buildRiwayatQuery($request);

        $totalSelesai = $data
            ->where('status', 'selesai')
            ->count();

        $totalDitolak = $data
            ->where('status', 'ditolak')
            ->count();

        return view(
            'permintaan_kendaraan.riwayat',
            compact(
                'data',
                'totalSelesai',
                'totalDitolak'
            )
        );
    }

    public function cekKetersediaan(Request $request)
    {
        $buffer = 2;

        $start = Carbon::parse(
            $request->tanggal_mulai . ' ' . $request->jam_mulai
        )->subHours($buffer);

        $end = Carbon::parse(
            $request->tanggal_selesai . ' ' . $request->jam_selesai
        )->addHours($buffer);

        $stok = Kendaraan::where('status_kondisi', 'aktif')
            ->where('tipe', 'mobil')
            ->count();

        $terpakai = PermintaanKendaraanItem::whereHas(
            'detail',
            function ($q) use ($start, $end) {

                $q->whereHas('permintaan', function ($q2) {

                    $q2->whereIn('status', [
                        'menunggu konfirmasi',
                        'disetujui',
                        'proses'
                    ]);
                });

                $q->where(function ($q2) use ($start, $end) {

                    $q2->whereRaw("
                        CONCAT(tanggal_mulai,' ',jam_mulai) <= ?
                        AND CONCAT(tanggal_selesai,' ',jam_selesai) >= ?
                    ", [$end, $start]);
                });
            }
        )->count();

        $sisa = $stok - $terpakai;

        return response()->json([
            'sisa' => max($sisa, 0)
        ]);
    }

    public function exportPdfRiwayat(Request $request)
    {
        $data = $this->buildRiwayatQuery($request);

        $totalSelesai = $data
            ->where('status', 'selesai')
            ->count();

        $totalDitolak = $data
            ->where('status', 'ditolak')
            ->count();

        $pdf = Pdf::loadView(
            'permintaan_kendaraan.pdf',
            [
                'laporan' => $data,
                'totalSelesai' => $totalSelesai,
                'totalDitolak' => $totalDitolak,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]
        )->setPaper('A4', 'portrait');

        return $pdf->download('laporan-permintaan-kendaraan.pdf');
    }

    public function exportExcelRiwayat(Request $request)
    {
        return Excel::download(
            new PermintaanKendaraanExport(
                $request->start_date,
                $request->end_date
            ),
            'laporan-permintaan-kendaraan.xlsx'
        );
    }
}