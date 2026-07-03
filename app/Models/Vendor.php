<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $primaryKey = 'id_vendor';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_vendor',
        'nama_perusahaan',
        'alamat',
        'contact_person',
        'jabatan_cp',
        'no_telp_cp',
        'email_perusahaan',
        'bidang_usaha',

        'akta',
        'akta_link',

        'nib',
        'nib_link',

        'npwp',
        'npwp_link',

        'pakta_integritas',
        'pakta_integritas_link',
    ];
}