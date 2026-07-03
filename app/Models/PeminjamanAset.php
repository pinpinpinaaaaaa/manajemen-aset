<?php

namespace App\Models;

class PeminjamanAset extends BaseModel
{
    protected $table = 'peminjaman_aset';
    protected $primaryKey = 'id_peminjaman';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_peminjaman',
        'nama_pengaju',
        'email_pengaju',
        'id_divisi',
        'alasan',
        'decision_status',
        'status',
        'decided_by',
        'decided_at',
        'lampiran',
        'catatan'
    ];

    public function details()
    {
        return $this->hasMany(PeminjamanAsetDetail::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class, 'kategori', 'kategori');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'decided_by', 'id_user');
    }
}
