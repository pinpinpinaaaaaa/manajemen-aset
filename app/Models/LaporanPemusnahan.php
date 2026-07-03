<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class LaporanPemusnahan extends BaseModel
{
    use HasFactory;

    protected $table = 'laporan_pemusnahan';
    protected $primaryKey = 'id_pemusnahan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pemusnahan',
        'id_aset',
        'id_ruangan',
        'id_gedung',
        'tanggal_pemusnahan',
        'metode',
        'decision_status',
        'status',
        'catatan',
        'biaya_keluar',
        'nilai_masuk',
        'requested_by',
        'decided_by',
        'decided_at',
        'lampiran',
        'pelaksana_type',
        'id_vendor',
    ];

    protected $casts = [
        'tanggal_pemusnahan' => 'date',
        'decided_at' => 'datetime',
    ];

    // === Relasi ===
    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'id_gedung');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }


    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset');
    }
    
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by', 'id_user');
    }

}
