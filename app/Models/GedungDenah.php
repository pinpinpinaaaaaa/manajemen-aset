<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GedungDenah extends Model
{
    protected $table = 'gedung_denah';

    public $timestamps = false;

    protected $fillable = [
        'id_gedung',
        'file_denah',
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