<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\CheckMenuAccess;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'menu.access' => CheckMenuAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tampilkan pesan ramah saat user kena rate limit (throttle)
        $exceptions->render(function (
            \Illuminate\Http\Exceptions\ThrottleRequestsException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->expectsJson()) {
                return null; // biarkan default untuk API/AJAX
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terlalu banyak percobaan. Silakan tunggu sebentar sebelum mencoba lagi.');
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        // === AUTO REKAP BULANAN ===
        // akan berjalan otomatis setiap tanggal 1 jam 00:00
        $schedule->command('rekap:bulanan')->monthlyOn(1, '00:00');
    })
    ->create();
