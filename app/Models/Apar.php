<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apar extends BaseModel
{
    use HasFactory;

    protected $table = 'apar';
    protected $primaryKey = 'id_apar';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_apar',
        'id_gedung',
        'id_ruangan',
        'expired_date',
        'jenis',
        'tanggal_refill',
        'ukuran',
        'keterangan',
        'foto',
        'last_checked_at',
        'last_checked_by',
        'last_used_at',
        'last_used_by',
        'lokasi',
        'merk',
        'media_isi',
    ];

    // === Relasi ===
    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'id_gedung');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }

    public function logs()
    {
        return $this->hasMany(AparLog::class, 'id_apar', 'id_apar');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'last_checked_by', 'id_user');
    }

    public function userPemakai()
    {
        return $this->belongsTo(User::class, 'last_used_by', 'id_user');
    }
}
