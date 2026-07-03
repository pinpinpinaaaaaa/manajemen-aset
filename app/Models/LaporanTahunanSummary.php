<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanTahunanSummary extends Model
{
    use HasFactory;

    protected $table = 'laporan_tahunan_summary';

    protected $fillable = [
        'id_laporan_tahunan',
        'total_aset',
        'total_maintenance',
        'total_pemusnahan',
        'stok_awal_tahun',
        'stok_akhir_tahun',
        'total_transaksi_gudang',
    ];

    public function laporan()
    {
        return $this->belongsTo(
            LaporanTahunan::class,
            'id_laporan_tahunan',
            'id_laporan_tahunan'
        );
    }
}
