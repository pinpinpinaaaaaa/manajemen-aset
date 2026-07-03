<?php

namespace App\Exports;

use App\Models\PengaduanKerusakan;
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

class PengaduanKerusakanExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN RIWAYAT PENGADUAN KERUSAKAN';
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function title(): string
    {
        return 'Riwayat Pengaduan';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = PengaduanKerusakan::query()
            ->with([
                'divisi:id_divisi,nama_divisi',
                'gedung:id_gedung,nama_gedung',
                'ruangan:id_ruangan,nama_ruangan',
                'details.aset:id_aset,nama_aset'
            ]);

        if ($this->start) {
            $q->whereDate('created_at', '>=', $this->start);
        }

        if ($this->end) {
            $q->whereDate('created_at', '<=', $this->end);
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
            'ID Pengaduan',
            'Nama Pelapor',
            'Email',
            'Divisi',
            'Gedung',
            'Ruangan',
            'Detail Kerusakan (Aset & Keluhan)',
            'Kategori',
            'Decision Status',
            'Disetujui Oleh',
            'Tanggal Persetujuan',
            'Created At',
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        // 🔥 gabungkan detail aset + keluhan
        $detail = $row->details->map(function ($d) {
            return 
                ($d->aset->nama_aset ?? 'Tanpa Aset')
                . ' | Keluhan: ' . $d->keluhan
                . ' | Kategori: ' . $d->kategori_kerusakan;
        })->implode("\n");

        return [
            $row->id_pengaduan,
            $row->nama_pelapor ?? '-',
            $row->email_pelapor ?? '-',
            $row->divisi?->nama_divisi ?? '-',
            $row->gedung?->nama_gedung ?? '-',
            $row->ruangan?->nama_ruangan ?? '-',
            $detail ?: '-',
            $row->details->pluck('kategori_kerusakan')->implode(', '),
            $row->decision_status ?? '-',
            $row->decided_by ?? '-',
            $row->decided_at,
            $row->created_at,
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:L5')->getFont()->setBold(true);

        $sheet->getStyle('A5:L5')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    /* ================= AFTER SHEET ================= */

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

                $sheet->mergeCells('A1:L1');
                $sheet->mergeCells('A2:L2');
                $sheet->mergeCells('A3:L3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sheet->getStyle('A1:L3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A5:L{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}