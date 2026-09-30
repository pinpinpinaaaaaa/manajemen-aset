<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RekapGudangService;

class RekapBulanan extends Command
{
    protected $signature = 'rekap:bulanan {bulan? : Bulan dalam format Y-m, contoh 2026-08 (default: bulan lalu)}';
    protected $description = 'Proses rekap stok bulanan gudang';

    public function handle(RekapGudangService $service): int
    {
        $bulan = $this->argument('bulan') ?? now()->subMonth()->format('Y-m');

        try {
            $result = $service->rekap($bulan);
            $this->info("Rekap {$result['bulan']} selesai — {$result['jumlah_barang']} barang diproses.");
            return self::SUCCESS;
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
