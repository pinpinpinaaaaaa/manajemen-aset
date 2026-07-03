<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class PengadaanBarangJasa extends BaseModel
{
    use HasFactory;

    protected $table = 'pengadaan_barang_jasa';
    protected $primaryKey = 'id_pengadaan';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengadaan',
        'nama_pengaju',
        'email_pengaju',
        'id_divisi',
        'alasan',
        'tanggal_kebutuhan',
        'total_biaya',
        'decision_status',
        'status',
        'decided_by',
        'decided_at',
        'catatan'
    ];

    protected $casts = [
        'tanggal_kebutuhan' => 'date',
        'total_biaya' => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(
            PengadaanBarangJasaDetail::class,
            'id_pengadaan',
            'id_pengadaan'
        );
    }

    public function divisi()
    {
        return $this->belongsTo(
            Divisi::class,
            'id_divisi',
            'id_divisi'
        );
    }

    public function getTotalBiayaHitungAttribute()
    {
        return $this->details()->sum('subtotal');
    }

}