<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GudangController;

class RekapBulanan extends Command
{
    protected $signature = 'rekap:bulanan';
    protected $description = 'Proses rekap stok bulanan gudang';

    public function handle()
    {
        $ctrl = new GudangController();
        $this->info($ctrl->rekapBulan());
    }
}
