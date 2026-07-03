<?php

namespace App\Exports;

use App\Models\Ekspedisi;
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

class EkspedisiExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell,
    WithStyles,
    WithTitle,
    WithChunkReading
{
    protected $judul = 'LAPORAN RIWAYAT EKSPEDISI';
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function title(): string
    {
        return 'Riwayat Ekspedisi';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    /* ================= QUERY ================= */

    public function query()
    {
        $q = Ekspedisi::query()
            ->with([
                'dokumen',
                'barang',
                'pengiriman'
            ])
            ->where(function ($x) {
                $x->where('decision_status', 'ditolak')
                  ->orWhereHas('pengiriman', function ($q2) {
                      $q2->where('status_pengiriman', 'selesai');
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
        return array_map('strtoupper', [
            'ID EKSPEDISI',
            'DATA PENGAJU',
            'DATA PENGIRIM',
            'DATA PENERIMA',
            'DETAIL ISI',
            'STATUS APPROVAL',
            'STATUS PENGIRIMAN',
            'DISETUJUI OLEH',
            'TANGGAL APPROVAL',
            'TANGGAL KIRIM',
            'TANGGAL TERIMA',
            'CREATED AT',
        ]);
    }

    /* ================= MAP ================= */

    public function map($row): array
    {
        $dokumen = $row->dokumen->map(fn($d) =>
            'Dokumen: '.$d->nama_dokumen
        );

        $barang = $row->barang->map(fn($b) =>
            'Barang: '.$b->nama_barang.' ('.$b->jumlah.')'
        );

        $detail = $dokumen->merge($barang)->implode(', ');

        $pengiriman = $row->pengiriman;

        $pengaju =
            "Nama : {$row->nama_pengaju}\n".
            "Email : ".($row->email_pengaju ?? '-')."\n".
            "Divisi : ".($row->divisi_pengaju?->nama_divisi ?? '-');

        $pengirim =
            "Nama : {$row->nama_pengirim}\n".
            "Email : ".($row->email_pengirim ?? '-')."\n".
            "HP : ".($row->no_hp_pengirim ?? '-')."\n".
            "Divisi : ".($row->divisi_pengirim?->nama_divisi ?? '-');

        $penerima =
            "Nama : {$row->nama_penerima}\n".
            "Email : ".($row->email_penerima ?? '-')."\n".
            "HP : ".($row->no_hp_penerima ?? '-')."\n".
            "Instansi : ".($row->instansi_penerima ?? '-')."\n".
            "Alamat : ".($row->alamat_penerima ?? '-');

        return [
            $row->id_ekspedisi,
            $pengaju,
            $pengirim,
            $penerima,
            $detail ?: '-',
            $row->decision_status ?? '-',
            $pengiriman->status_pengiriman ?? '-',
            $row->approved_by ?? '-',
            $row->approved_at,
            $pengiriman->waktu_dikirim ?? null,
            $pengiriman->waktu_diterima ?? null,
            $row->created_at,
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

                $sheet->getStyle("A5:L{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}