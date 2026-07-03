<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\AsetLog;
use Illuminate\Http\Request;

class AsetLogController extends Controller
{
    /**
     * 📜 Halaman Riwayat Aset (mirip dashboard ruangan)
     */
    public function index()
    {
        $logs = AsetLog::with('aset')
            ->timeline()
            ->paginate(20);

        return view('aset_log.index', compact('logs'));
    }

    /**
     * 📜 Timeline riwayat untuk 1 aset
     */
    public function byAset($id_aset)
    {
        $aset = Aset::findOrFail($id_aset);

        $logs = AsetLog::where('id_aset', $id_aset)
            ->timeline()
            ->get();

        return view('aset_log.by_aset', compact('aset', 'logs'));
    }
}
