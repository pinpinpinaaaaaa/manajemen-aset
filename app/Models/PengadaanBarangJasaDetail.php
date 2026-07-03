<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengadaanBarangJasaDetail extends Model
{
    use HasFactory;

    protected $table = 'pengadaan_barang_jasa_detail';

    protected $fillable = [
        'id_pengadaan',
        'jenis',
        'nama_barang',
        'jumlah',
        'kategori_jasa',
        'merk',
        'tipe_model',
        'spesifikasi',
        'harga_satuan',
        'subtotal',
        'catatan'
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pengadaan()
    {
        return $this->belongsTo(
            PengadaanBarangJasa::class,
            'id_pengadaan',
            'id_pengadaan'
        );
    }

    public function getNamaItemAttribute()
    {
        return $this->jenis === 'barang'
            ? $this->nama_barang
            : $this->kategori_jasa;
    }

    public function files()
    {
    return $this->hasMany(
        PengadaanBarangJasaFile::class,
        'id_detail'
    );
    }
}