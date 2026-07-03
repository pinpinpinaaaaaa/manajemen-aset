<?php

if (!function_exists('canMenu')) {
    function canMenu(string $routeName): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $allowed = \App\Services\MenuAccessService::allowedMenuIds($user);

        if ($allowed === null) return true;

        // Cache route_name per user agar tidak query DB tiap panggilan.
        // Di-key per id_user — aman untuk test multi-user dan queue job
        // (static polos tanpa key bisa bocor ke user lain dalam 1 proses).
        static $cache = [];
        $userId = $user->id_user;

        if (!isset($cache[$userId])) {
            $cache[$userId] = \App\Models\Menu::whereIn('id', $allowed)
                ->pluck('route_name')
                ->toArray();
        }

        // Matching identik dengan query lama: exact match ATAU prefix dengan titik.
        foreach ($cache[$userId] as $route) {
            if ($route === $routeName || str_starts_with($route, $routeName . '.')) {
                return true;
            }
        }
        return false;
    }
}
