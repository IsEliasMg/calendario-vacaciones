<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Employee\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $employeeId = session('employee_id');

        if (! $employeeId) {
            return redirect()->route('guest.name');
        }

        $employee = Employee::query()->find($employeeId);

        if (! $employee || $employee->isBlocked()) {
            session()->forget('employee_id');

            return redirect()->route('guest.name')
                ->withErrors(['name' => 'Tu sesión ha expirado o tu cuenta está bloqueada.']);
        }

        $request->attributes->set('employee', $employee);

        return $next($request);
    }
}
