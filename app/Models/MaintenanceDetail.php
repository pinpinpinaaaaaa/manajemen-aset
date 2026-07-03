<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceDetail extends Model
{
    protected $table = 'maintenance_detail';

    protected $fillable = [
        'id_maintenance',
        'id_aset',

        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_jam',

        'kerusakan',
        'biaya',

        'foto_before',
        'foto_after',

        'catatan',
        'status',

        'kelayakan_awal',
        'keterangan_awal',
        'status_aset_awal',

        'lampiran',

        'pelaksana_type',
        'id_vendor',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function maintenance()
    {
        return $this->belongsTo(
            Maintenance::class,
            'id_maintenance',
            'id_maintenance'
        );
    }

    public function aset()
    {
        return $this->belongsTo(
            Aset::class,
            'id_aset',
            'id_aset'
        );
    }

    public function vendor()
    {
        return $this->belongsTo(
            Vendor::class,
            'id_vendor',
            'id_vendor'
        );
    }
}