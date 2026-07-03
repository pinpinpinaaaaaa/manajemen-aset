<?php

namespace App\Exports;

use App\Models\PermintaanKendaraan;
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

class PermintaanKendaraanExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN RIWAYAT PERMINTAAN KENDARAAN';
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function title(): string
    {
        return 'Riwayat Kendaraan';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = PermintaanKendaraan::query()
            ->whereIn('status', ['selesai', 'ditolak'])
            ->with([
                'divisi:id_divisi,nama_divisi',
                'details.items.kendaraan'
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
            'ID Permohonan',
            'Nama',
            'Divisi',
            'Detail Perjalanan',
            'Kendaraan',
            'Status',
            'Tanggal Mulai',
            'Jam',
            'Tujuan',
            'Created At',
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        // 🔹 Detail perjalanan
        $detail = $row->details->map(function ($d) {
            return $d->keperluan . ' (' . $d->tempat_jemput . ' → ' . $d->tempat_tujuan . ')';
        })->implode(', ');

        // 🔹 Kendaraan
        $kendaraan = $row->details->flatMap(function ($d) {
            return $d->items->map(function ($item) {
                $k = $item->kendaraan;
                return ($k->plat_nomor ?? '-') . ' (' . ($k->merk ?? '') . ' ' . ($k->model ?? '') . ')';
            });
        })->implode(', ');

        // 🔹 Ambil tanggal & jam dari detail pertama
        $firstDetail = $row->details->first();

        return [
            $row->id_permohonan,
            $row->nama ?? '-',
            $row->divisi?->nama_divisi ?? '-',
            $detail ?: '-',
            $kendaraan ?: '-',
            ucfirst($row->status),
            $firstDetail?->tanggal_mulai ?? '-',
            $firstDetail ? ($firstDetail->jam_mulai . ' - ' . $firstDetail->jam_selesai) : '-',
            $firstDetail?->tempat_tujuan ?? '-',
            $row->created_at,
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:J5')->getFont()->setBold(true);
        $sheet->getStyle('A5:J5')
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

                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->mergeCells('A3:J3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:J3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A5:J{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}