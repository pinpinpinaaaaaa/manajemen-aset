<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class KendaraanController extends Controller
{
    private function generateId()
    {
        $latest = Kendaraan::selectRaw("CAST(SUBSTRING(id_kendaraan, 4) AS UNSIGNED) AS num")
            ->orderByDesc('num')
            ->lockForUpdate()
            ->first();

        if (!$latest) {
            return 'KND001';
        }

        $num = $latest->num + 1;
        return 'KND' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $kendaraan = Kendaraan::paginate($perPage);

        $stats = [
            'total'    => Kendaraan::count(),
            'aktif'    => Kendaraan::where('status_kondisi', 'aktif')->count(),
            'perbaikan'=> Kendaraan::where('status_kondisi', 'perbaikan')->count(),
            'non_aktif'=> Kendaraan::where('status_kondisi', 'non aktif')->count(),
        ];

        return view('kendaraan.index', compact('kendaraan', 'stats', 'perPage'));
    }

    public function show($id)
    {
        $kendaraan = Kendaraan::with('driver')->findOrFail($id);

        return view('kendaraan.show', compact('kendaraan'));
    }

    public function create()
    {
        $newId = $this->generateId();
        $drivers = User::orderBy('name')->get();

        return view('kendaraan.create', [
            'newId' => $newId,
            'drivers' => $drivers,
            'jenisOptions' => $this->getEnumValues('kendaraan', 'jenis_kendaraan'),
            'tipeOptions' => $this->getEnumValues('kendaraan', 'tipe'),
            'statusOptions' => $this->getEnumValues('kendaraan', 'status_kondisi'),
            'penggunaanOptions' => $this->getEnumValues('kendaraan', 'status_penggunaan'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_kendaraan' => 'required|in:roda 2,roda 4',
            'tipe' => 'required|in:motor,mobil',
            'plat_nomor' => 'required|unique:kendaraan,plat_nomor',
            'tahun_pembelian' => 'required|integer|min:1990|max:' . date('Y'),
            'umur_ekonomis' => 'required|integer|min:1',
            'merk' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'spesifikasi' => 'nullable|string',
            'no_rangka' => 'nullable|unique:kendaraan,no_rangka',
            'no_mesin' => 'nullable|unique:kendaraan,no_mesin',
            'status_kondisi' => 'required|in:aktif,perbaikan,non aktif',
            'status_penggunaan' => 'required|in:tersedia,terpakai',
            'driver_id' => 'nullable|exists:users,id_user',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $id = $this->generateId();
            $validated['id_kendaraan'] = $id;

            $fotoPath = $validated['jenis_kendaraan'] === 'roda 2'
                ? 'kendaraan/motor/default.jpg'
                : 'kendaraan/mobil/default.jpg';

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');

                $manager = new ImageManager(driver: new Driver());
                $image = $manager->read($file)
                    ->scaleDown(width: 1600)
                    ->toJpeg(60);

                $folder = $validated['jenis_kendaraan'] === 'roda 2'
                    ? 'kendaraan/motor/'
                    : 'kendaraan/mobil/';

                Storage::disk('public')->put($folder . $id . '.jpg', $image);

                $fotoPath = $folder . $id . '.jpg';
            }

            $validated['foto'] = $fotoPath;
            Kendaraan::create($validated);
        });

        return redirect()->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }



    public function edit($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $drivers = User::orderBy('name')->get();

        return view('kendaraan.edit', [
            'kendaraan' => $kendaraan,
            'drivers' => $drivers,
            'jenisOptions' => $this->getEnumValues('kendaraan', 'jenis_kendaraan'),
            'tipeOptions' => $this->getEnumValues('kendaraan', 'tipe'),
            'statusOptions' => $this->getEnumValues('kendaraan', 'status_kondisi'),
            'penggunaanOptions' => $this->getEnumValues('kendaraan', 'status_penggunaan'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $validated = $request->validate([
            'jenis_kendaraan' => 'required|in:roda 2,roda 4',
            'tipe' => 'required|in:motor,mobil',
            'plat_nomor' => "required|unique:kendaraan,plat_nomor,$id,id_kendaraan",
            'tahun_pembelian' => 'required|integer|min:1990|max:' . date('Y'),
            'umur_ekonomis' => 'required|integer|min:1',
            'merk' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'spesifikasi' => 'nullable|string',
            'no_rangka' => "nullable|unique:kendaraan,no_rangka,$id,id_kendaraan",
            'no_mesin' => "nullable|unique:kendaraan,no_mesin,$id,id_kendaraan",
            'status_kondisi' => 'required|in:aktif,perbaikan,non aktif',
            'status_penggunaan' => 'required|in:tersedia,terpakai',
            'driver_id' => 'nullable|exists:users,id_user',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);


        $isDefaultMotor = $kendaraan->foto === 'kendaraan/motor/default.jpg';
        $isDefaultMobil = $kendaraan->foto === 'kendaraan/mobil/default.jpg';
        $isDefaultFoto  = $isDefaultMotor || $isDefaultMobil;

        $fotoPath = $kendaraan->foto;

        if ($request->hasFile('foto')) {

            if (!$isDefaultFoto && Storage::disk('public')->exists($kendaraan->foto)) {
                Storage::disk('public')->delete($kendaraan->foto);
            }

            $file = $request->file('foto');

            $manager = new ImageManager(driver: new Driver());
            $image = $manager->read($file)
                ->scaleDown(width: 1600)
                ->toJpeg(60);

            $folder = $validated['jenis_kendaraan'] === 'roda 2'
                ? 'kendaraan/motor/'
                : 'kendaraan/mobil/';

            $filename = $kendaraan->id_kendaraan . '.jpg';

            Storage::disk('public')->put($folder . $filename, $image);

            $fotoPath = $folder . $filename;
        }
        /* Jika TIDAK upload foto, tapi jenis kendaraan berubah & foto masih default */
        else if ($isDefaultFoto) {
            $fotoPath = $validated['jenis_kendaraan'] === 'roda 2'
                ? 'kendaraan/motor/default.jpg'
                : 'kendaraan/mobil/default.jpg';
        }

        $validated['foto'] = $fotoPath;

        $kendaraan->update($validated);

        return redirect()->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil diperbarui!');
    }



    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        if (
            $kendaraan->foto !== 'kendaraan/motor/default.jpg' &&
            $kendaraan->foto !== 'kendaraan/mobil/default.jpg' &&
            Storage::disk('public')->exists($kendaraan->foto)
        ) {
            Storage::disk('public')->delete($kendaraan->foto);
        }

        $kendaraan->delete();

        return redirect()->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil dihapus!');
    }

    private function getEnumValues($table, $column)
    {
        $q = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column]);

        if (!$q) return [];

        if (!preg_match('/enum\((.*)\)/', $q[0]->Type, $matches)) {
            return [];
        }

        return explode(',', str_replace("'", "", $matches[1]));
    }
}
