<?php

namespace App\Models;

class RkatAnggaran extends BaseModel
{
    protected $table = 'rkat_anggaran';

    protected $fillable = [
        'tahun',
        'kode_coa',
        'laporan_keuangan',
        'kode_kegiatan',
        'coa_pos',
        'coa_sub',
        'nama_kegiatan',
        'anggaran',
        'rencana_jan', 'rencana_feb', 'rencana_mar', 'rencana_apr',
        'rencana_mei', 'rencana_jun', 'rencana_jul', 'rencana_agu',
        'rencana_sep', 'rencana_okt', 'rencana_nov', 'rencana_des',
        'created_by',
    ];

    protected $casts = [
        'tahun'       => 'integer',
        'anggaran'    => 'decimal:2',
        'rencana_jan' => 'decimal:2', 'rencana_feb' => 'decimal:2',
        'rencana_mar' => 'decimal:2', 'rencana_apr' => 'decimal:2',
        'rencana_mei' => 'decimal:2', 'rencana_jun' => 'decimal:2',
        'rencana_jul' => 'decimal:2', 'rencana_agu' => 'decimal:2',
        'rencana_sep' => 'decimal:2', 'rencana_okt' => 'decimal:2',
        'rencana_nov' => 'decimal:2', 'rencana_des' => 'decimal:2',
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
