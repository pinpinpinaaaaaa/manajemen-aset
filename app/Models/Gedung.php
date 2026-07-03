<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Gedung extends BaseModel
{
    use HasFactory;

    protected $table = 'gedung';
    protected $primaryKey = 'id_gedung';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_gedung',
        'nama_gedung',
        'status',
    ];

    // === Relasi ===
    public function ruangan()
    {
        return $this->hasMany(Ruangan::class, 'id_gedung', 'id_gedung');
    }

    public function aset()
    {
        return $this->hasMany(Aset::class, 'id_gedung');
    }

    public function apar()
    {
        return $this->hasMany(Apar::class, 'id_gedung');
    }

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class, 'id_gedung');
    }

    public static function getEnumStatus()
    {
        $type = \DB::select("SHOW COLUMNS FROM gedung LIKE 'status'")[0]->Type;

        preg_match('/enum\((.*)\)/', $type, $matches);

        return array_map(function($value) {
            return trim($value, "'");
        }, explode(',', $matches[1]));
    }

    public function gambar()
    {
        return $this->hasMany(
            GedungGambar::class,
            'id_gedung',
            'id_gedung'
        );
    }

    public function denah()
    {
        return $this->hasMany(
            GedungDenah::class,
            'id_gedung',
            'id_gedung'
        );
    }

}
