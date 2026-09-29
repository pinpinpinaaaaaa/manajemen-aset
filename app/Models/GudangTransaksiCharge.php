<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangTransaksiCharge extends Model
{
    protected $table = 'gudang_transaksi_charge';

    protected $fillable = ['id_transaksi', 'nama', 'jumlah'];

    public function transaksi()
    {
        return $this->belongsTo(GudangTransaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
