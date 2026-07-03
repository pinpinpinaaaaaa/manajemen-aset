<?php

namespace App\Exports;

use App\Models\PeminjamanAset;
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

class PeminjamanAsetExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN RIWAYAT PEMINJAMAN ASET';
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
        $q = PeminjamanAset::query()
            ->with([
                'divisi:id_divisi,nama_divisi',
                'details.aset',
                'approver:id_user,name'
            ])
            ->where(function ($x) {

                // Ditolak
                $x->where('decision_status', 'ditolak')

                    // Semua item sudah dikembalikan
                    ->orWhereDoesntHave('details', function ($q) {
                        $q->where('status_pengembalian', '!=', 'dikembalikan');
                    });
            });

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
            'Detail Aset',
            'Total Item',
            'Total Dipinjam',
            'Total Dikembalikan',
            'Decision Status',
            'Decider',
            'Decided At',
            'Alasan',
            'Catatan',
            'Created At',
            'Updated At',
        ];
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        $totalItem = $row->details->sum('jumlah');

        $totalDipinjam = $row->details
            ->where('status_pengembalian', 'dipinjam')
            ->sum('jumlah');

        $totalDikembalikan = $row->details
            ->where('status_pengembalian', 'dikembalikan')
            ->sum('jumlah');

        $detailAset = $row->details
            ->map(function ($d) {

                $status = match ($d->status_pengembalian) {
                    'menunggu' => 'Menunggu',
                    'dipinjam' => 'Dipinjam',
                    'dikembalikan' => 'Dikembalikan',
                    default => '-'
                };

                return
                    ($d->aset->nama_aset ?? '-') .
                    ' (Qty: ' . $d->jumlah . ')' .
                    ' - ' . $status .
                    ($d->kondisi_kembali
                        ? ' [' . $d->kondisi_kembali . ']'
                        : '');
            })
            ->implode("\n");

        return [
            $row->id_peminjaman,
            $row->nama_pengaju ?? '-',
            $row->email_pengaju ?? '-',
            $row->divisi?->nama_divisi ?? '-',
            $detailAset,
            $totalItem,
            $totalDipinjam,
            $totalDikembalikan,
            ucfirst($row->decision_status),
            $row->approver?->name ?? '-',
            optional($row->decided_at)?->format('d-m-Y H:i'),
            $row->alasan ?? '-',
            $row->catatan ?? '-',
            optional($row->created_at)?->format('d-m-Y H:i'),
            optional($row->updated_at)?->format('d-m-Y H:i'),
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:O5')->getFont()->setBold(true);
        $sheet->getStyle('A5:O5')
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

                $sheet->mergeCells('A1:O1');
                $sheet->mergeCells('A2:O2');
                $sheet->mergeCells('A3:O3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:O3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A5:O{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}