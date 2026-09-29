<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class GudangTransaksi extends BaseModel
{
    use HasFactory;

    protected $table = 'gudang_transaksi';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_transaksi',
        'tanggal',
        'jenis_transaksi',
        'status',
        'approved_by',
        'approved_at',
        'alasan',
        'dibuat_oleh',
        'total_biaya',
        'tipe_penyesuaian',
        'referensi',
        'rkat_anggaran_id',
        'struk',
    ];

    public function hitungTotal()
    {
        return $this->details()->sum('subtotal');
    }

    public function rkatAnggaran()
    {
        return $this->belongsTo(RkatAnggaran::class, 'rkat_anggaran_id');
    }

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Transaksi -> Biaya tambahan
    public function charges()
    {
        return $this->hasMany(GudangTransaksiCharge::class, 'id_transaksi', 'id_transaksi');
    }

    // Transaksi -> Detail barang
    public function details()
    {
        return $this->hasMany(
            GudangTransaksiDetail::class,
            'id_transaksi',
            'id_transaksi'
        );
    }
}
