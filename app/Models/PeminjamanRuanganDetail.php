<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class PeminjamanRuanganDetail extends BaseModel
{
    use HasFactory;

    protected $table = 'peminjaman_ruangan_detail';

    protected $fillable = [
        'id_peminjaman',
        'id_gedung',
        'id_ruangan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'catatan'
    ];

    // === RELASI ===

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanRuangan::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function aset()
    {
        return $this->hasMany(PeminjamanRuanganAset::class, 'detail_id');
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'id_gedung', 'id_gedung');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

}
