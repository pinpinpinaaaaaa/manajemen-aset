<?php

namespace App\Exports;

use App\Models\Maintenance;
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

class MaintenanceExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN MAINTENANCE ASET';
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function title(): string
    {
        return $this->judul;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = Maintenance::query()
            ->where(function($qq){
                $qq->where('status','Selesai')
                   ->orWhere('decision_status','ditolak');
            })
            ->with([
                'aset.gedung:id_gedung,nama_gedung',
                'aset.ruangan:id_ruangan,nama_ruangan',
                'gedung:id_gedung,nama_gedung',
                'ruangan:id_ruangan,nama_ruangan',
            ]);

        if ($this->start) {
            $q->whereDate('tanggal_laporan','>=',$this->start);
        }

        if ($this->end) {
            $q->whereDate('tanggal_laporan','<=',$this->end);
        }

        return $q;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    /* ================= HEADINGS ================= */

    public function headings(): array
    {
        return array_map('strtoupper', [
            'ID Maintenance',
            'ID Aset',
            'Nama Aset',
            'Gedung',
            'Ruangan',
            'Tanggal Laporan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Durasi (Jam)',
            'Kerusakan',
            'Biaya',
            'Decision Status',
            'Status',
            'Pelaksana',
            'Vendor',
            'Catatan',
            'Created At',
            'Updated At'
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        return [
            $row->id_maintenance,
            $row->id_aset,
            $row->aset?->nama_aset ?? '-',

            $row->gedung?->nama_gedung
                ?? $row->aset?->gedung?->nama_gedung
                ?? '-',

            $row->ruangan?->nama_ruangan
                ?? $row->aset?->ruangan?->nama_ruangan
                ?? '-',

            $row->tanggal_laporan,
            $row->tanggal_mulai,
            $row->tanggal_selesai,
            $row->durasi_jam,
            $row->kerusakan,
            $row->biaya,
            $row->decision_status,
            $row->status,
            $row->pelaksana_type,
            $row->id_vendor,
            $row->catatan,
            $row->created_at,
            $row->updated_at
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:R5')->getFont()->setBold(true);

        $sheet->getStyle('A5:R5')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    /* ================= HEADER + BORDER ================= */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $periode = 'SEMUA DATA';

                if ($this->start || $this->end) {
                    $periode =
                        ($this->start
                            ? Carbon::parse($this->start)->format('d M Y')
                            : 'Awal')
                        .' s/d '.
                        ($this->end
                            ? Carbon::parse($this->end)->format('d M Y')
                            : 'Sekarang');
                }

                $sheet->setCellValue('A1', $this->judul);
                $sheet->setCellValue('A2', 'PERIODE : '.$periode);
                $sheet->setCellValue(
                    'A3',
                    'DIEKSPOR : '.Carbon::now()->format('d M Y H:i:s')
                );

                $sheet->mergeCells('A1:R1');
                $sheet->mergeCells('A2:R2');
                $sheet->mergeCells('A3:R3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sheet->getStyle('A1:R3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A5:R{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
