<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class LaporanTahunanDetail extends BaseModel
{
    protected $table = 'laporan_tahunan_detail';
    protected $primaryKey = 'id'; // auto increment

    protected $fillable = [
        'id_laporan_tahunan',
        'jenis',        // Maintenance | Pemusnahan
        'id_referensi', // id_maintenance atau id_pemusnahan
    ];

    /**
     * Relasi ke Header Laporan Tahunan
     */
    public function laporan()
    {
        return $this->belongsTo(LaporanTahunan::class, 'id_laporan_tahunan', 'id_laporan_tahunan');
    }

    /**
     * Relasi ke Data Maintenance (hanya jika jenis == Maintenance)
     */
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'id_referensi', 'id_maintenance');
    }

    /**
     * Relasi ke Data Pemusnahan (hanya jika jenis == Pemusnahan)
     */
    public function pemusnahan()
    {
        return $this->belongsTo(LaporanPemusnahan::class, 'id_referensi', 'id_pemusnahan');
    }

    /**
     * Helper untuk otomatis ambil data referensi sesuai jenis
     */
    public function dataReferensi()
    {
        return $this->jenis === 'Maintenance'
            ? $this->maintenance
            : $this->pemusnahan;
    }
}
