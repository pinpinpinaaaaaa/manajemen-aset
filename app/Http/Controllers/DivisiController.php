<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;

class DivisiController extends Controller
{
    public function store(Request $request)
    {
        // Hanya admin (full access) yang boleh membuat divisi baru
        abort_unless(
            \App\Services\MenuAccessService::allowedMenuIds(auth()->user()) === null,
            403
        );

        $id = Divisi::generateId();

        Divisi::create([
            'id_divisi' => $id,
            'nama_divisi' => $request->nama_divisi,
            'kode_divisi' => $request->kode_divisi,
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Divisi berhasil ditambahkan');
    }

}
