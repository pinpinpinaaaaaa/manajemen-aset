<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GudangOpnameHeader extends Model
{
    use HasFactory;

    protected $table = 'gudang_opname_header';

    protected $fillable = [
        'kode_opname',
        'id_user',
        'started_at',
        'finished_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // ================= RELASI =================

    // ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // ke detail opname
    public function details()
    {
        return $this->hasMany(GudangOpnameDetail::class, 'id_opname');
    }
}