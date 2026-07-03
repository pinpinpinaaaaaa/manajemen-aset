<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EkspedisiPengiriman extends Model
{
    protected $table = 'ekspedisi_pengiriman';

    protected $fillable = [
        'id_ekspedisi',

        // KURIR
        'jenis_kurir',

        'id_kurir_internal',

        'nama_jasa_ekspedisi',
        'no_resi',

        // WAKTU
        'waktu_dikirim',
        'waktu_diterima',
        'waktu_selesai',

        // PENERIMA
        'nama_penerima_ttd',
        'jabatan_penerima',
        'catatan_penerimaan',

        // FILE / BUKTI
        'jenis_ttd',
        'ttd_digital',
        'ttd_file',
        'foto_bukti',

        // STATUS
        'status_pengiriman',
    ];

    protected $casts = [
        'waktu_dikirim' => 'datetime',
        'waktu_diterima' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function ekspedisi()
    {
        return $this->belongsTo(
            Ekspedisi::class,
            'id_ekspedisi',
            'id_ekspedisi'
        );
    }

    public function kurirInternal()
    {
        return $this->belongsTo(User::class, 'id_kurir_internal', 'id_user');
    }
}