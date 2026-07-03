<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\DB;
use App\Exports\AsetExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\AsetLogService;
use App\Models\Aset;
use App\Models\Ruangan;
use App\Models\Gedung;
use App\Models\JenisBarang;
use App\Models\Maintenance;
use App\Models\LaporanPemusnahan;
use App\Models\MaintenanceDetail;


class AsetController extends Controller
{
    private function generateKodeAset(JenisBarang $barang, $tahun): string
    {
        $prefix = strtoupper($barang->prefix_kode);
        $jenis = strtoupper($barang->jenis);
        $kategori = strtoupper(str_replace(' ', '_', $barang->kategori));

        $last = Aset::whereHas('jenisBarang', function ($q) use ($barang) {
                $q->where('id_jenis_barang', $barang->id_jenis_barang);
            })
            ->where('tahun_perolehan', $tahun)
            ->orderByDesc('kode_aset')
            ->first();

        $nomor = 1;

        if ($last) {
            $parts = explode('/', $last->kode_aset);

            $kodePrefix = $parts[3] ?? '';

            preg_match('/(\d+)$/', $kodePrefix, $match);

            if (isset($match[1])) {
                $nomor = ((int) $match[1]) + 1;
            }
        }

        return sprintf(
            'ASET/%s/%s/%s%s/%s',
            $jenis,
            $kategori,
            $prefix,
            str_pad($nomor, 3, '0', STR_PAD_LEFT),
            $tahun
        );
    }

