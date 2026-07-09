<?php

declare(strict_types=1);

namespace App\Domain\Employee\Actions;

use App\Domain\Employee\DTOs\EmployeeData;
use App\Domain\Employee\Models\Employee;

class UpdateEmployeeAction
{
    public function execute(Employee $employee, EmployeeData $data): Employee
    {
        $employee->update([
            'name' => $data->name,
            'color' => $data->color ?? $employee->color,
        ]);

        return $employee->fresh();
    }
}
