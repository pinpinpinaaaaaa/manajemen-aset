<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GedungGambar extends Model
{
    protected $table = 'gedung_gambar';

    public $timestamps = false;

    protected $fillable = [
        'id_gedung',
        'gambar',
    ];

    public function gedung()
    {
        return $this->belongsTo(
            Gedung::class,
            'id_gedung',
            'id_gedung'
        );
    }
}