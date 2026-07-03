<?php

namespace App\Exports;

use App\Models\Kendaraan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KendaraanSheet implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'DATA KENDARAAN';

    public function title(): string
    {
        return $this->judul;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    /* ================= QUERY (STREAMING) ================= */

    public function query()
    {
        return Kendaraan::query()
            ->with('driver:id,name')
            ->select([
                'id_kendaraan',
                'jenis_kendaraan',
                'tipe',
                'plat_nomor',
                'merk',
                'model',
                'spesifikasi',
                'no_rangka',
                'no_mesin',
                'tahun_pembelian',
                'umur_ekonomis',
                'status_kondisi',
                'status_penggunaan',
                'driver_id'
            ]);
    }

    /* ================= CHUNK SIZE ================= */

    public function chunkSize(): int
    {
        return 500; // bisa dinaikin kalau server kuat
    }

    /* ================= HEADINGS ================= */

    public function headings(): array
    {
        return [
            'ID KENDARAAN',
            'JENIS',
            'TIPE',
            'PLAT NOMOR',
            'MERK',
            'MODEL',
            'SPESIFIKASI',
            'NO RANGKA',
            'NO MESIN',
            'TAHUN BELI',
            'UMUR EKONOMIS',
            'STATUS KONDISI',
            'STATUS PENGGUNAAN',
            'DRIVER'
        ];
    }

    /* ================= MAPPING ================= */

    public function map($row): array
    {
        return [
            $row->id_kendaraan,
            strtoupper($row->jenis_kendaraan),
            strtoupper($row->tipe),
            strtoupper($row->plat_nomor),

            strtoupper($row->merk),
            strtoupper($row->model),
            $row->spesifikasi ?? '-',
            $row->no_rangka ?? '-',
            $row->no_mesin ?? '-',

            $row->tahun_pembelian,
            $row->umur_ekonomis . ' Tahun',
            strtoupper($row->status_kondisi),
            strtoupper($row->status_penggunaan),

            $row->driver?->name ?? '-',
        ];
    }

    /* ================= STYLE HEADER ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A4:N4')->getFont()->setBold(true);

        $sheet->getStyle('A4:N4')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    /* ================= AFTER SHEET ================= */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', $this->judul);
                $sheet->setCellValue(
                    'A2',
                    'DIEKSPOR PADA: ' . strtoupper(Carbon::now()->format('d M Y H:i:s'))
                );

                $sheet->mergeCells('A1:N1');
                $sheet->mergeCells('A2:N2');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setBold(true);

                $sheet->getStyle('A1:N2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A4:N{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
