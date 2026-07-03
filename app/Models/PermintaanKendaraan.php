<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanKendaraan extends Model
{
    protected $table = 'permintaan_kendaraan';
    protected $primaryKey = 'id_permohonan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_permohonan',
        'nama',
        'email',
        'id_divisi',
        'status',
        'catatan'
    ];

    // 🔗 Relasi ke detail
    public function details()
    {
        return $this->hasMany(PermintaanKendaraanDetail::class, 'id_permohonan', 'id_permohonan');
    }

    // optional (kalau mau langsung akses semua kendaraan)
    public function items()
    {
        return $this->hasManyThrough(
            PermintaanKendaraanItem::class,
            PermintaanKendaraanDetail::class,
            'id_permohonan', // FK di detail
            'detail_id',     // FK di items
            'id_permohonan', // PK di master
            'id'             // PK di detail
        );
    }

    public function divisi()
    {
        return $this->belongsTo(
            \App\Models\Divisi::class,
            'id_divisi',   // foreign key di tabel permintaan_kendaraan
            'id_divisi'    // primary key di tabel divisi
        );
    }
}