<?php

namespace App\Exports;

use App\Models\Aset;
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

class AsetSaranaSheet implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'DATA ASET SARANA';

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
        return Aset::query()
            ->whereHas('jenisBarang', function ($q) {
                $q->where('jenis', 'sarana');
            })
            ->with([
                'jenisBarang:id_jenis_barang,nama_barang,jenis,kategori',
                'gedung:id_gedung,nama_gedung',
                'ruangan:id_ruangan,nama_ruangan'
            ])
            ->select([
                'kode_aset',
                'nama_aset',
                'merk',
                'tipe_model',
                'spesifikasi',
                'id_jenis_barang',
                'id_gedung',
                'id_ruangan',
                'tahun_perolehan',
                'nilai',
                'kelayakan',
                'keterangan_kelayakan',
                'status'
            ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }

    /* ================= HEADINGS ================= */

    public function headings(): array
    {
        return array_map('strtoupper', [
            'Kode Aset',
            'Nama Aset',
            'Merk',
            'Tipe / Model',
            'Spesifikasi',
            'Jenis',
            'Kategori',
            'Jenis Barang',
            'Gedung',
            'Ruangan',
            'Tahun Perolehan',
            'Nilai',
            'Skor Kelayakan (1-5)',
            'Keterangan Kelayakan',
            'Status'
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        return [
            $row->kode_aset,
            $row->nama_aset,
            $row->merk,
            $row->tipe_model,
            $row->spesifikasi,
            $row->jenisBarang?->jenis ?? '-',
            $row->jenisBarang?->kategori ?? '-',
            $row->jenisBarang?->nama_barang ?? '-',
            $row->gedung?->nama_gedung ?? '-',
            $row->ruangan?->nama_ruangan ?? '-',
            $row->tahun_perolehan,
            $row->nilai,
            $row->kelayakan,
            $row->keterangan_kelayakan,
            $row->status,
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A4:O4')->getFont()->setBold(true);
        $sheet->getStyle('A4:O4')->getAlignment()
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

                $sheet->mergeCells('A1:O1');
                $sheet->mergeCells('A2:O2');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getFont()->setBold(true);

                $sheet->getStyle('A1:O2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A4:O{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
