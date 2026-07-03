<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\RuanganGambar;
use App\Models\Gedung;
use App\Models\Vendor;
use App\Models\MaintenanceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class RuanganController extends Controller
{
    private function generateId()
    {
        $latest = Ruangan::selectRaw("CAST(SUBSTRING(id_ruangan, 2) AS UNSIGNED) AS num")
            ->orderByDesc('num')
            ->first();

        if (!$latest) {
            return 'R0001';
        }

        $num = $latest->num + 1;
        return 'R' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function index($id_gedung = null)
    {
        if ($id_gedung) {
            $ruangan = Ruangan::with('gambar')
                ->where('id_gedung', $id_gedung)
                ->get();

            $gedung = Gedung::find($id_gedung);
        } else {
            $ruangan = Ruangan::with('gambar')->get();
            $gedung = null;
        }
        return view('ruangan.index', compact('ruangan', 'gedung'));
    }


    public function getByGedung($id)
    {
        try {
            $ruangan = Ruangan::where('id_gedung', $id)
                ->select('id_ruangan', 'nama_ruangan')
                ->get();

            return response()->json($ruangan);
        } catch (\Exception $e) {
            // supaya kalau ada error, langsung tahu sebabnya di console browser
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function byGedung($id)
    {
        $gedung = Gedung::findOrFail($id);
        $ruangan = Ruangan::with('gambar')
            ->where('id_gedung', $id)
            ->get();
        return view('ruangan.byGedung', compact('ruangan', 'gedung'));
    }

    public function show($id)
    {
        $ruangan = Ruangan::with([
            'gedung',
            'aset',
            'maintenance',
            'gambar'
        ])->findOrFail($id);

        $totalAset = $ruangan->aset->count() ?? 0;
        $totalBiayaMaintenance = MaintenanceDetail::whereHas('aset', function ($query) use ($id) {
            $query->where('id_ruangan', $id);
        })->sum('biaya');

        return view('ruangan.dashboard', [
            'ruangan' => $ruangan,
            'totalAset' => $totalAset,
            'totalBiayaMaintenance' => $totalBiayaMaintenance,
            'gedung' => Gedung::all(),
            'vendors' => Vendor::all()
        ]);
    }

    public function dashboard($id)
    {
        $ruangan = Ruangan::with([
            'gedung',
            'aset' => function ($query) {
                $query->whereNotIn('keterangan_kelayakan', [
                    'Lelang',
                    'Hibahkan',
                    'Dijual',
                    'Dimusnahkan'
                ]);
            },
            'gambar',
            'maintenance'
        ])->findOrFail($id);

        $totalAset = $ruangan->aset->where('jenisBarang.jenis', 'sarana')->count();
        $totalBiayaMaintenance = $ruangan->maintenance->sum('biaya');

        $vendors = Vendor::all();
        $gedungs = Gedung::all();
        $ruangans = Ruangan::with('gedung')->get();

        return view('ruangan.dashboard', compact(
            'ruangan',
            'totalAset',
            'totalBiayaMaintenance',
            'vendors',
            'gedungs',
            'ruangans'
        ));
    }

    public function create(Request $request)
    {
        $idGedung = $request->query('gedung');

        $gedung = Gedung::all();
        $selectedGedung = $idGedung ? Gedung::find($idGedung) : null;

        return view('ruangan.create', compact('gedung', 'selectedGedung', 'idGedung'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_gedung' => 'required',
            'nama_ruangan' => 'required',
            'kategori' => 'required|in:interior,eksterior',
            'lantai' => 'nullable|string|max:20',
            'status' => 'required|in:tersedia,terpakai,non aktif,maintenance',
            'foto.*' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $newId = $this->generateId();

        Ruangan::create([
            'id_ruangan' => $newId,
            'id_gedung' => $request->id_gedung,
            'kategori' => $request->kategori,
            'lantai' => $request->lantai,
            'nama_ruangan' => $request->nama_ruangan,
            'status' => $request->status,
        ]);

        if ($request->hasFile('foto')) {

            $no = 1;

            foreach ($request->file('foto') as $file) {

                $manager = new ImageManager(new Driver());

                $image = $manager->read($file)
                    ->scaleDown(width: 1600)
                    ->toJpeg(60);

                $filename = $no . '.jpg';

                $path = "ruangan/{$newId}/{$filename}";

                Storage::disk('public')->put($path, $image);

                RuanganGambar::create([
                    'id_ruangan' => $newId,
                    'foto' => $path,
                ]);

                $no++;
            }
        }

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::with('gambar')->findOrFail($id);
        $gedung = Gedung::all();
        return view('ruangan.edit', compact('ruangan', 'gedung'));
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::with('gambar')->findOrFail($id);

        $request->validate([
            'id_gedung' => 'required',
            'nama_ruangan' => 'required',
            'kategori' => 'required|in:interior,eksterior',
            'lantai' => 'nullable|string|max:20',
            'status' => 'required|in:tersedia,terpakai,non aktif,maintenance',
            'foto.*' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        if ($request->filled('hapus_foto')) {

            $fotoDihapus = RuanganGambar::whereIn(
                'id',
                $request->hapus_foto
            )->get();

            foreach ($fotoDihapus as $foto) {

                if (
                    $foto->foto &&
                    !str_contains($foto->foto, 'default-room.jpg') &&
                    Storage::disk('public')->exists($foto->foto)
                ) {
                    Storage::disk('public')->delete($foto->foto);
                }

                $foto->delete();
            }
        }

        $totalFotoSaatIni = $ruangan->gambar()
            ->whereNotIn('id', $request->hapus_foto ?? [])
            ->count();

        if ($totalFotoSaatIni <= 0 && !$request->hasFile('foto')) {
            return back()->with(
                'error',
                'Minimal harus ada 1 foto ruangan.'
            );
        }

        // Tambah foto baru (tidak menghapus foto lama)
        if ($request->hasFile('foto')) {

            foreach ($request->file('foto') as $file) {

                $manager = new ImageManager(new Driver());

                $image = $manager->read($file)
                    ->scaleDown(width: 1600)
                    ->toJpeg(60);

                $filename =
                    time() . '_' .
                    uniqid() .
                    '.jpg';

                $path = "ruangan/{$ruangan->id_ruangan}/{$filename}";

                Storage::disk('public')->put($path, $image);

                RuanganGambar::create([
                    'id_ruangan' => $ruangan->id_ruangan,
                    'foto' => $path,
                ]);
            }
        }

        $ruangan->update([
            'id_gedung' => $request->id_gedung,
            'kategori' => $request->kategori,
            'lantai' => $request->lantai,
            'nama_ruangan' => $request->nama_ruangan,
            'status' => $request->status,
        ]);

        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::with('gambar')->findOrFail($id);

        if ($ruangan->aset()->exists()) {
            return back()->with('error', 'Ruangan tidak dapat dihapus karena masih memiliki aset.');
        }

        if ($ruangan->maintenance()->exists()) {
            return back()->with('error', 'Ruangan tidak dapat dihapus karena ada riwayat maintenance.');
        }

        foreach ($ruangan->gambar as $gambar) {

            if (
                $gambar->foto &&
                !str_contains($gambar->foto, 'default-room.jpg') &&
                Storage::disk('public')->exists($gambar->foto)
            ) {
                Storage::disk('public')->delete($gambar->foto);
            }

        }

        Storage::disk('public')->deleteDirectory(
            'ruangan/' . $ruangan->id_ruangan
        );

        $ruangan->delete();

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus!');
    }

}

