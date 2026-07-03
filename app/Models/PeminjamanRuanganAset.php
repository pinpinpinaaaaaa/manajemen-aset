<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class PeminjamanRuanganAset extends BaseModel
{
    use HasFactory;

    protected $table = 'peminjaman_ruangan_aset';

    protected $fillable = [
        'detail_id',
        'sumber_barang',
        'id_aset',
        'id_gudang',
        'jumlah',
        'id_jenis_barang'
    ];

    // === RELASI ===

    public function detail()
    {
        return $this->belongsTo(PeminjamanRuanganDetail::class, 'detail_id');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

    public function gudangBarang()
    {
        return $this->belongsTo(gudangBarang::class, 'id_gudang', 'id_gudang');
    }

}
