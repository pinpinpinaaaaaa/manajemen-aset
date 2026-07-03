<?php

namespace App\Exports;

use App\Exports\GedungSheet;
use App\Exports\RuanganSheet;
use App\Exports\AsetSaranaSheet;
use App\Exports\AparSheet;
use App\Exports\KendaraanSheet;

use App\Models\Aset;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AsetExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new GedungSheet(),
            new RuanganSheet(),
            new AsetSaranaSheet(),
            new AparSheet(),
            new KendaraanSheet(),
        ];
    }
}
