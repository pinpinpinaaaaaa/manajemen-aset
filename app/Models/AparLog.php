<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AparLog extends Model
{
    protected $table = 'apar_log';

    protected $fillable = [
        'id_apar',
        'tipe',
        'ref_id',
        'keterangan',
        'tanggal_kejadian',
        'id_user'
    ];

    public function apar()
    {
        return $this->belongsTo(Apar::class, 'id_apar', 'id_apar');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}