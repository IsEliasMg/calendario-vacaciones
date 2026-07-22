<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesión expirada. Vuelve a iniciar sesión.'], 401);
            }

            return redirect()->route('admin.login');
        }

        auth()->shouldUse('admin');

        return $next($request);
    }
}
