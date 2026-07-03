<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class PengaduanKerusakanDetail extends BaseModel
{
    use HasFactory;

    protected $table = 'pengaduan_kerusakan_detail';

    protected $fillable = [
        'id_pengaduan',
        'id_aset',
        'keluhan',
        'kategori_kerusakan',
        'foto'
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Detail -> Pengaduan Kerusakan (Header)
    public function pengaduan()
    {
        return $this->belongsTo(
            PengaduanKerusakan::class,
            'id_pengaduan',
            'id_pengaduan'
        );
    }
    // Detail -> Aset
    public function aset()
    {
        return $this->belongsTo(
            Aset::class,
            'id_aset',
            'id_aset'
        );
    }

    // Detail -> Maintenance
    public function maintenance()
    {
        return $this->belongsTo(
            Maintenance::class,
            'id_maintenance',
            'id_maintenance'
        );
    }
}
