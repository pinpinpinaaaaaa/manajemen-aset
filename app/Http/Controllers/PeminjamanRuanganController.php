<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\PeminjamanRuangan;
use App\Models\PeminjamanRuanganDetail;
use App\Models\PeminjamanRuanganAset;
use App\Models\PeminjamanRuanganKonsumsi;
use App\Models\Divisi;
use App\Models\Aset;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\JenisBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeminjamanRuanganExport;

class PeminjamanRuanganController extends Controller
{
    
    private function generateId(): string
    {
        $date = now()->format('Ymd');

        $last = PeminjamanRuangan::where(
            'id_peminjaman',
            'like',
            "PMJ_R-$date-%"
        )->lockForUpdate()->orderByDesc('id_peminjaman')->first();

        $next = $last
            ? (int) substr($last->id_peminjaman, -4) + 1
            : 1;

        return "PMJ_R-$date-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function getRuanganByGedung($idGedung)
    {
        $ruangan = \App\Models\Ruangan::where('id_gedung', $idGedung)
            ->get();

        return response()->json($ruangan);
    }

    public function cekKetersediaanRuangan(Request $request)
    {
        $request->validate([
            'id_ruangan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $mulai = Carbon::parse(
            $request->tanggal_mulai.' '.$request->jam_mulai
        )->subHours(2);

        $selesai = Carbon::parse(
            $request->tanggal_selesai.' '.$request->jam_selesai
        )->addHours(2);

        $bentrok = PeminjamanRuanganDetail::where(
            'id_ruangan',
            $request->id_ruangan
        )
        ->whereHas('peminjaman', function ($q) {
            $q->where('decision_status', '!=', 'ditolak')
            ->where('status', '!=', 'Selesai');
        })
        ->where(function ($q) use ($mulai, $selesai) {

            $q->whereRaw("
                TIMESTAMP(tanggal_mulai, jam_mulai) <= ?
                AND
                TIMESTAMP(tanggal_selesai, jam_selesai) >= ?
            ", [
                $selesai,
                $mulai
            ]);

        })
        ->exists();

        return response()->json([
            'available' => !$bentrok
        ]);
    }

    private function ambilAsetTersedia(
        $idJenisBarang,
        $mulai,
        $selesai,
        $excludeAset = []
    )
    {
        return Aset::where('id_jenis_barang', $idJenisBarang)

            // tidak boleh aset yang sudah dipakai di request ini
            ->whereNotIn('id_aset', $excludeAset)
            ->whereNotIn('kelayakan', [4, 5])
            ->where('status', 'tersedia')

            ->whereDoesntHave('peminjamanRuanganAset.detail', function ($q)
                use ($mulai, $selesai) {

                $q->whereHas('peminjaman', function ($p) {
                    $p->where('decision_status', '!=', 'ditolak')
                    ->where('status', '!=', 'Selesai');
                });

                $q->whereRaw("
                    TIMESTAMP(tanggal_mulai, jam_mulai) <= ?
                    AND
                    TIMESTAMP(tanggal_selesai, jam_selesai) >= ?
                ", [
                    $selesai->copy()->addHours(2),
                    $mulai->copy()->subHours(2)
                ]);
            });
    }

    public function cekKetersediaanAsetRuangan(Request $request)
    {
        $namaAset = $request->nama_aset;
        $idJenisBarang = $request->id_jenis_barang;

        $mulai = Carbon::parse(
            $request->tanggal_mulai.' '.$request->jam_mulai
        );

        $selesai = Carbon::parse(
            $request->tanggal_selesai.' '.$request->jam_selesai
        );

        $total = Aset::where('nama_aset',$namaAset)
            ->where('id_jenis_barang',$idJenisBarang)
            ->where('status','tersedia')
            ->whereNotIn('kelayakan',[4,5])
            ->count();

        $dipakai = Aset::where('nama_aset',$namaAset)
            ->where('id_jenis_barang',$idJenisBarang)
            ->whereHas('peminjamanRuanganAset.detail', function($q)
                use($mulai,$selesai){

                $q->whereHas('peminjaman', function($p){
                    $p->where('decision_status','!=','ditolak')
                    ->where('status','!=','Selesai');
                });

                $q->whereRaw("
                    TIMESTAMP(tanggal_mulai,jam_mulai) <= ?
                    AND
                    TIMESTAMP(tanggal_selesai,jam_selesai) >= ?
                ",[
                    $selesai,
                    $mulai
                ]);
            })
            ->count();

