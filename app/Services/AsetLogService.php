<?php

namespace App\Services;

use App\Models\AsetLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AsetLogService
{
    public static function log(
        string $id_aset,
        string $tipe,
        ?string $refId,
        string $keterangan,
        ?Carbon $tanggal = null
    ): void {
        AsetLog::create([
            'id_aset' => $id_aset,
            'tipe' => $tipe,
            'ref_id' => $refId,
            'keterangan' => $keterangan,
            'tanggal_kejadian' => $tanggal ?? now(),
            'id_user' => Auth::check() ? Auth::id() : null,
        ]);
    }
}
