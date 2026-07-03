<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\AparLog;

class AparController extends Controller
{
    private function logApar(
        string $id_apar,
        string $tipe,
        ?string $refId,
        string $keterangan
    ) {
        AparLog::create([
            'id_apar' => $id_apar,
            'tipe' => $tipe,
            'ref_id' => $refId,
            'keterangan' => $keterangan,
            'tanggal_kejadian' => now(),
            'id_user' => auth()->user()->id_user,
        ]);
    }

    private function generateId()
    {
        $latest = Apar::selectRaw("CAST(SUBSTRING(id_apar, 3) AS UNSIGNED) AS num")
            ->lockForUpdate()
            ->orderByDesc('num')
            ->first();

        if (!$latest) {
            return 'AP0001';
        }

        $num = $latest->num + 1;
        return 'AP' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function show($id)
    {
        $apar = Apar::with([
            'gedung',
            'ruangan',
            'checker',
            'userPemakai',
            'logs' => function ($q) {
                $q->with('user')
                ->orderBy('tanggal_kejadian', 'desc');
            }
        ])->findOrFail($id);

        return view('apar.show', compact('apar'));
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $apar = Apar::with(['gedung', 'ruangan'])->paginate($perPage);

        $stats = [
            'total'      => Apar::count(),
            'refill'     => Apar::where('jenis', 'refill')->count(),
            'sekali_pakai'=> Apar::where('jenis', 'sekali pakai')->count(),
            'expired'    => Apar::whereNotNull('expired_date')->where('expired_date', '<', now())->count(),
        ];

        return view('apar.index', compact('apar', 'stats', 'perPage'));
    }

    public function create()
    {
        $gedung = Gedung::all();
        $newId = $this->generateId();

        return view('apar.create', compact('gedung', 'newId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_gedung' => 'required|exists:gedung,id_gedung',
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
            'expired_date' => 'nullable|date',
            'jenis' => 'nullable|in:refill,sekali pakai,hydran',
            'tanggal_refill' => $request->jenis === 'refill'
                ? 'required|date'
                : 'nullable',
            'ukuran' => 'required|string|max:20',
            'merk' => 'nullable|string|max:100',
            'media_isi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png',
            'lokasi' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $validated) {
            $newId = $this->generateId();
            $fotoPath = 'apar/default.jpg';

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');

                $manager = new ImageManager(driver: new Driver());
                $image = $manager->read($file)
                    ->scaleDown(width: 1600)
                    ->toJpeg(60);

                $filename = $newId . '.jpg';
                Storage::disk('public')->put('apar/' . $filename, $image);
                $fotoPath = 'apar/' . $filename;
            }

            Apar::create([
                'id_apar' => $newId,
                'id_gedung' => $validated['id_gedung'],
                'id_ruangan' => $validated['id_ruangan'],
                'expired_date' => $validated['expired_date'] ?? null,
                'jenis' => $validated['jenis'],
                'tanggal_refill' => $validated['tanggal_refill'] ?? null,
                'ukuran' => $validated['ukuran'],
                'merk' => $validated['merk'] ?? null,
                'media_isi' => $validated['media_isi'] ?? null,                'keterangan' => $validated['keterangan'] ?? null,
                'foto' => $fotoPath,
                'lokasi' => $validated['lokasi'] ?? null,
            ]);
        });

        return redirect()->route('apar.index')->with('success', 'APAR berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $apar = Apar::findOrFail($id);
        $gedung = Gedung::all();
        $ruangan = Ruangan::where('id_gedung', $apar->id_gedung)->get();

        return view('apar.edit', compact('apar', 'gedung', 'ruangan'));
    }

    public function update(Request $request, $id)
    {
        $apar = Apar::findOrFail($id);

        $validated = $request->validate([
            'id_gedung' => 'required|exists:gedung,id_gedung',
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
            'expired_date' => 'nullable|date',
            'jenis' => 'nullable|in:refill,sekali pakai,hydran',
            'tanggal_refill' => $request->jenis === 'refill'
                ? 'required|date'
                : 'nullable',
            'ukuran' => 'required|string|max:20',
            'merk' => 'nullable|string|max:100',
            'media_isi' => 'nullable|string|max:100',            'keterangan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png',
            'lokasi' => 'nullable|string|max:255',
        ]);

        $fotoPath = $apar->foto;

        if ($request->hasFile('foto')) {
            if ($fotoPath !== 'apar/default.jpg' && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }

            $file = $request->file('foto');

            $manager = new ImageManager(driver: new Driver());
            $image = $manager->read($file)
                ->scaleDown(width: 1600)
                ->toJpeg(60);

            $filename = $apar->id_apar . '.jpg';
            Storage::disk('public')->put('apar/' . $filename, $image);
            $fotoPath = 'apar/' . $filename;
        }

        $apar->update([
            'id_gedung' => $validated['id_gedung'],
            'id_ruangan' => $validated['id_ruangan'],
            'expired_date' => $validated['expired_date'] ?? null,
            'jenis' => $validated['jenis'],
            'tanggal_refill' => $validated['tanggal_refill'] ?? null,
            'ukuran' => $validated['ukuran'],
            'merk' => $validated['merk'] ?? null,
            'media_isi' => $validated['media_isi'] ?? null,            'keterangan' => $validated['keterangan'] ?? null,
            'foto' => $fotoPath,
            'lokasi' => $validated['lokasi'] ?? null,
        ]);

        return redirect()->route('apar.index')->with('success', 'Data APAR berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $apar = Apar::findOrFail($id);

        if ($apar->foto !== 'apar/default.jpg' && Storage::disk('public')->exists($apar->foto)) {
            Storage::disk('public')->delete($apar->foto);
        }

        $apar->delete();

        return redirect()->route('apar.index')->with('success', 'APAR berhasil dihapus!');
    }

    public function getRuanganByGedung($id)
    {
        $ruangan = Ruangan::where('id_gedung', $id)
            ->select('id_ruangan', 'nama_ruangan')
            ->get();

        return response()->json($ruangan);
    }

    public function check($id)
    {
        $apar = Apar::findOrFail($id);
        $user = auth()->user();

        $apar->update([
            'last_checked_at' => now(),
            'last_checked_by' => $user->id_user
        ]);

        $this->logApar(
            $apar->id_apar,
            'check',
            null,
            'APAR dicek'
        );

        return back()->with('success', 'APAR berhasil dicek!');
    }

    public function use($id)
    {
        $apar = Apar::findOrFail($id);
        $user = auth()->user();

        $apar->update([
            'last_used_at' => now(),
            'last_used_by' => $user->id_user
        ]);

        $this->logApar(
            $apar->id_apar,
            'dipakai',
            null,
            'APAR digunakan '
        );

        return back()->with('success', 'APAR berhasil dicatat sebagai digunakan!');
    }
}
