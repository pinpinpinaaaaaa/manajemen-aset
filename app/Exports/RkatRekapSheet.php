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

class RkatRekapSheet implements WithEvents, WithTitle
{
    private const HEADER_COLOR  = 'FF1E3A5F';
    private const GREEN_FILL    = 'FFD1FAE5'; // efisiensi
    private const RED_FILL      = 'FFFEE2E2'; // kelebihan
    private const GREEN_TOTAL   = 'FF065F46';
    private const RED_TOTAL     = 'FF991B1B';

    private const BULAN_LABEL = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    private const BULAN_KEYS  = ['jan','feb','mar','apr','mei','jun','jul','agu','sep','okt','nov','des'];

    public function __construct(
        private int $tahun,
        private Collection $anggaran,
        private Collection $realisasi
    ) {}

    public function title(): string { return 'Rekap Anggaran'; }

    public function registerEvents(): array
    {
        return [AfterSheet::class => fn(AfterSheet $e) => $this->build($e->sheet->getDelegate())];
    }

    private function build(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws): void
    {
        $currentMonth = (int) now()->format('m');

        // ── Judul ────────────────────────────────────────────────────────────
        $ws->setCellValue('A1', "REKAP REALISASI RKAT {$this->tahun}");
        $ws->mergeCells('A1:Q1');
        $ws->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $ws->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ws->setCellValue('A2', 'Kolom Sisa: Hijau = efisiensi (sisa ≥ 0)   |   Merah = kelebihan (sisa < 0)');
        $ws->mergeCells('A2:Q2');
        $ws->getStyle('A2')->getFont()->setItalic(true)->setSize(9)->getColor()->setARGB('FF6B7280');
        $ws->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Header per-bulan di baris 3 ───────────────────────────────────────
        // Row 3 = group labels: No, Pos, Anggaran, kemudian bulan-bulan, Total s/d bln ini, Grand Total, Sisa
        // Struktur:
        //  A=No, B=Kode COA, C=Nama Kegiatan, D=Jenis Pengeluaran,
        //  E=Anggaran, F..Q = Jan..Des (Rencana+Realisasi per bulan = 2 subkolom)
        //  ... terlalu lebar. Pakai 1 baris header dengan kolom vertikal:
        //  A=No, B=Kode COA, C=Jenis Pengeluaran, D=Nama Kegiatan, E=Anggaran
        //  F..Q = bulan Jan-Des (nilai realisasi saja)
        //  R=Total Realisasi s/d Bulan Ini, S=Total Rencana, T=Sisa

        $headers = ['No', 'Kode COA', 'Jenis Pengeluaran', 'Nama Kegiatan', 'Anggaran (Rp)'];
        foreach (self::BULAN_LABEL as $b) {
            $headers[] = $b;
        }
        $headers[] = 'Total Realisasi';
        $headers[] = 'Total Rencana';
        $headers[] = 'Sisa';

        $totalCols = count($headers); // 5 + 12 + 3 = 20
        $lastColIdx = $totalCols;
        $lastCol    = Coordinate::stringFromColumnIndex($lastColIdx);

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

        // ── Hitung realisasi keluar per anggaran per bulan ───────────────────
        // Group realisasi: [anggaran_id][bulan_index] => total keluar
        $realisasiMap = [];
        foreach ($this->realisasi as $r) {
            if ($r->jenis !== 'keluar') continue;
            $bulanIdx = (int) \Carbon\Carbon::parse($r->tanggal)->format('n') - 1; // 0-based
            $realisasiMap[$r->rkat_anggaran_id][$bulanIdx] =
                ($realisasiMap[$r->rkat_anggaran_id][$bulanIdx] ?? 0) + (float)$r->jumlah;
        }

        // ── Data ─────────────────────────────────────────────────────────────
        $row            = 5;
        $sumAnggaran    = 0;
        $sumBulan       = array_fill(0, 12, 0.0);
        $sumTotalReal   = 0;
        $sumTotalRenc   = 0;
        $sumSisa        = 0;

        foreach ($this->anggaran as $i => $pos) {
            // Realisasi per bulan
            $realBulan = [];
            for ($m = 0; $m < 12; $m++) {
                $realBulan[$m] = $realisasiMap[$pos->id][$m] ?? 0;
            }

            // Total realisasi s/d bulan ini (bulan berjalan = currentMonth, 1-based → idx = currentMonth-1)
            $totalReal = 0;
            for ($m = 0; $m < $currentMonth; $m++) {
                $totalReal += $realBulan[$m];
            }

            // Total rencana s/d bulan ini
            $totalRenc = 0;
            foreach (self::BULAN_KEYS as $mi => $k) {
                if ($mi < $currentMonth) {
                    $totalRenc += (float)($pos->{"rencana_{$k}"} ?? 0);
                }
            }

            $anggaran = (float)$pos->anggaran;
            $sisa     = $anggaran - $totalReal;

            $ws->setCellValue("A{$row}", $i + 1);
            $ws->setCellValue("B{$row}", $pos->kode_coa ?? '');
            $ws->setCellValue("C{$row}", $pos->coa_pos);
            $ws->setCellValue("D{$row}", $pos->nama_kegiatan);
            $ws->setCellValue("E{$row}", $anggaran);

            for ($m = 0; $m < 12; $m++) {
                $col = Coordinate::stringFromColumnIndex(6 + $m);
                $ws->setCellValue("{$col}{$row}", $realBulan[$m] > 0 ? $realBulan[$m] : '');
                $sumBulan[$m] += $realBulan[$m];
            }

            // Kolom R=18, S=19, T=20
            $ws->setCellValue("R{$row}", $totalReal > 0 ? $totalReal : '');
            $ws->setCellValue("S{$row}", $totalRenc > 0 ? $totalRenc : '');
            $ws->setCellValue("T{$row}", $sisa);

            // Format angka
            $ws->getStyle("E{$row}:T{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $ws->getStyle("E{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Warna hanya kolom Sisa
            $bg = $sisa >= 0 ? self::GREEN_FILL : self::RED_FILL;
            $ws->getStyle("T{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bg);

            $sumAnggaran  += $anggaran;
            $sumTotalReal += $totalReal;
            $sumTotalRenc += $totalRenc;
            $sumSisa      += $sisa;
            $row++;
        }

        if ($this->anggaran->isEmpty()) {
            $ws->setCellValue("A{$row}", 'Belum ada data anggaran.');
            $ws->mergeCells("A{$row}:T{$row}");
            $ws->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        // ── Baris total ───────────────────────────────────────────────────────
        $ws->setCellValue("D{$row}", 'TOTAL');
        $ws->setCellValue("E{$row}", $sumAnggaran);

        for ($m = 0; $m < 12; $m++) {
            $col = Coordinate::stringFromColumnIndex(6 + $m);
            $ws->setCellValue("{$col}{$row}", $sumBulan[$m] > 0 ? $sumBulan[$m] : '');
        }

        $ws->setCellValue("R{$row}", $sumTotalReal > 0 ? $sumTotalReal : '');
        $ws->setCellValue("S{$row}", $sumTotalRenc > 0 ? $sumTotalRenc : '');
        $ws->setCellValue("T{$row}", $sumSisa);

        $totalBg = $sumSisa >= 0 ? self::GREEN_TOTAL : self::RED_TOTAL;
        $ws->getStyle("A{$row}:T{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $totalBg]],
        ]);
        $ws->getStyle("E{$row}:T{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $ws->getStyle("E{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ── Catatan bulan terkini ─────────────────────────────────────────────
        $row++;
        $bulanKini = \Carbon\Carbon::now()->translatedFormat('F Y');
        $ws->setCellValue("A{$row}", "* Kolom realisasi s/d {$bulanKini} (bulan ke-{$currentMonth})");
        $ws->mergeCells("A{$row}:T{$row}");
        $ws->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9)->getColor()->setARGB('FF6B7280');

        // ── Border ────────────────────────────────────────────────────────────
        $ws->getStyle("A4:T" . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // ── Lebar kolom ───────────────────────────────────────────────────────
        $ws->getColumnDimension('A')->setWidth(5);
        $ws->getColumnDimension('B')->setWidth(11);
        $ws->getColumnDimension('C')->setWidth(22);
        $ws->getColumnDimension('D')->setWidth(28);
        $ws->getColumnDimension('E')->setWidth(16);
        foreach (range(6, 17) as $ci) {
            $ws->getColumnDimension(Coordinate::stringFromColumnIndex($ci))->setWidth(11);
        }
        $ws->getColumnDimension('R')->setWidth(15);
        $ws->getColumnDimension('S')->setWidth(14);
        $ws->getColumnDimension('T')->setWidth(15);

        $ws->freezePane('A5');
    }
}
