<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;

class MenuAccessService
{
    /**
     * Menu untuk sidebar
     */
    public static function menusForUser(User $user)
    {
        $allowedMenuIds = self::allowedMenuIds($user);

        return Menu::whereNull('parent_id')
            ->when($allowedMenuIds !== null, function ($q) use ($allowedMenuIds) {
                $q->whereIn('id', $allowedMenuIds);
            })
            ->with(['children' => function ($q) use ($allowedMenuIds) {
                if ($allowedMenuIds !== null) {
                    $q->whereIn('id', $allowedMenuIds);
                }
            }])
            ->orderBy('order')
            ->get();
    }

    /**
     * Cek akses route
     */
    public static function canAccessRoute(User $user, string $routeName): bool
    {
        $allowedMenuIds = self::allowedMenuIds($user);

        // role / user punya SEMUA menu
        if ($allowedMenuIds === null) {
            return true;
        }

        return Menu::where('route_name', $routeName)
            ->whereIn('id', $allowedMenuIds)
            ->exists();
    }

    /**
     * Tentukan menu yang boleh
     */
    protected static function allowedMenuIds(User $user): ?array
    {
        // 1️⃣ User override
        if (is_array($user->menu)) {
            return self::withParents($user->menu);
        }

        // 2️⃣ Role whitelist
        if ($user->role && is_array($user->role->menu)) {
            return self::withParents($user->role->menu);
        }

        // 3️⃣ null = FULL ACCESS
        return null;
    }

    /**
     * Tambahkan parent menu otomatis
     */
    protected static function withParents(array $menuIds): array
    {
        $parents = Menu::whereIn('id', $menuIds)
            ->whereNotNull('parent_id')
            ->pluck('parent_id')
            ->toArray();

        return array_unique(array_merge($menuIds, $parents));
    }

}
