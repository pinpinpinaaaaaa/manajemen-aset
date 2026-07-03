<?php

namespace App\Exports;

use App\Models\LaporanPemusnahan;
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

class PemusnahanExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN PEMUSNAHAN ASET';
    protected $start;
    protected $end;

    // ✅ TERIMA FILTER
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
        return 'A5'; // turun 1 baris buat periode
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = LaporanPemusnahan::query()
            ->where(function($x){
                $x->where('status','Selesai')
                  ->orWhere('decision_status','ditolak');
            })
            ->with([
                'aset.gedung:id_gedung,nama_gedung',
                'aset.ruangan:id_ruangan,nama_ruangan',
                'vendor:id_vendor,nama_perusahaan',
                'requester:id_user,name',
                'decider:id_user,name',
            ]);

        // ✅ APPLY FILTER
        if ($this->start) {
            $q->whereDate('tanggal_pemusnahan','>=',$this->start);
        }

        if ($this->end) {
            $q->whereDate('tanggal_pemusnahan','<=',$this->end);
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
            'ID Pemusnahan','ID Aset','Nama Aset','Gedung','Ruangan',
            'Tanggal Pemusnahan','Metode','Biaya Keluar','Nilai Masuk',
            'Decision Status','Status','Pelaksana Type',
            'ID Vendor','Nama Vendor','Requester','Decider',
            'Decided At','Catatan','Lampiran','Created At','Updated At'
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        return [
            $row->id_pemusnahan,
            $row->id_aset,
            $row->aset?->nama_aset ?? '-',
            $row->aset?->gedung?->nama_gedung ?? '-',
            $row->aset?->ruangan?->nama_ruangan ?? '-',
            $row->tanggal_pemusnahan,
            $row->metode,
            $row->biaya_keluar,
            $row->nilai_masuk,
            $row->decision_status,
            $row->status,
            $row->pelaksana_type,
            $row->id_vendor,
            $row->vendor?->nama_perusahaan ?? '-',
            $row->requester?->name ?? '-',
            $row->decider?->name ?? '-',
            $row->decided_at,
            $row->catatan,
            $row->lampiran,
            $row->created_at,
            $row->updated_at
        ];
    }

    /* ================= STYLE ================= */

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A5:U5')->getFont()->setBold(true);
        $sheet->getStyle('A5:U5')
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

                $sheet->mergeCells('A1:U1');
                $sheet->mergeCells('A2:U2');
                $sheet->mergeCells('A3:U3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:U3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A5:U{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}

