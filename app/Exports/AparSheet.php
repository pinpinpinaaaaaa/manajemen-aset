<?php

namespace App\Exports;

use App\Models\Apar;
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

class AparSheet implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'DATA APAR';

    public function title(): string
    {
        return $this->judul;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    /* ================= QUERY STREAM ================= */

    public function query()
    {
        return Apar::query()
            ->with([
                'gedung:id_gedung,nama_gedung',
                'ruangan:id_ruangan,nama_ruangan'
            ])
            ->select([
                'id_apar',
                'id_gedung',
                'id_ruangan',
                'jenis',
                'ukuran',
                'expired_date',
                'tanggal_refill',
                'keterangan'
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
            'ID APAR',
            'NAMA GEDUNG',
            'NAMA RUANGAN',
            'JENIS',
            'UKURAN',
            'EXPIRED DATE',
            'TANGGAL REFILL',
            'KETERANGAN'
        ];
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        return [
            $row->id_apar,
            $row->gedung?->nama_gedung ?? '-',
            $row->ruangan?->nama_ruangan ?? '-',
            strtoupper($row->jenis),
            $row->ukuran,

            $row->expired_date
                ? Carbon::parse($row->expired_date)->format('d-m-Y')
                : '-',

            $row->tanggal_refill
                ? Carbon::parse($row->tanggal_refill)->format('d-m-Y')
                : '-',

            $row->keterangan ?? '-',
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);

        $sheet->getStyle('A4:H4')->getAlignment()
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

                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setBold(true);

                $sheet->getStyle('A1:H2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A4:H{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
