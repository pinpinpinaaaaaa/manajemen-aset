<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class PengaduanKerusakan extends BaseModel
{
    use HasFactory;

    protected $table = 'pengaduan_kerusakan';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id_pengaduan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengaduan',
        'nama_pelapor',
        'email_pelapor',
        'id_divisi',
        'id_gedung',
        'id_ruangan',
        'decision_status',
        'decided_by',
        'decided_at'
    ];

    protected $casts = [
        'decided_at' => 'datetime'
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    public function details()
    {
        return $this->hasMany(
            PengaduanKerusakanDetail::class,
            'id_pengaduan',
            'id_pengaduan'
        );
    }

    public function divisi()
    {
        return $this->belongsTo(
            Divisi::class,
            'id_divisi',
            'id_divisi'
        );
    }

    public function gedung()
    {
        return $this->belongsTo(
            Gedung::class,
            'id_gedung',
            'id_gedung'
        );
    }

    public function ruangan()
    {
        return $this->belongsTo(
            Ruangan::class,
            'id_ruangan',
            'id_ruangan'
        );
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'decided_by', 'id_user');
    }
}
