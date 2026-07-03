<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        $routeName = $request->route()->getName();

        // 1️⃣ Kalau user FULL ACCESS → langsung lolos
        if (\App\Services\MenuAccessService::allowedMenuIds($user) === null) {
            return $next($request);
        }

        // 2️⃣ Route tidak punya nama → aman
        if (!$routeName) {
            return $next($request);
        }

        // 3️⃣ Kalau route tidak ada di menu → bukan menu-based → aman
        $menu = \App\Models\Menu::where('route_name', $routeName)->first();
        if (!$menu) {
            return $next($request);
        }

        // 4️⃣ Cek izin menu
        $allowed = \App\Services\MenuAccessService::allowedMenuIds($user);

        if (!in_array($menu->id, $allowed)) {
            abort(403);
        }

        return $next($request);
    }

}
