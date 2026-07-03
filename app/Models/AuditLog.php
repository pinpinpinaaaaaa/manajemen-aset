<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'id_user',
        'action',
        'table_name',
        'record_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
