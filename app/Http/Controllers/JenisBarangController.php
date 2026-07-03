<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisBarang;

class JenisBarangController extends Controller
{
    /**
     * Generate ID Jenis Barang
     * Contoh: JB0001
     */
    private function generateId()
    {
        $latest = JenisBarang::selectRaw(
                "CAST(SUBSTRING(id_jenis_barang, 3) AS UNSIGNED) AS num"
            )
            ->orderByDesc('num')
            ->first();

        if (!$latest) {
            return 'JB0001';
        }

        $num = $latest->num + 1;
        return 'JB' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Tampilkan semua jenis barang
     */
    public function index()
    {
        $data = JenisBarang::orderBy('kategori')
            ->orderBy('nama_barang')
            ->get();

        return view('jenis_barang.index', compact('data'));
    }

    /**
     * Form tambah jenis barang
     */
    public function create()
    {
        return view('jenis_barang.create');
    }

    /**
     * Simpan jenis barang baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis'        => 'required|in:sarana,prasarana',
            'kategori'     => 'required|in:it,elektronik,non elektronik',
            'nama_barang'  => 'required|string|max:100|unique:jenis_barang,nama_barang',
            'prefix_kode'  => 'required|string|max:10|unique:jenis_barang,prefix_kode',
            'bisa_dipindah'=> 'nullable|boolean',
        ], [
            'jenis.required' => 'Jenis wajib dipilih',
            'kategori.required' => 'Kategori wajib dipilih',
            'nama_barang.required' => 'Nama barang wajib diisi',
            'nama_barang.unique' => 'Nama barang sudah digunakan',
            'prefix_kode.unique' => 'Prefix kode sudah dipakai',
        ]);

        $nama = ucwords(strtolower(trim($request->nama_barang)));

        if (JenisBarang::whereRaw('LOWER(nama_barang) = ?', [strtolower($nama)])->exists()) {
            return back()->withErrors([
                'nama_barang' => 'Nama barang sudah digunakan'
            ])->withInput();
        }

        $bisaDipindah = $request->jenis === 'sarana'
            ? 1
            : $request->bisa_dipindah;

        $prefix = strtoupper(trim($request->prefix_kode));

        JenisBarang::create([
            'id_jenis_barang' => $this->generateId(),
            'jenis'           => $request->jenis,
            'kategori'        => $request->kategori,
            'nama_barang'     => $nama,
            'prefix_kode'     => $prefix,
            'bisa_dipindah'   => $bisaDipindah,
        ]);

        return redirect()
            ->route('jenis_barang.index')
            ->with('success', 'Jenis barang berhasil ditambahkan');
    }

    /**
     * Form edit jenis barang
     */
    public function edit($id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);
        return view('jenis_barang.edit', compact('jenisBarang'));
    }

    /**
     * Update jenis barang
     */
    public function update(Request $request, $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);

        $request->validate([
            'jenis'        => 'required|in:sarana,prasarana',
            'kategori'     => 'required|in:it,elektronik,non elektronik',
            'nama_barang' => 'required|string|max:100|unique:jenis_barang,nama_barang,' .
                            $id . ',id_jenis_barang',

            'prefix_kode' => 'required|string|max:10|unique:jenis_barang,prefix_kode,' .
                            $id . ',id_jenis_barang',
            'bisa_dipindah' => 'nullable|boolean',
        ]);

        $nama = ucwords(strtolower(trim($request->nama_barang)));
        $bisaDipindah = $request->jenis === 'sarana'
            ? 1
            : $request->bisa_dipindah;

        if (JenisBarang::whereRaw('LOWER(nama_barang) = ?', [strtolower($nama)])
            ->where('id_jenis_barang','!=',$id)
            ->exists()) {

            return back()->withErrors([
                'nama_barang' => 'Nama barang sudah digunakan'
            ])->withInput();
        }

        $jenisBarang->update([
            'jenis'         => $request->jenis,
            'kategori'      => $request->kategori,
            'nama_barang'   => $nama,
            'prefix_kode'   => strtoupper(trim($request->prefix_kode)),
            'bisa_dipindah' => $bisaDipindah,
        ]);
        return back()->with('success', 'Jenis barang berhasil diperbarui');
    }

    /**
     * Hapus jenis barang
     */
    public function destroy($id)
    {
        JenisBarang::findOrFail($id)->delete();

        return back()->with('success', 'Jenis barang berhasil dihapus');
    }

    /**
     * API: Ambil jenis barang berdasarkan kategori
     * Dipakai di form aset (AJAX)
     */
    public function getByKategori($kategori)
    {
        return JenisBarang::where('kategori', $kategori)
            ->select('id_jenis_barang', 'nama_barang', 'prefix_kode')
            ->orderBy('nama_barang')
            ->get();
    }
}
