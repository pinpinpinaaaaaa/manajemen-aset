<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class GudangTransaksiDetail extends BaseModel
{
    use HasFactory;

    protected $table = 'gudang_transaksi_detail';

    protected $primaryKey = 'id'; // auto increment

    protected $fillable = [
        'id_transaksi',
        'id_barang',
        'jumlah_input',
        'satuan',
        'konversi_pakai',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Detail -> Header transaksi
    public function transaksi()
    {
        return $this->belongsTo(
            GudangTransaksi::class,
            'id_transaksi',
            'id_transaksi'
        );
    }

    // Detail -> Barang gudang
    public function barang()
    {
        return $this->belongsTo(
            GudangBarang::class,
            'id_barang',
            'id_barang'
        );
    }
}
