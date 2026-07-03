<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanKendaraanItem extends Model
{
    protected $table = 'permintaan_kendaraan_items';

    protected $fillable = [
        'detail_id',
        'id_kendaraan'
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan');
    }

    public function detail()
    {
        return $this->belongsTo(PermintaanKendaraanDetail::class);
    }
}