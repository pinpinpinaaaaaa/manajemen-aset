<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarangGudangDetail extends Model
{
    use HasFactory;

    protected $table = 'permintaan_barang_gudang_detail';

    protected $fillable = [
        'id_permintaan',
        'id_barang',
        'jumlah',
    ];

    public function permintaan()
    {
        return $this->belongsTo(
            PermintaanBarangGudang::class,
            'id_permintaan',
            'id_permintaan'
        );
    }

    public function barang()
    {
        return $this->belongsTo(
            GudangBarang::class,
            'id_barang',
            'id_barang'
        );
    }
}