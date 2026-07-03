<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Divisi extends BaseModel
{
    protected $table = 'divisi';
    protected $primaryKey = 'id_divisi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_divisi',
        'nama_divisi',
        'kode_divisi',
        'deskripsi',
    ];

    public static function generateId()
    {
        $last = self::orderBy('id_divisi', 'desc')->first();

        if (!$last) {
            return 'DIV001';
        }

        $num = (int) substr($last->id_divisi, 3) + 1;

        return 'DIV' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
}
