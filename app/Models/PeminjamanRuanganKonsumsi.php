<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class PeminjamanRuanganKonsumsi extends BaseModel
{
    use HasFactory;

    protected $table = 'peminjaman_ruangan_konsumsi';

    protected $fillable = [
        'id_peminjaman',
        'jenis_konsumsi',
        'jumlah',
        'catatan',
    ];

    // === RELASI ===

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanRuangan::class, 'id_peminjaman', 'id_peminjaman');
    }
}
