<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class EkspedisiDokumen extends Model
{
    protected $table = 'ekspedisi_dokumen';

    protected $fillable = [
        'id_ekspedisi',
        'nama_dokumen',
        'jenis_dokumen',
        'jumlah'
    ];

    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class, 'id_ekspedisi', 'id_ekspedisi');
    }
}