    private function generateId()
    {
        $latest = Aset::selectRaw("CAST(SUBSTRING(id_aset, 2) AS UNSIGNED) AS num")
            ->lockForUpdate()
            ->orderByDesc('num')
            ->first();

        if (!$latest) {
            return 'A0001';
        }

        $num = $latest->num + 1;
        return 'A' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    // ID Pemusnahan — format PMN-YYYYMMDD-NNNN, konsisten dengan LaporanPemusnahanController
    private function generatePemusnahanId(): string
    {
        $tanggal = now()->format('Ymd');

        $last = \App\Models\LaporanPemusnahan::where('id_pemusnahan', 'like', "PMN-$tanggal-%")
            ->lockForUpdate()
            ->orderBy('id_pemusnahan', 'desc')
            ->first();

        $next = $last ? (int) substr($last->id_pemusnahan, -4) + 1 : 1;

        return "PMN-$tanggal-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // ID Maintenance — format MNT-YYYYMMDD-NNNN, konsisten dengan MaintenanceController
    private function generateMaintenanceId()
    {
        $tgl = now()->format('Ymd');

        $last = Maintenance::where('id_maintenance', 'like', "MNT-$tgl-%")
            ->lockForUpdate()
            ->orderByDesc('id_maintenance')
            ->first();

        $next = $last ? (int) substr($last->id_maintenance, -4) + 1 : 1;

        return "MNT-$tgl-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function uploadMaintenanceBefore($file, $id_maintenance)
    {
        if (!$file) {
            return null;
        }

        $manager = new ImageManager(driver: new Driver());
        $image = $manager->read($file)
            ->scaleDown(width: 1600)
            ->toJpeg(60);

        $filename = 'before_' . $id_maintenance . '.jpg';

        Storage::disk('public')->put('maintenance/before/' . $filename, $image);

        return 'maintenance/before/' . $filename;

    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $id_gedung = request('id_gedung');
        $id_ruangan = request('id_ruangan');
        $jenis = request('jenis');

        $makeQuery = fn() => Aset::query()
            ->when($id_gedung, fn($q) => $q->where('id_gedung', $id_gedung))
            ->when($id_ruangan, fn($q) => $q->where('id_ruangan', $id_ruangan))
            ->when($jenis, fn($q) => $q->whereHas('jenisBarang', fn($q2) => $q2->where('jenis', $jenis)));

        $data = $makeQuery()->with(['gedung', 'ruangan', 'jenisBarang'])->paginate($perPage);

        $stats = [
            'total'      => $makeQuery()->count(),
            'layak'      => $makeQuery()->whereIn('kelayakan', [1, 2])->count(),
            'pemantauan' => $makeQuery()->where('kelayakan', 3)->count(),
            'perbaikan'  => $makeQuery()->where('kelayakan', 4)->count(),
        ];

        $gedung = $id_gedung ? Gedung::find($id_gedung) : null;

        return view('aset.index', compact('data', 'stats', 'gedung', 'perPage'));
    }

    public function getByRuangan($id)
    {
        return response()->json(
            Aset::where('id_ruangan', $id)
                ->select(
                    'id_aset',
                    'nama_aset',
                    'kode_aset'
                )
                ->get()
        );
    }

    public function getJenisBarang($kategori)
    {
        try {
            $data = JenisBarang::where('kategori', $kategori)
                ->select('id_jenis_barang', 'nama_barang', 'prefix_kode')
                ->orderBy('nama_barang')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $gedung = Gedung::all();
        $selectedGedung = null;
        $selectedRuangan = null;

        if ($request->id_gedung) {
            $selectedGedung = Gedung::find($request->id_gedung);
        }

        if ($request->id_ruangan) {
            $selectedRuangan = Ruangan::find($request->id_ruangan);

            if ($selectedRuangan) {
                $selectedGedung = Gedung::find($selectedRuangan->id_gedung);
            }
        }


        return view('aset.create', compact(
            'gedung',
            'selectedGedung',
            'selectedRuangan'
        ));
    }

    public function store(Request $request)
    {
        if ($request->nilai === "") {
            $request->merge(['nilai' => null]);
        }

        $request->validate([
            'nama_aset' => 'required|string|max:255',
            'merk' => 'nullable|string|max:100',
            'tipe_model' => 'nullable|string|max:100',
            'spesifikasi' => 'nullable|string',
            'kode_aset' => 'nullable|string|max:50|unique:aset,kode_aset',

            'barang' => 'required|exists:jenis_barang,id_jenis_barang',

            'status' => 'required|in:tersedia,terpakai,non aktif,maintenance',

            'id_gedung' => 'required|exists:gedung,id_gedung',
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
            'tahun_perolehan' => 'nullable|integer|min:1900|max:' . date('Y'),
            'nilai' => 'nullable|numeric|min:0',
            'kelayakan' => 'required|integer|between:1,5',
            'keterangan_kelayakan' => 'required|string|max:50',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png',

            'foto_before' => $request->kelayakan == 4 
                ? 'required|image|mimes:jpg,jpeg,png'
                : 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $barang = JenisBarang::findOrFail($request->barang);

        if (!$barang) {
            return back()->withErrors(['barang' => 'Jenis barang tidak ditemukan'])->withInput();
        }

        DB::transaction(function() use ($request, $barang) {
            $id_aset = $this->generateId();

            $kode_aset = $this->generateKodeAset(
                $barang,
                $request->tahun_perolehan
            );

            $fotoPath = 'aset/default.jpg';

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');

                $manager = new ImageManager(driver: new Driver());
                $image = $manager->read($file)
                    ->scaleDown(width: 1600)
                    ->toJpeg(60);

                $filename = $id_aset . '.jpg';
                Storage::disk('public')->put('aset/' . $filename, $image);

                $fotoPath = 'aset/' . $filename;
            }

            $fotoBefore = $request->file('foto_before');

            $aset = Aset::create([
                'id_aset' => $id_aset,
                'kode_aset' => $kode_aset,
                'nama_aset' => $request->nama_aset,
                'merk' => $request->merk,
                'tipe_model' => $request->tipe_model,
                'spesifikasi' => $request->spesifikasi,
                'id_jenis_barang' => $barang->id_jenis_barang,
                'status' => $request->status,
                'id_gedung' => $request->id_gedung,
                'id_ruangan' => $request->id_ruangan,
                'tahun_perolehan' => $request->tahun_perolehan,
                'nilai' => $request->nilai,
                'kelayakan' => $request->kelayakan,
                'keterangan_kelayakan' => $request->keterangan_kelayakan,
                'foto' => $fotoPath,
            ]);

            AsetLogService::log(
                $aset->id_aset,
                'create',
                null,
                'Aset dibuat dan didaftarkan'
            );

            if ($aset->kelayakan == 4) {
                $id_maintenance = $this->generateMaintenanceId();

                Maintenance::create([
                    'id_maintenance' => $id_maintenance,
                    'id_aset' => $aset->id_aset,
                    'id_ruangan' => $aset->id_ruangan,
                    'id_gedung' => $aset->id_gedung,
                    'tanggal_laporan' => now(),
                    'kerusakan' => $aset->keterangan_kelayakan,
                    'biaya' => 0,
                    'foto_before' => $this->uploadMaintenanceBefore($fotoBefore, $id_maintenance),
                    'catatan' => 'Otomatis dari aset kelayakan 4',
                    'status' => 'Perlu Perbaikan',
                ]);

                $aset->update([
                    'status' => 'maintenance'
                ]);
            }

            if ($aset->kelayakan == 5) {
                \App\Models\LaporanPemusnahan::create([
                    'id_pemusnahan' => $this->generatePemusnahanId(),
                    'id_aset' => $aset->id_aset,
                    'tanggal_pemusnahan' => now(),
                    'metode' => $aset->keterangan_kelayakan,
                    'catatan' => 'Aset otomatis masuk pemusnahan karena kelayakan 5',
                ]);

                $aset->update([
                    'status' => 'non aktif'
                ]);
            }
        });

        return back()->with('success', 'Aset berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $aset = Aset::with(['gedung', 'ruangan'])->findOrFail($id);
        $gedung = Gedung::all();
        $ruangan = Ruangan::where('id_gedung', $aset->id_gedung)->get();

        return view('aset.edit', compact('aset', 'gedung', 'ruangan'));
    }

    public function update(Request $request, $id)
    {
        $aset = Aset::findOrFail($id);

        $request->validate([
            'nama_aset' => 'required|string|max:255',
            'merk' => 'nullable|string|max:100',
            'tipe_model' => 'nullable|string|max:100',
            'spesifikasi' => 'nullable|string',
            'kode_aset' => 'nullable|string|max:50|unique:aset,kode_aset,' . $id . ',id_aset',

            'barang' => 'required|exists:jenis_barang,id_jenis_barang',

            'status' => 'required|in:tersedia,terpakai,non aktif,maintenance',

            'tahun_perolehan' => 'nullable|integer|min:1900|max:' . date('Y'),
            'nilai' => 'nullable|numeric|min:0',
            'kelayakan' => 'required|integer|between:1,5',
            'keterangan_kelayakan' => 'required|string|max:50',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);


        $path = $aset->foto;

        if ($request->hasFile('foto')) {

            if (
                $aset->foto &&
                $aset->foto !== 'aset/default.jpg' &&
                Storage::disk('public')->exists($aset->foto)
            ) {
                Storage::disk('public')->delete($aset->foto);
            }

            $file = $request->file('foto');

            $manager = new ImageManager(driver: new Driver());
            $image = $manager->read($file)
                ->scaleDown(width: 1600)
                ->toJpeg(60);

            $filename = $aset->id_aset . '.jpg';
            Storage::disk('public')->put('aset/' . $filename, $image);

            $path = 'aset/' . $filename;
        }

        $barang = JenisBarang::find($request->barang);

        if (!$barang) {
            return back()->withErrors(['barang' => 'Jenis barang tidak ditemukan'])->withInput();
        }

        $oldKode = $aset->kode_aset;
        $oldJenisBarang = $aset->id_jenis_barang;

        $kodeBaru = $oldKode;

        if ($oldJenisBarang !== $barang->id_jenis_barang) {
            $kodeBaru = $this->generateKodeAset(
                $barang,
                $request->tahun_perolehan
            );
        }

        if (
            $aset->maintenance()->exists() &&
            $oldJenisBarang !== $barang->id_jenis_barang
        ) {
            return back()->withErrors([
                'barang' => 'Jenis barang tidak dapat diubah karena aset sudah memiliki riwayat maintenance'
            ]);
        }

        $aset->update([
            'kode_aset' => $kodeBaru,
            'nama_aset' => $request->nama_aset,
            'merk' => $request->merk,
            'tipe_model' => $request->tipe_model,
            'spesifikasi' => $request->spesifikasi,
            'id_jenis_barang' => $barang->id_jenis_barang,
            'status' => $request->status,
            'tahun_perolehan' => $request->tahun_perolehan,
            'nilai' => $request->nilai,
            'kelayakan' => $request->kelayakan,
            'keterangan_kelayakan' => $request->keterangan_kelayakan,
            'foto' => $path,
        ]);

        if ($oldKode !== $kodeBaru) {
            AsetLogService::log(
                $aset->id_aset,
                'kode_aset_update',
                null,
                "Kode aset diubah dari {$oldKode} menjadi {$kodeBaru}"
            );
        }

        AsetLogService::log(
            $aset->id_aset,
            'update',
            null,
            'Perubahan data aset'
        );

        if ($aset->kelayakan == 4) {
            $exists = MaintenanceDetail::where('id_aset', $aset->id_aset)
                ->where('status', '!=', 'Selesai')
                ->exists();

            if (!$exists) {
               $id_maintenance = $this->generateMaintenanceId();

                Maintenance::create([
                    'id_maintenance' => $id_maintenance,
                    'id_aset' => $aset->id_aset,
                    'id_ruangan' => $aset->id_ruangan,
                    'id_gedung' => $aset->id_gedung,
                    'tanggal_laporan' => now(),
                    'tanggal_mulai' => now(),
                    'tanggal_selesai' => null,
                    'durasi_jam' => 0,
                    'status' => 'Perlu Perbaikan',
                    'kerusakan' => 'Perlu perbaikan setelah update status',
                    'biaya' => 0,
                    'foto_before' => $aset->foto ?? '',
                    'foto_after' => null,
                    'catatan' => 'Otomatis dari perubahan status aset menjadi perlu perbaikan',
                ]);
                $aset->update([
                    'status' => 'maintenance'
                ]);
            }
        }

        elseif ($aset->kelayakan == 1) {
            $detail = MaintenanceDetail::where('id_aset', $aset->id_aset)
                ->whereNull('tanggal_selesai')
                ->latest('tanggal_mulai')
                ->first();

            if ($detail) {
                $detail->update([
                    'status' => 'Selesai',
                    'tanggal_selesai' => now(),
                    'durasi_jam' => now()->diffInHours($detail->tanggal_mulai ?? now()),
                    'catatan' => 'Perbaikan selesai (update otomatis dari status aset)',
                ]);
            }
        }

        return redirect()->to(url()->previous())->with('success', 'Data aset berhasil diperbarui!');
    }

    public function byGedung($id)
    {
        $gedung = Gedung::findOrFail($id);
        $aset = Aset::with('ruangan')->where('id_gedung', $id)->get();
        return view('aset.byGedung', compact('gedung', 'aset'));
    }

    public function show($id)
    {
        $aset = Aset::with([
            'gedung',
            'ruangan',
            'maintenance',
            'laporanPemusnahan',
            'logs' => function ($q) {
                $q->orderBy('tanggal_kejadian', 'desc');
            }
        ])->findOrFail($id);

        $totalBiayaMaintenance = $aset->maintenance->sum('biaya');

        return view('aset.show', compact(
            'aset',
            'totalBiayaMaintenance'
        ));
    }

    public function destroy($id)
    {
        $aset = Aset::findOrFail($id);

        if (
            $aset->foto &&
            $aset->foto !== 'aset/default.jpg' &&
            Storage::disk('public')->exists($aset->foto)
        ) {
            Storage::disk('public')->delete($aset->foto);
        }

        $aset->delete();

        return back()->with('success', 'Aset berhasil dihapus!');
    }

    public function check($id)
    {
        $aset = Aset::findOrFail($id);

        $user = auth()->user();

        $aset->update([
            'last_checked_at' => now(),
            'last_checked_by' => $user->id_user
        ]);

        AsetLogService::log(
            $aset->id_aset,
            'check',
            null,
            'Aset dicek'
        );

        return back()->with('success', 'Aset berhasil dicek!');
    }

    public function export()
    {
        return Excel::download(
            new AsetExport(),
            'aset-export.xlsx'
        );
    }

}
