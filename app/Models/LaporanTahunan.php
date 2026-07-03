<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class LaporanTahunan extends BaseModel
{
    protected $table = 'laporan_tahunan';
    protected $primaryKey = 'id_laporan_tahunan';
    public $incrementing = false; // karena primary bukan auto increment
    protected $keyType = 'string';

    protected $fillable = [
        'id_laporan_tahunan',
        'tahun',
        'catatan'
    ];

    public function details()
    {
        return $this->hasMany(LaporanTahunanDetail::class, 'id_laporan_tahunan', 'id_laporan_tahunan');
    }
    public function summary()
    {
        return $this->hasOne(LaporanTahunanSummary::class, 'id_laporan_tahunan', 'id_laporan_tahunan');
    }

}
