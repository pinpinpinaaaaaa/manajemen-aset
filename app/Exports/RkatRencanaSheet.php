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

class RkatRencanaSheet implements WithEvents, WithTitle
{
    private const HEADER_COLOR = 'FF0E7490';
    private const BULAN = ['Januari','Februari','Maret','April','Mei','Juni',
                            'Juli','Agustus','September','Oktober','November','Desember'];
    private const BULAN_KEYS = ['jan','feb','mar','apr','mei','jun','jul','agu','sep','okt','nov','des'];

    public function __construct(private int $tahun, private Collection $anggaran) {}

    public function title(): string { return 'Rencana Anggaran'; }

    public function registerEvents(): array
    {
        return [AfterSheet::class => fn(AfterSheet $e) => $this->build($e->sheet->getDelegate())];
    }

    private function build(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws): void
    {
        // ── Judul ────────────────────────────────────────────────────────────
        // ── Header kolom (baris 4) ────────────────────────────────────────────
        // A=No B=KodeCOA C=LapKeu D=KodeKegiatan E=JenisPengeluaran F=Uraian
        // G=NamaKegiatan H=Anggaran I..T=Jan-Des U=TotalRencana
        $headers = ['No','Kode COA','Lap. Keu.','Kode Kegiatan','Jenis Pengeluaran','Uraian Program Kerja',
                    'Nama Kegiatan','Anggaran', ...self::BULAN, 'Total Rencana'];
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));

        $ws->setCellValue('A1', "RENCANA ANGGARAN RKAT {$this->tahun}");
        $ws->mergeCells("A1:{$lastCol}1");
        $ws->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $ws->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ws->setCellValue('A2', 'nilai dalam satuan rupiah');
        $ws->mergeCells("A2:{$lastCol}2");
        $ws->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $ws->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($headers as $i => $h) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $ws->setCellValue("{$col}4", $h);
        }

        $ws->getStyle("A4:{$lastCol}4")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::HEADER_COLOR]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                             'color'       => ['argb' => 'FFFFFFFF']]],
        ]);
        $ws->getRowDimension(4)->setRowHeight(32);

        // ── Data ─────────────────────────────────────────────────────────────
        $row = 5;
        $totalAnggaran = 0;
        $totalRencana  = array_fill(0, 12, 0.0);
        $grandRencana  = 0;

        foreach ($this->anggaran as $i => $pos) {
            $rencanaMonth = [];
            $sumRencana   = 0;
            foreach (self::BULAN_KEYS as $k) {
                $v = (float)($pos->{"rencana_{$k}"} ?? 0);
                $rencanaMonth[] = $v;
                $sumRencana += $v;
            }

            $ws->setCellValue("A{$row}", $i + 1);
            $ws->setCellValue("B{$row}", $pos->kode_coa ?? '');
            $ws->setCellValue("C{$row}", $pos->laporan_keuangan ?? '');
            $ws->setCellValue("D{$row}", $pos->kode_kegiatan);
            $ws->setCellValue("E{$row}", $pos->coa_pos);
            $ws->setCellValue("F{$row}", $pos->coa_sub);
            $ws->setCellValue("G{$row}", $pos->nama_kegiatan);
            $ws->setCellValue("H{$row}", (float)$pos->anggaran);

            foreach ($rencanaMonth as $mi => $v) {
                $col = Coordinate::stringFromColumnIndex(9 + $mi);
                $ws->setCellValue("{$col}{$row}", $v ?: '');
                $totalRencana[$mi] += $v;
            }
            $ws->setCellValue("{$lastCol}{$row}", $sumRencana ?: '');

            // Format angka
            $ws->getStyle("H{$row}:{$lastCol}{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $ws->getStyle("H{$row}:{$lastCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $bg = $i % 2 === 0 ? 'FFF8FAFC' : 'FFFFFFFF';
            $ws->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bg);

            $totalAnggaran += (float)$pos->anggaran;
            $grandRencana  += $sumRencana;
            $row++;
        }

        // ── Baris total ───────────────────────────────────────────────────────
        if ($this->anggaran->isNotEmpty()) {
            $ws->setCellValue("G{$row}", 'TOTAL');
            $ws->setCellValue("H{$row}", $totalAnggaran);
            foreach ($totalRencana as $mi => $v) {
                $col = Coordinate::stringFromColumnIndex(9 + $mi);
                $ws->setCellValue("{$col}{$row}", $v ?: '');
            }
            $ws->setCellValue("{$lastCol}{$row}", $grandRencana);
            $ws->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']],
            ]);
            $ws->getStyle("H{$row}:{$lastCol}{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $ws->getStyle("H{$row}:{$lastCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // ── Border ────────────────────────────────────────────────────────────
        if ($row > 5) {
            $ws->getStyle("A4:{$lastCol}" . ($row))->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        // ── Lebar kolom ───────────────────────────────────────────────────────
        $ws->getColumnDimension('A')->setWidth(5);
        $ws->getColumnDimension('B')->setWidth(11);
        $ws->getColumnDimension('C')->setWidth(9);  // Lap. Keu.
        $ws->getColumnDimension('D')->setWidth(16);
        $ws->getColumnDimension('E')->setWidth(24);
        $ws->getColumnDimension('F')->setWidth(30);
        $ws->getColumnDimension('G')->setWidth(34);
        $ws->getColumnDimension('H')->setWidth(16);
        foreach (range(9, 20) as $ci) {
            $ws->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setWidth(12);
        }
        $ws->getColumnDimension('U')->setWidth(14);

        $ws->freezePane('A5');
    }
}
