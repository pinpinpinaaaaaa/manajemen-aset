<?php

namespace App\Exports;

use App\Models\PemindahanAset;
use App\Models\PemindahanAsetDetail;
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

class PemindahanAsetExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN PEMINDAHAN ASET';
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function title(): string
    {
        return $this->judul;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = PemindahanAsetDetail::query()
            ->with([
                'pemindahan',
                'aset:id_aset,kode_aset,nama_aset',
                'gedungAsal:id_gedung,nama_gedung',
                'ruanganAsal:id_ruangan,nama_ruangan',
                'gedungTujuan:id_gedung,nama_gedung',
                'ruanganTujuan:id_ruangan,nama_ruangan',
                'vendor:id_vendor,nama_perusahaan',
                'pemindahan.requester:id_user,name',
                'pemindahan.approver:id_user,name',
            ]);

        if ($this->start) {
            $q->whereHas('pemindahan', function ($x) {
                $x->whereDate('created_at', '>=', $this->start);
            });
        }

        if ($this->end) {
            $q->whereHas('pemindahan', function ($x) {
                $x->whereDate('created_at', '<=', $this->end);
            });
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

            'ID Pemindahan',

            'Kode Aset',
            'Nama Aset',

            'Gedung Asal',
            'Ruangan Asal',

            'Gedung Tujuan',
            'Ruangan Tujuan',

            'Status Approval',
            'Status Pemindahan',

            'Biaya',
            'Pelaksana',

            'Vendor',

            'Requester',
            'Approver',

            'Tanggal Pengajuan',
            'Tanggal Approval',

            'Alasan',
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        return [

            $row->id_pemindahan,

            $row->aset?->kode_aset ?? '-',
            $row->aset?->nama_aset ?? '-',

            $row->gedungAsal?->nama_gedung ?? '-',
            $row->ruanganAsal?->nama_ruangan ?? '-',

            $row->gedungTujuan?->nama_gedung ?? '-',
            $row->ruanganTujuan?->nama_ruangan ?? '-',

            $row->pemindahan?->decision_status ?? '-',
            $row->status,

            $row->biaya,

            ucfirst($row->pelaksana_type),

            $row->vendor?->nama_perusahaan ?? '-',

            $row->pemindahan?->requester?->name ?? '-',
            $row->pemindahan?->approver?->name ?? '-',

            optional($row->pemindahan?->created_at)->format('d-m-Y H:i'),

            optional($row->pemindahan?->decided_at)->format('d-m-Y H:i'),

            $row->pemindahan?->alasan,
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:Q5')->getFont()->setBold(true);
        $sheet->getStyle('A5:Q5')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    /* ================= HEADER / BORDER ================= */

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

                $sheet->mergeCells('A1:Q1');
                $sheet->mergeCells('A2:Q2');
                $sheet->mergeCells('A3:Q3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sheet->getStyle('A1:T3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A5:Q{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
