<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanKendaraanDetail extends Model
{
    protected $table = 'permintaan_kendaraan_detail';

    protected $fillable = [
        'id_permohonan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah',
        'jam_mulai',
        'jam_selesai',
        'keperluan',
        'tempat_jemput',
        'tempat_tujuan',
        'catatan',
        'surat_tugas'
    ];

    // 🔗 ke master
    public function permintaan()
    {
        return $this->belongsTo(PermintaanKendaraan::class, 'id_permohonan', 'id_permohonan');
    }

    // 🔗 ke items
    public function items()
    {
        return $this->hasMany(PermintaanKendaraanItem::class, 'detail_id');
    }
}