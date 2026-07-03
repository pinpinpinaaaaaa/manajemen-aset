<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class GudangBarang extends BaseModel
{
    use HasFactory;

    protected $table = 'gudang_barang';
    protected $primaryKey = 'id_barang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_barang',
        'nama_barang',
        'jenis',
        'satuan',
        'konversi_satuan',
        'satuan_dasar',
        'limit_stok',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'stok_akhir',
        'foto_produk',
        'keterangan',
        'stok_akhir',
        'stok_dipesan',
    ];

    // Relasi ke transaksi
    public function transaksi()
    {
        return $this->hasMany(GudangTransaksi::class, 'id_barang', 'id_barang');
    }

    // Relasi ke rekap
    public function rekap()
    {
        return $this->hasMany(GudangRekapBulanan::class, 'id_barang', 'id_barang');
    }
}
