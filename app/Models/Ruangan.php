<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Ruangan extends BaseModel
{
    use HasFactory;

    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_ruangan',
        'id_gedung',
        'kategori',
        'lantai',
        'nama_ruangan',
        'status'
    ];

    // === Relasi ===
    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'id_gedung');
    }

    public function aset()
    {
        return $this->hasMany(Aset::class, 'id_ruangan');
    }

    public function apar()
    {
        return $this->hasMany(Apar::class, 'id_ruangan');
    }

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class, 'id_ruangan');
    }

    public function gambar()
    {
        return $this->hasMany(
            RuanganGambar::class,
            'id_ruangan',
            'id_ruangan'
        );
    }

    public function gambarUtama()
    {
        return $this->hasOne(
            RuanganGambar::class,
            'id_ruangan',
            'id_ruangan'
        )->oldestOfMany();
    }
}
