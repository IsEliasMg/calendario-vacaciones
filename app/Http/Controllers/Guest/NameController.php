<?php

declare(strict_types=1);

namespace App\Http\Controllers\Guest;

use App\Domain\Employee\Actions\CreateEmployeeAction;
use App\Domain\Employee\DTOs\EmployeeData;
use App\Domain\Employee\Models\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\LookupEmployeeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NameController extends Controller
{
    public function create(): View
    {
        // Si había sesión de admin abierta, no la mezclamos con el flujo de trabajador
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        return view('guest.name');
    }

    public function store(LookupEmployeeRequest $request, CreateEmployeeAction $createEmployee): RedirectResponse
    {
        $name = trim($request->validated('name'));

        $employee = Employee::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if (! $employee) {
            $employee = $createEmployee->execute(new EmployeeData(name: $name));
        }

        if ($employee->isBlocked()) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Tu cuenta está bloqueada. Contacta al administrador.']);
        }

        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->regenerate();
        $request->session()->put('employee_id', $employee->id);

        return redirect()->route('guest.vacations.create');
    }
}
