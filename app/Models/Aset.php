<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Aset extends BaseModel
{
    use HasFactory;

    protected $table = 'aset';
    protected $primaryKey = 'id_aset';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_aset',
        'kode_aset',
        'nama_aset',
        'merk',         
        'tipe_model',  
        'spesifikasi',
        'id_jenis_barang',
        'id_gedung',
        'id_ruangan',
        'tahun_perolehan',
        'nilai',
        'kelayakan',
        'keterangan_kelayakan',
        'status',
        'foto',
        'jumlah_total',
        'jumlah_dipinjam',
        'jumlah_tersedia',
        'last_checked_at',
        'last_checked_by',
    ];

    // ================== RELASI ==================
    public function maintenanceDetails()
    {
        return $this->hasMany(
            MaintenanceDetail::class,
            'id_aset',
            'id_aset'
        );
    }

    public function pemindahanDetails()
    {
        return $this->hasMany(
            PemindahanAsetDetail::class,
            'id_aset',
            'id_aset'
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

    public function jenisBarang()
    {
        return $this->belongsTo(
            JenisBarang::class,
            'id_jenis_barang',
            'id_jenis_barang'
        );
    }

    public function laporanPemusnahan()
    {
        return $this->hasMany(LaporanPemusnahan::class, 'id_aset', 'id_aset');
    }

    public function logs()
    {
        return $this->hasMany(AsetLog::class, 'id_aset', 'id_aset');
    }

    // ================== ACCESSOR ==================

    public function getNilaiFormattedAttribute()
    {
        return $this->nilai
            ? number_format($this->nilai, 0, ',', '.')
            : '-';
    }

    public function peminjamanDetails()
    {
        return $this->hasMany(PeminjamanAsetDetail::class, 'id_aset', 'id_aset');
    }

    public function peminjamanRuanganAset()
    {
        return $this->hasMany(
            PeminjamanRuanganAset::class,
            'id_aset',
            'id_aset'
        );
    }

    public function maintenance()
    {
        return $this->hasMany(
            MaintenanceDetail::class,
            'id_aset',
            'id_aset'
        );
    }
}
