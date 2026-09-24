<?php

namespace App\Http\Controllers;

use App\Models\RkatAnggaran;
use App\Models\RkatRealisasi;
use Illuminate\Http\Request;

class AnggaranController extends Controller
{
    // ──────────────────────────── RKAT ANGGARAN ────────────────────────────

    public function rkat(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);

        $data = RkatAnggaran::where('tahun', $tahun)
            ->withSum('realisasis', 'jumlah')
            ->orderBy('kode_kegiatan')
            ->get();

        $totalAnggaran  = $data->sum('anggaran');
        $totalRealisasi = $data->sum('realisasis_sum_jumlah');
        $totalSisa      = $totalAnggaran - $totalRealisasi;

        $tahunList = RkatAnggaran::distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        if ($tahunList->isEmpty()) {
            $tahunList = collect([now()->year]);
        }

        $coaList = config('coa');

        return view('anggaran.rkat', compact(
            'data', 'tahun', 'totalAnggaran', 'totalRealisasi', 'totalSisa',
            'tahunList', 'coaList'
        ));
    }

    public function storeRkat(Request $request)
    {
        $validated = $request->validate([
            'tahun'         => 'required|integer|min:2020|max:2100',
            'kode_kegiatan' => 'required|string|max:50|unique:rkat_anggaran,kode_kegiatan',
            'coa_pos'       => 'required|string|max:100',
            'coa_sub'       => 'required|string|max:150',
            'nama_kegiatan' => 'required|string|max:255',
            'anggaran'      => 'required|numeric|min:0',
        ]);

        $validated['created_by'] = auth()->user()->id_user;

        RkatAnggaran::create($validated);

        return redirect()->route('anggaran-rkat.index', ['tahun' => $validated['tahun']])
            ->with('success', 'Pos anggaran berhasil ditambahkan.');
    }

    public function updateRkat(Request $request, $id)
    {
        $anggaran = RkatAnggaran::findOrFail($id);

        $validated = $request->validate([
            'tahun'         => 'required|integer|min:2020|max:2100',
            'kode_kegiatan' => 'required|string|max:50|unique:rkat_anggaran,kode_kegiatan,' . $id,
            'coa_pos'       => 'required|string|max:100',
            'coa_sub'       => 'required|string|max:150',
            'nama_kegiatan' => 'required|string|max:255',
            'anggaran'      => 'required|numeric|min:0',
        ]);

        $anggaran->update($validated);

        return redirect()->route('anggaran-rkat.index', ['tahun' => $validated['tahun']])
            ->with('success', 'Pos anggaran berhasil diperbarui.');
    }

    public function destroyRkat($id)
    {
        $anggaran = RkatAnggaran::findOrFail($id);
        $tahun    = $anggaran->tahun;
        $anggaran->delete();

        return redirect()->route('anggaran-rkat.index', ['tahun' => $tahun])
            ->with('success', 'Pos anggaran berhasil dihapus.');
    }

    // ──────────────────────────── RKAT REALISASI ───────────────────────────

    public function realisasi(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);

        $data = RkatRealisasi::with('anggaran')
            ->whereHas('anggaran', fn ($q) => $q->where('tahun', $tahun))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(50);

        $totalRealisasi  = RkatRealisasi::whereHas('anggaran', fn ($q) => $q->where('tahun', $tahun))
            ->sum('jumlah');
        $jumlahTransaksi = RkatRealisasi::whereHas('anggaran', fn ($q) => $q->where('tahun', $tahun))
            ->count();

        $tahunList = RkatAnggaran::distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        if ($tahunList->isEmpty()) {
            $tahunList = collect([now()->year]);
        }

        $anggaranList = RkatAnggaran::where('tahun', $tahun)
            ->orderBy('kode_kegiatan')
            ->get(['id', 'kode_kegiatan', 'nama_kegiatan']);

        return view('anggaran.realisasi', compact(
            'data', 'tahun', 'totalRealisasi', 'jumlahTransaksi',
            'tahunList', 'anggaranList'
        ));
    }

    public function storeRealisasi(Request $request)
    {
        $validated = $request->validate([
            'rkat_anggaran_id' => 'required|exists:rkat_anggaran,id',
            'tanggal'          => 'required|date',
            'deskripsi'        => 'nullable|string|max:500',
            'jumlah'           => 'required|numeric|min:1',
        ]);

        RkatRealisasi::create($validated);

        $tahun = RkatAnggaran::find($validated['rkat_anggaran_id'])->tahun;

        return redirect()->route('riwayat-realisasi.index', ['tahun' => $tahun])
            ->with('success', 'Realisasi berhasil ditambahkan.');
    }

    public function destroyRealisasi($id)
    {
        $realisasi = RkatRealisasi::with('anggaran')->findOrFail($id);
        $tahun     = $realisasi->anggaran->tahun;
        $realisasi->delete();

        return redirect()->route('riwayat-realisasi.index', ['tahun' => $tahun])
            ->with('success', 'Realisasi berhasil dihapus.');
    }
}
