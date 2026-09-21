<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'webauthn/*',
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'adminMiddleware' => \App\Http\Middleware\SystemAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->route('login')
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Phiên làm việc đã hết hạn. Trang đã được làm mới mã bảo mật, vui lòng thử lại!');
        });

        $exceptions->respond(function ($response, $e, $request) {
            if ($response->getStatusCode() === 419) {
                return redirect()->route('login')
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Phiên làm việc đã hết hạn. Trang đã được làm mới mã bảo mật, vui lòng thử lại!');
            }
            return $response;
        });
    })->create();

