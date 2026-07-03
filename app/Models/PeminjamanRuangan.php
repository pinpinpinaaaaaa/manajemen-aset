<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class PeminjamanRuangan extends BaseModel
{
    use HasFactory;

    protected $table = 'peminjaman_ruangan';
    protected $primaryKey = 'id_peminjaman';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_peminjaman',
        'nama_pengaju',
        'email_pengaju',
        'id_divisi',
        'jenis_kegiatan',
        'nama_kegiatan',
        'peserta_rapat',
        'catatan',
        'decision_status',
        'status',
        'decided_by',
        'decided_at',
        'lampiran'
    ];

    // Relasi ke detail ruangan
    public function details()
    {
        return $this->hasMany(PeminjamanRuanganDetail::class, 'id_peminjaman', 'id_peminjaman');
    }

    // Relasi ke konsumsi
    public function konsumsi()
    {
        return $this->hasMany(PeminjamanRuanganKonsumsi::class, 'id_peminjaman', 'id_peminjaman');
    }

    // Relasi ke divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function approver()
    {
        return $this->belongsTo(User::class,'decided_by','id_user');
    }
}
