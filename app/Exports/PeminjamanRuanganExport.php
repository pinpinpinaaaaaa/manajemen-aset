<?php

namespace App\Exports;

use App\Models\PeminjamanRuangan;
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

class PeminjamanRuanganExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN RIWAYAT PEMINJAMAN RUANGAN';
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function title(): string
    {
        return 'Riwayat Peminjaman';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = PeminjamanRuangan::query()
            ->where(function ($x) {
                $x->where('status', 'Selesai')
                ->orWhere('decision_status', 'ditolak');
            })
            ->with([
                'divisi:id_divisi,nama_divisi',
                'details.ruangan',
                'details.aset.aset',
                'konsumsi',
                'approver:id_user,name'
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
        return [
            'ID Peminjaman',
            'Nama Pengaju',
            'Email',
            'Divisi',

            'Detail Sesi',
            'Konsumsi',

            'Total Sesi',

            'Status',
            'Decision Status',
            'Decider',
            'Decided At',
            'Catatan',
            'Created At',
            'Updated At'
        ];
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        $totalSesi = $row->details->count();

        $tanggalMulai = $totalSesi
            ? Carbon::parse(
                $row->details->min('tanggal_mulai')
            )->format('d M Y')
            : '-';

        $tanggalSelesai = $totalSesi
            ? Carbon::parse(
                $row->details->max('tanggal_selesai')
            )->format('d M Y')
            : '-';

        // ================= DETAIL SESI =================

        $detailSesi = $row->details
            ->map(function ($d) {

                $aset = $d->aset
                    ->map(function ($a) {
                        return ($a->aset->nama_aset ?? '-') .
                            ' (' . $a->jumlah . ')';
                    })
                    ->implode(', ');

                return
                    ($d->ruangan->nama_ruangan ?? '-') .
                    "\nTanggal : " .
                    Carbon::parse($d->tanggal_mulai)->format('d-m-Y') .
                    "\nJam : {$d->jam_mulai} - {$d->jam_selesai}" .
                    "\nAset : " .
                    ($aset ?: '-');
            })
            ->implode("\n\n");
        // ================= KONSUMSI =================

        $detailKonsumsi = $row->konsumsi
            ->map(function ($k) {

                return
                    ($k->jenis_konsumsi ?? '-') .
                    ' (Qty: ' . $k->jumlah . ')';

            })
            ->implode("\n");

        return [
            $row->id_peminjaman,
            $row->nama_pengaju ?? '-',
            $row->email_pengaju ?? '-',
            $row->divisi?->nama_divisi ?? '-',

            $detailSesi,
            $detailKonsumsi,

            $totalSesi,

            $row->status ?? '-',
            ucfirst($row->decision_status),

            $row->approver?->name ?? '-',

            $row->decided_at
                ? Carbon::parse($row->decided_at)->format('d-m-Y H:i')
                : '-',

            $row->catatan ?? '-',

            optional($row->created_at)->format('d-m-Y H:i'),
            optional($row->updated_at)->format('d-m-Y H:i'),
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:N5')->getFont()->setBold(true);
        $sheet->getStyle('A5:N5')
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

                $sheet->mergeCells('A1:N1');
                $sheet->mergeCells('A2:N2');
                $sheet->mergeCells('A3:N3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:N3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A5:N{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}