        return response()->json([
            'sisa' => $total - $dipakai
        ]);
    }

    public function getRuanganAvailable(Request $request)
    {
        try {

            $request->validate([
                'gedung' => 'required',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
            ]);

            $mulai = Carbon::parse(
                $request->tanggal_mulai . ' ' . $request->jam_mulai
            )->subHours(2);

            $selesai = Carbon::parse(
                $request->tanggal_selesai . ' ' . $request->jam_selesai
            )->addHours(2);

            $ruanganDipakai = PeminjamanRuanganDetail::whereHas(
                'peminjaman',
                function ($q) {
                    $q->where('decision_status', '!=', 'ditolak')
                    ->where('status', '!=', 'Selesai');
                }
            )
            ->where(function ($q) use ($mulai, $selesai) {

                $q->whereRaw("
                    TIMESTAMP(tanggal_mulai, jam_mulai) <= ?
                    AND
                    TIMESTAMP(tanggal_selesai, jam_selesai) >= ?
                ", [
                    $selesai,
                    $mulai
                ]);
            })
            ->pluck('id_ruangan');

            $ruangan = Ruangan::where(
                'id_gedung',
                $request->gedung
            )
            ->whereNotIn('id_ruangan', $ruanganDipakai)
            ->get();

            return response()->json($ruangan);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    public function getAsetTersedia(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $mulai = Carbon::parse(
            $request->tanggal_mulai . ' ' . $request->jam_mulai
        );

        $selesai = Carbon::parse(
            $request->tanggal_selesai . ' ' . $request->jam_selesai
        );

        $excludeAset = $request->exclude_aset ?? [];

        $jenisBarang = \App\Models\JenisBarang::where('jenis', 'sarana')->get();

        $result = [];

        foreach ($jenisBarang as $jenis) {

            $asetTersedia = $this->ambilAsetTersedia(
                $jenis->id_jenis_barang,
                $mulai,
                $selesai,
                $excludeAset
            )
            ->get()
            ->groupBy('nama_aset');

            foreach($asetTersedia as $namaAset => $items){

                $result[] = [
                    'id_jenis_barang' => $jenis->id_jenis_barang,
                    'nama_aset' => $namaAset,
                    'total_tersedia' => $items->count()
                ];
            }
        }

        return response()->json($result);
    }

    public function getTanggalAvailable($idRuangan)
    {
        $today = Carbon::today();
        $end = Carbon::today()->addDays(30);

        $dates = [];

        while ($today <= $end) {

            $bentrok = PeminjamanRuanganDetail::where('id_ruangan', $idRuangan)
                ->whereHas('peminjaman', function ($q) {
                    $q->where('decision_status', '!=', 'ditolak')
                    ->where('status', '!=', 'Selesai');
                })
                ->whereDate('tanggal_mulai', '<=', $today)
                ->whereDate('tanggal_selesai', '>=', $today)
                ->exists();

            $dates[] = [
                'tanggal' => $today->toDateString(),
                'available' => !$bentrok
            ];

            $today->addDay();
        }

        return response()->json($dates);
    }

    public function riwayat(Request $request)
    {
        $data = $this->buildRiwayatQuery($request)->get();

        return view('peminjaman_ruangan.riwayat', [
            'data' => $data,

            'totalSelesai' => $data
                ->where('status', 'Selesai')
                ->where('decision_status', 'disetujui')
                ->count(),

            'totalDitolak' => $data
                ->where('decision_status', 'ditolak')
                ->count(),
        ]);
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $peminjaman = PeminjamanRuangan::with(['divisi', 'details'])
            ->where('decision_status', '!=', 'ditolak')
            ->where('status', '!=', 'Selesai')
            ->latest()
            ->paginate($perPage);

        return view('peminjaman_ruangan.index', compact('peminjaman', 'perPage'));
    }

    public function create()
    {
        $gedung = Gedung::all();
        $divisi = Divisi::all();
        $jenisBarang = JenisBarang::all();

        return view('peminjaman_ruangan.form', compact(
            'gedung',
            'divisi',
            'jenisBarang'
        ));
    }
    public function store(Request $request)
    {
        $excludeAsetIds = [];

        $request->validate([
            'nama_pengaju'     => 'required',
            'email_pengaju'    => 'required|email',
            'id_divisi'        => 'required|exists:divisi,id_divisi',
            'jenis_kegiatan'   => 'required',

            'nama_kegiatan' => [
                'nullable',
                'required_unless:jenis_kegiatan,rapat'
            ],

            'peserta_rapat' => [
                'nullable',
                'required_if:jenis_kegiatan,rapat'
            ],

            'ruangan'              => 'required|array|min:1|max:5',
            'ruangan.*.id_ruangan' => 'required|exists:ruangan,id_ruangan',
            'ruangan.*.id_gedung'  => 'required|exists:gedung,id_gedung',
        ], [
            'nama_pengaju.required'              => 'Nama peminjam wajib diisi.',
            'email_pengaju.required'             => 'Email peminjam wajib diisi.',
            'email_pengaju.email'                => 'Format email tidak valid.',
            'id_divisi.required'                 => 'Divisi wajib dipilih.',
            'id_divisi.exists'                   => 'Divisi yang dipilih tidak valid.',
            'jenis_kegiatan.required'            => 'Jenis kegiatan wajib dipilih.',
            'nama_kegiatan.required_unless'      => 'Nama kegiatan wajib diisi.',
            'peserta_rapat.required_if'          => 'Daftar/nama peserta rapat wajib diisi.',
            'ruangan.required'                   => 'Minimal 1 ruangan harus ditambahkan.',
            'ruangan.min'                        => 'Minimal 1 ruangan harus ditambahkan.',
            'ruangan.max'                        => 'Peminjaman tidak boleh lebih dari 5 ruangan sekaligus.',
            'ruangan.*.id_ruangan.required'      => 'Ruangan wajib dipilih.',
            'ruangan.*.id_ruangan.exists'        => 'Ruangan yang dipilih tidak valid.',
            'ruangan.*.id_gedung.required'       => 'Gedung wajib dipilih.',
            'ruangan.*.id_gedung.exists'         => 'Gedung yang dipilih tidak valid.',
        ]);


        try {

            DB::transaction(function () use ($request, &$excludeAsetIds) {

                $id = $this->generateId();

                $header = PeminjamanRuangan::create([
                    'id_peminjaman'   => $id,
                    'nama_pengaju'    => $request->nama_pengaju,
                    'email_pengaju'   => $request->email_pengaju,
                    'id_divisi'       => $request->id_divisi,
                    'jenis_kegiatan'  => $request->jenis_kegiatan,

                    'nama_kegiatan'   => $request->jenis_kegiatan === 'rapat'
                        ? null
                        : $request->nama_kegiatan,

                    'peserta_rapat'   => $request->jenis_kegiatan === 'rapat'
                        ? $request->peserta_rapat
                        : null,

                    'catatan'         => $request->catatan,
                    'decision_status' => 'menunggu_persetujuan',
                    'status'          => 'Belum Diproses',
                ]);

                foreach ($request->ruangan as $r) {

                    $mulai = Carbon::parse(
                        $r['tanggal_mulai'].' '.$r['jam_mulai']
                    )->subHours(2);

                    $selesai = Carbon::parse(
                        $r['tanggal_selesai'].' '.$r['jam_selesai']
                    )->addHours(2);

                    $bentrok = PeminjamanRuanganDetail::where(
                        'id_ruangan',
                        $r['id_ruangan']
                    )
                    ->whereHas('peminjaman', function ($q) {
                        $q->where('decision_status', '!=', 'ditolak')
                        ->where('status', '!=', 'Selesai');
                    })
                    ->where(function ($q) use ($mulai, $selesai) {

                        $q->whereRaw("
                            TIMESTAMP(tanggal_mulai, jam_mulai) <= ?
                            AND
                            TIMESTAMP(tanggal_selesai, jam_selesai) >= ?
                        ", [
                            $selesai,
                            $mulai
                        ]);

                    })
                    ->exists();

                    if ($bentrok) {
                        throw new \Exception("Ruangan bentrok jadwal.");
                    }

                    $detail = PeminjamanRuanganDetail::create([
                        'id_peminjaman' => $header->id_peminjaman,
                        'id_gedung' => $r['id_gedung'],
                        'id_ruangan' => $r['id_ruangan'],
                        'tanggal_mulai' => $r['tanggal_mulai'],
                        'tanggal_selesai' => $r['tanggal_selesai'],
                        'jam_mulai' => $r['jam_mulai'],
                        'jam_selesai' => $r['jam_selesai'],
                        'catatan' => $r['catatan'] ?? null,
                    ]);

                    if (!empty($r['aset'])) {

                        foreach ($r['aset'] as $a) {

                            if (
                                empty($a['id_jenis_barang']) ||
                                empty($a['jumlah'])
                            ) {
                                continue;
                            }

                            $jumlahDiminta = (int) $a['jumlah'];

                            $mulaiAset = Carbon::parse(
                                $r['tanggal_mulai'].' '.$r['jam_mulai']
                            );

                            $selesaiAset = Carbon::parse(
                                $r['tanggal_selesai'].' '.$r['jam_selesai']
                            );

                            $asetTersedia = $this->ambilAsetTersedia(
                                $a['id_jenis_barang'],
                                $mulaiAset,
                                $selesaiAset,
                                $excludeAsetIds
                            )
                            ->where('nama_aset', $a['nama_aset'])
                            ->limit($jumlahDiminta)
                            ->get();

                            if ($asetTersedia->count() < $jumlahDiminta) {

                                $namaBarang = JenisBarang::where(
                                    'id_jenis_barang',
                                    $a['id_jenis_barang']
                                )->value('nama_barang');

                                throw new \Exception(
                                    "{$namaBarang} tidak tersedia sesuai jumlah yang diminta."
                                );
                            }

                            foreach ($asetTersedia as $asetUnit) {

                                $excludeAsetIds[] = $asetUnit->id_aset;

                                PeminjamanRuanganAset::create([
                                    'detail_id' => $detail->id,
                                    'id_aset'   => $asetUnit->id_aset,
                                    'jumlah'    => 1,
                                ]);
                            }
                        }
                    }
                }

                if ($request->konsumsi) {

                    foreach ($request->konsumsi as $k) {

                        if (
                            empty($k['jenis_konsumsi']) ||
                            empty($k['jumlah'])
                        ) {
                            continue;
                        }

                        PeminjamanRuanganKonsumsi::create([
                            'id_peminjaman' => $header->id_peminjaman,
                            'jenis_konsumsi' => $k['jenis_konsumsi'],
                            'jumlah' => $k['jumlah'],
                            'catatan' => $k['catatan'] ?? null,
                        ]);
                    }
                }

            });

            return back()->with(
                'success',
                'Peminjaman berhasil diajukan.'
            );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());

        }
    }
    public function show($id)
    {
        $data = PeminjamanRuangan::with([
            'divisi',
            'details.ruangan',
            'details.gedung',
            'details.aset.aset',
            'details.aset.gudangBarang',
            'konsumsi'
        ])->findOrFail($id);

        return view('peminjaman_ruangan.show', compact('data'));
    }

    public function edit($id)
    {
        $peminjaman = PeminjamanRuangan::with([
            'details.ruangan',
            'details.gedung',
            'details.aset.aset',
            'konsumsi'
        ])->findOrFail($id);

        $divisi = Divisi::all();

        $gedung = Gedung::all();

        $ruangan = Ruangan::all();

        $asetList = Aset::where('status','tersedia')
            ->whereNotIn('kelayakan',[4,5])
            ->get();

        return view(
            'peminjaman_ruangan.edit',
            compact(
                'peminjaman',
                'divisi',
                'gedung',
                'ruangan',
                'asetList'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pengaju'   => 'required',
            'email_pengaju'  => 'required|email',
            'id_divisi'      => 'required|exists:divisi,id_divisi',
            'jenis_kegiatan' => 'required',
            'ruangan'        => 'nullable|array',
        ], [
            'id_divisi.exists' => 'Divisi yang dipilih tidak valid.',
        ]);

        try {

            DB::transaction(function () use ($request, $id) {

                $header = PeminjamanRuangan::with([
                    'details.aset',
                    'konsumsi'
                ])->findOrFail($id);
                
                $header->update([
                    'nama_pengaju'   => $request->nama_pengaju,
                    'email_pengaju'  => $request->email_pengaju,
                    'id_divisi'      => $request->id_divisi,
                    'jenis_kegiatan' => $request->jenis_kegiatan,

                    'nama_kegiatan' => $request->jenis_kegiatan === 'rapat'
                        ? null
                        : $request->nama_kegiatan,

                    'peserta_rapat' => $request->jenis_kegiatan === 'rapat'
                        ? $request->peserta_rapat
                        : null,

                    'catatan' => $request->catatan,
                ]);

                foreach ($request->ruangan ?? [] as $r) {

                    if (!empty($r['detail_id'])) {

                        $detail = PeminjamanRuanganDetail::findOrFail(
                            $r['detail_id']
                        );

                        if (($r['deleted'] ?? 0) == 1) {

                            PeminjamanRuanganAset::where(
                                'detail_id',
                                $detail->id
                            )->delete();

                            $detail->delete();

                            continue;
                        }
                        
                        $mulai = Carbon::parse(
                            $r['tanggal_mulai'].' '.$r['jam_mulai']
                        )->subHours(2);

                        $selesai = Carbon::parse(
                            $r['tanggal_selesai'].' '.$r['jam_selesai']
                        )->addHours(2);

                        $bentrok = PeminjamanRuanganDetail::where(
                            'id_ruangan',
                            $r['id_ruangan']
                        )
                        ->where('id', '!=', $detail->id)
                        ->whereHas('peminjaman', function ($q) {
                            $q->where('decision_status', '!=', 'ditolak')
                            ->where('status', '!=', 'Selesai');
                        })
                        ->whereRaw("
                            TIMESTAMP(tanggal_mulai, jam_mulai) <= ?
                            AND
                            TIMESTAMP(tanggal_selesai, jam_selesai) >= ?
                        ", [
                            $selesai,
                            $mulai
                        ])
                        ->exists();

                        if ($bentrok) {
                            throw new \Exception('Ruangan bentrok jadwal.');
                        }

                        $detail->update([
                            'id_gedung'       => $r['id_gedung'],
                            'id_ruangan'      => $r['id_ruangan'],
                            'tanggal_mulai'   => $r['tanggal_mulai'],
                            'tanggal_selesai' => $r['tanggal_selesai'],
                            'jam_mulai'       => $r['jam_mulai'],
                            'jam_selesai'     => $r['jam_selesai'],
                            'catatan'         => $r['catatan'] ?? null,
                        ]);

                        PeminjamanRuanganAset::where(
                            'detail_id',
                            $detail->id
                        )->delete();

                        $excludeAsetIds = [];

                        $semuaAset = array_merge(
                            $r['aset'] ?? [],
                            $r['aset_baru'] ?? []
                        );

                        foreach ($semuaAset as $a) {

                            if (
                                empty($a['id_jenis_barang']) ||
                                empty($a['nama_aset']) ||
                                empty($a['jumlah'])
                            ) {
                                continue;
                            }

                            $jumlahDiminta = (int) $a['jumlah'];

                            $mulaiAset = Carbon::parse(
                                $r['tanggal_mulai'].' '.$r['jam_mulai']
                            );

                            $selesaiAset = Carbon::parse(
                                $r['tanggal_selesai'].' '.$r['jam_selesai']
                            );

                            $asetTersedia = $this->ambilAsetTersedia(
                                $a['id_jenis_barang'],
                                $mulaiAset,
                                $selesaiAset,
                                $excludeAsetIds
                            )
                            ->where('nama_aset', $a['nama_aset'])
                            ->limit($jumlahDiminta)
                            ->get();

                            if ($asetTersedia->count() < $jumlahDiminta) {

                                throw new \Exception(
                                    "{$a['nama_aset']} tidak tersedia sesuai jumlah yang diminta."
                                );
                            }

                            foreach ($asetTersedia as $asetUnit) {

                                $excludeAsetIds[] = $asetUnit->id_aset;

                                PeminjamanRuanganAset::create([
                                    'detail_id' => $detail->id,
                                    'id_aset'   => $asetUnit->id_aset,
                                    'jumlah'    => 1,
                                ]);
                            }
                        }

                    } else {

                        $detail = PeminjamanRuanganDetail::create([
                            'id_peminjaman'   => $header->id_peminjaman,
                            'id_gedung'       => $r['id_gedung'],
                            'id_ruangan'      => $r['id_ruangan'],
                            'tanggal_mulai'   => $r['tanggal_mulai'],
                            'tanggal_selesai' => $r['tanggal_selesai'],
                            'jam_mulai'       => $r['jam_mulai'],
                            'jam_selesai'     => $r['jam_selesai'],
                            'catatan'         => $r['catatan'] ?? null,
                        ]);

                        $excludeAsetIds = [];

                        foreach (($r['aset_baru'] ?? []) as $a) {

                            if (
                                empty($a['id_jenis_barang']) ||
                                empty($a['nama_aset']) ||
                                empty($a['jumlah'])
                            ) {
                                continue;
                            }

                            $jumlahDiminta = (int) $a['jumlah'];

                            $mulaiAset = Carbon::parse(
                                $r['tanggal_mulai'].' '.$r['jam_mulai']
                            );

                            $selesaiAset = Carbon::parse(
                                $r['tanggal_selesai'].' '.$r['jam_selesai']
                            );

                            $asetTersedia = $this->ambilAsetTersedia(
                                $a['id_jenis_barang'],
                                $mulaiAset,
                                $selesaiAset,
                                $excludeAsetIds
                            )
                            ->where('nama_aset', $a['nama_aset'])
                            ->limit($jumlahDiminta)
                            ->get();

                            if ($asetTersedia->count() < $jumlahDiminta) {

                                throw new \Exception(
                                    "{$a['nama_aset']} tidak tersedia sesuai jumlah yang diminta."
                                );
                            }

                            foreach ($asetTersedia as $asetUnit) {

                                $excludeAsetIds[] = $asetUnit->id_aset;

                                PeminjamanRuanganAset::create([
                                    'detail_id' => $detail->id,
                                    'id_aset'   => $asetUnit->id_aset,
                                    'jumlah'    => 1,
                                ]);
                            }
                        }
                    }
                }

                foreach ($request->konsumsi ?? [] as $k) {

                    $konsumsi = PeminjamanRuanganKonsumsi::find(
                        $k['id']
                    );

                    if (!$konsumsi) {
                        continue;
                    }

                    if (($k['deleted'] ?? 0) == 1) {

                        $konsumsi->delete();

                        continue;
                    }

                    $konsumsi->update([
                        'jenis_konsumsi' => $k['jenis_konsumsi'],
                        'jumlah'         => $k['jumlah'],
                        'catatan'        => $k['catatan'] ?? null,
                    ]);
                }

                foreach ($request->konsumsi_baru ?? [] as $k) {

                    if (
                        empty($k['jenis_konsumsi']) ||
                        empty($k['jumlah'])
                    ) {
                        continue;
                    }

                    PeminjamanRuanganKonsumsi::create([
                        'id_peminjaman' => $header->id_peminjaman,
                        'jenis_konsumsi' => $k['jenis_konsumsi'],
                        'jumlah' => $k['jumlah'],
                        'catatan' => $k['catatan'] ?? null,
                    ]);
                }

            });

            return redirect()
                ->route('peminjaman-ruangan.index')
                ->with(
                    'success',
                    'Peminjaman ruangan berhasil diperbarui.'
                );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function approve($id)
    {
        $data = PeminjamanRuangan::findOrFail($id);

        $data->update([
            'decision_status' => 'disetujui',
            'decided_by' => auth()->user()->id_user ?? null,
            'decided_at' => now(),
        ]);

        return back()->with('success', 'Disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000'
        ]);
        $data = PeminjamanRuangan::findOrFail($id);

        $data->update([
            'decision_status' => 'ditolak',
            'status' => 'Selesai',
            'decided_by' => auth()->user()->id_user ?? null,
            'decided_at' => now(),
        ]);

        return back()->with('success', 'Ditolak.');
    }

    public function process($id)
    {
        $data = PeminjamanRuangan::findOrFail($id);

        if ($data->decision_status !== 'disetujui') {
            abort(403, 'Belum disetujui.');
        }

        $data->update([
            'status' => 'Sedang Diproses'
        ]);

        return back()->with('success', 'Sedang diproses.');
    }

    public function tersedia($id)
    {
        $data = PeminjamanRuangan::findOrFail($id);

        if ($data->status !== 'Sedang Diproses') {
            abort(403);
        }
        
        DB::transaction(function () use ($id) {
            

            $data = PeminjamanRuangan::with([
                'details',
                'details.aset'
            ])->findOrFail($id);

            $data->update([
                'status' => 'Sudah Tersedia'
            ]);

            foreach ($data->details as $detail) {

                Ruangan::where(
                    'id_ruangan',
                    $detail->id_ruangan
                )->update([
                    'status' => 'terpakai'
                ]);

                foreach ($detail->aset as $aset) {

                    Aset::where(
                        'id_aset',
                        $aset->id_aset
                    )->update([
                        'status' => 'terpakai'
                    ]);
                }
            }
        });

        return back();
    }
    public function complete($id)
    {
        $data = PeminjamanRuangan::findOrFail($id);

        if ($data->status !== 'Sudah Tersedia') {
            abort(403);
        }
        DB::transaction(function () use ($id) {

            $data = PeminjamanRuangan::with([
                'details',
                'details.aset'
            ])->findOrFail($id);

            foreach ($data->details as $detail) {

                Ruangan::where(
                    'id_ruangan',
                    $detail->id_ruangan
                )->update([
                    'status' => 'tersedia'
                ]);

                foreach ($detail->aset as $aset) {

                    Aset::where(
                        'id_aset',
                        $aset->id_aset
                    )->update([
                        'status' => 'tersedia'
                    ]);
                }
            }

            $data->update([
                'status' => 'Selesai'
            ]);
        });

        return back();
    }
    public function destroy($id)
    {
        $data = PeminjamanRuangan::findOrFail($id);

        if ($data->status !== 'Belum Diproses') {
            abort(403, 'Tidak bisa dihapus.');
        }

        $data->delete();

        return back()->with('success', 'Data dihapus.');
    }

    private function buildRiwayatQuery(Request $request)
    {
        $query = PeminjamanRuangan::with([
            'divisi',
            'details.ruangan',
            'details.aset.aset',
            'konsumsi',
            'approver'
        ])
        ->where(function ($q) {
            $q->where(function ($x) {
                $x->where('status', 'Selesai')
                ->where('decision_status', 'disetujui');
            })
            ->orWhere('decision_status', 'ditolak');
        });

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return $query->latest();
    }

    public function exportPdfRiwayat(Request $request)
    {
        $data = $this->buildRiwayatQuery($request)->get();

        $totalSelesai = $data
            ->where('status', 'Selesai')
            ->where('decision_status', 'disetujui')
            ->count();

        $totalDitolak = $data
            ->where('decision_status', 'ditolak')
            ->count();

        $pdf = Pdf::loadView(
            'peminjaman_ruangan.pdf',
            [
                'data'          => $data,
                'totalSelesai'  => $totalSelesai,
                'totalDitolak'  => $totalDitolak,
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date,
            ]
        )->setPaper('A4', 'portrait');

        return $pdf->download('riwayat-peminjaman-ruangan.pdf');
    }

    public function exportExcelRiwayat(Request $request)
    {
        return Excel::download(
            new PeminjamanRuanganExport(
                $request->start_date,
                $request->end_date
            ),
            'riwayat-peminjaman-ruangan.xlsx'
        );
    }
}