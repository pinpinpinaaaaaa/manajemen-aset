<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Maintenance extends BaseModel
{
    protected $table = 'maintenance';
    protected $primaryKey = 'id_maintenance';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_maintenance',
        'id_ruangan',
        'id_gedung',
        'tanggal_laporan',
        'catatan',
        'decision_status',
        'biaya_total',
        'requested_by',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'tanggal_laporan' => 'datetime',
        'decided_at' => 'datetime',
    ];
    // 🔗 Relasi
    public function details()
    {
        return $this->hasMany(
            MaintenanceDetail::class,
            'id_maintenance',
            'id_maintenance'
        );
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'id_gedung', 'id_gedung');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function requester()
    {
        return $this->belongsTo(User::class,'requested_by');
    }

    public function decider()
    {
        return $this->belongsTo(User::class,'decided_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'decided_by', 'id_user');
    }

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

}
