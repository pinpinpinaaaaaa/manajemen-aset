<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;

class MenuAccessService
{
    public static function allowedMenuIds(User $user): ?array
    {
        if ($user->menu !== null) {
            return $user->menu;
        }

        // User tanpa role tidak boleh dapat full access secara tidak sengaja
        if ($user->role === null) {
            return [];
        }

        if ($user->role->menu !== null) {
            return $user->role->menu;
        }

        return null; // NULL = semua menu (hanya role dengan menu=null yang sampai sini)
    }

    public static function menusForUser(User $user)
    {
        $allowed = self::allowedMenuIds($user);

        return Menu::where('is_active', true)
            ->when($allowed !== null, function ($q) use ($allowed) {
                $q->whereIn('id', $allowed);
            })
            ->whereNull('parent_id')
            ->with(['children' => function ($q) use ($allowed) {
                $q->where('is_active', true)
                  ->when($allowed !== null, fn ($qq) => $qq->whereIn('id', $allowed));
            }])
            ->orderBy('order')
            ->get();
    }
}
