<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class RkatRealisasiDetailSheet implements WithEvents, WithTitle
{
    private const HEADER_COLOR = 'FF1E3A5F';

    public function __construct(private int $tahun, private Collection $realisasi) {}

    public function title(): string { return 'Realisasi Detail'; }

    public function registerEvents(): array
    {
        return [AfterSheet::class => fn(AfterSheet $e) => $this->build($e->sheet->getDelegate())];
    }

    private function build(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws): void
    {
        // ── Judul ────────────────────────────────────────────────────────────
        $ws->setCellValue('A1', "REALISASI DETAIL RKAT {$this->tahun}");
        $ws->mergeCells('A1:J1');
        $ws->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $ws->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Header ───────────────────────────────────────────────────────────
        $headers = ['No','Tanggal','Kode COA','Kode Kegiatan','Jenis Pengeluaran',
                    'Uraian Program Kerja','Nama Kegiatan','Deskripsi','Jenis','Jumlah (Rp)'];
        foreach ($headers as $i => $h) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $ws->setCellValue("{$col}3", $h);
        }

        $ws->getStyle('A3:J3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::HEADER_COLOR]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                             'color'       => ['argb' => 'FFFFFFFF']]],
        ]);
        $ws->getRowDimension(3)->setRowHeight(28);

        // ── Data ─────────────────────────────────────────────────────────────
        $row   = 4;
        $total = 0.0;

        // Kelompokkan per bulan
        $byMonth = $this->realisasi->groupBy(fn($r) => \Carbon\Carbon::parse($r->tanggal)->format('Y-m'));
        $months  = $byMonth->keys()->sort()->values();

        foreach ($months as $ym) {
            [$y, $m] = explode('-', $ym);
            $label = \Carbon\Carbon::createFromDate($y, $m, 1)->translatedFormat('F Y');

            // Baris sub-header bulan
            $ws->setCellValue("A{$row}", $label);
            $ws->mergeCells("A{$row}:J{$row}");
            $ws->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2F0FB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);
            $row++;

            $monthTotal = 0.0;
            $no = 1;
            foreach ($byMonth[$ym] as $r) {
                $jenis = $r->jenis === 'masuk' ? 'Masuk' : 'Keluar';
                $ws->setCellValue("A{$row}", $no++);
                $ws->setCellValue("B{$row}", \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y'));
                $ws->setCellValue("C{$row}", $r->anggaran?->kode_coa ?? '');
                $ws->setCellValue("D{$row}", $r->anggaran?->kode_kegiatan ?? '');
                $ws->setCellValue("E{$row}", $r->anggaran?->coa_pos ?? '');
                $ws->setCellValue("F{$row}", $r->anggaran?->coa_sub ?? '');
                $ws->setCellValue("G{$row}", $r->anggaran?->nama_kegiatan ?? '');
                $ws->setCellValue("H{$row}", $r->deskripsi ?? '');
                $ws->setCellValue("I{$row}", $jenis);
                $ws->setCellValue("J{$row}", (float)$r->jumlah);

                $ws->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $ws->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Warna baris keluar vs masuk
                $bg = $r->jenis === 'masuk' ? 'FFE8F5E9' : 'FFFFFFFF';
                $ws->getStyle("A{$row}:J{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bg);

                $monthTotal += ($r->jenis === 'keluar') ? (float)$r->jumlah : 0;
                $total      += (float)$r->jumlah;
                $row++;
            }

            // Sub-total per bulan
            $ws->setCellValue("I{$row}", "Sub-total {$label}");
            $ws->setCellValue("J{$row}", $monthTotal);
            $ws->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['bold' => true, 'italic' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCFE2F3']],
            ]);
            $ws->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $ws->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }

        if ($this->realisasi->isEmpty()) {
            $ws->setCellValue("A{$row}", 'Belum ada data realisasi.');
            $ws->mergeCells("A{$row}:J{$row}");
            $ws->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        // ── Grand total ───────────────────────────────────────────────────────
        $ws->setCellValue("I{$row}", 'TOTAL REALISASI KELUAR');
        $ws->setCellValue("J{$row}", $this->realisasi->where('jenis', 'keluar')->sum('jumlah'));
        $ws->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0E7490']],
        ]);
        $ws->getStyle("I{$row}:J{$row}")->getFont()->getColor()->setARGB('FFFFFFFF');
        $ws->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $ws->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ── Border seluruh area ───────────────────────────────────────────────
        $ws->getStyle("A3:J{$row}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // ── Lebar kolom ───────────────────────────────────────────────────────
        $ws->getColumnDimension('A')->setWidth(5);
        $ws->getColumnDimension('B')->setWidth(12);
        $ws->getColumnDimension('C')->setWidth(12);
        $ws->getColumnDimension('D')->setWidth(16);
        $ws->getColumnDimension('E')->setWidth(24);
        $ws->getColumnDimension('F')->setWidth(28);
        $ws->getColumnDimension('G')->setWidth(30);
        $ws->getColumnDimension('H')->setWidth(28);
        $ws->getColumnDimension('I')->setWidth(10);
        $ws->getColumnDimension('J')->setWidth(16);

        $ws->freezePane('A4');
    }
}
