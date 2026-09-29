<?php

namespace App\Exports;

use App\Models\RkatAnggaran;
use App\Models\RkatRealisasi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RkatExport implements WithMultipleSheets
{
    public function __construct(private int $tahun) {}

    public function sheets(): array
    {
        $anggaran  = RkatAnggaran::where('tahun', $this->tahun)
            ->orderBy('kode_coa')
            ->orderBy('kode_kegiatan')
            ->get();

        $realisasi = RkatRealisasi::whereIn('rkat_anggaran_id', $anggaran->pluck('id'))
            ->with('anggaran')
            ->orderBy('tanggal')
            ->get();

        return [
            new RkatRencanaSheet($this->tahun, $anggaran),
            new RkatRealisasiDetailSheet($this->tahun, $realisasi),
            new RkatRekapSheet($this->tahun, $anggaran, $realisasi),
        ];
    }
}
