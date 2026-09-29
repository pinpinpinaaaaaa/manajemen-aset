<?php

namespace App\Http\Controllers;

use App\Models\RkatAnggaran;
use App\Models\RkatRealisasi;
use App\Exports\RkatExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AnggaranController extends Controller
{
    // ──────────────────────────── RKAT ANGGARAN ────────────────────────────

    public function rkat(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);

        $data = RkatAnggaran::where('tahun', $tahun)
            ->withSum(['realisasis as total_keluar' => fn($q) => $q->where('jenis', 'keluar')], 'jumlah')
            ->withSum(['realisasis as total_masuk'  => fn($q) => $q->where('jenis', 'masuk')],  'jumlah')
            ->orderBy('kode_kegiatan')
            ->get();

        $totalAnggaran = $data->sum('anggaran');
        $totalKeluar   = $data->sum('total_keluar');
        $totalMasuk    = $data->sum('total_masuk');
        $totalSisa     = $totalAnggaran - $totalKeluar;

        $tahunList = RkatAnggaran::distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        if ($tahunList->isEmpty()) {
            $tahunList = collect([now()->year]);
        }

        $coaList = config('coa');

        // Map kode_coa → coa_pos untuk auto-fill di form
        $coaKodeMap = RkatAnggaran::whereNotNull('kode_coa')
            ->where('kode_coa', '!=', '')
            ->select('kode_coa', 'coa_pos')
            ->get()
            ->unique('kode_coa')
            ->pluck('coa_pos', 'kode_coa');

        return view('anggaran.rkat', compact(
            'data', 'tahun', 'totalAnggaran', 'totalKeluar', 'totalMasuk', 'totalSisa',
            'tahunList', 'coaList', 'coaKodeMap'
        ));
    }

    public function showRkat($id)
    {
        $anggaran = RkatAnggaran::findOrFail($id);

        $realisasis = RkatRealisasi::where('rkat_anggaran_id', $id)
            ->orderBy('tanggal')
            ->get();

        $namaBulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];

        $byMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $items = $realisasis->filter(fn($r) => $r->tanggal->month === $m);
            $byMonth[$m] = [
                'label'  => $namaBulan[$m],
                'keluar' => $items->where('jenis', 'keluar')->sum('jumlah'),
                'masuk'  => $items->where('jenis', 'masuk')->sum('jumlah'),
                'items'  => $items->values(),
            ];
        }

        // Hitung kumulatif dan sisa per bulan
        $kumulatif = 0;
        foreach ($byMonth as $m => &$data) {
            $kumulatif        += $data['keluar'];
            $data['kumulatif'] = $kumulatif;
            $data['sisa']      = $anggaran->anggaran - $kumulatif;
        }
        unset($data);

        $totalKeluar = $realisasis->where('jenis', 'keluar')->sum('jumlah');
        $totalMasuk  = $realisasis->where('jenis', 'masuk')->sum('jumlah');
        $sisa        = $anggaran->anggaran - $totalKeluar;

        // Bulan rencana per bulan dari model
        $rencanaKeys = [
            1=>'rencana_jan',2=>'rencana_feb',3=>'rencana_mar',4=>'rencana_apr',
            5=>'rencana_mei',6=>'rencana_jun',7=>'rencana_jul',8=>'rencana_agu',
            9=>'rencana_sep',10=>'rencana_okt',11=>'rencana_nov',12=>'rencana_des',
        ];

        return view('anggaran.show', compact(
            'anggaran', 'byMonth', 'totalKeluar', 'totalMasuk', 'sisa', 'rencanaKeys'
        ));
    }

    public function exportRkat(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);
        $nama  = "RKAT-GA-{$tahun}.xlsx";
        return Excel::download(new RkatExport($tahun), $nama);
    }

    private array $bulanKeys = [
        'rencana_jan','rencana_feb','rencana_mar','rencana_apr',
        'rencana_mei','rencana_jun','rencana_jul','rencana_agu',
        'rencana_sep','rencana_okt','rencana_nov','rencana_des',
    ];

    private function rencanaRules(): array
    {
        return array_fill_keys(
            $this->bulanKeys,
            'nullable|numeric|min:0'
        );
    }

    private function validateRencanaSum(array $validated): ?string
    {
        $total = collect($this->bulanKeys)->sum(fn ($k) => (float)($validated[$k] ?? 0));
        if ($total > (float)$validated['anggaran']) {
            return 'Total rencana bulanan (Rp ' . number_format($total, 0, ',', '.') . ') melebihi anggaran (Rp ' . number_format($validated['anggaran'], 0, ',', '.') . ').';
        }
        return null;
    }

    private function normalizeRencana(array &$validated): void
    {
        foreach ($this->bulanKeys as $k) {
            $validated[$k] = (float)($validated[$k] ?? 0);
        }
    }

    public function storeRkat(Request $request)
    {
        $thisYear = now()->year;
        $validated = $request->validate(array_merge([
            'tahun'         => "required|integer|min:{$thisYear}|max:" . ($thisYear + 5),
            'kode_coa'          => 'nullable|string|max:30',
            'laporan_keuangan'  => 'nullable|in:IS,BS',
            'kode_kegiatan'     => 'required|string|max:50|unique:rkat_anggaran,kode_kegiatan',
            'coa_pos'           => 'required|string|max:255',
            'nama_kegiatan'     => 'required|string|max:255',
            'anggaran'          => 'required|numeric|min:0',
        ], $this->rencanaRules()));

        if ($err = $this->validateRencanaSum($validated)) {
            return back()->withErrors(['rencana' => $err])->withInput();
        }

        $this->normalizeRencana($validated);
        $validated['coa_sub']    = $validated['coa_pos'];
        $validated['created_by'] = auth()->user()->id_user;

        RkatAnggaran::create($validated);

        return redirect()->route('anggaran-rkat.index', ['tahun' => $validated['tahun']])
            ->with('success', 'Pos anggaran berhasil ditambahkan.');
    }

    public function updateRkat(Request $request, $id)
    {
        $anggaran = RkatAnggaran::findOrFail($id);

        $thisYear = now()->year;
        $validated = $request->validate(array_merge([
            'tahun'         => "required|integer|min:{$thisYear}|max:" . ($thisYear + 5),
            'kode_coa'         => 'nullable|string|max:30',
            'laporan_keuangan' => 'nullable|in:IS,BS',
            'kode_kegiatan'    => 'required|string|max:50|unique:rkat_anggaran,kode_kegiatan,' . $id,
            'coa_pos'           => 'required|string|max:255',
            'nama_kegiatan'     => 'required|string|max:255',
            'anggaran'          => 'required|numeric|min:0',
        ], $this->rencanaRules()));

        if ($err = $this->validateRencanaSum($validated)) {
            return back()->withErrors(['rencana' => $err])->withInput();
        }

        $this->normalizeRencana($validated);
        $validated['coa_sub'] = $validated['coa_pos'];
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
            ->get(['id', 'kode_kegiatan', 'coa_pos', 'nama_kegiatan']);

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
            'jenis'            => 'required|in:keluar,masuk',
        ]);

        RkatRealisasi::create($validated);

        $tahun = RkatAnggaran::find($validated['rkat_anggaran_id'])->tahun;

        return redirect()->route('riwayat-realisasi.index', ['tahun' => $tahun])
            ->with('success', 'Realisasi berhasil ditambahkan.');
    }

    public function updateRealisasi(Request $request, $id)
    {
        $realisasi = RkatRealisasi::with('anggaran')->findOrFail($id);

        $validated = $request->validate([
            'rkat_anggaran_id' => 'required|exists:rkat_anggaran,id',
            'tanggal'          => 'required|date',
            'deskripsi'        => 'nullable|string|max:500',
            'jumlah'           => 'required|numeric|min:1',
            'jenis'            => 'required|in:keluar,masuk',
        ]);

        $realisasi->update($validated);

        $tahun = RkatAnggaran::find($validated['rkat_anggaran_id'])->tahun;

        return redirect()->route('riwayat-realisasi.index', ['tahun' => $tahun])
            ->with('success', 'Realisasi berhasil diperbarui.');
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
