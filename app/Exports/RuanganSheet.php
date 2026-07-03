<?php

namespace App\Exports;

use App\Models\Ruangan;
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
use Illuminate\Support\Facades\DB;

class RuanganSheet implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'DATA RUANGAN';

    public function title(): string
    {
        return $this->judul;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    /* ================= FAST QUERY ================= */

    public function query()
    {
        return Ruangan::query()
            ->leftJoin('gedung', 'gedung.id_gedung', '=', 'ruangan.id_gedung')
            ->select([
                'ruangan.id_ruangan',
                'gedung.nama_gedung',
                'ruangan.nama_ruangan',
                'ruangan.kategori',
                'ruangan.lantai',
                'ruangan.status'
            ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }

    /* ================= HEADINGS ================= */

    public function headings(): array
    {
        return [
            'ID RUANGAN',
            'NAMA GEDUNG',
            'NAMA RUANGAN',
            'KATEGORI',
            'LANTAI',
            'STATUS'
        ];
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        return [
            $row->id_ruangan,
            $row->nama_gedung ?? '-',
            $row->nama_ruangan,
            strtoupper($row->kategori ?? '-'),
            $row->lantai ?? '-',
            strtoupper($row->status),
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        $sheet->getStyle('A4:F4')->getAlignment()
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

                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setBold(true);

                $sheet->getStyle('A1:F2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A4:F{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
