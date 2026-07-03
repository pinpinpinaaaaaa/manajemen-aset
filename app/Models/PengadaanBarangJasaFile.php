<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanBarangJasaFile extends Model
{
    protected $table = 'pengadaan_barang_jasa_files';

    protected $fillable = [
        'id_detail',
        'file_path',
        'file_name',
        'file_type',
    ];
    
    public function detail()
    {
        return $this->belongsTo(
            PengadaanBarangJasaDetail::class,
            'id_detail'
        );
    }
}