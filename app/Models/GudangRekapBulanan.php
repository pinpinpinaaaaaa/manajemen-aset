<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class GudangRekapBulanan extends BaseModel
{
    use HasFactory;

    protected $table = 'gudang_rekap_bulanan';
    protected $primaryKey = 'id_rekap';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_rekap',
        'id_barang',
        'bulan',
        'tahun',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'stok_akhir',
    ];

    public function barang()
    {
        return $this->belongsTo(GudangBarang::class, 'id_barang', 'id_barang');
    }
}
