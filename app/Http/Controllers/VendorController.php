<?php

namespace App\Http\Controllers;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    private function generateIdVendor()
    {
        $last = Vendor::lockForUpdate()->orderBy('id_vendor', 'desc')->first();

        if (!$last) {
            return 'VND001';
        }

        $number = (int) substr($last->id_vendor, 3);

        return 'VND' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $perPage = (int) request('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) $perPage = 50;

        $vendor = Vendor::latest()->paginate($perPage);

        return view('vendor.index', compact('vendor', 'perPage'));
    }

    public function create()
    {
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required',
            'email_perusahaan' => 'nullable|email',

            'akta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nib' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'npwp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pakta_integritas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'akta_link' => 'nullable|url',
            'nib_link' => 'nullable|url',
            'npwp_link' => 'nullable|url',
            'pakta_integritas_link' => 'nullable|url',
            'no_telp_cp' => [
                'required',
                'regex:/^62[0-9]{8,15}$/'
            ],
        ]);

        $docs = [
            'akta',
            'nib',
            'npwp',
            'pakta_integritas'
        ];

        foreach ($docs as $doc) {
            if ($request->hasFile($doc) && $request->filled($doc.'_link')) {
                return back()
                    ->withInput()
                    ->withErrors([
                        $doc => 'Pilih file atau link saja.'
                    ]);
            }
        }

        $data = [
            'nama_perusahaan' => $request->nama_perusahaan,
            'bidang_usaha' => $request->bidang_usaha,
            'alamat' => $request->alamat,
            'contact_person' => $request->contact_person,
            'no_telp_cp' => $request->no_telp_cp,
            'email_perusahaan' => $request->email_perusahaan,
            'jabatan_cp' => $request->jabatan_cp,
            'akta_link' => $request->akta_link,
            'nib_link' => $request->nib_link,
            'npwp_link' => $request->npwp_link,
            'pakta_integritas_link' => $request->pakta_integritas_link,
        ];

        if ($request->hasFile('akta')) {
            $data['akta'] = $request->file('akta')
                ->store('vendor/akta', 'public');
        }

        if ($request->hasFile('nib')) {
            $data['nib'] = $request->file('nib')
                ->store('vendor/nib', 'public');
        }

        if ($request->hasFile('npwp')) {
            $data['npwp'] = $request->file('npwp')
                ->store('vendor/npwp', 'public');
        }

        if ($request->hasFile('pakta_integritas')) {
            $data['pakta_integritas'] = $request->file('pakta_integritas')
                ->store('vendor/pakta_integritas', 'public');
        }

        DB::transaction(function() use ($data) {
            $data['id_vendor'] = $this->generateIdVendor();
            Vendor::create($data);
        });

        return redirect()
            ->route('vendor.create')
            ->with('success', 'Vendor berhasil didaftarkan');
    }


    public function show(Vendor $vendor)
    {
        return view('vendor.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return view('vendor.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'nama_perusahaan' => 'required',
            'email_perusahaan' => 'nullable|email',

            'akta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nib' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'npwp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pakta_integritas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'no_telp_cp' => [
                'required',
                'regex:/^62[0-9]{8,15}$/'
            ],
        ]);

        $data = [
            'nama_perusahaan' => $request->nama_perusahaan,
            'bidang_usaha' => $request->bidang_usaha,
            'alamat' => $request->alamat,
            'contact_person' => $request->contact_person,
            'no_telp_cp' => $request->no_telp_cp,
            'email_perusahaan' => $request->email_perusahaan,
            'jabatan_cp' => $request->jabatan_cp,

            'akta_link' => $request->akta_link,
            'nib_link' => $request->nib_link,
            'npwp_link' => $request->npwp_link,
            'pakta_integritas_link' => $request->pakta_integritas_link,
        ];

        // Ganti ke LINK
        if ($request->filled('akta_link')) {

            if ($vendor->akta &&
                Storage::disk('public')->exists($vendor->akta)) {

                Storage::disk('public')->delete($vendor->akta);
            }

            $data['akta'] = null;
        }

        // Ganti ke FILE
        if ($request->hasFile('akta')) {

            if ($vendor->akta &&
                Storage::disk('public')->exists($vendor->akta)) {

                Storage::disk('public')->delete($vendor->akta);
            }

            $data['akta'] = $request->file('akta')
                ->store('vendor/akta', 'public');

            $data['akta_link'] = null;
        }

        if ($request->filled('nib_link')) {

            if ($vendor->nib &&
                Storage::disk('public')->exists($vendor->nib)) {

                Storage::disk('public')->delete($vendor->nib);
            }

            $data['nib'] = null;
        }

        if ($request->hasFile('nib')) {

            if ($vendor->nib &&
                Storage::disk('public')->exists($vendor->nib)) {

                Storage::disk('public')->delete($vendor->nib);
            }

            $data['nib'] = $request->file('nib')
                ->store('vendor/nib', 'public');

            $data['nib_link'] = null;
        }

        if ($request->filled('npwp_link')) {

            if ($vendor->npwp &&
                Storage::disk('public')->exists($vendor->npwp)) {

                Storage::disk('public')->delete($vendor->npwp);
            }

            $data['npwp'] = null;
        }

        if ($request->hasFile('npwp')) {

            if ($vendor->npwp &&
                Storage::disk('public')->exists($vendor->npwp)) {

                Storage::disk('public')->delete($vendor->npwp);
            }

            $data['npwp'] = $request->file('npwp')
                ->store('vendor/npwp', 'public');

            $data['npwp_link'] = null;
        }

        if ($request->filled('pakta_integritas_link')) {

            if ($vendor->pakta_integritas &&
                Storage::disk('public')->exists($vendor->pakta_integritas)) {

                Storage::disk('public')->delete($vendor->pakta_integritas);
            }

            $data['pakta_integritas'] = null;
        }

        if ($request->hasFile('pakta_integritas')) {

            if ($vendor->pakta_integritas &&
                Storage::disk('public')->exists($vendor->pakta_integritas)) {

                Storage::disk('public')->delete($vendor->pakta_integritas);
            }

            $data['pakta_integritas'] = $request->file('pakta_integritas')
                ->store('vendor/pakta_integritas', 'public');

            $data['pakta_integritas_link'] = null;
        }

        $vendor->update($data);

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Vendor berhasil diupdate');
    }

    public function destroy(Vendor $vendor)
    {
        foreach ([
            $vendor->akta,
            $vendor->nib,
            $vendor->npwp,
            $vendor->pakta_integritas
        ] as $file) {

            if ($file && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }

        $vendor->delete();

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Vendor berhasil dihapus');
    }
}
