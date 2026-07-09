<?php

declare(strict_types=1);

namespace App\Domain\Employee\Actions;

use App\Domain\Employee\Models\Employee;

class UpdateEmployeeColorAction
{
    public function execute(Employee $employee, string $color): Employee
    {
        $employee->update(['color' => strtoupper($color)]);

        return $employee->fresh();
    }
}
