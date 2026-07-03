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
    ];

    public function hitungTotal()
    {
        return $this->details()->sum('subtotal');
    }

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

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
