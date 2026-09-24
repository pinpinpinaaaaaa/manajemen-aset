<?php

namespace App\Models;

class RkatAnggaran extends BaseModel
{
    protected $table = 'rkat_anggaran';

    protected $fillable = [
        'tahun',
        'kode_kegiatan',
        'coa_pos',
        'coa_sub',
        'nama_kegiatan',
        'anggaran',
        'created_by',
    ];

    protected $casts = [
        'tahun'    => 'integer',
        'anggaran' => 'decimal:2',
    ];

    public function realisasis()
    {
        return $this->hasMany(RkatRealisasi::class, 'rkat_anggaran_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
