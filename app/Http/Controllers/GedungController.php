<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\GedungGambar;
use App\Models\GedungDenah;
use Illuminate\Http\Request;
use App\Models\MaintenanceDetail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class GedungController extends Controller
{
    private function generateId()
    {
        $latest = Gedung::selectRaw("CAST(SUBSTRING(id_gedung, 2) AS UNSIGNED) AS num")
            ->orderByDesc('num')
            ->first();

        if (!$latest) {
            return 'G0001';
        }

        $num = $latest->num + 1;

        return 'G' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }


    /**
     * Tampilkan semua data gedung
     */
    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $gedung = Gedung::with('gambar')
            ->withCount([
                'ruangan',
                'ruangan as ruangan_tersedia' => function ($q) {
                    $q->where('status', 'tersedia');
                },
                'ruangan as ruangan_terpakai' => function ($q) {
                    $q->where('status', 'terpakai');
                },
                'ruangan as ruangan_maintenance' => function ($q) {
                    $q->where('status', 'maintenance');
                },
            ])
            ->paginate($perPage);

        return view('gedung.index', compact('gedung', 'perPage'));
    }


    /**
     * Form untuk tambah data gedung baru
     */
    public function create()
    {
        $statusList = Gedung::getEnumStatus();
        return view('gedung.create', compact('statusList'));
    }

    /**
     * Simpan data gedung baru ke database
     */
    public function store(Request $request)
    {
        $validValues = Gedung::getEnumStatus();

        $request->validate([
            'nama_gedung' => 'required|string|max:100',
            'status'      => 'required|in:' . implode(',', $validValues),

            'gambar'      => 'nullable|array',
            'gambar.*'    => 'image|mimes:jpg,jpeg,png',

            'file_denah'      => 'nullable|array',
            'file_denah.*'    => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $newId = $this->generateId();

        $gedung = Gedung::create([
            'id_gedung'   => $newId,
            'nama_gedung' => $request->nama_gedung,
            'status'      => $request->status,
        ]);

        $manager = new ImageManager(driver: new Driver());

        /**
         * SIMPAN GAMBAR
         */
        if ($request->hasFile('gambar')) {

            foreach ($request->file('gambar') as $index => $file) {

                $image = $manager->read($file)
                    ->scaleDown(width: 1600)
                    ->toJpeg(60);

                $filename = time() . '_' . $index . '.jpg';

                $path = "gedung/gambar/{$newId}/{$filename}";

                Storage::disk('public')->put($path, $image);

                GedungGambar::create([
                    'id_gedung' => $newId,
                    'gambar'    => $path,
                ]);
            }
        }

        /**
         * SIMPAN DENAH
         */
        if ($request->hasFile('file_denah')) {

            foreach ($request->file('file_denah') as $index => $file) {

                $extension = $file->getClientOriginalExtension();

                $filename = time() . '_denah_' . $index . '.' . $extension;

                $path = "gedung/denah/{$newId}/{$filename}";

                Storage::disk('public')->putFileAs(
                    "gedung/denah/{$newId}",
                    $file,
                    $filename
                );

                GedungDenah::create([
                    'id_gedung'  => $newId,
                    'file_denah' => $path,
                ]);
            }
        }

        return back()->with(
            'success',
            'Data gedung berhasil ditambahkan!'
        );
    }


    /**
     * Menampilkan detail satu gedung
     */
    public function show($id)
    {
        $gedung = Gedung::findOrFail($id);
        return view('gedung.show', compact('gedung'));
    }

    public function dashboard($id)
    {
        $gedung = Gedung::with([
            'gambar',
            'denah',
            'ruangan.aset'
        ])->findOrFail($id);

        // Hitung data ruangan
        $totalRuangan = $gedung->ruangan->count();
        $tersedia = $gedung->ruangan->where('status', 'tersedia')->count();
        $terpakai = $gedung->ruangan->where('status', 'terpakai')->count();
        $maintenance = $gedung->ruangan->where('status', 'maintenance')->count();

        // Hitung data aset
        $totalAset = $gedung->ruangan->flatMap->aset->where('jenisBarang.jenis', 'sarana')->count();
        $totalBiayaMaintenance = MaintenanceDetail::whereHas('aset.ruangan', function ($query) use ($id) {
            $query->where('id_gedung', $id);
        })->sum('biaya');

        return view('gedung.dashboard', compact(
            'gedung',
            'totalRuangan',
            'totalAset',
            'totalBiayaMaintenance',
            'tersedia',
            'terpakai',
            'maintenance'
        ));
    }

    /**
     * Form edit data gedung
     */
    public function edit($id)
    {
        $gedung = Gedung::with([
            'gambar',
            'denah'
        ])->findOrFail($id);

        $statusList = Gedung::getEnumStatus();

        return view(
            'gedung.edit',
            compact('gedung', 'statusList')
        );
    }


    /**
     * Update data gedung
     */
    public function update(Request $request, $id)
    {
        $validValues = Gedung::getEnumStatus();

        $request->validate([
            'nama_gedung' => 'required|string|max:100',
            'status'      => 'required|in:' . implode(',', $validValues),

            'gambar'      => 'nullable|array',
            'gambar.*'    => 'image|mimes:jpg,jpeg,png',

            'file_denah'      => 'nullable|array',
            'file_denah.*'    => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $gedung = Gedung::findOrFail($id);

        /**
         * HAPUS GAMBAR YANG DIPILIH
         */
        if ($request->filled('hapus_gambar')) {

            $gambarDihapus = GedungGambar::whereIn(
                'id',
                $request->hapus_gambar
            )->get();

            foreach ($gambarDihapus as $gambar) {

                if (
                    str_starts_with($gambar->gambar, 'gedung/') &&
                    Storage::disk('public')->exists($gambar->gambar)
                ) {
                    Storage::disk('public')->delete($gambar->gambar);
                }

                $gambar->delete();
            }
        }

        /**
         * HAPUS DENAH YANG DIPILIH
         */
        if ($request->filled('hapus_denah')) {

            $denahDihapus = GedungDenah::whereIn(
                'id',
                $request->hapus_denah
            )->get();

            foreach ($denahDihapus as $denah) {

                if (
                    str_starts_with($denah->file_denah, 'gedung/') &&
                    Storage::disk('public')->exists($denah->file_denah)
                ) {
                    Storage::disk('public')->delete($denah->file_denah);
                }

                $denah->delete();
            }
        }

        /**
         * OPTIONAL:
         * CEK MINIMAL 1 GAMBAR
         */
        $sisaGambar = $gedung->gambar()
            ->whereNotIn('id', $request->hapus_gambar ?? [])
            ->count();

        if ($sisaGambar <= 0 && !$request->hasFile('gambar')) {
            return back()->with(
                'error',
                'Minimal harus ada 1 gambar gedung.'
            );
        }

        /**
         * OPTIONAL:
         * CEK MINIMAL 1 DENAH
         */
        $sisaDenah = $gedung->denah()
            ->whereNotIn('id', $request->hapus_denah ?? [])
            ->count();

        if ($sisaDenah <= 0 && !$request->hasFile('file_denah')) {
            return back()->with(
                'error',
                'Minimal harus ada 1 file denah.'
            );
        }

        $gedung->update([
            'nama_gedung' => $request->nama_gedung,
            'status'      => $request->status,
        ]);

        $manager = new ImageManager(driver: new Driver());

        /**
         * TAMBAH GAMBAR BARU
         */
        if ($request->hasFile('gambar')) {

            foreach ($request->file('gambar') as $file) {

                $image = $manager->read($file)
                    ->scaleDown(width: 1600)
                    ->toJpeg(60);

                $filename = time() . '_' . uniqid() . '.jpg';

                $path = "gedung/gambar/{$id}/{$filename}";

                Storage::disk('public')->put($path, $image);

                GedungGambar::create([
                    'id_gedung' => $id,
                    'gambar' => $path,
                ]);
            }
        }

        /**
         * TAMBAH DENAH BARU
         */
        if ($request->hasFile('file_denah')) {

            foreach ($request->file('file_denah') as $file) {

                $extension = $file->getClientOriginalExtension();

                $filename = time() . '_denah_' . uniqid() . '.' . $extension;

                Storage::disk('public')->putFileAs(
                    "gedung/denah/{$id}",
                    $file,
                    $filename
                );

                GedungDenah::create([
                    'id_gedung' => $id,
                    'file_denah' => "gedung/denah/{$id}/{$filename}",
                ]);
            }
        }

        return back()->with(
            'success',
            'Data gedung berhasil diperbarui!'
        );
    }

    /**
     * Hapus data gedung
     */
    public function destroy($id)
    {
        $gedung = Gedung::with([
            'gambar',
            'denah'
        ])->findOrFail($id);

        foreach ($gedung->gambar as $gambar) {

            if (
                str_starts_with($gambar->gambar, 'gedung/') &&
                Storage::disk('public')->exists($gambar->gambar)
            ) {
                Storage::disk('public')->delete($gambar->gambar);
            }
        }

        foreach ($gedung->denah as $denah) {

            if (
                str_starts_with($denah->file_denah, 'gedung/') &&
                Storage::disk('public')->exists($denah->file_denah)
            ) {
                Storage::disk('public')->delete($denah->file_denah);
            }
        }

        Storage::disk('public')->deleteDirectory(
            "gedung/gambar/{$id}"
        );

        Storage::disk('public')->deleteDirectory(
            "gedung/denah/{$id}"
        );

        $gedung->delete();

        return back()->with(
            'success',
            'Data gedung berhasil dihapus!'
        );
    }

}
