<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user)
    {
        $changes = $user->getChanges();

        // skip kalau cuma updated_at
        if (count($changes) === 1 && isset($changes['updated_at'])) {
            return;
        }

        AuditLog::create([
            'id_user'   => Auth::id(),
            'action'    => 'update',
            'table_name'=> 'users',
            'record_id' => $user->id,
            'old_data'  => array_intersect_key(
                $user->getOriginal(),
                $changes
            ),
            'new_data'  => $changes,
            'ip_address'=> request()->ip(),
            'user_agent'=> request()->userAgent(),
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
