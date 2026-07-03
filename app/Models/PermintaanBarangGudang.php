<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class PermintaanBarangGudang extends BaseModel
{
    use HasFactory;

    protected $table = 'permintaan_barang_gudang';
    protected $primaryKey = 'id_permintaan';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_permintaan',
        'nama_pengaju',
        'email_pengaju',
        'id_divisi',
        'alasan',
        'tanggal_kebutuhan',
        'decision_status',
        'status',
        'decided_by',
        'decided_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal_kebutuhan' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(
            PermintaanBarangGudangDetail::class,
            'id_permintaan',
            'id_permintaan'
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
}