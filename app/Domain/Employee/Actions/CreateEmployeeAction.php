<?php

declare(strict_types=1);

namespace App\Domain\Employee\Actions;

use App\Domain\Employee\DTOs\EmployeeData;
use App\Domain\Employee\Models\Employee;

class CreateEmployeeAction
{
    public function execute(EmployeeData $data): Employee
    {
        return Employee::query()->create([
            'name' => $data->name,
            'color' => $data->color ?? Employee::DEFAULT_COLOR,
            'status' => Employee::STATUS_ACTIVE,
        ])->fresh();
    }
}
