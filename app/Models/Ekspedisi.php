<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Ekspedisi extends Model
{
    protected $table = 'ekspedisi';

    protected $primaryKey = 'id_ekspedisi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_ekspedisi',
        'nama_pengaju',
        'email_pengaju',
        'id_divisi_pengaju',

        'nama_pengirim',
        'email_pengirim',
        'no_hp_pengirim',
        'id_divisi_pengirim',

        'nama_penerima',
        'email_penerima',
        'no_hp_penerima',
        'instansi_penerima',
        'alamat_penerima',

        'judul_kegiatan',
        'keterangan',

        'decision_status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // ======================
    // RELASI
    // ======================

    // ke dokumen
    public function dokumen()
    {
        return $this->hasMany(EkspedisiDokumen::class, 'id_ekspedisi', 'id_ekspedisi');
    }

    // ke barang
    public function barang()
    {
        return $this->hasMany(EkspedisiBarang::class, 'id_ekspedisi', 'id_ekspedisi');
    }

    // ke pengiriman (1:1)
    public function pengiriman()
    {
        return $this->hasOne(EkspedisiPengiriman::class, 'id_ekspedisi', 'id_ekspedisi');
    }

    // OPTIONAL (kalau ada model Divisi)
    public function divisiPengaju()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function divisiPengirim()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi_pengirim', 'id_divisi');
    }

    public function divisi_pengirim()
    {
        return $this->belongsTo(
            Divisi::class,
            'id_divisi_pengirim',
            'id_divisi'
        );
    }

    public function divisi_pengaju()
    {
        return $this->belongsTo(
            Divisi::class,
            'id_divisi_pengaju',
            'id_divisi'
        );
    }
}
