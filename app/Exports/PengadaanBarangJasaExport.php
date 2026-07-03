<?php

namespace App\Exports;

use App\Models\PengadaanBarangJasa;
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

class PengadaanBarangJasaExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN RIWAYAT PENGADAAN BARANG & JASA';
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function title(): string
    {
        return 'Riwayat Pengadaan';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = PengadaanBarangJasa::with([
                'divisi:id_divisi,nama_divisi',
                'details'
            ])
            ->where(function ($q) {
                $q->where('status', 'Selesai')
                ->orWhere('decision_status', 'ditolak');
            });

        if ($this->start) {
            $q->whereDate('created_at', '>=', $this->start);
        }

        if ($this->end) {
            $q->whereDate('created_at', '<=', $this->end);
        }

        return $q->latest();
    }

    public function chunkSize(): int
    {
        return 500;
    }

    /* ================= HEADINGS ================= */

    public function headings(): array
    {
        return array_map('strtoupper', [
            'ID Pengadaan',
            'Nama Pengaju',
            'Email',
            'Divisi',
            'Alasan',
            'Tanggal Kebutuhan',
            'Detail Item (Barang/Jasa)',
            'Total Biaya',
            'Decision Status',
            'Disetujui Oleh',
            'Tanggal Persetujuan',
            'Created At',
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        $detail = $row->details->map(function ($d) {

            if ($d->jenis === 'barang') {

                return
                    "BARANG : {$d->nama_barang}\n" .
                    "Merk : " . ($d->merk ?: '-') . "\n" .
                    "Tipe : " . ($d->tipe_model ?: '-') . "\n" .
                    "Spesifikasi : " . ($d->spesifikasi ?: '-') . "\n" .
                    "Jumlah : {$d->jumlah}\n" .
                    "Harga : Rp " . number_format($d->harga_satuan, 0, ',', '.') . "\n" .
                    "Subtotal : Rp " . number_format($d->subtotal, 0, ',', '.');

            }

            return
                "JASA : {$d->kategori_jasa}\n" .
                "Jumlah : {$d->jumlah}\n" .
                "Harga : Rp " . number_format($d->harga_satuan, 0, ',', '.') . "\n" .
                "Subtotal : Rp " . number_format($d->subtotal, 0, ',', '.');

        })->implode("\n-----------------------------------\n");

        return [
            $row->id_pengadaan,
            $row->nama_pengaju ?? '-',
            $row->email_pengaju ?? '-',
            $row->divisi?->nama_divisi ?? '-',
            $row->alasan ?? '-',
            \Carbon\Carbon::parse($row->tanggal_kebutuhan)->format('d-m-Y'),
            $detail,
            $row->total_biaya,
            strtoupper($row->status ?? '-'),
            strtoupper($row->decision_status ?? '-'),
            $row->decided_at
                ? \Carbon\Carbon::parse($row->decided_at)->format('d-m-Y H:i')
                : '-',
            \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i'),
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

                $sheet->freezePane('A6');

                $sheet->getStyle("A5:L{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A6:L{$highestRow}")
                    ->getAlignment()
                    ->setWrapText(true);

                foreach (range('A', 'L') as $column) {

                    if ($column === 'G') {
                        continue;
                    }

                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $sheet->getColumnDimension('G')->setWidth(60);

                $sheet->getStyle("H6:H{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                $sheet->getStyle("A6:L{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_TOP);
            },
        ];
    }
}