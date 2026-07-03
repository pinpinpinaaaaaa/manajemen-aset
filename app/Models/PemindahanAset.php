<?php

namespace App\Models;

use App\Models\BaseModel;

class PemindahanAset extends BaseModel
{
    protected $table = 'pemindahan_aset';

    protected $primaryKey = 'id_pemindahan';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pemindahan',
        'alasan',
        'decision_status',
        'requested_by',
        'decided_by',
        'decided_at',
        'catatan',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];


    /* ================= RELATIONS ================= */
    public function details()
    {
        return $this->hasMany(
            PemindahanAsetDetail::class,
            'id_pemindahan',
            'id_pemindahan'
        );
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'decided_by', 'id_user');
    }
}
