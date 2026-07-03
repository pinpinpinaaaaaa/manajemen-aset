<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemindahanAsetDetail extends Model
{
    protected $table = 'pemindahan_aset_detail';

    protected $fillable = [
        'id_pemindahan',
        'id_aset',

        'from_gedung',
        'from_ruangan',

        'to_gedung',
        'to_ruangan',

        'status',
        'lampiran',
        'biaya',

        'pelaksana_type',
        'id_vendor',

        'catatan',
    ];

    public function pemindahan()
    {
        return $this->belongsTo(
            PemindahanAset::class,
            'id_pemindahan',
            'id_pemindahan'
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

    public function gedungAsal()
    {
        return $this->belongsTo(
            Gedung::class,
            'from_gedung',
            'id_gedung'
        );
    }

    public function ruanganAsal()
    {
        return $this->belongsTo(
            Ruangan::class,
            'from_ruangan',
            'id_ruangan'
        );
    }

    public function gedungTujuan()
    {
        return $this->belongsTo(
            Gedung::class,
            'to_gedung',
            'id_gedung'
        );
    }

    public function ruanganTujuan()
    {
        return $this->belongsTo(
            Ruangan::class,
            'to_ruangan',
            'id_ruangan'
        );
    }
}