<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OpnameExport implements FromCollection, WithHeadings
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        return DB::table('gudang_opname_detail as d')
            ->join('gudang_barang as b', 'b.id_barang', '=', 'd.id_barang')
            ->where('d.id_opname', $this->id)
            ->select(
                'b.nama_barang',
                'd.stok_sistem',
                'd.stok_fisik',
                'd.selisih'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Barang',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih'
        ];
    }
}