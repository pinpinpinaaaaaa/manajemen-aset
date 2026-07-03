<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class PeminjamanAsetDetail extends BaseModel
{
    protected $table = 'peminjaman_aset_detail';

    protected $fillable = [
        'id_peminjaman', 
        'id_aset',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_dikembalikan',
        'status_pengembalian',
        'kondisi_kembali',
        'catatan_pengembalian'
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanAset::class, 'id_peminjaman', 'id_peminjaman');
    }
}
