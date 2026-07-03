<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class JenisBarang extends BaseModel
{
    use HasFactory;

    protected $table = 'jenis_barang';
    protected $primaryKey = 'id_jenis_barang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id_jenis_barang',
        'jenis',
        'kategori',
        'nama_barang',
        'prefix_kode',
        'bisa_dipindah',
    ];
}
