<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\BaseModel;

class Kendaraan extends BaseModel
{
    use HasFactory;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id_kendaraan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id_kendaraan',
        'jenis_kendaraan',
        'tipe',
        'plat_nomor',
        'tahun_pembelian',
        'umur_ekonomis',
        'merk',
        'model',
        'spesifikasi',
        'no_rangka',
        'no_mesin',
        'status_kondisi',
        'status_penggunaan',
        'foto',
        'driver_id',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(
            \App\Models\PermintaanKendaraanItem::class,
            'id_kendaraan',   // foreign key di tabel items
            'id_kendaraan'    // primary key di kendaraan
        );
    }

    
}

