<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class PengaduanKerusakan extends BaseModel
{
    use HasFactory;

    protected $table = 'pengaduan_kerusakan';

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
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function details()
    {
        return $this->hasMany(PengaduanKerusakanDetail::class, 'id_pengaduan', 'id_pengaduan');
    }

    /** Maintenance yang dibuat saat pengaduan ini di-approve */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'id_gedung', 'id_gedung');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'decided_by', 'id_user');
    }

    // =========================================================
    // COMPUTED STATUS (dibaca dari relasi maintenance)
    // =========================================================

    /**
     * Status pengaduan diturunkan dari keputusan & status maintenance-nya.
     * Pastikan relasi 'maintenances.details' sudah di-eager-load sebelum
     * memanggil atribut ini untuk list banyak baris.
     *
     * Nilai yang dikembalikan:
     *   'ditolak'         — pengaduan ditolak
     *   'belum_approve'   — belum diputuskan
     *   'sedang_diproses' — disetujui, ada detail maintenance belum Selesai
     *   'selesai'         — disetujui, semua detail maintenance sudah Selesai
     */
    public function getStatusComputedAttribute(): string
    {
        if ($this->decision_status === 'ditolak') {
            return 'ditolak';
        }

        if ($this->decision_status !== 'disetujui') {
            return 'belum_approve';
        }

        // Sudah disetujui — baca dari maintenance
        $maintenances = $this->relationLoaded('maintenances')
            ? $this->maintenances
            : $this->maintenances()->with('details')->get();

        if ($maintenances->isEmpty()) {
            // Disetujui tapi maintenance belum dibuat (edge case)
            return 'sedang_diproses';
        }

        $semuaSelesai = $maintenances->every(function (Maintenance $m) {
            $details = $m->relationLoaded('details') ? $m->details : $m->details;
            return $details->isNotEmpty()
                && $details->every(fn($d) => $d->status === 'Selesai');
        });

        return $semuaSelesai ? 'selesai' : 'sedang_diproses';
    }
}
