<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GudangOpnameDetail extends Model
{
    use HasFactory;

    protected $table = 'gudang_opname_detail';

    protected $fillable = [
        'id_opname',
        'id_barang',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'catatan',
    ];

    // ================= RELASI =================

    // ke header
    public function header()
    {
        return $this->belongsTo(GudangOpnameHeader::class, 'id_opname');
    }

    // ke barang
    public function barang()
    {
        return $this->belongsTo(GudangBarang::class, 'id_barang', 'id_barang');
    }
}