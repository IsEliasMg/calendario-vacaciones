<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Employee\Actions\BlockEmployeeAction;
use App\Domain\Employee\Actions\CreateEmployeeAction;
use App\Domain\Employee\Actions\DeleteEmployeeAction;
use App\Domain\Employee\Actions\ReactivateEmployeeAction;
use App\Domain\Employee\Actions\UpdateEmployeeAction;
use App\Domain\Employee\DTOs\EmployeeData;
use App\Domain\Employee\Models\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployeeRequest;
use App\Http\Requests\Admin\UpdateEmployeeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $employees = Employee::query()
            ->withCount('vacations')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.employees.index', compact('employees', 'search'));
    }

    public function store(StoreEmployeeRequest $request, CreateEmployeeAction $action): JsonResponse
    {
        $employee = $action->execute(new EmployeeData(
            name: $request->validated('name'),
            color: $request->validated('color'),
        ));

        return response()->json([
            'message' => 'Empleado creado correctamente.',
            'employee' => $employee,
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee, UpdateEmployeeAction $action): JsonResponse
    {
        $this->authorize('update', $employee);

        $employee = $action->execute($employee, new EmployeeData(
            name: $request->validated('name'),
            color: $request->validated('color'),
        ));

        return response()->json([
            'message' => 'Empleado actualizado correctamente.',
            'employee' => $employee,
        ]);
    }

    public function destroy(Employee $employee, DeleteEmployeeAction $action): JsonResponse
    {
        $this->authorize('delete', $employee);

        $action->execute($employee);

        return response()->json(['message' => 'Empleado eliminado correctamente.']);
    }

    public function block(Employee $employee, BlockEmployeeAction $action): JsonResponse
    {
        $this->authorize('update', $employee);

        $employee = $action->execute($employee);

        return response()->json([
            'message' => 'Empleado bloqueado.',
            'employee' => $employee,
        ]);
    }

    public function reactivate(Employee $employee, ReactivateEmployeeAction $action): JsonResponse
    {
        $this->authorize('update', $employee);

        $employee = $action->execute($employee);

        return response()->json([
            'message' => 'Empleado reactivado.',
            'employee' => $employee,
        ]);
    }

    public function history(Employee $employee): JsonResponse
    {
        $vacations = $employee->vacations()
            ->orderByDesc('vacation_date')
            ->get(['id', 'vacation_date', 'created_at']);

        return response()->json([
            'employee' => $employee->only(['id', 'name', 'color', 'status']),
            'vacations' => $vacations,
        ]);
    }
}
