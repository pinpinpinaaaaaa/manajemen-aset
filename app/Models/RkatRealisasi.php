<?php

namespace App\Models;

class RkatRealisasi extends BaseModel
{
    protected $table = 'rkat_realisasi';

    protected $fillable = [
        'rkat_anggaran_id',
        'tanggal',
        'deskripsi',
        'jumlah',
        'jenis',
        'sumber_type',
        'sumber_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah'  => 'decimal:2',
    ];

    public function anggaran()
    {
        return $this->belongsTo(RkatAnggaran::class, 'rkat_anggaran_id');
    }

    public function sumber()
    {
        return $this->morphTo('sumber');
    }
}
