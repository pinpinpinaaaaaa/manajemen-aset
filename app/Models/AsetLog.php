<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsetLog extends BaseModel
{
    use HasFactory;

    protected $table = 'aset_log';

    protected $fillable = [
        'id_aset',
        'tipe',
        'ref_id',
        'keterangan',
        'tanggal_kejadian',
        'id_user',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'datetime',
    ];

    // =====================
    // 🔗 RELASI
    // =====================

    /**
     * Relasi ke aset
     */
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke user (optional)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // =====================
    // 🧠 ACCESSOR
    // =====================

    /**
     * Label manusiawi buat tipe log
     */
    public function getTipeLabelAttribute()
    {
        return match ($this->tipe) {
            'create' => 'Aset dibuat',
            'maintenance' => 'Maintenance',
            'pemindahan' => 'Pemindahan lokasi',
            'pemusnahan' => 'Pemusnahan aset',
            'update' => 'Perubahan data',
            default => ucfirst($this->tipe),
        };
    }

    /**
     * Format tanggal buat UI
     */
    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal_kejadian
            ? $this->tanggal_kejadian->format('d M Y H:i')
            : '-';
    }

    // =====================
    // 🔥 SCOPE (opsional tapi berguna)
    // =====================

    public function scopeTimeline($query)
    {
        return $query->orderBy('tanggal_kejadian', 'desc');
    }
}
