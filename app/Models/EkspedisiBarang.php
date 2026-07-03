<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkspedisiBarang extends Model
{
    protected $table = 'ekspedisi_barang';

    protected $fillable = [
        'id_ekspedisi',
        'nama_barang',
        'jumlah',
        'berat',
        'satuan',
        'keterangan'
    ];

    protected $casts = [
        'berat' => 'decimal:2'
    ];

    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class, 'id_ekspedisi', 'id_ekspedisi');
    }
}