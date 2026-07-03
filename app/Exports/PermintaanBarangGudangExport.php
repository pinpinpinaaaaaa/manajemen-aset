<?php

namespace App\Exports;

use App\Models\PermintaanBarangGudang;
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

class PermintaanBarangGudangExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN RIWAYAT PERMINTAAN BARANG GUDANG';
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function title(): string
    {
        return 'Riwayat Permintaan Gudang';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = PermintaanBarangGudang::query()
            ->where(function ($x) {
                $x->where('status', 'Selesai')
                  ->orWhere('decision_status', 'ditolak');
            })
            ->with([
                'divisi:id_divisi,nama_divisi',
                'details.barang:id_barang,nama_barang'
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
            'ID Permintaan',
            'Nama Pengaju',
            'Divisi',
            'Detail Barang',
            'Status',
            'Decision Status',
            'Disetujui Oleh',
            'Tanggal Persetujuan',
            'Alasan',
            'Tanggal Kebutuhan',
            'Created At',
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        $detailBarang = $row->details->map(function ($d) {
            $nama = $d->barang->nama_barang ?? 'Barang tidak ditemukan';
            return $nama . ' (' . $d->jumlah . ')';
        })->implode(', ');

        return [
            $row->id_permintaan,
            $row->nama_pengaju ?? '-',
            $row->divisi?->nama_divisi ?? '-',
            $detailBarang ?: '-',
            $row->status ?? '-',
            $row->decision_status ?? '-',
            $row->decided_by ?? '-',
            $row->decided_at,
            $row->alasan ?? '-',
            $row->tanggal_kebutuhan,
            $row->created_at,
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:K5')->getFont()->setBold(true);
        $sheet->getStyle('A5:K5')
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

                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');
                $sheet->mergeCells('A3:K3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:K3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A5:K{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}