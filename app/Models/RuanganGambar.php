<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuanganGambar extends Model
{
    protected $table = 'ruangan_gambar';

    protected $fillable = [
        'id_ruangan',
        'foto',
    ];

    public function ruangan()
    {
        return $this->belongsTo(
            Ruangan::class,
            'id_ruangan',
            'id_ruangan'
        );
    }
}