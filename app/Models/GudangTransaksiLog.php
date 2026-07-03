<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangTransaksiLog extends Model
{
    protected $table = 'gudang_transaksi_log';

    protected $fillable = [
        'id_transaksi',
        'aksi',
        'user',
        'keterangan',
    ];

    /**
     * Relasi ke transaksi utama
     */
    public function transaksi()
    {
        return $this->belongsTo(GudangTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function userData()
    {
        return $this->belongsTo(User::class, 'user', 'id_user');
    }
}