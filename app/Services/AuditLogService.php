<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public static function log(
        string $action,
        string $table,
        $recordId = null,
        $oldData = null,
        $newData = null
    ) {
        AuditLog::create([
            'id_user'    => Auth::check() ? Auth::user()->id_user : null,
            'action'     => $action,
            'table_name' => $table,
            'record_id'  => $recordId,
            'old_data'   => $oldData,
            'new_data'   => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
